<?php

namespace App\Plugins\ServiceDesk\tests\Backend\Controllers\Report;

use Tests\AddOnTestCase;
use App\Plugins\ServiceDesk\Model\Report\SdReportFilter;
use App\Plugins\ServiceDesk\Model\Report\SdReportFilterMeta;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use Illuminate\Filesystem\Filesystem;

/**
 * Tests ReportController
 * 
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
*/

class ReportControllerTest extends AddOnTestCase
{

  /** @group createUpdateReportFilter */
  public function test_createUpdateReportFilter_withFilterFields_returnsReportSavedSuccessfully()
  {
    $this->getLoggedInUserForWeb('agent');
    $response = $this->call('POST', url('service-desk/api/reports/create'), ['name' => 'asset by organization', 'description' => 'asset by organization report', 'type' => 'asset']);
    $this->assertDatabaseHas('sd_report_filters', ['name' => 'asset by organization', 'description' => 'asset by organization report', 'type' => 'asset']);
  }

  /** @group createUpdateReportFilter */
  public function test_createUpdateReportFilter_withExtraFilterMetaFields_returnsReportSavedSuccessfully()
  {
    $this->getLoggedInUserForWeb('agent');
    $assetId = factory(SdAssets::class)->create()->id;
    $response = $this->call('POST', url('service-desk/api/reports/create'), ['name' => 'asset by organization', 'description' => 'asset by organization report', 'type' => 'asset', 'fields' => [['key' => 'asset_ids', 'value' => $assetId, 'value_meta' => 'test']]]);
    $this->assertDatabaseHas('sd_report_filters', ['name' => 'asset by organization', 'description' => 'asset by organization report', 'type' => 'asset', 'creator_id' => $this->user->id]);
    $this->assertDatabaseHas('sd_report_filter_metas', ['key' => 'asset_ids', 'value' => serialize($assetId), 'value_meta' => serialize('test')]);
  }

  /** @group getReportFilterList */
  public function test_getReportFilterList_withExtraFilterMetaFields_returnsReportsList()
  {
    $this->getLoggedInUserForWeb('agent');
    $assetId = factory(SdAssets::class)->create()->id;
    $response = $this->call('POST', url('service-desk/api/reports/create'), ['name' => 'asset by organization', 'description' => 'asset by organization report', 'type' => 'asset', 'fields' => [['key' => 'asset_ids', 'value' => $assetId, 'value_meta' => 'test']]]);
    $this->assertDatabaseHas('sd_report_filter_metas', ['key' => 'asset_ids', 'value' => serialize($assetId), 'value_meta' => serialize('test')]);
     $response = $this->call('GET', url('service-desk/api/reports'));
     $reportFilters = json_decode($response->content())->data->reports->data;
     $this->assertCount(1, $reportFilters);
     foreach ($reportFilters as $reportFilter) {
       $this->assertDatabaseHas('sd_report_filters', ['name' => $reportFilter->name, 'description' => 'asset by organization report', 'type' => $reportFilter->type]);
     }

  }

  /** @group getReportFilterList */
  public function test_getReportFilter_withFilterId_returnsReportFilter()
  {
    $this->getLoggedInUserForWeb('agent');
    $assetId = factory(SdAssets::class)->create()->id;
    $response = $this->call('POST', url('service-desk/api/reports/create'), ['name' => 'asset by organization', 'description' => 'asset by organization report', 'type' => 'asset', 'fields' => [['key' => 'asset_ids', 'value' => $assetId, 'value_meta' => 'test']]]);
    $this->assertDatabaseHas('sd_report_filter_metas', ['key' => 'asset_ids', 'value' => serialize($assetId), 'value_meta' => serialize('test')]);
    $filterId = SdReportFilter::first()->id;
    $response = $this->call('GET', url("service-desk/api/reports/$filterId"));
    $reportFilter = json_decode($response->content())->data->report_filter;
    $this->assertDatabaseHas('sd_report_filters', ['id' => $filterId, 'name' => $reportFilter->name, 'description' => $reportFilter->description]);
    $filterMeta = reset($reportFilter->filter_meta);
    $this->assertDatabaseHas('sd_report_filter_metas', ['key' => $filterMeta->key, 'value' => serialize($filterMeta->value), 'value_meta' => serialize($filterMeta->value_meta)]);
  }

  /** @group getReportFilter */
  public function test_getReportFilter_withWrongFilterId_returnsReportFilterNotFound()
  {
    $this->getLoggedInUserForWeb('agent');
    $response = $this->call('GET', url("service-desk/api/reports/wrong-filter-id"));
    $response->assertStatus(400);
  }

   /** @group deleteReportFilter */
  public function test_deleteReportFilter_withWrongFilterId_returnsReportFilterNotFound()
  {
    $this->getLoggedInUserForWeb('agent');
    $response = $this->call('DELETE', url("service-desk/api/reports/wrong-filter-id"));
    $response->assertStatus(400);
  }

