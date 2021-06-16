<?php
namespace App\Plugins\ServiceDesk\tests\Backend\Controllers\Vendor;

use Tests\AddOnTestCase;
use App\Plugins\ServiceDesk\Model\Contract\ContractType;

/**
 * Tests ApiContractTypeController
 * 
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
*/
class ApiContractTypeControllerTest extends AddOnTestCase
{
  /** @group createUpdateContractType */
  public function test_createUpdateContractType_withFieldValidationVendorNameMissing_returnsNameFieldIsRequired()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('POST', url('service-desk/api/contract-type'));
    $response->assertStatus(412);
  }

  /** @group createUpdateContractType */
  public function test_createUpdateContractType_withContractTypeFields_returnsContractTypeSavedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $contractTypeName = 'Yearly';
    $response = $this->call('POST', url('service-desk/api/contract-type'), ['name' => $contractTypeName]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_contract_types', ['name' => $contractTypeName]);
  }

  /** @group createUpdateContractType */
  public function test_createUpdateContractType_withUpdateContractTypeName_returnsContractSavedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $contractType = factory(ContractType::class)->create();
    $updatedContractTypeName = 'Quaterly';
    $response = $this->call('POST', url('service-desk/api/contract-type'), [
      'id' => $contractType->id,
      'name' => $updatedContractTypeName
    ]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_contract_types', ['id' => $contractType->id, 'name' => $updatedContractTypeName]);
  }

  /** @group getContractType */
  public function test_getContractType_withWrongContractTypeId_returnsContractTypeNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url("service-desk/api/contract-type/wrong-contract-type-id"));
    $response->assertStatus(400);
  }

  /** @group getContractType */
  public function test_getContractType_withContractTypeId_returnContractTypeData()
  {
    $this->getLoggedInUserForWeb('admin');
    $contractTypeId = factory(ContractType::class)->create()->id;
    $response = $this->call('GET', url("service-desk/api/contract-type/$contractTypeId"));
    $contractType = json_decode($response->content())->data->contract_type;
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_contract_types', ['id' => $contractTypeId, 'name' => $contractType->name]);
  }

  /** @group getContractTypeList */
  public function test_getContractTypeList_withoutAnyExtraParameter_returnsCompleteContractTypesData()
  {
    $this->getloggedInUserForWeb('admin');
    factory(ContractType::class, 3)->create();
    $response = $this->call('GET', url('service-desk/api/contract-type-list'));
    $contractTypes = json_decode($response->content())->data->contract_types;
    $response->status(200);
    // 3 contract types has beed seeded so +3
    $this->assertCount(3+3, $contractTypes);
    $contractType = reset($contractTypes);
    $this->assertDatabaseHas('sd_contract_types', ['id' => $contractType->id, 'name' => $contractType->name]);
  }

  /** @group getContractTypeList */
  public function test_getContractTypeList_withLimit_returnsNumberOfContractTypesDataBasedOnLimit()
  {
    $this->getloggedInUserForWeb('admin');
    factory(ContractType::class, 3)->create();
    $limit = 1;
    $response = $this->call('GET', url('service-desk/api/contract-type-list'), ['limit' => $limit]);
    $contractTypes = json_decode($response->content())->data->contract_types;
    $response->status(200);
    $this->assertCount(1, $contractTypes);
    $contractType = reset($contractTypes);
    $this->assertDatabaseHas('sd_contract_types', ['id' => $contractType->id, 'name' => $contractType->name]);
  }

  /** @group getContractTypeList */
  public function test_getContractTypeList_withSortFieldAndSortOrder_returnsContractTypesDataInAscendingOrderBasedOnIdField()
  {
    $this->getloggedInUserForWeb('admin');
    factory(ContractType::class, 3)->create();
    $changesInDb = ContractType::orderBy('id', 'asc')->get()->toArray();
    $response = $this->call('GET', url('service-desk/api/contract-type-list'), ['sort-order' => 'asc', 'sort-field' => 'id']);
    $contractTypes = json_decode($response->content())->data->contract_types;
    $response->status(200);
    // 3 contract types has beed seeded so +3
    $this->assertCount(3+3, $contractTypes);
    foreach ($contractTypes as $contractType) {
      $this->assertDatabaseHas('sd_contract_types', ['id' => $contractType->id, 'name' => $contractType->name]);
    }
  }

  /** @group getContractTypeList */
  public function test_getContractTypeList_withPage_returnsContractTypeDataBasedOnSpecificPage()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(ContractType::class,3)->create();
    $sortOrder = 'asc';
    $page = 2;
    $limit = 1;
    $response = $this->call('GET', url('service-desk/api/contract-type-list'), ['sort-order' => $sortOrder, 'limit' => $limit, 'page' => $page]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->contract_types);
    $this->assertEquals($data->current_page, $page);
    $contractType = reset($data->contract_types);
    $this->assertDatabaseHas('sd_contract_types', ['id' => $contractType->id, 'name' => $contractType->name]);
  }

  /** @group getContractTypeList */
  public function test_getContractTypeList_withSearchQueryEmpty_returnsCompleteContractTypeData()
  {
    $this->getloggedInUserForWeb('admin');
    factory(ContractType::class, 3)->create();
    $response = $this->call('GET', url('service-desk/api/contract-type-list'), ['search-query' => '']);
    $contractTypes = json_decode($response->content())->data->contract_types;
    $response->status(200);
    // 3 contract types has beed seeded so +3
    $this->assertCount(3+3, $contractTypes);
    foreach ($contractTypes as $contractType) {
      $this->assertDatabaseHas('sd_contract_types', ['id' => $contractType->id, 'name' => $contractType->name]);
    }
  }

  /** @group getContractTypeList */
  public function test_getContractTypeList_withSearchQueryContractTypeName_returnsContractTypeDataBasedOnPassedContractTypeName()
  {
    $this->getloggedInUserForWeb('admin');
    factory(ContractType::class, 3)->create();
    $contractTypeInDb = factory(ContractType::class)->create();
    $response = $this->call('GET', url('service-desk/api/contract-type-list'), ['search-query' => $contractTypeInDb->name]);
    $response->status(200);
    $contractTypesInResponse = json_decode($response->content())->data->contract_types;
    $this->assertCount(1, $contractTypesInResponse);
    $contractTypeInResponse = reset($contractTypesInResponse);
    $this->assertEquals($contractTypeInDb->id, $contractTypeInResponse->id);
    $this->assertEquals($contractTypeInDb->name, $contractTypeInResponse->name);
  }

   /** @group getContractTypeList */
  public function test_getContractTypeList_withSearchQueryWrongContractTypeName_returnsContractTypeDataBasedOnPassedContractTypeName()
  {
    $this->getloggedInUserForWeb('admin');
    factory(ContractType::class, 3)->create();
    $response = $this->call('GET', url('service-desk/api/contract-type-list'), ['search-query' => 'wrong-contract-type-name']);
    $response->status(200);
    $contractTypesInResponse = json_decode($response->content())->data->contract_types;
    $this->assertCount(0, $contractTypesInResponse);
    $this->assertEmpty($contractTypesInResponse);

  }

  /** @group deleteContractType */
  public function test_deleteContractType_withWrongContractTypeId_returnsContractTypeNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('DELETE', url("service-desk/api/contract-type/wrong-contract-type-id"));
    $response->assertStatus(404);
  }

  /** @group deleteContractType */
  public function test_deleteVendor_withContractTypeId_returnContractTypeDeletedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $contractType = factory(ContractType::class)->create();
    $response = $this->call('DELETE', url("service-desk/api/contract-type/{$contractType->id}"));
    $response->assertStatus(200);
    $this->assertDatabaseMissing('sd_contract_types', ['id' => $contractType->id, 'name' => $contractType->name]);
  }


}
