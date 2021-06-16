<?php

namespace App\Plugins\ServiceDesk\tests\Backend\Controllers\Release;

use Tests\AddOnTestCase;
use App\Plugins\ServiceDesk\Model\Releases\SdReleases;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\Plugins\ServiceDesk\Model\Assets\CommonAssetRelation;
use App\Traits\FaveoDateParser;
use App\Plugins\ServiceDesk\Model\Changes\SdChanges;

/**
 * Tests ReleaseListController
 * 
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */

class ReleaseListControllerTest extends AddOnTestCase
{
  use FaveoDateParser;

  /* @group getReleaseList */
  public function test_getReleaseList_withLimit_returnsReleaseList()
  {
    $this->getLoggedInUserForWeb('agent');
    factory(SdReleases::class,3)->create();
    $limit = 2;
    $response = $this->call('GET', url('service-desk/api/release-list'), ['limit' => $limit]);
    $releases = json_decode($response->content())->data->releases;
    $response->status(200);
    $this->assertCount(2, $releases);
    $release = reset($releases);
    $this->assertDatabaseHas('sd_releases', ['id' => $release->id, 'subject' => $release->subject, 'planned_start_date' => $release->planned_start_date, 'planned_end_date' => $release->planned_end_date, 'release_type_id' => $release->release_type->id, 'priority_id' => $release->priority->id, 'status_id' => $release->status->id]);
  }

  /* @group getReleaseList */
  public function test_getReleaseList_withSorting_returnsReleaseList()
  {
    $this->getLoggedInUserForWeb('agent');
    factory(SdReleases::class,3)->create();
    $sortOrder = 'asc';
    $sortField = 'id';
    $response = $this->call('GET', url('service-desk/api/release-list'), ['sort-order' => $sortOrder, 'sort-field' => $sortField]);
    $releases = json_decode($response->content())->data->releases;
    $response->status(200);
    $this->assertCount(3, $releases);
    $this->assertDatabaseHas('sd_releases', ['id' => reset($releases)->id, 'subject' => reset($releases)->subject]);
    $this->assertTrue($releases[0]->id < $releases[1]->id);
    $this->assertTrue($releases[1]->id < $releases[2]->id);
  }

  /* @group getReleaseList */
  public function test_getReleaseList_withPage_returnsReleaseList()
  {
    $this->getLoggedInUserForWeb('agent');
    factory(SdReleases::class,3)->create();
    $sortOrder = 'asc';
    $page = 2;
    $limit = 1;
    $response = $this->call('GET', url('service-desk/api/release-list'), ['sort-order' => $sortOrder, 'limit' => $limit, 'page' => $page]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->releases);
    $this->assertEquals($data->current_page, $page);
    $release = reset($data->releases);
    $this->assertDatabaseHas('sd_releases', ['id' => $release->id, 'subject' => $release->subject, 'planned_start_date' => $release->planned_start_date, 'planned_end_date' => $release->planned_end_date, 'release_type_id' => $release->release_type->id, 'priority_id' => $release->priority->id, 'status_id' => $release->status->id]);
  }

