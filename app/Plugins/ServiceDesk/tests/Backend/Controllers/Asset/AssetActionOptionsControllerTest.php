<?php

namespace App\Plugins\ServiceDesk\tests\Backend\Controllers\Asset;

use Tests\AddOnTestCase;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\Plugins\ServiceDesk\Model\Problem\SdProblem;
use App\Plugins\ServiceDesk\Model\Releases\SdReleases;
use App\Plugins\ServiceDesk\Model\Changes\SdChanges;
use App\Model\helpdesk\Ticket\Tickets;
use App\Plugins\ServiceDesk\Model\Contract\SdContract;

/**
 * Tests AssetActionOptionsController to handle asset view actions
 * 
 */
class AssetActionOptionsControllerTest extends AddOnTestCase
{
  /** @group getAssetActionList */
  public function test_getAssetActionList_withWrongAssetId_returnsNotFoundBladePage()
  {
    $this->getLoggedInUserForWeb('agent');
    $response = $this->call('GET', url("service-desk/api/asset-action/wrong-asset-id"));
    $response->assertStatus(404);
  }

  /** @group getAssetActionList */
  public function test_getAssetActionList_withAssetId_returnsListOfAssetActionsAllowed()
  {
    $this->getLoggedInUserForWeb('agent');
    $assetId = factory(SdAssets::class)->create()->id;
    $response = $this->call('GET', url("service-desk/api/asset-action/$assetId"));
    $actions = json_decode($response->content())->data->asset_actions;
    $response->assertStatus(200);
    // assertions when no services attached or permission given to agent
    $this->assertEquals(false, $actions->attach_problems);
    $this->assertEquals(false, $actions->attach_changes);
    $this->assertEquals(false, $actions->attach_releases);
    $this->assertEquals(true,  $actions->attach_contracts);
    $this->assertEquals(true,  $actions->attach_tickets);
    $this->assertEquals(false, $actions->edit_asset);
    $this->assertEquals(false, $actions->delete_asset);
    $this->assertEquals(false, $actions->associated_problems_tab_viewable);
    $this->assertEquals(false, $actions->associated_changes_tab_viewable);
    $this->assertEquals(false, $actions->associated_releases_tab_viewable);
    $this->assertEquals(false, $actions->associated_contracts_tab_viewable);
    $this->assertEquals(false, $actions->associated_tickets_tab_viewable);
    $this->assertEquals(false, $actions->asset_type_properties);
  }

  /** @group getAssetActionList */
  public function test_getAssetActionList_withAssetIdAndServicesAttached_returnsActionsAllowed()
  {
    $this->getLoggedInUserForWeb('agent');
    $asset = factory(SdAssets::class)->create();
    $problemId = factory(SdProblem::class)->create()->id;
    $changeId = factory(SdChanges::class)->create()->id;
    $releaseId = factory(SdReleases::class)->create()->id;
    $contractId = factory(SdContract::class)->create()->id;
    $ticketId = factory(Tickets::class)->create()->id;
    $asset->problems()->sync([$problemId => ['type' => 'sd_problem']]);
    $asset->changes()->sync([$changeId => ['type' => 'sd_changes']]);
    $asset->releases()->sync([$releaseId => ['type' => 'sd_releases']]);
    $asset->contracts()->sync([$contractId => ['type' => 'sd_contracts']]);
    $asset->tickets()->sync([$ticketId => ['type' => 'sd_assets']]);
    $response = $this->call('GET', url("service-desk/api/asset-action/$asset->id"));
    $actions = json_decode($response->content())->data->asset_actions;
    $response->assertStatus(200);
    // assertions based on current scenario
    $this->assertEquals(true, $actions->associated_problems_tab_viewable);
    $this->assertEquals(true, $actions->associated_changes_tab_viewable);
    $this->assertEquals(true, $actions->associated_releases_tab_viewable);
    $this->assertEquals(true, $actions->associated_contracts_tab_viewable);
    $this->assertEquals(true, $actions->associated_tickets_tab_viewable);
    // other assertions
    $this->assertEquals(false, $actions->attach_problems);
    $this->assertEquals(false, $actions->attach_changes);
    $this->assertEquals(false, $actions->attach_releases);
    $this->assertEquals(true,  $actions->attach_contracts);
    $this->assertEquals(false, $actions->asset_type_properties);
    $this->assertEquals(true,  $actions->attach_tickets);
    $this->assertEquals(false, $actions->edit_asset);
    $this->assertEquals(false, $actions->delete_asset);
  }

  /** @group getAssetActionList */
  public function test_getAssetActionList_withAssetIdAndRequiredPermissionsGivenToAgent_returnsListOfAssetActionsAllowed()
  {
    $this->getLoggedInUserForWeb('agent');
    // providing required available permission related to asset view to agent 
    $this->user->sdPermission()->create([
      'user_id' => $this->user->id,
      'permission' => [
        'attach_problem' => 1,
        'attach_change' => 1,
        'attach_release' => 1,
        'edit_asset' => 1,
        'delete_asset' => 1
      ]
    ]);
    $assetId = factory(SdAssets::class)->create()->id;
    $response = $this->call('GET', url("service-desk/api/asset-action/$assetId"));
    $actions = json_decode($response->content())->data->asset_actions;
    $response->assertStatus(200);
    // assertions based on current scenario
    $this->assertEquals(true, $actions->attach_problems);
    $this->assertEquals(true, $actions->attach_changes);
    $this->assertEquals(true, $actions->attach_releases);
    $this->assertEquals(true, $actions->edit_asset);
    $this->assertEquals(true, $actions->delete_asset);
    // other assertions
    $this->assertEquals(true,  $actions->attach_contracts);
    $this->assertEquals(true,  $actions->attach_tickets);
    $this->assertEquals(false, $actions->associated_problems_tab_viewable);
    $this->assertEquals(false, $actions->associated_changes_tab_viewable);
    $this->assertEquals(false, $actions->associated_releases_tab_viewable);
    $this->assertEquals(false, $actions->associated_contracts_tab_viewable);
    $this->assertEquals(false, $actions->associated_tickets_tab_viewable);
    $this->assertEquals(false, $actions->asset_type_properties);
  }

}
