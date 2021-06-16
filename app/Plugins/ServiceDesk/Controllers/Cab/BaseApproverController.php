<?php

namespace App\Plugins\ServiceDesk\Controllers\Cab;

use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use DB;
use App\Model\helpdesk\Workflow\ApprovalLevel;
use App\Model\helpdesk\Agent\Department;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Controllers\Common\PhpMailController;
use App\User;
use Config;
use Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Exception;
use App\Plugins\ServiceDesk\Model\Changes\SdChanges;
use App\Plugins\ServiceDesk\Model\Cab\SdApprovalLevelStatus;
use App\Plugins\ServiceDesk\Model\Cab\SdApprovalWorkflowChange;
use App\Model\helpdesk\Agent\Teams as Team;
use App\Plugins\ServiceDesk\Traits\CabApprovalActivityHandler;
use Auth;

class BaseApproverController extends BaseServiceDeskController
{
    use CabApprovalActivityHandler;

    /**
     * Change on which workflow has been applied
     * @var Model
     */
    protected $model;

    /**
     * currently logged in user or the user who is performing the action(like approve/deny)
     * @var User
     */
    protected $user;

    /**
     * Creates required levels in approval_level_statuses based on parent workflow by copying levels from
     * approval_levels table and marking each of level status as PENDING (which will later be updated as approved)
     * @param approvalLevels
     * @param  ApprovalWorkflowChange $parentWorkflow   parent workflow for which levels are required to be created
     * @return array
     */
    protected function createPendingApprovalLevels($approvalLevels, SdApprovalWorkflowChange $parentApprovalWorkflow)
    { 
        $approvalLevelStatuses = collect();
        foreach ($approvalLevels as $key => $level) {
            //by default for the first level we mark level as active
            $isActive = $key == 0 ? 1 : 0;
            $approvalLevelStatuses->push($parentApprovalWorkflow->approvalLevels()->create(['approval_level_id' => $level->id, 'is_active'=>$isActive,
              'status'=>'PENDING', 'name'=>$level->name, 'order'=>$level->order, 'match'=> $level->match]));
            $approvers = DB::table('approval_level_approvers')->where('approval_level_id', $level->id)->get();
            $this->createPendingApproverStatuses($approvers, $approvalLevelStatuses[$key]);
        }
        return $approvalLevelStatuses;
    }

    /**
     * Creates required approvers in `approver_statuses` based on parent level by copying approvers from
     * `ticket_approvers` table and marking each of level status as PENDING (which will later be updated as approved)
     * @param  array              $approvers          array of objects from `approval_level_approvers` table
     * @param  sdApprovalLevelStatus $parentApprovalLevelStatus parent level status under which users has be be created
     * @return null
     */
    private function createPendingApproverStatuses($approvers, SdApprovalLevelStatus $parentApprovalLevelStatus)
    {
        foreach ($approvers as $approver) {
            // a random hash
            $hash = str_random(40);
            $parentApprovalLevelStatus->approverStatuses()->create(['approver_id' => $approver->approval_level_approver_id,
            'approver_type'=> $approver->approval_level_approver_type, 'hash'=>$hash, 'status'=>'PENDING']);
        }
    }

    /**
     * Gets list of approvers based on change and approval level along with hash
     * @param  sdChanges       $sdChange
     * @param  ApprovalLevel $approvalWorkflowLevel
     * @return Collection
     */
    protected function getApproversList(SdChanges $sdChange, SdApprovalLevelStatus $approvalWorkflowLevel) : Collection
    {
        // get all approvers and send mail
        //query into ApprovalLevelStatues instead of ApprovalLevel, so that we can get hash also
        $approverUsers = $approvalWorkflowLevel->approveUsers()
            ->select('users.id','first_name','last_name','email','user_name')->get();

        //adding hash property to each approverUser so that it can be symmetric with users
        foreach ($approverUsers as $user) {
          $user->hash = $user->pivot->hash;
        }

        $approverTypes = $approvalWorkflowLevel->approveUserTypes()->get();

         // check $approverTypes and if it has department manager, get all the users who are department
        foreach ($approverTypes as $approverType) {

          //insert hash in each of them
          if($approverType->key == 'department_manager' && $sdChange->department_id){
            $approverUsers = $approverUsers->merge($this->getDepartmentManagers($sdChange->department_id, $approverType->pivot->hash));
          }

          if($approverType->key == 'team_lead' && $sdChange->team_id){
            $approverUsers = $approverUsers->merge($this->getTeamLeads($sdChange->team_id, $approverType->pivot->hash));
          }
        }

        return $approverUsers;
    }

