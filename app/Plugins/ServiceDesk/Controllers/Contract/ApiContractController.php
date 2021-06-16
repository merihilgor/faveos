<?php

namespace App\Plugins\ServiceDesk\Controllers\Contract;

use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use App\Plugins\ServiceDesk\Model\Contract\SdContract;
use App\Plugins\ServiceDesk\Model\Contract\ContractUserOrganization;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\Plugins\ServiceDesk\Model\Common\Email;
use App\Plugins\ServiceDesk\Model\Common\Attachments;
use Illuminate\Http\Request;
use App\Plugins\ServiceDesk\Controllers\Library\UtilityController;
use App\Plugins\ServiceDesk\Request\Contract\CreateUpdateContractThreadRequest;
use Auth;
use App\Plugins\ServiceDesk\Requests\CreateUpdateContractRequest;
use File;
use App\Plugins\ServiceDesk\Controllers\Contract\ContractThreadController;
use Carbon\Carbon;
use Config;
use App\Plugins\ServiceDesk\Model\Common\SdUser;
use App\Plugins\ServiceDesk\Model\Contract\SdContractThread;
use App\Http\Controllers\Common\PhpMailController;
use App\Http\Controllers\Agent\helpdesk\Notifications\NotificationController as Notify;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Models\ActivityBatch;
use App\Plugins\ServiceDesk\Policies\AgentPermissionPolicy;
use App\Plugins\ServiceDesk\Request\Contract\RenewContractRequest;
use App\Plugins\ServiceDesk\Request\Contract\ExtendContractRequest;
use App\Plugins\ServiceDesk\Request\Contract\RejectContractRequest;
use App\Plugins\ServiceDesk\Request\Contract\ExpiryReminderContractRequest;
use App\Plugins\ServiceDesk\Jobs\ContractNotificationExpiry;
use Logger;
use App\FaveoStorage\Controllers\StorageController;
use Crypt;
use DB;



/**
 * Handles API's for Contract Controller
 * 
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 *
 * @author Abhishek Kumar Shashi <abhishek.shashi@ladybirdweb.com>
*/
class ApiContractController extends BaseServiceDeskController 
{
    protected $agentPermission;


    public function __construct() {
        $this->middleware('role.agent')->except(['contractApprovalActionByHash','contractDetailsByHash']);
    }

     /**
     * method is used to retrieve associated contracts based on assets linked to ticket and contract
     * @param $ticketId
     * @param Request request
     * @return response
     */
    public function getContractsBasedOnAssetAttachedToTicket($ticketId, Request $request)
    {

      $searchQuery = $request->input('search-query') ?? '';
      $sortOrder = $request->input('sort-order') ?? 'asc';
      $sortField = $request->input('sort-field') ?? 'name';
      $limit = $request->limit ?? 10;

      $contracts = SdContract::whereHas('attachAsset',function($baseQuery) use ($ticketId) {
          $baseQuery->whereHas('tickets', function($query) use ($ticketId) {
            $query->where('ticket_id', $ticketId);
          });
        })
        ->where(function($query) use ($searchQuery) {
          $query->where('name', 'LIKE', "%$searchQuery%")
            ->orWhere('identifier', 'LIKE', "%$searchQuery%")
            ->orWhere('cost', 'LIKE', "%$searchQuery%");
        })
        ->select('id','name','cost','contract_start_date','contract_end_date')
        ->orderBy($sortField,$sortOrder)
        ->paginate($limit);

        return successResponse('', $contracts);
    }

    /**
     * method for contract create blade page
     * @return view
     */
    public function contractCreatePage()
    {   
        if (!(new AgentPermissionPolicy())->contractCreate()) {
            return redirect('dashboard')->with('fails', trans('ServiceDesk::lang.permission-denied'));
        }

        return view('service::contract.create');
    }

    /**
     * method for contract edit blade page
     * @return view
     */
    public function contractEditPage(SdContract $contract)
    {      
        if (!(new AgentPermissionPolicy())->contractEdit()) {
            return redirect('dashboard')->with('fails', trans('ServiceDesk::lang.permission-denied'));
        }
        return view('service::contract.edit', compact('contract'));
    }

    /**
     * method to create or update contract
     * @param CreateUpdateContractRequest $request
     * @return response
     */
    public function createUpdateContract(CreateUpdateContractRequest $request)
    { 
        if(!(new AgentPermissionPolicy())->contractCreate()) {
            return errorResponse(trans('ServiceDesk::lang.permission-denied'));
        }
        $contract = array_merge($request->toArray(), ['owner_id' => Auth::user()->id]);
        $this->makeEmptyAttributesNullable($contract);
        $contract['contract_start_date'] = $request->contract_start_date ? convertDateTimeToUtc($contract['contract_start_date']) : NULL;
        $contract['contract_end_date'] = $request->contract_end_date ? convertDateTimeToUtc($contract['contract_end_date']) : NULL;
        $contractObject = SdContract::updateOrCreate(['id' => $request->id], $contract);
        $agentIds = $request->notify_agent_ids ?: [];
        $contractObject->attachAgents()->sync($agentIds);

        $this->attachEmails($request,$contractObject);
        $approver = $contractObject->approverRelation()->get()->toArray();

        if($approver) {
            $this->sendApproverMail($contractObject);
        }

        if($request->has('user_ids')) {
            $this->attachUser($request,$contractObject);
        }

        $this->attachAssetsToContract($request,$contractObject);
        $this->fillAttachment($request, $contractObject->id);
        $this->createContractThreadToContract($contract,$contractObject);      
        return successResponse(trans('ServiceDesk::lang.contract_saved_successfully'));
    }


