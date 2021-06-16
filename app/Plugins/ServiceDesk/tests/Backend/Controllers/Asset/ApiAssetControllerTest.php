<?php

namespace App\Plugins\ServiceDesk\tests\Backend\Controllers\Asset;

use Tests\AddOnTestCase;
use App\Plugins\ServiceDesk\Controllers\Library\UtilityController;
use App\Plugins\ServiceDesk\Model\Assets\SdImpactypes;
use App\Plugins\ServiceDesk\Model\Assets\SdAssettypes;
use App\Plugins\ServiceDesk\Model\Problem\SdProblem;
use App\Plugins\ServiceDesk\Model\Releases\SdReleases;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\User;
use App\Plugins\ServiceDesk\Model\Products\SdProducts;
use App\Plugins\ServiceDesk\Model\Products\SdProductstatus;
use App\Plugins\ServiceDesk\Model\Products\SdProductprocmode;
use App\Plugins\ServiceDesk\Model\Assets\CommonAssetRelation;
use App\Plugins\ServiceDesk\Model\Common\CommonTicketRelation;
use Illuminate\Http\UploadedFile;
use App\Plugins\ServiceDesk\Model\Changes\SdChanges;
use App\Model\helpdesk\Ticket\Tickets;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use App\Plugins\ServiceDesk\Model\Contract\SdContract;

/**
 * Tests ApiAssetController
 * 
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
*/

class ApiAssetControllerTest extends AddOnTestCase
{
  use DatabaseTransactions;

  /** @group createUpdateAsset */
  public function test_createUpdateAsset_withoutFewRequiredFields()
  {
    $this->getLoggedInUserForWeb('admin');
    $initialCount = SdAssets::count();
    $response = $this->call('POST', url('service-desk/api/asset'), [
        'name' => 'Book',
        'department_id' => 1,
        'description' => 'Super Brain Book'
      ]
    );
    $response->assertStatus(412);
    $this->assertDatabaseMissing('sd_assets', ['name' => 'Book', 'department_id' => 1, 'description' => 'Super Brain Book']);
    $this->assertEquals($initialCount, SdAssets::count());
  }

  /** @group createUpdateAsset */
  public function test_createUpdateAsset_withRequiredFields()
  {
    $this->getLoggedInUserForWeb('admin');
    $initialCount = SdAssets::count();
    $response = $this->call('POST', url('service-desk/api/asset'), [
        'name' => 'Book',
        'department_id' => 1,
        'managed_by_id' => 1,
        'used_by_id' => 1,
        'asset_type_id' => 1,
        'description' => 'Super Brain Book'
      ]
    );
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_assets', ['name' => 'Book', 'department_id' => 1, 'description' => 'Super Brain Book']);
    $this->assertEquals($initialCount+1, SdAssets::count());
  }

  /** @group createUpdateAsset */
  public function test_createUpdateAsset_withExistingExternalIdForNewAsset()
  {
    $this->getLoggedInUserForWeb('admin');
    $identifier = factory(SdAssets::class)->create()->identifier;
    $this->assertDatabaseHas('sd_assets', ['identifier' => $identifier]);
    $initialCount = SdAssets::count();
    $response = $this->call('POST', url('service-desk/api/asset'), [
        'name' => 'Book',
        'identifier' => $identifier,
        'department_id' => 1,
        'managed_by_id' => 1,
        'used_by_id' => 1,
        'asset_type_id' => 1,
        'description' => 'Super Brain Book'
      ]
    );
    $response->assertStatus(412);
    $this->assertDatabaseMissing('sd_assets', ['name' => 'Book', 'identifier' => $identifier, 'description' => 'Super Brain Book']);
    $this->assertEquals($initialCount, SdAssets::count());
  }

  // /** @group createUpdateAsset */
  // public function test_createUpdateAsset_withAttachment()
  // {
  //   $this->getLoggedInUserForWeb('admin');

  //   $initialCount = SdAssets::count();

  //   Storage::fake('document.pdf');

  //   $response = $this->call('POST', url('service-desk/api/asset'), [
  //       'name' => 'Book',
  //       'department_id' => 1,
  //       'managed_by' => 1,
  //       'used_by' => 1,
  //       'asset_type_id' => 1,
  //       'description' => 'Super Brain Book',
  //       'attachment' => UploadedFile::fake()->create('document.pdf', 20)
  //     ]
  //   );

