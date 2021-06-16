<?php

namespace App\Plugins\ServiceDesk\tests\Backend\Controllers\Cab;

use App\Model\helpdesk\Manage\UserType;
use App\Model\helpdesk\Workflow\ApprovalWorkflow;
use App\User;
use Tests\AddOnTestCase;
use App\Model\helpdesk\Workflow\ApprovalLevel;
use App\Model\helpdesk\Workflow\ApprovalLevelStatus;
use App\Model\helpdesk\Agent\Department;
use App\Http\Controllers\Agent\helpdesk\TicketsWrite\ApiApproverController;
use App\Plugins\ServiceDesk\Model\Changes\SdChanges;
use App\Plugins\ServiceDesk\Model\Cab\SdApprovalWorkflowChange;
use App\Plugins\ServiceDesk\Model\Cab\SdApprovalLevelStatus;
use App\Plugins\ServiceDesk\Model\Cab\SdApproverStatus;
use Queue;
use App\Jobs\SendEmail;


class ApiApproverControllerTest extends AddOnTestCase
{

  private $apiApproverController;

  public function setUp():void
  {
      parent::setUp();
      $this->apiApproverController = new ApiApproverController(new SdChanges);
  }

  /**
   * Creates workflow with one level and link $agentIds and $departmentIds with it
   * @param  array  $agentIds
   * @param  array  $departmentIds
   * @return int    workflowId
   */
  private function createWorkflowWithLevel(array $agentIds = [], array $userTypeIds = [])
  {
    $approvalWorkflowId = ApprovalWorkflow::create(['name'=>'test_workflow', 'user_id' => $this->user->id])->id;
    $this->createLevel($agentIds, $userTypeIds, 1, $approvalWorkflowId);
    $this->createLevel($agentIds, $userTypeIds, 2, $approvalWorkflowId);
    return $approvalWorkflowId;
  }

  /**
   * creates level and sync approvers with it
   * @param  array $agentIds
   * @param  array $userTypeIds
   * @param  int $order
   * @param  int $approvalWorkflowId
   * @return null
   */
  private function createLevel($agentIds, $userTypeIds, $order, $approvalWorkflowId){
    $level = ApprovalLevel::create(['name'=>'test_level_'.$order,'approval_workflow_id' => $approvalWorkflowId,'order'=>$order]);
    $level->approveUsers()->sync($agentIds);
    $level->approveUserTypes()->sync($userTypeIds);
  }

  /** @group applyApprovalWorkflow */
  public function test_applyApprovalWorkflow_forSuccessforCab()
  {
    $this->getLoggedInUserForWeb('admin');
    $changeId = factory(SdChanges::class)->create()->id;

    $approvalWorkflowId = $this->createWorkflowWithLevel([1],[4,5]);
    
  $response = $this->call('POST',url('service-desk/api/apply-cab-approval'), ['change_id' => $changeId, 'workflow_id' => $approvalWorkflowId]);
    //check two approval_level_statuses getting created
    $response->assertStatus(200);
  }

  /** @group applyApprovalWorkflow */
  public function test_applyApprovalWorkflow_forMutipleLevelsWithFirstLevelAsActiveForCab()
  {
    $this->getLoggedInUserForWeb('admin');
    $changeId = factory(SdChanges::class)->create()->id;

    $approvalWorkflowId = $this->createWorkflowWithLevel([1],[4,5]);

    $response = $this->call('POST',url('service-desk/api/apply-cab-approval'),
        ['change_id'=> $changeId, 'workflow_id'=>$approvalWorkflowId]);

    //check two approval_level_statuses getting created
    $response->assertStatus(200);

    $approvalWorkflowChanges = SdApprovalWorkflowChange::get();
    $this->assertCount(1,$approvalWorkflowChanges);
    $approvalStatusLevels = $approvalWorkflowChanges[0]->approvalLevels()->get();
    $this->assertCount(2,$approvalStatusLevels);
    $this->assertTrue((bool)$approvalStatusLevels[0]->is_active);
    $this->assertFalse((bool)$approvalStatusLevels[1]->is_active);
  }

