<?php
namespace App\Plugins\ServiceDesk\Controllers\Contract;

use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use App\Plugins\ServiceDesk\Policies\AgentPermissionPolicy;
use App\Plugins\ServiceDesk\Model\Contract\SdContract;
use App\Plugins\ServiceDesk\Model\Contract\SdContractThread;
use Auth;

/**
 * Handles all the actions or action-related data for a user, while handling a contract
 *
 * @author Abhishek Kumar Shashi <abhishek.shashi@ladybirdweb.com>
 */
class ContractActionOptionsController extends BaseServiceDeskController
{
    // agent permission based on logged in user
    protected $agentPermissionPolicy;

    public function __construct()
    {
        $this->agentPermissionPolicy = new AgentPermissionPolicy();
    }

    /**
     * Gets list of actions as allowed(true) or not-allowed(false) for logged in user. 
     * for eg. if the logged in user is not allowed to edit change
     * @param  $contract
     * @return Response array       success response with array of permissions
     */
    public function getContractActionList(SdContract $contract)
    {
        $allowedActions = [
            'contract_edit' => $this->isContractEditable($contract),
            'contract_delete' => $this->isContractPermission('contractDelete'),
            'contract_view' => $this->isContractPermission('contractsView'),
            'contract_asset_attach' => $this->isContractPermission('assetAttach'),
            'contract_asset_detach' => $this->isContractPermission('assetDetach'),
            'associated_asset' => $this->checkAssociatedAssets($contract),
            'contract_approve' => $this->iscontractApprovable($contract),
            'contract_terminate' => $this->iscontractTerminable($contract),
            'contract_extend' => $this->iscontractExtendable($contract),
            'contract_renew' => $this->iscontractRenewable($contract),
            'contract_expiry_reminder'=>$this->iscontractExpiryReminder($contract),
        ];

        return successResponse('', ['actions' => $allowedActions]);
    }

    /**
     * method to check agent has edit contract permission and for edit contract button visibility
     * @param $contract
     * In this we are comparing with number which are(Contract Statuses in DB) for contract edit button visibility
     * @return boolean
     */
    private function isContractEditable($contract)
    {   
        return (bool) ($contract->status_id == 1) && $this->agentPermissionPolicy->contractEdit();
    }

    /**
     * method to check agent has  contract permission and for  contract button visibility
     * @param $contractPermission(expected values : 'contractDelete', 'contractsView', 'assetAttach', 'assetDetach')
     * @return boolean
     */
    private function isContractPermission($contractPermission)
    {
        return (bool) $this->agentPermissionPolicy->$contractPermission();
    }

    /**
     * method to check associated assets and for associated assets tab visibility
     * @param $contract
     * @return boolean
     */
    private function checkAssociatedAssets($contract)
    {
        return (bool) $contract->attachAsset()->count();
    }

    /**
     * method to check agent has contract approve  permission and for approve  button visibility
     * this method will also be applicable for contract reject  permission and for reject  button visibility
     * In this we are comparing with number which are(Contract Statuses in DB) for contract approve  permission 
     and for approve  button visibility
     * @param $contract
     * @return boolean
     */
    private function iscontractApprovable($contract)
    {
        return (bool) (($contract->status_id == 1) or ($contract->status_id == 3 && $contract->renewal_status_id == 10) or ($contract->status_id == 5 && $contract->renewal_status_id == 7)) && (Auth::user()->id == $contract->approver_id);
    }

    /**
     * method to check terminate button visibility
     * @param $contract
     * In this we are comparing with number which are(Contract Statuses in DB) for contract terminate button 
     visibility
     * @return boolean
     */
    private function iscontractTerminable($contract)
    {
        return (bool) (($contract->status_id == 3) or ($contract->status_id == 3 && $contract->renewal_status_id == 10) or ($contract->status_id == 3 && $contract->renewal_status_id == 12) or ($contract->status_id == 5 && $contract->renewal_status_id == 7) or ($contract->status_id == 5 && $contract->renewal_status_id == 8));
    }

    /**
     * method to check extend button visibility
     * @param $contract
     * In this we are comparing with number which are(Contract Statuses in DB) for extend  button visibility
     * @return boolean
     */
    private function iscontractExtendable($contract)
    {
        $contractThreads = SdContractThread::where('contract_id',$contract->id)->get()->toArray();
        
        return (bool) ($contract->status_id == 3) && ($contractThreads[0]['renewal_status_id'] == null);
    }

    /**
     * method to check renew button visibility
     * @param $contract
     * In this we are comparing with number which are(Contract Statuses in DB) for renew  button visibility
     * @return boolean
     */
    private function iscontractRenewable($contract)
    {
        $contractThreads = SdContractThread::where('contract_id',$contract->id)->get()->toArray();
        
        return (bool) ($contract->status_id == 5) && ($contractThreads[0]['renewal_status_id'] == null);
    }

    /**
     * method to check expiry reminder button visibility
     * @param $contract
     * @return true
     */
    private function iscontractExpiryReminder($contract)
    {
        return true;
    }

}
