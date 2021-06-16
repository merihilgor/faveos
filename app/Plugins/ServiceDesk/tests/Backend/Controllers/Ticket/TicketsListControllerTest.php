<?php

namespace App\Plugins\ServiceDesk\tests\Backend\Controllers\Ticket;

use Tests\AddOnTestCase;
use App\Model\helpdesk\Ticket\Tickets;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\Plugins\ServiceDesk\Model\Common\Ticket as SdTicket;

/**
 * TicketsListControllerTest class is overriden
 * filter ticket by asset id parameter is added into it
 * Test TicketsListController
 *
 */
class TicketsListControllerTest extends AddOnTestCase
{
	/** @group getTicketsList */
	public function test_getTicketsList_withAssetId_returnsTicketsLinkedToAssetBasedOnAssetId()
	{
		// logging as admin, so that he can view all tickets, without giving any permission
		$this->getLoggedInUserForWeb('admin');
		$ticketId = factory(Tickets::class)->create()->id;
		factory(Tickets::class,5)->create();
		$assetId = factory(SdAssets::class)->create()->id;
		SdTicket::find($ticketId)->assets()->sync([$assetId => ['type' => 'sd_assets']]);
		$response = $this->call('GET', url('service-desk/api/ticket-list'), ['asset-ids' => [$assetId]]);
		$tickets = json_decode($response->content())->data->tickets;
		$response->assertStatus(200);
		$this->assertCount(1, $tickets);
		$ticket = reset($tickets);
		$this->assertDatabaseHas('tickets', ['id' => $ticketId, 'ticket_number' => $ticket->ticket_number]);
	}

	/** @group getTicketsList */
	public function test_getTicketsList_withWrongAssetId_returnsEmptyTicketsList()
	{
		$this->getLoggedInUserForWeb('admin');
		$ticketId = factory(Tickets::class)->create()->id;
		factory(Tickets::class,5)->create();
		$assetId = factory(SdAssets::class)->create()->id;
		SdTicket::find($ticketId)->assets()->sync([$assetId => ['type' => 'sd_assets']]);
		$response = $this->call('GET', url('service-desk/api/ticket-list'), ['asset-ids' => ['wrong-asset-id']]);
		$tickets = json_decode($response->content())->data->tickets;
		$response->assertStatus(200);
		$this->assertCount(0, $tickets);
	}
}
