<?php

namespace App\Plugins\Calendar\Model;

use Illuminate\Database\Eloquent\Model;

class Template extends Model {

    protected $table = 'task_templates';

    public function taskList()
    {
        return $this->belongsTo('App\Plugins\Calendar\Model\TaskList','task_list_id');
    }

}
