<?php
namespace App\Plugins\ServiceDesk\tests\Backend\Controllers\Product;

use Tests\AddOnTestCase;
use App\Plugins\ServiceDesk\Model\Products\SdProducts;
use App\Plugins\ServiceDesk\Model\Common\ProductVendorRelation;
use App\Plugins\ServiceDesk\Model\Products\SdProductstatus;
use App\Plugins\ServiceDesk\Model\Assets\SdAssettypes;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\Plugins\ServiceDesk\Model\Vendor\SdVendors;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use DB;
use App\Plugins\ServiceDesk\Model\Releases\SdReleases;


/**
 * Tests ApiProductController
 * 
 * @author Abhishek Kumar Shashi <abhishek.shashi@ladybirdweb.com>
*/
class ApiProductControllerTest extends AddOnTestCase
{
  /** @group getProductList*/
  public function test_getProductList_withoutAnyExtraParameter_returnsCompleteProductsData()
  {
    $this->getLoggedInUserForWeb('admin');

      factory(SdProducts::class,2)->create();

      $response = $this->call('GET', url('service-desk/api/product-list'));
      $products = json_decode($response->content())->data->products;

      $response->assertstatus(200);
      $this->assertCount(2, $products);
      $product = reset($products);
      $this->assertDatabaseHas('sd_products', ['id' => $product->id, 'name' => $product->name]);
  }

  /** @group getProductList */
    public function test_getProductList_withLimit_returnsNumberOfProductsDataBasedOnLimit()
    {
      $this->getLoggedInUserForWeb('admin');
      factory(SdProducts::class, 3)->create();
      $limit = 1;
      $response = $this->call('GET', url('service-desk/api/product-list'), ['limit' => $limit]);
      $products = json_decode($response->content())->data->products;
      $response->assertstatus(200);
      $this->assertCount(1, $products);
        $product = reset($products);
      $this->assertDatabaseHas('sd_products', ['id' => $product->id, 'name' => $product->name]);
  }

