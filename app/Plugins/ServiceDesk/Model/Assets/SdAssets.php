<?php
namespace App\Plugins\ServiceDesk\Model\Assets;

use Illuminate\Database\Eloquent\Model;
use App\Plugins\ServiceDesk\Model\Common\AssetRelation;
use App\Plugins\ServiceDesk\Model\Assets\CommonAssetRelation;
use App\Plugins\ServiceDesk\Model\Common\CommonTicketRelation;
use App\Model\helpdesk\Form\CustomFormValue;
use App\Plugins\ServiceDesk\Model\Problem\SdProblem;
use App\Plugins\ServiceDesk\Model\Changes\SdChanges;
use App\Plugins\ServiceDesk\Model\Releases\SdReleases;
use App\Plugins\ServiceDesk\Model\Contract\SdContract;
use App\Plugins\ServiceDesk\Model\Common\Ticket;
use App\Traits\Observable;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Traits\LogsActivity;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Models\Activity;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\ActivityLogger;
use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Models\ActivityBatch;
use Auth;
use App\Model\helpdesk\Agent\Department;
use App\Plugins\ServiceDesk\Model\Assets\SdImpactypes;
use App\Location\Models\Location;
use App\User;
use App\Plugins\ServiceDesk\Model\Assets\SdAssettypes;
use App\Plugins\ServiceDesk\Model\Products\SdProducts;
use App\Plugins\ServiceDesk\Controllers\Library\UtilityController;
use App\Model\helpdesk\Agent_panel\Organization;
use App\Plugins\ServiceDesk\Traits\IdentifierHandler;
use App\Plugins\ServiceDesk\Model\Assets\SdAssetStatus;

class SdAssets extends Model {
    use Observable,LogsActivity,IdentifierHandler;

    protected $table = 'sd_assets';
    protected $fillable = [
        'id',
        'name',
        'description',
        'department_id',
        'asset_type_id',
        'impact_type_id',
        'managed_by_id',
        'used_by_id',
        'location_id',
        'assigned_on',
        'product_id',
        'identifier',
        'organization_id',
        'status_id'
    ];

    // Activity log name for asset
    protected static $logName = 'asset';

    protected static $submitEmptyLogs = false;

    // Attributes included inside asset activity log 
    protected static $logAttributes = [
        'name',
        'department.name',
        'assetType.name',
        'impactType.name',
        'managedBy',
        'usedBy',
        'location.title',
        'assigned_on',
        'product.name',
        'identifier',
        'organization.name',
        'assetStatus.name'
    ];

    /**
     * fetch only changed atrributes not all attributes if logOnlyDirty is true
     * fetch all attributes even which are not changes if logOnlyDirty is false
     */
    protected static $logOnlyDirty = true;

