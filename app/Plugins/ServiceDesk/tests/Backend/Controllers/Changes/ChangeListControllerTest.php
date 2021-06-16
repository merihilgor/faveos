<?php

namespace App\Plugins\ServiceDesk\tests\Backend\Controllers\Changes;

use Tests\AddOnTestCase;
use App\Plugins\ServiceDesk\Model\Changes\SdChanges;
use App\Model\helpdesk\Agent\Department;
use App\Model\helpdesk\Agent\Teams;
use App\Plugins\ServiceDesk\Model\Changes\SdChangestatus;
use App\Plugins\ServiceDesk\Model\Changes\SdChangepriorities;
use App\Plugins\ServiceDesk\Model\Changes\SdChangetypes;
use App\Plugins\ServiceDesk\Model\Assets\SdImpactypes as SdImpacttypes;
use App\Location\Models\Location;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\Plugins\ServiceDesk\Model\Releases\SdReleases;
use App\User;
use App\Traits\FaveoDateParser;
use App\Model\helpdesk\Ticket\Tickets;
use App\Plugins\ServiceDesk\Model\Problem\SdProblem;

/**
 * Tests ChangeListController
 * 
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
*/

class ChangeListControllerTest extends AddOnTestCase
{
  use FaveoDateParser;
  /** @group getChangeList */
  public function test_getChangeList_withoutAnyExtraParameter_returnsCompleteChangeData()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdChanges::class, 3)->create();
    $response = $this->call('GET', url('service-desk/api/change-list'));
    $changes = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(3, $changes);
    $change = reset($changes);
    $this->assertDatabaseHas('sd_changes', ['id' => $change->id, 'subject' => $change->subject]);
  }

  /** @group getChangeList */
  public function test_getChangeList_withLimit_returnsNumberOfChangeDataBasedOnLimit()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdChanges::class, 3)->create();
    $limit = 1;
    $response = $this->call('GET', url('service-desk/api/change-list'), ['limit' => $limit]);
    $changes = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(1, $changes);
    $change = reset($changes);
    $this->assertDatabaseHas('sd_changes', ['id' => $change->id, 'subject' => $change->subject]);
  }

  /** @group getChangeList */
  public function test_getChangeList_withSortFieldAndSortOrder_returnsChangesDataInAscendingOrderBasedOnIdField()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdChanges::class, 3)->create();
    $changesInDb = SdChanges::orderBy('id', 'asc')->get()->toArray();
    $response = $this->call('GET', url('service-desk/api/change-list'), ['sort-order' => 'asc', 'sort-field' => 'id']);
    $changes = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(3, $changes);
    foreach ($changes as $change) {
      $this->assertDatabaseHas('sd_changes', ['id' => $change->id, 'subject' => $change->subject]);
    }
  }

  /** @group getChangeList */
  public function test_getChangeList_withPage_returnsChangeDataBasedOnSpecificPage()
  {
    $this->getLoggedInUserForWeb('agent');
    factory(SdChanges::class,3)->create();
    $sortOrder = 'asc';
    $page = 2;
    $limit = 1;
    $response = $this->call('GET', url('service-desk/api/change-list'), ['sort-order' => $sortOrder, 'limit' => $limit, 'page' => $page]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->changes);
    $this->assertEquals($data->current_page, $page);
    $change = reset($data->changes);
    $this->assertDatabaseHas('sd_changes', ['id' => $change->id, 'subject' => $change->subject]);
  }

  /** @group getChangeList */
  public function test_getChangeList_withSearchQueryEmpty_returnsCompleteChangeData()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdChanges::class, 3)->create();
    $response = $this->call('GET', url('service-desk/api/change-list'), ['search-query' => '']);
    $changes = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(3, $changes);
    foreach ($changes as $change) {
      $this->assertDatabaseHas('sd_changes', ['id' => $change->id, 'subject' => $change->subject]);
    }
  }

  /** @group getChangeList */
  public function test_getChangeList_withSearchQueryChangeSubject_returnsChangeDataBasedOnPassedChangeSubject()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdChanges::class, 3)->create();
    $changeInDb = factory(SdChanges::class)->create();
    $response = $this->call('GET', url('service-desk/api/change-list'), ['search-query' => $changeInDb->subject]);
    $changesInResponse = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(1, $changesInResponse);
    $changeInResponse = reset($changesInResponse);
    $this->assertEquals($changeInDb->id, $changeInResponse->id);
    $this->assertEquals($changeInDb->subject, $changeInResponse->subject);
  }

   /** @group getChangeList */
  public function test_getChangeList_withSearchQueryDepartmentName_returnsChangeDataBasedOnDepartmentName()
  {
    $this->getloggedInUserForWeb('agent');
    $department = Department::find(2);
    factory(SdChanges::class, 2)->create(['department_id' => 1]);
    factory(SdChanges::class, 3)->create(['department_id' => $department->id]);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['search-query' => $department->name]);
    $changes = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(3, $changes);
    foreach ($changes as $change) {
      $this->assertDatabaseHas('sd_changes', ['id' => $change->id, 'subject' => $change->subject, 'department_id' => $department->id]);
    }
  }

  /** @group getChangeList */
  public function test_getChangeList_withSearchQueryTeamName_returnsChangeDataBasedOnTeamName()
  {
    $this->getloggedInUserForWeb('agent');
    $team = Teams::find(2);
    factory(SdChanges::class, 2)->create(['team_id' => 1]);
    factory(SdChanges::class, 3)->create(['team_id' => $team->id]);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['search-query' => $team->name]);
    $changes = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(3, $changes);
    foreach ($changes as $change) {
      $this->assertDatabaseHas('sd_changes', ['id' => $change->id, 'subject' => $change->subject, 'team_id' => $team->id]);
    }
  }

  /** @group getChangeList */
  public function test_getChangeList_withSearchQueryRequesterFirstName_returnsChangeDataBasedOnRequesterFirstName()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdChanges::class, 2)->create(['requester_id' => 1]);
    factory(SdChanges::class, 3)->create(['requester_id' => $this->user->id]);
    $this->user = User::updateOrCreate(['id' => $this->user->id], ['first_name' => 'Sakti']);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['search-query' => $this->user->first_name]);
    $changes = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(3, $changes);
    foreach ($changes as $change) {
      $this->assertDatabaseHas('sd_changes', ['id' => $change->id, 'subject' => $change->subject, 'requester_id' => $this->user->id]);
    }
  }

  /** @group getChangeList */
  public function test_getChangeList_withSearchQueryRequesterLastName_returnsChangeDataBasedOnRequesterLastName()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdChanges::class, 2)->create(['requester_id' => 1]);
    factory(SdChanges::class, 3)->create(['requester_id' => $this->user->id]);
    $this->user = User::updateOrCreate(['id' => $this->user->id], ['last_name' => 'Kumar']);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['search-query' => $this->user->last_name]);
    $changes = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(3, $changes);
    foreach ($changes as $change) {
      $this->assertDatabaseHas('sd_changes', ['id' => $change->id, 'subject' => $change->subject, 'requester_id' => $this->user->id]);
    }
  }

  /** @group getChangeList */
  public function test_getChangeList_withSearchQueryRequesterFullName_returnsChangeDataBasedOnRequesterFullName()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdChanges::class, 2)->create(['requester_id' => 1]);
    factory(SdChanges::class, 3)->create(['requester_id' => $this->user->id]);
    $this->user = User::updateOrCreate(['id' => $this->user->id], ['first_name' => 'Sakti', 'last_name' => 'Kumar']);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['search-query' => $this->user->first_name . ' ' . $this->user->last_name]);
    $changes = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(3, $changes);
    foreach ($changes as $change) {
      $this->assertDatabaseHas('sd_changes', ['id' => $change->id, 'subject' => $change->subject, 'requester_id' => $this->user->id]);
    }
  }

  /** @group getChangeList */
  public function test_getChangeList_withSearchQueryRequesterUserName_returnsChangeDataBasedOnRequesterUserName()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdChanges::class, 2)->create(['requester_id' => 1]);
    factory(SdChanges::class, 3)->create(['requester_id' => $this->user->id]);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['search-query' => $this->user->user_name]);
    $changes = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(3, $changes);
    foreach ($changes as $change) {
      $this->assertDatabaseHas('sd_changes', ['id' => $change->id, 'subject' => $change->subject, 'requester_id' => $this->user->id]);
    }
  }

  /** @group getChangeList */
  public function test_getChangeList_withSearchQueryRequesterEmail_returnsChangeDataBasedOnRequesterEmail()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdChanges::class, 2)->create(['requester_id' => 1]);
    factory(SdChanges::class, 3)->create(['requester_id' => $this->user->id]);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['search-query' => $this->user->email]);
    $changes = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(3, $changes);
    foreach ($changes as $change) {
      $this->assertDatabaseHas('sd_changes', ['id' => $change->id, 'subject' => $change->subject, 'requester_id' => $this->user->id]);
    }
  }

  /** @group getChangeList */
  public function test_getChangeList_withSearchQueryStatusName_returnsChangeDataBasedOnStatusName()
  {
    $this->getloggedInUserForWeb('agent');
    $status = SdChangestatus::find(2);
    factory(SdChanges::class, 2)->create(['status_id' => 1]);
    factory(SdChanges::class, 3)->create(['status_id' => $status->id]);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['search-query' => $status->name]);
    $changes = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(3, $changes);
    foreach ($changes as $change) {
      $this->assertDatabaseHas('sd_changes', ['id' => $change->id, 'subject' => $change->subject, 'status_id' => $status->id]);
    }
  }

  /** @group getChangeList */
  public function test_getChangeList_withSearchQueryPriorityName_returnsChangeDataBasedOnPriorityName()
  {
    $this->getloggedInUserForWeb('agent');
    $priority = SdChangepriorities::find(2);
    factory(SdChanges::class, 2)->create(['priority_id' => 1]);
    factory(SdChanges::class, 3)->create(['priority_id' => $priority->id]);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['search-query' => $priority->name]);
    $changes = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(3, $changes);
    foreach ($changes as $change) {
      $this->assertDatabaseHas('sd_changes', ['id' => $change->id, 'subject' => $change->subject, 'priority_id' => $priority->id]);
    }
  }

  /** @group getChangeList */
  public function test_getChangeList_withSearchQueryChangeTypeName_returnsChangeDataBasedOnChangeTypeName()
  {
    $this->getloggedInUserForWeb('agent');
    $changeType = SdChangetypes::find(2);
    factory(SdChanges::class, 2)->create(['change_type_id' => 1]);
    factory(SdChanges::class, 3)->create(['change_type_id' => $changeType->id]);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['search-query' => $changeType->name]);
    $changes = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(3, $changes);
    foreach ($changes as $change) {
      $this->assertDatabaseHas('sd_changes', ['id' => $change->id, 'subject' => $change->subject, 'change_type_id' => $changeType->id]);
    }
  }

  /** @group getChangeList */
  public function test_getChangeList_withSearchQueryImpactTypeName_returnsChangeDataBasedOnImpactTypeName()
  {
    $this->getloggedInUserForWeb('agent');
    $impactType = SdImpacttypes::find(2);
    factory(SdChanges::class, 2)->create(['impact_id' => 1]);
    factory(SdChanges::class, 3)->create(['impact_id' => $impactType->id]);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['search-query' => $impactType->name]);
    $changes = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(3, $changes);
    foreach ($changes as $change) {
      $this->assertDatabaseHas('sd_changes', ['id' => $change->id, 'subject' => $change->subject, 'impact_id' => $impactType->id]);
    }
  }

  /** @group getChangeList */
  public function test_getChangeList_withSearchQueryLocationName_returnsChangeDataBasedOnLocationName()
  {
    $this->getloggedInUserForWeb('agent');
    $location = Location::find(2);
    factory(SdChanges::class, 2)->create(['location_id' => 1]);
    factory(SdChanges::class, 3)->create(['location_id' => $location->id]);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['search-query' => $location->title]);
    $changes = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(3, $changes);
    foreach ($changes as $change) {
      $this->assertDatabaseHas('sd_changes', ['id' => $change->id, 'subject' => $change->subject, 'location_id' => $location->id]);
    }
  }

  /** @group getChangeList */
  public function test_getChangeList_withChangeId_returnsChangeDataBasedOnPassedChangedId()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdChanges::class, 3)->create();
    $changeInDb = factory(SdChanges::class)->create();
    $response = $this->call('GET', url('service-desk/api/change-list'), ['change_ids' => $changeInDb->id]);
    $changesInResponse = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(1, $changesInResponse);
    $changeInResponse = reset($changesInResponse);
    $this->assertEquals($changeInDb->id, $changeInResponse->id);
    $this->assertEquals($changeInDb->subject, $changeInResponse->subject);
  }

  /** @group getChangeList */
  public function test_getChangeList_withWrongChangeId_returnsEmptyChangeDataBasedOnWrongChangeId()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdChanges::class, 3)->create();
    $response = $this->call('GET', url('service-desk/api/change-list'), ['change_ids' => 'wrong-change-id']);
    $changesInResponse = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(0, $changesInResponse);
    $this->assertEmpty($changesInResponse);
  }

  /** @group getChangeList */
  public function test_getChangeList_withDepartmentId_returnsChangeDataBasedOnPassedDepartmentId()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdChanges::class, 3)->create();
    factory(SdChanges::class)->create(['department_id' => 1]);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['department_ids' => 1]);
    $changes = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(1, $changes);
    $change = reset($changes);
    $this->assertDatabaseHas('sd_changes', ['id' => $change->id, 'subject' => $change->subject, 'department_id' => 1]);
  }

  /** @group getChangeList */
  public function test_getChangeList_withWrongDepartmentId_returnsEmptyChangeDataBasedOnWrongDepartmentId()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdChanges::class, 3)->create(['department_id' => 1]);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['department_ids' => 'wrong-department-id']);
    $changesInResponse = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(0, $changesInResponse);
    $this->assertEmpty($changesInResponse);
  }

  /** @group getChangeList */
  public function test_getChangeList_withTeamId_returnsChangeDataBasedOnPassedTeamId()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdChanges::class, 3)->create();
    factory(SdChanges::class)->create(['team_id' => 1]);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['team_ids' => 1]);
    $changes = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(1, $changes);
    $change = reset($changes);
    $this->assertDatabaseHas('sd_changes', ['id' => $change->id, 'subject' => $change->subject, 'team_id' => 1]);
  }

  /** @group getChangeList */
  public function test_getChangeList_withWrongTeamId_returnsEmptyChangeDataBasedOnWrongTeamId()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdChanges::class, 3)->create(['team_id' => 1]);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['team_ids' => 'wrong-team-id']);
    $changesInResponse = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(0, $changesInResponse);
    $this->assertEmpty($changesInResponse);
  }

  /** @group getChangeList */
  public function test_getChangeList_withRequesterId_returnsChangeDataBasedOnPassedRequesterId()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdChanges::class, 3)->create();
    factory(SdChanges::class)->create(['requester_id' => $this->user->id]);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['requester_ids' => $this->user->id]);
    $changes = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(1, $changes);
    $change = reset($changes);
    $this->assertDatabaseHas('sd_changes', ['id' => $change->id, 'subject' => $change->subject, 'requester_id' => $this->user->id]);
  }

  /** @group getChangeList */
  public function test_getChangeList_withWrongRequesterId_returnsEmptyChangeDataBasedOnWrongRequesterId()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdChanges::class, 3)->create(['team_id' => 1]);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['requester_ids' => 'wrong-requester-id']);
    $changesInResponse = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(0, $changesInResponse);
    $this->assertEmpty($changesInResponse);
  }

  /** @group getChangeList */
  public function test_getChangeList_withStatusId_returnsChangeDataBasedOnPassedStatusId()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdChanges::class, 3)->create();
    $statusId = 2;
    factory(SdChanges::class)->create(['status_id' => $statusId]);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['status_ids' => $statusId]);
    $changes = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(1, $changes);
    $change = reset($changes);
    $this->assertDatabaseHas('sd_changes', ['id' => $change->id, 'subject' => $change->subject, 'status_id' => $statusId]);
  }

  /** @group getChangeList */
  public function test_getChangeList_withWrongStatusId_returnsEmptyChangeDataBasedOnWrongStatusId()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdChanges::class, 3)->create(['status_id' => 1]);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['status_ids' => 'wrong-status-id']);
    $changesInResponse = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(0, $changesInResponse);
    $this->assertEmpty($changesInResponse);
  }

  /** @group getChangeList */
  public function test_getChangeList_withPriorityId_returnsChangeDataBasedOnPassedPriorityId()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdChanges::class, 3)->create();
    $priorityId = 2;
    factory(SdChanges::class)->create(['priority_id' => $priorityId]);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['priority_ids' => $priorityId]);
    $changes = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(1, $changes);
    $change = reset($changes);
    $this->assertDatabaseHas('sd_changes', ['id' => $change->id, 'subject' => $change->subject, 'priority_id' => $priorityId]);
  }

  /** @group getChangeList */
  public function test_getChangeList_withWrongPriorityId_returnsEmptyChangeDataBasedOnWrongPriorityId()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdChanges::class, 3)->create(['priority_id' => 1]);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['priority_ids' => 'wrong-priority-id']);
    $changesInResponse = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(0, $changesInResponse);
    $this->assertEmpty($changesInResponse);
  }

  /** @group getChangeList */
  public function test_getChangeList_withChangeTypeId_returnsChangeDataBasedOnPassedChangeTypeId()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdChanges::class, 3)->create();
    $changeTypeId = 2;
    factory(SdChanges::class)->create(['change_type_id' => $changeTypeId]);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['change_type_ids' => $changeTypeId]);
    $changes = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(1, $changes);
    $change = reset($changes);
    $this->assertDatabaseHas('sd_changes', ['id' => $change->id, 'subject' => $change->subject, 'change_type_id' => $changeTypeId]);
  }

  /** @group getChangeList */
  public function test_getChangeList_withWrongChangeTypeId_returnsEmptyChangeDataBasedOnWrongChangeTypeId()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdChanges::class, 3)->create(['change_type_id' => 1]);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['change_type_ids' => 'wrong-change-type-id']);
    $changesInResponse = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(0, $changesInResponse);
    $this->assertEmpty($changesInResponse);
  }

   /** @group getChangeList */
  public function test_getChangeList_withImpactId_returnsChangeDataBasedOnPassedImpactId()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdChanges::class, 3)->create();
    $impactId = 2;
    factory(SdChanges::class)->create(['impact_id' => $impactId]);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['impact_ids' => $impactId]);
    $changes = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(1, $changes);
    $change = reset($changes);
    $this->assertDatabaseHas('sd_changes', ['id' => $change->id, 'subject' => $change->subject, 'impact_id' => $impactId]);
  }

  /** @group getChangeList */
  public function test_getChangeList_withWrongImpactId_returnsEmptyChangeDataBasedOnWrongImpactId()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdChanges::class, 3)->create(['impact_id' => 1]);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['impact_ids' => 'wrong-impact-id']);
    $changesInResponse = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(0, $changesInResponse);
    $this->assertEmpty($changesInResponse);
  }

   /** @group getChangeList */
  public function test_getChangeList_withLocationId_returnsChangeDataBasedOnPassedLocationId()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdChanges::class, 3)->create();
    $locationId = 2;
    factory(SdChanges::class)->create(['location_id' => $locationId]);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['location_ids' => $locationId]);
    $changes = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(1, $changes);
    $change = reset($changes);
    $this->assertDatabaseHas('sd_changes', ['id' => $change->id, 'subject' => $change->subject, 'location_id' => $locationId]);
  }

  /** @group getChangeList */
  public function test_getChangeList_withWrongLocationId_returnsEmptyChangeDataBasedOnWrongLocationId()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdChanges::class, 3)->create(['location_id' => 1]);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['location_ids' => 'wrong-location-id']);
    $changesInResponse = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(0, $changesInResponse);
    $this->assertEmpty($changesInResponse);
  }

  /** @group getChangeList */
  public function test_getChangeList_withAssetId_returnsChangeDataBasedOnPassedAssetId()
  {
    $this->getLoggedInUserForWeb('agent');
    $assetId = factory(SdAssets::class)->create()->id;
    $change = factory(SdChanges::class)->create();
    $change->attachAssets()->sync([$assetId => ['type' => 'sd_changes']]);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['asset_ids' => $assetId]);
    $changes = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(1, $changes);
    $change = reset($changes);
    $this->assertDatabaseHas('sd_changes', ['id' => $change->id, 'subject' => $change->subject]);
    $this->assertDatabaseHas('sd_common_asset_relation', ['asset_id' => $assetId, 'type_id' => $change->id, 'type' => 'sd_changes']);
  }

  /** @group getChangeList */
  public function test_getChangeList_withWrongAssetId_returnsEmptyChangeDataBasedOnWrongAssetId()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdChanges::class, 3)->create();
    $response = $this->call('GET', url('service-desk/api/change-list'), ['asset_ids' => 'wrong-asset-id']);
    $changesInResponse = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(0, $changesInResponse);
    $this->assertEmpty($changesInResponse);
  }

   /** @group getChangeList */
  public function test_getChangeList_withReleaseId_returnsChangeDataBasedOnPassedReleaseId()
  {
    $this->getLoggedInUserForWeb('agent');
    $releaseId = factory(SdReleases::class)->create()->id;
    $change = factory(SdChanges::class)->create();
    $change->attachReleases()->sync($releaseId);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['release_ids' => $releaseId]);
    $changes = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(1, $changes);
    $change = reset($changes);
    $this->assertDatabaseHas('sd_changes', ['id' => $change->id, 'subject' => $change->subject]);
    $this->assertDatabaseHas('sd_change_release', ['release_id' => $releaseId, 'change_id' => $change->id]);
  }

  /** @group getChangeList */
  public function test_getChangeList_withWrongReleaseId_returnsEmptyChangeDataBasedOnWrongReleaseId()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdReleases::class, 3)->create();
    $response = $this->call('GET', url('service-desk/api/change-list'), ['release_ids' => 'wrong-release-id']);
    $changesInResponse = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(0, $changesInResponse);
    $this->assertEmpty($changesInResponse);
  }

  /** @group getChangeList */
  public function test_getChangeList_withSearchQueryReleaseSubject_returnsChangeDataBasedOnReleaseSubject()
  {
    $this->getLoggedInUserForWeb('agent');
    $release = factory(SdReleases::class)->create(['subject' => 'Desktop not working']);
    factory(SdChanges::class, 2)->create();
    $change = factory(SdChanges::class)->create();
    $change->attachReleases()->sync($release->id);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['search-query' => $release->subject]);
    $changes = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(1, $changes);
    $change = reset($changes);
    $this->assertDatabaseHas('sd_changes', ['id' => $change->id, 'subject' => $change->subject]);
    $this->assertDatabaseHas('sd_change_release', ['release_id' => $release->id, 'change_id' => $change->id]);
  }

  /* @group getChangeList */
  public function test_getChangeList_withChangeCreatedAtTimeRange_returnsChangeList()
  {
    $this->getLoggedInUserForWeb('agent');
    factory(SdChanges::class)->create(['created_at' => '2020-07-12 05:12:00']);
    factory(SdChanges::class)->create(['created_at' => '2020-07-14 05:12:00']);
    factory(SdChanges::class)->create(['created_at' => '2020-07-16 05:12:00']);
    factory(SdChanges::class)->create(['created_at' => '2020-07-18 05:12:00']);
    $endTimestamp = '2020-07-16 05:12:00';
    $startTimestamp = '2020-07-12 05:13:00';
    $startTime = changeTimezoneForDatetime($startTimestamp, agentTimeZone(), 'UTC');
    $endTime = changeTimezoneForDatetime($endTimestamp, agentTimeZone(), 'UTC');
    $timeRange = "date::{$startTimestamp}~{$endTimestamp}";
    $formattedRange = $this->getTimeRangeObject($timeRange, "UTC");
    $initialCount = SdChanges::where([['created_at', '<=', $endTime],['created_at', '>=', $startTime]])->count();
    $response = $this->call('GET', url('service-desk/api/change-list'), ['created_at' => $timeRange]);
    $changes = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount($initialCount, $changes);
    foreach ($changes as $change) {
      $this->assertGreaterThanOrEqual($startTime, $change->created_at);
      $this->assertLessThanOrEqual($endTime, $change->created_at);
      $this->assertDatabaseHas('sd_changes', ['id' => $change->id, 'subject' => $change->subject]);
    }
  }

  /* @group getChangeList */
  public function test_getChangeList_withChangeUpdatedAtTimeRange_returnsChangeList()
  {
    $this->getLoggedInUserForWeb('agent');
    factory(SdChanges::class)->create(['updated_at' => '2020-07-12 05:12:00']);
    factory(SdChanges::class)->create(['updated_at' => '2020-07-14 05:12:00']);
    factory(SdChanges::class)->create(['updated_at' => '2020-07-16 05:12:00']);
    factory(SdChanges::class)->create(['updated_at' => '2020-07-18 05:12:00']);
    $endTimestamp = '2020-07-16 05:12:00';
    $startTimestamp = '2020-07-12 05:13:00';
    $startTime = changeTimezoneForDatetime($startTimestamp, agentTimeZone(), 'UTC');
    $endTime = changeTimezoneForDatetime($endTimestamp, agentTimeZone(), 'UTC');
    $timeRange = "date::{$startTimestamp}~{$endTimestamp}";
    $formattedRange = $this->getTimeRangeObject($timeRange, "UTC");
    $initialCount = SdChanges::where([['updated_at', '<=', $endTime],['updated_at', '>=', $startTime]])->count();
    $response = $this->call('GET', url('service-desk/api/change-list'), ['updated_at' => $timeRange]);
    $changes = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount($initialCount, $changes);
    foreach ($changes as $change) {
      $changeUpdatedAtTime = SdChanges::find($change->id)->updated_at;
      $this->assertGreaterThanOrEqual($startTime, $changeUpdatedAtTime);
      $this->assertLessThanOrEqual($endTime, $changeUpdatedAtTime);
      $this->assertDatabaseHas('sd_changes', ['id' => $change->id, 'subject' => $change->subject]);
    }
  }

  /** @group getChangeList */
  public function test_getChangeList_withSearchQueryWrongChangeIdentifier_returnsEmptyChangeList()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdChanges::class)->create(['identifier' => 'CHN-33']);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['search-query' => 'wrong-identifier']);
    $changesInResponse = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(0, $changesInResponse);
    $this->assertEmpty($changesInResponse);
  }

  /** @group getChangeList */
  public function test_getChangeList_withSearchQueryChangeIdentifier_returnsChangeDataBasedOnPassedIdentifier()
  {
    $this->getloggedInUserForWeb('agent');
    factory(SdChanges::class, 3)->create();
    $changeInDb = factory(SdChanges::class)->create(['identifier' => 'CHN-33']);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['search-query' => $changeInDb->identifier]);
    $changesInResponse = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(1, $changesInResponse);
    $changeInResponse = reset($changesInResponse);
    $this->assertEquals($changeInDb->id, $changeInResponse->id);
    $this->assertEquals($changeInResponse->identifier, $changeInDb->identifier);
    $this->assertEquals($changeInDb->subject, $changeInResponse->subject);
  }

  /** @group getChangeList */
  public function test_getChangeList_withTicketId_returnsChangeDataBasedOnPassedTicketId()
  {
    $this->getLoggedInUserForWeb('agent');
    $ticketId = factory(Tickets::class)->create()->id;
    $change = factory(SdChanges::class)->create();
    $type = 'initiated';
    $change->attachTickets()->sync([$ticketId => ['type' => $type]]);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['ticket_ids' => [$ticketId]]);
    $changes = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(1, $changes);
    $change = reset($changes);
    $this->assertDatabaseHas('sd_changes', ['id' => $change->id, 'subject' => $change->subject]);
    $this->assertDatabaseHas('sd_change_ticket', ['change_id' => $change->id, 'ticket_id' => $ticketId, 'type' => $type]);
  }

  /** @group getChangeList */
  public function test_getChangeList_withWrongTicketId_returnsEmptyChangeDataBasedOnWrongTicketId()
  {
    $this->getloggedInUserForWeb('agent');
    $ticketId = factory(Tickets::class)->create()->id;
    $change = factory(SdChanges::class)->create();
    $type = 'initiated';
    $change->attachTickets()->sync([$ticketId => ['type' => $type]]);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['ticket_ids' => ['wrong-ticket-id']]);
    $changesInResponse = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(0, $changesInResponse);
    $this->assertEmpty($changesInResponse);
  }

  /** @group getChangeList */
  public function test_getChangeList_withProblemId_returnsChangeDataBasedOnPassedProblemId()
  {
    $this->getLoggedInUserForWeb('agent');
    $problemId = factory(SdProblem::class)->create()->id;
    $change = factory(SdChanges::class)->create();
    $change->attachProblems()->sync([$problemId]);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['problem_ids' => [$problemId]]);
    $changes = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(1, $changes);
    $change = reset($changes);
    $this->assertDatabaseHas('sd_changes', ['id' => $change->id, 'subject' => $change->subject]);
    $this->assertDatabaseHas('sd_problem_change', ['change_id' => $change->id, 'problem_id' => $problemId]);
  }

  /** @group getChangeList */
  public function test_getChangeList_withWrongProblemId_returnsEmptyChangeData()
  {
    $this->getloggedInUserForWeb('agent');
    $problemId = factory(SdProblem::class)->create()->id;
    $change = factory(SdChanges::class)->create();
    $change->attachProblems()->sync([$problemId]);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['problem_ids' => ['wrong-problem-id']]);
    $changesInResponse = json_decode($response->content())->data->changes;
    $response->status(200);
    $this->assertCount(0, $changesInResponse);
    $this->assertEmpty($changesInResponse);
  }

  /** @group getChangeList */
  public function test_getNotLinkedChangeList_withReleaseId_returnsAssetsListNotLinkedToReleaseBasedOnReleaseId()
  {
    $this->getLoggedInUserForWeb('admin');
    $change = factory(SdChanges::class)->create();
    factory(SdChanges::class,5)->create();
    $release = factory(SdReleases::class)->create();
    $release->attachChanges()->attach($change->id);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['release_id' => $release->id]);
    $changes = json_decode($response->content())->data->changes;
    $response->assertStatus(200);
    $this->assertCount(5, $changes);
  }

  /** @group getChangeList */
  public function test_getNotLinkedChangeList_withWrongReleaseId_returnsCompleteChangesList()
  {
    $this->getLoggedInUserForWeb('admin');
    $change = factory(SdChanges::class)->create();
    factory(SdChanges::class,5)->create();
    $release = factory(SdReleases::class)->create();
    $release->attachChanges()->attach($change->id);
    $response = $this->call('GET', url('service-desk/api/change-list'), ['release_id' => 'wrong-release-id']);
    $changes = json_decode($response->content())->data->changes;
    $response->assertStatus(200);
    $this->assertCount(6, $changes);
  }

}
