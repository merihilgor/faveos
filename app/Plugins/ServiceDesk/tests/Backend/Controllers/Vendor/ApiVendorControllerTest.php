<?php
namespace App\Plugins\ServiceDesk\tests\Backend\Controllers\Vendor;

use Tests\AddOnTestCase;
use App\Plugins\ServiceDesk\Model\Vendor\SdVendors;
use App\Plugins\ServiceDesk\Model\Contract\SdContract;
use App\Plugins\ServiceDesk\Model\Common\ProductVendorRelation;
use App\Plugins\ServiceDesk\Model\Products\SdProducts;
use App\Model\helpdesk\Agent\Department;
use App\Plugins\ServiceDesk\Model\Products\SdProductstatus;

/**
 * Tests ApiVendorController
 * 
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
*/
class ApiVendorControllerTest extends AddOnTestCase
{
  /** @group createUpdateVendor */
  public function test_createUpdateVendor_withFieldValidationVendorNameMissing_returnsNameFieldIsRequired()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('POST', url('service-desk/api/vendor'), [
                'email' => 'testingvendor@faveo.com',
                'primary_contact' => '9945164865',
                'address' => 'indiranagar, bangalore',
                'status_id' => 1,
                'description' => 'Testing Vendor'
              ]);
    $response->assertStatus(412);
  }

  /** @group createUpdateVendor */
  public function test_createUpdateVendor_withVendorFields_returnsVendorSavedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $vendorArray = [
      'name' => 'Ladybird',
      'email' => 'testingvendor@faveo.com',
      'primary_contact' => '9945164865',
      'address' => 'indiranagar, bangalore',
      'status_id' => 1,
      'description' => 'Testing Vendor'
    ];
    $response = $this->call('POST', url('service-desk/api/vendor'), $vendorArray);
    $response->assertStatus(200);
    $vendorArray['status'] = $vendorArray['status_id'];
    unset($vendorArray['status_id']);
    $this->assertDatabaseHas('sd_vendors', $vendorArray);
  }

  /** @group createUpdateVendor */
  public function test_createUpdateVendor_withProductIdInRequest_returnsVendorSavedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $productId = factory(SdProducts::class)->create()->id;
    $vendorArray = [
      'name' => 'Ladybird',
      'email' => 'testingvendor@faveo.com',
      'primary_contact' => '9945164865',
      'address' => 'indiranagar, bangalore',
      'status_id' => 1,
      'description' => 'Testing Vendor'
    ];
    $response = $this->call('POST', url('service-desk/api/vendor'),[
      'name' => 'Ladybird',
      'email' => 'testingvendor@faveo.com',
      'primary_contact' => '9945164865',
      'address' => 'indiranagar, bangalore',
      'status_id' => 1,
      'description' => 'Testing Vendor',
      'product_id' => $productId,]);
    $response->assertStatus(200);
    $vendorArray['status'] = $vendorArray['status_id'];
    unset($vendorArray['status_id']);
    $vendorId = ProductVendorRelation::where('product_id',$productId)->value('vendor_id');
    $this->assertDatabaseHas('sd_vendors', $vendorArray);
    $this->assertDatabaseHas('sd_product_vendor_relation',['vendor_id' => $vendorId, 'product_id' => $productId]);
  }

  /** @group createUpdateVendor */
  public function test_createUpdateVendor_withUpdateVendorName_returnsVendorSavedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $vendor = factory(SdVendors::class)->create();
    $updatedName = 'Ladybird';
    $response = $this->call('POST', url('service-desk/api/vendor'), [
      'id' => $vendor->id,
      'name' => $updatedName,
      'email' => $vendor->email,
      'primary_contact' => $vendor->primary_contact,
      'address' => $vendor->address,
      'status_id' => $vendor->status,
      'description' => $vendor->description
    ]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_vendors', ['id' => $vendor->id, 'name' => $updatedName, 'email' => $vendor->email]);
  }

  /** @group getVendor */
  public function test_getVendor_withWrongVendorId_returnsVendorNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url("service-desk/api/vendor/wrong-vendor-id"));
    $response->assertStatus(400);
  }

  /** @group getVendor */
  public function test_getVendor_withVendorId_returnVendorData()
  {
    $this->getLoggedInUserForWeb('admin');
    $vendorId = factory(SdVendors::class)->create()->id;
    $response = $this->call('GET', url("service-desk/api/vendor/$vendorId"));
    $vendor = json_decode($response->content())->data->vendor;
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_vendors', ['id' => $vendorId, 'name' => $vendor->name, 'status' => $vendor->status->id]);
  }

  /** @group getVendorList */
  public function test_getVendorList_withoutAnyExtraParameter_returnsCompleteVendorsData()
  {
    $this->getloggedInUserForWeb('admin');
    factory(SdVendors::class, 3)->create();
    $response = $this->call('GET', url('service-desk/api/vendor-list'));
    $vendors = json_decode($response->content())->data->vendors;
    $response->status(200);
    $this->assertCount(3, $vendors);
    $vendor = reset($vendors);
    $this->assertDatabaseHas('sd_vendors', ['id' => $vendor->id, 'name' => $vendor->name]);
  }

  /** @group getVendorList */
  public function test_getVendorList_withLimit_returnsNumberOfVendorsDataBasedOnLimit()
  {
    $this->getloggedInUserForWeb('admin');
    factory(SdVendors::class, 3)->create();
    $limit = 1;
    $response = $this->call('GET', url('service-desk/api/vendor-list'), ['limit' => $limit]);
    $vendors = json_decode($response->content())->data->vendors;
    $response->status(200);
    $this->assertCount(1, $vendors);
    $vendor = reset($vendors);
    $this->assertDatabaseHas('sd_vendors', ['id' => $vendor->id, 'name' => $vendor->name]);
  }

  /** @group getVendorList */
  public function test_getVendorList_withSortFieldAndSortOrder_returnsVendorsDataInAscendingOrderBasedOnIdField()
  {
    $this->getloggedInUserForWeb('admin');
    factory(SdVendors::class, 3)->create();
    $vendorsInDb = SdVendors::orderBy('id', 'asc')->get()->toArray();
    $response = $this->call('GET', url('service-desk/api/vendor-list'), ['sort-order' => 'asc', 'sort-field' => 'id']);
    $vendors = json_decode($response->content())->data->vendors;
    $response->status(200);
    $this->assertCount(3, $vendors);
    foreach ($vendors as $vendor) {
      $this->assertDatabaseHas('sd_vendors', ['id' => $vendor->id, 'name' => $vendor->name]);
    }
  }

  /** @group getVendorList */
  public function test_getVendorList_withPage_returnsVendorsDataBasedOnSpecificPage()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdVendors::class,3)->create();
    $sortOrder = 'asc';
    $page = 2;
    $limit = 1;
    $response = $this->call('GET', url('service-desk/api/vendor-list'), ['sort-order' => $sortOrder, 'limit' => $limit, 'page' => $page]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->vendors);
    $this->assertEquals($data->current_page, $page);
    $vendor = reset($data->vendors);
    $this->assertDatabaseHas('sd_vendors', ['id' => $vendor->id, 'name' => $vendor->name]);
  }

  /** @group getVendorList */
  public function test_getVendorList_withSearchQueryEmpty_returnsCompleteVendorData()
  {
    $this->getloggedInUserForWeb('admin');
    factory(SdVendors::class, 3)->create();
    $response = $this->call('GET', url('service-desk/api/vendor-list'), ['search-query' => '']);
    $vendors = json_decode($response->content())->data->vendors;
    $response->status(200);
    $this->assertCount(3, $vendors);
    foreach ($vendors as $vendor) {
      $this->assertDatabaseHas('sd_vendors', ['id' => $vendor->id, 'name' => $vendor->name]);
    }
  }

  /** @group getVendorList */
  public function test_getVendorList_withSearchQueryVendorName_returnsVendorsDataBasedOnPassedVendorName()
  {
    $this->getloggedInUserForWeb('admin');
    factory(SdVendors::class, 3)->create();
    $vendorInDb = factory(SdVendors::class)->create();
    $response = $this->call('GET', url('service-desk/api/vendor-list'), ['search-query' => $vendorInDb->name]);
    $response->status(200);
    $vendorsInResponse = json_decode($response->content())->data->vendors;
    $this->assertCount(1, $vendorsInResponse);
    $vendorInResponse = reset($vendorsInResponse);
    $this->assertEquals($vendorInDb->id, $vendorInResponse->id);
    $this->assertEquals($vendorInDb->name, $vendorInResponse->name);
  }

  /** @group getVendorList */
  public function test_getVendorList_withSearchQueryWrongVendorName_returnsVendorsDataBasedOnPassedVendorName()
  {
    $vendorName = 'wrong-vendor-name';
    $this->wrongSearchQueryForVendor($vendorName);
  }

  /**
   * method for wrong search query for vendor, it return empty vendor list data
   * associated assertions are done in this method
   * @param string $searchQuery
   * @return null
   */
  private function wrongSearchQueryForVendor(String $searchQuery)
  {
    $this->getloggedInUserForWeb('admin');
    factory(SdVendors::class, 3)->create();
    $response = $this->call('GET', url('service-desk/api/vendor-list'), ['search-query' => $searchQuery]);
    $response->status(200);
    $vendorsInResponse = json_decode($response->content())->data->vendors;
    $this->assertCount(0, $vendorsInResponse);
    $this->assertEmpty($vendorsInResponse);
  }

  /** @group getVendorList */
  public function test_getVendorList_withSearchQueryVendorPrimaryContact_returnsVendorsDataBasedOnPassedVendorPrimaryContact()
  {
    $this->getloggedInUserForWeb('admin');
    factory(SdVendors::class, 3)->create();
    $vendorInDb = factory(SdVendors::class)->create(['name' => 'DELL Indiranagar', 'primary_contact' => '9999999900', 'email' => 'dell@faveo.com']);
    $response = $this->call('GET', url('service-desk/api/vendor-list'), ['search-query' => $vendorInDb->primary_contact]);
    $response->status(200);
    $vendorsInResponse = json_decode($response->content())->data->vendors;
    $this->assertCount(1, $vendorsInResponse);
    $vendorInResponse = reset($vendorsInResponse);
    $this->assertEquals($vendorInDb->id, $vendorInResponse->id);
    $this->assertEquals($vendorInDb->primary_contact, $vendorInResponse->primary_contact);
  }

  /** @group getVendorList */
  public function test_getVendorList_withSearchQueryWrongVendorPrimaryContact_returnsVendorsDataBasedOnPassedVendorPrimaryContact()
  {
    $vendorPrimaryContact = 'wrong-vendor-primary-contact';
    $this->wrongSearchQueryForVendor($vendorPrimaryContact);
  }

  /** @group getVendorList */
  public function test_getVendorList_withSearchQueryVendorEmail_returnsVendorsDataBasedOnPassedVendorEmail()
  {
    $this->getloggedInUserForWeb('admin');
    factory(SdVendors::class, 3)->create();
    $vendorInDb = factory(SdVendors::class)->create();
    $response = $this->call('GET', url('service-desk/api/vendor-list'), ['search-query' => $vendorInDb->email]);
    $response->status(200);
    $vendorsInResponse = json_decode($response->content())->data->vendors;
    $this->assertCount(1, $vendorsInResponse);
    $vendorInResponse = reset($vendorsInResponse);
    $this->assertEquals($vendorInDb->id, $vendorInResponse->id);
    $this->assertEquals($vendorInDb->email, $vendorInResponse->email);
  }

  /** @group getVendorList */
  public function test_getVendorList_withSearchQueryWrongVendorEmail_returnsVendorsDataBasedOnPassedVendorEmail()
  {
    $vendorEmail = 'wrong-vendor-email';
    $this->wrongSearchQueryForVendor($vendorEmail);
  }

  /** @group getVendorList */
  public function test_getVendorList_withSearchQueryVendorAddress_returnsVendorsDataBasedOnPassedVendorAddress()
  {
    $this->getloggedInUserForWeb('admin');
    factory(SdVendors::class, 3)->create();
    $vendorInDb = factory(SdVendors::class)->create();
    $response = $this->call('GET', url('service-desk/api/vendor-list'), ['search-query' => $vendorInDb->address]);
    $response->status(200);
    $vendorsInResponse = json_decode($response->content())->data->vendors;
    $this->assertCount(1, $vendorsInResponse);
    $vendorInResponse = reset($vendorsInResponse);
    $this->assertEquals($vendorInDb->id, $vendorInResponse->id);
    $this->assertEquals($vendorInDb->address, $vendorInResponse->address);
  }

  /** @group getVendorList */
  public function test_getVendorList_withSearchQueryWrongVendorAddress_returnsVendorsDataBasedOnPassedVendorAddress()
  {
    $vendorAddress = 'wrong-vendor-address';
    $this->wrongSearchQueryForVendor($vendorAddress);
  }

     /** @group getVendorList */
  public function test_getVendorList_withSearchQueryVendorStatus_returnsVendorsDataBasedOnPassedVendorStatus()
  {
    $this->getloggedInUserForWeb('admin');
    factory(SdVendors::class, 3)->create();
    factory(SdVendors::class)->create(['status' => 0]);
    $response = $this->call('GET', url('service-desk/api/vendor-list'), ['search-query' => 'Inactive']);
    $response->status(200);
    $vendorsInResponse = json_decode($response->content())->data->vendors;
    $this->assertCount(1, $vendorsInResponse);
    // status is 0 means Inactive
    foreach ($vendorsInResponse as $vendor) {
      $this->assertDatabaseHas('sd_vendors', ['id' => $vendor->id, 'name' => $vendor->name, 'status' => 0]);
    }
  }

  /** @group getVendorList */
  public function test_getVendorList_withSearchQueryWrongStatus_returnsVendorsDataBasedOnPassedVendorStatus()
  {
    $vendorStatusName = 'wrong-vendor-status-name';
    $this->wrongSearchQueryForVendor($vendorStatusName);
  }

   /** @group deleteVendor */
  public function test_deleteVendor_withWrongVendorId_returnsVendorNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('DELETE', url("service-desk/api/vendor/wrong-vendor-id"));
    $response->assertStatus(404);
  }

  /** @group deleteVendor */
  public function test_deleteVendor_withVendorId_returnVendorDeletedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $vendor = factory(SdVendors::class)->create();
    $response = $this->call('DELETE', url("service-desk/api/vendor/{$vendor->id}"));
    $response->assertStatus(200);
    $this->assertDatabaseMissing('sd_vendors', ['id' => $vendor->id, 'name' => $vendor->name]);
  }

  /** @group getAssociatedContractList */
  public function test_getAssociatedContractList_withoutAnyExtraParameter_returnsContractListData()
  {
    $this->getloggedInUserForWeb('admin');
    $vendorId = factory(SdVendors::class)->create()->id;
    $contractId = factory(SdContract::class)->create(['vendor_id' => $vendorId])->id;
    $response = $this->call('GET', url("service-desk/api/vendor/contract/{$vendorId}"));
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(1, $contracts);
    foreach ($contracts as $contract) {
      $this->assertDatabaseHas('sd_contracts', ['id' => $contractId, 'name' => $contract->name, 'contract_start_date' => $contract->contract_start_date, 'contract_end_date' => $contract->contract_end_date]);
    }
  }

   /** @group getAssociatedProductList */
  public function test_getAssociatedContractList_withLimit_returnsContractListDataBasedOnLimit()
  {
    $this->getloggedInUserForWeb('admin');
    $vendorId = factory(SdVendors::class)->create()->id;
    $contractId1 = factory(SdContract::class)->create(['vendor_id' => $vendorId])->id;
    $contractId2 = factory(SdContract::class)->create(['vendor_id' => $vendorId])->id;
    $limit = 1;
    $response = $this->call('GET', url("service-desk/api/vendor/contract/{$vendorId}"), ['limit' => $limit]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount($limit, $contracts);
    foreach ($contracts as $contract) {
      $this->assertDatabaseHas('sd_contracts', ['id' => $contractId1, 'name' => $contract->name, 'contract_start_date' => $contract->contract_start_date, 'contract_end_date' => $contract->contract_end_date]);
    }
  }

  /** @group getAssociatedContractList */
  public function test_getAssociatedContractList_withSortFieldAndSortOrder_returnsContractListDataBasedOnSortOrderAndSortField()
  {
    $this->getloggedInUserForWeb('admin');
    $vendorId = factory(SdVendors::class)->create()->id;
    $contractId1 = factory(SdContract::class)->create(['vendor_id' => $vendorId])->id;
    $contractId2 = factory(SdContract::class)->create(['vendor_id' => $vendorId])->id;
    $limit = 1;
    $sortOrder = 'asc';
    $sortField = 'id';
    $response = $this->call('GET', url("service-desk/api/vendor/contract/{$vendorId}"), ['limit' => $limit, 'sort-order' => $sortOrder, 'sort-field' => $sortField]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount($limit, $contracts);
    foreach ($contracts as $contract) {
      $this->assertDatabaseHas('sd_contracts', ['id' => $contractId1, 'name' => $contract->name, 'contract_start_date' => $contract->contract_start_date, 'contract_end_date' => $contract->contract_end_date]);
    }
  }

  /** @group getAssociatedContractList */
  public function test_getAssociatedContractList_withPage_returnsContractListDataBasedOnSpecificPage()
  {
    $this->getloggedInUserForWeb('admin');
    $vendorId = factory(SdVendors::class)->create()->id;
    $contractId1 = factory(SdContract::class)->create(['vendor_id' => $vendorId])->id;
    $contractId2 = factory(SdContract::class)->create(['vendor_id' => $vendorId])->id;
    $limit = 1;
    $page = 2;
    $sortOrder = 'asc';
    $sortField = 'id';
    $response = $this->call('GET', url("service-desk/api/vendor/contract/{$vendorId}"), ['limit' => $limit, 'sort-order' => $sortOrder, 'sort-field' => $sortField, 'page' => $page]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertEquals($data->current_page, $page);
    $this->assertCount($limit, $data->contracts);
    foreach ($data->contracts as $contract) {
      $this->assertDatabaseHas('sd_contracts', ['id' => $contractId2, 'name' => $contract->name, 'contract_start_date' => $contract->contract_start_date, 'contract_end_date' => $contract->contract_end_date]);
    }
  }

  /** @group getAssociatedContractList */
  public function test_getAssociatedContractList_withSearchQueryEmpty_returnsCompleteContractListData()
  {
    $this->getloggedInUserForWeb('admin');
    $vendorId = factory(SdVendors::class)->create()->id;
    $contractId1 = factory(SdContract::class)->create(['vendor_id' => $vendorId])->id;
    $contractId2 = factory(SdContract::class)->create(['vendor_id' => $vendorId])->id;
    $response = $this->call('GET', url("service-desk/api/vendor/contract/{$vendorId}"), ['search-query' => '']);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(SdContract::count(), $contracts);
    foreach ($contracts as $contract) {
      $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => $contract->name, 'contract_start_date' => $contract->contract_start_date, 'contract_end_date' => $contract->contract_end_date]);
    }
  }

  /** @group getAssociatedContractList */
  public function test_getAssociatedContractList_withSearchQueryContractName_returnsContractListDataBasedOnContractName()
  {
    $this->getloggedInUserForWeb('admin');
    $vendorId = factory(SdVendors::class)->create()->id;
    $contractName = 'Dell Laptop Contract';
    $contractId1 = factory(SdContract::class)->create(['name' => $contractName, 'vendor_id' => $vendorId])->id;
    $contractId2 = factory(SdContract::class)->create(['vendor_id' => $vendorId])->id;
    $response = $this->call('GET', url("service-desk/api/vendor/contract/{$vendorId}"), ['search-query' => $contractName]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(1, $contracts);
    foreach ($contracts as $contract) {
      $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => $contractName, 'contract_start_date' => $contract->contract_start_date, 'contract_end_date' => $contract->contract_end_date]);
    }
  }

  /** @group getAssociatedContractList */
  public function test_getAssociatedContractList_withSearchQueryWrongContractName_returnsContractListDataEmptyBasedOnWrongContractName()
  {
    $contractName = 'wrong-contract-name';
    $this->wrongSearchQueryForContract($contractName);
  }

  /** @group getAssociatedContractList */
  public function test_getAssociatedContractList_withSearchQueryContractIdentifier_returnsContractListDataBasedOnContractIdentifier()
  {
    $this->getloggedInUserForWeb('admin');
    $vendorId = factory(SdVendors::class)->create()->id;
    $contractIdentifier = '#CNTR-12';
    $contractId1 = factory(SdContract::class)->create(['identifier' => $contractIdentifier, 'vendor_id' => $vendorId])->id;
    $contractId2 = factory(SdContract::class)->create(['vendor_id' => $vendorId])->id;
    $response = $this->call('GET', url("service-desk/api/vendor/contract/{$vendorId}"), ['search-query' => $contractIdentifier]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(1, $contracts);
    foreach ($contracts as $contract) {
      $this->assertDatabaseHas('sd_contracts', ['id' => $contractId1, 'name' => $contract->name, 'contract_start_date' => $contract->contract_start_date, 'contract_end_date' => $contract->contract_end_date]);
    }
  }

  /** @group getAssociatedContractList */
  public function test_getAssociatedContractList_withSearchQueryWrongContractIdentifier_returnsContractListDataEmptyBasedOnWrongContractIdentifier()
  {
    $contractIdentifier = 'wrong-contract-identifier';
    $this->wrongSearchQueryForContract($contractIdentifier);
  }

    /** @group getAssociatedContractList */
  public function test_getAssociatedContractList_withSearchQueryContractCost_returnsContractListDataBasedOnContractCost()
  {
    $this->getloggedInUserForWeb('admin');
    $vendorId = factory(SdVendors::class)->create()->id;
    $contractCost = '80009';
    $contractId1 = factory(SdContract::class)->create(['cost' => $contractCost, 'vendor_id' => $vendorId])->id;
    $contractId2 = factory(SdContract::class)->create(['vendor_id' => $vendorId])->id;
    $response = $this->call('GET', url("service-desk/api/vendor/contract/{$vendorId}"), ['search-query' => $contractCost]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(1, $contracts);
    foreach ($contracts as $contract) {
      $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'cost' => $contractCost, 'name' => $contract->name, 'contract_start_date' => $contract->contract_start_date, 'contract_end_date' => $contract->contract_end_date]);
    }
  }

  /** @group getAssociatedContractList */
  public function test_getAssociatedContractList_withSearchQueryWrongContractCost_returnsContractListDataEmptyBasedOnWrongContractCost()
  {
    $contractCost = 'wrong-contract-cost';
    $this->wrongSearchQueryForContract($contractCost);
  }

  /**
   * method for wrong search query for contract, it return empty contract list data
   * associated assertions are done in this method
   * @param string $searchQuery
   * @return null
   */
  private function wrongSearchQueryForContract(String $searchQuery)
  {
    $this->getloggedInUserForWeb('admin');
    $vendorId = factory(SdVendors::class)->create()->id;
    factory(SdContract::class)->create(['vendor_id' => $vendorId])->id;
    $response = $this->call('GET', url("service-desk/api/vendor/contract/{$vendorId}"), ['search-query' => $searchQuery]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(0, $contracts);
    $this->assertEmpty($contracts);
  }

  /** @group getAssociatedProductList */
  public function test_getAssociatedProductList_withoutAnyExtraParameter_returnsProductListData()
  {
    $this->getloggedInUserForWeb('admin');
    $vendorId = factory(SdVendors::class)->create()->id;
    $productId = factory(SdProducts::class)->create()->id;
    ProductVendorRelation::create(['product_id' => $productId, 'vendor_id' => $vendorId]);
    $response = $this->call('GET', url("service-desk/api/vendor/product/{$vendorId}"));
    $products = json_decode($response->content())->data->products;
    $response->status(200);
    $this->assertCount(1, $products);
    foreach ($products as $product) {
      $this->assertDatabaseHas('sd_products', ['id' => $productId, 'name' => $product->name, 'manufacturer' => $product->manufacturer, 'product_status_id' => $product->product_status->id, 'all_department' => $product->department->id]);
    }
  }

  /** @group getAssociatedProductList */
  public function test_getAssociatedProductList_withLimit_returnsProductListDataBasedOnLimit()
  {
    $this->getloggedInUserForWeb('admin');
    $vendorId = factory(SdVendors::class)->create()->id;
    $productId1 = factory(SdProducts::class)->create()->id;
    ProductVendorRelation::create(['product_id' => $productId1, 'vendor_id' => $vendorId]);
    $productId2 = factory(SdProducts::class)->create()->id;
    ProductVendorRelation::create(['product_id' => $productId2, 'vendor_id' => $vendorId]);
    $limit = 1;
    $sortField = 'id';
    $sortOrder = 'asc';
    $response = $this->call('GET', url("service-desk/api/vendor/product/{$vendorId}"), ['limit' => $limit, 'sort-field' => $sortField, 'sort-order' => $sortOrder]);
    $products = json_decode($response->content())->data->products;
    $response->status(200);
    $this->assertCount($limit, $products);
    foreach ($products as $product) {
      $this->assertDatabaseHas('sd_products', ['id' => $productId1, 'name' => $product->name, 'manufacturer' => $product->manufacturer, 'product_status_id' => $product->product_status->id, 'all_department' => $product->department->id]);
    }
  }

  /** @group getAssociatedProductList */
  public function test_getAssociatedProductList_withSortFieldAndSortOrder_returnsProductListDataBasedOnSortOrderAndSortField()
  {
    $this->getloggedInUserForWeb('admin');
    $vendorId = factory(SdVendors::class)->create()->id;
    $productId1 = factory(SdProducts::class)->create()->id;
    ProductVendorRelation::create(['product_id' => $productId1, 'vendor_id' => $vendorId]);
    $productId2 = factory(SdProducts::class)->create()->id;
    ProductVendorRelation::create(['product_id' => $productId2, 'vendor_id' => $vendorId]);
    $limit = 1;
    $sortOrder = 'asc';
    $sortField = 'id';
    $response = $this->call('GET', url("service-desk/api/vendor/product/{$vendorId}"), ['limit' => $limit, 'sort-order' => $sortOrder, 'sort-field' => $sortField]);
    $products = json_decode($response->content())->data->products;
    $response->status(200);
    $this->assertCount($limit, $products);
    foreach ($products as $product) {
      $this->assertDatabaseHas('sd_products', ['id' => $productId1, 'name' => $product->name, 'manufacturer' => $product->manufacturer, 'product_status_id' => $product->product_status->id, 'all_department' => $product->department->id]);
    }
  }

  /** @group getAssociatedProductList */
  public function test_getAssociatedProductList_withPage_returnsProductListDataBasedOnSpecificPage()
  {
    $this->getloggedInUserForWeb('admin');
    $vendorId = factory(SdVendors::class)->create()->id;
    $productId1 = factory(SdProducts::class)->create()->id;
    ProductVendorRelation::create(['product_id' => $productId1, 'vendor_id' => $vendorId]);
    $productId2 = factory(SdProducts::class)->create()->id;
    ProductVendorRelation::create(['product_id' => $productId2, 'vendor_id' => $vendorId]);
    $limit = 1;
    $page = 2;
    $sortOrder = 'asc';
    $sortField = 'id';
    $response = $this->call('GET', url("service-desk/api/vendor/product/{$vendorId}"), ['limit' => $limit, 'sort-order' => $sortOrder, 'sort-field' => $sortField, 'page' => $page]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertEquals($data->current_page, $page);
    $this->assertCount($limit, $data->products);
    foreach ($data->products as $product) {
      $this->assertDatabaseHas('sd_products', ['id' => $productId2, 'name' => $product->name, 'manufacturer' => $product->manufacturer, 'product_status_id' => $product->product_status->id, 'all_department' => $product->department->id]);
    }
  }

  /** @group getAssociatedProductList */
  public function test_getAssociatedProductList_withSearchQueryEmpty_returnsCompleteProductListData()
  {
    $this->getloggedInUserForWeb('admin');
    $vendorId = factory(SdVendors::class)->create()->id;
    $productId1 = factory(SdProducts::class)->create()->id;
    ProductVendorRelation::create(['product_id' => $productId1, 'vendor_id' => $vendorId]);
    $productId2 = factory(SdProducts::class)->create()->id;
    ProductVendorRelation::create(['product_id' => $productId2, 'vendor_id' => $vendorId]);
    $response = $this->call('GET', url("service-desk/api/vendor/product/{$vendorId}"), ['search-query' => '']);
    $products = json_decode($response->content())->data->products;
    $response->status(200);
    $this->assertCount(SdProducts::count(), $products);
    foreach ($products as $product) {
      $this->assertDatabaseHas('sd_products', ['id' => $product->id, 'name' => $product->name, 'manufacturer' => $product->manufacturer, 'product_status_id' => $product->product_status->id, 'all_department' => $product->department->id]);
    }
  }

  /** @group getAssociatedProductList */
  public function test_getAssociatedProductList_withSearchQueryProductName_returnsProductListDataBasedOnProductName()
  {
    $this->getloggedInUserForWeb('admin');
    $vendorId = factory(SdVendors::class)->create()->id;
    $productName = 'Dell Laptop';
    $productId1 = factory(SdProducts::class)->create(['name' => $productName])->id;
    ProductVendorRelation::create(['product_id' => $productId1, 'vendor_id' => $vendorId]);
    $productId2 = factory(SdProducts::class)->create()->id;
    ProductVendorRelation::create(['product_id' => $productId2, 'vendor_id' => $vendorId]);
    $response = $this->call('GET', url("service-desk/api/vendor/product/{$vendorId}"), ['search-query' => $productName]);
    $products = json_decode($response->content())->data->products;
    $response->status(200);
    $this->assertCount(1, $products);
    foreach ($products as $product) {
      $this->assertDatabaseHas('sd_products', ['id' => $product->id, 'name' => $productName, 'manufacturer' => $product->manufacturer, 'product_status_id' => $product->product_status->id, 'all_department' => $product->department->id]);
    }
  }

  /** @group getAssociatedProductList */
  public function test_getAssociatedProductList_withSearchQueryWrongProductName_returnsProductListDataEmptyBasedOnWrongProductName()
  {
    $productName = 'wrong-product-status-name';
    $this->wrongSearchQueryForProduct($productName);
  }

  /** @group getAssociatedProductList */
  public function test_getAssociatedProductList_withSearchQueryProductManufacturerName_returnsProductListDataBasedOnProductManufacturerName()
  {
    $this->getloggedInUserForWeb('admin');
    $vendorId = factory(SdVendors::class)->create()->id;
    $manufacturerName = 'Dell Laptop';
    $productId1 = factory(SdProducts::class)->create(['manufacturer' => $manufacturerName])->id;
    ProductVendorRelation::create(['product_id' => $productId1, 'vendor_id' => $vendorId]);
    $productId2 = factory(SdProducts::class)->create()->id;
    ProductVendorRelation::create(['product_id' => $productId2, 'vendor_id' => $vendorId]);
    $response = $this->call('GET', url("service-desk/api/vendor/product/{$vendorId}"), ['search-query' => $manufacturerName]);
    $products = json_decode($response->content())->data->products;
    $response->status(200);
    $this->assertCount(1, $products);
    foreach ($products as $product) {
      $this->assertDatabaseHas('sd_products', ['id' => $product->id, 'name' => $product->name, 'manufacturer' => $manufacturerName, 'product_status_id' => $product->product_status->id, 'all_department' => $product->department->id]);
    }
  }

  /** @group getAssociatedProductList */
  public function test_getAssociatedProductList_withSearchQueryWrongProductManufacturerName_returnsProductListDataEmptyBasedOnWrongProductManufacturerName()
  {
    $productManufacturer = 'wrong-product-manufacturer';
    $this->wrongSearchQueryForProduct($productManufacturer);
  }

  /** @group getAssociatedProductList */
  public function test_getAssociatedProductList_withSearchQueryProductDepartmentName_returnsProductListDataBasedOnProductDepartmentName()
  {
    $this->getloggedInUserForWeb('admin');
    $vendorId = factory(SdVendors::class)->create()->id;
    $departmentName = 'Sales';
    $departmentId = factory(Department::class)->create(['name' => $departmentName])->id;
    $productId1 = factory(SdProducts::class)->create(['all_department' => $departmentId])->id;
    ProductVendorRelation::create(['product_id' => $productId1, 'vendor_id' => $vendorId]);
    $productId2 = factory(SdProducts::class)->create()->id;
    ProductVendorRelation::create(['product_id' => $productId2, 'vendor_id' => $vendorId]);
    $response = $this->call('GET', url("service-desk/api/vendor/product/{$vendorId}"), ['search-query' => $departmentName]);
    $products = json_decode($response->content())->data->products;
    $response->status(200);
    $this->assertCount(1, $products);
    foreach ($products as $product) {
      $this->assertDatabaseHas('sd_products', ['id' => $product->id, 'name' => $product->name, 'manufacturer' => $product->manufacturer, 'product_status_id' => $product->product_status->id, 'all_department' => $departmentId]);
    }
  }

  /** @group getAssociatedProductList */
  public function test_getAssociatedProductList_withSearchQueryWrongDepartmentName_returnsProductListDataEmptyBasedOnWrongDepartmentName()
  {
    $departmentName = 'wrong-department-name';
    $this->wrongSearchQueryForProduct($departmentName);
  }

  /** @group getAssociatedProductList */
  public function test_getAssociatedProductList_withSearchQueryProductStatus_returnsProductListDataBasedOnProductStatus()
  {
    $this->getloggedInUserForWeb('admin');
    $vendorId = factory(SdVendors::class)->create()->id;
    $productStatusName = 'Finish';
    $productStatusId = factory(SdProductstatus::class)->create(['name' => $productStatusName])->id;
    $productId1 = factory(SdProducts::class)->create(['product_status_id' => $productStatusId])->id;
    ProductVendorRelation::create(['product_id' => $productId1, 'vendor_id' => $vendorId]);
    $productId2 = factory(SdProducts::class)->create()->id;
    ProductVendorRelation::create(['product_id' => $productId2, 'vendor_id' => $vendorId]);
    $response = $this->call('GET', url("service-desk/api/vendor/product/{$vendorId}"), ['search-query' => $productStatusName]);
    $products = json_decode($response->content())->data->products;
    $response->status(200);
    $this->assertCount(1, $products);
    foreach ($products as $product) {
      $this->assertDatabaseHas('sd_products', ['id' => $product->id, 'name' => $product->name, 'manufacturer' => $product->manufacturer, 'product_status_id' => $productStatusId, 'all_department' => $product->department->id]);
    }
  }

  /** @group getAssociatedProductList */
  public function test_getAssociatedProductList_withSearchQueryWrongProductStatus_returnsProductListDataEmptyBasedOnWrongProductStatus()
  {
    $productStatusName = 'wrong-product-status-name';
    $this->wrongSearchQueryForProduct($productStatusName);
  }

  /**
   * method for wrong search query for product, it return empty product list data
   * associated assertions are done in this method
   * @param string $searchQuery
   * @return null
   */
  private function wrongSearchQueryForProduct(String $searchQuery)
  {
    $this->getloggedInUserForWeb('admin');
    $vendorId = factory(SdVendors::class)->create()->id;
    $productId = factory(SdProducts::class)->create()->id;
    ProductVendorRelation::create(['product_id' => $productId, 'vendor_id' => $vendorId]);
    $response = $this->call('GET', url("service-desk/api/vendor/product/{$vendorId}"), ['search-query' => $searchQuery]);
    $products = json_decode($response->content())->data->products;
    $response->status(200);
    $this->assertCount(0, $products);
    $this->assertEmpty($products);
  }

}
