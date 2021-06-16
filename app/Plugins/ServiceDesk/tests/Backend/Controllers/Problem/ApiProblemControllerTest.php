<?php

namespace App\Plugins\ServiceDesk\tests\Backend\Controllers\Problem;

use Tests\AddOnTestCase;
use App\Plugins\ServiceDesk\Controllers\Problem\ApiProblemController;
use Illuminate\Support\Facades\Storage;
use App\Model\helpdesk\Ticket\Tickets;
use App\Plugins\ServiceDesk\Model\Problem\SdProblem;
use App\Plugins\ServiceDesk\Model\Changes\SdChanges;
use App\Plugins\ServiceDesk\Model\Common\CommonTicketRelation;
use App\Plugins\ServiceDesk\Model\Assets\CommonAssetRelation;
use App\Plugins\ServiceDesk\Model\Problem\ProblemChangeRelation;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use Illuminate\Http\UploadedFile;
use App\Model\helpdesk\Ticket\Ticket_Status as TicketType;
use App\Plugins\ServiceDesk\Model\Common\GeneralInfo;

/**
 * Tests ApiProblemController
 * 
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
*/
class ApiProblemControllerTest extends AddOnTestCase
{
  /** @group appendProblemToTimelineData */
  public function test_appendProblemToTimelineData_whenAProblemIsAssociated()
  {
    $ticketId = factory(Tickets::class)->create()->id;
    // assciate an problem with it
    $assocProblem = factory(SdProblem::class)->create();

    CommonTicketRelation::create(['ticket_id'=>$ticketId, 'type_id'=>$assocProblem->id, 'type'=>'sd_problem']);

    $initalTicketArray = ['id'=> $ticketId];

    (new ApiProblemController)->appendProblemToTimelineData($initalTicketArray);

    $this->assertArrayHasKeys(['id','associated_problem'], $initalTicketArray);

    $this->assertArrayHasKeys(['id','name','description'], $initalTicketArray['associated_problem']->toArray());

    $this->assertEquals($initalTicketArray['associated_problem']['id'], $assocProblem->id);
  }

  /** @group appendProblemToTimelineData */
  public function test_appendProblemToTimelineData_whenNoProblemIsAssociated()
  {
    $ticketId = factory(Tickets::class)->create()->id;
    // assciate an problem with it
    $assocProblem = SdProblem::create();

    $initalTicketArray = ['id'=> $ticketId];

    (new ApiProblemController)->appendProblemToTimelineData($initalTicketArray);

    $this->assertArrayHasKeys(['id','associated_problem'], $initalTicketArray);

    $this->assertNull($initalTicketArray['associated_problem']);
  }

