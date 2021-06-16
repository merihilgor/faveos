<?php

namespace App\Plugins\ServiceDesk\tests\Backend\Controllers\Release;

use Tests\AddOnTestCase;
use App\Plugins\ServiceDesk\Model\Releases\SdReleases;
use App\Plugins\ServiceDesk\Model\Assets\CommonAssetRelation;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\Plugins\ServiceDesk\Model\Changes\SdChanges;
use App\Plugins\ServiceDesk\Model\Changes\ChangeReleaseRelation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Plugins\ServiceDesk\Model\Common\GeneralInfo;
use DB;

/**
 * Tests ApiReleaseController
 * 
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
*/

class ApiReleaseControllerTest extends AddOnTestCase
{
  
  /** @group createUpdateRelease */
  public function test_createUpdateRelease_withoutFewRequiredFields_returnsFieldRequiredException()
  {
    $this->getLoggedInUserForWeb('agent');
    $initialCount = SdReleases::count();
    $response = $this->call('POST', url('service-desk/api/release'), [
        'subject' => 'Upgrade Web Server',
        'description' => 'Apache Server'
      ]
    );
    $response->assertStatus(412);
    $this->assertDatabaseMissing('sd_releases', [
      'subject' => 'Upgrade Web Server', 
      'description' => 'Apache Server'
    ]);
    $this->assertEquals($initialCount, SdReleases::count());
  }