    /**
     * method to send approver mail
     * @param $contract
     * return null
     */
    private function sendApproverMail($contract)
    {    
        $phpMailController = new PhpMailController();
        $approver = $contract->approverRelation()->first()->toArray();
        $hash = Crypt::encrypt($approver['email']);
        $hashID = Crypt::encrypt($contract->id);
        $from = $phpMailController->mailfrom('1', '0');
        $to = ['name' => $approver['full_name'], 'email' => $approver['email']];
        $message = ['message' => '','scenario' => 'approve-contract'];
        $templateVariables = ['contract_id' => $contract->id,
          'contract_name' => $contract->name,
          'contract_link' => faveoUrl('service-desk/contracts/' . $contract->id . '/show'),
          'contract_approval_link' => faveoUrl('service-desk/contract-approval/' . $hash .'/'.$hashID),
        ];
        $phpMailController->sendmail($from, $to, $message, $templateVariables);
    }

    /**
     * method to attach Emails to Contracts
     * @param $request
     * @param $contractObject
     * return null
     */
    private function attachEmails($request,$contractObject)
    {   
        $emails = $request->email_ids ?: [];
        $emailIdsObject = [];
        foreach($emails as $email) {
            $emailIdsObject[] = Email::updateOrCreate(['email' => $email], [$email])->id;
        }
        $contractObject->emails()->sync($emailIdsObject);
    }

    /**
     * method to create contract thread
     * @param $contract
     * @param $contractObject
     * return null
     */
    private function createContractThreadToContract($contract,$contractObject)
    {   
        $contractThread = new ContractThreadController();
        $request = new CreateUpdateContractThreadRequest(array_merge($contract, ['contract_id' => $contractObject->id]));
        $contractThread->createUpdateContractThread($request);
    }

    /**
     * method to attach user to contract
     * @param $request
     * @param $contractObject
     * return null
     */
    public function attachUser($request,$contractObject)
    {
        $contract = $request->toArray();
        $user['user_id'] = $contract['user_ids'];
        $user['organization_id']=NULL;
        if($request->has('organization_ids')) {
            $user['organization_id'] = $contract['organization_ids'];
        }
        ContractUserOrganization::updateOrCreate(['contract_id' => $contractObject->id], $user);
    }

    /**
    * method to attach assets to contract
    * @param $request
    * @param $contractObject
    * return null
    */
    public function attachAssetsToContract($request,$contractObject)
    {
        $assetIds = [];
        foreach ((array) $request->asset_ids as $assetId) {
            $assetIds[$assetId] = ['type' => 'sd_contracts'];
        }
        $contractObject->attachAsset()->sync($assetIds);
    }

    /**
     * method to fill attachment
     * @param $request
     * @param $contractId
     * @return null
     */
    private function fillAttachment($request, $contractId)
    {
        $attachment = Attachments::where('owner', 'sd_contracts:' . $contractId)->first();
        if($this->shallDeleteAttachmnet($request, $attachment)) {
            $file = $attachment->value;
            File::delete('uploads/service-desk/attachments/' . $file);
            Attachments::where('owner', 'sd_contracts:' . $contractId)->delete();
            $contract = SdContract::find($contractId);
            ($request->attachment_delete) ? $contract->manuallyLogActivityForPivot($contract, 'attachment','', 'deleted', null, 'contract_attachment', 0, null) : '';
        }
        if ($request->file('attachment')) {
            $attachments[] = $request->file('attachment');
            UtilityController::attachment($contractId, 'sd_contracts', $attachments);
            $contract = SdContract::find($contractId);
            $contract->manuallyLogActivityForPivot($contract, 'attachment', $request->file('attachment'), 'created', null, 'contract_attachment', 0, null);
        }
    }

    /**
     * method to delete attachment
     * @param $request
     * @param $attachment
     * @return boolean
     */
    private function shallDeleteAttachmnet($request, $attachment)
    {
        if($request->attachment_delete)
        {
            return true;
        }
        if($request->file('attachment') && $attachment)
        {
            return true;
        }
    }

    /**
     * method to get contract data based on ID
     * @param contract
     * @return response
     */
    public function getContract(SdContract $contract)
    {   
        $contract = $this->getContractDetails($contract);
        return successResponse('', ['contract' => $contract]);
    }


    /**
     * method to get contract data based on ID
     * @param contract
     * @return response
     */
    private function getContractDetails($contract)
    {   
        $contract = $contract::where('id', $contract->id)->with([
               'contractType:id,name',
               'vendor:id,name,email,primary_contact',
               'approverRelation:id,first_name,last_name,email,profile_pic',
               'licence:id,name',
               'contractStatus:id,name',
               'contractRenewalStatus:id,name',
               'owner:id,first_name,last_name,email,profile_pic',
               'notifyAgents:sd_contract_user.id,first_name,last_name,email,profile_pic',
               'attachAsset:sd_assets.id,name',
               'emails:sd_emails.id,email',
               'attachUser:users.id,first_name,last_name,email,profile_pic,role',
               'attachOrganization:organization.id,name,phone',
             ])
        ->first();
        $this->appendAttachmentsInContract($contract);
        $contract = $this->formatContract($contract->toArray());
        return $contract;
    }

