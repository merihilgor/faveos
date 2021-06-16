<?php
namespace App\Plugins\ServiceDesk\tests\Backend\Controllers\ProcurementMode;

use Tests\AddOnTestCase;
use App\Plugins\ServiceDesk\Model\Procurment\SdProcurment;

/**
 * Tests ApiProcurementModeController
 * 
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
*/
class ApiProcurementModeControllerTest extends AddOnTestCase
{
  /** @group createUpdateProcurementMode */
  public function test_createUpdateProcurementMode_withFieldValidationProcurementModeNameMissing_returnsNameFieldIsRequired()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('POST', url('service-desk/api/procurement-mode'));
    $response->assertStatus(412);
  }

  /** @group createUpdateProcurementMode */
  public function test_createUpdateProcurementMode_withProcurementModeFields_returnsProcurementModeSavedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $procurmentModeName = 'Yearly';
    $response = $this->call('POST', url('service-desk/api/procurement-mode'), ['name' => $procurmentModeName]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_product_proc_mode', ['name' => $procurmentModeName]);
  }

  /** @group createUpdateProcurementMode */
  public function test_createUpdateProcurementMode_withUpdateProcurementModeName_returnsProcurementModeSavedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $procurmentMode = factory(SdProcurment::class)->create();
    $updatedProcurmentModeName = 'Quaterly';
    $response = $this->call('POST', url('service-desk/api/procurement-mode'), [
      'id' => $procurmentMode->id,
      'name' => $updatedProcurmentModeName
    ]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_product_proc_mode', ['id' => $procurmentMode->id, 'name' => $updatedProcurmentModeName]);
  }

  /** @group getProcurementMode */
  public function test_getProcurementMode_withWrongProcurementModeId_returnsProcurementModeNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url("service-desk/api/procurement-mode/wrong-procurement-mode-id"));
    $response->assertStatus(400);
  }

  /** @group getProcurementMode */
  public function test_getProcurementMode_withProcurementModeId_returnProcurementModeData()
  {
    $this->getLoggedInUserForWeb('admin');
    $procurementModeId = factory(SdProcurment::class)->create()->id;
    $response = $this->call('GET', url("service-desk/api/procurement-mode/$procurementModeId"));
    $procurementMode = json_decode($response->content())->data->procurement_mode;
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_product_proc_mode', ['id' => $procurementModeId, 'name' => $procurementMode->name]);
  }

  /** @group getProcurementModeList */
  public function test_getProcurementModeList_withoutAnyExtraParameter_returnsCompleteProcurementModesData()
  {
    $this->getloggedInUserForWeb('admin');
    factory(SdProcurment::class, 3)->create();
    $response = $this->call('GET', url('service-desk/api/procurement-mode-list'));
    $procurementModes = json_decode($response->content())->data->procurement_modes;
    $response->status(200);
    // 2 procurement modes has beed seeded so +2
    $this->assertCount(3+2, $procurementModes);
    foreach ($procurementModes as $procurementMode) {
      $this->assertDatabaseHas('sd_product_proc_mode', ['id' => $procurementMode->id, 'name' => $procurementMode->name]);
    }
  }

  /** @group getProcurementModeList */
  public function test_getProcurementModeList_withLimit_returnsNumberOfProcurementModesDataBasedOnLimit()
  {
    $this->getloggedInUserForWeb('admin');
    factory(SdProcurment::class, 3)->create();
    $limit = 1;
    $response = $this->call('GET', url('service-desk/api/procurement-mode-list'), ['limit' => $limit]);
    $procurementModes = json_decode($response->content())->data->procurement_modes;
    $response->status(200);
    $this->assertCount(1, $procurementModes);
    foreach ($procurementModes as $procurementMode) {
      $this->assertDatabaseHas('sd_product_proc_mode', ['id' => $procurementMode->id, 'name' => $procurementMode->name]);
    }
  }

  /** @group getProcurementModeList */
  public function test_getProcurementModeList_withSortFieldAndSortOrder_returnsProcurementModesDataInAscendingOrderBasedOnIdField()
  {
    $this->getloggedInUserForWeb('admin');
    factory(SdProcurment::class, 3)->create();
    $procurementModesInDb = SdProcurment::orderBy('id', 'asc')->get()->toArray();
    $response = $this->call('GET', url('service-desk/api/procurement-mode-list'), ['sort-order' => 'asc', 'sort-field' => 'id']);
    $procurementModes = json_decode($response->content())->data->procurement_modes;
    $response->status(200);
    // 2 procurement modes has beed seeded so +2
    $this->assertCount(3+2, $procurementModes);
    for ($procurementModeIndex=0; $procurementModeIndex < SdProcurment::count(); $procurementModeIndex++) { 
      $this->assertDatabaseHas('sd_product_proc_mode', ['id' => $procurementModesInDb[$procurementModeIndex]['id'], 'name' => $procurementModes[$procurementModeIndex]->name]);
    }
  }

  /** @group getProcurementModeList */
  public function test_getProcurementModeList_withPage_returnsProcurementModeDataBasedOnSpecificPage()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdProcurment::class,3)->create();
    $sortOrder = 'asc';
    $page = 2;
    $limit = 1;
    $response = $this->call('GET', url('service-desk/api/procurement-mode-list'), ['sort-order' => $sortOrder, 'limit' => $limit, 'page' => $page]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->procurement_modes);
    $this->assertEquals($data->current_page, $page);
    foreach ($data->procurement_modes as $procurementMode) {
      $this->assertDatabaseHas('sd_product_proc_mode', ['id' => $procurementMode->id, 'name' => $procurementMode->name]);
    }
  }

  /** @group getProcurementModeList */
  public function test_getProcurementModeList_withSearchQueryEmpty_returnsCompleteProcurementModeData()
  {
    $this->getloggedInUserForWeb('admin');
    factory(SdProcurment::class, 3)->create();
    $response = $this->call('GET', url('service-desk/api/procurement-mode-list'), ['search-query' => '']);
    $procurementModes = json_decode($response->content())->data->procurement_modes;
    $response->status(200);
    // 2 procurement modes has beed seeded so +2
    $this->assertCount(3+2, $procurementModes);
    foreach ($procurementModes as $procurementMode) {
      $this->assertDatabaseHas('sd_product_proc_mode', ['id' => $procurementMode->id, 'name' => $procurementMode->name]);
    }
  }

  /** @group getProcurementModeList */
  public function test_getProcurementModeList_withSearchQueryProcurementModeName_returnsProcurementModeDataBasedOnPassedProcurementModeName()
  {
    $this->getloggedInUserForWeb('admin');
    factory(SdProcurment::class, 3)->create();
    $procurementModeInDb = factory(SdProcurment::class)->create();
    $response = $this->call('GET', url('service-desk/api/procurement-mode-list'), ['search-query' => $procurementModeInDb->name]);
    $response->status(200);
    $procurementModesInResponse = json_decode($response->content())->data->procurement_modes;
    $this->assertCount(1, $procurementModesInResponse);
    $procurementModeInResponse = reset($procurementModesInResponse);
    $this->assertEquals($procurementModeInDb->id, $procurementModeInResponse->id);
    $this->assertEquals($procurementModeInDb->name, $procurementModeInResponse->name);
  }

  /** @group getProcurementModeList */
  public function test_getProcurementModeList_withSearchQueryWrongProcurementModeName_returnsProcurementModeDataBasedOnPassedProcurementModeName()
  {
    $this->getloggedInUserForWeb('admin');
    factory(SdProcurment::class, 3)->create();
    $response = $this->call('GET', url('service-desk/api/procurement-mode-list'), ['search-query' => 'wrong-procurement-mode-name']);
    $response->status(200);
    $procurementModesInResponse = json_decode($response->content())->data->procurement_modes;
    $this->assertCount(0, $procurementModesInResponse);
    $this->assertEmpty($procurementModesInResponse);

  }

  /** @group deleteProcurementMode */
  public function test_deleteProcurementMode_withWrongProcurementModeId_returnsProcurementModeNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('DELETE', url("service-desk/api/procurement-mode/wrong-procurement-mode-id"));
    $response->assertStatus(404);
  }

  /** @group deleteProcurementMode */
  public function test_deleteProcurementMode_withProcurementModeId_returnProcurementModeDeletedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $procurementMode = factory(SdProcurment::class)->create();
    $response = $this->call('DELETE', url("service-desk/api/procurement-mode/{$procurementMode->id}"));
    $response->assertStatus(200);
    $this->assertDatabaseMissing('sd_product_proc_mode', ['id' => $procurementMode->id, 'name' => $procurementMode->name]);
  }


}