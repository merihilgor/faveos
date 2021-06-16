<?php
namespace App\Plugins\Calendar\Model;

use Illuminate\Database\Eloquent\Model;

class Project extends Model{
    protected $fillable = ['id','name'];
    protected $table = 'projects';

    public function taskLists()
    {
        return $this->hasMany('\App\Plugins\Calendar\Model\TaskList','project_id');
    }


    public function tasks()
    {
        return $this->hasManyThrough(Task::class,TaskList::class,'project_id','task_list_id','id','id');
    }
}