  /** @group applyApprovalWorkflow */
  public function test_applyApprovalWorkflow_forMutipleApproversForCab()
  {
    $this->getLoggedInUserForWeb('admin');
    $changeId = factory(SdChanges::class)->create()->id;

    $approvalWorkflowId = $this->createWorkflowWithLevel([1],[4,5]);

    $response = $this->call('POST',url('service-desk/api/apply-cab-approval'),
        ['change_id'=> $changeId, 'workflow_id'=>$approvalWorkflowId]);

    $approvalWorkflowChange = SdApprovalWorkflowChange::first();
    $approvalStatusLevels = $approvalWorkflowChange->approvalLevels()->get();
    $this->assertEquals($approvalStatusLevels[0]->approverStatuses()->count(), 3);
    $this->assertEquals($approvalStatusLevels[1]->approverStatuses()->count(), 3);
  }

  /** @group applyApprovalWorkflow */
  public function test_applyApprovalWorkflow_mailGetsSentToTheApproverForCab()
  {
    $user = factory(User::class)->create();

    //creating mail in first department so that mails can be tested
    $change = factory(SdChanges::class)->create();

    $this->getLoggedInUserForWeb('admin');

    $approvalWorkflowId = $this->createWorkflowWithLevel([$user->id],[4,5]);

    Queue::fake();

    $this->setUpOutgoingMail();

    $response = $this->call('POST',url('service-desk/api/apply-cab-approval'),
        ['change_id'=> $change->id, 'workflow_id'=>$approvalWorkflowId]);

    Queue::assertPushed(SendEmail::class, function($job) use ($user, $change){
      // $template = $this->getPrivateProperty($job,'template');
      // $this->assertEquals($user->full_name, $template['receiver_name']);
      // $this->assertEquals($ticket->ticket_number, $template['ticket_number']);
      return $job;
    });
  }

  /** @group getConversationByHash */
  public function test_getConversationByHash_forInvalidHashForCab()
  {
    $this->getLoggedInUserForWeb('agent'); 
    $hash = $this->getPrivateMethod($this->apiApproverController,'encryptHashWithEmail',['test_email','invalid_hash']);

    $response = $this->call('GET',url('service-desk/api/change-conversation/'.$hash));


    $response->assertStatus(404);
  }

  /** @group getConversationByHash */
  public function test_getConversationByHash_forExpiredHashWhenLevelIsApprovedAndApproverHasApprovedForCab()
  {
    $this->getLoggedInUserForWeb('agent'); 
    $hash = 'invalid_hash';

    \DB::statement('SET FOREIGN_KEY_CHECKS=0');

    $levelId = SdApprovalLevelStatus::create(['name'=>'test_level','status'=>'APPROVED'])->id;

    SdApproverStatus::create(['status'=>'APPROVED', 'hash'=>$hash, 'approval_level_status_id'=>$levelId]);

    $hash = $this->getPrivateMethod($this->apiApproverController,'encryptHashWithEmail',['test_email', $hash]);

    $response = $this->call('GET',url('service-desk/api/change-conversation/'.$hash));
    $response->assertStatus(404);
  }

  /** @group getConversationByHash */
  public function test_getConversationByHash_forExpiredHashWhenLevelIsApprovedAndApproverHasNotApprovedForCab()
  {
    $this->getLoggedInUserForWeb('agent'); 
    $hash = 'invalid_hash';

    \DB::statement('SET FOREIGN_KEY_CHECKS=0');

    $levelId = SdApprovalLevelStatus::create(['name'=>'test_level','status'=>'APPROVED'])->id;

    SdApproverStatus::create(['status'=>'PENDING', 'hash'=>$hash, 'approval_level_status_id'=>$levelId]);

    $hash = $this->getPrivateMethod($this->apiApproverController,'encryptHashWithEmail',['test_email', $hash]);

    $response = $this->call('GET',url('service-desk/api/change-conversation/'.$hash));
    $response->assertStatus(404);
  }

