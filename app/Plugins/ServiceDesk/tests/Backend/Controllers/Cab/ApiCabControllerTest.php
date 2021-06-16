<?php

namespace App\Plugins\ServiceDesk\tests\Backend\Controllers\Cab;

use App\Model\helpdesk\Manage\UserType;
use App\Model\helpdesk\Workflow\ApprovalWorkflow;
use App\User;
use Tests\AddOnTestCase;

/**
 * ApiCabController is mainly dependent on ApiApprovalWorkflowController only type changes to cab
 * associated testcased written
 *
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class ApiCabControllerTest extends AddOnTestCase
{
  // created approval workflow data is stored
  private $approvalWorkflow;

  // created approval workflow level is stored
  private $approvalLevel;


  /** @group createUpdateApprovalWorkflow */
  public function test_createUpdateApprovalWorkflow_withoutNameAndLevelForCab_returnsNameAndLevelsFieldRequired()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('POST', url('service-desk/api/cab'));
    $response->assertStatus(412);
  }

  /** @group createUpdateApprovalWorkflow */
  public function test_createUpdateApprovalWorkflow_WithApprovalWorkflowAndTwoLevelsForCab_returnsSavedSuccessfullyMessage()
  {
    $this->getLoggedInUserForWeb('admin');
    $users = factory(User::class, 3)->create(['role' => 'admin']);
    $userTypes = factory(UserType::class, 2)->create();
    $approvalWorkflow = $this->getApprovalWorkflowArray($users, $userTypes);
    $response = $this->call('POST', url('service-desk/api/cab'), $approvalWorkflow);
    $response->assertStatus(200);
    $this->assertDatabaseHas('approval_workflows', ['name' => 'My approval workflow', 'user_id' => $this->user->id, 'type' => 'cab']);
    $this->assertDatabaseHas('approval_levels', ['name'  => 'Level 1', 'match' => 'all', 'order' => 1]);
    $this->assertDatabaseHas('approval_levels', ['name'  => 'Level 2', 'match' => 'any', 'order' => 2]);
    foreach ($users as $user) {
      $this->assertDatabaseHas('approval_level_approvers', [
        'approval_level_approver_id'   => $user->id,
        'approval_level_approver_type' => User::class
      ]);
    }
    foreach ($userTypes as $userType) {
      $this->assertDatabaseHas('approval_level_approvers', [
        'approval_level_approver_id'   => $userType->id,
        'approval_level_approver_type' => UserType::class
      ]);
    }
  }

  /** @group createUpdateApprovalWorkflow */
  public function test_createUpdateApprovalWorkflow_withExistingApprovalWorkflowNameForCab_returnsNameAlreadyTaken()
  {
    $this->getLoggedInUserForWeb('admin');
    $this->createApprovalWorkflow($this->user);
    $users = factory(User::class, 3)->create(['role' => 'admin']);
    $userIds = $this->getModelIds($users);
    $approvalWorkflow = [
      'id'     => 0,
      'name'   => $this->approvalWorkflow->name,
      'levels' => [
        [
          'id'        => 0,
          'name'      => 'Level 1',
          'match'     => 'all',
          'order'     => 1,
          'approvers' => [
            'users'      => $userIds,
            'user_types' => []
          ]
        ]
      ]
    ];
    $response = $this->call('POST', url('service-desk/api/cab'), $approvalWorkflow);
    $response->assertStatus(412);
    $this->assertDatabaseHas('approval_workflows', ['name' => $this->approvalWorkflow->name, 'type' => 'cab']);
  }

  /** @group getApprovalWorkflow */
  public function test_getApprovalWorkflow_WithWrongIdForCab_returnsApprovalWorkflowNotFound()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url("service-desk/api/cab/wrong-id"));
    $response->assertStatus(400);
  }

    /** @group getApprovalWorkflow */
    public function test_getApprovalWorkflow_withApprovalWorkflowIdForCab_returnsApprovalWorkflowData()
    {
      $this->getLoggedInUserForWeb('admin');
      $user = factory(User::class)->create(['role' => 'admin']);
      $this->createApprovalWorkflow($user);
      $approvalWorkflowId = $this->approvalWorkflow->id;
      $response = $this->call('GET', url("service-desk/api/cab/$approvalWorkflowId"));
      $response->assertStatus(200);
      $approvalWorkflow = json_decode($response->content())->data;
      $this->assertDatabaseHas('approval_workflows', ['id' => $approvalWorkflowId, 'name' => $approvalWorkflow->name, 'user_id' => $approvalWorkflow->user_id, 'type' => 'cab']);
      foreach ($approvalWorkflow->levels as $level) {
        $this->assertDatabaseHas('approval_levels', ['name'  => $level->name, 'match' => $level->match, 'order' => $level->order, 'approval_workflow_id' => $approvalWorkflowId]);
        foreach ($level->approvers->users as $user) {
          $this->assertDatabaseHas('approval_level_approvers', [
            'approval_level_id' => $level->id,
            'approval_level_approver_id'   => $user->id,
            'approval_level_approver_type' => User::class
          ]);
        }
        $this->assertEmpty($level->approvers->user_types);
      }
    }

    /** @group getApprovalWorkflow */
    public function test_getApprovalWorkflow_withApprovalWorkflowIdForCab_returnApprovalWorkflowDataWithApproverUsers()
    {
      $this->getLoggedInUserForWeb('admin');
      $user = factory(User::class)->create(['role' => 'admin']);
      $this->createApprovalWorkflow($user);
      $approvalWorkflowId = $this->approvalWorkflow->id;
      $response = $this->call('GET', url("service-desk/api/cab/$approvalWorkflowId"));
      $approvalWorkflow = json_decode($response->content())->data;
      $this->assertDatabaseHas('approval_workflows', ['id' => $approvalWorkflowId, 'name' => $approvalWorkflow->name, 'user_id' => $approvalWorkflow->user_id, 'type' => 'cab']);
      foreach ($approvalWorkflow->levels as $level) {
        $this->assertDatabaseHas('approval_levels', ['name'  => $level->name, 'match' => $level->match, 'order' => $level->order, 'approval_workflow_id' => $approvalWorkflowId]);
        foreach ($level->approvers->users as $user) {
          $this->assertDatabaseHas('approval_level_approvers', [
            'approval_level_id' => $level->id,
            'approval_level_approver_id'   => $user->id,
            'approval_level_approver_type' => User::class
          ]);
        }
        $this->assertEmpty($level->approvers->user_types);
      }
    }

    /** @group getApprovalWorkflow */
    public function test_getApprovalWorkflow_withApprovalWorkflowIdForCab_returnApprovalWorkflowDataWithApproverUserTypes()
    {
      $this->getLoggedInUserForWeb('admin');
      $userType = factory(UserType::class)->create();
      $this->createApprovalWorkflow($userType);
      $approvalWorkflowId = $this->approvalWorkflow->id;
      $response = $this->call('GET', url("service-desk/api/cab/$approvalWorkflowId"));
      $approvalWorkflow = json_decode($response->content())->data;
      $this->assertDatabaseHas('approval_workflows', ['id' => $approvalWorkflowId, 'name' => $approvalWorkflow->name, 'user_id' => $approvalWorkflow->user_id, 'type' => 'cab']);
      foreach ($approvalWorkflow->levels as $level) {
        $this->assertDatabaseHas('approval_levels', ['name'  => $level->name, 'match' => $level->match, 'order' => $level->order, 'approval_workflow_id' => $approvalWorkflowId]);
        foreach ($level->approvers->user_types as $userType) {
          $this->assertDatabaseHas('approval_level_approvers', [
            'approval_level_id' => $level->id,
            'approval_level_approver_id'   => $userType->id,
            'approval_level_approver_type' => UserType::class
          ]);
        }
        $this->assertEmpty($level->approvers->users);
      }
    }


    /** @group getApprovalWorkflowList */
    public function test_getApprovalWorkflowList_withInvalidSortByForCab_returnsSortByIsInvalid()
    {
      $this->getLoggedInUserForWeb('admin');
      $this->createApprovalWorkflow($this->user);
      $response = $this->call('GET', url('service-desk/api/cab'), ['sort_by' => 'sort']);
      $response->assertStatus(412);
    }

    /** @group getApprovalWorkflowList */
    public function test_getApprovalWorkflowList_withInvalidOrderForCab_returnOrderIsInvalid()
    {
      $this->getLoggedInUserForWeb('admin');
      $this->createApprovalWorkflow($this->user);
      $response = $this->call('GET', url('service-desk/api/cab'), ['sort_by' => 'name', 'order' => 'order']);
      $response->assertStatus(412);
    }

     /** @group getApprovalWorkflowList */
    public function test_getApprovalWorkflowList_withNoParameterWorkflowEmptyForCab_returnsEmptyWorkflowList()
    {
      $this->getLoggedInUserForWeb('admin');
      $response = $this->call('GET', url('service-desk/api/cab'));
      $response->assertStatus(200);
      $data = json_decode($response->content())->data->data;
      $this->assertEmpty($data);
    }

    /** @group getApprovalWorkflowList */
    public function test_getApprovalWorkflowList_withNoParameterWorkflowExistForCab_returnsApprovalWorkflowList()
    {
      $this->getLoggedInUserForWeb('admin');
      $approvalWorkflowsInDb = factory(ApprovalWorkflow::class,2)->create(['user_id' => $this->user->id, 'type' => 'cab']);
      $approvalWorkflowsInDb = ApprovalWorkflow::orderBy('name', 'asc')->get();
      $response = $this->call('GET', url('service-desk/api/cab'));
      $response->assertStatus(200);
      $approvalWorkflows = json_decode($response->content())->data->data;
      for ($workflowIndex=0; $workflowIndex < count($approvalWorkflowsInDb); $workflowIndex++) { 
        $this->assertDatabaseHas('approval_workflows', ['id' => $approvalWorkflowsInDb[$workflowIndex]->id, 'name' => $approvalWorkflows[$workflowIndex]->name, 'user_id' => $approvalWorkflows[$workflowIndex]->user_id, 'type' => 'cab']);
      }
    }

    /** @group getApprovalWorkflowList */
    public function test_getApprovalWorkflowList_WithSortAndOrderForCab_returnsApprovalWorkflowDataInAscendingOrderBasedOnName()
    {
      $this->getLoggedInUserForWeb('admin');
      $approvalWorkflowsInDb = factory(ApprovalWorkflow::class,3)->create(['user_id' => $this->user->id, 'type' => 'cab']);
      $approvalWorkflowsInDb = ApprovalWorkflow::orderBy('name', 'desc')->get();
      $response = $this->call('GET', url('service-desk/api/cab'), ['sort_by' => 'name', 'order' => 'desc']);
      $response->assertStatus(200);
      $approvalWorkflows = json_decode($response->content())->data->data;
      for ($workflowIndex=0; $workflowIndex < count($approvalWorkflowsInDb); $workflowIndex++) {
        $this->assertDatabaseHas('approval_workflows', ['id' => $approvalWorkflowsInDb[$workflowIndex]->id, 'name' => $approvalWorkflows[$workflowIndex]->name, 'user_id' => $this->user->id]);
      } 
    }

    /** @group getApprovalWorkflowList */
    public function test_getApprovalWorkflowList_withWrongSearchForCab_returnsEmptyApprovalWorkflowData()
    {
      $this->getLoggedInUserForWeb('admin');
      factory(ApprovalWorkflow::class)->create(['user_id' => $this->user->id, 'type' => 'cab']);
      $response = $this->call('GET', url('service-desk/api/cab'), ['search' => 'wrong-approval']);
      $response->assertStatus(200);
      $approvalWorkflows = json_decode($response->content())->data->data;
      $this->assertEmpty($approvalWorkflows);
    }

    /** @group getApprovalWorkflowList */
    public function test_getApprovalWorkflowList_withSearchForCab_returnsApprovalWorkflowBasedOnSearchName()
    {
      $this->getLoggedInUserForWeb('admin');
      $approvalWorkflowsInDb = factory(ApprovalWorkflow::class)->create(['user_id' => $this->user->id, 'type' => 'cab']);
      $response = $this->call('GET', url('service-desk/api/cab'), ['search' => $approvalWorkflowsInDb->name]);
      $response->assertStatus(200);
      $approvalWorkflows = json_decode($response->content())->data->data;
      for ($workflowIndex=0; $workflowIndex < count($approvalWorkflows); $workflowIndex++) {
        $this->assertDatabaseHas('approval_workflows', ['id' => $approvalWorkflowsInDb->id, 'name' => $approvalWorkflows[$workflowIndex]->name, 'user_id' => $this->user->id, 'type' => 'cab']);
      } 
    }

     /** @group getApprovalWorkflowList */
    public function test_getApprovalWorkflowList_WithEmptySearchForCab_returnsAllExistingApprovalWorkflows()
    {
      $this->getLoggedInUserForWeb('admin');
      $approvalWorkflowInDb = factory(ApprovalWorkflow::class)->create(['user_id' => $this->user->id, 'type' => 'cab']);
      $response = $this->call('GET', url('service-desk/api/cab'), ['search' => '']);
      $response->assertStatus(200);
      $approvalWorkflows = json_decode($response->content())->data->data;
      for ($workflowIndex=0; $workflowIndex < count($approvalWorkflows); $workflowIndex++) {
        $this->assertDatabaseHas('approval_workflows', ['id' => $approvalWorkflowInDb->id, 'name' => $approvalWorkflows[$workflowIndex]->name, 'user_id' => $this->user->id, 'type' => 'cab']);
      } 
    }

    /** @group getApprovalWorkflowList */
    public function test_getApprovalWorkflowList_withLimitForCab_returnsLimitedApprovalWorkflowsBasedOnLimit()
    {
      $this->getLoggedInUserForWeb('admin');
      $approvalWorkflowsInDb = factory(ApprovalWorkflow::class,6)->create(['user_id' => $this->user->id, 'type' => 'cab']);
      $approvalWorkflowsInDb = ApprovalWorkflow::orderBy('name', 'asc')->take(5)->get();
      $limit = 5;
      $response = $this->call('GET', url('service-desk/api/cab'), ['limit' => $limit]);
      $response->assertStatus(200);
      $approvalWorkflows = json_decode($response->content())->data->data;
      $this->assertCount($limit, $approvalWorkflows);
      for ($workflowIndex=0; $workflowIndex < count($approvalWorkflowsInDb); $workflowIndex++) { 
        $this->assertDatabaseHas('approval_workflows', ['id' => $approvalWorkflowsInDb[$workflowIndex]->id, 'name' => $approvalWorkflows[$workflowIndex]->name, 'user_id' => $approvalWorkflows[$workflowIndex]->user_id, 'type' => 'cab']);
      }
    }


  /**
   * Function to make approval workflow array
   * @param User $users
   * @param UserType $userTypes
   * @return array $approvalWorkflow
   */
  private function getApprovalWorkflowArray($users, $userTypes)
  {
    $userIds     = $this->getModelIds($users);
    $userTypeIds = $this->getModelIds($userTypes);
    $approvalWorkflow = [
      'id'     => 0,
      'name'   => 'My approval workflow',
      'levels' => [
        [
          'id'        => 0,
          'name'      => 'Level 1',
          'match'     => 'all',
          'order'     => 1,
          'approvers' => [
            'users'      => $userIds,
            'user_types' => []
          ]
        ],
        [
          'id'        => 0,
          'name'      => 'Level 2',
          'match'     => 'any',
          'order'     => 2,
          'approvers' => [
            'users'      => [],
            'user_types' => $userTypeIds
          ]
        ]
      ]
    ];

    return $approvalWorkflow;
  }

    /** @group deleteApprovalWorkflow */
    public function test_deleteApprovalWorkflow_withWrongTypeAndWrongIdForCab_returnsInvalidWorkflowType()
    {
      $this->getLoggedInUserForWeb('admin');
      $response = $this->call('DELETE', url("service-desk/api/cab/wrong-id/wrong-type"));
      $response->assertStatus(400);
    }

    /** @group deleteApprovalWorkflow */
    public function test_deleteApprovalWorkflow_withTypeWorkflowAndWrongWorkflowIdForCab_returnsApprovalWorkflowNotFound()
    {
      $this->getLoggedInUserForWeb('admin');
      $response = $this->call('DELETE', url("service-desk/api/cab/11/workflow"));
      $response->assertStatus(400);
    }

    /** @group deleteApprovalWorkflow */
    public function test_deleteApprovalWorkflow_withTypeLevelAndWrongWorkflowLevelIdForCab_returnsWorkflowLevelNotFound()
    {
      $this->getLoggedInUserForWeb('admin');
      $response = $this->call('DELETE', url("service-desk/api/cab/wrong-id/level"));
      $response->assertStatus(400);
    }

    /** @group deleteApprovalWorkflow */
    public function test_deleteApprovalWorkflow_withWorkflowIdAndTypeLevelForCab_returnsApprovalWorkflowLevelNotFound()
    {
      $this->getLoggedInUserForWeb('admin');
      $this->createApprovalWorkflow($this->user);
      $approvalWorkflowId = $this->approvalWorkflow->id;
      $response = $this->call('DELETE', url("service-desk/api/cab/$approvalWorkflowId/level"));
      $response->assertStatus(400);
    }

    /** @group deleteApprovalWorkflow */
    public function test_deleteApprovalWorkflow_withWorkflowIdForCab_returnsApprovalWorkflowRemoved()
    {
      $this->getLoggedInUserForWeb('admin');
      $this->createApprovalWorkflow($this->user);
      $approvalWorkflowId = $this->approvalWorkflow->id;
      $response = $this->call('DELETE', url("service-desk/api/cab/$approvalWorkflowId/workflow"));
      $response->assertStatus(200);
      $this->assertDatabaseMissing('approval_level_approvers', [
          'approval_level_id'            => $this->approvalLevel->id,
          'approval_level_approver_id'   => $this->user->id,
          'approval_level_approver_type' => User::class,
      ]);
      $this->assertDatabaseMissing('approval_levels', [
          'approval_workflow_id' => $this->approvalWorkflow->id,
          'name'                 => $this->approvalLevel->name,
          'match'                => $this->approvalLevel->match,
          'order'                => $this->approvalLevel->order,
      ]);
      $this->assertDatabaseMissing('approval_workflows', [
          'name'    => $this->approvalWorkflow->name,
          'user_id' => $this->user->id,
          'type' => 'cab'
      ]);
    }

    /** @group deleteApprovalWorkflow */
    public function test_deleteApprovalWorkflow_withWorkflowLevelIdAndTypeWorkflowForCab_returnsApprovalWorkflowNotFound()
    {
      $this->getLoggedInUserForWeb('admin');
      $this->createApprovalWorkflow($this->user);
      $approvalLevelId = $this->approvalLevel->id;
      $response = $this->call('DELETE', url("service-desk/api/cab/$approvalLevelId/workflow"));
      $response->assertStatus(400);
      $this->assertDatabaseHas('approval_level_approvers', [
          'approval_level_id'            => $this->approvalLevel->id,
          'approval_level_approver_id'   => $this->user->id,
          'approval_level_approver_type' => User::class,
      ]);
      $this->assertDatabaseHas('approval_levels', [
          'approval_workflow_id' => $this->approvalWorkflow->id,
          'name'                 => $this->approvalLevel->name,
          'match'                => $this->approvalLevel->match,
          'order'                => $this->approvalLevel->order,
      ]);
      $this->assertDatabaseHas('approval_workflows', [
          'name'    => $this->approvalWorkflow->name,
          'user_id' => $this->user->id,
          'type' => 'cab'
      ]);
    }

    /** @group deleteApprovalWorkflow */
    public function test_deleteApprovalWorkflow_withWorkflowLevelIdForCab_returnsApprovalWorkflowLevelRemoved()
    {
      $this->getLoggedInUserForWeb('admin');
      $this->createApprovalWorkflow($this->user);
      $approvalLevelId = $this->approvalLevel->id;
      $response = $this->call('DELETE', url("service-desk/api/cab/$approvalLevelId/level"));
      $response->assertStatus(200);
      $this->assertDatabaseMissing('approval_level_approvers', [
          'approval_level_id'            => $this->approvalLevel->id,
          'approval_level_approver_id'   => $this->user->id,
          'approval_level_approver_type' => User::class,
      ]);
      $this->assertDatabaseMissing('approval_levels', [
          'approval_workflow_id' => $this->approvalWorkflow->id,
          'name'                 => $this->approvalLevel->name,
          'match'                => $this->approvalLevel->match,
          'order'                => $this->approvalLevel->order,
      ]);
      $this->assertDatabaseHas('approval_workflows', [
          'name'    => $this->approvalWorkflow->name,
          'user_id' => $this->user->id,
          'type' => 'cab'
      ]);
    }

    /**
     * Function to return array of id's from the array of collections
     * @param array $models
     * @return array $ids
     */
    private function getModelIds($models)
    {
      $ids = [];
      foreach ($models as $model) {
          $ids[] = $model->id;
      }
      return $ids;
    }

    /**
     * Function to create approval workflow with level
     * @param User $approver
     * @return void
     */
    private function createApprovalWorkflow($approver)
    {
      $this->approvalWorkflow = factory(ApprovalWorkflow::class)->create(['user_id' => $this->user->id, 'type' => 'cab']);
      $this->assertDatabaseHas('approval_workflows', [
          'name'    => $this->approvalWorkflow->name,
          'user_id' => $this->user->id,
          'type' => 'cab'
      ]);
      $this->approvalLevel = $this->approvalWorkflow->approvalLevels()->create([
          'name'  => 'Level 1',
          'match' => 'any',
          'order' => 1,
      ]);
      $this->assertDatabaseHas('approval_levels', [
          'approval_workflow_id' => $this->approvalWorkflow->id,
          'name'                 => $this->approvalLevel->name,
          'match'                => $this->approvalLevel->match,
      ]);
      if ($approver instanceof User) {
          $this->approvalLevel->approveUsers()->sync($approver->id);

          $this->assertDatabaseHas('approval_level_approvers', [
              'approval_level_id'            => $this->approvalLevel->id,
              'approval_level_approver_id'   => $approver->id,
              'approval_level_approver_type' => User::class,
          ]);
      } else {
          $this->approvalLevel->approveUserTypes()->sync($approver->id);

          $this->assertDatabaseHas('approval_level_approvers', [
              'approval_level_id'            => $this->approvalLevel->id,
              'approval_level_approver_id'   => $approver->id,
              'approval_level_approver_type' => UserType::class,
          ]);
      }
    }
}

