<?php

namespace App\Plugins\ServiceDesk\Traits;

use App\Plugins\ServiceDesk\Model\Cab\SdApprovalWorkflowChange;
use App\Plugins\ServiceDesk\Model\Cab\SdApproverStatus;
use App\Plugins\ServiceDesk\Model\Cab\SdApprovalLevelStatus;
use App\Model\helpdesk\Ticket\Ticket_Thread as Thread;
use App\Model\helpdesk\Manage\UserType;
use App\Plugins\ServiceDesk\Model\Changes\SdChanges;
use App\User;
use Exception;
use Config;

/**
 * Handles all activity related operations during cab approval updation
 */
trait CabApprovalActivityHandler
{

  /**
   * Creates activity based on the category
   * For eg. if category is passed as `workflow_applied`, it will create a thread that the given workflow_name
   * has been enforced (as internal notes).
   * if category is passed as `approver_approved`, it will create thread saying that particular approver has
   * approved
   * if category is passed as `level_approved`, it will create thread saying this level_name has approved
   * if new level is applied, it will say that level_name has been applied, with approver names
   * @param string $category  `workflow`, `level` or `approver`
   * @param object $referer an object of `SdApprovalWorkflowChange`,`SdApprovalLevelStatus` or `SdApproverStatus`
   * @param string $action `ENFORCED`,`APPROVED`,'DENIED'
   * @param SdChanges change
   * @param string $actionPerformer Full name of the user/workflow/listener which performs the actions
   * @return null
   */
  protected function createActivity($category, $referer, string $action, SdChanges $change, $comment = "", string $actionPerformer = null)
  {
      switch ($category) {

        case 'workflow':
          $internalNoteBody = $this->noteForWorkflow($referer, $action, $actionPerformer);
          break;

        case 'level':
          $internalNoteBody = $this->noteForLevel($referer, $action);
          break;

        case 'approver':
          $internalNoteBody = $this->noteForApprover($referer, $action, $comment);
          break;

        default:
          throw new Exception('invalid category passed to `createThread` method');
      }

      $change->manuallyLogActivityForPivot($change, $category, $internalNoteBody, 'created', '', 'change_cab', 0, $this->user);
  }

  /**
   * Construct thread body for workflow apply and remove
   * NOTE: the reason for making it a seperate method is because it might come as enhancement to put
   * more details in internal thread
   * @param  ApprovalWorkflow $approvalWorkflow
   * @param string $action
   * @param  string $actionPerformer Full name of the user/workflow/listener which performs the actions
   * @return string
   */
  private function noteForWorkflow(SdApprovalWorkflowChange $approvalWorkflow, string $action, string $actionPerformer = null) : string
  {
      if($action == 'ENFORCED'){
        return "<b>$approvalWorkflow->name</b> CAB approval has been enforced on the change by <b>$actionPerformer";
      } else if($action == 'REMOVED'){
        return "<b>$approvalWorkflow->name</b> CAB approval has been removed from the change by <b>$actionPerformer";
     }
        return "<b>$approvalWorkflow->name</b> CAB approval has been <b>$action</b>";
  }

  /**
   * Construct thread body for workflow apply
   * NOTE: the reason for making it a seperate method is because it might come as enhancement to put
   * more details in internal thread
   * @param  ApprovalLevel $approvalLevel
   * @return string
   */
  private function noteForLevel(SdApprovalLevelStatus $approvalLevel, string $action) : string
  {
      return "<b>$approvalLevel->name</b> CAB approval level has been <b>$action</b>";
  }

  /**
   * Construct thread body for workflow apply
   * NOTE: the reason for making it a seperate method is because it might come as enhancement to put
   * more details in internal thread
   * @param  ApprovalStatus $approvalLevel
   * @param string $action   `APPROVED` or DENIED
   * @return string
   */
  private function noteForApprover(SdApproverStatus $approver, string $action, $comment) : string
  {

      $user = User::where('id',$this->user->id)->select('first_name','last_name','email','user_name')->first();
      $userProfilePath = Config::get('app.url').'/user'."/".$this->user->id;
      $name = "<a href=$userProfilePath>$user->full_name</a>";

      //get approver name and mention he has approved or denied
      $body = "Change approval request has been <b>$action</b> by $name";

      if($comment){
        $body = $body."<br><b>Reason :</b> <i>$comment</i>";
      }

      return $body;
  }

}