    /** 
     * method to append attachments in contract data
     * @param SdContract $contract
     * @return null
     */
    private function appendAttachmentsInContract(SdContract $contract)
    {
        $contractId = $contract->id;
        $storageControllerInstance = (new StorageController);
        $contract->attachment = Attachments::where('owner', 'sd_contracts:' . $contractId)->get()->transform(function($attachment) use($storageControllerInstance, $contractId) 
        {
            $attachment->size = getSize($attachment->size);
            $attachment->link = implode('', ['/service-desk/download/', $attachment->id, '/sd_contracts:', $contractId, '/attachment']);
            $attachment->icon_url = $storageControllerInstance->getThumbnailUrlByMimeType($attachment->link);

            return $attachment;
        });
    }
    /**
     * method to format contract
     * @param $contract
     * @return contract
     */
    private function formatContract($contract)
    {
        $contract['contractType'] = $contract['contract_type'];
        $contract['notify_to'] = $contract['notify_agents'];
        $contract['approver'] = $contract['approver_relation'];
        $contract = $this->formatNotifyTo($contract);
        if($contract['approver']){
          $contract['approver']['name'] = $contract['approver']['full_name'];
          }
        $contract['owner']['name'] = $contract['owner']['full_name'];
        $contract['status'] = $contract['contract_status'];
        $contract['renewalstatus'] = $contract['contract_renewal_status'];
        $contract['attach_assets'] = $contract['attach_asset'];
        $contract['organization'] = $contract['attach_organization'];
        $contract['user'] = $contract['attach_user'];
        if( $contract['user']){
          $contract= $this->formatAttachedUser($contract);
        }
        foreach ($contract['emails'] as &$email) {
          $email['id']= "EM{$email['id']}";
        }
        $contract['notify_to'] = array_merge($contract['notify_to'],$contract['emails']);
        
        unset($contract['attach_user'],$contract['attach_organization'],$contract['contract_type_id'],$contract['contract_type'],$contract['vendor_id'],$contract['approver_id'],$contract['approver_relation'],$contract['license_type_id'],$contract['contract_status'],$contract['status_id'],$contract['owner_id'],$contract['contract_renewal_status'],$contract['renewal_status_id'],$contract['notify_agents'],$contract['attach_asset'],$contract['approver']['full_name'],$contract['approver']['first_name'],$contract['approver']['last_name'],$contract['approver']['meta_name'],$contract['owner']['full_name'],$contract['owner']['first_name'],$contract['owner']['last_name'],$contract['owner']['meta_name'],$contract['emails']);

        return $contract;
    }

    /**
     * method to format attached user to contract
     * @param $contract
     * @return $contract
     */
    private function formatAttachedUser($contract)
    {
        foreach ($contract['user'] as &$user) {
          $user['name'] = $user['full_name'];
          unset($user['full_name']);
        }
        return $contract;
    }

    /**
     * method to format NotifyTo
     * @param $contract
     * @return $contract
     */
    private function formatNotifyTo($contract)
    {
        foreach ($contract['notify_to'] as &$notifyTo) {
            $notifyTo['name'] = $notifyTo['full_name'];
            $notifyTo['contract_agent_pivot_id'] = $notifyTo['id'];
            $notifyTo['id'] = $notifyTo['pivot']['agent_id'];
            unset($notifyTo['full_name'],$notifyTo['first_name'],$notifyTo['last_name'],$notifyTo['meta_name']);
        }
        foreach ($contract['emails'] as &$email) {
            $email['name'] = $email['email'];
        }
        return $contract;
    }


    /** 
     * method to delete contract
     * @param SdContract $contract
     * @return response
     */
    public function deleteContract(SdContract $contract)
    {
        $contract->delete();
        return successResponse(trans('ServiceDesk::lang.contract_deleted_successfully'));
    }

    /**
    * method to get organization based on user
    * @param $userId
    * @param Request $request
    * @return response
    */
    public function organizationBasedOnUser($userId,Request $request)
    {
        $user = SdUser::where('id', $userId)->first();
        $limit = $request->input('limit') ?: 10;
        $searchString = $request->input('search-query') ?? '';
        if($user->get()->isEmpty()) {
            return errorResponse(trans('ServiceDesk::lang.user_not_found'));
        }
        $organizations = $user->organizations()->where(function($query) use($searchString) {
                            $query
                                ->where('name', 'LIKE', "%$searchString%");
                                })->select('organization.id','name')->paginate($limit)->toArray();
        foreach ($organizations['data'] as &$organization) {
            unset($organization['pivot']);
        }

        return successResponse('',$organizations);
    }

    /**
     * method for contract index page
     * @return view
     */
    public function contractIndexPage()
    {
        if (!(new AgentPermissionPolicy())->contractsView()) {
            return redirect('dashboard')->with('fails', trans('ServiceDesk::lang.permission-denied'));
        }
      
        return view('service::contract.index');
    }

    /**
     * method to attach asset
     * @param Request $request
     * @return response
     */
    public function attachAssets(Request $request)
    {   
        if(!(new AgentPermissionPolicy())->assetAttach()) {
            return errorResponse(trans('ServiceDesk::lang.permission-denied'));
        }
        $contract = SdContract::where('id', $request->contract_id);

        if ($contract->get()->isEmpty()) {
            return errorResponse(trans('ServiceDesk::lang.contract_not_found'));
        }
        $assetIds = [];
        if ($request->has('asset_ids')) {
            foreach ((array) $request->asset_ids as $assetId) {
                $assetIds[$assetId] = ['type' => 'sd_contracts'];
            }
        }
        $contract->first()->attachAsset()->attach($assetIds);
        return successResponse(trans('ServiceDesk::lang.asset_attached_successfully'));
    }

