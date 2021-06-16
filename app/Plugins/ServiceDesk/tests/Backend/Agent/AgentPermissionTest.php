<?php

  namespace App\Plugins\ServiceDesk\tests\Backend\Agent;

  use Tests\DBTestCase;
  use Tests\AddOnTestCase;
  use Illuminate\Foundation\Testing\RefreshDatabase;
  use Illuminate\Foundation\Testing\DatabaseTransactions;
  use App\User;
  use Illuminate\Http\UploadedFile;
  use Auth;
  use App\Model\helpdesk\Settings\Plugin;
  use App\Plugins\ServiceDesk\Model\Permission\AgentPermission;

/**
 * Tests AgentControllerTest
 * 
 * @author Danis John <danis.john@ladybirdweb.com>
*/

  class AgentPermissionTest extends AddOnTestCase{

    
    //It returns all helpdesk agent permissions
    public function getHelpdeskAgentPermissions(){

            $hdPermissions = [ 'create_ticket','edit_ticket','close_ticket','transfer_ticket',
                               'delete_ticket','assign_ticket','view_unapproved_tickets',
                               'apply_approval_workflow','access_kb','report','ban_email',
                               'organisation_document_upload','account_activate','agent_account_activate',
                               'change_duedate','re_assigning_tickets','global_access' ];

                               return $hdPermissions;
    }

    //It returns all servicedesk agent permissions
    public function getServicedeskAgentPermissions(){

            $sdPermissions = [ 'create_problem','edit_problem','view_problems',
                               'create_change','edit_change','view_changes',
                               'create_release','edit_release','view_releases',
                               'create_asset','edit_asset','view_assets',
                               'create_contract','edit_contract','view_contracts' ];

                              return $sdPermissions;
    }

    //Create agent with servicedesk & helpdesk permission
    private function createUpdateAgent()
    {
        $this->getLoggedInUserForWeb('admin');
        $hdPermissions = $this->getHelpdeskAgentPermissions();
        $sdPermissions = $this->getServicedeskAgentPermissions();
        $response = $this->call("POST", url("api/admin/agent"),
                             [ 'first_name' => 'Danis', 'last_name' => 'John',
                               'user_name' => 'danis.john', 'email' => 'danisjohn99@gmail.com',
                               'location_id' => 'bangalore','agent_tzone_id' => 1,'team_ids' => [1,2],
                               'type_ids' => [1], 'role'=> 'agent','department_ids'=> [1,2],
                               'permission_ids'=> $hdPermissions,'sd_permission_ids'=> $sdPermissions,
                               'agent_sign' => 'Hello mr hot']);
    }

    //Create agent with Helpdesk Agent permission & Servicedesk Agent permissions
    /** @group createUpdateAgent */
    public function test_createUpdateAgent_with_BothPermissions()
    {   
        $this->getLoggedInUserForWeb('admin');
        $hdPermissions = $this->getHelpdeskAgentPermissions();
        $sdPermissions = $this->getServicedeskAgentPermissions();
        $response      = $this->call("POST", url('api/admin/agent'), 
                            [
                              'first_name' => 'Danis', 'last_name' => 'John',
                              'user_name' => 'danis.john', 'email' => 'danisjohn99@gmail.com',
                              'location_id' => 'bangalore','agent_tzone_id' => 1,'team_ids' => [1,2],
                              'type_ids' => [1], 'role'=> 'admin','department_ids'=> [1,2],
                              'permission_ids'=> $hdPermissions,'sd_permission_ids'=> $sdPermissions,
                              'agent_sign' => 'Hello mr hot' ]);
        $response->assertStatus(200);
        $userId = User::where('role','admin')->orderBy('id', 'desc')->first()->id;
        $this->assertDatabaseHas('users', ['id' => $userId, 'first_name' => 'Danis','last_name' =>'John', 'role'=>'admin', 
                                            'email'=>'danisjohn99@gmail.com']);

        $this->assertDatabaseHas('permision', ['user_id' => $userId, 'permision'=> '{"create_ticket":"1","edit_ticket":"1","close_ticket":"1","transfer_ticket":"1","delete_ticket":"1","assign_ticket":"1","view_unapproved_tickets":"1","apply_approval_workflow":"1","access_kb":"1","report":"1","ban_email":"1","organisation_document_upload":"1","account_activate":"1","agent_account_activate":"1","change_duedate":"1","re_assigning_tickets":"1","global_access":"1"}' ]);

        $this->assertDatabaseHas('sd_agent_permission', ['user_id' => $userId, 'permission'=> '{"create_problem":"1","edit_problem":"1","view_problems":"1","create_change":"1","edit_change":"1","view_changes":"1","create_release":"1","edit_release":"1","view_releases":"1","create_asset":"1","edit_asset":"1","view_assets":"1","create_contract":"1","edit_contract":"1","view_contracts":"1"}' ]);

    }

    //Create agent with helpdesk agent permission only
    /** @group createUpdateAgent */
    public function test_createUpdateAgent_with_HelpdeskPermissionOnly()
    {   
        $this->getLoggedInUserForWeb('admin');
        $hdPermissions = $this->getHelpdeskAgentPermissions();
        $response      = $this->call("POST", url('api/admin/agent'), 
                            [
                              'first_name' => 'Danis', 'last_name' => 'John',
                              'user_name' => 'danis.john', 'email' => 'danisjohn99@gmail.com',
                              'location_id' => 'bangalore','agent_tzone_id' => 1,'team_ids' => [1,2],
                              'type_ids' => [1], 'role'=> 'admin','department_ids'=> [1,2],
                              'permission_ids'=> $hdPermissions,'agent_sign' => 'Hello mr hot' ]);
        $response->assertStatus(200);

        $userId = User::where('role','admin')->orderBy('id', 'desc')->first()->id;
        $this->assertDatabaseHas('users', ['id' => $userId, 'first_name' => 'Danis','last_name' =>'John', 'role'=>'admin', 
                                            'email'=>'danisjohn99@gmail.com']);

        $this->assertDatabaseHas('permision', ['user_id' => $userId, 'permision'=> '{"create_ticket":"1","edit_ticket":"1","close_ticket":"1","transfer_ticket":"1","delete_ticket":"1","assign_ticket":"1","view_unapproved_tickets":"1","apply_approval_workflow":"1","access_kb":"1","report":"1","ban_email":"1","organisation_document_upload":"1","account_activate":"1","agent_account_activate":"1","change_duedate":"1","re_assigning_tickets":"1","global_access":"1"}' ]);
 
    }

    //Create agent with servicedesk agent permission only
    /** @group createUpdateAgent */
    public function test_createUpdateAgent_with_ServicedeskPermissionOnly()
    {   
        $this->getLoggedInUserForWeb('admin');
        $sdPermissions = $this->getServicedeskAgentPermissions();
        $response      = $this->call("POST", url('api/admin/agent'), 
                            [
                              'first_name' => 'Danis', 'last_name' => 'John',
                              'user_name' => 'danis.john', 'email' => 'danisjohn99@gmail.com',
                              'location_id' => 'bangalore','agent_tzone_id' => 1,'team_ids' => [1,2],
                              'type_ids' => [1], 'role'=> 'admin','department_ids'=> [1,2],
                              'sd_permission_ids'=> $sdPermissions,'agent_sign' => 'Hello mr hot' ]);
        $response->assertStatus(200);
        $userId = User::where('role','admin')->orderBy('id', 'desc')->first()->id;
        $this->assertDatabaseHas('users', ['id' => $userId, 'first_name' => 'Danis','last_name' =>'John', 'role'=>'admin', 
                                            'email'=>'danisjohn99@gmail.com']);

        $this->assertDatabaseHas('sd_agent_permission', ['user_id' => $userId, 'permission'=> '{"create_problem":"1","edit_problem":"1","view_problems":"1","create_change":"1","edit_change":"1","view_changes":"1","create_release":"1","edit_release":"1","view_releases":"1","create_asset":"1","edit_asset":"1","view_assets":"1","create_contract":"1","edit_contract":"1","view_contracts":"1"}' ]);

    }

    //API test for get all servicedesk permissions
    /** @group permissions */
    public function test_GetServicedeskPermissionList(){

        $this->getLoggedInUserForWeb('admin');
        $response = $this->call("GET", url("service-desk/get/permissions"));
        $response->assertStatus(200);
    }

    //To check fetch agent details with servicedesk permissions
    /** @group editAgent */
    public function test_editAgent_ToCheckServicedeskPermission(){

        $this->getLoggedInUserForWeb('admin');
        $this->createUpdateAgent();
        $userId = User::where([ ['role' , 'agent'], ['first_name', 'Danis'], ['last_name', 'John'], 
                                ['email','danisjohn99@gmail.com'] ])->first()->id;
        $response = $this->call("GET", url("api/admin/agent/".$userId));
        $response->assertStatus(200);
        $agent = json_decode($response->content())->data->agent;
        $sdPermissions = $agent->sd_permissions;
        $this->assertCount(15, $sdPermissions);    
    }

  }