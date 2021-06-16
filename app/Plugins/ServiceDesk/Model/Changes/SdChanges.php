<?php
namespace App\Plugins\ServiceDesk\Model\Changes;
use Illuminate\Database\Eloquent\Model;
use App\Model\helpdesk\Agent\Teams;
use App\Model\helpdesk\Agent\Department;
use App\Plugins\ServiceDesk\Model\Common\SdUser;
use App\Plugins\ServiceDesk\Model\Assets\SdImpactypes as SdImpacttypes;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\Plugins\ServiceDesk\Model\Releases\SdReleases;
use App\Plugins\ServiceDesk\Controllers\Library\UtilityController;
use App\Traits\Observable;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Traits\LogsActivity;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Models\Activity;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\ActivityLogger;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Models\ActivityBatch;
use Auth;
use App\Plugins\ServiceDesk\Model\Common\Ticket;
use App\Plugins\ServiceDesk\Model\Problem\SdProblem;
use App\Plugins\ServiceDesk\Traits\IdentifierHandler;

class SdChanges extends Model
{
    use Observable, LogsActivity, IdentifierHandler;

    protected $table = 'sd_changes';
    protected $fillable = [
        'id',
        'requester_id',
        'description',
        'subject',
        'status_id',
        'priority_id',
        'change_type_id',
        'impact_id',
        'location_id',
        'approval_id',
        'team_id',
        'department_id',
        'identifier'
        ];

    // Activity log name for changes
    protected static $logName = 'change';

    protected static $submitEmptyLogs = false;

    // Attributes included inside change activity log 
    protected static $logAttributes = [
        'subject',
        'requester',
        'status.name',
        'priority.name',
        'changeType.name',
        'impactType.name',
        'location.title',
        'team.name',
        'department.name',
        'identifier'
    ];

    /**
     * fetch only changed atrributes not all attributes if logOnlyDirty is true
     * fetch all attributes even which are not changes if logOnlyDirty is false
     */
    protected static $logOnlyDirty = true;

    public function location() {
        return $this->belongsTo('App\Location\Models\Location', 'location_id');
    }
    
    public function status(){
        return $this->belongsTo('App\Plugins\ServiceDesk\Model\Changes\SdChangestatus', 'status_id');
    }
    
    public function priority(){
        return $this->belongsTo('App\Plugins\ServiceDesk\Model\Changes\SdChangepriorities','priority_id');
    }
    
    public function changeType(){
        return $this->belongsTo('App\Plugins\ServiceDesk\Model\Changes\SdChangetypes','change_type_id');
    }

    /**
     * relationship with team
     */
    public function team(){
        return $this->belongsTo(Teams::class, 'team_id');
    }

    /**
     * relationship with department
     */
    public function department(){
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * relationship with requester (user)
     */
    public function requester(){
        return $this->belongsTo(SdUser::class, 'requester_id');
    }

    /**
     * relationship with impactTypes
     */
    public function impactType()
    {
        return $this->belongsTo(SdImpacttypes::class, 'impact_id');
    }

    /**
     * To get all related records from user_assign_organization table
     */
    public function changeOrganizations()
    {
        return $this->belongsTo(
            'App\Model\helpdesk\Agent_panel\User_org',
            'requester_id',
            'user_id'
        );
    }

    /**
     * relationship with asset, assets linked to change
     */
    public function attachAssets()
    {
        return $this->belongsToMany(SdAssets::class, 'sd_common_asset_relation', 'type_id', 'asset_id')->where('type', 'sd_changes');
    }

    /**
     * relationship with releases, releases linked to change
     */
    public function attachReleases()
    {
        return $this->belongsToMany(SdReleases::class, 'sd_change_release', 'change_id', 'release_id');
    }

    /**
     * delete change
     * @param $model
     * @return null
     */
    public function beforeDelete()
    {
        $this->attachAssets()->detach();
        $this->attachReleases()->detach();
        $this->attachTickets()->detach();
        $activityLogs = $this->activityLog()->get();
        foreach ($activityLogs as $activityLog) {
            $activityLog->delete();
        }
        (new UtilityController)->deleteAttachments($this->id, 'sd_changes');
    }

    /**
     * method to get change activity log based on id
    */
    public function activityLog()
    {
        return $this->hasMany(Activity::class, 'source_id')->where('source_type', 'App\Plugins\ServiceDesk\Model\Changes\SdChanges');
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
    public function manuallyLogActivityForPivot($model, $relationName, $pivotId, $eventName, $batch = null, $log = 'change_pivot', $isRelation = 1, $user = null)
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
     * relationship with ticket, tickets linked to change
     */
    public function attachTickets()
    {
        return $this->belongsToMany(Ticket::class, 'sd_change_ticket', 'change_id', 'ticket_id');
    }

    /**
     * relationship with problem, problems linked to change
     */
    public function attachProblems()
    {
        return $this->belongsToMany(SdProblem::class, 'sd_problem_change', 'change_id', 'problem_id');
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
        $batch = ActivityBatch::create(['log_name' => 'change_pivot'])->id;
        $eventName = 'attached';
        foreach ($pivotIds as $pivotId) {
            (new SdChanges)->manuallyLogActivityForPivot($model, $relationName, $pivotId, $eventName, $batch);
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
        $batch = ActivityBatch::create(['log_name' => 'change_pivot'])->id;
        $eventName = 'detached';
        foreach ($pivotIds as $pivotId) {
            (new SdChanges)->manuallyLogActivityForPivot($model, $relationName, $pivotId, $eventName, $batch);
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
        $this->generateIdentifier($model, 'CHN-', SdChanges::class);
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
        $this->generateIdentifier($model, 'CHN-', SdChanges::class);
    }
    
}