    /**
     * method to detach assets
     * @param $contract
     * @param $asset
     * @return response
     */
    public function detachAsset(SdContract $contract, SdAssets $asset)
    {   
        if(!(new AgentPermissionPolicy())->assetDetach()) {
            return errorResponse(trans('ServiceDesk::lang.permission-denied'));
        }
        $contract->attachAsset()->wherePivot('asset_id', $asset->id)->detach();
        return successResponse(trans('ServiceDesk::lang.asset_detached_successfully'));
    }

    /**
     * method to renew contract
     * @param $contract
     * @param RenewContractRequest $request
     * @return response
     */
    public function renewContract(SdContract $contract, RenewContractRequest $request)
    {
        $contract->update(['cost' => $request->cost,
          'approver_id' => $request->approver_id,
          'renewal_status_id' => 7,
        ]);
        $this->createContractThread(['contract_id' => $contract->id,
          'contract_start_date' => $request->contract_start_date,
          'contract_end_date' => $request->contract_end_date,
        ]);

        $approver = $contract->approverRelation()->get()->toArray();
        if($approver) {
            $this->sendApproverMail($contract);
        }

        return successResponse(trans('ServiceDesk::lang.contract_renewed_successfully'));
    }

    /**
     * method to create contract Thread
     * @param $contract
     * @return null
     */
    private function createContractThread($contract)
    {   
        $contractThread = new ContractThreadController();
        $auth = Auth::user();
        $request = SdContract::where('id', $contract['contract_id'])->select('status_id', 'cost', 'approver_id', 'renewal_status_id')->first()->toArray();
        $existingThread = SdContractThread::where([['contract_start_date', $contract['contract_start_date'], ['contract_end_date', $contract['contract_end_date']]]])->first();
        if (!is_null($existingThread)) {
            $request['id'] = $existingThread->id;
        }
        $request['contract_id'] = $contract['contract_id'];
        $request['contract_start_date'] = $contract['contract_start_date'];
        $request['contract_end_date'] = $contract['contract_end_date'];
        $request['owner_id'] = $auth->id;
        $request = new CreateUpdateContractThreadRequest($request);
        $contractThread->createUpdateContractThread($request);
    }

    /**
     * method to approve contract
     * @param $contract
     * @return response
     */
    public function approveContract(SdContract $contract, Request $request)
    {   
        $auth = Auth::user();
        $contractThread = SdContractThread::where('contract_id', $contract->id);
        $renewalStatusId = $contract->renewal_status_id;
        $contractThreadWithoutRenewal = $contractThread->orderBy('id', 'asc')->first();
        if($request->purpose_of_approval)
        {
            $contract->update(['purpose_of_approval' => $request->purpose_of_approval]);
        }
        switch($contract->renewal_status_id)
        {
            case null:
                $contract->update(['status_id' => 2]);
                $contractThreadWithoutRenewal->update(['status_id' => 2]);
                break;
            case 10:
                $contract->update(['renewal_status_id' => 12]);
                break;
            case 7:
                $contract->update(['renewal_status_id' => 8]);
                break;
            default:
                break;
        }
        $this->makeContractApproved($contract,$contractThreadWithoutRenewal,$contractThread,$renewalStatusId);
        $this->makeContractStatusExpired();
        $this->makeContractStatusActive();
        return successResponse(trans('ServiceDesk::lang.contract_approved_successfully'));
    }

    /**
     * method to make contract status approved and expired
     * @param $contract
     * @param $contractThreadWithoutRenewal
     * @param $contractThread
     * @return null
     */
    private function makeContractApproved($contract, $contractThreadWithoutRenewal, $contractThread, $renewalStatusId)
    {
        $systemDate = convertDateTimeToUtc(carbon(faveoDate())->toDateTimeString());
        if (($contract->contract_start_date <= $systemDate) && ($contract->contract_end_date >= $systemDate) && ($contract->status_id == 2)) {
            $contract->update(['status_id' => 3]);
            $contractThreadWithoutRenewal->update(['status_id' => 3]);
        }

        if ($systemDate > $contract->contract_end_date) {
            $contract->update(['status_id' => 5]);
            $contractThreadWithoutRenewal->update(['status_id' => 5]);
        }

        $this->sendMailAndNotify($contract);
        $contractThread = $contractThread->whereIn('renewal_status_id', [7, 10])->first();
        if ($contractThread) {
            ($renewalStatusId == 7) ? $contractThread->update(['renewal_status_id' => 8]) : $contractThread->update(['renewal_status_id' => 12]);
        }
    }

    /**
     * method to send approved and rejected contract in app notification and mail
     * @param $contract
     * @return null
     */
    private function sendMailAndNotify($contract)
    {   
        $phpMailController = new PhpMailController();
        $approval_status = ($contract->status_id == 6) ? 'rejected' : 'approved';
        $approver = $contract->approverRelation()->first()->toArray();
        $registerNotifications[$approval_status . '_contract'] = [
                        'model' => $contract,
                        'userid'=> $contract->owner_id,
                        'from' => $phpMailController->mailfrom('1', '0'),
                        'message' => ['subject' => null, 'scenario' => $approval_status . '-contract'],
                        'variable' => ['contract_id' => $contract->id, 'contract_name' => $contract->name, 'approver_name' =>
                          $approver['full_name'], 'contract_link' =>faveoUrl('service-desk/contracts/' . $contract->id . '/show')]
                    ];
        if ($approval_status == 'rejected') {
            $registerNotifications[$approval_status . '_contract']['variable']['contract_reason_rejection'] = $contract->purpose_of_rejection;
        }
        $notification[] = $registerNotifications;
        $notify = new Notify();
        $notify->setDetails($notification);
    }

