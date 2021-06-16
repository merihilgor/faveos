<?php

namespace App\Plugins\Calendar\Model;

use App\Model\helpdesk\Ticket\Tickets;
use Illuminate\Database\Eloquent\Model;
use App\Model\helpdesk\Settings\System;
use App\User;

class Task extends Model
{

    protected $table = 'tasks';

    // protected $dates = ['task_end_date', 'task_start_date', 'due_alert'];

    protected $fillable = [
        'id', 'task_name', 'task_description', 'task_start_date',
        'task_end_date', 'status', 'is_private', 'ticket_id',
        'parent_id', 'is_complete', 'created_by', 'due_alert',
        'task_list_id','due_alert_text','alert_repeat_text'
    ];

    protected $appends = ['url','assigned_agents'];

    public function assignedTo()
    {
        return $this->hasMany('App\Plugins\Calendar\Model\TaskAssignees', 'task_id');
    }

    public function taskList()
    {
        return $this->belongsTo('App\Plugins\Calendar\Model\TaskList', 'task_list_id');
    }

    public function Alerts()
    {
        return $this->hasOne('App\Plugins\Calendar\Model\TaskAlerts', 'task_id');
    }

    public function getUrlAttribute()
    {
        return url('tasks/task/' . $this->id);
    }

    public function getTaskStartDateAttribute($value)
    {
        if (!$value) {
            return null;
        } 
        return \Carbon\Carbon::parse($value, 'UTC')->setTimezone(System::first()->time_zone);
    }

    public function getTaskEndDateAttribute($value)
    {
        if (!$value) {
            return null;
        } 
        return \Carbon\Carbon::parse($value, 'UTC')->setTimezone(System::first()->time_zone);
    }

    public function getDueAlertAttribute($value)
    {
        return \Carbon\Carbon::parse($value, 'UTC')->setTimezone(System::first()->time_zone);
    }

    public function ticket()
    {
        return $this->belongsTo(Tickets::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getAssignedAgentsAttribute()
    {
        $assignees = TaskAssignees::where('task_id', $this->id)->get(['user_id']);
        $assignedAgents = $assignees->map(function ($item, $key) {
            $user = User::where('id', $item->user_id)->first(['id','first_name','last_name']);
            return ['id' => $user->id, 'name' => $user->meta_name];
        });
        return $assignedAgents;
    }

    public function getCreatedByAttribute($value)
    {
        return User::where('id', $value)->first(['id','first_name','last_name']);
    }
}
