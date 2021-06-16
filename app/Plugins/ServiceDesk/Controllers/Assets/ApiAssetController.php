<?php

namespace App\Plugins\ServiceDesk\Controllers\Assets;

use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use App\Plugins\ServiceDesk\Controllers\Library\UtilityController;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use Illuminate\Http\Request;
use App\Model\helpdesk\Ticket\Tickets;
use App\Plugins\ServiceDesk\Model\Assets\CommonAssetRelation;
use App\Plugins\ServiceDesk\Model\Common\CommonTicketRelation;
use App\Plugins\ServiceDesk\Model\Common\Attachments;
use App\Plugins\ServiceDesk\Request\Assets\AssetRequest;
use DB;
use File;
use Lang;
use App\Model\helpdesk\Form\CustomFormValue;
use App\Model\helpdesk\Form\FormField;
use App\Model\helpdesk\Agent\Department;
use App\Plugins\ServiceDesk\Model\Assets\SdAssettypes;
use App\Plugins\ServiceDesk\Model\Releases\SdReleases;
use App\Plugins\ServiceDesk\Request\Assets\AttachOrDetachServicesToAssetRequest;
use App\Plugins\ServiceDesk\Model\Common\Ticket;
use Config;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Models\ActivityBatch;
use App\Plugins\ServiceDesk\Model\Problem\SdProblem;
use App\Plugins\ServiceDesk\Model\Changes\SdChanges;
use App\Plugins\ServiceDesk\Model\Contract\SdContract;
use App\Http\Controllers\Common\Dependency\DependencyDetails;
use App\Plugins\ServiceDesk\Controllers\Common\Dependency\SdDependencyController;
use App\FaveoStorage\Controllers\StorageController;
use App\Model\helpdesk\Agent_panel\Organization;
use App\Plugins\ServiceDesk\Model\Products\SdProducts;
use App\Plugins\ServiceDesk\Policies\AgentPermissionPolicy;

/**
 * Handles API's for Asset Controller
 * 
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
*/

class ApiAssetController extends BaseServiceDeskController {

  /**
  * Creates and Updates Asset
  * @param $request
  * @return Response
  */
  public function createUpdateAsset(AssetRequest $request)
  {
    $asset = $request->toArray();
    $this->makeEmptyAttributesNullable($asset);
    $asset['assigned_on'] = $request->assigned_on ? convertDateTimeToUtc($asset['assigned_on']) : NULL;
    $this->assetTypeChangeAction($request->asset_type_id, $request->id);
    $assetObject = SdAssets::updateOrCreate(['id' => $request->id], $asset);
    $this->fillAttachment($request, $assetObject);
    $this->createOrUpdateAssetCustomFields($asset, $assetObject);
    $assetIdentifierWithHyperLink = implode('', ['<a href=', faveoUrl("service-desk/assets/{$assetObject->id}/show"), " rel='noopener noreferrer' target='_blank'>{$assetObject->identifier}</a>"]);

    return successResponse(trans('ServiceDesk::lang.asset_saved_successfully', ['assetIdentifier'=> $assetIdentifierWithHyperLink]), '');
  }

  /**
   * method to handle asset type changed action
   * @param $assetTypeId
   * @param $assetId
   * @return null
   */
  private function assetTypeChangeAction($assetTypeId, $assetId)
  {
    $asset = SdAssets::find($assetId);
    if ($asset) {
      $asset->assetTypeChangedAction($assetTypeId);
    }
  }

