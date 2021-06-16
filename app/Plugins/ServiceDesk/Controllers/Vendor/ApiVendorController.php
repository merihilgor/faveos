<?php
namespace App\Plugins\ServiceDesk\Controllers\Vendor;

use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use App\Plugins\ServiceDesk\Model\Vendor\SdVendors;
use App\Plugins\ServiceDesk\Model\Common\ProductVendorRelation;
use App\Plugins\ServiceDesk\Request\Vendor\CreateUpdateVendorRequest;
use Illuminate\Http\Request;
use App\Plugins\ServiceDesk\Model\Products\SdProducts;
use App\Plugins\ServiceDesk\Model\Contract\SdContract;
use App\Plugins\ServiceDesk\Requests\CreateVendorRequest;
use Exception;

/**
 * Handles API's for Vendor Controller
 * 
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
*/
class ApiVendorController extends BaseServiceDeskController {

    public function __construct() {
        $this->middleware('role.admin')->except(['createUpdateVendor']);
    }
    
    // basic parameters for data tables
    private $searchString, $limit, $sortField, $sortOrder;

    /**
     * Function to create and update vendor
     * @param CreateUpdateVendorRequest $request
     * @return Response
     */
    public function createUpdateVendor(CreateUpdateVendorRequest $request)
    {
        $vendor = $request->toArray();
        $vendor['status'] = $vendor['status_id'];
        unset($vendor['status_id']);
    
        $vendorObject = SdVendors::updateOrCreate(['id' => $request->id], $vendor);

        if ($request->has('product_id')) {
             $vendorObject->attachProducts()->attach($vendor['product_id']);
        }

        return successResponse(trans('ServiceDesk::lang.vendor_saved_successfully'));
    }

    /**
     * Function to get vendor details
     * @param $vendorId 
     * @return Response
     */
    public function getVendor($vendorId)
    {
        $baseQuery = SdVendors::where('id', $vendorId);

        if ($baseQuery->get()->isEmpty()) {
            return errorResponse(trans('ServiceDesk::lang.vendor_not_found'));
        }

        $vendor = $baseQuery->first()->toArray();
        $statusName = $baseQuery->first()->statuses();
        $vendor['status'] = ['id' => $vendor['status'], 'name' => $statusName];

        return successResponse('', ['vendor' => $vendor]);
    }

    /**
     * Function to get vendor list
     * @param Request $request
     * @return Response
     */
    public function getVendorList(Request $request)
    {
        $this->initializeBasicParameters($request);
        $vendors = SdVendors::where('name', 'LIKE', "%$this->searchString%")
            ->orWhere('primary_contact', 'LIKE', "%$this->searchString%")
            ->orWhere('email', 'LIKE', "%$this->searchString%")
            ->orWhere('address', 'LIKE', "%$this->searchString%")
            ->orWhere('status', $this->searchString == 'Active' ? $this->searchString = 1 : ($this->searchString == 'Inactive' ? $this->searchString = 0 : $this->searchString ))
            ->orderBy($this->sortField, $this->sortOrder)
            ->paginate($this->limit)
            ->toArray();
        $vendors['vendors'] = $vendors['data'];
        unset($vendors['data']);
        
        return successResponse('', $vendors);
    
    }

    /**
     * Function to delete vendor
     * @param SdVendors $vendor
     * @return Response
     */
    public function deleteVendor(SdVendors $vendor)
    {   
        try
        {
            $vendor->delete();
            return successResponse(trans('ServiceDesk::lang.vendor_deleted_successfully'));
        }
        catch(Exception $e)
        {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Function to get associated product list
     * @param int $vendorId
     * @param Request $request
     * @return Response
     */
    public function getAssociatedProductList(int $vendorId, Request $request)
    {
        $this->initializeBasicParameters($request);
        $searchString = $this->searchString;
        $products = SdProducts::with(['departmentRelation', 'productStatus'])
            ->where(function($query) use ($searchString, $vendorId) {
                $query->where('name', 'LIKE', "%$searchString%")
                    ->orWhere('manufacturer', 'LIKE', "%$searchString%")
                    ->whereHas('vendorRelation.vendors',function($baseQuery) use ($vendorId) {
                        $baseQuery->where('id', $vendorId);
                    })
                    ->orWhereHas('departmentRelation',function($baseQuery) use ($searchString) {
                        $baseQuery->where('name', 'LIKE', "%$searchString%");
                    })
                    ->orWhereHas('productStatus',function($baseQuery) use ($searchString) {
                        $baseQuery->where('name', 'LIKE', "%$searchString%");
                    });
            })
            ->select('id', 'name', 'manufacturer', 'product_status_id', 'all_department')
            ->orderBy($this->sortField,$this->sortOrder)
            ->paginate($this->limit)
            ->toArray();

        $this->formatProductList($products);

        return successResponse('', $products);
    }

    /**
     * Function to format product list
     * @param array $products
     * @return null
     */
    private function formatProductList(&$products)
    {
        foreach ($products['data'] as &$product) {
            $product['department'] = $product['department_relation'];
            unset($product['department_relation']);
        }
        $products['products'] = $products['data'];
        unset($products['data']);
    }

    /**
     * Function to get associated contract list
     * @param int $vendorId
     * @param Request $request
     * @return Response
     */
    public function getAssociatedContractList(int $vendorId, Request $request)
    {
        $this->initializeBasicParameters($request);

        $searchString = $this->searchString;
        $contracts = SdContract::where(function($query) use ($searchString) {
            $query->where('name', 'LIKE', "%$searchString%")
                ->orWhere('identifier', 'LIKE', "%$searchString%")
                ->orWhere('cost', 'LIKE', "%$searchString%");
        })
        ->whereHas('vendor',function($baseQuery) use ($vendorId) {
            $baseQuery->where('id', $vendorId);
        })
        ->select('id','name','cost','contract_start_date','contract_end_date')
        ->orderBy($this->sortField,$this->sortOrder)
        ->paginate($this->limit)
        ->toArray();

        $contracts['contracts'] = $contracts['data'];
        unset($contracts['data']);

        return successResponse('', $contracts);
    }

    /**
     * Function to initialize basic data table parameters
     * @param Request $request
     * @return null
     */
    private function initializeBasicParameters(Request $request)
    {
        $this->searchString = $request->input('search-query') ?? '';
        $this->sortOrder = $request->input('sort-order') ?? 'desc';
        $this->sortField = $request->input('sort-field') ?? 'updated_at';
        $this->limit = $request->limit ?? 10;
    }

    /**
     * method for vendor index blade page
     * @return view
     */
    public function vendorsIndexPage() 
    {
        return view('service::vendor.index');
    }

    /**
     * method for vendor create blade page
     * @return view
     */
    public function vendorCreatePage() 
    {
        return view('service::vendor.create');
    }

    /**
     * method for vendor edit blade page
     * @param $vendorId
     * @return view
     */
    public function vendorEditPage($vendorId) 
    {
        return view('service::vendor.edit', compact('vendorId'));
    }

    /**
     * method for vendor view blade page
     * @param $vendorId
     * @return view
     */
    public function vendorViewPage($vendorId) 
    {
        return view('service::vendor.show', compact('vendorId'));
    }

    public function handleCreate(CreateVendorRequest $request) {
        try {
            SdVendors::create($request->toArray());
            
            return (trans('ServiceDesk::lang.vendor_created_successfully'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

}
