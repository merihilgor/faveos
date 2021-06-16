<?php
namespace App\Plugins\ServiceDesk\tests\Backend\Controllers\AssetStatus;

use Tests\AddOnTestCase;
use App\Plugins\ServiceDesk\Model\Assets\SdAssetStatus;

/**
 * Tests ApiAssetStatusController
 * 
*/
class ApiAssetStatusControllerTest extends AddOnTestCase
{
  /** @group createUpdateAssetStatus */
  public function test_createUpdateAssetStatus_withFieldValidationAssetStatusNameMissing_returnsNameFieldIsRequired()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('POST', url('service-desk/api/asset-status'));
    $response->assertStatus(412);
  }

  /** @group createUpdateAssetStatus */
  public function test_createUpdateAssetStatus_withAssetStatusFields_returnsAssetStatusSavedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $assetStatusName = 'Maintain';
    $response = $this->call('POST', url('service-desk/api/asset-status'), ['name' => $assetStatusName]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_asset_statuses', ['name' => $assetStatusName]);
  }

  /** @group createUpdateAssetStatus */
  public function test_createUpdateAssetStatus_withUpdateAssetStatusName_returnsAssetStatusSavedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $assetStatus = factory(SdAssetStatus::class)->create();
    $updatedAssetStatusName = 'Disposed';
    $response = $this->call('POST', url('service-desk/api/asset-status'), [
      'id' => $assetStatus->id,
      'name' => $updatedAssetStatusName,
      'description' => 'Assets to be disposed'
    ]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_asset_statuses', ['id' => $assetStatus->id, 'name' => $updatedAssetStatusName]);
  }

  /** @group getAssetStatus */
  public function test_getAssetStatus_withWrongAssetStatusId_returnsNotFoundBladePage()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url("service-desk/api/asset-status/wrong-asset-status-id"));
    $response->assertStatus(404);
  }

  /** @group getAssetStatus */
  public function test_getAssetStatus_withAssetStatusId_returnAssetStatusData()
  {
    $this->getLoggedInUserForWeb('admin');
    $assetStatusId = factory(SdAssetStatus::class)->create()->id;
    $response = $this->call('GET', url("service-desk/api/asset-status/$assetStatusId"));
    $assetStatus = json_decode($response->content())->data->asset_status;
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_asset_statuses', ['id' => $assetStatusId, 'name' => $assetStatus->name, 'description' => $assetStatus->description]);
  }

  /** @group getAssetStatusList */
  public function test_getAssetStatusList_withoutAnyExtraParameter_returnsCompleteAssetStatusesData()
  {
    $this->getloggedInUserForWeb('admin');
    factory(SdAssetStatus::class, 3)->create();
    $response = $this->call('GET', url('service-desk/api/asset-status-list'));
    $assetStatuses = json_decode($response->content())->data->data;
    $response->status(200);
    // 6 asset statuses has been seeded so +5
    $this->assertCount(6+3, $assetStatuses);
    foreach ($assetStatuses as $assetStatus) {
      $this->assertDatabaseHas('sd_asset_statuses', ['id' => $assetStatus->id, 'name' => $assetStatus->name]);
    }
  }

  /** @group getAssetStatusList */
  public function test_getAssetStatusList_withLimit_returnsNumberOfAssetStatusesDataBasedOnLimit()
  {
    $this->getloggedInUserForWeb('admin');
    factory(SdAssetStatus::class, 3)->create();
    $limit = 1;
    $response = $this->call('GET', url('service-desk/api/asset-status-list'), ['limit' => $limit]);
    $assetStatuses = json_decode($response->content())->data->data;
    $response->status(200);
    $this->assertCount(1, $assetStatuses);
    $assetStatus = reset($assetStatuses);
    $this->assertDatabaseHas('sd_asset_statuses', ['id' => $assetStatus->id, 'name' => $assetStatus->name]);
  }

  /** @group getAssetStatusList */
  public function test_getAssetStatusList_withSortFieldAndSortOrder_returnsAssetStatusesDataInAscendingOrderBasedOnIdField()
  {
    $this->getloggedInUserForWeb('admin');
    factory(SdAssetStatus::class, 3)->create();
    $assetStatusInDb = SdAssetStatus::orderBy('id', 'asc')->get()->toArray();
    $response = $this->call('GET', url('service-desk/api/asset-status-list'), ['sort-order' => 'asc', 'sort-field' => 'id']);
    $assetStatuses = json_decode($response->content())->data->data;
    $response->status(200);
    // 6 asset statuses has been seeded so +5
    $this->assertCount(6+3, $assetStatuses);
    foreach ($assetStatuses as $assetStatus) {
      $this->assertDatabaseHas('sd_asset_statuses', ['id' => $assetStatus->id, 'name' => $assetStatus->name]);
    }
  }

  /** @group getAssetStatusList */
  public function test_getAssetStatusList_withPage_returnsAssetStatusesDataBasedOnSpecificPage()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdAssetStatus::class,3)->create();
    $sortOrder = 'asc';
    $page = 2;
    $limit = 1;
    $response = $this->call('GET', url('service-desk/api/asset-status-list'), ['sort-order' => $sortOrder, 'limit' => $limit, 'page' => $page]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->data);
    $this->assertEquals($data->current_page, $page);
    foreach ($data->data as $assetStatus) {
      $this->assertDatabaseHas('sd_asset_statuses', ['id' => $assetStatus->id, 'name' => $assetStatus->name]);
    }
  }

  /** @group getAssetStatusList */
  public function test_getAssetStatusList_withSearchQueryEmpty_returnsCompleteAssetStatusData()
  {
    $this->getloggedInUserForWeb('admin');
    factory(SdAssetStatus::class, 3)->create();
    $response = $this->call('GET', url('service-desk/api/asset-status-list'), ['search-query' => '']);
    $assetStatuses = json_decode($response->content())->data->data;
    $response->status(200);
    // 6 asset statuses has been seeded so +5
    $this->assertCount(6+3, $assetStatuses);
    foreach ($assetStatuses as $assetStatus) {
      $this->assertDatabaseHas('sd_asset_statuses', ['id' => $assetStatus->id, 'name' => $assetStatus->name]);
    }
  }

  /** @group getAssetStatusList */
  public function test_getAssetStatusList_withSearchQueryAssetStatusName_returnsAssetStatusDataBasedOnPassedAssetStatusName()
  {
    $this->getloggedInUserForWeb('admin');
    factory(SdAssetStatus::class, 3)->create();
    $assetStatusInDb = factory(SdAssetStatus::class)->create();
    $response = $this->call('GET', url('service-desk/api/asset-status-list'), ['search-query' => $assetStatusInDb->name]);
    $response->status(200);
    $assetStatusesInResponse = json_decode($response->content())->data->data;
    $this->assertCount(1, $assetStatusesInResponse);
    $assetStatusInResponse = reset($assetStatusesInResponse);
    $this->assertEquals($assetStatusInDb->id, $assetStatusInResponse->id);
    $this->assertEquals($assetStatusInDb->name, $assetStatusInResponse->name);
  }

   /** @group getAssetStatusList */
  public function test_getAssetStatusList_withSearchQueryWrongAssetStatusName_returnsAssetStatusDataBasedOnPassedAssetStatusName()
  {
    $this->getloggedInUserForWeb('admin');
    factory(SdAssetStatus::class, 3)->create();
    $response = $this->call('GET', url('service-desk/api/asset-status-list'), ['search-query' => 'wrong-asset-status-name']);
    $response->status(200);
    $assetStatusesInResponse = json_decode($response->content())->data->data;
    $this->assertCount(0, $assetStatusesInResponse);
    $this->assertEmpty($assetStatusesInResponse);

  }

  /** @group deleteAssetStatus */
  public function test_deleteAssetStatus_withWrongAssetStatusId_returnsPageNotFoundBladePage()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('DELETE', url("service-desk/api/asset-status/wrong-asset-status-id"));
    $response->assertStatus(404);
  }

  /** @group deleteAssetStatus */
  public function test_deleteAssetStatus_withAssetStatusId_returnAssetStatusDeletedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $assetStatus = factory(SdAssetStatus::class)->create();
    $response = $this->call('DELETE', url("service-desk/api/asset-status/{$assetStatus->id}"));
    $response->assertStatus(200);
    $this->assertDatabaseMissing('sd_asset_statuses', ['id' => $assetStatus->id, 'name' => $assetStatus->name]);
  }


}