  /** 
   * method to create or update asset custom fields
   * @param array $asset
   * @param SdAssets $assetId
   * @return null
   */
  private function createOrUpdateAssetCustomFields(array $asset, SdAssets $assetObject)
  {
    $customFields = $this->splitCustomFields($asset);
    $assetTypeCustomFieldIds = [];
    $this->appendAssetTypeNodesInAsset($asset);
    $this->getListofFormFieldIdsOfCertainType($asset['asset_type'], $assetTypeCustomFieldIds);
    $activityBatchId = ActivityBatch::create(['log_name' => 'asset_custom_field'])->id;
    foreach ($customFields as $customField) {
      $type = '';
      if (in_array($customField['form_field_id'], $assetTypeCustomFieldIds)) {
        $type = 'asset_type';
      }
      $customField = array_merge($customField, ['type' => $type, 'custom_id' => $assetObject->id, 'custom_type' => SdAssets::class]);
      $customFieldWithValue = $customField;
      $previousValueObject = CustomFormValue::where([['form_field_id', $customField['form_field_id']], ['custom_type', SdAssets::class], ['custom_id', $assetObject->id]])->first();
      if ($this->performActionOnExistingOrEmptyCustomFormValue($previousValueObject, $assetObject, $activityBatchId, $customField['value'])) {
        continue;
      }
      unset($customField['value']);
      $currentValueObject = CustomFormValue::updateOrCreate($customField, $customFieldWithValue);
      $this->addActivityLogForCustomFields($currentValueObject, $assetObject, $activityBatchId);
    }
  }

  /**
   * method to perform necessary action on existing or empty custom form value
   * @param CustomFormValue/null $previousValueObject
   * @param SdAssets $assetObject
   * @param int $activityBatchId
   * @param string $customFieldValue
   * @return bool $action ( true or false)
   */

  private function performActionOnExistingOrEmptyCustomFormValue($previousValueObject, $assetObject, $activityBatchId, $customFieldValue) {
    $action = false;
    if (isset($previousValueObject->value) && !$customFieldValue) {
      $previousValueObject->value = '';
      $this->addActivityLogForCustomFields($previousValueObject, $assetObject, $activityBatchId);
      $previousValueObject->delete();
      $action = true;
    }
    if (isset($previousValueObject->value) && ($previousValueObject->value == $customFieldValue) || !$customFieldValue) {
      // ignore if custom field value is empty
      $action = true;
    }

    return $action;
  }

  /**
   * method to add activity log for custom fields
   * @param CustomFormValue $currnetValueObject
   * @param SdAssets $assetObject
   * @param int $activityBatchId
   * @return null
   */
  private function addActivityLogForCustomFields(CustomFormValue $currentValueObject, SdAssets $assetObject, int $activityBatchId)
  {
    $currentValueObject->value = (array) $currentValueObject->value;
    $currentValueObject->value = implode(', ', $currentValueObject->value);
    $assetObject->manuallyLogActivityForPivot($assetObject, $currentValueObject->form_field_id, $currentValueObject->value, 'created', $activityBatchId, 'asset_custom_field', 0);
  }


  /**
   * method to split custom fields form asset array
   * @param array $asset
   * @return array $customFields
   */
  public function splitCustomFields($asset)
  {
    $customFields = [];
    array_walk($asset, function($value, $key) use (&$customFields) {
      if (strpos($key, 'custom_') === 0) {
        $formFieldId = (int) filter_var($key, FILTER_SANITIZE_NUMBER_INT);
        $fieldType = FormField::find($formFieldId)->type;
        if ($fieldType == 'date') {
          $value = convertDateTimeToUtc($value);
        }
        array_push($customFields, ['form_field_id' => $formFieldId, 'value' => $value]);
      }
    });

    return $customFields;
  }

  /**
  * Function to fill attachment
  * @param Request $request
  * @param SdAssets $asset
  * @return 
  */
  private function fillAttachment(Request $request, SdAssets $asset)
  {
    $attachmentIds = Attachments::where('owner', implode(['sd_assets:',$asset->id]))->pluck('id')->toArray();
    $requestAttachmentIds = $request->attachment_ids ?: [];
    $deleteAttachmentIds = array_diff($attachmentIds, $requestAttachmentIds);
    if ($deleteAttachmentIds) {
      foreach ($deleteAttachmentIds as $attachmentId) {
        $fileName = Attachments::find($attachmentId)->value;
        File::delete(public_path('uploads/service-desk/attachments/' . $fileName));
        Attachments::where('id', $attachmentId)->delete();
      }
      $asset->manuallyLogActivityForPivot($asset, 'attachment', '', 'deleted', null, 'asset_attachment', 0);
    }

    if ($request->file('attachments')) {
      $attachments = $request->file('attachments');
      UtilityController::attachment($asset->id, 'sd_assets', $attachments);
      $asset->manuallyLogActivityForPivot($asset, 'attachment', '', 'created', null, 'asset_attachment', 0);
    }
  }

