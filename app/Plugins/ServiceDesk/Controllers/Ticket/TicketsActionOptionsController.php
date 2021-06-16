<?php

namespace App\Plugins\ServiceDesk\Controllers\Ticket;

use App\Plugins\ServiceDesk\Model\Common\CommonTicketRelation;
use App\Plugins\ServiceDesk\Policies\AgentPermissionPolicy;
use App\Plugins\ServiceDesk\Model\Common\Ticket;

class TicketsActionOptionsController
{
  protected $agentPermission;

    /**
     * Appends Service desk timeline options to $allowedActions, which can be used to hide or display
     * @param  array &$allowedActions
     * @param  int $ticketId
     * @return null
     */
    public function appendSdTicketActions(array &$allowedActions, $ticketId)
    {
      $agentPermission = new AgentPermissionPolicy();
      $isProblemAlreadyAttached = CommonTicketRelation::where('type','sd_problem')
            ->where('ticket_id', $ticketId)->count();

      $allowedActions['show_detach_problem'] = (bool)$isProblemAlreadyAttached && $agentPermission->problemDetach();
      
      $allowedActions['show_problem'] = !(bool)$isProblemAlreadyAttached;

      $allowedActions['show_attach_new_problem'] = !(bool)$isProblemAlreadyAttached && $agentPermission->problemCreate();
      $allowedActions['show_attach_existing_problem'] = !(bool)$isProblemAlreadyAttached && $agentPermission->problemAttach();

      $allowedActions['show_attach_assets'] = $agentPermission->assetAttach();
      $allowedActions['show_detach_asset'] = $agentPermission->assetDetach();

      $this->addChangeActions($allowedActions, $agentPermission, $ticketId);
    }

    /**
     * Appends attach change and detach change to Service desk timeline options
     * @param  array &$allowedActions
     * @param  AgentPermissionPolicy $agentPermission
     * @param int $ticketId
     * @return null
     */
    private function addChangeActions(array &$allowedActions, AgentPermissionPolicy $agentPermission, $ticketId)
    {
      $ticket = Ticket::find($ticketId);
      if ($ticket) {
        $attachChangeInitiatedByThisTicket =$ticket->changes()->wherePivot('type', 'initiated')->count();
        $attachChangeInitiatingThisTicket = $ticket->changes()->wherePivot('type', 'initiating')->count();
        $allowedActions['attach_change_initiated'] = $agentPermission->changeAttach() && !$attachChangeInitiatedByThisTicket;
        $allowedActions['attach_change_initiating'] = $agentPermission->changeAttach() && !$attachChangeInitiatingThisTicket;
        $allowedActions['detach_change'] = $agentPermission->changeDetach();
      }
    }
}