    /**
     * gets department managers of the given department and appends passed hash to each manager
     * @param  int $departmentId
     * @return array
     */
    private function getDepartmentManagers($departmentId, $hash){
        $users = Department::select('id')->find($departmentId)->managers()
          ->select('users.id','first_name','last_name','email','user_name')->get();

        //adding hash property to each user
        foreach ($users as $user) {
          $user->hash = $hash;
        }
        return $users;
    }

    /**
     * Gets team lead of the given team and appends hash to each team lead
     * @param int $teamId
     * @return array
     */
    private function getTeamLeads($teamId, $hash){
        $users = Team::select('id','team_lead')->find($teamId)->lead()
          ->select('users.id','first_name','last_name','email','user_name')->get();

        //adding hash property to each user
        foreach ($users as $user) {
          $user->hash = $hash;
        }
        return $users;
    }

    /**
     * Send mail to approver with approval link
     * @param User $approver
     * @param SdChanges $change
     * @param string $hash     unique hash which will be used to recognize who has approved the change
     * @return null
     */
    protected function sendMailToApprover(User $approver, SdChanges $change, string $hash)
    {
        $phpMailController = new PhpMailController;

        $from = $phpMailController->mailfrom('1', '0');

        $to = ['name' => $approver->full_name, 'email' => $approver->email];

        $encodeHash = $this->encryptHashWithEmail($approver->email, $hash);

        $approvalLink = Config::get('app.url')."/change-approval"."/$encodeHash";

        $changeLink = Config::get('app.url').'/service-desk/changes/'.$change->id.'/show';

        $message = ['message'=>'','scenario'=>'approve-change'];

        $templateVariables = ['receiver_name'=> $approver->full_name, 'change_number' => '#CHN-'.$change->id, 'change_approval_link'=> $approvalLink, 'change_link' => $changeLink];
        
        $phpMailController->sendmail($from, $to, $message, $templateVariables);
    }

    /**
     * Creates an associative array of email and hash and encryts that
     * @param  string $email
     * @param  string $hash
     * @return string
     */
    private function encryptHashWithEmail(string $email = null, string $hash) : string
    {
        $hashString = json_encode(['hash'=> $hash, 'email' => $email]);
        $encryptedHashString = Crypt::encrypt($hashString);
        return $encryptedHashString;
    }


    /**
     * Decrypts hash and gives an object in structure {email:'email',hash:'hash'}
     * @param  string $encryptedHash
     * @return object {email:'email',hash:'hash'}
     */
    protected function decryptHash($encryptedHash)
    {
      try{
        return json_decode(Crypt::decrypt($encryptedHash));
      }catch(DecryptException $e){
          throw new Exception($e);
      }
    }

    /**
     * Handles approver related operations when a change approval action is performed
     * @param SdApprovalLevelStatus $activeLevel
     * @param string $action
     * @param int $approverId
     * @param string $approverType
     * @param string $comemnt
     * @return Boolean    if approver is updated or not
     */
    protected function handleApproverUpdate(SdApprovalLevelStatus $activeLevel, string $action, $approverId,
      string $approverType, string $comment = null, SdChanges $change)
    {
      $approver = $activeLevel->approverStatuses()->where('approver_id', $approverId)
          ->where('approver_type',$approverType)->first();

      if($approver){
        $approver->update(['status' => $action, 'comment' => $comment]);
        $this->createActivity('approver', $approver, $action, $change, $comment);
        return true;
      }
      return false;
    }

    /**
     * Handles update of active level by checking its approvers status. Once level is completed,
     * it marks that as approved or denied
     * @param  ApprovalLevelStatus $activeLevelStatus
     * @return null
     */
    protected function handleLevelUpdate(SdApprovalLevelStatus $activeLevelStatus, SdChanges $change)
    {
      // It checks if the current level is approved, it marks current level as inactive
      // and next level(by order) as active
      //query for the active one, check its approvers and update accordingly
      //get if it is any or all
      $approvedCount = $activeLevelStatus->approverStatuses()->where('sd_approver_statuses.status','APPROVED')->count();
      $deniedCount = $activeLevelStatus->approverStatuses()->where('sd_approver_statuses.status','DENIED')->count();
      $pendingCount = $activeLevelStatus->approverStatuses()->where('sd_approver_statuses.status','PENDING')->count();
      if($activeLevelStatus->match == 'any'){
          if($approvedCount > 0){
            $this->approveLevel($activeLevelStatus, $change);
          }

          if($deniedCount > 0 && $pendingCount + $approvedCount == 0){
            //level has been denied, so workflow will be denied too
            $this->denyLevel($activeLevelStatus, $change);
          }
      }

      if($activeLevelStatus->match == 'all'){
          if($deniedCount > 0){
            $this->denyLevel($activeLevelStatus, $change);
          }

          if($approvedCount > 0 && $pendingCount + $deniedCount == 0){
            $this->approveLevel($activeLevelStatus, $change);
          }
      }
    }