    /**
     * relationship with department
     */
    public function department() {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * relationship with impact type
     */
    public function impactType() {
        return $this->belongsTo(SdImpactypes::class, 'impact_type_id');
    }

    /**
     * relationship with location
     */
    public function location() {
        return $this->belongsTo(Location::class, 'location_id');
    }

    /**
     * relationship with user as used by
     */
    public function usedBy() {
        return $this->belongsTo(User::class, 'used_by_id');
    }

    /**
     * relationship with user as managed by
     */
    public function managedBy() {
        return $this->belongsTo(User::class, 'managed_by_id');
    }

    /**
     * relationship with asset type
     */
    public function assetType() {
        return $this->belongsTo(SdAssettypes::class);
    }

    /**
     * relationship with product
     */
    public function product() {
        return $this->belongsTo(SdProducts::class, 'product_id');
    }

    public function deleteAttachment($id) {
        \App\Plugins\ServiceDesk\Controllers\Library\UtilityController::deleteAssetRelation($id);
        $table = $this->table;
        \App\Plugins\ServiceDesk\Controllers\Library\UtilityController::deleteAttachments($id, $table);
    }

    /**
     * method to delete asset
     */
    public function beforeDelete() {
        $this->deleteAttachment($this->id);
        $this->contracts()->detach();
        $this->problems()->detach();
        $this->changes()->detach();
        $this->releases()->detach();
        $this->tickets()->detach();
        $activityLogs = $this->activityLog()->get();
        foreach ($activityLogs as $activityLog) {
            $activityLog->delete();
        }
        
    }

    /**
     * relationship with organization
     */
    public function organization() {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    /**
     * relationship with problem
     */
    public function problems()
    {
        return $this->belongsToMany(SdProblem::class, 'sd_common_asset_relation', 'asset_id', 'type_id')->wherePivot('type', 'sd_problem')->withTimestamps();
    }

    /**
     * relationship with change
     */
    public function changes()
    {
        return $this->belongsToMany(SdChanges::class, 'sd_common_asset_relation', 'asset_id', 'type_id')->wherePivot('type', 'sd_changes')->withTimestamps();
    }

    /**
     * relationship with release
     */
    public function releases()
    {
        return $this->belongsToMany(SdReleases::class, 'sd_common_asset_relation', 'asset_id', 'type_id')->wherePivot('type', 'sd_releases')->withTimestamps();
    }  

    /**
     * relationship with contract
     */
    public function contracts()
    {
        return $this->belongsToMany(SdContract::class, 'sd_common_asset_relation', 'asset_id', 'type_id')->wherePivot('type', 'sd_contracts')->withTimestamps();
    }

    /**
    * relationship with ticket
    */
    public function tickets(){
        return $this->belongsToMany(Ticket::class, 'sd_common_ticket_relation', 'type_id', 'ticket_id')->wherePivot('type', 'sd_assets')->withTimestamps();
    }

    /**
     * relationship for connecting custom field values to ticket
     */
    public function customFieldValues()
    {
        return $this->morphMany(CustomFormValue::class, 'custom');
    }

    /**
     * method to get asset activity log based on id
    */
    public function activityLog()
    {
        return $this->hasMany(Activity::class, 'source_id')->where('source_type', 'App\Plugins\ServiceDesk\Model\Assets\SdAssets');
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
    public function manuallyLogActivityForPivot($model, $relationName, $pivotId, $eventName, $batch = null, $log = 'asset_pivot', $isRelation = 1, $user = null)
    {
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

    /**
     * relationship for connecting custom field values to asset
     * only asset form builder custom field values
     */
    public function customFieldValuesForAssetFormBuilderOnly()
    {
        return $this->customFieldValues()->where('type', '');
    }

    /**
     * relationship for connecting custom field values to asset
     * only asset type custom field values
     */
    public function customFieldValuesForAssetType()
    {
        return $this->customFieldValues()->where('type', 'asset_type');
    }

    /**
     * relationship for connecting custom field values to asset
     * only department custom field values
     */
    public function customFieldValuesForDepartment()
    {
        return $this->customFieldValues()->where('type', 'department');
    }

    /**
     * method to delete previous custom field previous values of sd formgroup
     * while the asset type is changed
     * @param $assetTypeId
     * @return null
     */
    public function assetTypeChangedAction($assetTypeId)
    {
        $previousAssetTypeId = $this->asset_type_id;
        if ($previousAssetTypeId != $assetTypeId) {
            foreach ($this->customFieldValuesForAssetType as $fieldValue) {
                $fieldValue->delete();
            }
        }
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
        $batch = ActivityBatch::create(['log_name' => 'asset_pivot'])->id;
        $eventName = 'attached';
        foreach ($pivotIds as $pivotId) {
            (new SdAssets)->manuallyLogActivityForPivot($model, $relationName, $pivotId, $eventName, $batch);
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
        $batch = ActivityBatch::create(['log_name' => 'asset_pivot'])->id;
        $eventName = 'detached';
        foreach ($pivotIds as $pivotId) {
            (new SdAssets)->manuallyLogActivityForPivot($model, $relationName, $pivotId, $eventName, $batch);
        }
    }

    /**
     * method to handle created event and update default identifier
     * if identifier value does not exist
     * @param $model
     * @return null
     */
    public function afterCreate($model)
    {
        $this->generateIdentifier($model, 'AST-', SdAssets::class);
    }

    /**
     * method to handle updated event and update default identifier
     * if identifier value does not exist
     * @param $model
     * @return null
     */
    public function afterUpdate($model)
    {
        $model->identifier = $this->attributes['identifier'];
        $this->generateIdentifier($model, 'AST-', SdAssets::class);
    } 

    /*
     * relationship with asset status
     */
    public function assetStatus()
    {
        return $this->belongsTo(SdAssetStatus::class, 'status_id');
    }
}
