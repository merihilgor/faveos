<?php

namespace App\Plugins\Calendar\Tests\Backend;

use App\Plugins\Calendar\Model\Task;
use App\Plugins\Calendar\Model\TaskList;
use Tests\AddOnTestCase;

class TaskListControllerTest extends AddOnTestCase
{
    public function test_Create_AddsTaskList_Returns_SuccessResponse()
    {
        $this->getLoggedInUserForWeb('agent');
        $createPayload = array(
            "name" => 'TaskList default',
            'project_id' => 1
        );
        $response = $this->call('POST','/tasks/tasklist/create',$createPayload);
        $response->assertOk();
        $this->assertDatabaseHas('task_lists',$createPayload);
    }


    public function test_EditMethod_UpdatesTheTaskList_AndReturnsSuccessResponse()
    {
        $this->getLoggedInUserForWeb('admin');
        $id = factory(TaskList::class,1)->create()->first();
        $updatePayload = [
            'name'       => 'New TaskList 2',
            'project_id' => $id->first()->project_id
        ];
        $response = $this->call('PUT','/tasks/tasklist/edit/'.$id->id,$updatePayload);
        $response->assertOk()
                 ->assertExactJson(['success' => true,'message' => 'Task list updated']);;
        $this->assertDatabaseMissing('projects',[
            'id' => $id->name
        ]);
        $this->assertDatabaseHas('task_lists',[
            'name' => 'New TaskList 2'
        ]);
    }

    public function test_IndexMethod_ReturnsAllTaskLists()
    {
        $this->getLoggedInUserForWeb('admin');
        $actual = factory(TaskList::class,1)->create();
        $response = $this->call('GET','/tasks/tasklist/view');
        $response->assertOk()
                 ->assertJsonFragment([
                     'name' => $actual->first()->name
                 ]);
    }

    public function test_DestroyMethod_DeletesTheTasklist_AndReturnsSuccessResponse()
    {

        $this->getLoggedInUserForWeb('admin');
        $taskLists = factory(TaskList::class,1)->create()->first();
        $tasks = factory(Task::class,1)->create()->first();
        $tasks->update([
            'task_list_id' => $taskLists->id
        ]);
        $response = $this->call('DELETE','/tasks/tasklist/delete/'.$taskLists->id);
        $response->assertOk();
        $this->assertDatabaseMissing('task_lists',[
            'id' => $taskLists->id
        ]);
        $this->assertDatabaseMissing('tasks',[
            'task_name' => $tasks->task_name
        ]);
    }
}