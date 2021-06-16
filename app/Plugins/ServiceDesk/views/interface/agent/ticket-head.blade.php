<?php 
    $ticket = \App\Plugins\ServiceDesk\Controllers\Library\UtilityController::getTicketByThreadId($id->ticket_id);
    //$problem = $ticket->getTicketRelation('sd_problem');
    $change = $ticket->getTicketRelation('sd_change');
    $ticketid = $ticket->id;
    $asset  = \App\Plugins\ServiceDesk\Controllers\Library\UtilityController::getAssetsByTicketid($ticketid);
    $problem  = \App\Plugins\ServiceDesk\Controllers\Library\UtilityController::getProblemsByTicketid($ticketid);
    $sdPolicy = new App\Plugins\ServiceDesk\Policies\AgentPermissionPolicy();
?>
<tr>
    <td>@if($sdPolicy->assetAttach())
        @include("service::interface.agent.popup.add-asset-one")
        @endif
   </td>
        @if(!$problem)
            <td>
                @include("service::interface.agent.popup.add-problem")
            </td>
        @endif
       
        <?php 
            Event::listen('ticket.timeline.marble',function()use($problem ,$asset, $ticketid){
                $controller = new App\Plugins\ServiceDesk\Controllers\Library\UtilityController();
                $controller->timelineMarble($problem, $asset, $ticketid);
            }); 
        ?>
</tr>