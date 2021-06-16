<?php

namespace App\Plugins\ServiceDesk\tests\Backend\Controllers\Asset;

use Tests\AddOnTestCase;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\Plugins\ServiceDesk\Model\Assets\CommonAssetRelation;
use App\Plugins\ServiceDesk\Model\Contract\SdContract;
use App\Model\helpdesk\Agent_panel\Organization;

/**
 * Tests AssetController for attach asset popup
 * 
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
*/

class AssetControllerTest extends AddOnTestCase
{
  /** @group getAssetsForAttachAssets */
  public function test_getAssetsForAttachAssets_WithAssetNotAttachedToContractBasedOnAssetType()
  {
    $this->getLoggedInUserForWeb('agent');
    $assetCollection = factory(SdAssets::class)->create(['asset_type_id' => 2]);
    $response = $this->call('GET', url('service-desk/attach-assets/contract'), ['asset_type_id' => $assetCollection->asset_type_id]);
    $this->assertionPass($assetCollection, $response->content());
  }

  /** @group getAssetsForAttachAssets */
  public function test_getAssetsForAttachAssets_WithAssetNotAttachedToContractBasedOnusedBy()
  {
    $this->getLoggedInUserForWeb('agent');
    $assetCollection = factory(SdAssets::class)->create(['used_by_id' => $this->user->id]);
    $response = $this->call('GET', url('service-desk/attach-assets/contract'), ['used_by_id' => $this->user->id]);
    $this->assertionPass($assetCollection, $response->content());
  }

  /** @group getAssetsForAttachAssets */
  public function test_getAssetsForAttachAssets_WithAssetNotAttachedToContractBasedOnManagedBy()
  {
    $this->getLoggedInUserForWeb('agent');
    $assetCollection = factory(SdAssets::class)->create(['managed_by_id' => $this->user->id]);
    $response = $this->call('GET', url('service-desk/attach-assets/contract'), ['managed_by_id' => $this->user->id]);
    $this->assertionPass($assetCollection, $response->content());
  }

  /** @group getAssetsForAttachAssets */
  public function test_getAssetsForAttachAssets_WithAssetNotAttachedToContractBasedOnOrganization()
  {
    $this->getLoggedInUserForWeb('agent');
    $organizationId = factory(Organization::class)->create()->id;
    $assetCollection = factory(SdAssets::class)->create(['organization_id' => $organizationId]);
    $response = $this->call('GET', url('service-desk/attach-assets/contract'), ['organization_id' => $organizationId]);
    $this->assertionPass($assetCollection, $response->content());
  }


  /** @group getAssetsForAttachAssets */
  public function test_getAssetsForAttachAssets_WithAssetNotAttachedToContractBasedOnAssetTypeUsedByManagedByAndOrganization()
  {
    $this->getLoggedInUserForWeb('agent');
    $organizationId = factory(Organization::class)->create()->id;
    factory(SdAssets::class)->create();
    $assetCollection = factory(SdAssets::class)->create([
      'organization_id' => $organizationId,
      'used_by_id' => $this->user->id,
      'managed_by_id' => $this->user->id,
      'asset_type_id' => 2
    ]);
    $response = $this->call('GET', url('service-desk/attach-assets/contract'), [
      'organization_id' => $organizationId,
      'used_by_id' => $this->user->id,
      'managed_by_id' => $this->user->id,
      'asset_type_id' => 2
    ]);
    $this->assertionPass($assetCollection, $response->content());
  }

  /** @group getAssetsForAttachAssets */
  public function test_getAssetsForAttachAssets_WithAssetAttachedToContract()
  {
    $this->getLoggedInUserForWeb('agent');
    $assetCollection = factory(SdAssets::class)->create(['used_by_id' => $this->user->id]);
    $contract = factory(SdContract::class)->create();
    CommonAssetRelation::create(['type_id' => $contract->id, 'asset_id' => $assetCollection->id, 'type' => 'sd_contracts']);
    $response = $this->call('GET', url('service-desk/attach-assets/contract'), ['used_by_id' => $this->user->id]);
    $assetJson = json_decode($response->content())->data;
    // one attach exist and that was already attached so no new asset and asset json is empty
    $this->assertEmpty($assetJson);
  }

  /**
   * Fuction with assertion to test getAssetForAttachAssets method
   * @param collection $assetCollection
   * @param json $dataInJson
   * @return void
   */
  private function assertionPass($assetCollection, $dataInJson)
  {
    $assetJson = json_decode($dataInJson)->data;
    $this->assertCount(1, $assetJson);
    $assetJson = reset($assetJson);
    // id is coming in <input type='checkbox' name='asset[]'  class='selectval icheckbox_flat-blue' value='" . $assetCollection->id . "'></input> format
    $this->assertEquals($assetJson->id, "<input type='checkbox' name='asset[]'  class='selectval icheckbox_flat-blue' value='" . $assetCollection->id . "'></input>");
    // if asset usedby name or asset name string length is greater than 20 characters then ... is appended after 20 characters
    // to remove ... str_replace method is used
    $this->assertStringContainsString(str_replace('...', '', $assetJson->used_by), $assetCollection->usedBy()->first()->full_name);
    $this->assertStringContainsString(str_replace('...', '', $assetJson->name), $assetCollection->name);

  }

}