  /** @group getProductList */
  public function test_getProductList_withSortFieldAndSortOrder_returnsProductsDataInAscendingOrderBasedOnIdField()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdProducts::class, 3)->create();
    $productsInDb = SdProducts::orderBy('id', 'asc')->get()->toArray();
    $response = $this->call('GET', url('service-desk/api/product-list'), ['sort-order' => 'asc', 'sort-field' => 'id']);
    $products = json_decode($response->content())->data->products;
    $response->status(200);
    $this->assertCount(3, $products);
    foreach ($products as $product) {
      $this->assertDatabaseHas('sd_products', ['id' => $product->id, 'name' => $product->name]);
    }
  }

  /** @group getProductList */
  public function test_getProductList_withPage_returnsProductsDataBasedOnSpecificPage()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdProducts::class,3)->create();
    $sortOrder = 'asc';
    $page = 2;
    $limit = 1;
    $response = $this->call('GET', url('service-desk/api/product-list'), ['sort-order' => $sortOrder, 'limit' => $limit, 'page' => $page]);
    $data = json_decode($response->content())->data;
    $response->assertstatus(200);
    $this->assertCount(1, $data->products);
    $this->assertEquals($data->current_page, $page);
    $product = reset($data->products);
    $this->assertDatabaseHas('sd_products', ['id' => $product->id, 'name' => $product->name]);
  }

  /** @group getProductist */
  public function test_getProductList_withSearchQueryEmpty_returnsCompleteProductData()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdProducts::class, 3)->create();
    $response = $this->call('GET', url('service-desk/api/product-list'), ['search-query' => '']);
    $products = json_decode($response->content())->data->products;
    $response->assertstatus(200);
    $this->assertCount(3, $products);
    foreach ($products as $product) {
      $this->assertDatabaseHas('sd_products', ['id' => $product->id, 'name' => $product->name]);
    }
  }

  /** @group getProductList */
  public function test_getProductList_withSearchQueryProductName_returnsProductsDataBasedOnPassedProductName()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdProducts::class, 3)->create();
    $productInDb = factory(SdProducts::class)->create();
    $response = $this->call('GET', url('service-desk/api/product-list'), ['search-query' => $productInDb->name]);
    $response->status(200);
    $productsInResponse = json_decode($response->content())->data->products;
    $this->assertCount(1, $productsInResponse);
    $productInResponse = reset($productsInResponse);
    $this->assertEquals($productInDb->id, $productInResponse->id);
    $this->assertEquals($productInDb->name, $productInResponse->name);
  }

  /** @group getProductList */
  public function test_getProductList_withSearchQueryWrongProductName_returnsEmptyProductList()
  {
    $productName = 'wrong-product-name';
    $this->wrongSearchQueryForProduct($productName);
  }

  /**
   * method for wrong search query for product, it return empty product list data
   * associated assertions are done in this method
   * @param string $searchQuery
   * @return null
   */
  private function wrongSearchQueryForProduct(String $searchQuery)
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdProducts::class, 3)->create();
    $response = $this->call('GET', url('service-desk/api/product-list'), ['search-query' => $searchQuery]);
    $response->assertstatus(200);
    $productsInResponse = json_decode($response->content())->data->products;
    $this->assertCount(0, $productsInResponse);
    $this->assertEmpty($productsInResponse);
  }

  /** @group getProductList */
  public function test_getProductList_withSearchQueryProductManufacturer_returnsProductsDataBasedOnPassedProductManufacturer()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdProducts::class, 3)->create();
    $productInDb = factory(SdProducts::class)->create(['name' => 'Dell Laptop', 'manufacturer' => 'Dell']);
    $response = $this->call('GET', url('service-desk/api/product-list'), ['search-query' => $productInDb->manufacturer]);
    $response->assertstatus(200);
    $productsInResponse = json_decode($response->content())->data->products;
    $this->assertCount(1, $productsInResponse);
    $productInResponse = reset($productsInResponse);
    $this->assertEquals($productInDb->id, $productInResponse->id);
    $this->assertEquals($productInDb->manufacturer, $productInResponse->manufacturer);
  }

  /** @group getProductList */
  public function test_getProductList_withSearchQueryWrongProductManufacturer_returnsEmptyProductList()
  {
    $productManufacturer = 'wrong-product-manufacturer';
    $this->wrongSearchQueryForProduct($productManufacturer);
  }

  /** @group getProductList */
  public function test_getProductList_withSearchQueryProductStatus_returnsProductsDataBasedOnPassedProductStatus()
  {
    $this->getLoggedInUserForWeb('admin');
    $productInDb = factory(SdProducts::class)->create(['product_status_id'=>3]);
    $productStatusInDb = factory(SdProductstatus::class)->create(['name'=>'Retired']);
    $response = $this->call('GET', url('service-desk/api/product-list'), ['search-query' => $productStatusInDb->name]);
    $response->assertstatus(200);
    $productsInResponse = json_decode($response->content())->data->products;
    $this->assertCount(1, $productsInResponse);
    $productInResponse = reset($productsInResponse);
    $this->assertEquals($productInDb->id, $productInResponse->id);
    $this->assertEquals($productInDb->product_status_id, $productInResponse->product_status->id);
  }

  /** @group getProductList */
  public function test_getProductList_withWrongSearchQueryProductStatus_returnsEmptyProductList()
  {
    $productStatus = 'wrong-product-status';
    $this->wrongSearchQueryForProduct($productStatus);
  }

  /** @group getProductList */
  public function test_getProductList_withSearchQueryProductAssetType_returnsProductsDataBasedOnPassedProductAssetType()
  {
    $this->getLoggedInUserForWeb('admin');
    $productInDb = factory(SdProducts::class)->create(['asset_type_id'=>3]);
    $assetTypeInDb = factory(SdAssettypes::class)->create(['name'=>'Hardware']);
    $response = $this->call('GET', url('service-desk/api/product-list'), ['search-query' => $assetTypeInDb->name]);
    $response->assertstatus(200);
    $productsInResponse = json_decode($response->content())->data->products;
    $this->assertCount(1, $productsInResponse);
    $productInResponse = reset($productsInResponse);
    $this->assertEquals($productInDb->id, $productInResponse->id);
    $this->assertEquals($productInDb->asset_type_id, $productInResponse->asset_type->id);
  }

  /** @group getProductList */
  public function test_getProductList_withWrongSearchQueryProductAssetType_returnsEmptyProductList()
  {
    $productAssetType = 'wrong-product-asset-type';
    $this->wrongSearchQueryForProduct($productAssetType);
  }

     /** @group getProductList */
  public function test_getProductList_withSearchQueryStatus_returnsProductsDataBasedOnPassedStatus()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdProducts::class, 3)->create();
    // status 0 means Inactive
    factory(SdProducts::class)->create(['status' => 0]);
    $response = $this->call('GET', url('service-desk/api/product-list'), ['search-query' => 'Inactive']);
    $response->assertstatus(200);
    $productsInResponse = json_decode($response->content())->data->products;
    $this->assertCount(1, $productsInResponse);
    foreach ($productsInResponse as $product) {
      $this->assertDatabaseHas('sd_products', ['id' => $product->id, 'name' => $product->name, 'status' => 0]);
    }
  }

     /** @group getProductList */
  public function test_getProductList_withWrongSearchQueryStatus_returnsEmptyProductList()
  {
    $status = 'wrong-status';
    $this->wrongSearchQueryForProduct($status);
  }

  /** @group deleteProduct */
  public function test_deleteProduct_withProductId_returnProductDeletedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $product = factory(SdProducts::class)->create();
    $response = $this->call('DELETE', url("service-desk/api/product-delete/{$product->id}"));
    $response->assertStatus(200);
    $this->assertDatabaseMissing('sd_products', ['id' => $product->id, 'name' => $product->name]);
  }

  /** @group deleteProduct */
  public function test_deleteProduct_withWrongProductId_returnsProductNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('DELETE', url("service-desk/api/product-delete/wrong-product-id"));
    $response->assertStatus(400);
  }


  /** @group createUpdateProduct */
  public function test_createUpdateProduct_withProductFields_returnsProductSavedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $initialCount = SdProducts::count();
    $vendor = factory(SdProducts::class)->create(['id' => $this->user->id,'asset_type_id' => 1]);
    $productStatusId = 1;
    $productModeProcurementId = 1;
    $allDepartment = 1;
    $status = 1;
    $name = 'Apple Laptop';
    $description = 'Apple Laptop';
    $manufacturer = 'Apple';
    $assetTypeId = 1;
    $response = $this->call('POST',url('service-desk/api/product'),
  [
        'id'=>$this->user->id,
        'name'=>$name,
        'description'=>$description,
        'manufacturer'=>$manufacturer,
        'product_status_id'=>$productStatusId,
        'procurement_mode_id'=>$productModeProcurementId,
        'department_id'=>$allDepartment,
        'status_id'=>$status,
        'asset_type_id'=>$assetTypeId,
    ]    
  );
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_products', [
        'id'=>$this->user->id,
        'name'=>$name,
        'description'=>$description,
        'manufacturer'=>$manufacturer,
        'product_status_id'=>1,
        'product_mode_procurement_id'=>1,
        'all_department'=>1,
        'status'=>$status,
        'asset_type_id'=>1, 
    ]);
    $this->assertEquals($initialCount+1, SdProducts::count());
  }

  /** @group createUpdateProduct */
  public function test_createUpdateProduct_withoutStatusRequiredFields_returnsFieldRequiredException()
  {     
    $initialCount = SdProducts::count();
    $productStatusId = 1;
    $productModeProcurementId = 1;
    $allDepartment = 1;
    $status = 1;
    $name = 'Apple Laptop';
    $description = 'Apple Laptop';
    $manufacturer = 'Apple';

    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('POST', url('service-desk/api/product'), [
        'id'=>$this->user->id,
        'name'=>$name,
        'description'=>$description,
        'manufacturer'=>$manufacturer,
        'product_status_id'=>$productStatusId,
        'product_mode_procurement_id'=>$productModeProcurementId,
        'all_department'=>$allDepartment,
      ]
    );
    $response->assertStatus(412);
    $this->assertDatabaseMissing('sd_products', [
        'id'=>$this->user->id,
        'name'=>$name,
        'description'=>$description,
        'manufacturer'=>$manufacturer,
        'product_status_id'=>$productStatusId,
        'product_mode_procurement_id'=>$productModeProcurementId,
        'all_department'=>$allDepartment,
        'status'=>$status,
    ]);

    $this->assertEquals($initialCount, SdProducts::count());
  }

  /** group getProduct */
  public function test_getProduct_withProductId_returnsProductDataBasedOnId()
  {
    $this->getLoggedInUserForWeb('admin');
    $productStatusId = 1;
    $productModeProcurementId = 1;
    $allDepartment = 1;
    $assetTypeId = 1;
    $productInDb = factory(SdProducts::class)->create(['product_status_id' => $productStatusId, 'product_mode_procurement_id' => $productModeProcurementId,]);

    $response = $this->call('GET', url("service-desk/api/product/$productInDb->id"));
    $response->assertStatus(200);

    $productInResponse = json_decode($response->content());
    $this->assertDatabaseHas('sd_products', [
        'id'=>$productInDb->id,
        'name'=>$productInDb->name,
        'description'=>$productInDb->description,
        'manufacturer'=>$productInDb->manufacturer,
        'product_status_id'=>$productStatusId,
        'product_mode_procurement_id'=>$productModeProcurementId,
        'all_department'=>$allDepartment,
        'status'=>$productInDb->status,
        'asset_type_id'=>$assetTypeId,
    ]);

  }

  /** group getProduct */
  public function test_getProduct_withWrongProductId_returnsProductNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url("service-desk/api/product/wrong-product-id"));
    $response->assertStatus(400);
  }
  
  /** @group getAssociatedProductList */
  public function test_getAssociatedVendorList_withoutAnyExtraParameter_returnsVendorListData()
  {
    $this->getloggedInUserForWeb('admin');
    $vendorId = factory(SdVendors::class)->create()->id;
    $productId = factory(SdProducts::class)->create()->id;
    ProductVendorRelation::create(['product_id' => $productId, 'vendor_id' => $vendorId]);
    $response = $this->call('GET', url("service-desk/api/product/vendor/{$productId}"));
    $vendors = json_decode($response->content())->data->vendors;
    $response->status(200);
    $this->assertCount(1, $vendors);
    foreach ($vendors as $vendor) {
      $this->assertDatabaseHas('sd_vendors', ['id' => $vendorId,'name' => $vendor->name,'email' => $vendor->email,'primary_contact' => $vendor->primary_contact,'status' => $vendor->status]);
    }
  }

  /** @group getAssociatedProductList */
  public function test_getAssociatedVendorList_withLimit_returnsVendorListDataBasedOnLimit()
  {
    $this->getloggedInUserForWeb('admin');
    $vendorId1 = factory(SdVendors::class)->create()->id;
    $productId = factory(SdProducts::class)->create()->id;
    $vendorId2 = factory(SdVendors::class)->create()->id;
    ProductVendorRelation::create(['product_id' => $productId, 'vendor_id' => $vendorId1]);
    ProductVendorRelation::create(['product_id' => $productId, 'vendor_id' => $vendorId2]);
    $limit = 1;
    $response = $this->call('GET', url("service-desk/api/product/vendor/{$productId}"), ['limit' => $limit]);
    $vendors = json_decode($response->content())->data->vendors;
    $response->status(200);
    $this->assertCount($limit, $vendors);
    foreach ($vendors as $vendor) {
      $this->assertDatabaseHas('sd_vendors', ['id' => $vendorId1,'name' => $vendor->name,'email' => $vendor->email,'primary_contact' => $vendor->primary_contact,'status' => $vendor->status]);
    }
  }

  /** @group getAssociatedVendorList */
  public function test_getAssociatedVendorList_withSortFieldAndSortOrder_returnsVendorListDataBasedOnSortOrderAndSortField()
  {
    $this->getloggedInUserForWeb('admin');
    $vendorId1 = factory(SdVendors::class)->create()->id;
    $productId = factory(SdProducts::class)->create()->id;
    ProductVendorRelation::create(['product_id' => $productId, 'vendor_id' => $vendorId1]);
    $vendorId2 = factory(SdVendors::class)->create()->id;
    ProductVendorRelation::create(['product_id' => $productId, 'vendor_id' => $vendorId2]);
    $limit = 1;
    $sortOrder = 'asc';
    $sortField = 'id';
    $response = $this->call('GET', url("service-desk/api/product/vendor/{$productId}"), ['limit' => $limit, 'sort-order' => $sortOrder, 'sort-field' => $sortField]);
    $vendors = json_decode($response->content())->data->vendors;
    $response->status(200);
    $this->assertCount($limit, $vendors);
    foreach ($vendors as $vendor) {
      $this->assertDatabaseHas('sd_vendors', ['id' => $vendorId1,'name' => $vendor->name,'email' => $vendor->email,'primary_contact' => $vendor->primary_contact,'status' => $vendor->status]);
    }
  }

  /** @group getAssociatedVendorList */
  public function test_getAssociatedVendorList_withPage_returnsVendorListDataBasedOnSpecificPage()
  {
    $this->getloggedInUserForWeb('admin');
    $vendorId1 = factory(SdVendors::class)->create()->id;
    $productId = factory(SdProducts::class)->create()->id;
    ProductVendorRelation::create(['product_id' => $productId, 'vendor_id' => $vendorId1]);
    $vendorId2 = factory(SdVendors::class)->create()->id;
    ProductVendorRelation::create(['product_id' => $productId, 'vendor_id' => $vendorId2]);
    $limit = 1;
    $page = 1;
    $sortOrder = 'asc';
    $sortField = 'id';
    $response = $this->call('GET', url("service-desk/api/product/vendor/{$productId}"), ['limit' => $limit, 'sort-order' => $sortOrder, 'sort-field' => $sortField, 'page' => $page]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertEquals($data->current_page, $page);
    $this->assertCount($limit, $data->vendors);
    foreach ($data->vendors as $vendor) {
      $this->assertDatabaseHas('sd_vendors', ['id' => $vendorId1,'name' => $vendor->name,'email' => $vendor->email,'primary_contact' => $vendor->primary_contact,'status' => $vendor->status]);
    }
  }

  /** @group getAssociatedVendorList */
  public function test_getAssociatedVendorList_withSearchQueryEmpty_returnsCompleteVendorListData()
  {
    $this->getloggedInUserForWeb('admin');
    $vendorId1 = factory(SdVendors::class)->create()->id;
    $productId = factory(SdProducts::class)->create()->id;
    ProductVendorRelation::create(['product_id' => $productId, 'vendor_id' => $vendorId1]);
    $vendorId2 = factory(SdVendors::class)->create()->id;
    ProductVendorRelation::create(['product_id' => $productId, 'vendor_id' => $vendorId2]);
    $response = $this->call('GET', url("service-desk/api/product/vendor/{$productId}"), ['search-query' => '']);
    $vendors = json_decode($response->content())->data->vendors;
    $response->status(200);
    $this->assertCount(SdVendors::count(), $vendors);
    foreach ($vendors as $vendor) {
      $this->assertDatabaseHas('sd_vendors', ['id' => $vendor->id,'name' => $vendor->name,'email' => $vendor->email,'primary_contact' => $vendor->primary_contact,'status' => $vendor->status]);
    }
  }

  /** @group getAssociatedVendorList */
  public function test_getAssociatedVendorList_withSearchQueryVendorName_returnsVendorListDataBasedOnVendorName()
  {
    $this->getloggedInUserForWeb('admin');
    $productId= factory(SdProducts::class)->create()->id;
    $vendorName = 'Dell';
    $vendorId1 = factory(SdVendors::class)->create(['name' => 'LG Indiranagar', 'email' => 'lg@faveo.com', 'primary_contact' => '9065434567'])->id;
    $vendorId2 = factory(SdVendors::class)->create(['name' => $vendorName, 'email' => 'dell@faveo.com', 'primary_contact' => '9054564356'])->id;
    ProductVendorRelation::create(['product_id' => $productId, 'vendor_id' => $vendorId1]);
    
    ProductVendorRelation::create(['product_id' => $productId, 'vendor_id' => $vendorId2]);
    $response = $this->call('GET', url("service-desk/api/product/vendor/{$productId}"), ['search-query' => $vendorName]);
    $vendors = json_decode($response->content())->data->vendors;
    $response->assertstatus(200);
    $this->assertCount(1, $vendors);
    foreach ($vendors as $vendor) {
      $this->assertDatabaseHas('sd_vendors', ['id' => $vendor->id,'name' => $vendor->name,'email' => $vendor->email,'primary_contact' => $vendor->primary_contact,'status' => $vendor->status]);
    }
  }

  /** @group getAssociatedVendorList */
  public function test_getAssociatedVendorList_withSearchQueryVendorEmail_returnsVendorListDataBasedOnVendorEmail()
  {
    $this->getloggedInUserForWeb('admin');
    $productId= factory(SdProducts::class)->create()->id;
    $vendorEmail = 'Dell@gmail.com';
    $vendorId1 = factory(SdVendors::class)->create()->id;
    $vendorId2 = factory(SdVendors::class)->create(['email' => $vendorEmail])->id;
    ProductVendorRelation::create(['product_id' => $productId, 'vendor_id' => $vendorId1]);
    
    ProductVendorRelation::create(['product_id' => $productId, 'vendor_id' => $vendorId2]);
    $response = $this->call('GET', url("service-desk/api/product/vendor/{$productId}"), ['search-query' => $vendorEmail]);
    $vendors = json_decode($response->content())->data->vendors;
    $response->assertstatus(200);
    $this->assertCount(1, $vendors);
    foreach ($vendors as $vendor) {
      $this->assertDatabaseHas('sd_vendors', ['id' => $vendor->id,'name' => $vendor->name,'email' => $vendor->email,'primary_contact' => $vendor->primary_contact,'status' => $vendor->status]);
    }
  }

  /** @group getAssociatedVendorList */
  public function test_getAssociatedVendorList_withSearchQueryVendorPrimaryContact_returnsVendorListDataBasedOnVendorPrimaryContact()
  {
    $this->getloggedInUserForWeb('admin');
    $productId= factory(SdProducts::class)->create()->id;
    $vendorPrimaryContact = '8888855555';
    $vendorId1 = factory(SdVendors::class)->create()->id;
    $vendorId2 = factory(SdVendors::class)->create(['primary_contact' => $vendorPrimaryContact])->id;
    ProductVendorRelation::create(['product_id' => $productId, 'vendor_id' => $vendorId1]);
    
    ProductVendorRelation::create(['product_id' => $productId, 'vendor_id' => $vendorId2]);
    $response = $this->call('GET', url("service-desk/api/product/vendor/{$productId}"), ['search-query' => $vendorPrimaryContact]);
    $vendors = json_decode($response->content())->data->vendors;
    $response->assertstatus(200);
    $this->assertCount(1, $vendors);
    foreach ($vendors as $vendor) {
      $this->assertDatabaseHas('sd_vendors', ['id' => $vendor->id,'name' => $vendor->name,'email' => $vendor->email,'primary_contact' => $vendor->primary_contact,'status' => $vendor->status]);
    }
  }

  /** @group attachVendor */
  public function test_attachVendor_withProductIdAndVendorId_returnsVendorAttachedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $productId =  factory(SdProducts::class)->create()->id;
    $vendorId = factory(SdVendors::class)->create()->id;
    $vendorId1 = factory(SdVendors::class)->create()->id;
    $vendorId2 = factory(SdVendors::class)->create()->id;
    $initialCount = ProductVendorRelation::count();
    $response = $this->call('POST', url("service-desk/api/product/attach/vendor"),['product_id' => $productId,'vendor_ids' => [$vendorId,$vendorId1,$vendorId2]]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_product_vendor_relation', ['vendor_id' => $vendorId, 'product_id' => $productId]);
    $this->assertEquals($initialCount+3, ProductVendorRelation::count());
  }

  /** @group attachVendor */
  public function test_attachVendor_withWrongProductIdAndWrongVendorId_returnProductNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('POST', url("service-desk/api/product/attach/vendor"),['product_id' => 'wrong-product-id','vendor_ids' => ['wrong-vendor-id']]);
    $response->assertStatus(412);
  }

  /** @group attachVendor */
  public function test_attachVendor_withVendorIdAndWrongProductId_returnProductNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $vendorId =  factory(SdVendors::class)->create()->id;
    $response = $this->call('POST', url("service-desk/api/product/attach/vendor"),['product_id' => 'wrong-product-id','vendor_ids' => [$vendorId]]);   
    $response->assertStatus(412);
  }

  /** @group detachVendor */
  public function test_detachVendor_withProductIdandVendorId_returnsVendorDetachedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $vendorId =  factory(SdVendors::class)->create()->id;
    $product = factory(SdProducts::class)->create();
    $productId = $product->id;
    $product->attachVendors()->sync($vendorId);
    $initialCount = ProductVendorRelation::count();
    $response = $this->call('DELETE', url("service-desk/api/product/detach-vendor/$productId/$vendorId"));
    $response->assertStatus(200);
    $this->assertDatabaseMissing('sd_product_vendor_relation', ['vendor_id' => $vendorId, 'product_id' => $productId]);
    $this->assertEquals($initialCount-1,ProductVendorRelation::count());
  }
  /** @group detachVendor */
  public function test_detachVendor_withWrongProductIdandWrongVendorId_returnProductNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('DELETE', url("service-desk/api/product/detach-vendor/wrong-product-id/wrong-vendor-id"));
    $response->assertStatus(400);
  }

  /** @group detachVendor */
  public function test_detachVendor_withWrongProductIdAndVendorId_returnProductNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $vendorId =  factory(SdVendors::class)->create()->id;
    $response = $this->call('DELETE', url("service-desk/api/product/detach-vendor/wrong-product-id/$vendorId"));
    $response->assertStatus(400);
  }

  /** @group detachVendor */
  public function test_detachVendor_withProductIdAndWrongVendorId_returnProductNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $productId =  factory(SdProducts::class)->create()->id;
    $response = $this->call('DELETE', url("service-desk/api/product/detach-vendor/$productId/wrong-vendor-id"));
    $response->assertStatus(400);
  }

  /** @group attachAsset */
  public function test_attachAsset_withProductIdAndAssetId_returnsAssetAttachedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $productId = factory(SdProducts::class)->create()->id;
    $assetId = factory(SdAssets::class)->create(['product_id' => NULL,])->id;
    $response = $this->call('POST', url("service-desk/api/product/attach-asset"),['product_id' => $productId,'asset_ids' => [$assetId]]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_assets', ['product_id' => $productId,'id' => $assetId,]);
  }

  /** @group attachAsset */
  public function test_attachAsset_withWrongProductIdAndAssetId_returnsProductNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create(['product_id' => NULL,]);
    $response = $this->call('POST', url("service-desk/api/product/attach-asset"),['product_id' => 'wrong-product-id','asset_ids' => [$asset->id]]);
    $response->assertStatus(400);
  }

  /** @group attachAsset */
  public function test_attachAsset_withWrongProductIdAndWrongAssetId_returnsProductNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('POST', url("service-desk/api/product/attach-asset"),['product_id' =>'wrong-product-id','asset_ids' => ['wrong-asset-id']]);
    $response->assertStatus(400);
  }

  /** @group detachAsset */
  public function test_detachAsset_withProductIdAndAssetId_returnsAssetDetachedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $product = factory(SdProducts::class)->create();
    $asset = factory(SdAssets::class)->create(['product_id' => $product->id,]);
    $response = $this->call('DELETE', url("service-desk/api/product/detach-asset/$product->id/$asset->id"));
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_assets', ['product_id' => NULL,'id' => $asset->id,]);
  }

  /** @group detachAsset */
  public function test_detachAsset_withWrongProductIdAndWrongAssetId_returnsProductNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('DELETE', url("service-desk/api/product/detach-asset/wrong-product-id/wrong-asset-id"));
    $response->assertStatus(400);
  }

  /** @group detachAsset */
  public function test_detachAsset_withProductIdAndWrongAssetId_returnsAssetNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $product = factory(SdProducts::class)->create();
    $asset = factory(SdAssets::class)->create(['product_id' => $product->id,]);
    $response = $this->call('DELETE', url("service-desk/api/product/detach-asset/$product->id/wrong-asset-id"));
    $response->assertStatus(400);
  }

}