  /**
   * method to get list of form field ids of certain type
   * @param $formFields
   * @param array $formFieldsIds
   * @return null
   */
  private function getListofFormFieldIdsOfCertainType($formFields, &$formFieldIds)
  {
    foreach ($formFields->nodes as $formField) {
      array_push($formFieldIds, $formField->id);
      if (!$formField->options->isEmpty()) {
        foreach ($formField->options as $option) {
          if (!$option->nodes->isEmpty()) {
            $this->getListofFormFieldIdsOfCertainType($option, $formFieldIds);
          }
        }
      }
    }
  }

  /**
  * method to edit asset
  * @param SdAssets $asset
  * @return Response
  */
  public function editAsset(SdAssets $asset)
  {
    $extraAssetRelations = ['customFieldValues:id,form_field_id,value,type,custom_id'];
    $asset = $this->getAssetData($asset->id, $extraAssetRelations)->toArray();
    $this->appendAssetTypeNodesInAsset($asset);
    $this->appendCustomFormValuesInAssetData($asset);

    return successResponse('', $asset);
  }

  /**
   * method to get asset data
   * @param integer $assetId
   * @param array $extraAssetRelations
   * @return $asset (SdAssets Object Data)
   */
  private function getAssetData(int $assetId, array $extraAssetRelations)
  {
    $assetRelations = [
      'usedBy:id,first_name,last_name,email,profile_pic',
      'managedBy:id,first_name,last_name,email,profile_pic',
      'organization:id,name',
      'product:id,name',
      'department:id,name',
      'assetType:id,name',
      'impactType:id,name',
      'location:id,title as name',
      'assetStatus:id,name'
    ];

    $assetRelations = array_merge($assetRelations, $extraAssetRelations);
    $asset = SdAssets::where('id', $assetId)->with($assetRelations)->first();

    if (!is_null($asset->usedBy)) {
      $asset->usedBy->name = $asset->usedBy->full_name;
    } 

    if (!is_null($asset->managedBy)) {
      $asset->managedBy->name = $asset->managedBy->full_name;
    }

    $this->appendAttachmentsInAsset($asset); 

    unset($asset->location_id, $asset->product_id, $asset->impact_type_id, $asset->organization_id, $asset->managed_by_id, $asset->used_by_id, $asset->status_id);

    return $asset;
  }

  /** 
   * method to append attachments in asset data
   * @param SdAssets $asset
   * @return null
   */
  private function appendAttachmentsInAsset(SdAssets $asset){
    $assetId = $asset->id;
    $storageControllerInstance = (new StorageController);
    $asset->attachments = Attachments::where('owner', 'sd_assets:' . $asset->id)->get()
        ->transform(function($attachment) use($storageControllerInstance, $assetId) {
          $attachment->size = getSize($attachment->size);
          $attachment->link = implode('', ['/service-desk/download/', $attachment->id, '/sd_assets:', $assetId, '/attachment']);
          $fileLink = public_path(implode('', ['uploads/service-desk/attachments/', $attachment->value]));
          $attachment->icon_url = $storageControllerInstance->getThumbnailUrlByMimeType($fileLink);

          return $attachment;
        });
  }

  /** 
   * method to append asset type nodes in asset data
   * @param array $asset
   * @return null
   */
  private function appendAssetTypeNodesInAsset(array &$asset)
  {
    $sdDependencyControllerInstance = (new SdDependencyController);
    $sdDependencyControllerInstance->initializeParameterValues(new Request(['meta' => true, 'ids' => [$asset['asset_type_id']]]));
    $asset['asset_type'] = $sdDependencyControllerInstance->handleDependencies('asset_types')['asset_types']->first();
  }

  /** 
   * method to append custom field values in asset data
   * @param array $asset
   * @return null
   */
  private function appendCustomFormValuesInAssetData(array &$asset)
  {
    foreach ($asset['custom_field_values'] as $customField) {
      $customFieldKeyName = implode('', ['custom_', $customField['form_field_id']]);
      $asset[$customFieldKeyName] = $customField['value'];
    }

    unset($asset['department_id'], $asset['asset_type_id']);
  }

