<?php
namespace App\Plugins\ServiceDesk\Controllers\Products;

use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use App\Plugins\ServiceDesk\Model\Assets\SdAssettypes;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\Plugins\ServiceDesk\Model\Vendor\SdVendors;
use App\Plugins\ServiceDesk\Model\Products\SdProducts;
use App\Plugins\ServiceDesk\Requests\CreateUpdateProductRequest;
use App\Plugins\ServiceDesk\Requests\ProductAttachVendorRequest;
use App\Plugins\ServiceDesk\Model\Common\ProductVendorRelation;
use Exception;
use Illuminate\Http\Request;
use App\Plugins\ServiceDesk\Policies\AgentPermissionPolicy;
use App\Plugins\ServiceDesk\Requests\CreateVendorRequest;
use Lang;


class ApiProductController extends BaseServiceDeskController
{   
    
    public function __construct() {
        $this->middleware('role.admin');
    }

    // basic parameters for data tables
    private $searchString, $limit, $sortField, $sortOrder;
    
        /**
     * method to create or update product
     * @param ApiProductRequest $request
     * @return response
     */

    public function createUpdateProduct(CreateUpdateProductRequest $request)
    {
        $product = $this->productParametersRename($request->toArray());
        $productObject = SdProducts::updateOrCreate(['id' => $request->id], $product);

        if ($request->has('vendor_id')) {
            $productObject->first()->attachVendors()->sync($vendorId);
        }

        return successResponse(trans('ServiceDesk::lang.product_saved_successfully'));
    }

    /**
    * Function to rename product parameters
    * @param array $product
    * @return Response
    */
    private function productParametersRename($product)
    {
        $product['all_department'] = $product['department_id'];
        $product['product_mode_procurement_id'] = $product['procurement_mode_id'];
        $product['status'] = $product['status_id'];

        unset($product['department_id'], $product['procurement_mode_id'], $product['status_id']);

        return $product;
    }

    /**
     * Function to get product list
     * @param Request $request
     * @return Response
     */
    public function getProductList(Request $request)
    {   
        $searchString = $request->input('search-query') ?? '';
        $sortOrder = $request->input('sort-order') ?? 'asc';
        $sortField = $request->input('sort-field') ?? 'updated_at';
        $limit = $request->limit ?? 10;
        $searchString = ($searchString == 'Disable') ? 0 : ($searchString == 'Enable' ? 1 : $searchString);
        $products = SdProducts::with([
            'productStatus:id,name',
            'departmentRelation:id,name',
            'procurement:id,name',
            'assetType:id,name',
        ])->where(function($query) use ($searchString) {
            $query
                ->where('name', 'LIKE', "%$searchString%")
                ->orWhere('manufacturer', 'LIKE', "%$searchString%")
                ->orWhereHas('productStatus' , function($query) use ($searchString) {
                    $query
                    ->where('name', 'LIKE', "%$searchString%");
                })
                ->orWhereHas('assetType' , function($query) use ($searchString) {
                    $query
                    ->where('name', 'LIKE', "%$searchString%");
                })->orWhere('status', $searchString);

            })
            ->orderBy($sortField, $sortOrder)
            ->paginate($limit)
            ->toArray();;
        $products['products'] = $products['data'];
        unset($products['data']);

        $count = sizeof($products['products']);

        for($i=0;$i<$count;$i++)
        {
       $products['products'][$i]['department'] = $products['products'][$i]['department_relation'];
       $products['products'][$i]['procurement_mode'] = $products['products'][$i]['procurement'];
        unset($products['products'][$i]['department_relation'], $products['products'][$i]['product_status_id'], $products['products'][$i]['product_mode_procurement_id'],$products['products'][$i]['all_department'],$products['products'][$i]['procurement'],$products['products'][$i]['asset_type_id']);
       }
       
        return successResponse('',$products);
    }

    /**
     * Function to delete product
     * @param $productId 
     * @return Response
     */
    public function deleteProduct($productId)
    {
        $baseQuery = SdProducts::where('id', $productId);

        if ($baseQuery->get()->isEmpty()) {
            return errorResponse(trans('ServiceDesk::lang.product_not_found'));
        }
      
        $baseQuery->delete();

        return successResponse(trans('ServiceDesk::lang.product_deleted_successfully'));
    }

    /**
     * Function to get associated vendor list
     * @param int $productId
     * @param Request $request
     * @return Response
     */
    public function getAssociatedVendorList(int $productId, Request $request)
    {
        $searchString = $request->input('search-query') ?? '';
        $limit = $request->input('limit') ?: 10;
        $sortField = $request->input('sort-field') ?: 'updated_at';
        $sortOrder = $request->input('sort-order') ?: 'desc';

        $product = SdProducts::where('id', $productId)->first();
        if($product->get()->isEmpty())
        {
            return errorResponse(trans('ServiceDesk::lang.product_not_found'));
        }
        
        $vendors = $product->attachVendors()
                    ->where(function ($query) use ($searchString) {
                        $query
                    ->where('name', 'LIKE', "%$searchString%")
                    ->orWhere('email', 'LIKE', "%$searchString%")
                    ->orWhere('primary_contact', 'LIKE', "%$searchString%");
                })
            ->orderBy($sortField,$sortOrder)
            ->paginate($limit)
            ->toArray();
            $vendors['vendors'] = $vendors['data'];
            unset($vendors['data']);

            $count = sizeof($vendors['vendors']);

        for($i=0;$i<$count;$i++)
        {

        unset($vendors['vendors'][$i]['created_at'],$vendors['vendors'][$i]['updated_at'],$vendors['vendors'][$i]['pivot']);

       }
            return successResponse('', $vendors);
    }

