<?php

namespace App\Plugins\ServiceDesk\Model\Problem;

use Illuminate\Database\Eloquent\Model;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Traits\LogsActivity;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Models\Activity;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\ActivityLogger;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\Plugins\ServiceDesk\Model\Changes\SdChanges;
use App\Model\helpdesk\Ticket\Tickets as Ticket;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Models\ActivityBatch;
use Auth;
use App\User;
use App\Plugins\ServiceDesk\Model\Assets\SdImpactypes;
use App\Traits\Observable;
use App\Plugins\ServiceDesk\Traits\IdentifierHandler;

class SdProblem extends Model {

    use Observable, LogsActivity, IdentifierHandler;

    protected $table = 'sd_problem';
    protected $fillable = ['id', 'requester_id', 'name', 'subject', 'description', 'status_type_id', 'priority_id', 'impact_id', 'location_id', 'group_id', 'agent_id', 'assigned_id',
        'department_id','identifier',
    ];

    // Activity log name for changes
    protected static $logName = 'problem';

    protected static $submitEmptyLogs = false;

    // Attributes included inside change activity log 
    protected static $logAttributes = [
        'identifier',
        'subject',
        'requester',
        'assignedTo',
        'status.name',
        'priority.priority',
        'impact.name',
        'location.title',
        'department.name',
        'identifier'
    ];

    /**
     * fetch only changed atrributes not all attributes if logOnlyDirty is true
     * fetch all attributes even which are not changes if logOnlyDirty is false
     */
    protected static $logOnlyDirty = true;

    /**
     * method to get change activity log based on id
    */
    public function activityLog()
    {
        return $this->hasMany(Activity::class, 'source_id')->where('source_type', 'App\Plugins\ServiceDesk\Model\Problem\SdProblem');
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
    public function manuallyLogActivityForPivot($model, $relationName, $pivotId, $eventName, $batch = null, $log = 'problem_pivot', $isRelation = 1, $user = null)
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
    

    // /**
    //  * detach the problem fro relation table
    //  */
    public function detachRelation() {
        $relation = new \App\Plugins\ServiceDesk\Model\Common\TicketRelation();
        $table = $this->table;
        $id = $this->attributes['id'];
        $owner = "$table:$id";
        $relations = $relation->where('owner',$owner)->get();
        if($relations->count()>0){
            foreach($relations as $rel){
                if($rel){
                    $rel->delete();
                }
            }
        }
    }
    
    public function changeRelaion(){
       $through = "App\Plugins\ServiceDesk\Model\Problem\ProblemChangeRelation";
        $firstKey = 'problem_id';
        return $this->hasMany($through, $firstKey);
    }

    /**
     * delete the attachment
     * @param int $id
     */
    public function deleteAttachment($id) {
        $table = $this->table;
        \App\Plugins\ServiceDesk\Controllers\Library\UtilityController::deleteAttachments($id, $table);
    }
    

    public function beforeDelete() {
        $id = $this->id;
        $this->deleteAttachment($id);
        $this->detachRelation();
        $this->changeRelaion()->delete();
    }
    
    
    public function generalAttachments($identifier){
        $table = $this->table;
        $id = $this->attributes['id'];
        //$identifier = "root-cause";
        $owner = "$table:$identifier:$id";
        $attachment = new \App\Plugins\ServiceDesk\Model\Common\Attachments();
        $attachments = $attachment->where('owner',$owner)->get();
        return $attachments;
    }

    public function getGeneralByIdentifier($identifier){
        $table = $this->table;
        $id = $this->attributes['id'];
        $owner = "$table:$id";
        $generals = new \App\Plugins\ServiceDesk\Model\Common\GeneralInfo();
        $general = $generals->where('owner',$owner)->where('key',$identifier)->first();
        return $general;
        
    }
    
     public function subject(){
        $id = $this->attributes['id'];
        $title = $this->attributes['subject'];
        $subject = "<a href=".url('service-desk/problem/'.$id.'/show').">".$title."</a>";
        return $subject;
    }
    
    /**
     * get the status name
     * @return string
     */
    public function statuses() {
        $value = "--";
        $status = $this->attributes['status_type_id'];
        if ($status) {
            $statuses = $this->belongsTo('App\Model\helpdesk\Ticket\Ticket_Status', 'status_type_id')->first();
            if ($statuses) {
                $value = $statuses->name;
            }
        }
        return ucfirst($value);
    }

    // relation with user
    public function requester()
    {
        return $this->hasOne(\App\User::class, 'id', 'requester_id');
    }

    // relation with department
    public function department()
    {
        return $this->hasOne(\App\Model\helpdesk\Agent\Department::class, 'id', 'department_id');
    }

    // relation with impact
    public function impact()
    {
        return $this->hasOne(SdImpactypes::class, 'id', 'impact_id');
    }

    // relation with impact
    public function status()
    {
        return $this->hasOne(\App\Model\helpdesk\Ticket\Ticket_Status::class, 'id', 'status_type_id');
    }

    // relation with location
    public function location()
    {
        return $this->hasOne(\App\Location\Models\Location::class, 'id', 'location_id');
    }

    // relation with priority
    public function priority()
    {
        return $this->hasOne(\App\Model\helpdesk\Ticket\Ticket_Priority::class, 'priority_id', 'priority_id');
    }

    // relation with user
    public function assignedTo()
    {
        return $this->hasOne(\App\User::class, 'id', 'assigned_id');
    }

    /**
    * relationship with asset
    */
    public function attachAssets()
    {
        return $this->belongsToMany(SdAssets::class, 'sd_common_asset_relation', 'type_id', 'asset_id')->where('type', 'sd_problem');
    }

    /**
     * relationship with ticket, tickets linked to problem
     */
    public function attachTickets()
    {
        return $this->belongsToMany(Ticket::class, 'sd_common_ticket_relation', 'type_id', 'ticket_id')->where('sd_common_ticket_relation.type', 'sd_problem');
    }

    /**
     * relationship with change, change linked to problem
     */
    public function attachChange()
    {
        return $this->belongsToMany(SdChanges::class,'sd_problem_change','problem_id','change_id');
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
        $batch = ActivityBatch::create(['log_name' => 'problem_pivot'])->id;
        $eventName = 'attached';
        foreach ($pivotIds as $pivotId) {
            (new SdProblem)->manuallyLogActivityForPivot($model, $relationName, $pivotId, $eventName, $batch);
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
        $batch = ActivityBatch::create(['log_name' => 'problem_pivot'])->id;
        $eventName = 'detached';
        foreach ($pivotIds as $pivotId) {
            (new SdProblem)->manuallyLogActivityForPivot($model, $relationName, $pivotId, $eventName, $batch);
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
        $this->generateIdentifier($model, 'PRB-', SdProblem::class);
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
        $this->generateIdentifier($model, 'PRB-', SdProblem::class);
    }

}
