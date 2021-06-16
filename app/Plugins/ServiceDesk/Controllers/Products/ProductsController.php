<?php

namespace App\Plugins\ServiceDesk\Controllers\Products;

use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use App\Plugins\ServiceDesk\Model\Assets\SdAssettypes;
use App\Plugins\ServiceDesk\Model\Products\SdProducts;
use App\Model\helpdesk\Agent\Department;
use App\Plugins\ServiceDesk\Model\Products\SdProductstatus;
use App\Plugins\ServiceDesk\Model\Products\SdProductprocmode;
use App\Plugins\ServiceDesk\Requests\CreateProductsRequest;
use Exception;
use App\Plugins\ServiceDesk\Requests\CreateVendorRequest;
use App\Plugins\ServiceDesk\Model\Vendor\SdVendors;
use Illuminate\Http\Request;
use Lang;

class ProductsController extends BaseServiceDeskController {

    /**
     * 
     */
    public function __construct() {
        $this->middleware('auth');
    }
    
    /**
     * 
     * @param \App\Plugins\ServiceDesk\Requests\CreateProductsRequest $request
     * @return type
     */
    public function productshandleCreate(CreateProductsRequest $request) {
        try {
            $sd_products = new SdProducts;
            $sd_products->name = $request->name;
            $sd_products->description = $request->description;
            $sd_products->manufacturer = $request->manufacturer;
            $sd_products->product_status_id = $request->Product_status;
            $sd_products->product_mode_procurement_id = $request->mode_procurement;
            $sd_products->all_department = $request->department_access;
            $sd_products->status = $request->status;
            $sd_products->save();
            if($request->page){
                return (Lang::get('ServiceDesk::lang.product_created_successfully'));
            }
            return \Redirect::route('service-desk.products.index')->with('message', Lang::get('ServiceDesk::lang.product_created_successfully'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @param type $id
     * @param \App\Plugins\ServiceDesk\Requests\CreateProductsRequest $request
     * @return type
     */
    public function productshandleEdit($id, CreateProductsRequest $request) {
        try {
            $sd_products = SdProducts::findOrFail($id);
            $sd_products->name = $request->name;
            $sd_products->description = $request->description;
            $sd_products->manufacturer = $request->manufacturer;
            //$sd_products->asset_type_id = $request->asset_type;
            $sd_products->product_status_id = $request->Product_status;
            $sd_products->product_mode_procurement_id = $request->mode_procurement;
            $sd_products->all_department = $request->department_access;
            $sd_products->status = $request->status;
            $sd_products->save();
            return \Redirect::route('service-desk.products.index')->with('message', Lang::get('ServiceDesk::lang.product_updated_successfully'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @param type $id
     * @return type
     */
    public function productsHandledelete($id) {
        try {
            $sd_products = SdProducts::findOrFail($id);
            $sd_products->delete();
            return \Redirect::route('service-desk.products.index')->with('message', Lang::get('ServiceDesk::lang.product_deleted_successfully'));
        } catch (Exception $ex) {
           return redirect()->back()->with('fails',Lang::get('ServiceDesk::lang.cannot_delete_product_when_contracts_are_associated'));
        }
    }

    /**
     * 
     * @param type $id
     * @return type
     * @throws \Exception
     */
    public function show($id) {
        try {
            $products = new SdProducts();
            $product = $products->find($id);
            if ($product) {
                return view('service::products.show', compact('product'));
            } else {
                throw new \Exception('Sorry we can not find your request');
            }
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @param \App\Plugins\ServiceDesk\Requests\CreateVendorRequest $request
     * @return type
     */
    public function addVendor(CreateVendorRequest $request) {
        try {
            $vendor = SdVendors::create($request->toArray());

            $product_id = $request->input('product');

            if ($vendor) {
                $relation = $this->createVendorRelation($product_id, $vendor->id);

                if ($relation) {
                    return redirect()->back()->with('success', Lang::get('ServiceDesk::lang.vendor_created_successfully'));
                }
            }
            return redirect()->back()->with('fails', 'Can not process your request');
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @param \Illuminate\Http\Request $request
     * @return type
     * @throws Exception
     */
    public function addExistingVendor(Request $request) {
        $this->validate($request, [
            'vendor' => 'required|unique:sd_product_vendor_relation,vendor_id',
        ]);
        try {
            $product_id = $request->input('product');
            $vendor_id = $request->input('vendor');
            $relation = $this->createVendorRelation($product_id, $vendor_id);
            if ($relation) {
                return redirect()->back()->with('success', Lang::get('ServiceDesk::lang.vendor_updated_successfully'));
            }
            throw new Exception('Sorry we can not find your request');
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @param type $product_id
     * @param type $vendor_id
     * @return type
     */
    public function createVendorRelation($product_id, $vendor_id) {
        $vendor_relation = new \App\Plugins\ServiceDesk\Model\Common\ProductVendorRelation();
        return $vendor_relation->create([
                    'product_id' => $product_id,
                    'vendor_id' => $vendor_id,
        ]);
    }

    /**
     * 
     * @param type $productid
     * @param type $vendorid
     * @return type
     * @throws Exception
     */
    public function removeVendor($productid, $vendorid) {
        try {
            $vendor_relation = new \App\Plugins\ServiceDesk\Model\Common\ProductVendorRelation();
            $relation = $vendor_relation
                    ->where('product_id', $productid)
                    ->where('vendor_id', $vendorid)
                    ->first();
            if ($relation) {
                $relation->delete();
                return redirect()->back()->with('success', Lang::get('ServiceDesk::lang.vendor_deleted_successfully'));
            }
            throw new Exception('Sorry we can not find your request');
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

}