    /**
     * method to change contract status to expired
     * @return null
     */
    public function makeContractStatusExpired()
    {
        try {
            $contracts = new SdContract();
            $contracts = $contracts->get();
            $systemDate =  convertDateTimeToUtc(carbon(faveoDate())->toDateTimeString());
            foreach ($contracts as $contract) {
                if (($contract->contract_end_date <= $systemDate) && (($contract->status_id == 3) || ($contract->status_id == 5)))
                {
                    $contractThread = SdContractThread::where([['contract_id', $contract->id], ['contract_start_date', '<=', $systemDate], ['contract_end_date', '>=', $systemDate]])->whereIn('renewal_status_id', [8, 12])->first();
                    if ($contractThread) {
                        $contract->update(['status_id' => 3, 'renewal_status_id' => null, 'contract_start_date' => $contractThread->contract_start_date, 'contract_end_date' => $contractThread->contract_end_date]);
                    }
                    else if($contract->renewal_status_id != 8) {
                        $contract->update(['status_id' => 5, 'renewal_status_id' => null]);
                    }
                }
            }
        } catch (Exception $e) {
              Logger::exception($e);
        }
    }

    /**
     * method to change contract status to active
     * @return null
     */
    public function makeContractStatusActive()
    {
        try {
            $contracts = new SdContract();
            $contracts = $contracts->get();
            foreach ($contracts as $contract) {
                $systemDate =  convertDateTimeToUtc(carbon(faveoDate())->toDateTimeString());
                if (($contract->contract_start_date <= $systemDate) && ($contract->status_id == 2))
                {
                    $contract->update(['status_id' => 3]);
                }
            }
        } catch (Exception $e) {
              Logger::exception($e);
        }
    }


    /**
     * method to extend contract
     * @param $contract
     * @param ExtendContractRequest $request
     * @return response
     */
    public function extendContract(SdContract $contract, ExtendContractRequest $request)
    {
        $contract->update(['cost' => $request->cost,
          'approver_id' => $request->approver_id,
          'renewal_status_id' => 10,
        ]);

        $contractThread = SdContractThread::where('contract_id', $contract->id)->whereIn('renewal_status_id', [8, 12])->orderBy('id', 'desc')->first();
        $extendStartDate = !is_null($contractThread) ? $contractThread->contract_end_date : $contract->contract_end_date;
        if ($contract->contract_end_date != $request->contract_end_date){
            $this->createContractThread(['contract_id' => $contract->id,
            'contract_start_date' => $extendStartDate,
            'contract_end_date' => $request->contract_end_date
          ]);
        }

        $approver = $contract->approverRelation()->get()->toArray();
        if($approver) {
            $this->sendApproverMail($contract);
        }
      
        return successResponse(trans('ServiceDesk::lang.contract_extended_successfully'));
    }

    /**
     * method to terminate contract
     * @param $contract
     * @return response
     */
    public function terminateContract(SdContract $contract)
    {   
        $contractThread = new ContractThreadController();
        $auth = Auth::user();

        if ($contract->owner_id != $auth->id) {
            return errorResponse(trans('ServiceDesk::lang.you_dont_have_access_to_terminate'));

        }
        if ($contract->status_id != 3) {
            return errorResponse(trans('ServiceDesk::lang.you_cannot_terminate'));
        }
        $contract->update(['status_id' => 4, 'renewal_status_id' => null]);
        $contractThread->deletecontractThreads($contract->id);

        return successResponse(trans('ServiceDesk::lang.contract_terminated_successfully'));
    }

    /**
     * method to reject contract
     * @param $contract
     * @param RejectContractRequest $request
     * @return response
     */
    public function rejectContract(SdContract $contract, RejectContractRequest $request)
    {
        $auth = Auth::user();
        $contractThread = SdContractThread::where('contract_id', $contract->id);
        
        $renewalStatusId = $contract->renewal_status_id; 
        $contractThreadWithoutRenewal = $contractThread->orderBy('id', 'asc')->first();
        $contract->update(['purpose_of_rejection' => $request->purpose_of_rejection]);

        switch($contract->renewal_status_id)
        {
            case null:
                $contract->update(['status_id' => 6, 'purpose_of_rejection' => $request->purpose_of_rejection]);
                $contractThreadWithoutRenewal->update(['status_id' => 6]);
                break;
            case 7:
                $contract->update(['renewal_status_id' => 9, 'purpose_of_rejection' => $request->purpose_of_rejection]);
                break;
            case 10:
                $contract->update(['renewal_status_id' => 11, 'purpose_of_rejection' => $request->purpose_of_rejection]);
                break;
            default:
                break;
        }
        $this->makeContractExpiredAfterRejection($contract, $contractThreadWithoutRenewal, $contractThread);
        $this->makeContractStatusExpired();
        $this->makeContractStatusActive();

        return successResponse(trans('ServiceDesk::lang.contract_rejected_successfully'));
    }

    /**
     * method to make contract status expired after rejected
     * @param $contract
     * @param $contractThreadWithoutRenewal
     * @param $contractThread
     * @return null
     */
    private function makeContractExpiredAfterRejection($contract, $contractThreadWithoutRenewal, $contractThread)
    {
        $systemDate = convertDateTimeToUtc(carbon(faveoDate())->toDateTimeString());
        if ($systemDate > $contract->contract_end_date) {
            $contract->update(['status_id' => 5]);
            $contractThreadWithoutRenewal->update(['status_id', 5]);
        }

        $this->sendMailAndNotify($contract);
        $contractThread = $contractThread->whereIn('renewal_status_id', [7, 10])->first();

        if ($contractThread) {
            ($renewalStatusId == 7) ? $contractThread->update(['renewal_status_id' => 9]) : $contractThread->update(['renewal_status_id' => 11]);
        }
    }

