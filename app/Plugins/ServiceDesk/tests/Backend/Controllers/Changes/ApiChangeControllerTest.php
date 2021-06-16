<?php

namespace App\Plugins\ServiceDesk\tests\Backend\Controllers\Changes;

use Tests\AddOnTestCase;
use App\Plugins\ServiceDesk\Model\Changes\SdChanges;
use App\Plugins\ServiceDesk\Model\Assets\CommonAssetRelation;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use DB;
use App\Plugins\ServiceDesk\Model\Releases\SdReleases;
use File;
use App\Plugins\ServiceDesk\Model\Changes\ChangeReleaseRelation;
use App\Plugins\ServiceDesk\Model\Changes\SdChangestatus;
use App\Plugins\ServiceDesk\Model\Common\GeneralInfo;
use App\Model\helpdesk\Ticket\Tickets as Ticket;

/**
 * Tests ApiChangeController
 * 
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */

class ApiChangeControllerTest extends AddOnTestCase
{
  
  /** @group createUpdateChange */
  public function test_createUpdateChange_withoutFewRequiredFields_returnsFieldRequiredException()
  {
    $this->getLoggedInUserForWeb('agent');
    $permission = 'create_change';
    $this->AddPermission($permission);
    $initialCount = SdChanges::count();
    $response = $this->call('POST', url('service-desk/api/change'), [
        'subject' => 'Upgrade Web Server',
        'department_id' => 1,
        'team_id' => 1,
        'description' => 'Apache Server'
      ]
    );
    $response->assertStatus(412);
    $this->assertDatabaseMissing('sd_changes', [
      'subject' => 'Upgrade Web Server', 
      'department_id' => 1, 
      'description' => 'Apache Server'
    ]);
    $this->assertEquals($initialCount, SdChanges::count());
  }

   /** @group createUpdateChange */
  public function test_createUpdateChange_withRequiredFields_returnsPermissionDeniedError()
  {
    $this->getLoggedInUserForWeb('agent');
    $statusId = 1;
    $priorityId = 1;
    $changeTypeId = 1;
    $response = $this->call('POST', url('service-desk/api/change'), [
        'subject' => 'Upgrade Web Server',
        'requester_id' => $this->user->id,
        'status_id' => $statusId,
        'priority_id' => $priorityId,
        'change_type_id' => $changeTypeId,
        'description' => 'Apache Web Server'
      ]
    );
    // permission denied error
    $response->assertStatus(400);
  }

