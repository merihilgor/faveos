<?php

namespace App\Plugins\ServiceDesk\Model\Contract;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Observable;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\Plugins\ServiceDesk\Traits\IdentifierHandler;
use App\Plugins\ServiceDesk\Model\Common\SdUser;
use App\Plugins\ServiceDesk\Model\Common\Email;
use App\Plugins\ServiceDesk\Model\Common\EmailSources;
use App\User;
use App\Model\helpdesk\Agent_panel\Organization;
use App\Plugins\ServiceDesk\Controllers\Library\UtilityController;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Traits\LogsActivity;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Models\Activity;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\ActivityLogger;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Models\ActivityBatch;
use Auth;
use App\Plugins\ServiceDesk\Model\Common\Ticket;

/**
 * SdContract Model
 * 
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 *
 * @author Abhishek Kumar Shashi <krishna.vishwakarma@ladybirdweb.com>
*/
class SdContract extends Model
{
    use Observable,IdentifierHandler,LogsActivity;

    protected $table = 'sd_contracts';
    protected $fillable = [
        'id',
        'name',
        'description',
        'cost',
        'contract_type_id',
        'approver_id',
        'vendor_id',
        'license_type_id',
        'licensce_count',
        'notify_expiry',
        'contract_start_date',
        'contract_end_date',
        'status_id',
        'renewal_status_id',
        'purpose_of_rejection',
        'owner_id',
        'notify_before',
        'identifier',
        'purpose_of_approval'
    ];

    // Activity log name for contracts
    protected static $logName = 'contract';

    protected static $submitEmptyLogs = false;

    // Attributes included inside contract activity log 
    protected static $logAttributes = [
        'name',
        'cost',
        'contractType.name',
        'approverRelation',
        'vendor',
        'licence.name',
        'licensce_count',
        'attachment',
        'contract_start_date',
        'contract_end_date',
        'contractStatus.name',
        'contractRenewalStatus.name',
        'purpose_of_rejection',
        'owner',
        'notify_before',
        'purpose_of_approval' 
    ];

    /**
     * fetch only changed atrributes not all attributes if logOnlyDirty is true
     * fetch all attributes even which are not changes if logOnlyDirty is false
     */
    protected static $logOnlyDirty = true;

    /**
     * method to get contract activity log based on id
    */
    public function activityLog()
    {
        return $this->hasMany(Activity::class, 'source_id')->where('source_type', 'App\Plugins\ServiceDesk\Model\Contract\SdContract');
    }

    /**
     * method to handle pivot attached event
     * @param $model
     * @param $relationName
     * @param $pivotIds
     * @param $pivotIdsAttributes
     * @return null
     */
    public function afterPivotAttached($model, $relationName, $pivotIds, $pivotIdsAttributes)
    {
        $batch = ActivityBatch::create(['log_name' => 'contract_pivot'])->id;
        $eventName = 'attached';
        foreach ($pivotIds as $pivotId) {
            (new SdContract)->manuallyLogActivityForPivot($model, $relationName, $pivotId, $eventName, $batch);
        }
    }

    /**
     * method to handle pivot detached event
     * @param $model
     * @param $relationName
     * @param $pivotIds
     * @return null
     */
    public function afterPivotDetached($model, $relationName, $pivotIds)
    {
        $batch = ActivityBatch::create(['log_name' => 'contract_pivot'])->id;
        $eventName = 'detached';
        foreach ($pivotIds as $pivotId) {
            (new SdContract)->manuallyLogActivityForPivot($model, $relationName, $pivotId, $eventName, $batch);
        }
    }

    /**
     * method to add activity log manually
     * @param Model Instance $model
     * @param string $relationName
     * @param int $pivotId
     * @param string $eventName
     * @param string $log
     * @param int $batch
     * @return null
     */
    public function manuallyLogActivityForPivot($model, $relationName, $pivotId, $eventName, $batch = null, $log = 'contract_pivot', $isRelation = 1, $user = null)
    {
        if($relationName=='attachAgents')
            $relationName = 'notifyTo';
        $userModel = Auth::user() ?: $user;
        $batch = ActivityBatch::updateOrCreate(['id' => $batch], ['log_name' => $log])->id;
        $logger = app(ActivityLogger::class)
            ->useBatch($batch)
            ->useLog($log)
            ->performedOn($model)
            ->useFieldOrRelation($relationName)
            ->useIsRelation($isRelation)
            ->useFinalValue($pivotId)
            ->useEventName($eventName)
            ->causedBy($userModel);
        $logger->log();
    }
    