    /**
     * marks active level as in_active and APPROVED and if next level exists, it marks that as active
     * @param SdApprovalLevelStatus $activeLevelStatus  active level status
     * @return null
     */
    private function approveLevel(SdApprovalLevelStatus $activeLevelStatus, SdChanges $change)
    {
      $activeLevelStatus->update(['is_active'=> 0,'status'=>'APPROVED']);

      $this->createActivity('level',$activeLevelStatus, 'APPROVED', $change);

      $nextLevel = SdApprovalLevelStatus::orderBy('order', 'asc')->where('order', '>', $activeLevelStatus->order)
          ->where('approval_workflow_change_id', $activeLevelStatus->approval_workflow_change_id)->first();

      if($nextLevel){
        $nextLevel->update(['is_active'=>1]);
        $this->createActivity('level',$nextLevel, 'ENFORCED', $change);

        //send mail to next level people
        $approvers = $this->getApproversList($this->model, $nextLevel);

        foreach ($approvers as $approver) {
          $this->sendMailToApprover($approver, $this->model, $approver->hash);
        }
      }
    }

    /**
     * Marks a level as denied
     * @param  SdApprovalLevelStatus $activeLevelStatus Active level
     * @return null
     */
    private function denyLevel(SdApprovalLevelStatus $activeLevelStatus, SdChanges $change)
    {
      $activeLevelStatus->update(['is_active'=> 0,'status'=>'DENIED']);
      $this->createActivity('level',$activeLevelStatus, 'DENIED', $change);
    }


    /**
     * Updates workflow status according to the active levels
     * @param  ApprovalWorkflowChange $workflow
     * @param  string                 $action    `APPROVED` or `DENIED`
     * @return null
     */
    protected function handleWorlflowUpdate(SdApprovalWorkflowChange $workflow, SdChanges $change)
    {
      //if all levels are approved, it will mark workflow as approved
      //query for all levels and update workflow accordingly
      if(!$workflow->approvalLevels()->where('is_active', 1)->first()){

        //if even a single level is denied, it will mark workflow as denied
        $deniedCount = $workflow->approvalLevels()->where('sd_approval_level_statuses.status','DENIED')->count();
        $workflowStatus = $deniedCount > 0 ? 'DENIED' : 'APPROVED';
        //updating ticket
        $statusId = $workflowStatus == 'APPROVED' ? $workflow->action_on_approve : $workflow->action_on_deny;
        // $this->changeStatus($statusId);
        //updating workflow
        $workflow->update(['status' => $workflowStatus]);
        $this->createActivity('workflow', $workflow, $workflowStatus, $change);
        $this->changeStatusBasedOnCabApprovedOrDenied($change->id,$workflow);
      }
    }

    /**
     * method to change change status to planning or pending release
     * based on applied cab is approved or denied
     * @param int $changeId
     * @param string $appliedCabStatus
     * @return null
     */
    private function changeStatusBasedOnCabApprovedOrDenied($changeId, $workflow)
    {
      if ($workflow->action_on_deny && $workflow->status == 'DENIED') {
        $this->model->where('id', $changeId)->update(['status_id' => $workflow->action_on_deny]);
        $this->createChangeActivityLog($changeId);
      }
      if ($workflow->action_on_approve && $workflow->status == 'APPROVED') {
        $this->model->where('id', $changeId)->update(['status_id' => $workflow->action_on_approve]);
        $this->createChangeActivityLog($changeId);
      }
      // change status when action on approve or action on deny status exists and create similar activity
    }

    /**
     * method to create change activity log while changing change status
     * @param int $changeId
     * @return null
     */
    protected function createChangeActivityLog($changeId)
    {
      $change = $this->model->find($changeId);
      $change->manuallyLogActivityForPivot($change, 'status', $change->status->name, 'updated', '', 'change', 1, $this->user);
    }

    /**
     * method to append hyperlink to aciton performer
     * @return string $actionPerformer
     */
    protected function appendHyperlinkToActionPerformer()
    {
      $actionPerformer = Auth::user();
      $actionPerformerUrl = Config::get('app.url')."/user/{$actionPerformer->id}";
      $actionPerformer = "<a href={$actionPerformerUrl}>{$actionPerformer->full_name}</a>";

      return $actionPerformer;
    }

}