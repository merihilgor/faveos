<?php

namespace App\Plugins\ServiceDesk\tests\Backend\Controllers\Release;

use Tests\AddOnTestCase;
use App\Plugins\ServiceDesk\Model\Releases\SdReleases;
use App\Plugins\ServiceDesk\Model\Changes\SdChanges;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\Plugins\ServiceDesk\Model\Common\GeneralInfo;

/**
 * Tests ProblemActionOptionsController
 * 
*/
class ReleaseActionOptionsControllerTest extends AddOnTestCase
{

  /** @group getProblemActionList */
  public function test_getReleaseActionList_returnsActionsAllowed()
  {
    $this->getLoggedInUserForWeb('admin');
    $releaseId = factory(SdReleases::class)->create()->id;
    $response = $this->call('GET', url("service-desk/api/release-action/$releaseId"));
    $actions = json_decode($response->content())->data->actions;
    $response->assertStatus(200);
    $this->assertEquals(true,$actions->release_view);
    $this->assertEquals(true,$actions->release_edit);
    $this->assertEquals(true,$actions->release_delete);
    $this->assertEquals(true,$actions->release_change_attach);
    $this->assertEquals(true,$actions->release_change_detach);
    $this->assertEquals(true,$actions->release_asset_attach);
    $this->assertEquals(true,$actions->release_asset_detach);
    $this->assertEquals(false,$actions->associated_asset);
    $this->assertEquals(false,$actions->associated_change);
    $this->assertEquals(false,$actions->check_planning);
  }

  /** @group getReleaseActionList */
  public function test_getReleaseActionList_withAssetAttached_returnsActionsAllowedWithAssociatedAssetTrue()
  {
    $this->getLoggedInUserForWeb('admin');
    $release = factory(SdReleases::class)->create();
    $assetId = factory(SdAssets::class)->create()->id;
    $release->attachAssets()->attach([$assetId => ['type' => 'sd_releases']]);
    $response = $this->call('GET', url("service-desk/api/release-action/$release->id"));
    $actions = json_decode($response->content())->data->actions;
    $response->assertStatus(200);
    $this->assertEquals(true,$actions->release_view);
    $this->assertEquals(true,$actions->release_edit);
    $this->assertEquals(true,$actions->release_delete);
    $this->assertEquals(true,$actions->release_change_attach);
    $this->assertEquals(true,$actions->release_change_detach);
    $this->assertEquals(true,$actions->release_asset_attach);
    $this->assertEquals(true,$actions->release_asset_detach);
    $this->assertEquals(true,$actions->associated_asset);
    $this->assertEquals(false,$actions->associated_change);
    $this->assertEquals(false,$actions->check_planning);
    
  }

  /** @group getReleaseActionList */
  public function test_getReleaseActionList_withChangeAttached_returnsActionsAllowedWithAssociatedChangeTrue()
  {
    $this->getLoggedInUserForWeb('admin');
    $release = factory(SdReleases::class)->create();
    $changeId = factory(SdChanges::class)->create()->id;
    $release->attachChanges()->attach($changeId);
    $response = $this->call('GET', url("service-desk/api/release-action/$release->id"));
    $actions = json_decode($response->content())->data->actions;
    $response->assertStatus(200);
    $this->assertEquals(true,$actions->release_view);
    $this->assertEquals(true,$actions->release_edit);
    $this->assertEquals(true,$actions->release_delete);
    $this->assertEquals(true,$actions->release_change_attach);
    $this->assertEquals(true,$actions->release_change_detach);
    $this->assertEquals(true,$actions->release_asset_attach);
    $this->assertEquals(true,$actions->release_asset_detach);
    $this->assertEquals(false,$actions->associated_asset);
    $this->assertEquals(true,$actions->associated_change);
    $this->assertEquals(false,$actions->check_planning);
  }

  /** @group getReleaseActionList */
  public function test_getReleaseActionList_withPlanningPopup_returnsActionsAllowedWithCheckPlanningTrue()
  {
    $this->getLoggedInUserForWeb('admin');
    $releaseId = factory(SdReleases::class)->create()->id;
    $owner = "sd_releases:{$releaseId}";
    $key = 'build-plan';
    $description = 'proceed with another update';
    GeneralInfo::create(['owner' => $owner, 'key' => $key, 'value' => $description]);
    $response = $this->call('GET', url("service-desk/api/release-action/$releaseId"));
    $actions = json_decode($response->content())->data->actions;
    $response->assertStatus(200);
    $this->assertEquals(true,$actions->release_view);
    $this->assertEquals(true,$actions->release_edit);
    $this->assertEquals(true,$actions->release_delete);
    $this->assertEquals(true,$actions->release_change_attach);
    $this->assertEquals(true,$actions->release_change_detach);
    $this->assertEquals(true,$actions->release_asset_attach);
    $this->assertEquals(true,$actions->release_asset_detach);
    $this->assertEquals(false,$actions->associated_asset);
    $this->assertEquals(false,$actions->associated_change);
    $this->assertEquals(true ,$actions->check_planning);
  } 
}