    /**
     * method to attach vendor
     * @param Request $request
     * @return response
     */
    public function attachVendor(ProductAttachVendorRequest $request)
    {    
        $vendorIds = $request->vendor_ids;  
        $product = SdProducts::where('id', $request->product_id)->first()->attachVendors()->attach($vendorIds);
          return successResponse(trans('ServiceDesk::lang.vendor_attached_successfully'));
        
    }

    /**
     * method to detach vendor
     * @param $productId
     * @param $vendorId
     * @return response
     */
    public function detachVendor($productId,$vendorId)
    {
        $product = SdProducts::where('id', $productId);

        if ($product->get()->isEmpty()) {
          return errorResponse(trans('ServiceDesk::lang.product_not_found'));
        }

        $vendor = SdVendors::where('id', $vendorId);

        if ($vendor->get()->isEmpty()) {
            return errorResponse(trans('ServiceDesk::lang.vendor_not_found'));
        }

        $product->first()->attachVendors()->wherePivot('vendor_id','=',$vendorId)->detach();

        return successResponse(trans('ServiceDesk::lang.vendor_detached_successfully'));
    }

    /**
     * method to attach asset
     * @param Request $request
     * @return response
     */
    public function attachAsset(Request $request)
    {
        $product = SdProducts::where('id', $request->product_id);

        if ($product->get()->isEmpty()) {
          return errorResponse(trans('ServiceDesk::lang.product_not_found'));
        }

        $assetIds = [];
        if ($request->has('asset_ids')) {
          foreach ((array) $request->asset_ids as $assetId) {
            $asset = SdAssets::where('id',$assetId)
                               ->update(['product_id' => $request->product_id]);
          }
        }

        return successResponse(trans('ServiceDesk::lang.asset_attached_successfully'));
    }

    /**
     * method to detach asset
     * @param $productId
     * @param $assetId
     * @return response
     */
    public function detachAsset($productId, $assetId)
    {
        $product = SdProducts::where('id', $productId);

        if ($product->get()->isEmpty()) {
          return errorResponse(trans('ServiceDesk::lang.product_not_found'));
        }

        $asset = SdAssets::where('id', $assetId);

        if ($asset->get()->isEmpty()) {
          return errorResponse(trans('ServiceDesk::lang.asset_not_found'));
        }

        
            SdAssets::where('id',$assetId)
                               ->update(['product_id' => NULL]);
        

        return successResponse(trans('ServiceDesk::lang.asset_detached_successfully'));
    }

    /**
     * Function to initialize basic data table parameters
     * @param Request $request
     * @return null
     */
    private function initializeBasicParameters(Request $request)
    {
        $this->searchString = $request->input('search-query') ?? '';
        $this->sortOrder = $request->input('sort-order') ?? 'asc';
        $this->sortField = $request->input('sort-field') ?? 'updated_at';
        $this->limit = $request->limit ?? 10;
    }

    /**
     * method for product index blade page
     * @return view
     */
    public function productsIndexPage()
    {
        return view('service::products.index');
    }

    /**
     * method to get product data based on ID
     * @param productId
     * @return response
     */
    public function getProduct($productId)
    {
        $baseQuery = SdProducts::where('id', $productId);

        if ($baseQuery->get()->isEmpty()) {
          return errorResponse(trans('ServiceDesk::lang.product_not_found'));
        }

        $product = $baseQuery->with([
            'productStatus:id,name',
            'departmentRelation:id,name',
            'procurement:id,name',
            'assetType:id,name',
        ])
        ->first()->toArray();

        $product['department'] = $product['department_relation'];
        unset($product['department_relation'],$product['product_status_id'],$product['product_mode_procurement_id'],$product['all_department'],$product['asset_type_id']);
        
        return successResponse('', ['product' => $product]);
    }

    /**
    * Function to rename product parameter
    * @param $product
    * @return $product
    */
    private function productParameterRename($product)
    {
        $product['all_department'] = $product['department_id'];
        $product['product_mode_procurement_id'] = $product['procurement_mode_id'];
        $product['status'] = $product['status_id'];
        unset($product['department_id'],$product['procurement_mode_id'],$product['status_id']);

        return $product;
    }

    /**
     * method for product create blade page
     * @return view
     */
    public function productCreatePage() 
    {
        return view('service::products.create');
    }

    /**
     * method for product edit blade page
     * @param type $productId
     * @return view
     */
    public function productEditPage($productId) 
    {
        return view('service::products.edit', compact('productId'));
    }

}