  /** @group deleteReportFilterList */
  public function test_deleteReportFilter_withFilterId_returnsReportFilterDeletedSuccessfully()
  {
    $this->getLoggedInUserForWeb('agent');
    $assetId = factory(SdAssets::class)->create()->id;
    $response = $this->call('POST', url('service-desk/api/reports/create'), ['name' => 'asset by organization', 'description' => 'asset by organization report', 'type' => 'asset', 'fields' => [['key' => 'asset_ids', 'value' => $assetId, 'value_meta' => 'test']]]);
    $filterId = SdReportFilter::first()->id;
    $response = $this->call('GET', url("service-desk/api/reports/$filterId"));
    $reportFilter = json_decode($response->content())->data->report_filter;
    $response = $this->call('DELETE', url("service-desk/api/reports/$filterId"));
    $this->assertDatabaseMissing('sd_report_filters', ['id' => $filterId, 'name' => $reportFilter->name, 'description' => $reportFilter->description]);
    $filterMeta = reset($reportFilter->filter_meta);
    $this->assertDatabaseMissing('sd_report_filter_metas', ['key' => $filterMeta->key, 'value' => serialize($filterMeta->value), 'value_meta' => serialize($filterMeta->value_meta)]);
  }

  /** @group exportReportToExcel */
  public function test_exportReportToExcel_withoutFilterId_returnsFieldIsRequired()
  {
    $this->getLoggedInUserForWeb('agent');
    $response = $this->call('GET', url('service-desk/api/export/excel'));
    $response->assertStatus(412);
  }

  /** @group exportReportToExcel */
  public function test_exportReportToExcel_withWrongFilterId_returnsFilterIdIsInvalid()
  {
    $this->getLoggedInUserForWeb('agent');
    $wrongFilterId = 988908;
    $response = $this->call('GET', url('service-desk/api/export/excel'), ['filter_id' => $wrongFilterId]);
    $response->assertStatus(412);
  }

  /** @group exportReportToExcel */
  public function test_exportReportToExcel_withFilterId_returnsAssetReportBinaryFile()
  {
    $this->getLoggedInUserForWeb('agent');
    $this->makeDirectory('public/export');
    $assetId = factory(SdAssets::class)->create()->id;
    factory(SdAssets::class,2)->create();
    $response = $this->call('POST', url('service-desk/api/reports/create'), ['name' => 'asset filter', 'description' => 'asset by organization report', 'type' => 'asset', 'fields' => [['key' => 'asset_ids', 'value' => $assetId, 'value_meta' => 'test']]]);
    $filterId = SdReportFilter::where('name', 'asset filter')->first()->id;
    $response = $this->call('GET', url('service-desk/api/export/excel'), ['filter_id' => $filterId, 'file_path' => 'public/export/'."sdreport$filterId.xlsx"]);
    $this->assertTrue(file_exists(public_path().'/export/'."sdreport$filterId.xlsx"));
    $this->recursiveRmdir('public/export');
    $response->assertStatus(200);
  }

  /** @group exportReportToCsv */
  public function test_exportReportToCsv_withoutFilterId_returnsFieldIsRequired()
  {
    $this->getLoggedInUserForWeb('agent');
    $response = $this->call('GET', url('service-desk/api/export/csv'));
    $response->assertStatus(412);
  }

  /** @group exportReportToCsv */
  public function test_exportReportToCsv_withWrongFilterId_returnsFilterIdIsInvalid()
  {
    $this->getLoggedInUserForWeb('agent');
    $wrongFilterId = 988908;
    $response = $this->call('GET', url('service-desk/api/export/csv'), ['filter_id' => $wrongFilterId]);
    $response->assertStatus(412);
  }

  /** @group exportReportToCsv */
  public function test_exportReportToCsv_withRequiredAssetReportData_returnsAssetReportCSVFile()
  {
    $this->getLoggedInUserForWeb('agent');
    $this->makeDirectory('public/export');
    $assetId = factory(SdAssets::class)->create()->id;
    factory(SdAssets::class,2)->create();
    $response = $this->call('POST', url('service-desk/api/reports/create'), ['name' => 'asset filter', 'description' => 'asset by organization report', 'type' => 'asset', 'fields' => [['key' => 'asset_ids', 'value' => $assetId, 'value_meta' => 'test']]]);
    $filterId = SdReportFilter::where('name', 'asset filter')->first()->id;
    $response = $this->call('GET', url('service-desk/api/export/csv'), ['filter_id' => $filterId, 'file_path' => 'public/export/'."sdreport$filterId.csv"]);
    $this->assertTrue(file_exists(public_path().'/export/'."sdreport$filterId.csv"));
    $this->recursiveRmdir('public/export');
    $response->assertStatus(200);
  }

  /**
   * method to delete newly created directory and it files recursively
   * @param $path
   * @return 
   */
  private function recursiveRmdir($path) 
  { 
    if (is_dir($path)) { 
      $objects = array_diff( scandir($path), array('..', '.') );
        foreach ($objects as $object) { 
        $objectPath = $path."/".$object;
          if (is_dir($objectPath)) {
            $this->recursive_rmdir($objectPath);
          }
          else {
            unlink($objectPath); 
          }
        } 
        rmdir($path); 
    } 
  }


  /**
   * method to create directory based on given path
   * @param $path
   *@return $path
   */
  private function makeDirectory($path)
  {
      $fileSystem = new Filesystem();
      if (!$fileSystem->isDirectory($path)) {
        $fileSystem->makeDirectory($path, 0777, true, true);
      }

      return $path;
  }
 
}
