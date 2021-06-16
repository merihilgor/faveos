<?php

namespace App\Plugins\ServiceDesk\Controllers\Contract;

use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use App\Plugins\ServiceDesk\Model\Contract\SdContractThread;
use App\Plugins\ServiceDesk\Model\Contract\SdContract;
use App\Plugins\ServiceDesk\Request\Contract\CreateUpdateContractThreadRequest;
use Exception;
use Lang;
use DB;
use File;
use Auth;

class ContractThreadController extends BaseServiceDeskController {

  protected $contract;
  protected $contractThread;

  public function __construct()
  {
    $this->middleware('role.agent');
    $this->contract = new SdContract();
    $this->contractThread = new SdContractThread();
    $this->user = Auth::user(); 
  }

  /**
   * create update method for contract thread
   * @param CreateUpdateContractThreadRequest $request
   * @return response
   */
  public function createUpdateContractThread(CreateUpdateContractThreadRequest $request)
  {
    $contractThread = $request->toArray();
    $ownerId = Auth::user()->id;
    $contractThread['owner_id'] = !$request->has('owner_id') ? $ownerId : $contractThread['owner_id'];
    $this->contractThread->updateOrCreate(['id' => $request->id], $contractThread);

    return successResponse(Lang::get('ServiceDesk::lang.contract_thread_saved_successfully'));
  }

  /**
   * edit contract thread
   * @param $contractThreadId
   * @return response
   */
  public function editContractThread($contractThreadId)
  {
    $contractThreadObj = $this->contractThread->find($contractThreadId);
    if (is_null($contractThreadObj)) {
      return errorResponse(Lang::get('ServiceDesk::lang.contract_thread_not_found'));
    }

    $contractThread = $this->formatContractThread($contractThreadObj);

    return successResponse('', ['contract_thread' => $contractThread]);
  }

  /**
   * Method to format contract thread
   * @param SdContractThread $contractThreadObj
   * @return array $contractThread
  */
  private function formatContractThread($contractThreadObj)
  {
    $contractThread = $contractThreadObj->toArray();
    $contract = $contractThreadObj->contract()->first();
    $contractThread['contract'] = ['id' => $contract->id, 'name' => $contract->name];
    $status = $contractThreadObj->contractStatus()->first();
    $contractThread['status'] = ['id' => $status->id, 'name' => $status->name];
    if (!is_null($contractThread['renewal_status_id']) || !empty($contractThread['renewal_status_id'])) {
      $renewalStatus = $contractThreadObj->contractRenewalStatus()->first();
      $contractThread['renewal_status'] = ['id' => $renewalStatus->id, 'name' => $renewalStatus->name];
      $contractThread['type'] = $renewalStatus->name;
    }
    else {
      $contractThread['renewal_status'] = $contractThread['renewal_status_id'];
    }
    $this->formatApproverAndOwner($contractThread, $contractThreadObj);
    unset($contractThread['contract_id'], $contractThread['status_id'], $contractThread['renewal_status_id'],
      $contractThread['approver_id'], $contractThread['owner_id']);
    return $contractThread;
  }

  /**
   * Method to format Approver and Owner and extension of formatContractThread method
   * @param array $contractThread
   * @param SdContractThread $contractThreadObj
   * @return 
  */
  private function formatApproverAndOwner(&$contractThread, $contractThreadObj)
  {
    $approver = $contractThreadObj->approver()->first();
    $owner = $contractThreadObj->owner()->first();
    if($approver){
      $contractThread['approver'] = ['id' => $approver->id,
          'name' => $approver->full_name,
          'email' => $approver->email,
          'profile_pic' => $approver->profile_pic
            ];
      }
    $contractThread['owner'] = ['id' => $owner->id,
      'name' => $owner->full_name,
      'email' => $owner->email,
      'profile_pic' => $owner->profile_pic
    ];
  }

   /**
   * method to get contract specific threads
   * @param $contractId
   * @return response
   */
  public function getcontractThreads($contractId)
  {
    $contractObj = $this->contract->find($contractId);
    if (is_null($contractObj)) {
      return errorResponse(Lang::get('ServiceDesk::lang.contract_not_found'));
    }
    $currentTimeStamp = $this->convertDateTime(faveoDate());
    $contractThreads = $this->contractThread->where('contract_id', $contractId)->orderBy('id', 'desc')->get()->toArray();
    $formattedContractThreads = [];
    foreach ($contractThreads as $contractThread) {
      $contractThreadObj = $this->contractThread->find($contractThread['id']);
      $current = (bool) ($currentTimeStamp >= $contractThread['contract_start_date']) && ($currentTimeStamp <= $contractThread['contract_end_date']) && ((is_null($contractThreadObj->renewal_status_id) && ($contractThreadObj->status_id == 3)) || (in_array($contractThreadObj->renewal_status_id, [8,12]) && in_array($contractThreadObj->status_id, [3,5])));
      $expired = (bool) ($contractObj->contract_start_date == $contractThreadObj->contract_start_date) && ($contractObj->contract_end_date == $contractThreadObj->contract_end_date) && ($contractObj->status_id == 5) && is_null($contractObj->renewal_status_id);
      $contractThread = $this->formatContractThread($contractThreadObj);
      $contractThread = array_merge($contractThread, ['current' => (int) $current, 'expired' => (int) $expired]);
      array_push($formattedContractThreads, $contractThread);
    }
    return successResponse('', ['contract_threads' => $formattedContractThreads]);
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
   * method to delete contract threads based on contract id
   * @param $contractId
   * @return response
   */
  public function deletecontractThreads($contractId)
  {
    $contractObj = $this->contract->find($contractId);
    if (is_null($contractObj)) {
      return errorResponse(Lang::get('ServiceDesk::lang.contract_not_found'));
    }

    $this->contractThread->where('contract_id', $contractId)->delete();

    return successResponse(Lang::get('ServiceDesk::lang.contract_threads_deleted_successfully'));
  }

  /**
   * method to delete contract thread based on contract thread id
   * @param $contractThreadId
   * @return response
   */
  public function deletecontractThread($contractThreadId)
  {
    $contractThreadObj = $this->contractThread->find($contractThreadId);
    if (is_null($contractThreadObj)) {
      return errorResponse(Lang::get('ServiceDesk::lang.contract_thread_not_found'));
    }

    $contractThreadObj->first()->delete();

    return successResponse(Lang::get('ServiceDesk::lang.contract_thread_deleted_successfully'));
  }


}
