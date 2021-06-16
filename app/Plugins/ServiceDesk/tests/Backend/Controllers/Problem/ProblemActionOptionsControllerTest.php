<?php

namespace App\Plugins\ServiceDesk\tests\Backend\Controllers\Problem;

use Tests\AddOnTestCase;
use App\Model\helpdesk\Ticket\Tickets;
use App\Plugins\ServiceDesk\Model\Problem\SdProblem;
use App\Plugins\ServiceDesk\Model\Changes\SdChanges;
use App\Plugins\ServiceDesk\Model\Common\CommonTicketRelation;
use App\Plugins\ServiceDesk\Model\Assets\CommonAssetRelation;
use App\Plugins\ServiceDesk\Model\Problem\ProblemChangeRelation;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\Plugins\ServiceDesk\Model\Common\GeneralInfo;

/**
 * Tests ProblemActionOptionsController
 * 
*/
class ProblemActionOptionsControllerTest extends AddOnTestCase
{

  /** @group getProblemActionList */
  public function test_getProblemActionList_returnsActionsAllowed()
  {
    $this->getLoggedInUserForWeb('admin');
    $problemId = factory(SdProblem::class)->create()->id;
    $response = $this->call('GET', url("service-desk/api/problem-action/$problemId"));
    $actions = json_decode($response->content())->data->actions;
    $response->assertStatus(200);
    $this->assertEquals(true,$actions->change_create);
    $this->assertEquals(true,$actions->problem_view);
    $this->assertEquals(true,$actions->problem_edit);
    $this->assertEquals(true,$actions->problem_delete);
    $this->assertEquals(true,$actions->problem_close);
    $this->assertEquals(true,$actions->problem_change_attach);
    $this->assertEquals(false,$actions->problem_change_detach);
    $this->assertEquals(true,$actions->problem_asset_attach);
    $this->assertEquals(true,$actions->problem_asset_detach);
    $this->assertEquals(false,$actions->associated_asset);
    $this->assertEquals(false,$actions->associated_ticket);
    $this->assertEquals(false,$actions->associated_change);
  }

  /** @group getProblemActionList */
  public function test_getProblemActionList_withAssetAttached_returnsActionsAllowedWithAssociatedAssetTrue()
  {
    $this->getLoggedInUserForWeb('admin');
    $problemId = factory(SdProblem::class)->create()->id;
    $assetId = factory(SdAssets::class)->create()->id;
    CommonAssetRelation::create(['asset_id' => $assetId, 'type_id' => $problemId, 'type' => 'sd_problem']);
    $response = $this->call('GET', url("service-desk/api/problem-action/$problemId"));
    $actions = json_decode($response->content())->data->actions;
    $response->assertStatus(200);
    $this->assertEquals(false,$actions->associated_ticket);
    $this->assertEquals(false,$actions->associated_change);
    $this->assertEquals(true,$actions->associated_asset);
  }

  /** @group getProblemActionList */
  public function test_getProblemActionList_withTicketAttached_returnsActionsAllowedWithAssociatedTicketTrue()
  {
    $this->getLoggedInUserForWeb('admin');
    $problemId = factory(SdProblem::class)->create()->id;
    $ticketId = factory(Tickets::class)->create()->id;
    CommonTicketRelation::create(['ticket_id' => $ticketId, 'type_id' => $problemId, 'type' => 'sd_problem']);
    $response = $this->call('GET', url("service-desk/api/problem-action/$problemId"));
    $actions = json_decode($response->content())->data->actions;
    $response->assertStatus(200);
    $this->assertEquals(true,$actions->associated_ticket);
    $this->assertEquals(false,$actions->associated_change);
    $this->assertEquals(false,$actions->associated_asset);
  }

  /** @group getProblemActionList */
  public function test_getProblemActionList_withChangeAttached_returnsActionsAllowedWithAssociatedChangeTrue()
  {
    $this->getLoggedInUserForWeb('admin');
    $problemId = factory(SdProblem::class)->create()->id;
    $changeId = factory(SdChanges::class)->create()->id;
    ProblemChangeRelation::create(['change_id' => $changeId, 'problem_id' => $problemId]);
    $response = $this->call('GET', url("service-desk/api/problem-action/$problemId"));
    $actions = json_decode($response->content())->data->actions;
    $response->assertStatus(200);
    $this->assertEquals(false,$actions->associated_ticket);
    $this->assertEquals(true,$actions->associated_change);
    $this->assertEquals(false,$actions->associated_asset);
    $this->assertEquals(false,$actions->problem_change_attach);
    $this->assertEquals(true,$actions->problem_change_detach);
  }

  /** @group getProblemActionList */
  public function test_getProblemActionList_PlanningData_returnsActionsAllowedWithCheckPLanningTrue()
  {
    $this->getLoggedInUserForWeb('admin');
    $problemId = factory(SdProblem::class)->create()->id;
    $owner = "sd_problem:{$problemId}";
    $key = 'root-cause';
    $description = 'proceed with another update';
    GeneralInfo::create(['owner' => $owner, 'key' => $key, 'value' => $description]);
    $response = $this->call('GET', url("service-desk/api/problem-action/$problemId"));
    $actions = json_decode($response->content())->data->actions;
    $response->assertStatus(200);
    
    $this->assertEquals(true,$actions->check_planning);   
  }

}
