<?php
namespace App\Plugins\ServiceDesk\Controllers\Changes;

use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use App\Plugins\ServiceDesk\Policies\AgentPermissionPolicy;
use App\Plugins\ServiceDesk\Model\Changes\SdChanges;
use App\Plugins\ServiceDesk\Model\Changes\SdChangestatus;
use App\Plugins\ServiceDesk\Model\Changes\ChangeReleaseRelation;
use App\Plugins\ServiceDesk\Model\Cab\SdApprovalWorkflowChange;
use Auth;
use App\Model\helpdesk\Manage\UserType;

/**
 * Handles all the actions or action-related data for a user, while handling a change
 *
 * @author Krishna Vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class ChangeActionOptionsController extends BaseServiceDeskController
{
    // agent permission based on logged in user
    protected $agentPermissionPolicy;

    public function __construct()
    {
        $this->agentPermissionPolicy = new AgentPermissionPolicy();

        $this->middleware(['auth', 'role.agent']);
    }

    /**
     * Gets list of actions as allowed(true) or not-allowed(false) for logged in user. 
     * for eg. if the logged in user is not allowed to edit change
     * @param $changeId
     * @return Response array       success response with array of permissions
     */
    public function getChangeActionList($changeId)
    {
        $change = SdChanges::find($changeId);

        if(is_null($change)){
          return errorResponse(Lang::get('ServiceDesk::lang.change_not_found'));
        }

        $allowedActions = [
            'change_create' => $this->isChangeCreatable(),
            'change_edit' => $this->isChangeEditable(),
            'change_delete' => $this->isChangeDeletable(),
            'change_view' => $this->isChangeViewable(),
            'change_close' => $this->isChangeStatusClosable($changeId),
            'change_release_attach' => $this->isReleaseAttachable($changeId),
            'change_release_detach' => $this->isReleaseDetachable($changeId),
            'allowed_enforce_cab'=> $this->isEnforceCabAllowed($changeId),
            'allowed_cab_action'=> $this->isCabActionAllowed($change),
            'view_cab_progress'=> $this->isCabProgressVisible($changeId),
            // if any approval workflow is pending for approval and agent or admin has permission for apply approval workflow
            'remove_cab'=> $this->isRemovalCabActionAllowed($changeId),
        ];

        return successResponse('', ['actions' => $allowedActions]);
    }

    /**
     * method to check agent has create change permission and for create change button visibility
     * @param  AgentPermissionPolicy  $agentPermissionPolicy
     * @return boolean
     */
    private function isChangeCreatable()
    {
        return (bool) $this->agentPermissionPolicy->changeCreate();
    }

    /**
     * method to check agent has edit change permission and for edit change button visibility
     * @param  AgentPermissionPolicy  $agentPermissionPolicy
     * @return boolean
     */
    private function isChangeEditable()
    {
        return (bool) $this->agentPermissionPolicy->changeEdit();
    }

    /**
     * method to check agent has delete change permission and for delete change button visibility
     * @param  AgentPermissionPolicy  $agentPermissionPolicy
     * @return boolean
     */
    private function isChangeDeletable()
    {
        return (bool) $this->agentPermissionPolicy->changeDelete();
    }

    /**
     * method to check agent has view change permission and for view change button visibility
     * @param  AgentPermissionPolicy  $agentPermissionPolicy
     * @return boolean
     */
    private function isChangeViewable()
    {
        return (bool) $this->agentPermissionPolicy->changesView();
    }

    /**
     * method to check agent has release attach permission and for release attach button visibility
     * @param  $changeId
     * @return boolean
     */
    private function isReleaseAttachable($changeId)
    {
        $releaseAttached = ChangeReleaseRelation::where('change_id', $changeId)->count();
        return (bool) !$releaseAttached && $this->agentPermissionPolicy->releaseAttach();
    }

    /**
     * method to check agent has release detach permission and for release detach button visibility
     * @param  $changeId
     * @return boolean
     */
    private function isReleaseDetachable($changeId)
    {
        $releaseAttached =ChangeReleaseRelation::where('change_id', $changeId)->count();
        return (bool) $releaseAttached && $this->agentPermissionPolicy->releaseDetach();
    }

    /**
     * method to check change status could be closed and for close button visibility
     * @param int $changeId
     * @return boolean
     */
    private function isChangeStatusClosable($changeId)
    {
        // check closed status and awaiting approval status ids
        $statusIds = SdChangestatus::whereIn('name', ['Closed','Awaiting Approval'])->pluck('id')->toArray();

        return (bool) SdChanges::where('id', $changeId)->whereNotIn('status_id', $statusIds)->count();
    }

    /**
     * If enforcing cab is allowed
     * NOTE: this method is seperate because there are some future enhancements(permissions) that has to be
     * built in PHASE-2
     * @return boolean
     */
    private function isEnforceCabAllowed($changeId)
    {
      $isCabAllowed = SdApprovalWorkflowChange::where('change_id', $changeId)
          ->where('status','PENDING')->count();

      return !$isCabAllowed;
    }

    /**
     * If approving/denying a change is allowed
     * @param SdChanges $change
     * @return boolean
     */
    private function isCabActionAllowed(SdChanges $change)
    {
      //will be only allowed if there is a cab for the change and user role `admin`
      // if the user is there in the list of action takers in current level, (check team lead and department manager also)
      $userId = Auth::user()->id;
      $changeId = $change->id;

      $cabForChange = SdApprovalWorkflowChange::where('change_id', $changeId)
          ->where('status','PENDING')->first();

      if(!$cabForChange){
        return false;
      }

      $activelevelForChange = $cabForChange->approvalLevels()->where('is_active', 1)->first();

      if(!$activelevelForChange){
        return false;
      }

      $approverStatuses = $activelevelForChange->approverStatuses()->where('status','PENDING')->get();
      //check if user is in the list as user
      if(isDepartmentManager($change->department_id, $userId)){
        $userTypeId = UserType::where('key','department_manager')->first()->id;
        return (bool)$approverStatuses->where('approver_id',$userTypeId)
          ->where('approver_type','App\Model\helpdesk\Manage\UserType')->count();
      }

      if(isTeamLead($change->team_id, $userId)){
        $userTypeId = UserType::where('key','team_lead')->first()->id;
        return (bool)$approverStatuses->where('approver_id',$userTypeId)
          ->where('approver_type','App\Model\helpdesk\Manage\UserType')->count();
      }

      return (bool)$approverStatuses->where('approver_id',$userId)
        ->where('approver_type','App\User')->count();
    }

    /**
     * If cab progress should be visible
     * @param  int  $changeId
     * @return boolean
     */
    private function isCabProgressVisible($changeId)
    {
      //will be only allowed if there is a cab for the change
      return (bool)SdApprovalWorkflowChange::where('change_id', $changeId)->count();
    }

     /**
     * if cab remove is allowed
     * @param  int $changeId
     * @return boolean
     */
    private function isRemovalCabActionAllowed($changeId)
    {
        $isAppliedCabOwner = Auth::user()->id;
        $isCabRemoveActionAllowed = SdApprovalWorkflowChange::where([['change_id', $changeId],['status','PENDING'],['user_id', $isAppliedCabOwner]])->count();

        return (bool)$isCabRemoveActionAllowed;
    }




}