  /* @group getReleaseList */
  public function test_getReleaseList_withWrongPage_returnsReleaseList()
  {
    $this->getLoggedInUserForWeb('agent');
    factory(SdReleases::class,3)->create();
    $sortOrder = 'asc';
    $page = 2;
    $limit = 3;
    $response = $this->call('GET', url('service-desk/api/release-list'), ['sort-order' => $sortOrder, 'limit' => $limit, 'page' => $page]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(0, $data->releases);
    $this->assertEquals($data->current_page, $page);
  }

  /* @group getReleaseList */
  public function test_getReleaseList_withSearchSubjectOfReleaseSearchQuery_returns()
  {
    $this->getLoggedInUserForWeb('agent');
    $releaseInDb = factory(SdReleases::class)->create(['subject' => 'Re Install Windows']);
    $this->assertDatabaseHas('sd_releases', ['id' => $releaseInDb->id, 'status_id' => 1]);
    factory(SdReleases::class)->create(['subject' => 'New USB Mouse']);
    $response = $this->call('GET', url('service-desk/api/release-list'), ['search-query' => $releaseInDb->subject]);
    $releases = json_decode($response->content())->data->releases;
    $response->status(200);
    $this->assertCount(1, $releases);
    $release = reset($releases);
    $this->assertDatabaseHas('sd_releases', ['id' => $releaseInDb->id, 'subject' => $release->subject, 'planned_start_date' => $release->planned_start_date, 'planned_end_date' => $release->planned_end_date, 'release_type_id' => $release->release_type->id, 'priority_id' => $release->priority->id, 'status_id' => $release->status->id]);
  }

  /* @group getReleaseList */
  public function test_getReleaseList_withStatusSearchTerm_returnsReleaseList()
  {
    $this->getLoggedInUserForWeb('agent');
    // Incomplete is with status id 4
    $releaseInDb = factory(SdReleases::class)->create(['status_id' => 4]);

    $this->assertDatabaseHas('sd_releases', ['id' => $releaseInDb->id, 'status_id' => 4]);
    factory(SdReleases::class)->create(['subject' => 'Usb mouse for dept sales']);
    $response = $this->call('GET', url('service-desk/api/release-list'), ['search-query' => 'Incomplete']);
    $releases = json_decode($response->content())->data->releases;
    $response->status(200);
    $this->assertCount(1, $releases);
    $release = reset($releases);
    $this->assertDatabaseHas('sd_releases', ['id' => $releaseInDb->id, 'subject' => $release->subject, 'planned_start_date' => $release->planned_start_date, 'planned_end_date' => $release->planned_end_date, 'release_type_id' => $release->release_type->id, 'priority_id' => $release->priority->id, 'status_id' => $releaseInDb->status->id]);
  }

  /* @group getReleaseList */
  public function test_getReleaseList_withPrioritySearchTerm_returnsReleasesList()
  {
    $this->getLoggedInUserForWeb('agent');
    // Medium is with priority id 2
    $release = factory(SdReleases::class)->create(['priority_id' => 2]);
    $this->assertDatabaseHas('sd_releases', ['id' => $release->id, 'priority_id' => 2]);
    factory(SdReleases::class)->create(['subject' => 'USB needed']);
    $response = $this->call('GET', url('service-desk/api/release-list'), ['search-query' => 'Medium']);
    $releases = json_decode($response->content())->data->releases;
    $response->status(200);
    $this->assertCount(1, $releases);
    $this->assertDatabaseHas('sd_releases', ['id' => $release->id, 'subject' => reset($releases)->subject, 'priority_id' => $release->priority_id]);
  }

  /* @group getReleaseList */
  public function test_getReleaseList_withNoParameters_returnsReleasesList()
  {
    $this->getLoggedInUserForWeb('agent');
    factory(SdReleases::class,2)->create();
    $response = $this->call('GET', url('service-desk/api/release-list'));
    $releases = json_decode($response->content())->data->releases;
    $response->status(200);
    $this->assertCount(2, $releases);
    $this->assertDatabaseHas('sd_releases', ['id' => reset($releases)->id, 'subject' => reset($releases)->subject]);
  }

  /* @group getReleaseList */
  public function test_getReleaseList_withNoParametersAndNoReleases_returnsEmptyReleaseList()
  {
    $this->getLoggedInUserForWeb('agent');
    $response = $this->call('GET', url('service-desk/api/release-list'));
    $releases = json_decode($response->content())->data->releases;
    $response->status(200);
    $this->assertCount(0, $releases);
    $this->assertEmpty($releases);
  }

  /* @group getReleaseList */
  public function test_getReleaseList_withWrong_releaseId_returnsEmptyReleasesList()
  {
    $this->getLoggedInUserForWeb('agent');
    $response = $this->call('GET', url('service-desk/api/release-list'), ['release_id' => 'q']);
    $releases = json_decode($response->content())->data->releases;
    $response->status(200);
    $this->assertCount(0, $releases);
    $this->assertEmpty($releases);
  }

  /* @group getReleaseList */
  public function test_getReleaseList_withStatusId_returnsReleasesList()
  {
    $this->getLoggedInUserForWeb('agent');
    $release = factory(SdReleases::class)->create(['status_id' => 1]);
    $this->assertDatabaseHas('sd_releases', ['id' => $release->id, 'status_id' => 1]);
    $response = $this->call('GET', url('service-desk/api/release-list'), ['status_ids' => [1]]);
    $releases = json_decode($response->content())->data->releases;
    $response->status(200);
    $this->assertCount(1, $releases);
    $this->assertDatabaseHas('sd_releases', ['id' => reset($releases)->id, 'status_id' => $release->status_id]);
  }

  /* @group getReleaseList */
  public function test_getReleaseList_withWrong_statusId_returns_emptyReleasesList()
  {
    $this->getLoggedInUserForWeb('agent');
    $response = $this->call('GET', url('service-desk/api/release-list'), ['status_ids' => ['wrong-status-id']]);
    $releases = json_decode($response->content())->data->releases;
    $response->status(200);
    $this->assertCount(0, $releases);
    $this->assertEmpty($releases);
  }

  /* @group getReleaseList */
  public function test_getReleaseList_withReleaseTypeId_returnsReleasesList()
  {
    $this->getLoggedInUserForWeb('agent');
    $release = factory(SdReleases::class)->create(['release_type_id' => 2]);
    $this->assertDatabaseHas('sd_releases', ['id' => $release->id, 'release_type_id' => 2]);
    $response = $this->call('GET', url('service-desk/api/release-list'), ['release_type_ids' => [2]]);
    $releases = json_decode($response->content())->data->releases;
    $response->status(200);
    $this->assertDatabaseHas('sd_releases', ['id' => reset($releases)->id, 'release_type_id' => $release->release_type_id]);
  }

  /* @group getReleaseList */
  public function test_getReleaseList_byLocationId_returnsReleasesList()
  {
    $this->getLoggedInUserForWeb('agent');
    $releaseId = factory(SdReleases::class)->create(['location_id' => 1])->id;
    $this->assertDatabaseHas('sd_releases', ['id' => $releaseId, 'location_id' => 1]);
    $response = $this->call('GET', url('service-desk/api/release-list'), ['location_ids' => [1]]);
    $releases = json_decode($response->content())->data->releases;
    $response->status(200);
    $this->assertCount(1, $releases);
    $this->assertDatabaseHas('sd_releases', ['id' => $releaseId, 'subject' => reset($releases)->subject]);
  }

  /* @group getReleaseList */
  public function test_getReleaseList_withLocationIdWrong_returnsEmptyReleasesList()
  {
    $this->getLoggedInUserForWeb('agent');
    $response = $this->call('GET', url('service-desk/api/release-list'), ['location_ids' => ['wrong-location-id']]);
    $releases = json_decode($response->content())->data->releases;
    $response->status(200);
    $this->assertCount(0, $releases);
    $this->assertEmpty($releases);
  }

  /* @group getReleaseList */
  public function test_getReleaseList_withProirityId_returnsReleasesList()
  {
    $this->getLoggedInUserForWeb('agent');
    $releaseId = factory(SdReleases::class)->create(['priority_id' => 1])->id;
    $this->assertDatabaseHas('sd_releases', ['id' => $releaseId, 'priority_id' => 1]);
    $response = $this->call('GET', url('service-desk/api/release-list'), ['priority_ids' => [1]]);
    $releases = json_decode($response->content())->data->releases;
    $response->status(200);
    $this->assertCount(1, $releases);
    $this->assertDatabaseHas('sd_releases', ['id' => $releaseId, 'subject' => reset($releases)->subject]);
  }

  /* @group getReleaseList */
  public function test_getReleaseList_withPriorityWrong_returnsEmptyReleaseList()
  {
    $this->getLoggedInUserForWeb('agent');
    $response = $this->call('GET', url('service-desk/api/release-list'), ['priority_ids' => ['wrong-priority-id']]);
    $releases = json_decode($response->content())->data->releases;
    $response->status(200);
    $this->assertCount(0, $releases);
    $this->assertEmpty($releases);
  }

  /* @group getReleaseList */
  public function test_getReleaseList_withPlannedStartDateTimeRange_returnsReleasesList()
  {
    $this->getLoggedInUserForWeb('agent');
    factory(SdReleases::class)->create(['planned_start_date' => '2020-07-12 05:12:00', 'planned_end_date' => '2020-07-14 05:12:00']);
    factory(SdReleases::class)->create(['planned_start_date' => '2020-07-14 05:12:00', 'planned_end_date' => '2020-07-16 05:12:00']);
    factory(SdReleases::class)->create(['planned_start_date' => '2020-07-16 05:12:00', 'planned_end_date' => '2020-07-18 05:12:00']);
    factory(SdReleases::class)->create(['planned_start_date' => '2020-07-18 05:12:00', 'planned_end_date' => '2020-07-20 05:12:00']);
    $endTimestamp = '2020-07-16 06:12:00';
    $startTimestamp = '2020-07-12 06:12:00';
    $startTime = changeTimezoneForDatetime($startTimestamp, agentTimeZone(), 'UTC');
    $endTime = changeTimezoneForDatetime($endTimestamp, agentTimeZone(), 'UTC');
    $timeRange = "date::{$startTimestamp}~{$endTimestamp}";
    $formattedRange = $this->getTimeRangeObject($timeRange, "UTC");
    $initialCount = SdReleases::where([['planned_start_date', '<=', $endTime],['planned_start_date', '>=', $startTime]])->count();
    $response = $this->call('GET', url('service-desk/api/release-list'), ['planned_start_date' => $timeRange]);
    $releases = json_decode($response->content())->data->releases;
    $response->status(200);
    $this->assertCount($initialCount, $releases);
    foreach ($releases as $release) {
      $this->assertGreaterThanOrEqual($startTime, $release->planned_start_date);
      $this->assertLessThanOrEqual($endTime, $release->planned_start_date);
      $this->assertDatabaseHas('sd_releases', ['id' => $release->id, 'subject' => $release->subject]);
    }
  }

  /* @group getReleaseList */
  public function test_getReleaseList_withPlannedEndDateTimeRange_returnsReleasesList()
  {
    $this->getLoggedInUserForWeb('agent');
    factory(SdReleases::class)->create(['planned_start_date' => '2020-07-12 05:12:00', 'planned_end_date' => '2020-07-14 05:12:00']);
    factory(SdReleases::class)->create(['planned_start_date' => '2020-07-14 05:12:00', 'planned_end_date' => '2020-07-16 05:12:00']);
    factory(SdReleases::class)->create(['planned_start_date' => '2020-07-16 05:12:00', 'planned_end_date' => '2020-07-18 05:12:00']);
    factory(SdReleases::class)->create(['planned_start_date' => '2020-07-18 05:12:00', 'planned_end_date' => '2020-07-20 05:12:00']);
    $endTimestamp = '2020-07-16 06:12:00';
    $startTimestamp = '2020-07-12 06:12:00';
    $startTime = changeTimezoneForDatetime($startTimestamp, agentTimeZone(), 'UTC');
    $endTime = changeTimezoneForDatetime($endTimestamp, agentTimeZone(), 'UTC');
    $timeRange = "date::{$startTimestamp}~{$endTimestamp}";
    $formattedRange = $this->getTimeRangeObject($timeRange, "UTC");
    $initialCount = SdReleases::where([['planned_end_date', '<=', $endTime],['planned_end_date', '>=', $startTime]])->count();
    $response = $this->call('GET', url('service-desk/api/release-list'), ['planned_end_date' => $timeRange]);
    $releases = json_decode($response->content())->data->releases;
    $response->status(200);
    $this->assertCount($initialCount, $releases);
    foreach ($releases as $release) {
      $this->assertGreaterThanOrEqual($startTime, $release->planned_end_date);
      $this->assertLessThanOrEqual($endTime, $release->planned_end_date);
      $this->assertDatabaseHas('sd_releases', ['id' => $release->id, 'subject' => $release->subject]);
    }
  }

  /* @group getReleaseList */
  public function test_getReleaseList_withCreatedAtTimeRange_returnsReleasesList()
  {
    $this->getLoggedInUserForWeb('agent');
    factory(SdReleases::class)->create(['created_at' => '2020-07-12 05:12:00']);
    factory(SdReleases::class)->create(['created_at' => '2020-07-14 05:12:00']);
    factory(SdReleases::class)->create(['created_at' => '2020-07-16 05:12:00']);
    factory(SdReleases::class)->create(['created_at' => '2020-07-18 05:12:00']);
    $endTimestamp = '2020-07-16 06:12:00';
    $startTimestamp = '2020-07-12 06:12:00';
    $startTime = changeTimezoneForDatetime($startTimestamp, agentTimeZone(), 'UTC');
    $endTime = changeTimezoneForDatetime($endTimestamp, agentTimeZone(), 'UTC');
    $timeRange = "date::{$startTimestamp}~{$endTimestamp}";
    $formattedRange = $this->getTimeRangeObject($timeRange, "UTC");
    $initialCount = SdReleases::where([['created_at', '<=', $endTime],['created_at', '>=', $startTime]])->count();
    $response = $this->call('GET', url('service-desk/api/release-list'), ['created_at' => $timeRange]);
    $releases = json_decode($response->content())->data->releases;
    $response->status(200);
    $this->assertCount($initialCount, $releases);
    foreach ($releases as $release) {
      $createdAt = SdReleases::find($release->id)->created_at;
      $this->assertGreaterThanOrEqual($startTime, $createdAt);
      $this->assertLessThanOrEqual($endTime, $createdAt);
      $this->assertDatabaseHas('sd_releases', ['id' => $release->id, 'subject' => $release->subject]);
    }
  }

  /* @group getReleaseList */
  public function test_getReleaseList_withUpdatedAtTimeRange_returnsReleasesList()
  {
    $this->getLoggedInUserForWeb('agent');
    factory(SdReleases::class)->create(['updated_at' => '2020-07-12 05:12:00']);
    factory(SdReleases::class)->create(['updated_at' => '2020-07-14 05:12:00']);
    factory(SdReleases::class)->create(['updated_at' => '2020-07-16 05:12:00']);
    factory(SdReleases::class)->create(['updated_at' => '2020-07-18 05:12:00']);
    $endTimestamp = '2020-07-16 06:12:00';
    $startTimestamp = '2020-07-12 06:12:00';
    $startTime = changeTimezoneForDatetime($startTimestamp, agentTimeZone(), 'UTC');
    $endTime = changeTimezoneForDatetime($endTimestamp, agentTimeZone(), 'UTC');
    $timeRange = "date::{$startTimestamp}~{$endTimestamp}";
    $formattedRange = $this->getTimeRangeObject($timeRange, "UTC");
    $initialCount = SdReleases::where([['updated_at', '<=', $endTime],['updated_at', '>=', $startTime]])->count();
    $response = $this->call('GET', url('service-desk/api/release-list'), ['updated_at' => $timeRange]);
    $releases = json_decode($response->content())->data->releases;
    $response->status(200);
    $this->assertCount($initialCount, $releases);
    foreach ($releases as $release) {
      $updatedAt = SdReleases::find($release->id)->updated_at;
      $this->assertGreaterThanOrEqual($startTime, $updatedAt);
      $this->assertLessThanOrEqual($endTime, $updatedAt);
      $this->assertDatabaseHas('sd_releases', ['id' => $release->id, 'subject' => $release->subject]);
    }
  }

  /* @group getReleaseList */
  public function test_getReleaseList_withAssetId_returnsReleaseDataBasedOnPassedAssetId()
  {
    $this->getLoggedInUserForWeb('agent');
    $assetId = factory(SdAssets::class)->create()->id;
    $release = factory(SdReleases::class)->create();
    $release->attachAssets()->sync([$assetId => ['type' => 'sd_releases']]);
    $response = $this->call('GET', url('service-desk/api/release-list'), ['asset_ids' => $assetId]);
    $releases = json_decode($response->content())->data->releases;
    $response->status(200);
    $this->assertCount(1, $releases);
    $release = reset($releases);
    $this->assertDatabaseHas('sd_releases', ['id' => $release->id, 'subject' => $release->subject]);
    $this->assertDatabaseHas('sd_common_asset_relation', ['asset_id' => $assetId, 'type_id' => $release->id, 'type' => 'sd_releases']);
  }

  /* @group getReleaseList */
  public function test_getReleaseList_withWrongAssetId_returnsEmptyReleaseDataBasedOnWrongAssetId()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdReleases::class, 3)->create();
    $response = $this->call('GET', url('service-desk/api/release-list'), ['asset_ids' => 'wrong-asset-id']);
    $releasesInResponse = json_decode($response->content())->data->releases;
    $response->status(200);
    $this->assertCount(0, $releasesInResponse);
    $this->assertEmpty($releasesInResponse);
  }

  /* @group getReleaseList */
  public function test_getReleaseList_withSearchQueryAssetName_returnsReleaseDataBasedOnAssetName()
  {
    $this->getLoggedInUserForWeb('agent');
    $asset = factory(SdAssets::class)->create(['name' => 'Desktop']);
    factory(SdReleases::class, 2)->create();
    $release = factory(SdReleases::class)->create();
    $release->attachAssets()->sync([$asset->id => ['type' => 'sd_releases']]);
    $response = $this->call('GET', url('service-desk/api/release-list'), ['search-query' => $asset->name]);
    $releases = json_decode($response->content())->data->releases;
    $response->status(200);
    $this->assertCount(1, $releases);
    $releases = reset($releases);
    $this->assertDatabaseHas('sd_releases', ['id' => $release->id, 'subject' => $release->subject]);
    $this->assertDatabaseHas('sd_common_asset_relation', ['asset_id' => $asset->id, 'type_id' => $release->id, 'type' => 'sd_releases']);
  }

  /* @group getReleaseList */
  public function test_getReleaseList_withChangeId_returnsReleaseDataBasedOnPassedChangeId()
  {
    $this->getLoggedInUserForWeb('agent');
    $changeId = factory(SdChanges::class)->create()->id;
    $release = factory(SdReleases::class)->create();
    $release->attachChanges()->sync([$changeId]);
    $response = $this->call('GET', url('service-desk/api/release-list'), ['change_ids' => $changeId]);
    $releases = json_decode($response->content())->data->releases;
    $response->status(200);
    $this->assertCount(1, $releases);
    $release = reset($releases);
    $this->assertDatabaseHas('sd_releases', ['id' => $release->id, 'subject' => $release->subject]);
    $this->assertDatabaseHas('sd_change_release', ['change_id' => $changeId, 'release_id' => $release->id]);
  }

  /* @group getReleaseList */
  public function test_getReleaseList_withWrongChangeId_returnsEmptyReleaseDataBasedOnWrongChangeId()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdReleases::class, 3)->create();
    $response = $this->call('GET', url('service-desk/api/release-list'), ['change_ids' => 'wrong-change-id']);
    $releasesInResponse = json_decode($response->content())->data->releases;
    $response->status(200);
    $this->assertCount(0, $releasesInResponse);
    $this->assertEmpty($releasesInResponse);
  }

    /* @group getReleaseList */
  public function test_getReleaseList_withSearchQueryChangeSubject_returnsReleaseDataBasedOnChangeSubject()
  {
    $this->getLoggedInUserForWeb('agent');
    $change = factory(SdChanges::class)->create(['subject' => 'Desktop Broken']);
    factory(SdReleases::class, 2)->create();
    $release = factory(SdReleases::class)->create();
    $release->attachChanges()->sync([$change->id]);
    $response = $this->call('GET', url('service-desk/api/release-list'), ['search-query' => $change->subject]);
    $releases = json_decode($response->content())->data->releases;
    $response->status(200);
    $this->assertCount(1, $releases);
    $releases = reset($releases);
    $this->assertDatabaseHas('sd_releases', ['id' => $release->id, 'subject' => $release->subject]);
    $this->assertDatabaseHas('sd_change_release', ['change_id' => $change->id, 'release_id' => $release->id]);
  }


}