  /**
  * gets existing asset data based on asset id
  * @param SdAssets $asset
  * @return Response
  */
  public function getAsset(SdAssets $asset)
  {
    $extraAssetRelations = [
      'customFieldValuesForAssetFormBuilderOnly:id,form_field_id,value,type,custom_id',
      'customFieldValuesForAssetType:id,form_field_id,value,type,custom_id'
    ];

    $asset = $this->getAssetData($asset->id, $extraAssetRelations);
  
    return successResponse('', $asset);
  }

  public function assetRelation($assetId, Request $request)
  {
    $limit = $request->input('limit') ? $request->input('limit') : 10;

    $searchQuery = $request->input('search_query') ? $request->input('search_query') : '';

    $sortField = $request->input('sort_field') ? $request->input('sort_field') : 'updated_at';
        
    $sortOrder = $request->input('sort_order') ? $request->input('sort_order') : 'desc';

    $asset = SdAssets::where('id', $request->id)
            ->select('id', 'name')
            ->orderBy($sortField, $sortOrder)
            ->orWhereHas('problems', function($q) use ($searchQuery) {
                $q
                  ->where('subject', 'like', '%' . $searchQuery . '%');
            })
            ->orWhereHas('problems.status', function($q) use ($searchQuery) {
              $q
                ->where('name', 'like', '%' . $searchQuery . '%');
            })
            ->orWhereHas('releases', function($q) use ($searchQuery) {
                $q
                  ->where('subject', 'like', '%' . $searchQuery . '%');
            })
            ->orWhereHas('releases.status', function($q) use ($searchQuery) {
              $q
                ->where('name', 'like', '%' . $searchQuery . '%');
            })
            ->with(['problems.status:id,name','releases.status:id,name','changes'])
            ->paginate($limit)->toArray(); 

    
    $asset = $this->formatAssetRelation($asset);

    return successResponse('', $asset);
  }

  private function formatAssetRelation($asset)
  {
     $asset['asset'] = [];
     foreach ($asset['data'] as &$assett) {
      foreach ($assett['problems'] as &$problem) {
        $problem['request'] = 'Problems';
        unset($problem['from'], $problem['department'], $problem['description'], $problem['status_type_id'], $problem['priority_id'], $problem['impact_id'], $problem['location_type_id'], $problem['group_id'], $problem['agent_id'], $problem['assigned_id'], $problem['pivot']);
       }
       foreach ($assett['changes'] as &$change) {
        $change['request'] = 'Changes';
        unset($change['description'], $change['requester'], $change['status_id'], $change['priority_id'], $change['change_type_id'], $change['impact_id'], $change['location_id'], $change['approval_id'], $change['pivot']);
       }
       foreach ($assett['releases'] as &$release) {
        $release['request'] = 'Releases';
        unset($release['description'], $release['planned_start_date'], $release['planned_end_date'], $release['status_id'], $release['priority_id'], $release['release_type_id'], $release['location_id'], $release['pivot']);
       }
       array_push($asset['asset'], $assett);
     }
     unset($asset['data']);
    
    return $asset;
  }

  /**
  * detach asset based on ticket id and asset id
  * @param $assetId
  * @return Response
  */
  public function detachAsset($ticketId, $assetId)
  {
    $asset = CommonTicketRelation::where([['ticket_id', $ticketId], ['type_id', $assetId], ['type', 'sd_assets']]);

    if ($asset->get()->isEmpty()) {
      return errorResponse(Lang::get('ServiceDesk::lang.asset_not_found'), 412);
    }

    $asset->delete();

    return successResponse(Lang::get('ServiceDesk::lang.asset_detached_successfully'));

  }

  /**
  * attach asset based on ticket id and asset ids
  * @param $request
  * @return Response
  */
  public function attachAssets(Request $request)
  {
    if (Tickets::where('id', $request->ticket_id)->get()->isEmpty()) {
      return errorResponse(Lang::get('ServiceDesk::lang.ticket_not_found'), 412);
    }

    if ($request->has('asset_ids')) {
      foreach ((array) $request->asset_ids as $key => $value) {
         
         CommonTicketRelation::updateOrCreate(['ticket_id' => $request->ticket_id, 'type_id' => $value, 'type' => 'sd_assets'], ['ticket_id' => $request->ticket_id, 'type_id' => $value, 'type' => 'sd_assets']);
      }
    }
    else {
      return errorResponse(Lang::get('ServiceDesk::lang.asset_not_found'), 412);
    }

    return successResponse(Lang::get('ServiceDesk::lang.assets_attached_successfully'));

  }