  /** @group createUpdateChange */
  public function test_createUpdateChange_withRequiredFieldsAndWithPermission_returnsChangeSavedSuccessfully()
  {
    $this->getLoggedInUserForWeb('agent');
    $permission = 'create_change';
    $this->AddPermission($permission);
    $initialCount = SdChanges::count();
    $statusId = 1;
    $priorityId = 1;
    $changeTypeId = 1;
    $response = $this->call('POST', url('service-desk/api/change'), [
        'subject' => 'Upgrade Web Server',
        'requester_id' => $this->user->id,
        'status_id' => $statusId,
        'priority_id' => $priorityId,
        'change_type_id' => $changeTypeId,
        'description' => 'Apache Web Server'
      ]
    );
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_changes', [
      'subject' => 'Upgrade Web Server', 
      'requester_id' => $this->user->id, 
      'status_id' => $statusId, 
      'priority_id' => $priorityId, 
      'change_type_id' => $changeTypeId, 
      'description' => 'Apache Web Server'
    ]);
    $this->assertEquals($initialCount+1, SdChanges::count());
  }

  /** @group createUpdateChange */
  public function test_createUpdateChange_withRequiredFieldsAndWithAsset_returnsChangeSavedSuccessfully()
  {
    $this->getLoggedInUserForWeb('agent');
    $permission = 'create_change';
    $this->AddPermission($permission);
    $assetId =  factory(SdAssets::class)->create()->id;
    $initialCount = SdChanges::count();
    $statusId = 1;
    $priorityId = 1;
    $changeTypeId = 1;
    $response = $this->call('POST', url('service-desk/api/change'), [
        'subject' => 'Upgrade Web Server',
        'requester_id' => $this->user->id,
        'status_id' => $statusId,
        'priority_id' => $priorityId,
        'change_type_id' => $changeTypeId,
        'description' => 'Apache Web Server',
        'asset_ids' => [$assetId]
      ]
    );
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_changes', [
      'subject' => 'Upgrade Web Server', 
      'requester_id' => $this->user->id, 
      'status_id' => $statusId, 
      'priority_id' => $priorityId, 
      'change_type_id' => $changeTypeId, 
      'description' => 'Apache Web Server'
    ]);

    $changeId = SdChanges::orderBy('id', 'desc')->first()->id;

    $this->assertDatabaseHas('sd_common_asset_relation', ['asset_id' => $assetId, 'type_id' => $changeId, 'type' => 'sd_changes']);
    $this->assertEquals($initialCount+1, SdChanges::count());
  }

  /** @group createUpdateChange */
  public function test_createUpdateChange_withAllChangeFieldsExceptAsset_returnsChangeSavedSuccessfully()
  {
    $this->getLoggedInUserForWeb('agent');
    $permission = 'create_change';
    $this->AddPermission($permission);
    $initialCount = SdChanges::count();
    $statusId = 1;
    $priorityId = 1;
    $changeTypeId = 1;
    $locationId = 1;
    $teamId = 1;
    $departmentId = 1;
    $response = $this->call('POST', url('service-desk/api/change'), [
        'subject' => 'Upgrade Web Server',
        'requester_id' => $this->user->id,
        'status_id' => $statusId,
        'priority_id' => $priorityId,
        'change_type_id' => $changeTypeId,
        'description' => 'Apache Web Server',
        'location_id' => $locationId,
        'team_id' => $teamId,
        'department_id' => $departmentId
      ]
    );
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_changes', [
      'subject' => 'Upgrade Web Server', 
      'requester_id' => $this->user->id, 
      'status_id' => $statusId, 
      'priority_id' => $priorityId, 
      'change_type_id' => $changeTypeId, 
      'description' => 'Apache Web Server', 
      'location_id' => $locationId, 
      'team_id' => $teamId, 
      'department_id' => $departmentId
    ]);
    $this->assertEquals($initialCount+1, SdChanges::count());
  }

  // /** @group createUpdateChange */
  // public function test_createUpdateChange_withAddAttachment_returnsReleaseSavedSuccessfully()
  // {
  //   $this->getLoggedInUserForWeb('agent');
  //   $permission = 'create_change';
  //   $this->AddPermission($permission);
  //   $change = factory(SdChanges::class)->create(['subject' => 'Downgrade Web Server']);
  //   $initialCount = SdChanges::count();
  //   $statusId = 1;
  //   $priorityId = 1;
  //   $changeTypeId = 1;
  //   $fileName = 'document.pdf';
  //   Storage::fake($fileName);
  //   $response = $this->call('POST', url('service-desk/api/change'), [
  //       'id' => $change->id,
  //       'subject' => 'Upgrade Web Server',
  //       'requester_id' => $this->user->id,
  //       'status_id' => $statusId,
  //       'priority_id' => $priorityId,
  //       'change_type_id' => $changeTypeId,
  //       'description' => 'Apache Web Server',
  //       'attachment' => UploadedFile::fake()->create($fileName, 20)
  //     ]
  //   );
  //   $response->assertStatus(200);
  //   $this->assertDatabaseHas('sd_changes', [
  //     'subject' => 'Upgrade Web Server', 
  //     'requester_id' => $this->user->id, 
  //     'status_id' => $statusId, 
  //     'priority_id' => $priorityId, 
  //     'change_type_id' => $changeTypeId, 
  //     'description' => 'Apache Web Server'
  //   ]);
  //   $this->assertEquals($initialCount, SdChanges::count());
  //   // assert attachement by file name
  //   $this->assertStringContainsString($fileName, DB::table('sd_attachments')->where('owner',"sd_changes:{$change->id}")->first()->value);
  // }

  /** @group createUpdateChange */
  public function test_createUpdateChange_withUpdateSubject_returnsChangeSavedSuccessfully()
  {
    $this->getLoggedInUserForWeb('agent');
    $permission = 'create_change';
    $this->AddPermission($permission);
    $change = factory(SdChanges::class)->create(['subject' => 'Downgrade Web Server']);
    $initialCount = SdChanges::count();
    $response = $this->call('POST', url('service-desk/api/change'), [
        'id' => $change->id,
        'subject' => 'Upgrade Web Server',
        'requester_id' => $change->requester_id,
        'status_id' => $change->status_id,
        'priority_id' => $change->priority_id,
        'change_type_id' => $change->change_type_id,
        'description' => $change->description
      ]
    );
  
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_changes', [
      'id' => $change->id, 
      'subject' => 'Upgrade Web Server']);
    $this->assertEquals($initialCount, SdChanges::count());
  }

  /** @group getChange */
  public function test_getChange_withWrongChangeId_returnsChangeNotFound()
  {
    $this->getLoggedInUserForWeb('agent');
    $permission = 'edit_change';
    $this->AddPermission($permission);
    $response = $this->call('GET', url("service-desk/api/change/wrong-change-id"));
    $response->assertStatus(400);
  }

  /** @group getChange */
  public function test_getChange_withChangeId_returnsChangeDataBasedOnChangeId()
  {
    $this->getLoggedInUserForWeb('agent');
    $permission = 'edit_change';
    $this->AddPermission($permission);
    $teamId = 1;
    $departmentId = 1;
    $changeInDb = factory(SdChanges::class)->create(['team_id' => $teamId, 'department_id' => $departmentId]);
    $response = $this->call('GET', url("service-desk/api/change/$changeInDb->id"));
    $response->assertStatus(200);
    $changeInResponse = json_decode($response->content())->data->change;
    $this->assertDatabaseHas('sd_changes', [
      'id' => $changeInDb->id,
      'subject' => $changeInResponse->subject, 
      'description' => $changeInResponse->description, 
      'requester_id' => $changeInResponse->requester->id, 
      'status_id' => $changeInDb->status_id, 
      'team_id' => $teamId, 
      'department_id' => $departmentId
    ]);
  }

  /** @group getChange */
  public function test_getChange_withChangeIdAndAssetsAttached_returnsChangeDataBasedOnChangeId()
  {
    $this->getLoggedInUserForWeb('admin');
    $teamId = 1;
    $departmentId = 1;
    $changeInDb = factory(SdChanges::class)->create(['team_id' => $teamId, 'department_id' => $departmentId]);
    $assetId = factory(SdAssets::class)->create()->id;
    CommonAssetRelation::create(['asset_id' => $assetId, 'type_id' => $changeInDb->id, 'type' => 'sd_changes']);
    $response = $this->call('GET', url("service-desk/api/change/$changeInDb->id"));
    $response->assertStatus(200);
    $changeInResponse = json_decode($response->content())->data->change;
    $this->assertDatabaseHas('sd_changes', [
      'id' => $changeInDb->id,
      'subject' => $changeInResponse->subject, 
      'description' => $changeInResponse->description, 
      'requester_id' => $changeInResponse->requester->id, 
      'status_id' => $changeInDb->status_id, 
      'team_id' => $teamId, 
      'department_id' => $departmentId
    ]);
  }

  /** @group deleteChange */
  public function test_deleteChange_withwrongChangeId_returnChangeNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('DELETE', url("service-desk/api/change/wrong-change-id"));
    $response->assertStatus(404);
  }

  /** @group deleteChange */
  public function test_deleteChange_withChangeId_returnChangeDeletedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $changeId = factory(SdChanges::class)->create()->id;
    $response = $this->call('DELETE', url("service-desk/api/change/$changeId"));
    $response->assertStatus(200);
    $this->assertDatabaseMissing('sd_changes', ['id' => $changeId]);
  }

  /** @group deleteChange */
  public function test_deleteChange_withChangeIdAndAttachedAsset_returnChangeDeletedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $change = factory(SdChanges::class)->create();
    $changeId = $change->id;
    $assetId = factory(SdAssets::class)->create()->id;
    $change->attachAssets()->sync([$assetId => ['type' => 'sd_changes']]);
    $this->assertDatabaseHas('sd_common_asset_relation', ['asset_id' => $assetId, 'type_id' => $changeId, 'type' => 'sd_changes']);
    $response = $this->call('DELETE', url("service-desk/api/change/$changeId"));
    $response->assertStatus(200);
    $this->assertDatabaseMissing('sd_changes', ['id' => $changeId]);
    $this->assertDatabaseMissing('sd_common_asset_relation', ['asset_id' => $assetId, 'type_id' => $changeId, 'type' => 'sd_changes']);
  }

  /** @group deleteChange */
  public function test_deleteChange_withChangeIdAndAttachedRelease_returnChangeDeletedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $change = factory(SdChanges::class)->create();
    $changeId = $change->id;
    $releaseId = factory(SdReleases::class)->create()->id;
    $change->attachReleases()->sync($releaseId);
    $this->assertDatabaseHas('sd_change_release', ['release_id' => $releaseId, 'change_id' => $changeId]);
    $response = $this->call('DELETE', url("service-desk/api/change/$changeId"));
    $response->assertStatus(200);
    $this->assertDatabaseMissing('sd_changes', ['id' => $changeId]);
    $this->assertDatabaseMissing('sd_change_release', ['release_id' => $releaseId, 'change_id' => $changeId]);
  }

  // in Ubuntu this test case is passing but through jenkins it's failing, throwing error directory not found
  // /** @group deleteChange */
  // public function test_deleteChange_withChangeIdAndAttachment_returnChangeDeletedSuccessfully()
  // {
  //   $this->getLoggedInUserForWeb('admin');
  //   $fileName = 'document.pdf';
  //   Storage::fake($fileName);
  //   $changeId = factory(SdChanges::class)->create()->id;
  //   $this->call('POST', url('service-desk/api/change'), [
  //       'id' => $changeId,
  //       'subject' => 'Upgrade Web Server',
  //       'requester_id' => $this->user->id,
  //       'status_id' => 1,
  //       'priority_id' => 1,
  //       'change_type_id' => 1,
  //       'description' => 'Apache Web Server',
  //       'attachment' => UploadedFile::fake()->create($fileName, 20)
  //     ]
  //   );
  //   $fileNameWithRandomNumber = DB::table('sd_attachments')->where('owner',"sd_changes:{$changeId}")->first()->value;
  //   $filePath = public_path() . '/uploads/service-desk/attachments/' . $fileNameWithRandomNumber;
  //   File::put($filePath, 'testing attachement');
  //   $this->assertDatabaseHas('sd_attachments', ['owner' => "sd_changes:$changeId"]);
  //   $response = $this->call('DELETE', url("service-desk/api/change/$changeId"));
  //   $response->assertStatus(200);
  //   $this->assertDatabaseMissing('sd_changes', ['id' => $changeId]);
  //   // checking attachment entry exists or not in sd_attachments table
  //   $this->assertDatabaseMissing('sd_attachments', ['owner' => "sd_changes:{$changeId}"]);
  // }

  /**
   * method to add permission
   * @param string $permission
   * @return null
   */
  private function AddPermission($permission)
  {
    // adding permission to logged in agent
    $this->user->sdPermission()->create(['user_id' => $this->user->id, 'permission' => [$permission => 1]]);
  }

  /** @group attachAssets */
  public function test_attachAssets_withChangeIdAndAssetId_returnsAssetAttachedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $assetId =  factory(SdAssets::class)->create()->id;
    $changeId = factory(SdChanges::class)->create()->id;
    $initialCount = SdChanges::count();
    $response = $this->call('POST', url("service-desk/api/change/attach-assets/$changeId"), ['asset_ids' => [$assetId]]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_common_asset_relation', ['asset_id' => $assetId, 'type_id' => $changeId, 'type' => 'sd_changes']);
    $this->assertEquals($initialCount, SdChanges::count());
  }

  /** @group attachAssets */
  public function test_attachAssets_withwrongChangeIdAndAssetId_returnChangeNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $assetId =  factory(SdAssets::class)->create()->id;
    $response = $this->call('POST', url("service-desk/api/change/attach-assets/wrong-changeId"), ['asset_ids' => [$assetId]]);
    $response->assertStatus(400);
  }

  /** @group detachAsset */
  public function test_detachAsset_withwrongChangeIdAndAssetId_returnChangeNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $assetId =  factory(SdAssets::class)->create()->id;
    $response = $this->call('DELETE', url("service-desk/api/change/detach-asset/wrong-changeId/$assetId"));
    $response->assertStatus(400);
  }

  /** @group detachAsset */
  public function test_detachAsset_withChangeIdAndWrongAssetId_returnsAssetNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $changeId = factory(SdChanges::class)->create()->id;
    $response = $this->call('DELETE', url("service-desk/api/change/detach-asset/$changeId/wrong-asset-id"));
    $response->assertStatus(400);
  }

  /** @group detachAsset */
  public function test_detachAsset_withChangeIdAndAssetId_returnsAssetDetachedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $assetId =  factory(SdAssets::class)->create()->id;
    $changeId = factory(SdChanges::class)->create()->id;
    CommonAssetRelation::create(['asset_id' => $assetId, 'type_id' => $changeId, 'type' => 'sd_changes']);
    $initialCount = CommonAssetRelation::count();
    $response = $this->call('DELETE', url("service-desk/api/change/detach-asset/$changeId/$assetId"));
    $response->assertStatus(200);
    $this->assertDatabaseMissing('sd_common_asset_relation', ['asset_id' => $assetId, 'type_id' => $changeId, 'type' => 'sd_changes']);
    $this->assertEquals($initialCount-1, CommonAssetRelation::count());
  }

  /** @group detachRelease */
  public function test_detachRelease_withwrongChangeId_returnChangeNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('DELETE', url("service-desk/api/change/detach-release/wrong-change-id"));
    $response->assertStatus(400);
  }

   /** @group detachRelease */
  public function test_detachRelease_withChangeId_returnsReleaseDetachedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $releaseId =  factory(SdReleases::class)->create()->id;
    $change = factory(SdChanges::class)->create();
    $changeId = $change->id;
    $change->attachReleases()->sync($releaseId);
    $initialCount = ChangeReleaseRelation::count();
    $response = $this->call('DELETE', url("service-desk/api/change/detach-release/$changeId"));
    $response->assertStatus(200);
    $this->assertDatabaseMissing('sd_change_release', ['release_id' => $releaseId, 'change_id' => $changeId]);
    $this->assertEquals($initialCount-1, ChangeReleaseRelation::count());
  }

  /** @group attachRelease */
  public function test_attachRelease_withReleaseIdAndWrongChangeId_returnChangeNotFound()
  {
    $this->getLoggedInUserForWeb('agent');
    $releaseId =  factory(SdReleases::class)->create()->id;
    $response = $this->call('POST', url("service-desk/api/change/attach-release/wrong-change-id/$releaseId"));
    $response->assertStatus(400);
  }

  /** @group attachRelease */
  public function test_attachRelease_withwrongChangeIdAndReleaseId_returnChangeNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('POST', url("service-desk/api/change/attach-release/wrong-change-id/wrong-release-id"));
    $response->assertStatus(400);
  }

  /** @group atatchRelease */
  public function test_attachRelease_withChangeIdAndWrongReleaseId_returnsReleaseNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $changeId = factory(SdChanges::class)->create()->id;
    $response = $this->call('DELETE', url("service-desk/api/change/detach-asset/$changeId/wrong-release-id"));
    $response->assertStatus(400);
  }

  /** @group attachRelease */
  public function test_attachRelease_withChangeIdAndReleaseId_returnsReleaseAttachedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $releaseId =  factory(SdReleases::class)->create()->id;
    $changeId = factory(SdChanges::class)->create()->id;
    $initialCount = ChangeReleaseRelation::count();
    $response = $this->call('POST', url("service-desk/api/change/attach-release/$changeId/$releaseId"));
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_change_release', ['release_id' => $releaseId, 'change_id' => $changeId]);
    $this->assertEquals($initialCount+1, ChangeReleaseRelation::count());
  }

  /** @group updateChangeStatusToClose */
  public function test_updateChangeStatusToClose_withwrongChangeId_returnChangeNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('POST', url("service-desk/api/change-close/wrong-change-id"));
    $response->assertStatus(400);
  }

  /** @group updateChangeStatusToClose */
  public function test_updateChangeStatusToClose_withChangeId_returnChangeSavedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $changeId = factory(SdChanges::class)->create()->id;
    $closedStatusId = SdChangestatus::where('name', 'Closed')->first()->id;
    $this->assertDatabaseMissing('sd_changes', ['id' => $changeId, 'status_id' => $closedStatusId]);
    $response = $this->call('POST', url("service-desk/api/change-close/$changeId"));
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_changes', ['id' => $changeId, 'status_id' => $closedStatusId]);
  }

  /** @group planningPopups */
  public function test_planningPopups_WithChangeId_returnsPlanningPopupDataBasedOnChangeId()
  {
    $this->getLoggedInUserForWeb('admin');
    $changeId = factory(SdChanges::class)->create()->id;
    $owner = "sd_changes:{$changeId}";
    $key = 'rollout-plan';
    $description = 'proceed with another update';
    GeneralInfo::create(['owner' => $owner, 'key' => $key, 'value' => $description]);
    $response = $this->call('GET', url("service-desk/api/change-planning/$changeId"));
    $response->assertStatus(200);
    $planningPopups = reset(json_decode($response->content())->data->planning_popups);
    $this->assertDatabaseHas('sd_gerneral', [
      'owner' => $owner,
      'key' => $key,
      'value' => $description
    ]);
  }
  
  /** @group attachChangeToTicket */
  public function test_attachChangeToTicket_withoutRequiredFields_returnsChangeIdTicketIdTypeFieldIsRequired()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('POST', url("service-desk/api/attach-change/ticket"));
    $response->assertStatus(412);
  }

  /** @group attachChangeToTicket */
  public function test_attachChangeToTicket_withRequiredFields_returnsChangeAttachedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $change = factory(SdChanges::class)->create();
    $ticketId = factory(Ticket::class)->create()->id;
    $type = 'initiated';
    $response = $this->call('POST', url('service-desk/api/attach-change/ticket'), ['change_id' => $change->id, 'ticket_id' => $ticketId, 'type' => $type]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_change_ticket', ['change_id' => $change->id, 'ticket_id' => $ticketId, 'type' => $type]);
  }

  /** @group detachChangeFromTicket */
  public function test_detachChangeFromTicket_withoutRequiredFields_returnsChangeIdTicketIdTypeFieldIsRequired()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('DELETE', url("service-desk/api/detach-change/ticket"));
    $response->assertStatus(412);
  }

  /** @group detachChangeFromTicket */
  public function test_detachChangeFromTicket_withRequiredFields_returnsChangeDetachedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $change = factory(SdChanges::class)->create();
    $ticketId = factory(Ticket::class)->create()->id;
    $type = 'initiated';
    $change->attachTickets()->sync([$ticketId => ['type' => $type]]);
    $response = $this->call('DELETE', url('service-desk/api/detach-change/ticket'), ['change_id' => $change->id, 'ticket_id' => $ticketId, 'type' => $type]);
    $response->assertStatus(200);
    $this->assertDatabaseMissing('sd_change_ticket', ['change_id' => $change->id, 'ticket_id' => $ticketId, 'type' => $type]);
  }

  /** @group associatedChangesLinkedToTicket */
  public function test_associatedChangesLinkedToTicket_withoutAnyExtraParameter_returnsEmptyChangeData()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdChanges::class)->create();
    $response = $this->call('GET', url('service-desk/api/attached-change/ticket'));
    $changes = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertEmpty($changes);
  }

  /** @group associatedChangesLinkedToTicket */
  public function test_associatedChangesLinkedToTicket_withTicketId_returnsChangeDataLinkedWithTicket()
  {
    $this->getloggedInUserForWeb('agent');
    $change = factory(SdChanges::class)->create();
    $ticketId = factory(Ticket::class)->create()->id;
    $type = 'initiating';
    $change->attachTickets()->attach([$ticketId => ['type' => $type]]);
    $response = $this->call('GET', url('service-desk/api/attached-change/ticket/'), ['ticket_id' => $ticketId]);
    $changes = json_decode($response->content())->data->changes;
    $changeInResponse = reset($changes);
    $this->assertCount(1, $changes);
    $response->status(200);
    $this->assertEquals($changeInResponse->type, ucfirst($type));
    $this->assertDatabaseHas('sd_changes',['id' => $change->id, 'subject' => $changeInResponse->subject]);
  }

  /** @group associatedChangesLinkedToTicket */
  public function test_associatedChangesLinkedToTicket_withTicketIdAndSearchQueryAsSubject_returnsChangeDataLinkedWithTicketBasedOnSearchQuery()
  {
    $this->getloggedInUserForWeb('agent');
    $change = factory(SdChanges::class)->create(['subject' => 'router firmware update']);
    $ticketId = factory(Ticket::class)->create()->id;
    $type = 'initiating';
    $change->attachTickets()->attach([$ticketId => ['type' => $type]]);
    $response = $this->call('GET', url('service-desk/api/attached-change/ticket/'), ['ticket_id' => $ticketId, 'search_query' => $change->subject]);
    $changes = json_decode($response->content())->data->changes;
    $changeInResponse = reset($changes);
    $this->assertCount(1, $changes);
    $response->status(200);
    $this->assertEquals($changeInResponse->type, ucfirst($type));
    $this->assertDatabaseHas('sd_changes',['id' => $change->id, 'subject' => $changeInResponse->subject]);
  }

  /** @group associatedTicketsLinkedToChange */
  public function test_associatedTicketsLinkedToChange_withChangeId_returnsTicketDataLinkedWithChange()
  {
    $this->getloggedInUserForWeb('agent');
    $change = factory(SdChanges::class)->create();
    $ticket = factory(Ticket::class)->create();
    $type = 'initiating';
    $change->attachTickets()->attach([$ticket->id => ['type' => $type]]);
    $response = $this->call('GET', url('service-desk/api/attached-ticket/change/'), ['change_id' => $change->id]);
    $tickets = json_decode($response->content())->data->tickets;
    $ticketInResponse = reset($tickets);
    $this->assertCount(1, $tickets);
    $response->status(200);
    $this->assertEquals($ticketInResponse->type, ucfirst($type));
    $this->assertDatabaseHas('tickets',['id' => $ticket->id, 'ticket_number' => $ticketInResponse->ticket_number]);
  }

  /** @group associatedTicketsLinkedToChange */
  public function test_associatedTicketsLinkedToChange_withChangeIdAndSearchQueryAsTicketNumber_returnsTicketDataLinkedWithChangeBasedOnSearchQuery()
  {
    $this->getloggedInUserForWeb('agent');
    $change = factory(SdChanges::class)->create(['subject' => 'router firmware update']);
    $ticket = factory(Ticket::class)->create();
    $type = 'initiating';
    $change->attachTickets()->attach([$ticket->id => ['type' => $type]]);
    $response = $this->call('GET', url('service-desk/api/attached-ticket/change/'), ['change_id' => $change->id, 'search_query' => $ticket->ticket_number]);
    $tickets = json_decode($response->content())->data->tickets;
    $ticketInResponse = reset($tickets);
    $this->assertCount(1, $tickets);
    $response->status(200);
    $this->assertEquals($ticketInResponse->type, ucfirst($type));
    $this->assertDatabaseHas('tickets',['id' => $ticket->id, 'ticket_number' => $ticketInResponse->ticket_number]);
  }

}
