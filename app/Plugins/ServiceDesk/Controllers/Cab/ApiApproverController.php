<?php

namespace App\Plugins\ServiceDesk\Controllers\Cab;

use Illuminate\Http\Request;
use App\Model\helpdesk\Workflow\ApprovalWorkflow;
use App\Model\helpdesk\Workflow\ApprovalLevel;
use App\Model\helpdesk\Manage\UserType;
use Auth;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use App\Plugins\ServiceDesk\Controllers\Cab\BaseApproverController;
use Exception;
use App\Plugins\ServiceDesk\Model\Changes\SdChanges;
use App\Plugins\ServiceDesk\Model\Cab\SdApprovalWorkflowChange;
use App\Plugins\ServiceDesk\Model\Cab\SdApproverStatus;
use Logger;
use App\Plugins\ServiceDesk\Controllers\Changes\ApiChangeController;

/**
 * ApiApproverController
 * This controller is used to enforce CAB
 * few methods are commented, it will be used when change page is converted to vue
 *
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class ApiApproverController extends BaseApproverController
{

  public function __construct(SdChanges $sdChanges)
  {
    $this->middleware(['auth','role.agent'])->except(['getConversationByHash','approvalActionByHash']);
    $this->model = $sdChanges;
  }

  /**
   * Applies workflow to given change by creating a copy of the entire cab, so that in case
   * when approval_workflows table gets updated, it doesn't effect the enforced change
   * @param  Request $request  with parameters `change_id` and `workflow_id`
   * @return Response
   */
  public function applyApproval(Request $request)
  {
    $changeId = $request->change_id;
    $workflowId = $request->workflow_id;
    // get data from approval_workflow tables
    // update sd_approval_workflow_changes
    $approvalWorkflow = ApprovalWorkflow::find($workflowId);
    $change = $this->model->find($changeId);
    $approvalWorkflowChange = SdApprovalWorkflowChange::create([
      'approval_workflow_id' => $approvalWorkflow->id,
      'change_id'=>$changeId,
      'status'=>'PENDING',
      'name'=> $approvalWorkflow->name,
      'user_id'=> Auth::user()->id,
      'action_on_approve' => $approvalWorkflow->getOriginal('action_on_approve'),
      'action_on_deny' => $approvalWorkflow->getOriginal('action_on_deny'),
      'change_status_id' => $change->status_id
    ]);

    // get approval_level data
    // update approval_level_statuses
    $approvalLevels = $approvalWorkflow->approvalLevels()->orderBy('order','asc')->get();

    //creating entries in approval_level_statuses in pending state
    $approvalLevelStatuses = $this->createPendingApprovalLevels($approvalLevels ,$approvalWorkflowChange);

    //get approvers for first level only
    $approvers = $this->getApproversList($change, $approvalLevelStatuses->first());

    foreach ($approvers as $approver) {
      // hash proprty has been additionally added, that's why sending it as an additional  parameter
      // for consistency reasons
      $this->sendMailToApprover($approver, $change, $approver->hash);
    }

    //assign current logged in user
    $actionPerformer = $this->appendHyperlinkToActionPerformer();
    //create thread that this workflow has been applied
    $this->createActivity('workflow', $approvalWorkflowChange,'ENFORCED', $change, '', $actionPerformer);

    // changing change status to awaiting approval (awaiting approval status id is 3)
    $this->model->where('id', $changeId)->update(['status_id' => 3]);
    $this->createChangeActivityLog($changeId);

    return successResponse(trans('ServiceDesk::lang.cab_applied_successfully'));

  }


  /**
   * Removes a workflow applied on change based on changeId
   * @param  $changeId
   * @return Response
   */
  public function removeApprovalWorkflow($changeId)
  {
    $workFlow = SdApprovalWorkflowChange::where([['change_id', $changeId],['status','PENDING']])->orderBy('id', 'desc')->first();
    if (is_null($workFlow)) {
      return errorResponse(trans('lang.no_proper_cab'));
    }
    $this->model->where('id', $changeId)->update(['status_id' => $workFlow->change_status_id]);
    $this->createChangeActivityLog($changeId);
    //assign current logged in user
    $actionPerformer = $this->appendHyperlinkToActionPerformer();
    //create thread that this workflow has been removed
    $this->model = $this->model->find($changeId);
    $this->createActivity('workflow', $workFlow,'REMOVED', $this->model, '', $actionPerformer);

    $workFlow->delete();

    return successResponse(trans('ServiceDesk::lang.cab_removed_successfully'));
  }

  /**
   * Approves or denies a Change approval
   * @param  string $hash
   * @param Request
   * @return Response
   */
  public function approvalActionByHash($hash, Request $request)
  {
    $actionType = $request->action_type;
    $comment = $request->comment ? $request->comment : '';

    $hashString = $this->decryptHash($hash);

    $this->user = User::select('id','first_name','last_name','email','user_name')
        ->where('email', $hashString->email)->where('email','!=', null)->first();

    if(!$this->user) {
      return errorResponse(trans('invalid_user'));
    }

    $hash = $hashString->hash;

    // either should return that this hash been used already (if has status as APPROVED)
    $approver = SdApproverStatus::where('hash', $hash)->first();

    if(!$approver){
      return errorResponse(trans('lang.invalid_hash'), 404);
    }

    // when hash is found but expired
    if($approver->status != 'PENDING'){
      return errorResponse(trans('lang.hash_expired'), 404);
    }

    $action = ($actionType == 'approve') ? 'APPROVED' : 'DENIED';

    // get change Id from approver
    $changeId = $approver->approvalLevelStatus()->first()->approvalWorkflow()->first()->change_id;

    $this->handleChangeApprovalAction($action, $changeId, $comment);
    $changeQuery = $this->model->where('id', $changeId);
    $change = (new ApiChangeController)->getChangeQueryAndReturnChangeData($changeQuery);

    return successResponse(trans('lang.updated_successfully'));
  }

  /**
   * Approves or denies a Change approval
   * @param  string $hash
   * @param Request
   * @return Response
   */
  public function approvalActionByChangeId($changeId, Request $request)
  {
    $action = ($request->action_type == 'approve') ? 'APPROVED' : 'DENIED';
    $comment = $request->comment ? $request->comment : '';

    $this->model = $this->model->find($changeId);

    if(!$this->model){
        return errorResponse(trans('lang.change_not_found'));
    }

    try {
      $this->user = Auth::user();
      $this->handleChangeApprovalAction($action, $changeId, $comment);
      return successResponse(trans('lang.updated_successfully'));
    }
    catch(Exception $e){
      // log the exception
      Logger::exception($e, 'default');
      // return errorResponse(Lang::get('lang.some_error_occured'));
      return errorResponse($e->getMessage());
    }
  }

  /**
   * Handles approval/denial of the ticket
   * @param  string $actionType `APPROVE` OR `DENY`
   * @param  int $ticketId
   * @return Boolean
   */
  private function handleChangeApprovalAction(string $action, $changeId, string $comment)
  {
    $this->model = $this->model->find($changeId);

    //active workflow
    $workflow = SdApprovalWorkflowChange::where('change_id', $this->model->id)
            ->where('status','PENDING')->first();

    //active level
    $activeLevel = $workflow->approvalLevels()->where('status','PENDING')
        ->where('is_active', 1)->first();

    $userId = $this->user->id;


    // first check if currently logged in user is department manager if the ticket
    // if yes, send that as hash
    // if no, check if ticket is assigned to a team, if yes, then check if user is
    // team lead of the ticket, if yes, send team lead hash,
    // if no, send the hash corresponding to the user
    if(isDepartmentManager($this->model->department_id, $userId)){
        $userTypeId = UserType::where('key','department_manager')->first()->id;
        $this->handleApproverUpdate($activeLevel, $action, $userTypeId, 'App\Model\helpdesk\Manage\UserType', $comment, $this->model);
    }

    if(isTeamLead($this->model->team_id, $userId)){
      $userTypeId = UserType::where('key','team_lead')->first()->id;
      $this->handleApproverUpdate($activeLevel, $action, $userTypeId, 'App\Model\helpdesk\Manage\UserType', $comment, $this->model);
    }

    $this->handleApproverUpdate($activeLevel, $action, $userId, 'App\User', $comment, $this->model);
    $this->handleLevelUpdate($activeLevel, $this->model);
    $this->handleWorlflowUpdate($workflow, $this->model);
  }

  /**
   * gets conversation by hash
   * @param  string  $hash
   * @param  Request $request
   * @return null
   */
  public function getConversationByHash($hash, Request $request)
  {
    $hashString = $this->decryptHash($hash);
    $hash = $hashString->hash;
    $approver = SdApproverStatus::where('hash', $hash)->first();
    if(!$approver){
      return errorResponse(trans('lang.invalid_hash'), 404);
    }

    $isApproverPending = (bool)($approver->status == 'PENDING');
    $isLevelPending = (bool)($approver->approvalLevelStatus()->first()->status == 'PENDING');

    // //when hash is found but expired
    if(!$isLevelPending || ($isLevelPending && !$isApproverPending)){
      return errorResponse(trans('lang.hash_expired'), 404);
    }

    //get to ApprovalWorkflowStatus table to get ticketId
    $changeId = $approver->approvalLevelStatus()->first()->approvalWorkflow()->first()->change_id;

    $change = SdChanges::find($changeId);

    return successResponse('', ['change' => $change]);
  }

  /**
   * Gets current approval state of the change
   * @param  int $changeId
   * @return null
   */
  public function getChangeApprovalStatus($changeId)
  {
    //if change id is not found in ApprovalStatus, it should send errorResponse
    $approvalWorkflowChanges = SdApprovalWorkflowChange::with([
      'approvalLevels'=> function($q){
        return $q->select('id','status','name','approval_level_id','approval_workflow_change_id','is_active')
          ->with(['approveUsers','approveUserTypes'])->orderBy('id', 'asc');
      }
    ])->where('change_id',$changeId)->orderBy('id','asc')->get()->toArray();

    if(!$approvalWorkflowChanges){
      return errorResponse(trans('ServiceDesk::lang.no_cab_applied'));
    }

    $this->formatChangeApprovalData($approvalWorkflowChanges);
    return successResponse('',$approvalWorkflowChanges);
  }

  /**
   * Formats approval cab data in the required format
   * @param  Collection $cabData
   * @return null
   */
  private function formatChangeApprovalData(&$cabData)
  {
      foreach ($cabData as &$workflow) {

        foreach ($workflow['approval_levels'] as &$level) {

          foreach ($level['approve_users'] as &$user) {
            $user['status'] = $user['pivot']['status'];
            $user['name'] = !$user['first_name'] ? $user['user_name'] : $user['first_name'].' '.$user['last_name'];
            unset($user['pivot'], $user['first_name'], $user['user_name'], $user['last_name'], $user['email']);
          }

          foreach ($level['approve_user_types'] as &$userTypes) {
            $userTypes['status'] = $userTypes['pivot']['status'];
            unset($userTypes['pivot']);
          }
        }
     }
  }

}