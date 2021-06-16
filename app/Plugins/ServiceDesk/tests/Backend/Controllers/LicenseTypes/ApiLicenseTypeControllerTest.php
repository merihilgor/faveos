<?php
namespace App\Plugins\ServiceDesk\tests\Backend\Controllers\LicenseType;

use Tests\AddOnTestCase;
use App\Plugins\ServiceDesk\Model\Contract\License;

/**
 * Tests ApiLicenseTypeController
 * 
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
*/
class ApiLicenseTypeControllerTest extends AddOnTestCase
{
  /** @group createUpdateLicenseType */
  public function test_createUpdateLicenseType_withFieldValidationLicenseTypeNameMissing_returnsNameFieldIsRequired()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('POST', url('service-desk/api/license-type'));
    $response->assertStatus(412);
  }

  /** @group createUpdateLicenseType */
  public function test_createUpdateLicenseType_withLicenseTypeFields_returnsLicenseTypeSavedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $licenseTypeName = 'Yearly';
    $response = $this->call('POST', url('service-desk/api/license-type'), ['name' => $licenseTypeName]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_license_types', ['name' => $licenseTypeName]);
  }

  /** @group createUpdateLicenseType */
  public function test_createUpdateLicenseType_withUpdateLicenseTypeName_returnsLicenseTypeSavedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $licenseType = factory(License::class)->create();
    $updatedLicenseTypeName = 'Quaterly';
    $response = $this->call('POST', url('service-desk/api/license-type'), [
      'id' => $licenseType->id,
      'name' => $updatedLicenseTypeName
    ]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_license_types', ['id' => $licenseType->id, 'name' => $updatedLicenseTypeName]);
  }

  /** @group getLicenseType */
  public function test_getLicenseType_withWrongLicenseTypeId_returnsLicenseTypeNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url("service-desk/api/license-type/wrong-license-type-id"));
    $response->assertStatus(400);
  }

  /** @group getLicenseType */
  public function test_getLicenseType_withLicenseTypeId_returnLicenseTypeData()
  {
    $this->getLoggedInUserForWeb('admin');
    $licenseTypeId = factory(License::class)->create()->id;
    $response = $this->call('GET', url("service-desk/api/license-type/$licenseTypeId"));
    $licenseType = json_decode($response->content())->data->license_type;
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_license_types', ['id' => $licenseTypeId, 'name' => $licenseType->name]);
  }

  /** @group getLicenseTypeList */
  public function test_getLicenseTypeList_withoutAnyExtraParameter_returnsCompleteLicenseTypesData()
  {
    $this->getloggedInUserForWeb('admin');
    factory(License::class, 3)->create();
    $response = $this->call('GET', url('service-desk/api/license-type-list'));
    $licenseTypes = json_decode($response->content())->data->license_types;
    $response->status(200);
    // 2 license types has beed seeded so +2
    $this->assertCount(3+2, $licenseTypes);
    foreach ($licenseTypes as $licenseType) {
      $this->assertDatabaseHas('sd_license_types', ['id' => $licenseType->id, 'name' => $licenseType->name]);
    }
  }

  /** @group getLicenseTypeList */
  public function test_getLicenseTypeList_withLimit_returnsNumberOfLicenseTypesDataBasedOnLimit()
  {
    $this->getloggedInUserForWeb('admin');
    factory(License::class, 3)->create();
    $limit = 1;
    $response = $this->call('GET', url('service-desk/api/license-type-list'), ['limit' => $limit]);
    $licenseTypes = json_decode($response->content())->data->license_types;
    $response->status(200);
    $this->assertCount(1, $licenseTypes);
    foreach ($licenseTypes as $licenseType) {
      $this->assertDatabaseHas('sd_license_types', ['id' => $licenseType->id, 'name' => $licenseType->name]);
    }
  }

  /** @group getLicenseTypeList */
  public function test_getLicenseTypeList_withSortFieldAndSortOrder_returnsLicenseTypesDataInAscendingOrderBasedOnIdField()
  {
    $this->getloggedInUserForWeb('admin');
    factory(License::class, 3)->create();
    $licenseTypesInDb = License::orderBy('id', 'asc')->get()->toArray();
    $response = $this->call('GET', url('service-desk/api/license-type-list'), ['sort-order' => 'asc', 'sort-field' => 'id']);
    $licenseTypes = json_decode($response->content())->data->license_types;
    $response->status(200);
    // 2 license types has beed seeded so +2
    $this->assertCount(3+2, $licenseTypes);
    for ($licenseTypeIndex=0; $licenseTypeIndex < License::count(); $licenseTypeIndex++) { 
      $this->assertDatabaseHas('sd_license_types', ['id' => $licenseTypesInDb[$licenseTypeIndex]['id'], 'name' => $licenseTypes[$licenseTypeIndex]->name]);
    }
  }

  /** @group getLicenseTypeList */
  public function test_getLicenseTypeList_withPage_returnsLicenseTypeDataBasedOnSpecificPage()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(License::class,3)->create();
    $sortOrder = 'asc';
    $page = 2;
    $limit = 1;
    $response = $this->call('GET', url('service-desk/api/license-type-list'), ['sort-order' => $sortOrder, 'limit' => $limit, 'page' => $page]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->license_types);
    $this->assertEquals($data->current_page, $page);
    foreach ($data->license_types as $licenseType) {
      $this->assertDatabaseHas('sd_license_types', ['id' => $licenseType->id, 'name' => $licenseType->name]);
    }
  }

  /** @group getLicenseTypeList */
  public function test_getLicenseTypeList_withSearchQueryEmpty_returnsCompleteLicenseTypeData()
  {
    $this->getloggedInUserForWeb('admin');
    factory(License::class, 3)->create();
    $response = $this->call('GET', url('service-desk/api/license-type-list'), ['search-query' => '']);
    $licenseTypes = json_decode($response->content())->data->license_types;
    $response->status(200);
    // 2 license types has beed seeded so +2
    $this->assertCount(3+2, $licenseTypes);
    foreach ($licenseTypes as $licenseType) {
      $this->assertDatabaseHas('sd_license_types', ['id' => $licenseType->id, 'name' => $licenseType->name]);
    }
  }

  /** @group getLicenseTypeList */
  public function test_getLicenseTypeList_withSearchQueryLicenseTypeName_returnsLicenseTypeDataBasedOnPassedLicenseTypeName()
  {
    $this->getloggedInUserForWeb('admin');
    factory(License::class, 3)->create();
    $licenseTypeInDb = factory(License::class)->create();
    $response = $this->call('GET', url('service-desk/api/license-type-list'), ['search-query' => $licenseTypeInDb->name]);
    $response->status(200);
    $licenseTypesInResponse = json_decode($response->content())->data->license_types;
    $this->assertCount(1, $licenseTypesInResponse);
    $licenseTypeInResponse = reset($licenseTypesInResponse);
    $this->assertEquals($licenseTypeInDb->id, $licenseTypeInResponse->id);
    $this->assertEquals($licenseTypeInDb->name, $licenseTypeInResponse->name);
  }

  /** @group getLicenseTypeList */
  public function test_getLicenseTypeList_withSearchQueryWrongLicenseTypeName_returnsLicenseTypeDataBasedOnPassedLicenseTypeName()
  {
    $this->getloggedInUserForWeb('admin');
    factory(License::class, 3)->create();
    $response = $this->call('GET', url('service-desk/api/license-type-list'), ['search-query' => 'wrong-license-type-name']);
    $response->status(200);
    $licenseTypesInResponse = json_decode($response->content())->data->license_types;
    $this->assertCount(0, $licenseTypesInResponse);
    $this->assertEmpty($licenseTypesInResponse);

  }

  /** @group deleteLicenseType */
  public function test_deleteLicenseType_withWrongLicenseTypeId_returnsLicenseTypeNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('DELETE', url("service-desk/api/license-type/wrong-license-type-id"));
    $response->assertStatus(400);
  }

  /** @group deleteLicenseType */
  public function test_deleteLicenseType_withLicenseTypeId_returnLicenseTypeDeletedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $licenseType = factory(License::class)->create();
    $response = $this->call('DELETE', url("service-desk/api/license-type/{$licenseType->id}"));
    $response->assertStatus(200);
    $this->assertDatabaseMissing('sd_license_types', ['id' => $licenseType->id, 'name' => $licenseType->name]);
  }


}