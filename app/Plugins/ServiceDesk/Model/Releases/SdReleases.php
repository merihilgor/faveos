<?php

namespace App\Plugins\ServiceDesk\Model\Releases;

use Illuminate\Database\Eloquent\Model;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\Plugins\ServiceDesk\Model\Changes\SdChanges;
use App\Plugins\ServiceDesk\Traits\IdentifierHandler;
use App\Traits\Observable;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Traits\LogsActivity;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Models\Activity;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\ActivityLogger;
use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Models\ActivityBatch;
use Auth;

class SdReleases extends Model {
    use Observable, IdentifierHandler, LogsActivity;

    protected $table = 'sd_releases';
    protected $fillable = [
        'id', 
        'description', 
        'subject', 
        'planned_start_date', 
        'planned_end_date', 
        'status_id', 
        'priority_id', 
        'release_type_id', 
        'location_id', 
        'identifier'
        ];

    // Activity log name for release
    protected static $logName = 'release';

    protected static $submitEmptyLogs = false;

    // Attributes included inside change activity log 
    protected static $logAttributes = [
        'description',
        'subject',
        'requester',
        'planned_end_date',
        'planned_start_date',
        'status.name',
        'priority.name',
        'releaseType.name',
        'location.title'
    ];

    protected static $logOnlyDirty = true;

    public function activityLog()
    {
        return $this->hasMany(Activity::class, 'source_id')->where('source_type', 'App\Plugins\ServiceDesk\Model\Releases\SdReleases');
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
        $batch = ActivityBatch::create(['log_name' => 'release_pivot'])->id;
        $eventName = 'attached';
        foreach ($pivotIds as $pivotId) {
            (new SdReleases)->manuallyLogActivityForPivot($model, $relationName, $pivotId, $eventName, $batch);
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
        $batch = ActivityBatch::create(['log_name' => 'release_pivot'])->id;
        $eventName = 'detached';
        foreach ($pivotIds as $pivotId) {
            (new SdReleases)->manuallyLogActivityForPivot($model, $relationName, $pivotId, $eventName, $batch);
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
    public function manuallyLogActivityForPivot($model, $relationName, $pivotId, $eventName, $batch = null, $log = 'release_pivot', $isRelation = 1, $user = null)
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
    
    public function location() {
        return $this->belongsTo('App\Location\Models\Location','location_id');
    }
    
    public function status(){
        return $this->belongsTo('App\Plugins\ServiceDesk\Model\Releases\SdReleasestatus', 'status_id');
    }

    public function priority(){
        return $this->belongsTo('App\Plugins\ServiceDesk\Model\Releases\SdReleasepriorities','priority_id');
    }
    
    public function releaseType(){
        return $this->belongsTo('App\Plugins\ServiceDesk\Model\Releases\SdReleasetypes','release_type_id');
    }

    public function deleteAttachment($id) {
        $table = $this->table;
        \App\Plugins\ServiceDesk\Controllers\Library\UtilityController::deleteAttachments($id, $table);
    }   

    /**
     * relationship with asset, assets linked to release
     */
    public function attachAssets()
    {
        return $this->belongsToMany(SdAssets::class, 'sd_common_asset_relation', 'type_id', 'asset_id')->where('type', 'sd_releases');
    }

    /**
     * relationship with changes, changes linked to release
     */
    public function attachChanges()
    {
        return $this->belongsToMany(SdChanges::class, 'sd_change_release', 'release_id', 'change_id');
    }

    /**
    * delete release
    * @return null
    */
    public function beforeDelete()
    {
        $this->deleteAttachment($this->id);
        $this->attachAssets()->detach();
        $this->attachChanges()->detach();

    }

    /**
     * method to handle created event and update default identifier
     * if identifier value does not exist
     * @param $model
     * @return null
     */
    public function afterCreate($model)
    {
        $this->generateIdentifier($model, 'REL-', SdReleases::class);
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
        $this->generateIdentifier($model, 'REL-', SdReleases::class);
    }

}
