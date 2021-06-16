<?php
namespace App\Plugins\ServiceDesk\Controllers\Ticket;

use App\Http\Controllers\Agent\helpdesk\TicketsView\TicketListController;
use Illuminate\Http\Request;
use App\Plugins\ServiceDesk\Model\Common\Ticket;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

/**
 * TicketsListController uses the all the parameters of TicketListController and TicketsCategoryController
 * Extra Parameters
 * asset-ids (array, optional)
 */
class TicketsListController extends TicketListController
{
    public function __construct()
    {
         $this->middleware(['auth']);
    }

    /**
     * method to update helpdesk ticket model baseQuery with servicedesk ticket model
     *
     * @param HelpdeskTicketModel $baseQuery
     * @return null
     */
    public function updateBaseQuery(&$baseQuery)
    {
        $baseQuery = Ticket::query();
    }

    /**
     * method to add extra parameters for ticket filter 
     * it could be used for filtering tickets based on asset, problem, change, etc
     * @param QueryBuilder $ticketsQuery
     * @param Request $request
     * @return null
     */
    public function addFilterTicketByServiceDeskRelationsQuery(QueryBuilder $ticketsQuery, Request $request)
    {
        $this->request = $request;

        // filter tickets based on asset ids
        $this->filterTicketByRelation($ticketsQuery, 'asset-ids', 'assets', 'type_id');
    }


    
    
}
