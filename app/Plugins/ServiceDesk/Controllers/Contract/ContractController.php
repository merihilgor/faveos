<?php

namespace App\Plugins\ServiceDesk\Controllers\Contract;

use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use App\Plugins\ServiceDesk\Model\Contract\SdContract;
use App\Plugins\ServiceDesk\Model\Contract\ContractType;
use App\Plugins\ServiceDesk\Model\Contract\License;
use App\Plugins\ServiceDesk\Model\Vendor\SdVendors;
use App\Plugins\ServiceDesk\Requests\CreateContractRequest;
use Exception;
use Lang;
use DB;
use File;
use Illuminate\Http\Request;
use App\Plugins\ServiceDesk\Controllers\Library\UtilityController;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\Plugins\ServiceDesk\Model\Assets\CommonAssetRelation;
use App\User;
use App\Plugins\ServiceDesk\Model\Contract\SdContractStatus;
use App\Plugins\ServiceDesk\Model\Contract\SdContractSdUser;
use App\Http\Controllers\Common\PhpMailController;
use App\Http\Controllers\Agent\helpdesk\Notifications\NotificationController as Notify;
use App\Plugins\ServiceDesk\Policies\AgentPermissionPolicy;
use App\Plugins\ServiceDesk\Request\Contract\RenewContractRequest;
use App\Plugins\ServiceDesk\Request\Contract\ExtendContractRequest;
use App\Plugins\ServiceDesk\Request\Contract\RejectContractRequest;
use App\Plugins\ServiceDesk\Controllers\Contract\ContractThreadController;
use App\Plugins\ServiceDesk\Request\Contract\CreateUpdateContractThreadRequest;
use App\Plugins\ServiceDesk\Model\Contract\SdContractThread;
use Auth;
use App\Plugins\ServiceDesk\Jobs\ContractNotificationExpiry;
use Carbon\Carbon;
use App\Plugins\ServiceDesk\Model\Common\SdOrganization;
use Illuminate\Database\Eloquent\Collection;
use Redirect;

class ContractController extends BaseServiceDeskController {

    private $phpMailController;
    private $contractThread;
    private $contractThreadRequest;

    public function __construct()
    {   
        $this->middleware('role.agent');
        $this->phpMailController = new PhpMailController();
        $this->agentPermission = new AgentPermissionPolicy();
        $this->contractThread = new ContractThreadController(); 
    }

    /**
     * 
     * @return type
     */
    public function index() {
         
        if (!$this->agentPermission->contractsView()) {
            return redirect('dashboard')->with('fails', Lang::get('ServiceDesk::lang.permission-denied'));
        }
        $sdPolicy = $this->agentPermission;
        $sdcontracts = SdContract::all();
        return view('service::contract.index', compact('sdcontracts','sdPolicy'));
    }

