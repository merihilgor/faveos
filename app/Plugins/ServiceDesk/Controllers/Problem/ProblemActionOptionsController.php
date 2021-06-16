<?php
namespace App\Plugins\ServiceDesk\Controllers\Problem;

use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use App\Plugins\ServiceDesk\Policies\AgentPermissionPolicy;
use App\Plugins\ServiceDesk\Model\Problem\SdProblem;
use App\Plugins\ServiceDesk\Model\Common\GeneralInfo;
use Auth;
use App\Model\helpdesk\Ticket\Ticket_Status;

/**
 * Handles all the actions or action-related data for a user, while handling a problem
 *
 * @author Abhishek Kumar Shashi <abhishek.shashi@ladybirdweb.com>
 */
class ProblemActionOptionsController extends BaseServiceDeskController
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
     * @param  $problem
     * @return Response array       success response with array of permissions
     */
    public function getProblemActionList(SdProblem $problem)
    {
        $allowedActions = [
            'change_create' => $this->isChangeCreatable(),
            'problem_edit' => $this->isProblemEditable(),
            'problem_delete' => $this->isProblemDeletable(),
            'problem_view' => $this->isProblemViewable(),
            'problem_close' => $this->isProblemStatusClosable($problem),
            'problem_change_attach' => $this->isChangeAttachable($problem),
            'problem_change_detach' => $this->isChangeDetachable($problem),
            'problem_asset_attach' => $this->isAssetAttachable($problem),
            'problem_asset_detach' => $this->isAssetDetachable($problem),
            'associated_asset' => $this->checkAssociatedAssets($problem),
            'associated_ticket' => $this->checkAssociatedTickets($problem),
            'associated_change' => $this->checkAssociatedChange($problem),
            'check_planning' => $this->checkplanning($problem),
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
     * method to check agent has edit problem permission and for edit problem button visibility
     * @param  AgentPermissionPolicy  $agentPermissionPolicy
     * @return boolean
     */
    private function isProblemEditable()
    {
        return (bool) $this->agentPermissionPolicy->problemEdit();
    }

    /**
     * method to check agent has delete problem permission and for delete problem button visibility
     * @param  AgentPermissionPolicy  $agentPermissionPolicy
     * @return boolean
     */
    private function isProblemDeletable()
    {
        return (bool) $this->agentPermissionPolicy->problemDelete();
    }

    /**
     * method to check agent has view problem permission and for view problem button visibility
     * @param  AgentPermissionPolicy  $agentPermissionPolicy
     * @return boolean
     */
    private function isProblemViewable()
    {
        return (bool) $this->agentPermissionPolicy->problemsView();
    }

    /**
     * method to check agent has change attach permission and for change attach button visibility
     * @param  $problem
     * @return boolean
     */
    private function isChangeAttachable($problem)
    {
        $changeAttached = $problem->attachChange()->count();
        return (bool) !$changeAttached && $this->agentPermissionPolicy->changeAttach();
    }

    /**
     * method to check agent has change detach permission and for change detach button visibility
     * @param  $problem
     * @return boolean
     */
    private function isChangeDetachable($problem)
    {
        $changeAttached = $problem->attachChange()->count();
        return (bool) $changeAttached && $this->agentPermissionPolicy->changeDetach();
    }

    /**
     * method to check agent has asset attach permission and for asset attach button visibility
     * @return boolean
     */
    private function isAssetAttachable()
    {
        return (bool) $this->agentPermissionPolicy->assetAttach();
    }

    /**
     * method to check agent has asset detach permission and for asset detach button visibility
     * @return boolean
     */
    private function isAssetDetachable()
    {
        return (bool) $this->agentPermissionPolicy->assetDetach();
    }

    /**
     * method to check problem status could be closed and for close button visibility
     * @param $problem
     * @return boolean
     */
    private function isProblemStatusClosable($problem)
    {
        if(($closedStatusId = Ticket_Status::where('name', 'Close')->first()))
            $closedStatusId = $closedStatusId->id;
        if(($closedStatusId = Ticket_Status::where('name', 'Closed')->first()))
            $closedStatusId = $closedStatusId->id;
        return (bool) SdProblem::where([['id', $problem->id],['status_type_id', '<>', $closedStatusId]])->count();
    }

    /**
     * method to check associated assets and for associated assets tab visibility
     * @param $problem
     * @return boolean
     */
    private function checkAssociatedAssets($problem)
    {
        return (bool) $problem->attachAssets()->count();
    }

    /**
     * method to check associated tickets and for associated tickets tab visibility
     * @param $problem
     * @return boolean
     */
    private function checkAssociatedTickets($problem)
    {
       return (bool) $problem->attachTickets()->count();
    }

    /**
     * method to check associated change and for associated change tab visibility
     * @param $problem
     * @return boolean
     */
    private function checkAssociatedChange($problem)
    {
        return (bool) $problem->attachChange()->count();
    }
    /**
     * method to check Popup Details and for planning popup tab visibility
     * @param $problem
     * @return boolean
     */
    private function checkplanning($problem)
    {
        $problemModel = 'sd_problem:'.$problem->id;
        return (bool) GeneralInfo::where('owner', $problemModel)->count();

    }


}
