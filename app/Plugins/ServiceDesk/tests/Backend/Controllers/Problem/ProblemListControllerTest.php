<?php

namespace App\Plugins\ServiceDesk\tests\Backend\Controllers\Problem;

use Tests\AddOnTestCase;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\Plugins\ServiceDesk\Model\Problem\ProblemChangeRelation;
use App\Plugins\ServiceDesk\Model\Changes\SdChanges;
use App\Plugins\ServiceDesk\Model\Assets\CommonAssetRelation;
use App\Plugins\ServiceDesk\Model\Common\CommonTicketRelation;
use App\Model\helpdesk\Ticket\Tickets;
use App\Plugins\ServiceDesk\Model\Problem\SdProblem;
use App\Traits\FaveoDateParser;
use App\User;

/**
 * Tests AssetListController
 * 
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
*/

class ProblemListControllerTest extends AddOnTestCase
{
  use FaveoDateParser;

  /** @group getProblemList */
  public function test_getProblemList_withLimit_returnsProblemListBasedOnLimit()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdProblem::class,3)->create();
    $limit = 2;
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['limit' => $limit]);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(2, $problems);
    $problem = reset($problems);
    $this->assertEquals("#PRB-{$problem->id}", $problem->problem_number);
    $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'subject' => $problem->subject]);
  }

  /** @group getProblemList */
  public function test_getProblemList_withNegativeLimitOrWrongLimit_returnsProblemList()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdProblem::class,3)->create();
    // default limit value is considered for negative or wrong limit value (default value = 10)
    $limit = -1;
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['limit' => $limit]);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(3, $problems);
    $this->assertDatabaseHas('sd_problem', ['id' => reset($problems)->id, 'subject' => reset($problems)->subject]);
  }

  /** @group getProblemList */
  public function test_getProblemList_withSort_returnsProblemListBasesOnSort()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdProblem::class,3)->create();
    $sortOrder = 'asc';
    $sortField = 'id';
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['sort-order' => $sortOrder, 'sort-field' => $sortField]);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(3, $problems);
    $this->assertDatabaseHas('sd_problem', ['id' => reset($problems)->id, 'subject' => reset($problems)->subject]);
    $this->assertTrue($problems[0]->id < $problems[1]->id);
    $this->assertTrue($problems[1]->id < $problems[2]->id);
  }
  
  /** @group getProblemList */
  public function test_getProblemList_withPage_returnsProblemListBasesOnPage()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdProblem::class,3)->create();
    $sortOrder = 'asc';
    $page = 2;
    $limit = 1;
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['sort-order' => $sortOrder, 'limit' => $limit, 'page' => $page]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->problems);
    $this->assertEquals($data->current_page, $page);
    $this->assertDatabaseHas('sd_problem', ['id' => reset($data->problems)->id, 'subject' => reset($data->problems)->subject]);
  }

  /** @group getProblemList */
  public function test_getProblemList_withWrongPage_returnsProblemListBasesOnPage()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdProblem::class,3)->create();
    $sortOrder = 'asc';
    $page = 2;
    $limit = 3;
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['sort-order' => $sortOrder, 'limit' => $limit, 'page' => $page]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(0, $data->problems);
    $this->assertEquals($data->current_page, $page);
  }

  /** @group getProblemList */
  public function test_getProblemList_withSearchQuerySubject_returnsProblemListBasesOnSubject()
  {
    $this->getLoggedInUserForWeb('admin');
    $problem = factory(SdProblem::class)->create(['subject' => 'Pendrive Not Working']);
    $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'status_type_id' => 1]);
    factory(SdProblem::class)->create(['subject' => 'Laptop Not Working']);
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['search-query' => $problem->subject]);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(1, $problems);
    $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'subject' => reset($problems)->subject]);
  }

  /** @group getProblemList */
  public function test_getProblemList_withSearchQueryDepartmentName_returnsProblemListBasesOnDepartmentName()
  {
    $this->getLoggedInUserForWeb('admin');
    // Sales is with department id 2
    $problem = factory(SdProblem::class)->create(['department_id' => 2]);
    $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'status_type_id' => 1]);
    factory(SdProblem::class)->create(['subject' => 'Laptop Not Working']);
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['search-query' => 'Sales']);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(1, $problems);
    $this->assertEquals(reset($problems)->department->name, 'Sales');
    $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'department_id' => reset($problems)->department->id]);
  }

  /** @group getProblemList */
  public function test_getProblemList_withSearchQueryImpactTypeName_returnsProblemListBasesOnImpactTypeName()
  {
    $this->getLoggedInUserForWeb('admin');
    // Medium is with impact id 2
    $problem = factory(SdProblem::class)->create(['impact_id' => 2]);
    $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'status_type_id' => 1]);
    factory(SdProblem::class)->create(['subject' => 'Laptop Not Working']);
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['search-query' => 'Medium']);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(1, $problems);
    $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'impact_id' => 2]);
  }

  /** @group getProblemList */
  public function test_getProblemList_withSearchQueryStatusName_returnsProblemListBasesOnStatusName()
  {
    $this->getLoggedInUserForWeb('admin');
    // Resolved is with status id 2
    $problem = factory(SdProblem::class)->create(['status_type_id' => 2]);
    $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'status_type_id' => 2]);
    factory(SdProblem::class)->create(['subject' => 'Laptop Not Working']);
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['search-query' => 'Resolved']);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(1, $problems);
    $this->assertEquals(reset($problems)->status->name, 'Resolved');
    $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'status_type_id' => reset($problems)->status->id]);
  }

  /** @group getProblemList */
  public function test_getProblemList_withSearchQueryLocationName_returnsProblemListBasesOnLocationName()
  {
    $this->getLoggedInUserForWeb('admin');
    // Delhi is with location id 2
    $problem = factory(SdProblem::class)->create(['location_id' => 2]);
    $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'location_id' => 2]);
    factory(SdProblem::class)->create(['subject' => 'Laptop Not Working']);
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['search-query' => 'Delhi']);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(1, $problems);
    $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'subject' => reset($problems)->subject]);
  }

  /** @group getProblemList */
  public function test_getProblemList_withAssetName_returnsProblemListBasesOnAssetName()
  {
    $this->getLoggedInUserForWeb('admin');
    $problemId = factory(SdProblem::class)->create()->id;
    $this->assertDatabaseHas('sd_problem', ['id' => $problemId]);
    factory(SdProblem::class)->create(['subject' => 'Laptop Not Working']);
    $asset = factory(SdAssets::class)->create();
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id]);
    CommonAssetRelation::create(['asset_id' => $asset->id, 'type_id' => $problemId, 'type' => 'sd_problem']);
    $this->assertDatabaseHas('sd_common_asset_relation', ['asset_id' => $asset->id, 'type_id' => $problemId, 'type' => 'sd_problem']);
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['search-query' => $asset->name]);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(1, $problems);
    $this->assertDatabaseHas('sd_problem', ['id' => $problemId, 'subject' => reset($problems)->subject]);
  }

  /** @group getProblemList */
  public function test_getProblemList_withSearchQueryPriorityName_returnsProblemListBasesOnPriorityName()
  {
    $this->getLoggedInUserForWeb('admin');
    // Normal is with priority id 2
    $problem = factory(SdProblem::class)->create(['priority_id' => 2]);
    $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'priority_id' => 2]);
    factory(SdProblem::class)->create(['subject' => 'Laptop Not Working']);
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['search-query' => 'Normal']);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(1, $problems);
    $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'subject' => reset($problems)->subject]);
  }

  /** @group getProblemList */
  public function test_getProblemList_withSearchQueryTicketNumber_returnsProblemListBasesOnTicketNumber()
  {
    $this->getLoggedInUserForWeb('admin');
    $ticket = factory(Tickets::class)->create();
    $this->assertDatabaseHas('tickets', ['id' => $ticket->id]);
    factory(SdProblem::class,2)->create();
    $problemId = factory(SdProblem::class)->create()->id;
    $this->assertDatabaseHas('sd_problem', ['id' => $problemId]);
    CommonTicketRelation::create(['ticket_id' => $ticket->id, 'type_id' => $problemId, 'type' => 'sd_problem']);
    $this->assertDatabaseHas('sd_common_ticket_relation', ['ticket_id' => $ticket->id, 'type_id' => $problemId, 'type' => 'sd_problem']);
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['search-query' => $ticket->ticket_number]);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(1, $problems);
    $this->assertDatabaseHas('sd_problem', ['id' => $problemId, 'subject' => reset($problems)->subject]);
  }

  /** @group getProblemList */
  public function test_getProblemList_withSearchQueryRequesterFirstName_returnsProblemListBasesOnRequesterFirstName()
  {
    $this->getLoggedInUserForWeb('admin');
    $problem = factory(SdProblem::class)->create(['requester_id' => $this->user->id]);
    $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'status_type_id' => 1]);
    factory(SdProblem::class)->create(['subject' => 'Laptop Not Working']);
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['search-query' => $this->user->first_name]);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(1, $problems);
    $this->assertEquals(reset($problems)->requester->id, $problem->requester_id);
    $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'requester_id' => reset($problems)->requester->id]);
  }

  /** @group getProblemList */
  public function test_getProblemList_withSearchQueryRequesterLastName_returnsProblemListBasesOnRequesterLastName()
  {
    $this->getLoggedInUserForWeb('admin');
    $problem = factory(SdProblem::class)->create(['requester_id' => $this->user->id]);
    $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'status_type_id' => 1]);
    factory(SdProblem::class)->create(['subject' => 'Laptop Not Working']);
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['search-query' => $this->user->last_name]);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(1, $problems);
    $this->assertEquals(reset($problems)->requester->id, $problem->requester_id);
    $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'requester_id' => reset($problems)->requester->id]);
  }

  /** @group getProblemList */
  public function test_getProblemList_withSearchQueryRequesterFullName_returnsProblemListBasesOnRequesterFullName()
  {
    $this->getLoggedInUserForWeb('admin');
    $problem = factory(SdProblem::class)->create(['requester_id' => $this->user->id]);
    $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'status_type_id' => 1]);
    factory(SdProblem::class)->create(['subject' => 'Laptop Not Working']);
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['search-query' => $this->user->first_name . ' ' . $this->user->last_name]);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(1, $problems);
    $this->assertEquals(reset($problems)->requester->id, $problem->requester_id);
    $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'requester_id' => reset($problems)->requester->id]);
  }

  /** @group getProblemList */
  public function test_getProblemList_withSearchQueryRequesterUserName_returnsProblemListBasesOnRequesterUserName()
  {
    $this->getLoggedInUserForWeb('admin');
    $problem = factory(SdProblem::class)->create(['requester_id' => $this->user->id]);
    $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'status_type_id' => 1]);
    factory(SdProblem::class)->create(['subject' => 'Laptop Not Working']);
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['search-query' => $this->user->user_name]);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(1, $problems);
    $this->assertEquals(reset($problems)->requester->id, $problem->requester_id);
    $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'requester_id' => reset($problems)->requester->id]);
  }

  /** @group getProblemList */
  public function test_getProblemList_withSearchQueryAssignedFirstName_returnsProblemListBasesOnAssignedToFirstName()
  {
    $this->getLoggedInUserForWeb('admin');
    $problem = factory(SdProblem::class)->create(['assigned_id' => $this->user->id]);
    $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'status_type_id' => 1]);
    factory(SdProblem::class)->create(['subject' => 'Laptop Not Working']);
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['search-query' => $this->user->first_name]);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(1, $problems);
    $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'requester_id' => reset($problems)->requester->id]);
  }

  /** @group getProblemList */
  public function test_getProblemList_withSearchQueryAssignedLastName_returnsProblemListBasesOnAssignedToLastName()
  {
    $this->getLoggedInUserForWeb('admin');
    $problem = factory(SdProblem::class)->create(['assigned_id' => $this->user->id]);
    $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'status_type_id' => 1]);
    factory(SdProblem::class)->create(['subject' => 'Laptop Not Working']);
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['search-query' => $this->user->last_name]);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(1, $problems);
    $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'requester_id' => reset($problems)->requester->id]);
  }

  /** @group getProblemList */
  public function test_getProblemList_withSearchQueryAssignedFullName_returnsProblemListBasesOnAssignedToFullName()
  {
    $this->getLoggedInUserForWeb('admin');
    $problem = factory(SdProblem::class)->create(['assigned_id' => $this->user->id]);
    $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'status_type_id' => 1]);
    factory(SdProblem::class)->create(['subject' => 'Laptop Not Working']);
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['search-query' => $this->user->first_name . ' ' . $this->user->last_name]);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(1, $problems);
    $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'requester_id' => reset($problems)->requester->id]);
  }

  /** @group getProblemList */
  public function test_getProblemList_withSearchQueryAssignedUserName_returnsProblemListBasesOnAssignedToUserName()
  {
    $this->getLoggedInUserForWeb('admin');
    $problem = factory(SdProblem::class)->create(['assigned_id' => $this->user->id]);
    $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'status_type_id' => 1]);
    factory(SdProblem::class)->create(['subject' => 'Laptop Not Working']);
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['search-query' => $this->user->user_name]);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(1, $problems);
    $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'requester_id' => reset($problems)->requester->id]);
  }

  /** @group getProblemList */
  public function test_getProblemList_withSearchQueryAssignedEmail_returnsProblemListBasesOnAssignedToEmail()
  {
    $this->getLoggedInUserForWeb('admin');
    $problem = factory(SdProblem::class)->create(['assigned_id' => $this->user->id]);
    $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'status_type_id' => 1]);
    factory(SdProblem::class)->create(['subject' => 'Laptop Not Working']);
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['search-query' => $this->user->email]);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(1, $problems);
    $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'assigned_id' => reset($problems)->assignedTo->id]);
    $this->assertDatabaseHas('users', ['id' => reset($problems)->assignedTo->id, 'email' => reset($problems)->assignedTo->email]);
  }

  /** @group getProblemList */
  public function test_getProblemList_withoutAnyParameterAndWithMultipleProblem_returnsProblemList()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdProblem::class,2)->create();
    $response = $this->call('GET', url('service-desk/api/problem-list'));
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(2, $problems);
    $this->assertDatabaseHas('sd_problem', ['id' => reset($problems)->id, 'subject' => reset($problems)->subject]);
  }

  /** @group getProblemList */
  public function test_getProblemList_withoutAnyParameterAndWithNoProblem_returnsEmptyProblemList()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url('service-desk/api/problem-list'));
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(0, $problems);
    $this->assertEmpty($problems);
  }

  /** @group getProblemList */
  public function test_getProblemList_withProblemId_returnsProblemListBasedOnProblemId()
  {
    $this->getLoggedInUserForWeb('admin');
    $problemId = factory(SdProblem::class)->create(['status_type_id' => 1])->id;
    $this->assertDatabaseHas('sd_problem', ['id' => $problemId, 'status_type_id' => 1]);
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['problem_ids' => $problemId]);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(1, $problems);
    $this->assertDatabaseHas('sd_problem', ['id' => $problemId, 'subject' => reset($problems)->subject]);
  }

  /** @group getProblemList */
  public function test_getProblemList_withWrongProblemId_returnsEmptyProblemList()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['problem_ids' => 'wrong-problem-id']);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(0, $problems);
    $this->assertEmpty($problems);
  }

  /** @group getProblemList */
  public function test_getProblemList_withDepartmentId_returnsProblemListBasedOnDepartmentId()
  {
    $this->getLoggedInUserForWeb('admin');
    $problemId = factory(SdProblem::class)->create(['department_id' => 1])->id;
    $this->assertDatabaseHas('sd_problem', ['id' => $problemId, 'department_id' => 1]);
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['dept_ids' => 1]);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(1, $problems);
    $this->assertDatabaseHas('sd_problem', ['id' => $problemId, 'subject' => reset($problems)->subject]);
  }

  /** @group getProblemList */
  public function test_getProblemList_withWrongDepartmentId_returnsEmptyProblemList()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['dept_ids' => 'wrong-department-id']);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(0, $problems);
    $this->assertEmpty($problems);
  }

  /** @group getProblemList */
  public function test_getProblemList_withImpactId_returnsProblemListBasedOnImpactId()
  {
    $this->getLoggedInUserForWeb('admin');
    $problemId = factory(SdProblem::class)->create(['impact_id' => 1])->id;
    $this->assertDatabaseHas('sd_problem', ['id' => $problemId, 'impact_id' => 1]);
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['impact_ids' => 1]);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(1, $problems);
    $this->assertDatabaseHas('sd_problem', ['id' => $problemId, 'subject' => reset($problems)->subject]);
  }

  /** @group getProblemList */
  public function test_getProblemList_withWrongImpactId_returnsEmptyProblemList()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['impact_ids' => 'wrong-impact-id']);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(0, $problems);
    $this->assertEmpty($problems);
  }

  /** @group getProblemList */
  public function test_getProblemList_withStatusId_returnsProblemListBasedOnStatusId()
  {
    $this->getLoggedInUserForWeb('admin');
    $problemId = factory(SdProblem::class)->create(['status_type_id' => 1])->id;
    $this->assertDatabaseHas('sd_problem', ['id' => $problemId, 'status_type_id' => 1]);
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['status_ids' => 1]);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(1, $problems);
    $this->assertDatabaseHas('sd_problem', ['id' => $problemId, 'status_type_id' => reset($problems)->status->id]);
  }

  /** @group getProblemList */
  public function test_getProblemList_withWrongStatusId_returnsEmptyProblemList()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['status_ids' => 'wrong-status-id']);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(0, $problems);
    $this->assertEmpty($problems);
  }

  /** @group getProblemList */
  public function test_getProblemList_withLocationId_returnsProblemListBasedOnLocationId()
  {
    $this->getLoggedInUserForWeb('admin');
    $problemId = factory(SdProblem::class)->create(['location_id' => 1])->id;
    $this->assertDatabaseHas('sd_problem', ['id' => $problemId, 'location_id' => 1]);
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['location_ids' => 1]);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(1, $problems);
    $this->assertDatabaseHas('sd_problem', ['id' => $problemId, 'subject' => reset($problems)->subject]);
  }

  /** @group getProblemList */
  public function test_getProblemList_withWrongLocationId_returnsEmptyProblemList()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['location_ids' => 'wrong-location-id']);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(0, $problems);
    $this->assertEmpty($problems);
  }

  /** @group getProblemList */
  public function test_getProblemList_withPriorityId__returnsProblemListBasedOnPriorityId()
  {
    $this->getLoggedInUserForWeb('admin');
    $problemId = factory(SdProblem::class)->create(['priority_id' => 1])->id;
    $this->assertDatabaseHas('sd_problem', ['id' => $problemId, 'priority_id' => 1]);
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['priority_ids' => 1]);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(1, $problems);
    $this->assertDatabaseHas('sd_problem', ['id' => $problemId, 'subject' => reset($problems)->subject]);
  }

  /** @group getProblemList */
  public function test_getProblemList_withWrongPriorityId_returnsEmptyProblemList()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['priority_ids' => 'wrong-priority-id']);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(0, $problems);
    $this->assertEmpty($problems);
  }

  /** @group getProblemList */
  public function test_getProblemList_withAssignedId_returnsProblemListBasedOnAssignedId()
  {
    $this->getLoggedInUserForWeb('admin');
    $problemId = factory(SdProblem::class)->create(['assigned_id' => $this->user->id])->id;
    $this->assertDatabaseHas('sd_problem', ['id' => $problemId, 'assigned_id' => $this->user->id]);
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['assigned_ids' => $this->user->id]);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(1, $problems);
    $this->assertDatabaseHas('sd_problem', ['id' => $problemId, 'subject' => reset($problems)->subject]);
  }

  /** @group getProblemList */
  public function test_getProblemList_withWrongAssignedId_returnsEmptyProblemList()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['assigned_ids' => 'wrong-assigned-id']);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(0, $problems);
    $this->assertEmpty($problems);
  }

  /** @group getProblemList */
  public function test_getProblemList_withAssetId_returnsProblemListBasedOnAssetId()
  {
    $this->getLoggedInUserForWeb('admin');
    $problemId = factory(SdProblem::class)->create()->id;
    $this->assertDatabaseHas('sd_problem', ['id' => $problemId]);
    $assetId = factory(SdAssets::class)->create()->id;
    $this->assertDatabaseHas('sd_assets', ['id' => $assetId]);
    CommonAssetRelation::create(['asset_id' => $assetId, 'type_id' => $problemId, 'type' => 'sd_problem']);
    $this->assertDatabaseHas('sd_common_asset_relation', ['asset_id' => $assetId, 'type_id' => $problemId, 'type' => 'sd_problem']);
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['asset_ids' => $assetId]);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(1, $problems);
    $this->assertDatabaseHas('sd_problem', ['id' => $problemId, 'subject' => reset($problems)->subject]);
  }

  /** @group getProblemList */
  public function test_getProblemList_withWrongAssetId_returnsEmptyProblemList()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['asset_ids' => 'wrong-asset-id']);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(0, $problems);
    $this->assertEmpty($problems);
  }

  /** @group getProblemList */
  public function test_getProblemList_withMetaFalse_returnsProblemList()
  {
    $this->getLoggedInUserForWeb('admin');
    $problemId = factory(SdProblem::class)->create(['subject' => 'Pendrive Not Working'])->id;
    $this->assertDatabaseHas('sd_problem', ['id' => $problemId, 'subject' => 'Pendrive Not Working']);
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['meta' => false]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->problems);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertDatabaseHas('sd_problem', ['id' => $problemId, 'subject' => reset($data->problems)->subject]);
  }

  /** @group getProblemList */
  public function test_getProblemList_withFormatFalse_returnsProblemList()
  {
    $this->getLoggedInUserForWeb('admin');
    $problemId = factory(SdProblem::class)->create(['subject' => 'Pendrive Not Working'])->id;
    $this->assertDatabaseHas('sd_problem', ['id' => $problemId, 'status_type_id' => 1]);
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['format' => false]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->problems);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertDatabaseHas('sd_problem', ['id' => $problemId, 'subject' => reset($data->problems)->subject]);
  }

  /** @group getProblemList */
  public function test_getProblemList_withFormatWrongSearchQueryId_returnsEmptyProblemList()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['format' => true, 'search-query' => 'wrong-searchQuery-id']);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(0, $problems);
    $this->assertEmpty($problems);
  }

  /** @group getProblemList */
  public function test_getProblemList_withFormatWrongSearchStatus_returnsEmptyProblemList()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['format' => true, 'search-query' => 'wrong-searchQuery-status']);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(0, $problems);
    $this->assertEmpty($problems);
  }

  /** @group getProblemList */
  public function test_getProblemList_withFormatWrongSearchQuerySubject_returnsEmptyProblemList()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['format' => true, 'search-query' => 'wrong-searchQuery-subject']);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(0, $problems);
    $this->assertEmpty($problems);
  }

  /** @group getProblemList */
  public function test_getProblemList_withTicketId_returnsProblemListBasedOnTicketId()
  {
    $this->getLoggedInUserForWeb('admin');
    $ticketId = factory(Tickets::class)->create()->id;
    $this->assertDatabaseHas('tickets', ['id' => $ticketId]);
    factory(SdProblem::class,2)->create();
    $problemId = factory(SdProblem::class)->create()->id;
    $this->assertDatabaseHas('sd_problem', ['id' => $problemId]);
    CommonTicketRelation::create(['ticket_id' => $ticketId, 'type_id' => $problemId, 'type' => 'sd_problem']);
    $this->assertDatabaseHas('sd_common_ticket_relation', ['ticket_id' => $ticketId, 'type_id' => $problemId, 'type' => 'sd_problem']);
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['ticket_ids' => $ticketId]);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(1, $problems);
    $this->assertDatabaseHas('sd_problem', ['id' => $problemId, 'subject' => reset($problems)->subject]);
  }

  /** @group getProblemList */
  public function test_getProblemList_withWrongTicketId_returnsEmptyProblemList()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['ticket_ids' => 'wrong-ticket-id']);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(0, $problems);
    $this->assertEmpty($problems);
  }

  /** @group getProblemList */
  public function test_getProblemList_withRequesterId_returnsProblemListBasedOnRequesterId()
  {
    $this->getLoggedInUserForWeb('admin');
    $problemId = factory(SdProblem::class)->create(['requester_id' => $this->user->id])->id;
    $this->assertDatabaseHas('sd_problem', ['id' => $problemId, 'requester_id' => $this->user->id]);
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['requester_ids' => $this->user->id]);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(1, $problems);
    $this->assertDatabaseHas('sd_problem', ['id' => $problemId, 'subject' => reset($problems)->subject]);
  }

  /** @group getProblemList */
  public function test_getProblemList_withWrongRequesterId_returnsEmptyProblemList()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['format' => true, 'search-query' => 'wrong-from-id']);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(0, $problems);
    $this->assertEmpty($problems);
  }

  /** @group getProblemList */
  public function test_getProblemList_withChangeId_returnsProblemListBasedOnChangeId()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdProblem::class,5)->create();
    $problemId = factory(SdProblem::class)->create()->id;
    $this->assertDatabaseHas('sd_problem', ['id' => $problemId]);
    $changeId = factory(SdChanges::class)->create()->id;
    $this->assertDatabaseHas('sd_changes', ['id' => $changeId]);
    ProblemChangeRelation::create(['change_id' => $changeId, 'problem_id' => $problemId]);
    $this->assertDatabaseHas('sd_problem_change', ['change_id' => $changeId, 'problem_id' => $problemId]);
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['change_ids' => $changeId]);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(1, $problems);
    $this->assertDatabaseHas('sd_problem', ['id' => $problemId, 'subject' => reset($problems)->subject]);
  }

  /** @group getProblemList */
  public function test_getProblemList_withWrongChangeId_returnsEmptyProblemList()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['change_ids' => 'wrong-change-id']);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount(0, $problems);
    $this->assertEmpty($problems);
  }

    /* @group getProblemList */
  public function test_getProblemList_withProblemCreatedAtTimeRange_returnsProblemListBasedOnCreatedAtTimeRange()
  {
    $this->getLoggedInUserForWeb('agent');
    factory(SdProblem::class)->create(['created_at' => '2020-07-12 05:12:00']);
    factory(SdProblem::class)->create(['created_at' => '2020-07-14 05:12:00']);
    factory(SdProblem::class)->create(['created_at' => '2020-07-16 05:12:00']);
    $endTimestamp = '2020-07-16 05:13:00';
    $startTimestamp = '2020-07-12 05:12:00';
    $startTime = changeTimezoneForDatetime($startTimestamp, agentTimeZone(), 'UTC');
    $endTime = changeTimezoneForDatetime($endTimestamp, agentTimeZone(), 'UTC');
    $timeRange = "date::{$startTimestamp}~{$endTimestamp}";
    $formattedRange = $this->getTimeRangeObject($timeRange, "UTC");
    $initialCount = SdProblem::where([['created_at', '<=', $endTime],['created_at', '>=', $startTime]])->count();
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['created_at' => $timeRange]);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount($initialCount, $problems);
    foreach ($problems as $problem) {
      $createdAt = SdProblem::find($problem->id)->created_at;
      $this->assertGreaterThanOrEqual($startTime, $createdAt);
      $this->assertLessThanOrEqual($endTime, $createdAt);
      $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'subject' => $problem->subject]);
    }
  }

   /* @group getProblemList */
  public function test_getProblemList_withProblemUpdatedAtTimeRange_returnsProblemListBasedOnUpdatedAtTimeRange()
  {
    $this->getLoggedInUserForWeb('agent');
    factory(SdProblem::class)->create(['updated_at' => '2020-07-12 05:12:00']);
    factory(SdProblem::class)->create(['updated_at' => '2020-07-14 05:12:00']);
    factory(SdProblem::class)->create(['updated_at' => '2020-07-16 05:12:00']);
    factory(SdProblem::class)->create(['updated_at' => '2020-07-18 05:12:00']);
    $endTimestamp = '2020-07-16 05:12:00';
    $startTimestamp = '2020-07-12 05:12:00';
    $startTime = changeTimezoneForDatetime($startTimestamp, agentTimeZone(), 'UTC');
    $endTime = changeTimezoneForDatetime($endTimestamp, agentTimeZone(), 'UTC');
    $timeRange = "date::{$startTimestamp}~{$endTimestamp}";
    $formattedRange = $this->getTimeRangeObject($timeRange, "UTC");
    $initialCount = SdProblem::where([['updated_at', '<=', $endTime],['updated_at', '>=', $startTime]])->count();
    $response = $this->call('GET', url('service-desk/api/problem-list'), ['updated_at' => $timeRange]);
    $problems = json_decode($response->content())->data->problems;
    $response->status(200);
    $this->assertCount($initialCount, $problems);
    foreach ($problems as $problem) {
      $updatedAt = SdProblem::find($problem->id)->updated_at;
      $this->assertGreaterThanOrEqual($startTime, $updatedAt);
      $this->assertLessThanOrEqual($endTime, $updatedAt);
      $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'subject' => $problem->subject]);
    }
  }
}