  /** @group createUpdateProblem */
  public function test_createUpdateProblem_withFieldValidationSubjectMissing()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('POST', url('service-desk/api/problem'), [
                'requester_id' => $this->user->id,
                'department_id' => 1,
                'description' => 'Pen drive not working',
                'status_type_id' => 1,
                'priority_id' => 1,
                'impact_id' => 1,
                'location_id' => 1,
                'assigned_id' => 1,
                'identifier' => '',
              ]);
    $response->assertStatus(412);
  }

  /** @group createUpdateProblem */
  public function test_createUpdateProblem_withoutAsset()
  {
    $this->getLoggedInUserForWeb('admin');
    $initialCount = SdProblem::count();
    $response = $this->call('POST', url('service-desk/api/problem'), [
                'requester_id' => $this->user->id,
                'subject' => 'HP Pendrive',
                'department_id' => 1,
                'description' => 'Pen drive not working',
                'status_type_id' => 1,
                'priority_id' => 1,
                'impact_id' => 1,
                'location_id' => 1,
                'assigned_id' => 1,
                'identifier' => '',
              ]);
    $response->assertStatus(200);
    $this->assertEquals($initialCount+1, SdProblem::count());

  }

  /** @group createUpdateProblem */
  public function test_createUpdateProblem_withWrongTicket()
  {
    $this->getLoggedInUserForWeb('admin');
    $initialCount = SdProblem::count();
    $response = $this->call('POST', url('service-desk/api/problem'), [
                'requester_id' => $this->user->id,
                'subject' => 'HP Pendrive',
                'department_id' => 1,
                'description' => 'Pen drive not working',
                'status_type_id' => 1,
                'priority_id' => 1,
                'impact_id' => 1,
                'location_id' => 1,
                'assigned_id' => 1,
                'ticket_id' => 'wrong_ticket_id'
                ,'identifier' => '',
              ]);
    $response->assertStatus(412);
    $this->assertEquals($initialCount, SdProblem::count());
    $this->assertDatabaseMissing('sd_problem', ['requester_id' => $this->user->id, 'subject' => 'HP Pendrive']);
    $this->assertDatabaseMissing('sd_common_ticket_relation', ['ticket_id' => 'wrong_ticket_id', 'type' => 'sd_problem']);

  }

  /** @group createUpdateProblem */
  public function test_createUpdateProblem_withTicket()
  {
    $this->getLoggedInUserForWeb('admin');
    $initialCount = SdProblem::count();
    $ticketId = factory(Tickets::class)->create()->id;
    $this->assertDatabaseHas('tickets', ['id' => $ticketId]);
    $response = $this->call('POST', url('service-desk/api/problem'), [
                'requester_id' => $this->user->id,
                'subject' => 'HP Pendrive',
                'department_id' => 1,
                'description' => 'Pen drive not working',
                'status_type_id' => 1,
                'priority_id' => 1,
                'impact_id' => 1,
                'location_id' => 1,
                'assigned_id' => 1,
                'ticket_id' => $ticketId,
                'identifier' => '',
              ]);
    $response->assertStatus(200);
    $this->assertEquals($initialCount+1, SdProblem::count());
    $this->assertDatabaseHas('sd_problem', ['requester_id' => $this->user->id, 'subject' => 'HP Pendrive']);
    $this->assertDatabaseHas('sd_common_ticket_relation', ['ticket_id' => $ticketId, 'type' => 'sd_problem']);
  }

  /** @group createUpdateProblem */
  public function test_createUpdateProblem_withAsset()
  {
    $this->getLoggedInUserForWeb('admin');
    $initialCount = SdProblem::count();
    $assetId = SdAssets::create()->id;
    $response = $this->call('POST', url('service-desk/api/problem'), [
                'requester_id' => $this->user->id,
                'subject' => 'HP Pendrive',
                'department_id' => 1,
                'description' => 'Pen drive not working',
                'status_type_id' => 1,
                'priority_id' => 1,
                'impact_id' => 1,
                'location_id' => 1,
                'assigned_id' => 1,
                'asset_ids' => $assetId,
                'identifier' => '',
              ]);
    $response->assertStatus(200);
    $this->assertEquals($initialCount+1, SdProblem::count());
  }

  // /** @group createUpdateProblem */
  // public function test_createUpdateProblem_withAttachment()
  // {
  //   $this->getLoggedInUserForWeb('admin');
  //   Storage::fake('document.pdf');
  //   $initialCount = SdProblem::count();
  //   $assetId = SdAssets::create()->id;
  //   $response = $this->call('POST', url('service-desk/api/problem'), [
  //               'from' => $this->user->email,
  //               'subject' => 'HP Pendrive',
  //               'department_id' => 1,
  //               'description' => 'Pen drive not working',
  //               'status_type_id' => 1,
  //               'priority_id' => 1,
  //               'impact_id' => 1,
  //               'location_type_id' => 1,
  //               'assigned_id' => 1,
  //               'attachment' => UploadedFile::fake()->create('document.pdf', 20)
  //             ]);
  //   $response->assertStatus(200);
  //   $this->assertEquals('HP Pendrive', SdProblem::first()->subject);
  //   $this->assertEquals($initialCount+1, SdProblem::count());
  // }

  /** @group createUpdateProblem */
  public function test_createUpdateProblem_withUpdateSubject()
  {
    $this->getLoggedInUserForWeb('admin');
    $initialCount = SdProblem::count();
    $assetId = factory(SdAssets::class)->create()->id;
    $problemId = factory(SdProblem::class)->create()->id;
    $response = $this->call('POST', url('service-desk/api/problem'), [
                'id' => $problemId,
                'requester_id' => $this->user->id,
                'subject' => 'HP Pendrive',
                'department_id' => 1,
                'description' => 'Pen drive not working',
                'status_type_id' => 1,
                'priority_id' => 1,
                'impact_id' => 1,
                'location_id' => 1,
                'assigned_id' => 1,
                'asset_ids' => $assetId,
                'identifier' => '',
              ]);
    $problem = SdProblem::find($problemId);
    $response->assertStatus(200);
    $this->assertEquals('HP Pendrive', $problem->subject);
    $this->assertEquals($initialCount+1, SdProblem::count());
  }

  /** @group createUpdateProblem */
  public function test_createUpdateProblem_withValidationError()
  {
    $this->getLoggedInUserForWeb('admin');
    $initialCount = SdProblem::count();
    $response = $this->call('POST', url('service-desk/api/problem'), [
                'subject' => 'HP Pendrive',
                'department_id' => 1,
                'description' => 'Pen drive not working',
                'status_type_id' => 1,
                'priority_id' => 1,
                'impact_id' => 1,
                'location_id' => 1,
                'assigned_id' => 1,
                'identifier' => '',
              ]);

    $response->assertStatus(412);
    $this->assertNotEquals($initialCount+1, SdProblem::count());
  }

  /** @group editProblem */
  public function test_editProblem_withAsset()
  {
    $this->getLoggedInUserForWeb('admin');
    $assetId = SdAssets::create(['name' => 'asset'])->id;
    $this->call('POST', url('service-desk/api/problem'), [
                'requester_id' => $this->user->id,
                'subject' => 'HP Pendrive',
                'department_id' => 1,
                'description' => 'Pen drive not working',
                'status_type_id' => 1,
                'priority_id' => 1,
                'impact_id' => 1,
                'location_id' => 1,
                'assigned_id' => 1,
                'asset_ids' => $assetId,
                'identifier' => '',
              ]);

    $problemId = SdProblem::orderBy('id', 'desc')->first()->id;
    $response = $this->call('GET', url("service-desk/api/problem/$problemId"));
    $id = json_decode($response->content())->data->id;
    $response->assertStatus(200);
    $this->assertEquals($problemId, $id);
  }

  /** @group editProblem */
  public function test_editProblem_withMultipleAssets()
  {
    $this->getLoggedInUserForWeb('admin');
    $assetId1 = SdAssets::create(['name' => 'asset1', 'external_id' => 1])->id;
    $assetId2 = SdAssets::create(['name' => 'asset2', 'external_id' => 2])->id;
    $this->call('POST', url('service-desk/api/problem'), [
                'requester_id' => $this->user->id,
                'subject' => 'HP Pendrive',
                'department_id' => 1,
                'description' => 'Pen drive not working',
                'status_type_id' => 1,
                'priority_id' => 1,
                'impact_id' => 1,
                'location_id' => 1,
                'assigned_id' => 1,
                'asset_ids' => [$assetId1, $assetId2],
                'identifier' => '',
              ]);
    $problemId = SdProblem::orderBy('id', 'desc')->first()->id;
    $response = $this->call('GET', url("service-desk/api/problem/$problemId"));
    $id = json_decode($response->content())->data->id;
    $response->assertStatus(200);
    $this->assertEquals($problemId, $id);
  }

  /** @group editProblem */
  public function test_editProblem_withWrongProblemId()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url("service-desk/api/problem/wrong-problemId"));
    $response->assertStatus(404);
  }

  /** @group editProblem */
  public function test_editProblem_withFormat()
  {
    $this->getLoggedInUserForWeb('admin');
    $problemId = factory(SdProblem::class)->create()->id;
    $response = $this->call('GET', url("service-desk/api/problem/$problemId"));
    $problem = json_decode($response->content())->data;
    $response->assertStatus(200);
    $this->assertEquals($problem->id, $problemId);
    $this->assertCount(1, [$problem]);
  }

  /** @group getProblem */
  public function test_getProblem_withWrongProblemId()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url("service-desk/api/get-problem/wrong-problemId"));
    $response->assertStatus(404);
  }

  /** @group getAssets */
  public function test_getAssets_withWrongProblemId()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url("service-desk/api/problem-assets/wrong-problemId"));
    $response->assertStatus(404);
  }
  
  /** @group getTickets */
  public function test_getTickets_withWrongProblemId_returnsProblemNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url("service-desk/api/problem-tickets/wrong-problemId"));
    $response->assertStatus(404);
  }

  /** @group getTickets */
  public function test_getTickets_withFormat_returnsTicketsData()
  {
    $this->getLoggedInUserForWeb('admin');
    $ticketId = factory(Tickets::class)->create()->id;
    $problemId = factory(SdProblem::class)->create()->id;
    CommonTicketRelation::create(['ticket_id' => $ticketId, 'type_id' => $problemId, 'type' => 'sd_problem']);
    $this->assertDatabaseHas('sd_common_ticket_relation', ['ticket_id' => $ticketId, 'type_id' => $problemId, 'type' => 'sd_problem']);
    $response = $this->call('GET', url("service-desk/api/problem-tickets/$problemId"));
    $ticket = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, [$ticket]);
  }

  /** @group getChange */
  public function test_getChange_withWrongProblemId_returnsProblemNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url("service-desk/api/problem-change/wrong-problemId"));
    $response->assertStatus(404);
  }

  /** @group getTickets */
  public function test_getChange_returnsChangeData()
  {
    $this->getLoggedInUserForWeb('admin');
    $changeId = factory(SdChanges::class)->create()->id;
    $problemId = factory(SdProblem::class)->create()->id;
    ProblemChangeRelation::create(['change_id'=>$changeId, 'problem_id' => $problemId]);
    $this->assertDatabaseHas('sd_problem_change', ['change_id' => $changeId, 'problem_id' => $problemId]);
    $response = $this->call('GET', url("service-desk/api/problem-change/$problemId"));
    $change = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, [$change]);
  }

  /** @group detachTicket */
  public function test_detachTicket_withWrongProblemIdAndTicketId_returnsProblemNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('DELETE', url("service-desk/api/problem-detach-ticket/wrong-problemId/wrong-ticketId"));
    $response->assertStatus(404);
  }

  /** @group detachTicket */
  public function test_detachTicket_withProblemIdAndTicketId_returnsTicketDetachedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $ticketId = factory(Tickets::class)->create()->id;
    $problemId = factory(SdProblem::class)->create()->id;
    CommonTicketRelation::create(['ticket_id' => $ticketId, 'type_id' => $problemId, 'type' => 'sd_problem']);
    $this->assertDatabaseHas('sd_common_ticket_relation', ['ticket_id' => $ticketId, 'type_id' => $problemId, 'type' => 'sd_problem']);
    $response = $this->call('DELETE', url("service-desk/api/problem-detach-ticket/$problemId/$ticketId"));
    $response->assertStatus(200);
    $this->assertDatabaseMissing('sd_common_ticket_relation', ['ticket_id' => $ticketId, 'type_id' => $problemId, 'type' => 'sd_problem']);
  }

  /** @group attachTicket */
  public function test_attachTicket_withProblemIdAndTicketId_returnsTicketAttachedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $ticketId = factory(Tickets::class)->create()->id;
    $problemId = factory(SdProblem::class)->create()->id;
    $ticketIds = [$ticketId];
    $response = $this->call('POST', url("service-desk/api/problem-attach-ticket/$problemId"),
                            ['ticket_ids' =>$ticketIds]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_common_ticket_relation', ['ticket_id' => $ticketId, 'type_id' => $problemId, 'type' => 'sd_problem']);
  }

  /** @group attachTicket */
  public function test_attachTicket_withWrongProblemIdAndTicketId_returnsProblemNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $ticketId = factory(Tickets::class)->create()->id;
    $response = $this->call('POST', url("service-desk/api/problem-attach-ticket/wrong-problem-id"),
                    ['ticket_ids' => $ticketId]);
    $response->assertStatus(404);
  }

  /** @group attachTicket */
  public function test_attachTicket_withWrongProblemIdAndWrongTicketId_returnsTicketNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('POST', url("service-desk/api/problem-attach-ticket/wrong-problem-id"),
                  ['ticket_ids' => 'wrong_ticket_id']);
    $response->assertStatus(404);
  }

  /** @group detachAsset */
  public function test_detachAsset_withWrongProblemIdAndAssetId_returnsProblemNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('DELETE', url("service-desk/api/problem-detach-asset/wrong-problemId/wrong-assetId"));
    $response->assertStatus(404);
  }

  /** @group detachAsset */
  public function test_detachAsset_withProblemIdAndAssetId_returnsAssetDetachedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $assetId = factory(SdAssets::class)->create()->id;
    $problemId = factory(SdProblem::class)->create()->id;
    CommonAssetRelation::create(['asset_id' => $assetId, 'type_id' => $problemId, 'type' => 'sd_problem']);
    $this->assertDatabaseHas('sd_common_asset_relation', ['asset_id' => $assetId, 'type_id' => $problemId, 'type' => 'sd_problem']);
    $response = $this->call('DELETE', url("service-desk/api/problem-detach-asset/$problemId/$assetId"));
    $this->assertDatabaseMissing('sd_common_asset_relation', ['asset_id' => $assetId, 'type_id' => $problemId, 'type' => 'sd_problem']);
    $response->assertStatus(200);
  }

  /** @group deleteProblem */
  public function test_deleteProblem_withWrongProblemId_returnsProblemNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('DELETE', url("service-desk/api/problem-delete/wrong_problem_id"));
    $response->assertStatus(404);
  }

  /** @group deleteProblem */
  public function test_deleteProblem_withProblemId_returnsProblemDeletedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $problem_id = factory(SdProblem::class)->create()->id;
    $this->assertDatabaseHas('sd_problem', ['id' => $problem_id]);
    $response = $this->call('DELETE', url("service-desk/api/problem-delete/$problem_id"));
    $response->assertStatus(200);
    $this->assertDatabaseMissing('sd_problem', ['id' => $problem_id]);
  }

  /** @group deleteProblem */
  public function test_deleteProblem_withProblemIdAndAsset_returnsProblemDeletedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset_id = factory(SdAssets::class)->create()->id;
    $problem_id = factory(SdProblem::class)->create()->id;
    CommonAssetRelation::create(['asset_id' => $asset_id, 'type_id' => $problem_id, 'type' => 'sd_problem']);
    $this->assertDatabaseHas('sd_common_asset_relation', ['asset_id' => $asset_id, 'type_id' => $problem_id, 'type' => 'sd_problem']);
    $this->assertDatabaseHas('sd_problem', ['id' => $problem_id]);
    $response = $this->call('DELETE', url("service-desk/api/problem-delete/$problem_id"));
    $response->assertStatus(200);
    $this->assertDatabaseMissing('sd_problem', ['id' => $problem_id]);
    $this->assertDatabaseMissing('sd_common_asset_relation', ['asset_id' => $asset_id, 'type_id' => $problem_id, 'type' => 'sd_problem']);
  }

  /** @group deleteProblem */
  public function test_deleteProblem_withProblemAndAssetAndTicket_returnsProblemDeletedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset_id = factory(SdAssets::class)->create()->id;
    $problem_id = factory(SdProblem::class)->create()->id;
    $ticket_id = factory(Tickets::class)->create()->id;
    CommonTicketRelation::create(['ticket_id' => $ticket_id, 'type_id' => $problem_id, 'type' => 'sd_problem']);
    CommonAssetRelation::create(['asset_id' => $asset_id, 'type_id' => $problem_id, 'type' => 'sd_problem']);
    $this->assertDatabaseHas('sd_common_ticket_relation', ['ticket_id' => $ticket_id, 'type_id' => $problem_id, 'type' => 'sd_problem']);
    $this->assertDatabaseHas('sd_common_asset_relation', ['asset_id' => $asset_id, 'type_id' => $problem_id, 'type' => 'sd_problem']);
    $this->assertDatabaseHas('sd_problem', ['id' => $problem_id]);
    $response = $this->call('DELETE', url("service-desk/api/problem-delete/$problem_id"));
    $response->assertStatus(200);
    $this->assertDatabaseMissing('sd_problem', ['id' => $problem_id]);
    $this->assertDatabaseMissing('sd_common_asset_relation', ['asset_id' => $asset_id, 'type_id' => $problem_id, 'type' => 'sd_problem']);
    $this->assertDatabaseMissing('sd_common_ticket_relation', ['ticket_id' => $ticket_id, 'type_id' => $problem_id, 'type' => 'sd_problem']);
  }

  /** @group attachChange */
  public function test_attachChange_withProblemIdAndChangeId_returnsChangeAttachedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $changeId = factory(SdChanges::class)->create()->id;
    $problemId = factory(SdProblem::class)->create()->id;
    $response = $this->call('POST', url("service-desk/api/problem-attach-change/$problemId/$changeId"));
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_problem_change', ['change_id' => $changeId, 'problem_id' => $problemId,]);
  }

  /** @group attachChange */
  public function test_attachChange_withProblemIdAndWrongChangeId_returnsChangeNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $problemId = factory(SdProblem::class)->create()->id;
    $response = $this->call('POST', url("service-desk/api/problem-attach-change/$problemId/wrong-change-id"));
    $response->assertStatus(404);
  }

  /** @group attachChange */
  public function test_attachChange_withWrongProblemIdAndChangeId_returnsProblemNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $changeId = factory(SdChanges::class)->create()->id;
    $response = $this->call('POST', url("service-desk/api/problem-attach-change/wrong-problem-id/$changeId"));
    $response->assertStatus(404);
  }

  /** @group attachChange */
  public function test_attachChange_withWrongProblemIdAndWrongChangeId_returnsProblemNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('POST', url("service-desk/api/problem-attach-change/wrong-problem-id/wrong-change-id"));
    $response->assertStatus(404);
  }

  /** @group detachChange */
  public function test_detachChange_withWrongProblemIdAndWrongChangeId_returnsProblemNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('DELETE', url("service-desk/api/problem-detach-change/wrong-problemId/wrong-changeId"));
    $response->assertStatus(404);
  }

  /** @group detachChange */
  public function test_detachChange_withProblemIdAndChangeId_returnsChangeDetachedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $changeId = factory(SdChanges::class)->create()->id;
    $problemId = factory(SdProblem::class)->create()->id;
    ProblemChangeRelation::create(['change_id' => $changeId, 'problem_id' => $problemId]);
    $this->assertDatabaseHas('sd_problem_change', ['change_id' => $changeId, 'problem_id' => $problemId,]);
    $response = $this->call('DELETE', url("service-desk/api/problem-detach-change/$problemId/$changeId"));
    $this->assertDatabaseMissing('sd_problem_change', ['change_id' => $changeId, 'problem_id' => $problemId,]);
    $response->assertStatus(200);
  }

  /** @group updateProblemStatusToClose */
  public function test_updateProblemStatusToClose_withwrongproblemId_returnProblemNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('POST', url("service-desk/api/problem-close/wrong-change-id"));
    $response->assertStatus(404);
  }

  /** @group updateProblemStatusToClose */
  public function test_updateProblemStatusToClose_withProblemId_returnProblemSavedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $problemId = factory(SdProblem::class)->create()->id;
    $problemStatusId = TicketType::where('name', 'Closed')->first()->id;
    $this->assertDatabaseMissing('sd_problem', ['id' => $problemId, 'status_type_id' => $problemStatusId]);
    $response = $this->call('POST', url("service-desk/api/problem-close/$problemId"));
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_problem', ['id' => $problemId, 'status_type_id' => $problemStatusId]);
  }

  /** @group attachAssets */
  public function test_attachAssets_withProblemIdAndAssetId_returnsAssetAttachedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin');
    $assetId =  factory(SdAssets::class)->create()->id;
    $problemId = factory(SdProblem::class)->create()->id;
    $initialCount = SdProblem::count();
    $response = $this->call('POST', url("service-desk/api/problem-attach-assets/$problemId"), ['asset_ids' => [$assetId]]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_common_asset_relation', ['asset_id' => $assetId, 'type_id' => $problemId, 'type' => 'sd_problem']);
    $this->assertEquals($initialCount, SdProblem::count());
  }

  /** @group attachAssets */
  public function test_attachAssets_withwrongProblemIdAndAssetId_returnProblemNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $assetId =  factory(SdAssets::class)->create()->id;
    $response = $this->call('POST', url("service-desk/api/problem-attach-assets/wrong-problemId"), ['asset_ids' => [$assetId]]);
    $response->assertStatus(404);
  }

  /** @group updateProblemStatusToClose */
  public function test_updateProblemStatusToClose_withWrongProblemId_returnsProblemNotFound()
  {
    $this->getLoggedInUserForWeb('agent');
    $response = $this->call('POST', url("service-desk/api/problem-close/wrong-problem-id"));
    $response->assertStatus(404);
  }

  /** @group updateProblemStatusToClose */
  public function test_updateProblemStatusToClose_withProblemAndItsStatusSetToOpen_returnsProblemClosedSuccessfully()
  {
    $this->getLoggedInUserForWeb('agent');
    $openStatusId = 1;
    $closedStatusId = 3;
    $problemId = factory(SdProblem::class)->create(['status_type_id' => $openStatusId])->id;
    $response = $this->call('POST', url("service-desk/api/problem-close/{$problemId}"));
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_problem', ['id' => $problemId, 'status_type_id' => $closedStatusId]);
  }

  /** @group updateProblemStatusToClose */
  public function test_updateProblemStatusToClose_withProblemLinkedToTicketAndItsStatusSetToOpen_returnsProblemClosedSuccessfully()
  {
    $this->getLoggedInUserForWeb('admin'); 
    $openStatusId = 1;
    $closedStatusId = 3;
    $problem = factory(SdProblem::class)->create(['status_type_id' => $openStatusId]);
    $ticketId = factory(Tickets::class)->create()->id;
    $problem->attachTickets()->sync([$ticketId => ['type' => 'sd_problem']]);
    $response = $this->call('POST', url("service-desk/api/problem-close/{$problem->id}"));
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'status_type_id' => $closedStatusId]);
    $this->assertDatabaseHas('tickets', ['id' => $ticketId, 'status' => $closedStatusId]);
  }

  /** @group updateProblemStatusToClose */
  public function test_updateProblemStatusToClose_withProblemLinkedToTicketAndItsStatusSetToOpenAndAgentLoggedInAndWithoutTicketClosePermission_returnsProblemClosedSuccessfully()
  {
    $this->getLoggedInUserForWeb('agent');
    $openStatusId = 1;
    $closedStatusId = 3;
    $problem = factory(SdProblem::class)->create(['status_type_id' => $openStatusId]);
    $ticketId = factory(Tickets::class)->create()->id;
    $problem->attachTickets()->sync([$ticketId => ['type' => 'sd_problem']]);
    $response = $this->call('POST', url("service-desk/api/problem-close/{$problem->id}"));
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'status_type_id' => $closedStatusId]);
    $this->assertDatabaseMissing('tickets', ['id' => $ticketId, 'status' => $closedStatusId]);
  }

  /** @group updateProblemStatusToClose */
  public function test_updateProblemStatusToClose_withProblemLinkedToTicketAndItsStatusSetToOpenAndAgentLoggedInAndWithTicketClosePermission_returnsProblemClosedSuccessfully()
  {
    $this->getLoggedInUserForWeb('agent');
    $this->user->permision()->create(['user_id' => $this->user->id, 'permision' => ['close_ticket' => 1]]);
    $openStatusId = 1;
    $closedStatusId = 3;
    $problem = factory(SdProblem::class)->create(['status_type_id' => $openStatusId]);
    $ticketId = factory(Tickets::class)->create()->id;
    $problem->attachTickets()->sync([$ticketId => ['type' => 'sd_problem']]);
    $response = $this->call('POST', url("service-desk/api/problem-close/{$problem->id}"));
    $response->assertStatus(200);
    $this->assertDatabaseHas('sd_problem', ['id' => $problem->id, 'status_type_id' => $closedStatusId]);
    $this->assertDatabaseHas('tickets', ['id' => $ticketId, 'status' => $closedStatusId]);
  }

  /** @group planningPopups */
  public function test_planningPopups_WithProblemId_returnsPlanningPopupDataBasedOnProblemId()
  {
    $this->getLoggedInUserForWeb('admin');
    $problemId = factory(SdProblem::class)->create()->id;
    $owner = "sd_problem:{$problemId}";
    $key = 'root-cause';
    $description = 'proceed with another update';
    GeneralInfo::create(['owner' => $owner, 'key' => $key, 'value' => $description]);
    $response = $this->call('GET', url("service-desk/api/problem-planning/$problemId"));
    $response->assertStatus(200);
    $planningPopups = reset(json_decode($response->content())->data->planning_popups);
    $this->assertDatabaseHas('sd_gerneral', [
      'owner' => $owner,
      'key' => $key,
      'value' => $description
    ]);
  }

}
