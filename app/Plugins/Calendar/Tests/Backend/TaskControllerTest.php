<?php

namespace App\Plugins\Calendar\Tests\Backend;

use App\Model\helpdesk\Ticket\Tickets;
use App\Model\MailJob\QueueService;
use App\Plugins\Calendar\Jobs\TaskNotificationJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Queue;
use Tests\AddOnTestCase;
use App\Plugins\Calendar\Model\Task;
use App\Plugins\Calendar\Model\Project;
use App\Plugins\Calendar\Model\TaskList;

class TaskControllerTest extends AddOnTestCase
{
    public function test_IndexMethod_ReturnsAllTasks_With_All_Details()
    {
        $this->getLoggedInUserForWeb('agent');
        $project = factory(Project::class, 1)->create()->first();
        $taskList = factory(TaskList::class, 1)->create()->first();
        $taskList->project_id = $project->id;
        $taskList->save();
        $tasks = factory(Task::class, 1)->create()->first();
        $tasks->update(['task_list_id'=>$taskList->id]);
        $response = $this->call('GET', '/tasks/task');
        $response->assertOk();
    }

    public function test_ReturnTasksMethod_returnsFilteredTasksBasedOnValidProjects()
    {
        $this->getLoggedInUserForWeb('agent');
        $project = factory(Project::class, 1)->create();
        $taskList = factory(TaskList::class, 1)->create()->first();
        $taskList->project_id = $project->first()->id;
        $taskList->save();
        $tasks = factory(Task::class, 3)->create();
        $tasks->first()->update(['task_list_id'=>$taskList->id,'task_name' => 'This is it..','created_by' =>\Auth::user()->id]);
        $tasks->get(1)->update(['task_list_id'=>$taskList->id,'task_name' => 'This is 2 it..','created_by' =>\Auth::user()->id  ]);
        $response  = $this->call('GET', 'tasks/get-all-tasks', [
            'projects' => array($project->first()->id)
        ]);
        $response->assertOk()->assertJsonFragment(['task_name' => 'This is it..','task_name' => 'This is 2 it..','total' => 2]);
    }

    public function test_ReturnTasksMethod_returnsNoTasksOnInValidProjects()
    {
        $this->getLoggedInUserForWeb('agent');
        $project = factory(Project::class, 1)->create();
        $taskList = factory(TaskList::class, 1)->create()->first();
        $taskList->project_id = $project->first()->id;
        $taskList->save();
        $tasks = factory(Task::class, 1)->create();
        $tasks->first()->update(['task_list_id'=>$taskList->id,]);
        
        $response  = $this->call('GET', 'tasks/get-all-tasks', [
            'projects' => array(10001)
        ]);
        $response->assertOk()->assertJsonFragment(['total' => 0]);
    }

    public function test_ReturnTasksMethod_returnsNoTasksBasedOnInValidTickets()
    {
        $this->getLoggedInUserForWeb('agent');
        $tasks = factory(Task::class, 1)->create();
        $response  = $this->call('GET', 'tasks/get-all-tasks', [
            'ticket_ids' => array(1876)
        ]);
        $response->assertOk()->assertJsonFragment(['total' => 0]);
    }


    public function test_ReturnTaskMethod_returnsNumberOfTasks_SpecifiedInLimit()
    {
        $this->getLoggedInUserForWeb('agent');
        $tasks = factory(Task::class, 5)->create();
        $response = $this->call('GET', 'tasks/get-all-tasks', [
            'limit' => 3
        ]);
        $response->assertOk()->assertJsonFragment(['per_page'=>3]);
    }

    public function test_ReturnTasksMethod_returnsFilteredTasksBasedOnValidAssignees()
    {
        $this->getLoggedInUserForWeb('agent');
        $project = factory(Project::class, 1)->create();
        $taskList = factory(TaskList::class, 1)->create()->first();
        $taskList->project_id = $project->first()->id;
        $taskList->save();
        $tasks = factory(Task::class, 3)->create();
        $tasks->first()->update(['task_list_id'=>$taskList->id,'task_name' => 'This is it..']);
        $tasks->get(1)->update(['task_list_id'=>$taskList->id,'task_name' => 'This is 2 it..']);
        $tasks->get(2)->update(['task_list_id'=>$taskList->id,'task_name' => 'This is not it..']);
        \DB::table('task_assignees')->insert([[
            'task_id' => $tasks->first()->id,
            'user_id' => \Auth::user()->id
        ],[
            'task_id' => $tasks->get(1)->id,
            'user_id' => \Auth::user()->id
        ]
        ]);
        $response  = $this->call('GET', 'tasks/get-all-tasks', [
            'assigned_to' => array(\Auth::user()->id)
        ]);
        $response->assertOk()
        ->assertJsonFragment(['task_name' => 'This is it..','task_name' => 'This is 2 it..','total' => 2])
        ->assertJsonMissing(['task_name' => 'This is not it..']);
    }
    
    public function test_DestroyMethod_Fails_WhenTriedToDelete_ByTheUserWhoHasNotCreatedIt()
    {
        $this->getLoggedInUserForWeb('agent');
        $taskId = factory(Task::class, 1)->create(['created_by' => 1000])->first()->id;
        $this->call('DELETE', "/tasks/task-delete/$taskId")->assertStatus(400)
        ->assertJsonFragment(["message" => "You are not authorized to delete this task."]);
    }

    public function test_createMethodCreatesTheTaskAndPushesToQueueForNotifications_ForSuccess()
    {
        $this->getLoggedInUserForWeb('agent');
        QueueService::where('status', 1)->update(['status' => 0]);
        QueueService::where('short_name', 'sync')->update(['status' => 1]);
        Queue::fake();
        $response = $this->post(url('/tasks/task'), [
            'task_start_date' => Carbon::now(),
            'task_end_date' => Carbon::now()->addDays(12),
            'task_name' => "xcdsd",
            'task_description' => "sda",
            'is_private' => 1,
            'status' => 'Open'
        ]);
        $response->assertOk()->assertJsonFragment(["message" => "Task Created Successfully."]);
        $this->assertDatabaseHas('tasks', ['task_name' => "xcdsd"]);
        Queue::assertPushed(TaskNotificationJob::class);

    }
}