  //   $response->assertStatus(200);
  //   $this->assertEquals(SdAssets::orderBy('id', 'desc')->first()->name, 'Book');
  //   $this->assertEquals($initialCount+1, SdAssets::count());
  // }

  /** @group createUpdateAsset */
  public function test_createUpdateAsset_withExistingNameForNewAsset()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create();
    $this->assertDatabaseHas('sd_assets', ['name' => $asset->name, 'organization_id' => $asset->organization_id, 'description' => $asset->description]);
    $initialCount = SdAssets::count();
    $response = $this->call('POST', url('service-desk/api/asset'), [
        'name' => $asset->name,
        'external_id' => 1,
        'department_id' => 1,
        'managed_by_id' => $this->user->id,
        'used_by_id' => $this->user->id,
        'asset_type_id' => 1,
        'organization_id' => $asset->organization_id,
        'description' => 1
      ]
    );
    $response->assertStatus(412);
    $this->assertDatabaseMissing('sd_assets', ['name' => $asset->name, 'used_by_id' => $this->user->id, 'managed_by_id' => $this->user->id]);
    $this->assertEquals($initialCount, SdAssets::count());
  }

  /** @group createUpdateAsset */
  public function test_createUpdateAsset_withUpdateName()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create();
    $this->assertDatabaseHas('sd_assets', ['name' => $asset->name, 'organization_id' => $asset->organization_id, 'description' => $asset->description]);
    $initialCount = SdAssets::count();
    $response = $this->call('POST', url('service-desk/api/asset'), [
        'id' => $asset->id,
        'name' => 'Book',
        'external_id' => 1,
        'department_id' => $asset->department_id,
        'managed_by_id' => $asset->managed_by_id,
        'used_by_id' => $asset->used_by_id,
        'asset_type_id' => $asset->asset_type_id,
        'organization_id' => $asset->organization_id,
        'description' => $asset->description
      ]
    );
    $response->assertStatus(200);
    $this->assertDatabaseMissing('sd_assets', ['name' => $asset->name, 'organization_id' => $asset->organization_id, 'description' => $asset->description]);
    $this->assertDatabaseHas('sd_assets', ['name' => 'Book', 'organization_id' => $asset->organization_id, 'description' => $asset->description, 'status_id' => $asset->status_id]);
    $this->assertEquals($initialCount, SdAssets::count());
  }


  /** @group createUpdateAsset */
  public function test_createUpdateAsset_withSameName()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create();
    $statusId = 2;
    $this->assertDatabaseHas('sd_assets', ['name' => $asset->name, 'organization_id' => $asset->organization_id, 'description' => $asset->description]);
    $initialCount = SdAssets::count();
    $response = $this->call('POST', url('service-desk/api/asset'), [
        'id' => $asset->id,
        'name' => $asset->name,
        'external_id' => 1,
        'department_id' => $asset->department_id,
        'managed_by_id' => $asset->managed_by_id,
        'used_by_id' => $asset->used_by_id,
        'asset_type_id' => $asset->asset_type_id,
        'organization_id' => $asset->organization_id,
        'description' => $asset->description,
        'status_id' => $statusId
      ]
    );
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_assets', ['name' => $asset->name, 'organization_id' => $asset->organization_id, 'description' => $asset->description, 'status_id' => $statusId]);
    $this->assertEquals($initialCount, SdAssets::count());
  }

   /** @group getAsset */
  public function test_getAsset_withWrongAssetId()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url("service-desk/api/asset/wrongAssetId"));
    $response->assertStatus(404);
  }

  /** @group getAsset */
  public function test_getAsset_withAsset()
  {
    $this->getLoggedInUserForWeb('admin');
    $assetId = factory(SdAssets::class)->create()->id;
    $this->assertDatabaseHas('sd_assets', ['id' => $assetId]);
    $response = $this->call('GET', url("service-desk/api/asset/$assetId"));
    $asset = json_decode($response->content())->data;
    $response->assertStatus(200);
    $this->assertCount(1, [$asset]);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $asset->name]);
  }

  /** @group assetRelation */
  public function test_assetRelation_withProblems()
  {
    $this->getLoggedInUserForWeb('admin');
    $assetId = factory(SdAssets::class)->create()->id;
    $problem = SdProblem::create(['status_type_id' => 3]);
    CommonAssetRelation::create(['asset_id' => $assetId, 'type_id' => $problem->id, 'type' => 'sd_problem']);
    $response = $this->call('GET', url("service-desk/api/asset-relation/$assetId"));
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_common_asset_relation', ['asset_id' => $assetId, 'type' => 'sd_problem']);
    $this->assertEquals(1, SdAssets::count());
  }

  /** @group assetRelation */
  public function test_assetRelation_withReleases()
  {
    $this->getLoggedInUserForWeb('admin');
    $assetId = factory(SdAssets::class)->create()->id;
    $release = SdReleases::create(['status_id' => 3]);
    CommonAssetRelation::create(['asset_id' => $assetId, 'type_id' => $release->id, 'type' => 'sd_releases']);
    $response = $this->call('GET', url("service-desk/api/asset-relation/$assetId"));
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_common_asset_relation', ['asset_id' => $assetId, 'type' => 'sd_releases']);
  }

  /** @group assetRelation */
  public function test_assetRelation_withChanges()
  {
    $this->getLoggedInUserForWeb('admin');
    $assetId = factory(SdAssets::class)->create()->id;
    $changes = SdChanges::create(['status_id' => 3]);
    CommonAssetRelation::create(['asset_id' => $assetId, 'type_id' => $changes->id, 'type' => 'sd_changes']);
    $response = $this->call('GET', url("service-desk/api/asset-relation/$assetId"));
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_common_asset_relation', ['asset_id' => $assetId, 'type' => 'sd_changes']);
  }

  /** @group assetRelation */
  public function test_assetRelation_withAll()
  {
    $this->getLoggedInUserForWeb('admin');
    $assetId = factory(SdAssets::class)->create()->id;
    $release = SdReleases::create(['status_id' => 3]);
    $problem = SdProblem::create(['status_type_id' => 3]);
    $problem2 = SdProblem::create(['status_type_id' => 3]);
    $changes = SdChanges::create(['status_id' => 3]);
    CommonAssetRelation::create(['asset_id' => $assetId, 'type_id' => $changes->id, 'type' => 'sd_changes']);
    CommonAssetRelation::create(['asset_id' => $assetId, 'type_id' => $problem->id, 'type' => 'sd_problem']);
    CommonAssetRelation::create(['asset_id' => $assetId, 'type_id' => $problem2->id, 'type' => 'sd_problem']);
    CommonAssetRelation::create(['asset_id' => $assetId, 'type_id' => $release->id, 'type' => 'sd_releases']);
    $response = $this->call('GET', url("service-desk/api/asset-relation/$assetId"));
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_common_asset_relation', ['asset_id' => $assetId, 'type' => 'sd_problem']);
    $this->assertDatabaseHas('sd_common_asset_relation', ['asset_id' => $assetId, 'type' => 'sd_releases']);
    $this->assertDatabaseHas('sd_common_asset_relation', ['asset_id' => $assetId, 'type' => 'sd_changes']);
  }

  /** @group editAsset */
  public function test_editAsset_wrongId()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url("service-desk/api/edit-asset/wrong-assetId"));
    $response->assertStatus(404);
  }

  /** @group editAsset */
  public function test_editAsset_withFormat()
  {
    $this->getLoggedInUserForWeb('admin');
    $assetId = factory(SdAssets::class)->create()->id;
    $response = $this->call('GET', url("service-desk/api/edit-asset/$assetId"));
    $asset = json_decode($response->content())->data;
    $response->assertStatus(200);
    $this->assertEquals($asset->id, $assetId);
    $this->assertCount(1, [$asset]);
  }

  /** @group attachAssets */
  public function test_attachAssets_withAssetIdAndTicketId()
  {
    $this->getLoggedInUserForWeb('admin');
    $ticketId = factory(Tickets::class)->create()->id;
    $assetId = factory(SdAssets::class)->create()->id;
    $response = $this->call('POST', url('service-desk/api/ticket-attach-assets'), ['ticket_id' => $ticketId, 'asset_ids' => $assetId]);
    $this->assertDatabaseHas('sd_common_ticket_relation', ['ticket_id' => $ticketId, 'type_id' => $assetId, 'type' => 'sd_assets']);
    $response->assertStatus(200);
  }

  /** @group attachAssets */
  public function test_attachAssets_withAssetIdNoTicketId()
  {
    $this->getLoggedInUserForWeb('admin');
    $assetId = factory(SdAssets::class)->create()->id;
    $response = $this->call('POST', url('service-desk/api/ticket-attach-assets'), ['asset_id' => $assetId]);
    $response->assertStatus(412);
  }

  /** @group attachAssets */
  public function test_attachAssets_withTicketIdNoAssetId()
  {
    $this->getLoggedInUserForWeb('admin');
    $ticketId = factory(Tickets::class)->create()->id;
    $response = $this->call('POST', url('service-desk/api/ticket-attach-assets'), ['ticket_id' => $ticketId]);
    $response->assertStatus(412);
  }

  /** @group attachAssets */
  public function test_attachAssets_withoutTicketIdAndAssetId()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('POST', url('service-desk/api/ticket-attach-assets'));
    $response->assertStatus(412);
  }

  /** @group assetRelation */
  public function test_detachAsset_withTicketIdAndAssetId()
  {
    $this->getLoggedInUserForWeb('admin');
    $assetId = factory(SdAssets::class)->create()->id;
    $ticketId = factory(Tickets::class)->create()->id;
    CommonTicketRelation::create(['ticket_id' => $ticketId, 'type_id' => $assetId, 'type' => 'sd_assets']);
    $initialCount = CommonTicketRelation::count();
    $response = $this->call('DELETE', url("service-desk/api/detach-asset/$ticketId/$assetId"));
    $response->assertStatus(200);
    $this->assertEquals($initialCount-1, CommonTicketRelation::count());
  }

  /** @group assetRelation */
  public function test_detachAsset_withwrongId()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('DELETE', url("service-desk/api/detach-asset/wrongTicketId/wrongAssetId"));
    $response->assertStatus(412);
  }

  /** @group deleteAsset */
  public function test_deleteAsset_withwrongAssetId()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('DELETE', url("service-desk/api/asset-delete/wrong_asset_id"));
    $response->assertStatus(404);
  }

  /** @group deleteAsset */
  public function test_deleteAsset_withAsset()
  {
    $this->getLoggedInUserForWeb('admin');
    $assetId = factory(SdAssets::class)->create()->id;
    $this->assertDatabaseHas('sd_assets', ['id' => $assetId]);
    $response = $this->call('DELETE', url("service-desk/api/asset-delete/$assetId"));
    $response->assertStatus(200);
    $this->assertDatabaseMissing('sd_assets', ['id' => $assetId]);
  }

  /** @group deleteAsset */
  public function test_deleteAsset_withAssetAndTicket()
  {
    $this->getLoggedInUserForWeb('admin');
    $assetId = factory(SdAssets::class)->create()->id;
    $ticketId = factory(Tickets::class)->create()->id;
    CommonTicketRelation::create(['ticket_id' => $ticketId, 'type_id' => $assetId, 'type' => 'sd_assets']);
    $this->assertDatabaseHas('sd_assets', ['id' => $assetId]);
    $this->assertDatabaseHas('tickets', ['id' => $ticketId]);
    $this->assertDatabaseHas('sd_common_ticket_relation', ['ticket_id' => $ticketId, 'type_id' => $assetId, 'type' => 'sd_assets']);
    $response = $this->call('DELETE', url("service-desk/api/asset-delete/$assetId"));
    $response->assertStatus(200);
    $this->assertDatabaseMissing('sd_common_ticket_relation', ['ticket_id' => $ticketId, 'type_id' => $assetId, 'type' => 'sd_assets']);
    $this->assertDatabaseMissing('sd_assets', ['id' => $assetId]);
  }

  /** @group deleteAsset */
  public function test_deleteAsset_withAssetAndTicketAndProblem()
  {
    $this->getLoggedInUserForWeb('admin');
    $assetId = factory(SdAssets::class)->create()->id;
    $ticketId = factory(Tickets::class)->create()->id;
    $problemId = factory(SdProblem::class)->create()->id;
    CommonTicketRelation::create(['ticket_id' => $ticketId, 'type_id' => $assetId, 'type' => 'sd_assets']);
    CommonAssetRelation::create(['asset_id' => $assetId, 'type_id' => $problemId, 'type' => 'sd_problem']);
    $this->assertDatabaseHas('sd_assets', ['id' => $assetId]);
    $this->assertDatabaseHas('tickets', ['id' => $ticketId]);
    $this->assertDatabaseHas('sd_problem', ['id' => $problemId]);
    $this->assertDatabaseHas('sd_common_ticket_relation', ['ticket_id' => $ticketId, 'type_id' => $assetId, 'type' => 'sd_assets']);
    CommonAssetRelation::create(['asset_id' => $assetId, 'type_id' => $problemId, 'type' => 'sd_problem']);
    $this->assertDatabaseHas('sd_common_asset_relation', ['asset_id' => $assetId, 'type_id' => $problemId, 'type' => 'sd_problem']);
    $response = $this->call('DELETE', url("service-desk/api/asset-delete/$assetId"));
    $response->assertStatus(200);
    $this->assertDatabaseMissing('sd_common_ticket_relation', ['ticket_id' => $ticketId, 'type_id' => $assetId, 'type' => 'sd_assets']);
    $this->assertDatabaseMissing('sd_common_asset_relation', ['asset_id' => $assetId, 'type_id' => $problemId, 'type' => 'sd_problem']);
    $this->assertDatabaseMissing('sd_assets', ['id' => $assetId]);
  }

  /** @group getContractBasedOnAssetApi */
  public function test_getContractBasedOnAssetApi_withWrongAssetId()
  {
    $this->getLoggedInUserForWeb('agent');
    $response = $this->call('GET', url("service-desk/api/asset-contract/wrongAssetId"));
    $response->assertStatus(400);
  }

  /** @group getContractBasedOnAssetApi */
  public function test_getContractBasedOnAssetApi_withAssetAndAttachedContract()
  {
    $this->getLoggedInUserForWeb('agent');
    $assetId = factory(SdAssets::class)->create()->id;
    $contractId = factory(SdContract::class)->create(['notify_before' => 3])->id;
    CommonAssetRelation::create(['asset_id' => $assetId, 'type_id' => $contractId, 'type' => 'sd_contracts']);
    $response = $this->call('GET', url("service-desk/api/asset-contract/$assetId"));
    $contract = json_decode($response->content())->data;
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_contracts', ['id' => $contractId, 'name' => $contract->name, 'approver_id' => $contract->approver->id, 'vendor_id' => $contract->vendor->id]);
  }

  /** @group attachServicesToAsset */
  public function test_attachServicesToAsset_withAssetIdAndProblemId_returnsAttachedSuccessfully()
  {
    $this->getLoggedInUserForWeb('agent');
    $problemId = factory(SdProblem::class)->create()->id;
    $asset = factory(SdAssets::class)->create();
    $response = $this->call('POST', url("service-desk/api/attach-asset-services/{$asset->id}"), ['type' => 'sd_problem', 'type_ids' => $problemId]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_common_asset_relation', ['asset_id' => $asset->id, 'type_id' => $problemId, 'type' => 'sd_problem']);
  }

  /** @group attachServicesToAsset */
  public function test_attachServicesToAsset_withAssetIdAndChangeId_returnsAttachedSuccessfully()
  {
    $this->getLoggedInUserForWeb('agent');
    $changeId = factory(SdChanges::class)->create()->id;
    $asset = factory(SdAssets::class)->create();
    $response = $this->call('POST', url("service-desk/api/attach-asset-services/{$asset->id}"), ['type' => 'sd_changes', 'type_ids' => $changeId]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_common_asset_relation', ['asset_id' => $asset->id, 'type_id' => $changeId, 'type' => 'sd_changes']);
  }

  /** @group attachServicesToAsset */
  public function test_attachServicesToAsset_withAssetIdAndReleaseId_returnsAttachedSuccessfully()
  {
    $this->getLoggedInUserForWeb('agent');
    $releaseId = factory(SdReleases::class)->create()->id;
    $asset = factory(SdAssets::class)->create();
    $response = $this->call('POST', url("service-desk/api/attach-asset-services/{$asset->id}"), ['type' => 'sd_releases', 'type_ids' => $releaseId]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_common_asset_relation', ['asset_id' => $asset->id, 'type_id' => $releaseId, 'type' => 'sd_releases']);
  }

  /** @group attachServicesToAsset */
  public function test_attachServicesToAsset_withAssetIdAndContractId_returnsAttachedSuccessfully()
  {
    $this->getLoggedInUserForWeb('agent');
    $contractId = factory(SdContract::class)->create()->id;
    $asset = factory(SdAssets::class)->create();
    $response = $this->call('POST', url("service-desk/api/attach-asset-services/{$asset->id}"), ['type' => 'sd_contracts', 'type_ids' => $contractId]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_common_asset_relation', ['asset_id' => $asset->id, 'type_id' => $contractId, 'type' => 'sd_contracts']);
  }

  /** @group attachServicesToAsset */
  public function test_attachServicesToAsset_withAssetIdAndTicketId_returnsAttachedSuccessfully()
  {
    $this->getLoggedInUserForWeb('agent');
    $ticketId = factory(Tickets::class)->create()->id;
    $asset = factory(SdAssets::class)->create();
    $response = $this->call('POST', url("service-desk/api/attach-asset-services/{$asset->id}"), ['type' => 'tickets', 'type_ids' => [$ticketId]]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_common_ticket_relation', ['ticket_id' => $ticketId, 'type_id' => $asset->id, 'type' => 'sd_assets']);
  }

  /** @group detachServicesFromAsset */
  public function test_detachServicesFromAsset_withAssetIdAndProblemId_returnsDetachedSuccessfully()
  {
    $this->getLoggedInUserForWeb('agent');
    $problemId = factory(SdProblem::class)->create()->id;
    $asset = factory(SdAssets::class)->create();
    $asset->problems()->sync([$problemId => ['type' => 'sd_problem']]);
    $response = $this->call('DELETE', url("service-desk/api/detach-asset-services/{$asset->id}"), ['type' => 'sd_problem', 'type_ids' => $problemId]);
    $response->assertStatus(200);
    $this->assertDatabaseMissing('sd_common_asset_relation', ['asset_id' => $asset->id, 'type_id' => $problemId, 'type' => 'sd_problem']);
  }

  /** @group detachServicesFromAsset */
  public function test_detachServicesFromAsset_withAssetIdAndChangeId_returnsDetachedSuccessfully()
  {
    $this->getLoggedInUserForWeb('agent');
    $changeId = factory(SdChanges::class)->create()->id;
    $asset = factory(SdAssets::class)->create();
    $asset->changes()->sync([$changeId => ['type' => 'sd_changes']]);
    $response = $this->call('DELETE', url("service-desk/api/detach-asset-services/{$asset->id}"), ['type' => 'sd_changes', 'type_ids' => $changeId]);
    $response->assertStatus(200);
    $this->assertDatabaseMissing('sd_common_asset_relation', ['asset_id' => $asset->id, 'type_id' => $changeId, 'type' => 'sd_changes']);
  }

  /** @group detachServicesFromAsset */
  public function test_detachServicesFromAsset_withAssetIdAndReleaseId_returnsDetachedSuccessfully()
  {
    $this->getLoggedInUserForWeb('agent');
    $releaseId = factory(SdReleases::class)->create()->id;
    $asset = factory(SdAssets::class)->create();
    $asset->releases()->sync([$releaseId => ['type' => 'sd_releases']]);
    $response = $this->call('DELETE', url("service-desk/api/detach-asset-services/{$asset->id}"), ['type' => 'sd_releases', 'type_ids' => $releaseId]);
    $response->assertStatus(200);
    $this->assertDatabaseMissing('sd_common_asset_relation', ['asset_id' => $asset->id, 'type_id' => $releaseId, 'type' => 'sd_releases']);
  }

  /** @group detachServicesFromAsset */
  public function test_detachServicesFromAsset_withAssetIdAndContractId_returnsDetachedSuccessfully()
  {
    $this->getLoggedInUserForWeb('agent');
    $contractId = factory(SdContract::class)->create()->id;
    $asset = factory(SdAssets::class)->create();
    $asset->releases()->sync([$contractId => ['type' => 'sd_contracts']]);
    $response = $this->call('DELETE', url("service-desk/api/detach-asset-services/{$asset->id}"), ['type' => 'sd_contracts', 'type_ids' => $contractId]);
    $response->assertStatus(200);
    $this->assertDatabaseMissing('sd_common_asset_relation', ['asset_id' => $asset->id, 'type_id' => $contractId, 'type' => 'sd_contracts']);
  }

  /** @group detachServicesFromAsset */
  public function test_detachServicesFromAsset_withAssetIdAndTicketId_returnsDetachedSuccessfully()
  {
    $this->getLoggedInUserForWeb('agent');
    $ticketId = factory(Tickets::class)->create()->id;
    $asset = factory(SdAssets::class)->create();
    $asset->tickets()->sync([$ticketId => ['type' => 'sd_assets']]);
    $response = $this->call('DELETE', url("service-desk/api/detach-asset-services/{$asset->id}"), ['type' => 'tickets', 'type_ids' => [$ticketId]]);
    $response->assertStatus(200);
    $this->assertDatabaseMissing('sd_common_ticket_relation', ['ticket_id' => $ticketId, 'type_id' => $asset->id, 'type' => 'sd_assets']);
  }

}