    public function contractType(){
        return $this->belongsTo('App\Plugins\ServiceDesk\Model\Contract\ContractType','contract_type_id');
    }
    
    public function approverRelation(){
        return $this->belongsTo('App\User','approver_id');
    }
    
    public function vendor(){
        return $this->belongsTo('App\Plugins\ServiceDesk\Model\Vendor\SdVendors','vendor_id');
    }
    
    public function licence(){
        return $this->belongsTo('App\Plugins\ServiceDesk\Model\Contract\License','license_type_id');
    }

    /**
    * relationship with Agents to notify about contract expiry
    */
    public function notifyAgents()
    {
        return $this->belongsToMany('App\Plugins\ServiceDesk\Model\Common\SdUser', 'sd_contract_user', 'contract_id', 'agent_id');
    }

    /**
    * relationship with SdContractStatus for contract status
    */
    public function contractStatus()
    {
        return $this->belongsTo('App\Plugins\ServiceDesk\Model\Contract\SdContractStatus', 'status_id', 'id');
    }

    /**
    * relationship with SdContractStatus for contract renewal
    */
    public function contractRenewalStatus()
    {
        return $this->belongsTo('App\Plugins\ServiceDesk\Model\Contract\SdContractStatus', 'renewal_status_id', 'id');
    }

    /**
    * relation with SdUser for Contract owner
    */
    public function owner(){
        return $this->belongsTo('App\Plugins\ServiceDesk\Model\Common\SdUser','owner_id', 'id');
    }

    /**
    * relation with SdContractThread for contract history
    */
    public function contractThread(){
        return $this->belongsTo('App\Plugins\ServiceDesk\Model\Contract\SdContractThread', 'id', 'contract_id');
    }

    /**
     * delete contract
     * @param $model
     * @return 
     */
    public function beforeDelete($model)
    {
      // detach all associated assets
      $model->attachAsset()->detach();
      
      $threads = $model->contractThread()->get();

       // detach all contract associated contract threads
      foreach ($threads as $thread) {
          $thread->delete();
      }

      // detach all notify agents
      $model->notifyAgents()->detach();
     
    }

    /**
     * relationship with asset, assets linked to contract
     */
    public function attachAsset()
    {
        return $this->belongsToMany(SdAssets::class, 'sd_common_asset_relation', 'type_id', 'asset_id')->where('type', 'sd_contracts');
    }

    /**
     * method to handle created event and update default identifier
     * if identifier value does not exist
     * @param $model
     * @return null
     */
    public function afterCreate($model)
    {
        $this->generateIdentifier($model, 'CNTR-', SdContract::class);
    }

    /**
     * method to handle updated event and update default identifier
     * if identifier value does not exist
     * @param $model
     * @return null
     */
    public function afterUpdate($model)
    {
        $this->generateIdentifier($model, 'CNTR-', SdContract::class);
    }

    public function attachUser()
    {
        return $this->belongsToMany(User::class, 'sd_contract_user_organization', 'contract_id', 'user_id');
    }

    /**
     * relationship with organization, organizations linked to contract
     */
    public function attachOrganization()
    {
        return $this->belongsToMany(Organization::class, 'sd_contract_user_organization', 'contract_id', 'organization_id');
    }

    /**
     * relationship with agent, agents linked to contract
     */
    public function attachAgents()
    {
        return $this->belongsToMany(SdUser::class, 'sd_contract_user', 'contract_id', 'agent_id');
    
    }

    /**
     * relationship with emails, emails linked to contract
     */
    public function emails()
    {
        return $this->morphToMany(Email::class,'source','sd_emails_sources','source_id','email_id');
    }

}