  /** @group createUpdateRelease */
  public function test_createUpdateRelease_withRequiredFields_returnsReleaseSavedSuccessfully()
  {
    $this->getLoggedInUserForWeb('agent');
    $initialCount = SdReleases::count();
    $statusId = 1;
    $priorityId = 1;
    $releaseTypeId = 1;
    $response = $this->call('POST', url('service-desk/api/release'), [
        'subject' => 'Upgrade Web Server',
        'status_id' => $statusId,
        'priority_id' => $priorityId,
        'release_type_id' => $releaseTypeId,
        'description' => 'Apache Web Server'
      ]
    );
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_releases', [
      'subject' => 'Upgrade Web Server', 
      'status_id' => $statusId, 
      'priority_id' => $priorityId, 
      'release_type_id' => $releaseTypeId, 
      'description' => 'Apache Web Server'
    ]);
    $this->assertEquals($initialCount+1, SdReleases::count());
  }

  /** @group createUpdateRelease */
  public function test_createUpdateRelease_withRequiredFieldsAndWithAsset_returnsReleaseSavedSuccessfully()
  {
    $this->getLoggedInUserForWeb('agent');
    $assetId =  factory(SdAssets::class)->create()->id;
    $initialCount = SdReleases::count();
    $statusId = 1;
    $priorityId = 1;
    $releaseTypeId = 1;
    $response = $this->call('POST', url('service-desk/api/release'), [
        'subject' => 'Upgrade Web Server',
        'status_id' => $statusId,
        'priority_id' => $priorityId,
        'release_type_id' => $releaseTypeId,
        'description' => 'Apache Web Server',
        'asset_ids' => [$assetId]
      ]
    );
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_releases', [
      'subject' => 'Upgrade Web Server', 
      'status_id' => $statusId, 
      'priority_id' => $priorityId, 
      'release_type_id' => $releaseTypeId, 
      'description' => 'Apache Web Server'
    ]);

    $releaseId = SdReleases::orderBy('id', 'desc')->first()->id;

    $this->assertDatabaseHas('sd_common_asset_relation', ['asset_id' => $assetId, 'type_id' => $releaseId, 'type' => 'sd_releases']);
    $this->assertEquals($initialCount+1, SdReleases::count());
  }

  /** @group createUpdateRelease */
  public function test_createUpdateRelease_withAllReleaseFieldsExceptAsset_returnsReleaseSavedSuccessfully()
  {
    $this->getLoggedInUserForWeb('agent');
    $initialCount = SdReleases::count();
    $statusId = 1;
    $priorityId = 1;
    $releaseTypeId = 1;
    $locationId = 1;
    $plannedStartDate = '2018-09-06 16:32:00';
    $plannedEndDate = '2018-11-06 16:32:00';
    $response = $this->call('POST', url('service-desk/api/release'), [
        'subject' => 'Upgrade Web Server',
        'status_id' => $statusId,
        'priority_id' => $priorityId,
        'release_type_id' => $releaseTypeId,
        'description' => 'Apache Web Server',
        'location_id' => $locationId,
        'planned_start_date' => $plannedStartDate,
        'planned_end_date' => $plannedEndDate
      ]
    );
    $plannedStartDateInUtc = convertDateTimeToUtc($plannedStartDate);
    $plannedEndDateInUtc = convertDateTimeToUtc($plannedEndDate);
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_releases', [
      'subject' => 'Upgrade Web Server', 
      'status_id' => $statusId, 
      'priority_id' => $priorityId, 
      'release_type_id' => $releaseTypeId, 
      'description' => 'Apache Web Server', 
      'location_id' => $locationId,
      'planned_start_date' => $plannedStartDateInUtc,
      'planned_end_date' => $plannedEndDateInUtc
    ]);
    $this->assertEquals($initialCount+1, SdReleases::count());
  }

  /** @group createUpdateRelease */
  public function test_createUpdateRelease_withUpdateSubject_returnsReleaseSavedSuccessfully()
  {
    $this->getLoggedInUserForWeb('agent');
    $release = factory(SdReleases::class)->create(['subject' => 'Downgrade Web Server']);
    $initialCount = SdReleases::count();
    $response = $this->call('POST', url('service-desk/api/release'), [
        'id' => $release->id,
        'subject' => 'Upgrade Web Server',
        'status_id' => $release->status_id,
        'priority_id' => $release->priority_id,
        'release_type_id' => $release->release_type_id,
        'description' => $release->description
      ]
    );
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_releases', [
      'id' => $release->id, 
      'subject' => 'Upgrade Web Server']);
    $this->assertEquals($initialCount, SdReleases::count());
  }

  /** @group getRelease */
  public function test_getRelease_withWrongReleaseId_returnsReleaseNotFound()
  {
    $this->getLoggedInUserForWeb('agent');
    $response = $this->call('GET', url("service-desk/api/release/wrong-release-id"));
    $response->assertStatus(404);
  }

  /** @group getRelease */
  public function test_getRelease_withReleaseId_returnsReleaseDataBasedOnReleaseId()
  {
    $this->getLoggedInUserForWeb('agent');
    $releaseInDb = factory(SdReleases::class)->create();
    $response = $this->call('GET', url("service-desk/api/release/$releaseInDb->id"));
    $response->assertStatus(200);
    $releaseInResponse = json_decode($response->content())->data->release;
    $this->assertDatabaseHas('sd_releases', [
      'id' => $releaseInDb->id,
      'subject' => $releaseInResponse->subject, 
      'description' => $releaseInResponse->description, 
      'status_id' => $releaseInDb->status_id
    ]);
  }

  /** @group attachAssets */
  public function test_attachAssets_withReleaseIdAndAssetId_returnsAssetAttachedSuccessfully()
  {
    $this->getLoggedInUserForWeb('agent');
    $assetId =  factory(SdAssets::class)->create()->id;
    $releaseId = factory(SdReleases::class)->create()->id;
    $initialCount = SdReleases::count();
    $response = $this->call('POST', url("service-desk/api/release/attach-assets/$releaseId"), ['asset_ids' => [$assetId]]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_common_asset_relation', ['asset_id' => $assetId, 'type_id' => $releaseId, 'type' => 'sd_releases']);
    $this->assertEquals($initialCount, SdReleases::count());
  }

  /** @group attachAssets */
  public function test_attachAssets_withwrongReleaseIdAndAssetId_returnReleaseNotFound()
  {
    $this->getLoggedInUserForWeb('agent');
    $assetId =  factory(SdAssets::class)->create()->id;
    $response = $this->call('POST', url("service-desk/api/release/attach-assets/wrong-releaseId"), ['asset_ids' => [$assetId]]);
    $response->assertStatus(404);
  }

  /** @group detachAsset */
  public function test_detachAsset_withwrongReleaseIdAndAssetId_returnReleaseNotFound()
  {
    $this->getLoggedInUserForWeb('agent');
    $assetId =  factory(SdAssets::class)->create()->id;
    $response = $this->call('DELETE', url("service-desk/api/release/detach-asset/wrong-releaseId/$assetId"));
    $response->assertStatus(404);
  }

  /** @group detachAsset */
  public function test_detachAsset_withReleaseIdAndWrongAssetId_returnsAssetNotFound()
  {
    $this->getLoggedInUserForWeb('agent');
    $releaseId = factory(SdReleases::class)->create()->id;
    $response = $this->call('DELETE', url("service-desk/api/release/detach-asset/$releaseId/wrong-asset-id"));
    $response->assertStatus(404);
  }

  /** @group detachAsset */
  public function test_detachAsset_withReleaseIdAndAssetId_returnsAssetDetachedSuccessfully()
  {
    $this->getLoggedInUserForWeb('agent');
    $assetId =  factory(SdAssets::class)->create()->id;
    $release = factory(SdReleases::class)->create();
    $release->attachAssets()->attach([$assetId => ['type' => 'sd_releases']]);
    $initialCount = CommonAssetRelation::count();
    $response = $this->call('DELETE', url("service-desk/api/release/detach-asset/$release->id/$assetId"));
    $response->assertStatus(200);
    $this->assertDatabaseMissing('sd_common_asset_relation', ['asset_id' => $assetId, 'type_id' => $release->id, 'type' => 'sd_releases']);
    $this->assertEquals($initialCount-1, CommonAssetRelation::count());
  }
  
  /** @group deleteRelease */
  public function test_deleteRelease_withValidReleaseId_returnsSuccessResponse()
  {
    $this->getLoggedInUserForWeb('agent');
      $releaseId = factory(SdReleases::class)->create()->id;
      $this->assertDatabaseHas('sd_releases', ['id' => $releaseId]);
      $response = $this->call('DELETE', url("service-desk/api/release/$releaseId"));
      $response->assertStatus(200);
      $this->assertDatabaseMissing('sd_releases', ['id' => $releaseId]);
  }

  /** @group deleteRelease */
  public function test_deleteRelease_WithInvalidReleaseId_returnsFailureResponse()
  {
    $this->getLoggedInUserForWeb('agent');
      $response = $this->call('DELETE', url("service-desk/api/release/wrong-release-id"));
      $response->assertStatus(404);
  }
  
  /** @group deleteRelease */
  public function test_deleteRelease_withReleaseId_returnReleaseDeletedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $release = factory(SdReleases::class)->create();
    $response = $this->call('DELETE', url("service-desk/api/release/{$release->id}"));
    $response->assertStatus(200);
    $this->assertDatabaseMissing('sd_releases', ['id' => $release->id, 'subject' => $release->subject]);
  }

  /** @group attachChanges */
  public function test_attachChanges_withReleaseIdAndChangeId_returnsChangeAttachedSuccessfully()
  {
    $this->getLoggedInUserForWeb('agent');
    $changeId =  factory(SdChanges::class)->create()->id;
    $releaseId = factory(SdReleases::class)->create()->id;
    $initialCount = ChangeReleaseRelation::count();
    $response = $this->call('POST', url("service-desk/api/release/attach-change/$releaseId"), ['change_ids' => [$changeId]]);
    $response->assertStatus(200);
    $this->assertEquals($initialCount+1, ChangeReleaseRelation::count());
    $this->assertDatabaseHas('sd_change_release', ['change_id' => $changeId, 'release_id' => $releaseId]);
  }

  /** @group attachChanges */
  public function test_attachChanges_withWrongReleaseIdAndChangeId_returnsPageNotFound()
  {
    $this->getLoggedInUserForWeb('agent');
    $changeId =  factory(SdChanges::class)->create()->id;
    $response = $this->call('POST', url("service-desk/api/release/attach-change/wrong-release-id"), ['change_ids' => [$changeId]]);
    $response->assertStatus(404);
  }

  /** @group detachChanges */
  public function test_detachChanges_withReleaseIdAndChangeId_returnsChangeDetachedSuccessfully()
  {
    $this->getLoggedInUserForWeb('agent');
    $changeId =  factory(SdChanges::class)->create()->id;
    $release = factory(SdReleases::class)->create();
    $release->attachChanges()->attach($changeId);
    $initialCount = ChangeReleaseRelation::count();
    $response = $this->call('DELETE', url("service-desk/api/release/detach-change/$release->id/$changeId"));
    $response->assertStatus(200);
    $this->assertEquals($initialCount-1, ChangeReleaseRelation::count());
    $this->assertDatabaseMissing('sd_change_release', ['change_id' => $changeId, 'release_id' => $release->id]);
  }

  /** @group detachChanges */
  public function test_detachChanges_withWrongReleaseIdAndChangeId_returnsReleaseNotFound()
  {
    $this->getLoggedInUserForWeb('agent');
    $changeId =  factory(SdChanges::class)->create()->id;
    $response = $this->call('DELETE', url("service-desk/api/release/detach-change/wrong-release-id/$changeId"));
    $response->assertStatus(404);
  }

  /** @group detachChanges */
  public function test_detachChanges_withReleaseIdAndWrongChangeId_returnsPageNotFound()
  {
    $this->getLoggedInUserForWeb('agent');
    $releaseId = factory(SdReleases::class)->create()->id;
    $response = $this->call('DELETE', url("service-desk/api/release/detach-change/$releaseId/wrong-change-id"));
    $response->assertStatus(404);
  }

  /** @group markReleaseAsCompleted */
  public function test_markReleaseAsCompleted_withReleaseId_returnsReleaseHasCompleted()
  {
    $this->getLoggedInUserForWeb('agent');
    $releaseId = factory(SdReleases::class)->create()->id;
    $response = $this->call('POST', url("service-desk/api/complete-release/$releaseId"));
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_releases',['id' => $releaseId, 'status_id' => 5]);
  }

  /** @group markReleaseAsCompleted */
  public function test_markReleaseAsCompleted_withWrongReleaseId_returnsPageNotFound()
  {
    $this->getLoggedInUserForWeb('agent');
    $response = $this->call('POST', url("service-desk/api/complete-release/wrong-release-id"));
    $response->assertStatus(404);
  }

}