    /**
     * method to set expiry reminder of contract
     * @param $contract
     * @param ExpiryReminderContractRequest $request
     * @return response
     */
    public function expiryReminderContract(SdContract $contract, ExpiryReminderContractRequest $request)
    {
        $contract->update(['notify_before' => $request->notify_before,
        ]);

        $agentIds = $request->notify_agent_ids ?: [];
        $contract->attachAgents()->sync($agentIds);
      
        $this->attachEmails($request,$contract);
        return successResponse(trans('ServiceDesk::lang.contract_saved_successfully'));
    }

    /**
     * method to get contract activity log
     * @param $contractId
     * @param Request $request
     * @return response
     */
    public function getContractActivityLog($contractId, Request $request)
    {
        $contract = SdContract::where('id', $contractId);
        $limit = $request->input('limit') ?: 10;
        $sortField = $request->input('sort-field') ?: 'id';
        $sortOrder = $request->input('sort-order') ?: 'desc';
        $page = $request->page ?: 1;

        $contractActivityLogs = ActivityBatch::with(['activity.creator:id,first_name,last_name,user_name,email,profile_pic'])->whereHas('activity', function($query) use($contractId)
            {
                $query->where([['source_type', 'App\Plugins\ServiceDesk\Model\Contract\SdContract'], ['source_id', $contractId]]);
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate($limit)
            ->toArray();

        $this->formatContractActivityLog($contractActivityLogs);

        return successResponse('', ['contract_activity_logs' => $contractActivityLogs]);
    }

    /**
     * method to format contract activity log
     * @param array $contractActivityLogs
     * @return null
     */
    private function formatContractActivityLog(array &$contractActivityLogs)
    {
        $activity = '';
        $contractActivity = [];
        foreach ($contractActivityLogs['data'] as $activityLogs) {
            $activityLogs = $activityLogs['activity'];
            $activity = ($activityLogs[0]['event_name'] == 'created' && $activityLogs[0]['log_name'] == 'contract') ? "created a new contract <b>(#CNTR-{$activityLogs[0]['source_id']})</b>, " : '';
            if ($activityLogs[0]['log_name'] == 'contract_pivot') {
                $this->formatContractPivotActivityLog($activityLogs, $contractActivity);
                continue;
            }
            $this->formatActivity($activityLogs, $activity);
            $activity = rtrim($activity, ', ');
            $contractActivity[] = ['id' => $activityLogs[0]['id'], 'creator' => $activityLogs[0]['creator'], 'name' => $activity, 'created_at' => $activityLogs[0]['created_at']];
        }
        $contractActivityLogs['data'] = $contractActivity;
    }

    /** 
     * method to format activity
     * @param array $activityLogs
     * @param $activity
     * @return null
     */
    private function formatActivity(array &$activityLogs, string &$activity)
    {
        foreach ($activityLogs as &$contractActivityLog) {
            $tag = $contractActivityLog['field_or_relation'] == 'description' ? 'br' : 'b';
            switch($contractActivityLog['field_or_relation'])
            {
                case 'owner':
                    $owner = json_decode($contractActivityLog['final_value']);
                    $ownerUrl = Config::get('app.url')."/user/{$owner->id}";
                    $contractActivityLog['final_value'] = "<a href={$ownerUrl}>{$owner->full_name}</a>";
                    break;
                case 'vendor':
                    $vendor = json_decode($contractActivityLog['final_value']);
                    $vendorUrl = Config::get('app.url')."/service-desk/vendor/{$vendor->id}/show";
                    $contractActivityLog['final_value'] = "<a href={$vendorUrl}>{$vendor->name}</a>"; 
                    break;
                case 'approverRelation':
                    $approver = json_decode($contractActivityLog['final_value']);
                    $approverUrl = Config::get('app.url')."/user/{$approver->id}";
                    $contractActivityLog['final_value'] = "<a href={$approverUrl}>{$approver->full_name}</a>";
                    $contractActivityLog['field_or_relation'] = 'approver';
                    break;
                case 'contract_start_date':
                    $contractActivityLog = $this->formatContractDateActivityLog($contractActivityLog);
                    break;
                 case 'contract_end_date':
                    $contractActivityLog = $this->formatContractDateActivityLog($contractActivityLog);
                    break;   
                case 'attachment':
                    $this->formatContractAttachmentActivityLog($contractActivityLog, $activity);
                    break;
            }
            if($contractActivityLog['log_name'] != 'contract_attachment')
            {   
                if(!$contractActivityLog['final_value'])
                {
                    $contractActivityLog['final_value'] = 'empty';
                }
                $fieldOrRelation = implode(' ', array_map('strtolower', preg_split('/(?=[A-Z])/', str_replace("_"," ",$contractActivityLog['field_or_relation']))));
                $activity = implode('',[$activity ,'set ' ,$fieldOrRelation , " as <{$tag}>{$contractActivityLog['final_value']}</{$tag}>, "]);
            }
        }
    }

    /**
     * method to format contract start date and end date activity log
     * @param $contractActivityLog
     * @return $contractActivityLog
     */
    private function formatContractDateActivityLog($contractActivityLog)
    {
        $contractActivityLog['final_value'] = strtotime($contractActivityLog['final_value']) ? faveoDate($contractActivityLog['final_value'], 'F j, Y, g:i a', agentTimeZone()) : $contractActivityLog['final_value'];
        return $contractActivityLog;
    }

    /**
     * method to format contract attachment activity log
     * @param Activity $contractActivityLog
     * @param array $activity
     * @return null
     */
    private function formatContractAttachmentActivityLog($contractActivityLog, &$activity)
    {
        $added = 'added ';
        if($contractActivityLog['event_name'] == 'deleted')
        {   
            $added = 'deleted ';
        }
        $fieldOrRelation = implode(' ', array_map('strtolower', preg_split('/(?=[A-Z])/', str_replace("_"," ",$contractActivityLog['field_or_relation']))));
        $activity = implode('',[$activity , $added ,$fieldOrRelation]);
    }

    /**
     * method to format contract pivot activity log
     * @param Activity $contractActivityLogs
     * @param array $contractActivity
     * @return null
     */
    private function formatContractPivotActivityLog($contractActivityLogs, array &$contractActivity)
    { 
        switch($contractActivityLogs[0]['field_or_relation'])
        {
            case 'notifyTo':
                return $this->formatAgentsAttachedToContractActivityLog($contractActivityLogs, $contractActivity);
            case 'emails':
                return $this->formatEmailsAttachedToContractActivityLog($contractActivityLogs, $contractActivity);
            case 'attachAsset':
                return $this->formatAssetsAttachedToContractActivityLog($contractActivityLogs, $contractActivity);
            default:
                return null;
        }
    }

    /**
     * method to format agents attached to contract activity log
     * @param Activity $contractActivityLogs
     * @param array $contractActivity
     * @return null
     */
    private function formatEmailsAttachedToContractActivityLog($contractActivityLogs, array &$contractActivity)
    {
        $emails = '';
        foreach ($contractActivityLogs as $contractActivityLog) {
            $email = Email::find($contractActivityLog['final_value']);
            $emails = $emails."<b>{$email->email}</b></a>, ";
        }
        $emails = rtrim($emails, ', ');
        $format = 'set';
        if($contractActivityLog['event_name'] == 'detached'){
            $format  = 'unset';
        }
        $activity = "{$format} notifyTo $emails";
        $contractActivity[] = ['id' => $contractActivityLogs[0]['id'], 'creator' => $contractActivityLogs[0]['creator'], 'name' => $activity, 'created_at' => $contractActivityLogs[0]['created_at']];
    }


    /**
     * method to format agents attached to contract activity log
     * @param Activity $contractActivityLogs
     * @param array $contractActivity
     * @return null
     */
    private function formatAgentsAttachedToContractActivityLog($contractActivityLogs, array &$contractActivity)
    {
        $agentNames = '';
        foreach ($contractActivityLogs as $contractActivityLog) {
            $agent = SdUser::find($contractActivityLog['final_value']);
            $agentNames = $agentNames . '<a href='.faveoUrl("user/{$agent->id}").'>'."<b>({$agent->id}) {$agent->full_name}</b></a>, ";
        }
        $agentNames = rtrim($agentNames, ', ');
        $format = 'set';
        if($contractActivityLog['event_name'] == 'detached'){
            $format  = 'unset';
        }
        $activity = "{$format} {$contractActivityLogs[0]['field_or_relation']}  $agentNames";
        $contractActivity[] = ['id' => $contractActivityLogs[0]['id'], 'creator' => $contractActivityLogs[0]['creator'], 'name' => $activity, 'created_at' => $contractActivityLogs[0]['created_at']];
    }

    /**
     * method to format assets attached to contract activity log
     * @param Activity $contractActivityLogs
     * @param array $contractActivity
     * @return null
     */
    private function formatAssetsAttachedToContractActivityLog($contractActivityLogs, array &$contractActivity)
    {
        $assetNames = '';
        foreach ($contractActivityLogs as $contractActivityLog) {
            $asset = SdAssets::find($contractActivityLog['final_value']);
            $assetNames = $assetNames . '<a href='.faveoUrl("service-desk/assets/{$asset->id}/show").'>'."<b>(#AST-{$asset->id}) {$asset->name}</b></a>, ";
        }
        $assetNames = rtrim($assetNames, ', ');
        $activity = "{$contractActivityLogs[0]['event_name']} asset $assetNames";
        $contractActivity[] = ['id' => $contractActivityLogs[0]['id'], 'creator' => $contractActivityLogs[0]['creator'], 'name' => $activity, 'created_at' => $contractActivityLogs[0]['created_at']];
    }

    /**
     * method to send contract expiry email to selected agents and emails
     * @return null
     */
    public function sendContractNotificationExpiry()
    {
        try {
            $contracts = new SdContract();
            $delayInSeconds = 0;
            $contractWithAgents = $contracts->with('notifyAgents')->has('notifyAgents')->get()->toArray();
            $contractWithEmails = $contracts->with('emails')->has('emails')->get()->toArray();
            $contractWithAgents = array_merge($contractWithAgents,$contractWithEmails);
            $systemDate =  date('Y-m-d', strtotime(convertDateTimeToUtc(carbon(faveoDate())->toDateTimeString())));
            foreach ($contractWithAgents as $contract) {
                if(array_key_exists('emails', $contract)) {
                    $contract['notify_agents'] = $contract['emails'];
                }
                $contractNotifyExpiry = date('Y-m-d', strtotime($contract['contract_end_date'] . '-' . $contract['notify_before'] . ' day'));
                if ($contractNotifyExpiry == $systemDate) {
                    foreach ($contract['notify_agents'] as $agent) {
                    $notifyExpiryEmailJOb = (new ContractNotificationExpiry($agent, $contract))
                    ->delay(Carbon::now()->addSecond($delayInSeconds));
                    dispatch($notifyExpiryEmailJOb);
                    $delayInSeconds += 20;
                    }
                }
            }

        } catch (Exception $e) {
              Logger::exception($e);
        }
    }

    /**
     * method to get notifiedTo agents and emails of contract
     * @param $contract
     * @param $request
     * @return response
     */
    public function getNotifiedTo(SdContract $contract, Request $request)
    {
        $searchString = $request->input('search-query') ?? '';
        $limit = $request->input('limit') ?: 10;
        $sortField = $request->input('sort-field') ?: 'updated_at';
        $sortOrder = $request->input('sort-order') ?: 'desc';
        
        $agents = $contract->attachAgents()
            ->where(function ($query) use ($searchString) {
                        $query
                    ->where('first_name', 'LIKE', "%$searchString%")
                    ->orWhere('last_name', 'LIKE', "%$searchString%")
                    ->orWhere('email', 'LIKE', "%$searchString%");
                })
            ->orderBy($sortField,$sortOrder)
            ->paginate($limit)->toArray();
        $emails = $contract->emails()
            ->where(function ($query) use ($searchString) {
                        $query
                    ->where('email', 'LIKE', "%$searchString%");
                })
            ->orderBy($sortField,$sortOrder)
            ->paginate($limit)->toArray();
        $emails['emails'] = $emails['data'];
        unset($emails['data']); 
        $agents = $this->getAgentsFormatContract($agents);
        $agents = array_merge($agents,$emails);
        $agents = $this->getEmailsFormatContract($agents);
        return successResponse('', $agents);
      }

    /**
     * method to format contract associated emails
     * @param $agents
     * @return $agents
     */
    private function getEmailsFormatContract($agents)
    {
        foreach ($agents['emails'] as &$email) {
            $email['id']= "EM{$email['id']}";
            $email['name'] = $email['email'];
        }
        return $agents;
    }

    /**
     * method to format contract associated agents
     * @param $agents
     * @return $agents
     */
    private function getAgentsFormatContract($agents)
    {
        $agents['agents'] = [];
        foreach ($agents['data'] as $agentData) {
            $agent = [
                'id' => $agentData['id'],
                'email' => $agentData['email'],
                'profile_pic' =>  $agentData['profile_pic'],
                'name' => $agentData['full_name'],
            ];
            array_push($agents['agents'], $agent);
        }
        unset($agents['data']);

        return $agents;
    }

    /**
     * method to approve or reject contract through email hash
     *
     * Note: Approval by hash should only processed when any one of the below it true
     * 1: Contract is in "Draft" status with id 1
     * 2: Contract is Requested for renewal that is "renewal_status_id" must be 7
     * 3: Contract is Requested for enxtension that is "renewal_status_id" must be 10
     *
     * @param $hash(encrpted approver email)
     * @param $contractId(encrpted contractId)
     * @param $request
     * @return response
     */
    public function contractApprovalActionByHash($hash, $contractId, Request $request )
    {   
        $contractId = Crypt::decrypt($contractId);
        $email = Crypt::decrypt($hash);
        $contract = SdContract::where('id',$contractId)->first();
        if($contract->status_id == 1 || in_array($contract->renewal_status_id, [7,10])) {
            $approver = $contract->approverRelation()->first()->toArray();
            if($email != $approver['email'])
            {   
                return errorResponse(trans('lang.invalid_hash'));
            }
            $message = $this->contractApprovalOrRejection($contract,$approver,$request);
            return $message;
        }

        return errorResponse(trans('lang.hash_expired'));
    }

    /**
     * method to approve or reject contract
     * @param $request
     * @return $message
     */
    private function contractApprovalOrRejection($contract,$approver,$request)
    {
        switch($request->actionType)
        {
            case 'approve':
                $message = $this->approveContract($contract,$request);
                break;
            case 'reject':
                $rejectObject = new RejectContractRequest();
                $rejectObject->purpose_of_rejection=$request->purpose_of_rejection;
                $message = $this->rejectContract($contract, $rejectObject);
                break;
        }
        DB::table('sd_activity_logs')->whereNull(['causer_id','causer_type'])->where('source_id', $contract->id)
        ->update(['causer_id' => $approver['id'],
                'causer_type' => 'App\User'
                ]);
        return $message;
    }

    /**
     * method to approve or reject contract through email hash
     *
     * Note: Approval by hash should only processed when any one of the below it true
     * 1: Contract is in "Draft" status with id 1
     * 2: Contract is Requested for renewal that is "renewal_status_id" must be 7
     * 3: Contract is Requested for enxtension that is "renewal_status_id" must be 10
     *
     * @param $hash(encrpted approver email)
     * @param $contractId(encypted contractId)
     * @return response
     */
    public function contractDetailsByHash($hash, $contractId)
    {   
        $contractId = Crypt::decrypt($contractId);
        $email = Crypt::decrypt($hash);
        $contract = SdContract::where('id',$contractId)->first();
        if($contract->status_id == 1 || in_array($contract->renewal_status_id, [7,10])) {
            $approver = $contract->approverRelation()->first()->toArray();
            if($email != $approver['email'])
            {   
                return errorResponse(trans('lang.invalid_hash'));
            }
            $contract = $this->getContractDetails($contract);
            return successResponse('', ['contract' => $contract]);
        }

        return errorResponse(trans('lang.hash_expired'));
    }

}
