<?php

namespace App\Plugins\ServiceDesk\tests\Backend\Controllers\AssetType;

use Tests\AddOnTestCase;
use App\Plugins\ServiceDesk\Model\Assets\SdAssettypes;
use App\Plugins\ServiceDesk\Model\Common\SdDefault;

/**
 * Tests ApiAssetTypeController
 * 
*/
class ApiAssetTypeControllerTest extends AddOnTestCase
{
  /** @group createUpdateAssetType */
  public function test_createUpdateAssetType_withFieldValidationAssetTypeNameMissing_returnsNameFieldIsRequired()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('POST', url('service-desk/api/asset-type'));
    $response->assertStatus(412);
  }

  /** @group createUpdateAssetType */
  public function test_createUpdateAssetType_withAssetTypeNameParentIdAndIsDefaultParameters_returnsAssetTypeSavedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $assetType = ['name' => 'Quarterly', 'parent_id' => 2, 'is_default' => true];
    $response = $this->call('POST', url('service-desk/api/asset-type'), $assetType);
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_asset_types', ['name' => $assetType['name'], 'parent_id' => $assetType['parent_id']]);
    $recentlyCreatedAssetTypeId = SdAssettypes::whereName($assetType['name'])->first()->id;
    $this->assertDatabaseHas('sd_default', ['id' => 1, 'asset_type_id' => $recentlyCreatedAssetTypeId]);
  }

  /** @group createUpdateAssetType */
  public function test_createUpdateAssetType_withUpdatedAssetTypeName_returnsAssetTypeSavedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $assetType = factory(SdAssettypes::class)->create();
    $updatedAssetTypeName = 'Quaterly';
    $response = $this->call('POST', url('service-desk/api/asset-type'), [
      'id' => $assetType->id,
      'name' => $updatedAssetTypeName
    ]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_asset_types', ['id' => $assetType->id, 'name' => $updatedAssetTypeName]);
  }

  /** @group createUpdateAssetType */
  public function test_createUpdateAssetType_withSameParentIdAndAssetTypeId_returnsCannotMakeSelfParentMessageException()
  {
    $this->getLoggedInUserForWeb('admin');
    $assetType = factory(SdAssettypes::class)->create();
    $response = $this->call('POST', url('service-desk/api/asset-type'), [
      'id' => $assetType->id,
      'name' => $assetType->name,
      'parent_id' => $assetType->id
    ]);
    $response->assertStatus(412);
    // asset type parent id not set to asset type id
    $this->assertNotEquals($assetType->id, $assetType->parent_id);
  }

  /** @group getAssetType */
  public function test_getAssetType_withWrongAssetTypeId_returnsNotFoundBladePage()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url("service-desk/api/asset-type/wrong-asset-type-id"));
    $response->assertStatus(404);
  }

  /** @group getAssetType */
  public function test_getAssetType_withAssetTypeId_returnsAssetTypeData()
  {
    $this->getLoggedInUserForWeb('admin');
    $assetTypeArray = ['name' => 'Quarterly', 'parent_id' => 2];
    $assetTypeId = factory(SdAssettypes::class)->create($assetTypeArray)->id;
    $response = $this->call('GET', url("service-desk/api/asset-type/$assetTypeId"));
    $assetType = json_decode($response->content())->data->asset_type;
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_asset_types', ['id' => $assetTypeId, 'name' => $assetType->name, 'parent_id' => $assetType->parent->id]);
    $this->assertDatabaseHas('sd_asset_types', ['id' => $assetTypeArray['parent_id'], 'name' => $assetType->parent->name]);
  }

  /** @group getAssetTypeList */
  public function test_getAssetTypeList_withoutAnyExtraParameter_returnsCompleteAssetTypesData()
  {
    $this->getloggedInUserForWeb('admin');
    factory(SdAssettypes::class, 3)->create();
    $response = $this->call('GET', url('service-desk/api/asset-type-list'));
    $assetTypes = json_decode($response->content())->data->asset_types;
    $response->status(200);
    // extra asset types had been seeded
    $this->assertCount(3+7, $assetTypes);
    foreach ($assetTypes as $assetType) {
      $this->assertDatabaseHas('sd_asset_types', ['id' => $assetType->id, 'name' => $assetType->name]);
    }
  }

  /** @group getAssetTypeList */
  public function test_getAssetTypeList_withLimit_returnsNumberOfAssetTypesDataBasedOnLimit()
  {
    $this->getloggedInUserForWeb('admin');
    factory(SdAssettypes::class, 3)->create();
    $limit = 1;
    $response = $this->call('GET', url('service-desk/api/asset-type-list'), ['limit' => $limit]);
    $assetTypes = json_decode($response->content())->data->asset_types;
    $response->status(200);
    $this->assertCount(1, $assetTypes);
    foreach ($assetTypes as $assetType) {
      $this->assertDatabaseHas('sd_asset_types', ['id' => $assetType->id, 'name' => $assetType->name]);
    }
  }

  /** @group getAssetTypeList */
  public function test_getAssetTypeList_withSortFieldAndSortOrder_returnsAssetTypesDataInAscendingOrderBasedOnIdField()
  {
    $this->getloggedInUserForWeb('admin');
    factory(SdAssettypes::class, 3)->create();
    $limit = 10;
    $assetTypesInDb = SdAssettypes::orderBy('id', 'asc')->take($limit)->get()->toArray();
    $response = $this->call('GET', url('service-desk/api/asset-type-list'), ['sort-order' => 'asc', 'sort-field' => 'id', 'limit' =>  $limit]);
    $assetTypes = json_decode($response->content())->data->asset_types;
    $response->status(200);
    $defaultAssetTypeId = SdDefault::first()->asset_type_id;
    // extra asset types had been seeded
    $this->assertCount(3+7, $assetTypes);
    for ($assetTypeIndex=0; $assetTypeIndex < $limit; $assetTypeIndex++) { 
      $this->assertDatabaseHas('sd_asset_types', ['id' => $assetTypesInDb[$assetTypeIndex]['id'], 'name' => $assetTypes[$assetTypeIndex]->name]);
      $isDefault = $defaultAssetTypeId == $assetTypes[$assetTypeIndex]->id ? 1 : 0;
      $this->assertEquals($isDefault, $assetTypes[$assetTypeIndex]->is_default);
    }
  }

  /** @group getAssetTypeList */
  public function test_getAssetTypeList_withPage_returnsAssetTypeDataBasedOnSpecificPage()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdAssettypes::class,3)->create();
    $sortOrder = 'asc';
    $page = 2;
    $limit = 1;
    $response = $this->call('GET', url('service-desk/api/asset-type-list'), ['sort-order' => $sortOrder, 'limit' => $limit, 'page' => $page]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->asset_types);
    $this->assertEquals($data->current_page, $page);
    foreach ($data->asset_types as $assetType) {
      $this->assertDatabaseHas('sd_asset_types', ['id' => $assetType->id, 'name' => $assetType->name]);
    }
  }

  /** @group getAssetTypeList */
  public function test_getAssetTypeList_withSearchQueryEmpty_returnsCompleteAssetTypeData()
  {
    $this->getloggedInUserForWeb('admin');
    factory(SdAssettypes::class, 3)->create();
    $response = $this->call('GET', url('service-desk/api/asset-type-list'), ['search-query' => '']);
    $assetTypes = json_decode($response->content())->data->asset_types;
    $response->status(200);
    // extra asset types had been seeded
    $this->assertCount(3+7, $assetTypes);
    foreach ($assetTypes as $assetType) {
      $this->assertDatabaseHas('sd_asset_types', ['id' => $assetType->id, 'name' => $assetType->name]);
    }
  }

  /** @group getAssetTypeList */
  public function test_getAssetTypeList_withSearchQueryAssetTypeName_returnsAssetTypeDataBasedOnPassedAssetTypeName()
  {
    $this->getloggedInUserForWeb('admin');
    factory(SdAssettypes::class, 3)->create();
    $assetTypeInDb = factory(SdAssettypes::class)->create();
    $response = $this->call('GET', url('service-desk/api/asset-type-list'), ['search-query' => $assetTypeInDb->name]);
    $response->status(200);
    $assetTypesInResponse = json_decode($response->content())->data->asset_types;
    $this->assertCount(1, $assetTypesInResponse);
    $assetTypeInResponse = reset($assetTypesInResponse);
    $this->assertEquals($assetTypeInDb->id, $assetTypeInResponse->id);
    $this->assertEquals($assetTypeInDb->name, $assetTypeInResponse->name);
  }

  /** @group getAssetTypeList */
  public function test_getAssetTypeList_withSearchQueryWrongAssetTypeName_returnsAssetTypeDataBasedOnPassedAssetTypeName()
  {
    $this->getloggedInUserForWeb('admin');
    factory(SdAssettypes::class, 3)->create();
    $response = $this->call('GET', url('service-desk/api/asset-type-list'), ['search-query' => 'wrong-asset-type-name']);
    $response->status(200);
    $assetTypesInResponse = json_decode($response->content())->data->asset_types;
    $this->assertCount(0, $assetTypesInResponse);
    $this->assertEmpty($assetTypesInResponse);

  }

  /** @group deleteAssetType */
  public function test_deletedeleteAssetType_withWrongAssetTypeId_returnsNotFoundBladePage()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('DELETE', url("service-desk/api/asset-type/wrong-asset-type-id"));
    $response->assertStatus(404);
  }

  /** @group deleteAssetType */
  public function test_deletedeleteAssetType_withAssetTypeId_returnAssetTypeDeletedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $assetType = factory(SdAssettypes::class)->create();
    $response = $this->call('DELETE', url("service-desk/api/asset-type/{$assetType->id}"));
    $response->assertStatus(200);
    $this->assertDatabaseMissing('sd_asset_types', ['id' => $assetType->id, 'name' => $assetType->name]);
  }

}
