<?php 
namespace App\Plugins\Calendar\Model;

use App\Plugins\Calendar\Model\Task;
use Illuminate\Database\Eloquent\Model;

class TaskAssignees extends Model
{

    protected $table = 'task_assignees';

    protected $fillable = ['id', 'task_id', 'user_id', 'team_id'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
