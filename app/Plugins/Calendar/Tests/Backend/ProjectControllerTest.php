<?php

namespace App\Plugins\Calendar\Tests\Backend;

use Tests\AddOnTestCase;
use App\Plugins\Calendar\Model\Task;
use App\Plugins\Calendar\Model\Project;
use App\Plugins\Calendar\Model\TaskList;

class ProjectControllerTest extends AddOnTestCase {

    public function test_StoreMethod_ShouldCreateProject_ReturnSuccessResponse()
    {
        $this->getLoggedInUserForWeb('agent');
        $data = [
            'name' => 'Project 1'
        ];
        $response = $this->call('POST','tasks/project/create',$data);
        $response->assertOk()
                 ->assertExactJson(['success' => true,'message' => 'Project Created Successfully.']);
        $this->assertDatabaseHas('projects',$data);
    }

    public function test_IndexMethod_ReturnsAllProjects()
    {
        $this->getLoggedInUserForWeb('admin');
        $actual = factory(Project::class,1)->create();
        $response = $this->call('GET','/tasks/project/view');
        $response->assertOk()
                 ->assertJsonFragment([
                     'name' => $actual->first()->name
                 ]);
    }

    public function test_EditMethod_UpdatesTheProject_AndReturnsSuccessResponse()
    {
        $this->getLoggedInUserForWeb('admin');
        $id = factory(Project::class,1)->create()->first();
        $updatePayload = [
            'name' => 'New Project 2'
        ];
        $response = $this->call('PUT','/tasks/project/edit/'.$id->id,$updatePayload);
        $response->assertOk()
                 ->assertExactJson(['success' => true,'message' => 'Project updated successfully']);;
        $this->assertDatabaseMissing('projects',[
            'id' => $id->name
        ]);
        $this->assertDatabaseHas('projects',$updatePayload);
    }

    public function test_DestroyMethod_DeletesTheProjectWithAssociatedTaskLists_AndReturnsSuccessResponse()
    {
        $this->withoutExceptionHandling();
        $this->getLoggedInUserForWeb('admin');
        $project = factory(Project::class,1)->create()->first();
        $taskList = factory(TaskList::class,1)->create()->first();
        $taskList->project_id = $project->id;
        $taskList->save();
        $response = $this->call('DELETE','/tasks/project/delete/'.$project->id);
        $response->assertOk();
        $this->assertDatabaseMissing('projects',[
            'id' => $project->id
        ]);
        $this->assertDatabaseMissing('task_lists',[
            'name' => $taskList->name
        ]);
    }

    public function test_IndexWithTasksMethod_ReturnsAllProjectsWithRelatedTasksListsAndTasks()
    {
        $this->getLoggedInUserForWeb('admin');
        $project = factory(Project::class,1)->create()->first();
        $taskList = factory(TaskList::class,1)->create()->first();
        $taskList->project_id = $project->id;
        $taskList->save();
        $tasks = factory(Task::class,1)->create()->first();
        $tasks->update(['task_list_id'=>$taskList->id]);
        $response = $this->call('GET','/tasks/project/view-with-tasks');
        $response->assertOk()->assertJsonFragment(['name' => $project->name]);
        $this->assertGreaterThanOrEqual(count(json_decode($response->getContent(),true)[0]['tasks']),1);  
        $this->assertGreaterThanOrEqual(count(json_decode($response->getContent(),true)[0]['task_lists']),1);      
    }
    

}