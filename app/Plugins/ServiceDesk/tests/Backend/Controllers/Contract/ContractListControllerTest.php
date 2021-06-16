<?php

namespace App\Plugins\ServiceDesk\tests\Backend\Controllers\Contract;

use Tests\AddOnTestCase;
use App\Plugins\ServiceDesk\Model\Contract\SdContract;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\Plugins\ServiceDesk\Model\Vendor\SdVendors;
use App\Plugins\ServiceDesk\Model\Contract\ContractType;
use App\Plugins\ServiceDesk\Model\Contract\License;
use App\Plugins\ServiceDesk\Model\Contract\SdContractStatus;
use App\Traits\FaveoDateParser;
use App\User;

/**
 * Tests ContractListController
 * 
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
*/
class ContractListControllerTest extends AddOnTestCase
{
  use FaveoDateParser;

  /* @group getContractList */
  public function test_getContractList_withLimit_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdContract::class,3)->create();
    $limit = 2;
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['limit' => $limit]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(2, $contracts);
    $this->assertDatabaseHas('sd_contracts', ['id' => reset($contracts)->id, 'name' => reset($contracts)->name]);
  }

  /* @group getContractList */
  public function test_getContractList_withSorting_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdContract::class,3)->create();
    $sortOrder = 'asc';
    $sortField = 'id';
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['sort-order' => $sortOrder, 'sort-field' => $sortField]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(3, $contracts);
    $this->assertDatabaseHas('sd_contracts', ['id' => reset($contracts)->id, 'name' => reset($contracts)->name]);
    $this->assertTrue($contracts[0]->id < $contracts[1]->id);
    $this->assertTrue($contracts[1]->id < $contracts[2]->id);
  }

  /* @group getContractList */
  public function test_getContractList_withPage_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdContract::class,3)->create();
    $sortOrder = 'asc';
    $page = 2;
    $limit = 1;
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['sort-order' => $sortOrder, 'limit' => $limit, 'page' => $page]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->contracts);
    $this->assertEquals($data->current_page, $page);
    $this->assertDatabaseHas('sd_contracts', ['id' => reset($data->contracts)->id, 'name' => reset($data->contracts)->name]);
  }

  /* @group getContractList */
  public function test_getContractList_withWrongPage_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdContract::class,3)->create();
    $sortOrder = 'asc';
    $page = 2;
    $limit = 3;
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['sort-order' => $sortOrder, 'limit' => $limit, 'page' => $page]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(0, $data->contracts);
    $this->assertEquals($data->current_page, $page);
  }

  /* @group getContractList */
  public function test_getContractList_withContractId_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdContract::class)->create();
    $contractId = factory(SdContract::class)->create()->id;
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['contract_ids' => $contractId]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(1, $contracts);
    foreach ($contracts as $contract) {
      $this->assertDatabaseHas('sd_contracts', ['id' => $contractId, 'name' => $contract->name]);
    }
  }

  /* @group getContractList */
  public function test_getContractList_withWrongContractId_returnsEmptyContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdContract::class)->create();
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['contract_ids' => ['wrong-contract-id']]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(0, $contracts);
    $this->assertEmpty($contracts);
  }

  /* @group getContractList */
  public function test_getContractList_withContractTypeId_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdContract::class)->create(['contract_type_id' => 1]);
    $contractTypeId = factory(SdContract::class)->create(['contract_type_id' => 2])->contract_type_id;
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['contract_type_ids' => $contractTypeId]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(1, $contracts);
    foreach ($contracts as $contract) {
      $this->assertDatabaseHas('sd_contracts', ['contract_type_id' => $contractTypeId, 'name' => $contract->name]);
    }
  }

  /* @group getContractList */
  public function test_getContractList_withWrongContractTypeId_returnsEmptyContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdContract::class)->create(['contract_type_id' => 1]);
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['contract_type_ids' => ['wrong-contract-type-id']]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(0, $contracts);
    $this->assertEmpty($contracts);
  }

  /* @group getContractList */
  public function test_getContractList_withContractApproverId_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdContract::class)->create();
    $approverId = factory(SdContract::class)->create(['approver_id' => $this->user->id])->approver_id;
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['approver_ids' => $approverId]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(1, $contracts);
    foreach ($contracts as $contract) {
      $this->assertDatabaseHas('sd_contracts', ['approver_id' => $approverId, 'name' => $contract->name]);
    }
  }

  /* @group getContractList */
  public function test_getContractList_withWrongContractApproverId_returnsEmptyContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdContract::class)->create();
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['approver_ids' => ['wrong-contract-approver-id']]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(0, $contracts);
    $this->assertEmpty($contracts);
  }

  /* @group getContractList */
  public function test_getContractList_withVendorId_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    $vendorId = factory(SdVendors::class)->create()->id;
    factory(SdContract::class)->create();
    $contractId = factory(SdContract::class)->create(['vendor_id' => $vendorId])->id;
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['vendor_ids' => $vendorId]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(1, $contracts);
    foreach ($contracts as $contract) {
      $this->assertDatabaseHas('sd_contracts', ['id' => $contractId, 'vendor_id' => $vendorId, 'name' => $contract->name]);
    }
  }

  /* @group getContractList */
  public function test_getContractList_withWrongContractVendorId_returnsEmptyContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdContract::class)->create();
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['vendor_ids' => ['wrong-contract-vendor-id']]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(0, $contracts);
    $this->assertEmpty($contracts);
  }

  /* @group getContractList */
  public function test_getContractList_withContractLicenseTypeId_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdContract::class)->create(['license_type_id' => 1]);
    $licenseTypeId = factory(SdContract::class)->create(['license_type_id' => 2])->license_type_id;
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['license_type_ids' => $licenseTypeId]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(1, $contracts);
    foreach ($contracts as $contract) {
      $this->assertDatabaseHas('sd_contracts', ['license_type_id' => $licenseTypeId, 'name' => $contract->name]);
    }
  }

  /* @group getContractList */
  public function test_getContractList_withWrongContractLicenseTypeId_returnsEmptyContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdContract::class)->create();
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['license_type_ids' => ['wrong-contract-license-type-id']]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(0, $contracts);
    $this->assertEmpty($contracts);
  }

  /* @group getContractList */
  public function test_getContractList_withContractLicenseCount_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdContract::class)->create(['licensce_count' => 1111]);
    $licenseCount = factory(SdContract::class)->create(['licensce_count' => 1234])->licensce_count;
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['license_counts' => $licenseCount]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(1, $contracts);
    foreach ($contracts as $contract) {
      $this->assertDatabaseHas('sd_contracts', ['licensce_count' => $licenseCount, 'name' => $contract->name]);
    }
  }

  /* @group getContractList */
  public function test_getContractList_withWrongContractLicenseCount_returnsEmptyContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdContract::class)->create();
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['license_counts' => ['wrong-contract-license-count']]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(0, $contracts);
    $this->assertEmpty($contracts);
  }

  /* @group getContractList */
  public function test_getContractList_withContractStatusId_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdContract::class)->create(['status_id' => 1]);
    $statusId = factory(SdContract::class)->create(['status_id' => 2])->status_id;
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['status_ids' => $statusId]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(1, $contracts);
    foreach ($contracts as $contract) {
      $this->assertDatabaseHas('sd_contracts', ['status_id' => $statusId, 'name' => $contract->name]);
    }
  }

  /* @group getContractList */
  public function test_getContractList_withWrongContractStatusId_returnsEmptyContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdContract::class)->create();
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['status_ids' => ['wrong-contract-status-id']]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(0, $contracts);
    $this->assertEmpty($contracts);
  }

  /* @group getContractList */
  public function test_getContractList_withContractRenewalStatusId_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdContract::class)->create(['renewal_status_id' => 7]);
    $renewalStatusId = factory(SdContract::class)->create(['renewal_status_id' => 8])->renewal_status_id;
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['renewal_status_ids' => $renewalStatusId]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(1, $contracts);
    foreach ($contracts as $contract) {
      $this->assertDatabaseHas('sd_contracts', ['renewal_status_id' => $renewalStatusId, 'name' => $contract->name]);
    }
  }

  /* @group getContractList */
  public function test_getContractList_withWrongContractRenewalStatusId_returnsEmptyContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdContract::class)->create();
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['renewal_status_ids' => ['wrong-contract-status-id']]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(0, $contracts);
    $this->assertEmpty($contracts);
  }

  /* @group getContractList */
  public function test_getContractList_withContractOwnerId_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdContract::class)->create();
    $ownerId = factory(SdContract::class)->create(['owner_id' => $this->user->id])->owner_id;
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['owner_ids' => $ownerId]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(1, $contracts);
    foreach ($contracts as $contract) {
      $this->assertDatabaseHas('sd_contracts', ['owner_id' => $ownerId, 'name' => $contract->name]);
    }
  }

  /* @group getContractList */
  public function test_getContractList_withWrongContractOwnerId_returnsEmptyContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdContract::class)->create();
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['owner_ids' => 'wrong-contract-owner-id']);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(0, $contracts);
    $this->assertEmpty($contracts);
  }

  /* @group getContractList */
  public function test_getContractList_withSearchContractWrongSearchString_returnsEmptyContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdContract::class, 2)->create();
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['search-query' => 'wrong-search-string']);
     $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(0, $contracts);
    $this->assertEmpty($contracts);
  }

  /* @group getContractList */
  public function test_getContractList_withSearchContractNameAsSearchQuery_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    $contract = factory(SdContract::class)->create(['name' => "Laptop 1234S"]);
    factory(SdContract::class)->create();
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['search-query' => $contract->name]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(1, $contracts);
    $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => reset($contracts)->name]);
  }

  /* @group getContractList */
  public function test_getContractList_withSearchContractIdentifierAsSearchQuery_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    $contract = factory(SdContract::class)->create(['identifier' => "CNTR-12"]);
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['search-query' => $contract->identifier]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(1, $contracts);
    $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => reset($contracts)->name, 'identifier' => $contract->identifier]);
  }

  /* @group getContractList */
  public function test_getContractList_withSearchContractTypeNameAsSearchQuery_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    $contractType = ContractType::find(2);
    $contract = factory(SdContract::class)->create(['contract_type_id' => $contractType->id]);
    factory(SdContract::class)->create(['contract_type_id' => 1]);
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['search-query' => $contractType->name]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(1, $contracts);
    $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => reset($contracts)->name, 'contract_type_id' => $contractType->id]);
  }

  /* @group getContractList */
  public function test_getContractList_withSearchVendorNameAsSearchQuery_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    $vendor = factory(SdVendors::class)->create();
    $contract = factory(SdContract::class)->create(['vendor_id' => $vendor->id]);
    factory(SdContract::class)->create(['contract_type_id' => 1]);
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['search-query' => $vendor->name]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(1, $contracts);
    $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => reset($contracts)->name, 'vendor_id' => $vendor->id]);
  }

  /* @group getContractList */
  public function test_getContractList_withSearchLicenseTypeNameAsSearchQuery_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    $licenseType = License::find(2);
    $contract = factory(SdContract::class)->create(['license_type_id' => $licenseType->id]);
    factory(SdContract::class)->create(['license_type_id' => 1]);
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['search-query' => $licenseType->name]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(1, $contracts);
    $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => reset($contracts)->name, 'license_type_id' => $licenseType->id]);
  }

  /* @group getContractList */
  public function test_getContractList_withSearchContractStatusNameAsSearchQuery_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    $status = SdContractStatus::find(2);
    $contract = factory(SdContract::class)->create(['status_id' => $status->id]);
    factory(SdContract::class)->create(['status_id' => 1]);
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['search-query' => $status->name]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(1, $contracts);
    $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => reset($contracts)->name, 'status_id' => $status->id]);
  }

  /* @group getContractList */
  public function test_getContractList_withSearchContractRenewalStatusNameAsSearchQuery_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    $renewalStatus = SdContractStatus::find(7);
    $contract = factory(SdContract::class)->create(['renewal_status_id' => $renewalStatus->id]);
    factory(SdContract::class)->create(['renewal_status_id' => 8]);
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['search-query' => $renewalStatus->name]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(1, $contracts);
    $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => reset($contracts)->name, 'renewal_status_id' => $renewalStatus->id]);
  }

  /** @group getContractList */
  public function test_getContractList_withSearchQueryContractApproverByFirstName_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    $contract = factory(SdContract::class)->create(['approver_id' => $this->user->id]);
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['search-query' => $this->user->first_name]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->contracts);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->contracts)->name, $contract->name);
    $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => reset($data->contracts)->name, 'approver_id' => $this->user->id]);
  }

  /** @group getContractList */
  public function test_getContractList_withSearchQueryContractApproverByLastName_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    $contract = factory(SdContract::class)->create(['approver_id' => $this->user->id]);
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['search-query' => $this->user->last_name]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->contracts);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->contracts)->name, $contract->name);
    $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => reset($data->contracts)->name, 'approver_id' => $this->user->id]);
  }

  /** @group getContractList */
  public function test_getContractList_withSearchQueryContractApproverByFullName_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    $contract = factory(SdContract::class)->create(['approver_id' => $this->user->id]);
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['search-query' => $this->user->first_name .' '. $this->user->last_name]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->contracts);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->contracts)->name, $contract->name);
    $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => reset($data->contracts)->name, 'approver_id' => $this->user->id]);
  }

  /** @group getContractList */
  public function test_getContractList_withSearchQueryContractApproverByUserName_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    $contract = factory(SdContract::class)->create(['approver_id' => $this->user->id]);
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['search-query' => $this->user->user_name]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->contracts);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->contracts)->name, $contract->name);
    $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => reset($data->contracts)->name, 'approver_id' => $this->user->id]);
  }

  /** @group getContractList */
  public function test_getContractList_withSearchQueryContractApproverByEmail_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    $contract = factory(SdContract::class)->create(['approver_id' => $this->user->id]);
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['search-query' => $this->user->email]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->contracts);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->contracts)->name, $contract->name);
    $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => reset($data->contracts)->name, 'approver_id' => $this->user->id]);
  }

  /** @group getContractList */
  public function test_getContractList_withSearchQueryContractOwnerByFirstName_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    $contract = factory(SdContract::class)->create(['owner_id' => $this->user->id]);
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['search-query' => $this->user->first_name]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->contracts);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->contracts)->name, $contract->name);
    $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => reset($data->contracts)->name, 'owner_id' => $this->user->id]);
  }

  /** @group getContractList */
  public function test_getContractList_withSearchQueryContractOwnerByLastName_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    $contract = factory(SdContract::class)->create(['owner_id' => $this->user->id]);
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['search-query' => $this->user->last_name]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->contracts);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->contracts)->name, $contract->name);
    $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => reset($data->contracts)->name, 'owner_id' => $this->user->id]);
  }

  /** @group getContractList */
  public function test_getContractList_withSearchQueryContractOwnerByFullName_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    $contract = factory(SdContract::class)->create(['owner_id' => $this->user->id]);
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['search-query' => $this->user->first_name .' '. $this->user->last_name]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->contracts);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->contracts)->name, $contract->name);
    $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => reset($data->contracts)->name, 'owner_id' => $this->user->id]);
  }

  /** @group getContractList */
  public function test_getContractList_withSearchQueryContractOwnerByUserName_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    $contract = factory(SdContract::class)->create(['owner_id' => $this->user->id]);
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['search-query' => $this->user->user_name]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->contracts);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->contracts)->name, $contract->name);
    $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => reset($data->contracts)->name, 'owner_id' => $this->user->id]);
  }

  /** @group getContractList */
  public function test_getContractList_withSearchQueryContractOwnerByEmail_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    $contract = factory(SdContract::class)->create(['owner_id' => $this->user->id]);
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['search-query' => $this->user->email]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->contracts);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->contracts)->name, $contract->name);
    $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => reset($data->contracts)->name, 'owner_id' => $this->user->id]);
  }

  /* @group getContractList */
  public function test_getContractList_withContractStartDateTimeRange_returnsContractList()
  {
    $this->getLoggedInUserForWeb('agent');
    factory(SdContract::class)->create(['contract_start_date' => '2020-07-12 05:12:00', 'contract_end_date' => '2020-07-14 05:12:00']);
    factory(SdContract::class)->create(['contract_start_date' => '2020-07-14 05:12:00', 'contract_end_date' => '2020-07-16 05:12:00']);
    factory(SdContract::class)->create(['contract_start_date' => '2020-07-16 05:12:00', 'contract_end_date' => '2020-07-18 05:12:00']);
    factory(SdContract::class)->create(['contract_start_date' => '2020-07-18 05:12:00', 'contract_end_date' => '2020-07-20 05:12:00']);
    $endTimestamp = '2020-07-16 05:12:00';
    $startTimestamp = '2020-07-12 05:12:00';
    $startTime = changeTimezoneForDatetime($startTimestamp, agentTimeZone(), 'UTC');
    $endTime = changeTimezoneForDatetime($endTimestamp, agentTimeZone(), 'UTC');
    $timeRange = "date::{$startTimestamp}~{$endTimestamp}";
    $formattedRange = $this->getTimeRangeObject($timeRange, "UTC");
    $initialCount = SdContract::where([['contract_start_date', '<=', $endTime],['contract_start_date', '>=', $startTime]])->count();
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['contract_start_date' => $timeRange]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount($initialCount, $contracts);
    foreach ($contracts as $contract) {
      $contractStartDate = SdContract::find($contract->id)->contract_start_date;
      $this->assertGreaterThanOrEqual($startTime, $contractStartDate);
      $this->assertLessThanOrEqual($endTime, $contractStartDate);
      $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => $contract->name]);
    }
  }

  /* @group getContractList */
  public function test_getContractList_withContractEndDateTimeRange_returnsContractList()
  {
    $this->getLoggedInUserForWeb('agent');
    factory(SdContract::class)->create(['contract_start_date' => '2020-07-12 05:12:00', 'contract_end_date' => '2020-07-14 05:12:00']);
    factory(SdContract::class)->create(['contract_start_date' => '2020-07-14 05:12:00', 'contract_end_date' => '2020-07-16 05:12:00']);
    factory(SdContract::class)->create(['contract_start_date' => '2020-07-16 05:12:00', 'contract_end_date' => '2020-07-18 05:12:00']);
    factory(SdContract::class)->create(['contract_start_date' => '2020-07-18 05:12:00', 'contract_end_date' => '2020-07-20 05:12:00']);
    $endTimestamp = '2020-07-16 05:12:00';
    $startTimestamp = '2020-07-12 05:12:00';
    $startTime = changeTimezoneForDatetime($startTimestamp, agentTimeZone(), 'UTC');
    $endTime = changeTimezoneForDatetime($endTimestamp, agentTimeZone(), 'UTC');
    $timeRange = "date::{$startTimestamp}~{$endTimestamp}";
    $formattedRange = $this->getTimeRangeObject($timeRange, "UTC");
    $initialCount = SdContract::where([['contract_end_date', '<=', $endTime],['contract_end_date', '>=', $startTime]])->count();
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['contract_end_date' => $timeRange]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount($initialCount, $contracts);
    foreach ($contracts as $contract) {
      $this->assertGreaterThanOrEqual($startTime, $contract->expiry->timestamp);
      $this->assertLessThanOrEqual($endTime, $contract->expiry->timestamp);
      $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => $contract->name]);
    }
  }

    /* @group getContractList */
  public function test_getContractList_withContractUpdatedAtTimeRange_returnsContractList()
  {
    $this->getLoggedInUserForWeb('agent');
    factory(SdContract::class)->create(['contract_start_date' => '2020-07-12 05:12:00', 'contract_end_date' => '2020-07-14 05:12:00']);
    factory(SdContract::class)->create(['contract_start_date' => '2020-07-14 05:12:00', 'contract_end_date' => '2020-07-16 05:12:00']);
    factory(SdContract::class)->create(['contract_start_date' => '2020-07-16 05:12:00', 'contract_end_date' => '2020-07-18 05:12:00']);
    factory(SdContract::class)->create(['contract_start_date' => '2020-07-18 05:12:00', 'contract_end_date' => '2020-07-20 05:12:00']);
    $endTimestamp = '2020-07-16 05:12:00';
    $startTimestamp = '2020-07-12 05:12:00';
    $startTime = changeTimezoneForDatetime($startTimestamp, agentTimeZone(), 'UTC');
    $endTime = changeTimezoneForDatetime($endTimestamp, agentTimeZone(), 'UTC');
    $timeRange = "date::{$startTimestamp}~{$endTimestamp}";
    $formattedRange = $this->getTimeRangeObject($timeRange, "UTC");
    $initialCount = SdContract::where([['updated_at', '<=', $endTime],['updated_at', '>=', $startTime]])->count();
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['updated_at' => $timeRange]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount($initialCount, $contracts);
    foreach ($contracts as $contract) {
      $contractUpdatedAt = SdContract::find($contract->id)->updated_at;
      $this->assertGreaterThanOrEqual($startTime, $contractUpdatedAt);
      $this->assertLessThanOrEqual($endTime, $contractUpdatedAt);
      $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => $contract->name]);
    }
  }

    /* @group getContractList */
  public function test_getContractList_withContractCreatedAtTimeRange_returnsContractList()
  {
    $this->getLoggedInUserForWeb('agent');
    factory(SdContract::class)->create(['contract_start_date' => '2020-07-12 05:12:00', 'contract_end_date' => '2020-07-14 05:12:00']);
    factory(SdContract::class)->create(['contract_start_date' => '2020-07-14 05:12:00', 'contract_end_date' => '2020-07-16 05:12:00']);
    factory(SdContract::class)->create(['contract_start_date' => '2020-07-16 05:12:00', 'contract_end_date' => '2020-07-18 05:12:00']);
    factory(SdContract::class)->create(['contract_start_date' => '2020-07-18 05:12:00', 'contract_end_date' => '2020-07-20 05:12:00']);
    $endTimestamp = '2020-07-16 05:12:00';
    $startTimestamp = '2020-07-12 05:12:00';
    $startTime = changeTimezoneForDatetime($startTimestamp, agentTimeZone(), 'UTC');
    $endTime = changeTimezoneForDatetime($endTimestamp, agentTimeZone(), 'UTC');
    $timeRange = "date::{$startTimestamp}~{$endTimestamp}";
    $formattedRange = $this->getTimeRangeObject($timeRange, "UTC");
    $initialCount = SdContract::where([['created_at', '<=', $endTime],['created_at', '>=', $startTime]])->count();
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['created_at' => $timeRange]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount($initialCount, $contracts);
    foreach ($contracts as $contract) {
      $contractCreatedAt = SdContract::find($contract->id)->created_at;
      $this->assertGreaterThanOrEqual($startTime, $contractCreatedAt);
      $this->assertLessThanOrEqual($endTime, $contractCreatedAt);
      $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => $contract->name]);
    }
  }

  /* @group getContractList */
  public function test_getContractList_withAssetId_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    $assetId = factory(SdAssets::class)->create()->id;
    $contract = factory(SdContract::class)->create();
    $contract->attachAsset()->sync([$assetId => ['type' => 'sd_contracts']]);
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['asset_ids' => $assetId]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(1, $contracts);
    $contract = reset($contracts);
    $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => $contract->name]);
    $this->assertDatabaseHas('sd_common_asset_relation', ['asset_id' => $assetId, 'type_id' => $contract->id, 'type' => 'sd_contracts']);
  }

  /* @group getContractList */
  public function test_getContractList_withWrongAssetId_returnsEmptyContractList()
  {
    $this->getloggedInUserForWeb('admin');
    factory(SdContract::class, 3)->create();
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['asset_ids' => 'wrong-asset-id']);
    $contractsInResponse = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(0, $contractsInResponse);
    $this->assertEmpty($contractsInResponse);
  }

  /* @group getContractList */
  public function test_getContractList_withSearchQueryAssetName_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create(['name' => 'Desktop']);
    factory(SdContract::class, 2)->create();
    $contract = factory(SdContract::class)->create();
    $contract->attachAsset()->sync([$asset->id => ['type' => 'sd_contracts']]);
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['search-query' => $asset->name]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(1, $contracts);
    $contracts = reset($contracts);
    $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => $contract->name]);
    $this->assertDatabaseHas('sd_common_asset_relation', ['asset_id' => $asset->id, 'type_id' => $contract->id, 'type' => 'sd_contracts']);
  }

  /* @group getContractList */
  public function test_getContractList_withNotifyAgentId_returnsContractList()
  {
    $this->getLoggedInUserForWeb('agent');
    $contract = factory(SdContract::class)->create();
    $contract->notifyAgents()->sync([$this->user->id]);
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['notify_agent_ids' => $this->user->id]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(1, $contracts);
    $contract = reset($contracts);
    $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => $contract->name]);
    $this->assertDatabaseHas('sd_contract_user', ['agent_id' => $this->user->id, 'contract_id' => $contract->id]);
  }

  /* @group getContractList */
  public function test_getContractList_withContractCostRange_returnsContractList()
  {
    $this->getLoggedInUserForWeb('agent');
    factory(SdContract::class)->create(['cost' => 999]);
    factory(SdContract::class)->create(['cost' => 1999]);
    factory(SdContract::class)->create(['cost' => 2999]);
    factory(SdContract::class)->create(['cost' => 3999]);
    $costBegin = 1000;
    $costEnd = 3000;
    $initialCount = SdContract::where([['cost', '>=', $costBegin],['cost', '<=', $costEnd]])->count();
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['cost_begin' => $costBegin, 'cost_end' => $costEnd]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount($initialCount, $contracts);
    foreach ($contracts as $contract) {
      $this->assertGreaterThanOrEqual($costBegin, $contract->cost);
      $this->assertLessThanOrEqual($costEnd, $contract->cost);
      $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => $contract->name, 'cost' => $contract->cost]);
    }
  }

  /* @group getContractList */
  public function test_getContractList_withContractNotifyInDaysRange_returnsContractList()
  {
    $this->getLoggedInUserForWeb('agent');
    factory(SdContract::class)->create(['notify_before' => 2]);
    factory(SdContract::class)->create(['notify_before' => 3]);
    factory(SdContract::class)->create(['notify_before' => 4]);
    factory(SdContract::class)->create(['notify_before' => 5]);
    $notifyInDaysBegin = 1;
    $notifyInDaysEnd = 4;
    $initialCount = SdContract::where([['notify_before', '>=', $notifyInDaysBegin],['notify_before', '<=', $notifyInDaysEnd]])->count();
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['notify_in_days_begin' => $notifyInDaysBegin, 'notify_in_days_end' => $notifyInDaysEnd]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount($initialCount, $contracts);
    foreach ($contracts as $contract) {
      $notifyBeforeInDays = SdContract::find($contract->id)->notify_before;
      $this->assertGreaterThanOrEqual($notifyInDaysBegin, $notifyBeforeInDays);
      $this->assertLessThanOrEqual($notifyInDaysEnd, $notifyBeforeInDays);
      $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => $contract->name, 'notify_before' => $notifyBeforeInDays]);
    }
  }

  /* @group getContractList */
  public function test_getContractList_withWrongNotifyAgentId_returnsEmptyContractList()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdContract::class, 3)->create();
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['notify_agent_ids' => 'wrong-notify-agent-id']);
    $contractsInResponse = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(0, $contractsInResponse);
    $this->assertEmpty($contractsInResponse);
  }

  /* @group getContractList */
  public function test_getContractList_withSearchQueryNotifyAgentFullName_returnsContractList()
  {
    $this->getLoggedInUserForWeb('agent');
    factory(SdContract::class, 2)->create();
    $contract = factory(SdContract::class)->create();
    $contract->notifyAgents()->sync([$this->user->id]);
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['search-query' => $this->user->full_name]);
    $contracts = json_decode($response->content())->data->contracts;
    $response->status(200);
    $this->assertCount(1, $contracts);
    $contracts = reset($contracts);
    $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => $contract->name]);
    $this->assertDatabaseHas('sd_contract_user', ['agent_id' => $this->user->id, 'contract_id' => $contract->id]);
  }

  /** @group getContractList */
  public function test_getContractList_withSearchQueryNotifyAgentByFirstName_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdContract::class, 2)->create();
    $contract = factory(SdContract::class)->create();
    $contract->notifyAgents()->sync([$this->user->id]);
    User::find($this->user->id)->update(['first_name' => 'Test']);
    $this->user = User::find($this->user->id);
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['search-query' => $this->user->first_name]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->contracts);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->contracts)->name, $contract->name);
    $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => reset($data->contracts)->name]);
    $this->assertDatabaseHas('sd_contract_user', ['agent_id' => $this->user->id, 'contract_id' => $contract->id]);
  }

  /** @group getContractList */
  public function test_getContractList_withSearchQueryNotifyAgentByLastName_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdContract::class, 2)->create();
    $contract = factory(SdContract::class)->create();
    $contract->notifyAgents()->sync([$this->user->id]);
    User::find($this->user->id)->update(['last_name' => 'Test']);
    $this->user = User::find($this->user->id);
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['search-query' => $this->user->last_name]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->contracts);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->contracts)->name, $contract->name);
    $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => reset($data->contracts)->name]);
    $this->assertDatabaseHas('sd_contract_user', ['agent_id' => $this->user->id, 'contract_id' => $contract->id]);
  }

  /** @group getContractList */
  public function test_getContractList_withSearchQueryNotifyAgentByUserName_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdContract::class, 2)->create();
    $contract = factory(SdContract::class)->create();
    $contract->notifyAgents()->sync([$this->user->id]);
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['search-query' => $this->user->user_name]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->contracts);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->contracts)->name, $contract->name);
    $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => reset($data->contracts)->name]);
    $this->assertDatabaseHas('sd_contract_user', ['agent_id' => $this->user->id, 'contract_id' => $contract->id]);
  }

  /** @group getContractList */
  public function test_getContractList_withSearchQueryNotifyAgentByEmail_returnsContractList()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdContract::class, 2)->create();
    $contract = factory(SdContract::class)->create();
    $contract->notifyAgents()->sync([$this->user->id]);
    $response = $this->call('GET', url('service-desk/api/contract-list'), ['search-query' => $this->user->email]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->contracts);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->contracts)->name, $contract->name);
    $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => reset($data->contracts)->name]);
    $this->assertDatabaseHas('sd_contract_user', ['agent_id' => $this->user->id, 'contract_id' => $contract->id]);
  }

}