  /** @group getConversationByHash */
  public function test_getConversationByHash_forValidAndNotExpiredHashForCab()
  {
    $hash = 'valid_hash';

    \DB::statement('SET FOREIGN_KEY_CHECKS=0');

    $changeId = factory(SdChanges::class)->create()->id;

    $userId = factory(User::class)->create()->id;

    $approvalWorkflowId = SdApprovalWorkflowChange::create(['approval_workflow_id'=>1,'change_id'=>$changeId])->id;

    $level = SdApprovalLevelStatus::create(['approval_workflow_change_id'=>$approvalWorkflowId, 'is_active'=>true, 'status'=>'PENDING']);

    $level->approveUsers()->sync([$userId => ['hash'=>$hash, 'status'=>'PENDING']]);

    $hash = $this->getPrivateMethod($this->apiApproverController,'encryptHashWithEmail',['test_email', $hash]);

    $response = $this->call('GET',url('service-desk/api/change-conversation/'.$hash));
  
    $response->assertStatus(200);

    $change = json_decode($response->content())->data->change;
    $this->assertEquals($changeId, $change->id);
  }

  /** @group getAppovalStatus */
  public function test_getAppovalStatus_forWrongChangeIdForCab()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET',url('service-desk/api/change-approval-status/wrong_change_id'));
    $response->assertStatus(400);
  }

  /** @group getAppovalStatus */
  public function test_getAppovalStatus_forCorrectChangeWithWorkflowForCab()
  {
    $this->getLoggedInUserForWeb('admin');

    $changeId = factory(SdChanges::class)->create()->id;

    $approvalWorkflowId = $this->createWorkflowWithLevel([1],[4,5]);

    $response = $this->call('POST',url('service-desk/api/apply-cab-approval'),
        ['change_id'=> $changeId, 'workflow_id'=>$approvalWorkflowId]);

    $response = $this->call('GET',url('service-desk/api/change-approval-status/'.$changeId));
    $response->assertStatus(200);
  }

  /** @group approvalActionByChangeId */
  public function test_approvalActionByChangeId_forApproveActionTypeForApproverAsAgentForCab()
  {
    $change = factory(SdChanges::class)->create();
    $this->getLoggedInUserForWeb('admin');
    $approvalWorkflowId = $this->createWorkflowWithLevel([$this->user->id],[4,5]);
    $response = $this->call('POST',url('service-desk/api/apply-cab-approval'),
        ['change_id'=> $change->id, 'workflow_id'=>$approvalWorkflowId]);
    $response = $this->call('POST',url('service-desk/api/approval-action-without-hash/'.$change->id),['action_type'=>'approve']);
    $response->assertStatus(200);
    $approverStatus = SdApproverStatus::where('approver_id',$this->user->id)->where('approver_type','App\User')->first();
    $this->assertEquals($approverStatus->status,'APPROVED');
  }

  /** @group approvalActionByChangeId */
  public function test_approvalActionByChangeId_forDenyActionTypeForApproverAsAgentForCab()
  {
    $change = factory(SdChanges::class)->create();
    $this->getLoggedInUserForWeb('admin');
    $approvalWorkflowId = $this->createWorkflowWithLevel([$this->user->id],[4,5]);
    $response = $this->call('POST',url('service-desk/api/apply-cab-approval'),
        ['change_id'=> $change->id, 'workflow_id'=>$approvalWorkflowId]);
    $response = $this->call('POST',url('service-desk/api/approval-action-without-hash/'.$change->id),['action_type'=>'deny']);
    $response->assertStatus(200);
    $approverStatus = SdApproverStatus::where('approver_id',$this->user->id)->where('approver_type','App\User')->first();
    $this->assertEquals($approverStatus->status,'DENIED');
  }

  /** @group approvalActionByHash */
  public function test_approvalActionByHash_forWrongHashForCab()
  {
    $this->getLoggedInUserForWeb('admin');

    $change = factory(SdChanges::class)->create();

    $approvalWorkflowId = $this->createWorkflowWithLevel([$this->user->id]);

    $response = $this->call('POST',url('service-desk/api/apply-cab-approval'),
        ['change_id'=> $change->id, 'workflow_id'=>$approvalWorkflowId]);

    $hash = $this->getPrivateMethod($this->apiApproverController,'encryptHashWithEmail',[$this->user->email,'test_hash']);

    $response = $this->call('POST',url('service-desk/api/approval-action/'.$hash),['action_type'=>'deny']);
    $response->assertStatus(404);
  }

  /** @group approvalActionByHash */
  public function test_approvalActionByHash_forExpiredHashForCab()
  {
    $this->getLoggedInUserForWeb('admin');

    $change = factory(SdChanges::class)->create();

    $approvalWorkflowId = $this->createWorkflowWithLevel([$this->user->id]);

    $response = $this->call('POST',url('service-desk/api/apply-cab-approval'),
        ['change_id'=> $change->id, 'workflow_id'=>$approvalWorkflowId]);

    $approver = SdApproverStatus::where('status','PENDING')->first();
    $approver->update(['status'=>'APPROVED']);
    $hash = $this->getPrivateMethod($this->apiApproverController,'encryptHashWithEmail',[$this->user->email, $approver->hash]);

    $response = $this->call('POST',url('service-desk/api/approval-action/'.$hash),['action_type'=>'deny']);
    $response->assertStatus(404);
  }

  /** @group approvalActionByHash */ 
  public function test_approvalActionByHash_forCorrectHashAndActionAsDenyForCab()
  {
    $this->getLoggedInUserForWeb('admin');

    $change = factory(SdChanges::class)->create();

    $approvalWorkflowId = $this->createWorkflowWithLevel([$this->user->id]);

    $response = $this->call('POST',url('service-desk/api/apply-cab-approval'),
        ['change_id'=> $change->id, 'workflow_id'=>$approvalWorkflowId]);

    $hash = SdApproverStatus::where('status','PENDING')->first()->hash;
    $hash = $this->getPrivateMethod($this->apiApproverController,'encryptHashWithEmail',[$this->user->email, $hash]);

    $response = $this->call('POST',url('service-desk/api/approval-action/'.$hash),['action_type'=>'deny']);
    $response->assertStatus(200);
    $approverStatus = SdApproverStatus::where('approver_id',$this->user->id)->where('approver_type','App\User')->first();
    $this->assertEquals($approverStatus->status,'DENIED');
  }

  /** @group approvalActionByHash */
  public function test_approvalActionByHash_forCorrectHashAndActionAsApproveForCab()
  {
    $this->getLoggedInUserForWeb('admin');

    $change = factory(SdChanges::class)->create();

    $approvalWorkflowId = $this->createWorkflowWithLevel([$this->user->id]);

    $response = $this->call('POST',url('service-desk/api/apply-cab-approval'),
        ['change_id'=> $change->id, 'workflow_id'=>$approvalWorkflowId]);

    $hash = SdApproverStatus::where('status','PENDING')->first()->hash;
    $hash = $this->getPrivateMethod($this->apiApproverController,'encryptHashWithEmail',[$this->user->email, $hash]);

    $response = $this->call('POST',url('service-desk/api/approval-action/'.$hash),['action_type'=>'approve']);
    $response->assertStatus(200);
    $approverStatus = SdApproverStatus::where('approver_id',$this->user->id)->where('approver_type','App\User')->first();
    $this->assertEquals($approverStatus->status,'APPROVED');
  }

  /** @group removeApprovalWorkflow */
  public function test_removeApprovalWorkflow_withWrongChangeIdForCab()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('DELETE', url("service-desk/api/remove-cab-approval/wrong-change-id"));
    $response->assertStatus(400);
  }

  /** @group removeApprovalWorkflow */
  public function test_removeApprovalWorkflow_withProperChangeIdForCab()
  {
    $this->getLoggedInUserForWeb('admin');
    $changeId = factory(SdChanges::class)->create(['status_id' => 1])->id;
    $approvalWorkflowId = $this->createWorkflowWithLevel([1],[4,5]);
    $response = $this->call('POST',url('service-desk/api/apply-cab-approval'),
        ['change_id'=> $changeId, 'workflow_id'=>$approvalWorkflowId]);
    $response->assertStatus(200);
    $workflow = SdApprovalWorkflowChange::where('approval_workflow_id', $approvalWorkflowId)->first();
    $approverStatuses = $workflow->approvalLevels()->first()->approverStatuses()->get()->toArray();
    $response = $this->call('DELETE', url("service-desk/api/remove-cab-approval/$changeId"));
    $workflow = SdApprovalWorkflowChange::where('change_id', $changeId)->first();
    $this->assertEquals($workflow, null);
    $this->assertDatabaseMissing('sd_approval_workflow_changes', ['id' => $approvalWorkflowId]);
    $this->assertDatabaseMissing('sd_approval_level_statuses', ['approval_workflow_change_id' => $approvalWorkflowId]);
    foreach ($approverStatuses as $approverStatus) {
      $this->assertDatabaseMissing('sd_approver_statuses', $approverStatus);
    }
  }
}