    /**
     * 
     * @return type
     */
    public function getContracts() {
        try {
            $contract = new SdContract();
            $contracts = $contract->select('id', 'name', 'description', 'cost', 'contract_type_id', 'vendor_id', 'license_type_id', 'licensce_count', 'notify_expiry', 'contract_start_date', 'contract_end_date', 'created_at', 'status_id', 'renewal_status_id')->get();
  
            return \DataTables::Collection($contracts)
                            ->addColumn('c_number', function($model) {
                                return "<b>#CNTR-".$model->id."</b>";
                            })
                            ->addColumn('name', function($model) {
                                return "<span title='".$model->name."'>". str_limit($model->name, 20)."</span>";
                            })
                            ->addColumn('contract_status', function($model) {
                                $statusName =  is_null($model->status_id) ? null : $model->contractStatus()->first()->name;
                                return ucfirst($statusName);
                            })
                            ->addColumn('renewal_status', function($model) {
                                $renewalStatusName =  is_null($model->renewal_status_id) ? null : $model->contractRenewalStatus()->first()->name;
                                return ucfirst(str_limit($renewalStatusName, 15));
                            })
                            ->addColumn('expiry', function($model) {
                                $expiry = ($model->contract_end_date && $model->contract_end_date != "--" ) ? faveoDate($model->contract_end_date) : '--';
                                return $expiry;
                            })
                            ->addColumn('action', function($model) {
                                $url = url('service-desk/contracts/' . $model->id . '/delete');
                                $delete =  $this->agentPermission->contractDelete() ? \App\Plugins\ServiceDesk\Controllers\Library\UtilityController::deletePopUp($model->id, $url, "Delete $model->subject") :'';
                                $edit = $this->agentPermission->contractEdit() && (((($model->status_id == 1) || is_null($model->status_id))&& is_null($model->renewal_status_id))) ? "<a href=" . url('service-desk/contracts/' . $model->id . '/edit') . " class='btn btn-primary btn-xs'>  <i class='fa fa-edit' style='color:white;'>&nbsp;".Lang::get('ServiceDesk::lang.edit')."</i></a> &nbsp;" : '';
                                $view = $this->agentPermission->contractsView() ? " <a href=" . url('service-desk/contracts/' . $model->id . '/show') . " class='btn btn-primary btn-xs'><i class='fa fa-eye' style='color:white;'>&nbsp;&nbsp;  </i>".Lang::get('ServiceDesk::lang.view')."</a> &nbsp;" : '';
                                return $edit.$delete.$view;
                            })
                            ->rawColumns(['c_number','name','cost','expiry','action'])
                            ->make();

        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @return type
     */
    public function create() {
        if (!$this->agentPermission->contractCreate()) {
            return redirect('dashboard')->with('fails', Lang::get('ServiceDesk::lang.permission-denied'));
        }
        $contract_type_ids = ContractType::pluck('name', 'id')->toArray();
        $license_type_ids = License::pluck('name', 'id')->toArray();
        $vendor_ids = SdVendors::where('status',1)->pluck('name', 'id')->toArray();
        $contractStatus = SdContractStatus::where('id', 1)->pluck('name', 'id')->toArray();
        $contractStatus[1] = ucwords($contractStatus[1]);
        
        return view('service::contract.create', compact('contract_type_ids','license_type_ids', 'vendor_ids', 'contractStatus'));
    }

    /**
     * 
     * @param \App\Plugins\ServiceDesk\Requests\CreateContractRequest $request
     * @return type
     */
    public function handleCreate(CreateContractRequest $request) {
      $auth = Auth::user();
      $contract = array_merge($request->toArray(), ['owner_id' => $auth->id]);
      $this->makeEmptyAttributesNullable($contract);
      $sd_contracts = new SdContract();
      $sd_contracts = $sd_contracts->create($contract);
      
      if($request->has('asset')){
               $assetId = $request->input('asset');
               UtilityController::commonAssetAttach($assetId, $sd_contracts->id, $type="sd_contracts");
      }

      if($request->has('agent_ids')){
          $agentIds = $request->input('agent_ids');
          foreach ($agentIds as $key => $value) {
              SdContractSdUser::create(['contract_id' => $sd_contracts->id, 'agent_id' => $value]);
          }
      }

      if($request->contract_start_date){
        $start_date = \Carbon\Carbon::createFromFormat(dateTimeFormat(), $request->contract_start_date, timezone())->setTimezone('UTC');
        $contract_start_date =  $start_date->toDateTimeString();
        $sd_contracts->contract_start_date =$contract_start_date;
        $sd_contracts->save();
       }

      if($request->contract_end_date){
        $end_date = \Carbon\Carbon::createFromFormat(dateTimeFormat(), $request->contract_end_date, timezone())->setTimezone('UTC');
        $contract_end_date =  $end_date->toDateTimeString();
        $sd_contracts->contract_end_date = $contract_end_date;
        $sd_contracts->save();
      }
      $this->sendApproverMail($sd_contracts);
      \App\Plugins\ServiceDesk\Controllers\Library\UtilityController::attachment($sd_contracts->id, 'sd_contracts', $request->file('attachments'));
      $request->merge(['contract_start_date' => $contract_start_date]);
      $request->merge(['contract_end_date' => $contract_end_date]);
      $request->merge(['owner_id' => $auth->id]);
      $request = new CreateUpdateContractThreadRequest(array_merge($request->toArray(), ['contract_id' => $sd_contracts->id]));
      $this->contractThread->createUpdateContractThread($request);
      return \Redirect::route('service-desk.contract.index')->with('message', Lang::get('ServiceDesk::lang.contract_created_successfully'));
    }

  /**
   * Function to send approver mail
   * @param SdContract $contract
   * @return
   */
  private function sendApproverMail($contract)
  {
    $approver = $contract->approverRelation()->first()->toArray();
    $from = $this->phpMailController->mailfrom('1', '0');
    $to = ['name' => $approver['full_name'], 'email' => $approver['email']];
    $message = ['message' => '','scenario' => 'approve-contract'];
    $templateVariables = ['contract_id' => $contract->id,
      'contract_name' => $contract->name,
      'contract_link' => faveoUrl('service-desk/contracts/' . $contract->id . '/show')
    ];
    $this->phpMailController->sendmail($from, $to, $message, $templateVariables);
  }

    /**
     * method to delete contract
     * @param type $id
     * @return type
     */
    public function delete($id) {
        $sdcontract = SdContract::find($id);
        
        if (!$sdcontract) {
          return errorResponse(Lang::get('ServiceDesk::lang.contract_not_found'));
        }
  
        $sdcontract->delete();

        return Redirect::route('service-desk.contract.index')->with('message', Lang::get('ServiceDesk::lang.contract_deleted_successfully'));
    }

    /**
     * 
     * @param type $id
     * @return type
     */
    public function edit($id) {
    
    try{
        $contract = SdContract::where('id', '=', $id)->first();
        if ((!$this->agentPermission->contractEdit()) || ($contract['status_id'] != 1)) {
            return redirect('dashboard')->with('fails', Lang::get('ServiceDesk::lang.permission-denied'));
        } 
        $contract_type_ids = ContractType::pluck('name', 'id')->toArray();
        $license_type_ids = License::pluck('name', 'id')->toArray();
        $approvers   = User::pluck('email', 'id')->toArray();
        $vendor_ids  = SdVendors::where('status',1)->pluck('name', 'id')->toArray();
        $start_date  = ($contract->contract_start_date && $contract->contract_start_date != '--')?faveoDate($contract->contract_start_date):null;
        $end_date    = ($contract->contract_end_date && $contract->contract_end_date != '--' )?faveoDate($contract->contract_end_date):null;
        $assets = SdAssets::pluck('name', 'id')->toArray();
        $assetIds = CommonAssetRelation::where('type','sd_contracts')->where('type_id',$id)->pluck('asset_id')->toArray();
        $selectedassets = SdAssets::whereIn('id', $assetIds)->pluck('id','name')->toArray();
        $contractStatus = SdContractStatus::where('id', 1)->pluck('name', 'id')->toArray();
        $contractStatus[1] = ucwords($contractStatus[1]);
        $agentIds = SdContractSdUser::where('contract_id', $id)->pluck('agent_id')->toArray();
        $selectedAgents = User::whereIn('id', $agentIds)->pluck('id', 'email')->toArray();
        $edit = (($contract->status_id == 1 || is_null($contract->status_id)) && is_null($contract->renewal_status_id)) ? true : false;
        $selectedAgents = User::whereIn('id', $agentIds)->pluck('id', 'email')->toArray();
        if (!$edit) {
          return \Redirect::route('service-desk.contract.index')->with('message', Lang::get('ServiceDesk::lang.only_draft_status_contract_can_be_edited'));
        }
        else {
          return view('service::contract.edit', compact('contract', 'contract_type_ids', 'approvers', 'license_type_ids', 'vendor_ids','start_date','end_date', 'selectedassets', 'assets', 'contractStatus', 'selectedAgents', 'edit'));
        }
    
    }catch(Exception $ex){
         return redirect()->back()->with('fails',$ex->getMessage());

    }
       
    }

    /**
     * 
     * @param type $id
     * @param \App\Plugins\ServiceDesk\Requests\CreateContractRequest $request
     * @return type
     */
    public function handleEdit($id, CreateContractRequest $request) {
        $contract = $request->toArray();
        $this->makeEmptyAttributesNullable($contract);
        $sdContract = SdContract::findOrFail($id);
        $sdContract->update($contract);

        $assetId = $request->input('asset', []);
        UtilityController::commonAssetAttach($assetId, $sdContract->id, $type="sd_contracts");

        SdContractSdUser::where('contract_id', $sdContract->id)->delete();
        if($request->has('agent_ids')){
            $agentIds = $request->input('agent_ids');
            foreach ($agentIds as $key => $value) {
                SdContractSdUser::create(['contract_id' => $sdContract->id, 'agent_id' => $value]);
            }
        }

        if($request->contract_start_date){
            $start_date = \Carbon\Carbon::createFromFormat(dateTimeFormat(), $request->contract_start_date, timezone())->setTimezone('UTC');
            $contract_start_date =  $start_date->toDateTimeString();

           SdContract::where('id',$id)->update(['contract_start_date'=>$contract_start_date]);
        }else{
           SdContract::where('id',$id)->update(['contract_start_date'=>null]);
        }

        if($request->contract_end_date){
            $end_date = \Carbon\Carbon::createFromFormat(dateTimeFormat(), $request->contract_end_date, timezone())->setTimezone('UTC');
            $contract_end_date =  $end_date->toDateTimeString();

           SdContract::where('id',$id)->update(['contract_end_date'=>$contract_end_date]);
        }else{
           SdContract::where('id',$id)->update(['contract_end_date'=>null]);
        }

        if ($request->file('attachments')) {
            $attachment = DB::table('sd_attachments')->where('owner', '=', 'sd_problem:' . $id)->first();
            if ($attachment) {
                $file = $attachment->value;
                File::delete(public_path('uploads/service-desk/attachments/' . $file));
                $attachment = DB::table('sd_attachments')->where('owner', '=', 'sd_problem:' . $id)->delete();
            }
        }
        \App\Plugins\ServiceDesk\Controllers\Library\UtilityController::attachment($sdContract->id, 'sd_contracts', $request->file('attachments'));
        $request->merge(['contract_start_date' => $contract_start_date]);
        $request->merge(['contract_end_date' => $contract_end_date]);
        $request = new CreateUpdateContractThreadRequest(array_merge($request->toArray(), ['contract_id' => $id]));
        $this->contractThread->createUpdateContractThread($request);
        return \Redirect::route('service-desk.contract.index')->with('message', Lang::get('ServiceDesk::lang.contract_updated_successfully'));
    }
    
    /**
     * 
     * @param type $id
     * @return type
     * @throws \Exception
     */
    public function show($id) {
        try {
            if (!$this->agentPermission->contractsView()) {
            return redirect('dashboard')->with('fails', Lang::get('ServiceDesk::lang.permission-denied'));
            }
            $contract = SdContract::where('id', '=', $id)->first();
            $sdPolicy = $this->agentPermission;
            $button = $this->listContractButton($contract->id);
            $statusName =  is_null($contract->status_id) ? null : $contract->contractStatus()->first()->name;
            $renewalStatusName =  is_null($contract->renewal_status_id) ? null : $contract->contractRenewalStatus()->first()->name;
            $owner = $contract->owner()->first() ? $contract->owner()->first()->first_name .' '. $contract->owner()->first()->last_name: null;
            $notifyBefore = $contract->notify_before ? $contract->notify_before : null;
            $contractThread = SdContractThread::where('contract_id', $contract->id)->whereIn('renewal_status_id', [8, 12])->orderBy('id', 'desc')->first();
            $end = !is_null($contractThread) ? $contractThread->contract_end_date : $contract->contract_end_date;
            if ($contract) {
                return view('service::contract.show', compact('contract', 'sdPolicy', 'button','statusName','renewalStatusName','owner', 'notifyBefore', 'end'));
            } else {
                throw new \Exception('Sorry we can not find your request');
            }
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @param type $id
     * @param \Illuminate\Http\Request $request
     * @return type
     */
    public function deleteUploadfile($id, Request $request) {
        try {

            $file = $request->filename;
            $attachment = DB::table('sd_attachments')->where('owner', '=', 'sd_contracts:' . $id)->delete();
            File::delete(public_path('uploads/service-desk/attachments/' . $file));
            return Lang::get('ServiceDesk::lang.your_status_updated_successfully');

            // return redirect()->back()->with('success', 'Updated');
        } catch (\Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    //Auto search for contract create[vendor api]  
    public function vendorApi(Request $request){
       
        $term     = $request->input('term');
        $vendor   = SdVendors::where('status',1)->where('name','LIKE','%'.$term.'%')->select('id','name','status')->get();
        
        return $vendor;
   }
  
   
   // To export contract details to Excel
    public function exportContract(){
        try{
            $contract   = new SdContract();
            $contracts  = $contract->leftJoin('sd_contract_types','sd_contracts.contract_type_id','=','sd_contract_types.id')
              ->leftJoin('sd_vendors','sd_contracts.vendor_id','=','sd_vendors.id')
              ->select('sd_contracts.identifier as Identifier',
                'sd_contracts.name as Name',
                'sd_contracts.cost as Cost',
                'sd_contract_types.name as Contract Type',
                'sd_vendors.name as Vendor',
                'sd_contracts.description as Description',
                'sd_contracts.created_at as Created At',
                'sd_contracts.contract_start_date as Contract Start Date',
                'sd_contracts.contract_end_date as Contract Expiry Date')->get()->toArray();
            foreach ($contracts as &$contract) {
              $contract['Description'] = strip_tags(html_entity_decode($contract['Description']));
              $contract['Created At'] = ($contract['Created At'] && $contract['Created At'] != "--" ) ? faveoDate($contract['Created At']) : '';
              $contract['Contract Start Date'] = ($contract['Contract Start Date'] && $contract['Contract Start Date'] != "--" ) ? faveoDate($contract['Contract Start Date']) : '';
              $contract['Contract Expiry Date'] = ($contract['Contract Expiry Date'] && $contract['Contract Expiry Date'] != "--") ? faveoDate($contract['Contract Expiry Date']) : '';
            }
            $excel_controller = new \App\Http\Controllers\Common\ExcelController();
            $filename         ="Contract details";
            $data             = $contracts;
            $excel_controller->export($filename,$data);
        }catch (Exception $ex) {
            return redirect()->back()->with('fails',$ex->getMessage());
        }      
  }
   
   /**
     * 
     * @param Request $request
     * @return type
     */
    public function attachAssetToContract(Request $request) {
      if (!$request->has('asset')) {
        return redirect()->back();
      }
        $request->validate([
                'asset' => 'required',
                'contractid' => 'required'
            ]);
        try{
                $data = CommonAssetRelation::whereIn('asset_id', request('asset'))->where('type_id', request('contractid'))
                        ->where('type', 'sd_contracts')->get()->toArray();
            if(count($data)> 0){
                return redirect()->back()->with('fails',Lang::get('ServiceDesk::lang.please_remove_existing_asset_while_asset_selection'));
            }
                $contractId = $request->input('contractid');
            if($request->has('asset')){
                $assetIds = $request->input('asset');
                        UtilityController::commonCreateAssetAttach($assetIds, $contractId, 'sd_contracts');
            } 
                return redirect()->back()->with('success',Lang::get('ServiceDesk::lang.asset_added_successfully'));
        }catch (Exception $ex) {
                return redirect()->back()->with('fails', $ex->getMessage());
            }
    }

  /**
   * Function to convert date time to time stamp format
   * @param $date
   * @return $date
   */
  private function convertDateTime($date)
  {
    $utcDateFormat = \Carbon\Carbon::createFromFormat(dateTimeFormat(), $date, timezone())->setTimezone('UTC');
    $date =  $utcDateFormat->toDateTimeString();

    return $date;
  }

  /**
   * Function to renew contract
   * @param $contractId
   * @param $request
   * @return redirect
   */
  public function renewContract($contractId, RenewContractRequest $request)
  {
    $contract = SdContract::where('id', $contractId);
    if ($contract->get()->isEmpty()) {
      return \Redirect::route('service-desk.contract.index')->with('message', Lang::get('ServiceDesk::lang.contract_not_found'));
    }
    $contract->update(['cost' => $request->cost,
      'approver_id' => $request->approver_id,
      'renewal_status_id' => 7
    ]);
     $this->createContractThread(['contract_id' => $contractId,
      'contract_start_date' => $this->convertDateTime($request->contract_start_date),
      'contract_end_date' => $this->convertDateTime($request->contract_end_date),
    ]);

    return \Redirect::route('service-desk.contract.index')->with('message', Lang::get('ServiceDesk::lang.contract_renewed_successfully'));
  }

   /**
   * Function to create contract Thread
   * @param $contractId
   * @return 
   */
  private function createContractThread($contract)
  {
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
    $this->contractThread->createUpdateContractThread($request);
  }

  /**
   * Function to extend contract
   * @param $contractId
   * @param $request
   * @return redirect
   */
  public function extendContract($contractId, ExtendContractRequest $request)
  {
    $contract = SdContract::where('id', $contractId);
    if ($contract->get()->isEmpty()) {
      return \Redirect::route('service-desk.contract.index')->with('message', Lang::get('ServiceDesk::lang.contract_not_found'));
    }
    $contract = $contract->first();
    $contract->update(['cost' => $request->cost,
      'approver_id' => $request->approver_id,
      'renewal_status_id' => 10
    ]);
    $contractThread = SdContractThread::where('contract_id', $contract->id)->whereIn('renewal_status_id', [8, 12])->orderBy('id', 'desc')->first();
    $extendStartDate = !is_null($contractThread) ? $contractThread->contract_end_date : $contract->contract_end_date;
    if ($contract->contract_end_date != $this->convertDateTime($request->contract_end_date)) {
      $this->createContractThread(['contract_id' => $contractId,
        'contract_start_date' => $extendStartDate,
        'contract_end_date' => $this->convertDateTime($request->contract_end_date)
      ]);
    }
  
    return \Redirect::route('service-desk.contract.index')->with('message', Lang::get('ServiceDesk::lang.contract_extended_successfully'));
  }

  /**
   * Function to terminate contract
   * @param $contractId
   * @return redirect
   */
  public function terminateContract($contractId)
  {
    $contract = SdContract::where('id', $contractId);
    $auth = Auth::user();
    if ($contract->get()->isEmpty()) {
    return \Redirect::route('service-desk.contract.index')->with('message', Lang::get('ServiceDesk::lang.contract_not_found'));
    }
    $contract = $contract->first();
    if ($contract->owner_id != $auth->id) {
      return \Redirect::route('service-desk.contract.index')->with('message', "You don't have access to terminate " . $contract->name . ' contract.');
    }
    if ($contract->status_id != 3) {
      return \Redirect::route('service-desk.contract.index')->with('message', Lang::get('ServiceDesk::lang.you_cannot_terminate'));
    }
    $contract->update(['status_id' => 4, 'renewal_status_id' => null]);
    $this->contractThread->deletecontractThreads($contract->id);

    return \Redirect::route('service-desk.contract.index')->with('message', Lang::get('ServiceDesk::lang.contract_terminated_successfully'));
  }

  /**
   * Function to approve contract
   * @param $contractId
   * @return redirect
   */
  public function approveContract($contractId)
  {
    $auth = Auth::user();
    $contract = SdContract::where('id', $contractId);
    $contractThread = SdContractThread::where('contract_id', $contractId);
    if ($contract->get()->isEmpty()) {
      return \Redirect::route('service-desk.contract.index')->with('message', Lang::get('ServiceDesk::lang.contract_not_found'));
    }
    $contract = $contract->first();
    $renewalStatusId = $contract->renewal_status_id;
    if ($contract->approver_id != $auth->id) {
      return \Redirect::route('service-desk.contract.index')->with('message', "You don't have access to approve " . $contract->name . ' contract');
    }
    $contractThreadWithoutRenewal = $contractThread->orderBy('id', 'asc')->first();
    if (is_null($contract->renewal_status_id)) {
      $contract->update(['status_id' => 2]);
      $contractThreadWithoutRenewal->update(['status_id' => 2]);
    }
    if ($contract->renewal_status_id == 10) {
      $contract->update(['renewal_status_id' => 12]);
    }
    if ($contract->renewal_status_id == 7) {
      $contract->update(['renewal_status_id' => 8]);
    }
    $systemDate = $this->convertDateTime(faveoDate());
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
    $this->makeContractStatusExpired();
    $this->makeContractStatusActive();

    return \Redirect::route('service-desk.contract.index')->with('message', Lang::get('ServiceDesk::lang.contract_approved_successfully'));
  }

  /**
   * Function to send approved and rejected contract in app notification and mail
   * @param object $contract
   * @return
   */
  private function sendMailAndNotify($contract)
  {
    $approval_status = ($contract->status_id == 6) ? 'rejected' : 'approved';
    $approver = $contract->approverRelation()->first()->toArray();
    $registerNotifications[$approval_status . '_contract'] = [
                    'model' => $contract,
                    'userid'=> $contract->owner_id,
                    'from' => $this->phpMailController->mailfrom('1', '0'),
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
   * Function to reject contract
   * @param $contractId
   * @param $request
   * @return redirect
   */
  public function rejectContract($contractId, RejectContractRequest $request)
  {
    $auth = Auth::user();
    $contract = SdContract::where('id', $contractId);
    $contractThread = SdContractThread::where('contract_id', $contractId);
    if ($contract->get()->isEmpty()) {
      return \Redirect::route('service-desk.contract.index')->with('message', Lang::get('ServiceDesk::lang.contract_not_found'));
    }
    $contract = $contract->first();
    $renewalStatusId = $contract->renewal_status_id; 
    if ($contract->approver_id != $auth->id) {
      return \Redirect::route('service-desk.contract.index')->with('message', "You don't have access to approve " . $contract->name . ' contract.');
    }
    $contractThreadWithoutRenewal = $contractThread->orderBy('id', 'asc')->first();
    $contract->update(['purpose_of_rejection' => $request->purpose_of_rejection]);
    if (is_null($contract->renewal_status_id)) {
      $contract->update(['status_id' => 6, 'purpose_of_rejection' => $request->purpose_of_rejection]);
      $contractThreadWithoutRenewal->update(['status_id' => 6]);
    }
    else if ($contract->renewal_status_id == 7) {
      $contract->update(['renewal_status_id' => 9, 'purpose_of_rejection' => $request->purpose_of_rejection]);
    }
    else if ($contract->renewal_status_id == 10) {
      $contract->update(['renewal_status_id' => 11, 'purpose_of_rejection' => $request->purpose_of_rejection]);
    }
    $systemDate = $this->convertDateTime(faveoDate());
    if ($systemDate > $contract->contract_end_date) {
      $contract->update(['status_id' => 5]);
      $contractThreadWithoutRenewal->update(['status_id', 5]);
    }
    $this->sendMailAndNotify($contract);
    $contractThread = $contractThread->whereIn('renewal_status_id', [7, 10])->first();
    if ($contractThread) {
      ($renewalStatusId == 7) ? $contractThread->update(['renewal_status_id' => 9]) : $contractThread->update(['renewal_status_id' => 11]);
    }
    $this->makeContractStatusExpired();
    $this->makeContractStatusActive();

    return \Redirect::route('service-desk.contract.index')->with('message', Lang::get('ServiceDesk::lang.contract_rejected_successfully'));
  }

  /**
   * Function to list contract button
   * @param $contractId
   * @return array $button
   */
  private function listContractButton($contractId)
  {
    $auth = Auth::user();
    $contract = SdContract::where('id', $contractId)->first();
    $button = [];
    if ($contract->approver_id == $auth->id) {
      $button['reject'] = $button['approve'] = (bool) (($contract->approver_id == $auth->id) && ($contract->status_id == 1) || ($contract->renewal_status_id == 7) || ($contract->renewal_status_id == 10));
    }
    else {
       $button['reject'] = $button['approve'] = false;
    }
    if (($auth->role == 'agent' && $contract->owner_id == $auth->id)  || ($auth->role == 'admin')) {
      $button['extend'] = (bool) ($contract->status_id == 3) && (is_null($contract->renewal_status_id) || $contract->renewal_status_id == 11) && !$button['approve'];
      $button['terminate'] = (bool) ($contract->status_id == 3);
      $button['renew'] = (bool) ($contract->status_id == 5) && ($contract->renewal_status_id != 8) && !$button['approve'];
      $button['edit'] = (($contract->status_id == 1 || is_null($contract->status_id)) && is_null($contract->renewal_status_id)) ? true : false;
    }
    else {
      $button['extend'] = $button['terminate'] = $button['renew'] = $button['edit'] = false;
    }
    return $button;
  }

   /**
     * This function will retrive notify agent list in  yajra datatable format
     * @param $contractId
     */
    public function getNotifiedAgents($contractId)
    {
      $agentIds = SdContractSdUser::where('contract_id', $contractId)->pluck('agent_id')->toArray();
      $selectedAgents = User::whereIn('id', $agentIds)->select('id','first_name', 'last_name', 'email')->get()->toArray();  
      return \DataTables::of($selectedAgents)
        ->addColumn('name', function($model) {
          $name = \App\User::where('id', $model['id'] )->first();
          $nameinfo = ($name!= null) ? $name->fullName : null ;
          return "<a href=".url('user/'.$model['id']). ">".$nameinfo."</a>";
        })
        ->rawColumns(['name'])
        ->make(true);
    }

  /**
   * Function to send contract expiry email to selected agents and vendor
   * @return
   */
  public function sendContractNotificationExpiry()
  {
    try {
          $contracts = new SdContract();
          $delayInSeconds = 0;
          $contractWithAgents = $contracts->with('notifyAgents')->has('notifyAgents')->get()->toArray();
          $systemDate =  date('Y-m-d', strtotime($this->convertDateTime(faveoDate())));
          foreach ($contractWithAgents as $contract) {
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
      // returns if try fails
      return Lang::get('ServiceDesk::lang.send_mail_error_on_contract_notification_expiry');
    }
  }

  /**
   * Function to change contract status to expired
   * @return
   */
  public function makeContractStatusExpired()
  {
    try {
          $contracts = new SdContract();
          $contracts = $contracts->get();
          $systemDate =  $this->convertDateTime(faveoDate());
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
      // returns if try fails
      // return $e->getMessage();
      return Lang::get('ServiceDesk::lang.contract_expiry_error');
    }
  }

  /**
   * Function to change contract status to active
   * @return
   */
  public function makeContractStatusActive()
  {
    try {
          $contracts = new SdContract();
          $contracts = $contracts->get();
          foreach ($contracts as $contract) {
            $systemDate =  $this->convertDateTime(faveoDate());
            if (($contract->contract_start_date <= $systemDate) && ($contract->status_id == 2))
            {
              $contract->update(['status_id' => 3]);
            }
          }
    } catch (Exception $e) {
      // returns if try fails
      // return $e->getMessage();
      return Lang::get('ServiceDesk::lang.contract_active_error');
    }
  }

  /**
   * Function is used to retrieve associated contracts based on assets linked to organization and contract
   * @param $organizationId
   * @return html code
   */
  public function getAttachedContractsBasedOnOrganization($organizationId, Request $request)
  {
    
    $searchQuery = $request->input('search-query') ?? '';
    
    $sortOrder = $request->input('sort-order') ?? 'asc';
    
    $sortField = $request->input('sort-field') ?? 'name';
    
    $limit = $request->limit ?? 10;
    
    $contracts = SdContract::whereHas('attachAsset',function($query) use ($organizationId) {
      $query->where('organization_id',$organizationId);
      })
      ->where(function($query) use ($searchQuery) {
        $query->where('name', 'LIKE', "%$searchQuery%")
          ->orWhere('identifier', 'LIKE', "%$searchQuery%")
          ->orWhere('cost', 'LIKE', "%$searchQuery%");
      })
      ->select('id','name','identifier','cost','contract_start_date','contract_end_date')
      ->orderBy($sortField,$sortOrder)
      ->paginate($limit)
      ->toArray(); 
      return successResponse('', $contracts);
  }

}