  /**
  * method to delete asset
  * @param SdAssets $asset
  * @return Response
  */
  public function deleteAsset(SdAssets $asset)
  {

      $asset->delete();

      return successResponse(trans('ServiceDesk::lang.asset_deleted_successfully'));
  }


    /**
     * Function to get contract based on asset object
     * @param $asset
     * @return array $contract
     */
    public function getContractBasedOnAsset(SdAssets $asset) {
      $contract = $asset->contracts()->with(['vendor:id,name','approverRelation:users.id,first_name,last_name'])->select('sd_contracts.id', 'name', 'cost', 'contract_start_date', 'contract_end_date', 'notify_before', 'approver_id', 'vendor_id', 'sd_contracts.created_at')->first();

      if (!is_null($contract)) {
        $contract = $contract->toArray();
        $contract['created_at'] = faveoDate($contract['created_at']);
        $contract['contract_start_date'] = faveoDate($contract['contract_start_date']);
        $contract['contract_end_date'] = faveoDate($contract['contract_end_date']);
        $contract['approver'] = ['id' => $contract['approver_relation']['id'], 'name' => $contract['approver_relation']['full_name']];
        $contract['vendor']['name'] = ucfirst($contract['vendor']['name']);
        $contract['name'] = ucfirst($contract['name']);
        unset($contract['approver_id'], $contract['vendor_id'], $contract['pivot'], $contract['approver_relation']);
      }

      return $contract;
    }

    /**
     * Function to get contract based on asset id API
     * @param $assetId
     * @return response
     */
    public function getContractBasedOnAssetApi($assetId) {
      $asset = SdAssets::find($assetId);
      if (is_null($asset)) {
        return errorResponse(Lang::get('ServiceDesk::lang.asset_not_found'));
      }
      $contract = $this->getContractBasedOnAsset($asset);
      
      return successResponse('', $contract);
    }
    
    /**
     * method to attach services to asset
     * @param SdAssets $asset
     * @param AttachOrDetachServicesToAssetRequest $request  (type and type_ids)
     * type =>  service type Eg: sd_problem, sd_changes, sd_releases, sd_contracts, tickets
     * type_ids => service type ids
     * @return response
     */
    public function attachServicesToAsset(SdAssets $asset, AttachOrDetachServicesToAssetRequest $request)
    {
      $attachingRelationName = '';
      $service = $this->attachOrDetachServicesCommonCode($request, $attachingRelationName);
      $asset->$attachingRelationName()->attach($service['type_ids']);

      return successResponse(trans('ServiceDesk::lang.attached_successfully'));
    }

    /**
     * method to handle common code for attach and detach asset services
     * @parm AttachOrDetachServicesToAssetRequest $request
     * @param string $attachingOrDetachingRelationName
     * @param array $serviceTypeIds
     * @return array
     */
    private function attachOrDetachServicesCommonCode($request, &$attachingOrDetachingRelationName, $serviceTypeIds = []) 
    {
      $serviceType = $request->type == 'tickets' ? 'sd_assets' : $request->type;
      $relationType = $request->type == 'sd_problem' ? 'problems' : $request->type;

      if (empty($serviceTypeIds)) {
        foreach ((array) $request->type_ids as $typeId) {
          $serviceTypeIds[$typeId] = ['type' => $serviceType];
        }
      }

      $relationName = explode('_', $relationType);
      $attachingOrDetachingRelationName = end($relationName);

      return ['type' => $serviceType, 'type_ids' => $serviceTypeIds];
    }

