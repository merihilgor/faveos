<?php

namespace App\Plugins\ServiceDesk\tests\Backend\Controllers\Ticket;

use Tests\AddOnTestCase;
use App\Plugins\ServiceDesk\Model\Common\CommonTicketRelation;
use App\Plugins\ServiceDesk\Controllers\Ticket\TicketsActionOptionsController;
use App\Model\helpdesk\Ticket\Tickets;
use App\Plugins\ServiceDesk\Model\Permission\AgentPermission;
use App\Plugins\ServiceDesk\Policies\AgentPermissionPolicy;
use Auth;

class TicketsActionOptionsControllerTest extends AddOnTestCase
{
    /** @group appendSdTicketActions */
    public function test_appendSdTicketActions_whenAProblemIsAttached()
    {
      //create a ticket
      $this->getLoggedInUserForWeb('admin');
      $allowedActions = [];
      $ticketId = factory(Tickets::class)->create()->id;
      CommonTicketRelation::create(['ticket_id'=>$ticketId, 'type_id'=> 1, 'type'=>'sd_problem']);
      (new TicketsActionOptionsController)->appendSdTicketActions($allowedActions, $ticketId);
      $this->assertArrayHasKeys(['show_detach_problem','show_attach_new_problem','show_attach_existing_problem','show_attach_assets'], $allowedActions);
      $this->assertEquals($allowedActions['show_detach_problem'], true);
      $this->assertEquals($allowedActions['show_attach_new_problem'], false);
      $this->assertEquals($allowedActions['show_attach_existing_problem'], false);
      $this->assertEquals($allowedActions['show_attach_assets'], true);
    }

    /** @group appendSdTicketActions */
    public function test_appendSdTicketActions_whenNoProblemIsAttached()
    {

      //create a ticket
      $this->getLoggedInUserForWeb('admin');
      $allowedActions = [];
      (new TicketsActionOptionsController)->appendSdTicketActions($allowedActions, 'wrong_id');
      $this->assertEquals($allowedActions['show_detach_problem'], false);
      $this->assertEquals($allowedActions['show_attach_new_problem'], true);
      $this->assertEquals($allowedActions['show_attach_existing_problem'], true);
      $this->assertEquals($allowedActions['show_attach_assets'], true);
    }



    //It returns all servicedesk agent permissions
    private function getServicedeskAgentPermissions(){

      $sdPermissions =  ['create_problem'=>'1','attach_problem'=>'1','detach_problem'=>'1','attach_asset'=>'1','detach_asset'=>'1' ];
                        return $sdPermissions;
    }


    /** @group appendSdTicketActions */
    public function test_appendSdTicketActions_WithProblem(){
           
      $this->getLoggedInUserForWeb('agent');
      $agentPermission = new AgentPermissionPolicy();

      //created ticket
      $ticketId = factory(Tickets::class)->create()->id;

      //Added servicedesk permissions
      $sdPermissions = json_encode($this->getServicedeskAgentPermissions());
      $auth = Auth::user()->id;
      AgentPermission::create([ "user_id"=> $auth, "permission"=> $sdPermissions ]);

      //Attached Problem with ticket
      CommonTicketRelation::create(['ticket_id'=>$ticketId, 'type_id'=> 1, 'type'=>'sd_problem']);
      $isProblemAlreadyAttached = CommonTicketRelation::where("type", "sd_problem")->where("type_id", 1)->count();
    
      $allowedActions = ['show_detach_problem','show_attach_new_problem','show_attach_existing_problem','show_attach_assets','show_detach_asset']; 

      //Passed to the controller & checked the keys
      (new TicketsActionOptionsController)->appendSdTicketActions($allowedActions, $ticketId);
      $this->assertArrayHasKeys(['show_detach_problem','show_attach_new_problem','show_attach_existing_problem','show_attach_assets','show_detach_asset'], $allowedActions);

      //check assertions
      $this->assertEquals($allowedActions['show_attach_assets'], true);
      $this->assertEquals($allowedActions['show_detach_asset'], true);   

      $this->assertEquals($allowedActions['show_attach_new_problem'], false);
      $this->assertEquals($allowedActions['show_attach_existing_problem'], false);   

    }

}
