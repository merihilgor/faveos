<?php
namespace App\Plugins\Calendar\Model;

use Illuminate\Database\Eloquent\Model;

class TaskList extends Model{
    protected $fillable = ['id','name','project_id'];
    protected $table = 'task_lists';

   public function tasks()
   {
       return $this->hasMany('App\Plugins\Calendar\Model\Task');
   }

   public function templates()
   {
       return $this->hasMany('App\Plugins\Calendar\Model\Template');
   }

   public function project()
    {
        return $this->belongsTo('App\Plugins\Calendar\Model\Project');
    }

}