    /**
     * method to detach services from asset
     * @param SdAssets $asset
     * @param AttachOrDetachServicesToAssetRequest $request  (type and type_ids)
     * type =>  service type Eg: sd_problem, sd_changes, sd_releases, sd_contracts, tickets
     * type_ids => service type ids
     * @return response
     */
    public function detachServicesFromAsset(SdAssets $asset, AttachOrDetachServicesToAssetRequest $request)
    {
      $serviceTypeIds = $request->type_ids;
      $detachingRelationName = '';
      $service = $this->attachOrDetachServicesCommonCode($request, $detachingRelationName, $serviceTypeIds);
      $asset->$detachingRelationName()->wherePivot('type', $service['type'])->detach($service['type_ids']);

      return successResponse(trans('ServiceDesk::lang.detached_successfully'));
    }

    /**
    * method to get asset activity log
    * @param SdAssets $asset
    * @param Request $request
    * @return response
    */
    public function getAssetActivityLog(SdAssets $asset, Request $request)
    {
        $limit = $request->input('limit') ?: 10;
        $sortField = $request->input('sort-field') ?: 'id';
        $sortOrder = $request->input('sort-order') ?: 'desc';
        $assetId = $asset->id;

        $assetActivityLogs = ActivityBatch::with(['activity.creator:id,first_name,last_name,user_name,email,profile_pic'])->whereHas('activity', function($query) use($assetId)
            {
                $query->where([['source_type', 'App\Plugins\ServiceDesk\Model\Assets\SdAssets'], ['source_id', $assetId]]);
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate($limit)
            ->toArray();

        $this->formatAssetActivityLog($assetActivityLogs);

        return successResponse('', ['asset_activity_logs' => $assetActivityLogs]);
    }

    /**
     * method to format asset activity log
     * @param array $assetActivityLogs
     * @return null
     */
    private function formatAssetActivityLog(array &$assetActivityLogs)
    {
        $activity = '';
        $assetActivity = [];
        foreach ($assetActivityLogs['data'] as $activityLogs) {
            $activityLogs = $activityLogs['activity'];
            $activity  = (($activityLogs[0]['event_name'] == 'deleted') && ($activityLogs[0]['log_name'] == 'asset_attachment')) ? 'deleted attachment' : ($activityLogs[0]['event_name'] == 'created' ? ($activityLogs[0]['log_name'] == 'asset' ? "created a new asset <b>(#AST-{$activityLogs[0]['source_id']})</b>, " : ($activityLogs[0]['log_name'] == 'asset_attachment' ? 'added attachment' : '')) : '');
            if ($activityLogs[0]['log_name'] == 'asset_pivot') {
                $this->formatAssetPivotActivityLog($activityLogs, $assetActivity);
                continue;
            }
            $this->formatActivity($activityLogs, $activity);
            $activity = rtrim($activity, ', ');
            $assetActivity[] = ['id' => $activityLogs[0]['id'], 'creator' => $activityLogs[0]['creator'], 'name' => $activity, 'created_at' => $activityLogs[0]['created_at']];
        }
        $assetActivityLogs['data'] = $assetActivity;
    }

    /** 
     * method to format activity
     * @param array $activityLogs
     * @param $activity
     * @return null
     */
    private function formatActivity(array &$activityLogs, string &$activity)
    {
        foreach ($activityLogs as &$assetActivityLog) {
            if ($assetActivityLog['log_name'] == 'asset_custom_field') {
                $formField = FormField::find($assetActivityLog['field_or_relation']);
                $assetActivityLog['field_or_relation'] = $formField->label;
                $assetActivityLog['final_value'] = (($formField->type == 'date') && $assetActivityLog['final_value']) ? faveoDate($assetActivityLog['final_value'], 'F j, Y, g:i a', agentTimeZone()) : $assetActivityLog['final_value'];
            }
            
            if (($assetActivityLog['field_or_relation'] == 'usedBy' || $assetActivityLog['field_or_relation'] == 'managedBy') && $assetActivityLog['final_value'])
            {
                $usedBy = json_decode($assetActivityLog['final_value']);
                $usedByUrl = Config::get('app.url')."/user/{$usedBy->id}";
                $assetActivityLog['final_value'] = "<a href={$usedByUrl}>{$usedBy->full_name}</a>";
            }

            $activity = $this->extraFormatActivityLog($assetActivityLog, 'b', $activity);
        }
    }

    /**
     * method to format exta asset activity log
     * @param array $assetActivityLog
     * @param string $tag
     * @param string $activity
     * @return string $activity
     */
    private function extraFormatActivityLog($assetActivityLog, $tag, $activity)
    {
        $choice = $assetActivityLog['field_or_relation'];
        if (!$assetActivityLog['final_value']) {
          $choice = 'default';
          // setting empty value when field value is empty in activity log
          $assetActivityLog['final_value'] = '<b>empty</b>';
        }
         
        switch ($choice) {
          case 'organization':
            $organization = organization::whereName($assetActivityLog['final_value'])->first();
            $organizationNameWithHyperLink = implode('', ['<a href=', faveoUrl("organizations/{$organization->id}"), ">{$organization->name}</a>"]);

            return $this->setActivityLog($assetActivityLog, $activity, $tag, $organizationNameWithHyperLink);

          case 'department':
            $department = Department::whereName($assetActivityLog['final_value'])->first();
            $departmentNameWithHyperLink = implode('', ['<a href=', faveoUrl("department/{$department->id}"), ">{$department->name}</a>"]);

            return $this->setActivityLog($assetActivityLog, $activity, $tag, $departmentNameWithHyperLink);

          case 'product':
            $product = SdProducts::whereName($assetActivityLog['final_value'])->first();
            $productNameWithHyperLink = implode('', ['<a href=', faveoUrl("service-desk/products/{$product->id}/show"), ">{$product->name}</a>"]);

            return $this->setActivityLog($assetActivityLog, $activity, $tag, $productNameWithHyperLink);

          default:
            return $this->setActivityLog($assetActivityLog, $activity, $tag, $assetActivityLog['final_value']);
        }
    }

    /**
     * method to format and set each asset field activity log
     * @param array $assetActivityLog
     * @param string $activity
     * @param string $tag
     * @param string $fieldValue
     * @return string $activity
     */
    private function setActivityLog($assetActivityLog, $activity, $tag, $fieldValue)
    {
      if ($assetActivityLog['field_or_relation'] === 'assigned_on') {
        $assetActivityLog['field_or_relation'] = 'assigned on';
        $fieldValue = strtotime($fieldValue) ? faveoDate($fieldValue, 'F j, Y, g:i a', agentTimeZone()) : $fieldValue;
      }

      if ($assetActivityLog['log_name'] != 'asset_custom_field') {
        $assetActivityLog['field_or_relation'] = implode(' ', array_map('strtolower', preg_split('/(?=[A-Z])/', $assetActivityLog['field_or_relation'])));
      }
      
      ($assetActivityLog['field_or_relation'] == 'attachment') ?: $activity = implode('', [$activity, 'set ', $assetActivityLog['field_or_relation'], " as <{$tag}>{$fieldValue}</{$tag}>, "]);

      return $activity;
    }

    /**
     * method to format asset pivot activity log
     * @param Activity $assetActivityLogs
     * @param array $assetActivity
     * @return null
     */
    private function formatAssetPivotActivityLog($assetActivityLogs, array &$assetActivity)
    {
        switch($assetActivityLogs[0]['field_or_relation'])
        {
            case 'releases':
                return $this->formatReleaseAttachedToAssetActivityLog($assetActivityLogs, $assetActivity);

            case 'tickets':
                return $this->formatTicketsAttachedToAssetActivityLog($assetActivityLogs, $assetActivity);

            case 'problems':
                return $this->formatProblemsAttachedToAssetActivityLog($assetActivityLogs, $assetActivity);

            case 'changes':
                return $this->formatChangesAttachedToAssetActivityLog($assetActivityLogs, $assetActivity);

            case 'contracts':
                return $this->formatContractsAttachedToAssetActivityLog($assetActivityLogs, $assetActivity);

            default:
                return false;
        }
    }

    /**
     * method to format release attached to asset activity log
     * @param Activity $assetActivityLogs
     * @param array $assetActivity
     * @return null
     */
    private function formatReleaseAttachedToAssetActivityLog($assetActivityLogs, array &$assetActivity)
    {
        $releaseNames = '';
        foreach ($assetActivityLogs as $assetActivityLog) {
            $release = SdReleases::find($assetActivityLog['final_value']);
            $releaseNames = $releaseNames . '<a href='.faveoUrl("service-desk/releases/{$release->id}/show").'>'. "<b>({$release->identifier}) {$release->subject}</b></a>, ";
        }
        $this->formatAssetPivotActivityLogs($assetActivityLogs, $assetActivity, 'release', $releaseNames);
    }

    /**
     * method to format tickets attached or detached in asset activity log
     * @param Activity $assetActivityLogs
     * @param array $assetActivity
     * @return null
     */
    private function formatTicketsAttachedToAssetActivityLog($assetActivityLogs, array &$assetActivity)
    {
        $ticketNames = '';
        foreach ($assetActivityLogs as $assetActivityLog) {
            $ticket = Ticket::with('firstThread')->where('id', $assetActivityLog['final_value'])->first()->toArray();
            $ticketNames = $ticketNames . '<a href='.faveoUrl("thread/{$ticket['id']}").'>'. "<b>({$ticket['ticket_number']}) {$ticket['first_thread']['title']}</b></a>, ";
        }
        $this->formatAssetPivotActivityLogs($assetActivityLogs, $assetActivity, 'ticket', $ticketNames);
    }

    /**
     * method to format problems attached to asset activity log
     * @param Activity $assetActivityLogs
     * @param array $assetActivity
     * @return null
     */
    private function formatProblemsAttachedToAssetActivityLog($assetActivityLogs, array &$assetActivity)
    {
        $problemNames = '';
        foreach ($assetActivityLogs as $assetActivityLog) {
            $problem = SdProblem::find($assetActivityLog['final_value']);
            $problemNames = $problemNames . '<a href='.faveoUrl("service-desk/problem/{$problem->id}/show").'>'. "<b>({$problem->identifier}) {$problem->subject}</b></a>, ";
        }
        $this->formatAssetPivotActivityLogs($assetActivityLogs, $assetActivity, 'problem', $problemNames);
    }

    /**
     * method to format changes attached to asset activity log
     * @param Activity $assetActivityLogs
     * @param array $assetActivity
     * @return null
     */
    private function formatChangesAttachedToAssetActivityLog($assetActivityLogs, array &$assetActivity)
    {
        $changeNames = '';
        foreach ($assetActivityLogs as $assetActivityLog) {
            $change = SdChanges::find($assetActivityLog['final_value']);
            $changeNames = $changeNames . '<a href='.faveoUrl("service-desk/changes/{$change->id}/show").'>'. "<b>({$change->identifier}) {$change->subject}</b></a>, ";
        }
        $this->formatAssetPivotActivityLogs($assetActivityLogs, $assetActivity, 'change', $changeNames);
    }

    /**
     * method to format contracts attached to asset activity log
     * @param Activity $assetActivityLogs
     * @param array $assetActivity
     * @return null
     */
    private function formatContractsAttachedToAssetActivityLog($assetActivityLogs, array &$assetActivity)
    {
        $contractNames = '';
        foreach ($assetActivityLogs as $assetActivityLog) {
            $contract = SdContract::find($assetActivityLog['final_value']);
            $contractNames = $contractNames . '<a href='.faveoUrl("service-desk/contracts/{$contract->id}/show").'>'. "<b>({$contract->identifier}) {$contract->name}</b></a>, ";
        }
        $this->formatAssetPivotActivityLogs($assetActivityLogs, $assetActivity, 'contract', $contractNames);
    }

    /**
     * method to format asset pivot activity logs
     * @param Activity $assetActivityLogs
     * @param array $assetActivity
     * @param string $tagName
     * @param string $serviceNames
     * @return null
     */
    private function formatAssetPivotActivityLogs($assetActivityLogs, &$assetActivity, $tagName, $serviceNames)
    {
      $serviceNames = rtrim($serviceNames, ', ');
      $tagName = count($assetActivityLogs) > 1 ? $tagName.'s' : $tagName;
      $activity = "{$assetActivityLogs[0]['event_name']} $tagName $serviceNames";
      $assetActivity[] = ['id' => $assetActivityLogs[0]['id'], 'creator' => $assetActivityLogs[0]['creator'], 'name' => $activity, 'created_at' => $assetActivityLogs[0]['created_at']];
    }

}
