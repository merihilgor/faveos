<?php

namespace App\Plugins\ServiceDesk\tests\Backend\Controllers\Contract;

use Tests\AddOnTestCase;
use App\Plugins\ServiceDesk\Model\Contract\SdContract;
use App\Plugins\ServiceDesk\Model\Contract\ContractUserOrganization;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\Plugins\ServiceDesk\Model\Vendor\SdVendors;
use App\Plugins\ServiceDesk\Model\Assets\CommonAssetRelation;
use App\Model\helpdesk\Ticket\Tickets;
use App\Plugins\ServiceDesk\Model\Common\CommonTicketRelation;
use App\Plugins\ServiceDesk\Model\Common\Email;
use App\Plugins\ServiceDesk\Model\Contract\SdContractSdUser;
use App\Plugins\ServiceDesk\Model\Contract\SdContractStatus;
use App\Plugins\ServiceDesk\Model\Common\SdUser;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use DB;
use Auth;
use App\Plugins\ServiceDesk\Model\Common\Attachments;
use App\Model\helpdesk\Agent_panel\Organization;
use App\Plugins\ServiceDesk\Controllers\Contract\ApiContractController;
use App\Plugins\ServiceDesk\Model\Contract\SdContractThread;



/**
 * Tests ApiContractController
 * 
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 *
 * @author Abhishek Kumar Shashi <abhishek.shashi@ladybirdweb.com>
*/
class ApiContractControllerTest extends AddOnTestCase
{   
    /** @group getContractsBasedOnAssetAttachedToTicket */
    public function test_getContractsBasedOnAssetAttachedToTicket_WithWrongTicketId_returnsEmptyContractData()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdContract::class)->create();

        $response = $this->call('GET', url('service-desk/api/contract/ticket/wrong-ticket-id'));
        $contractsData = json_decode($response->content())->data->data;
        $response->assertStatus(200);
        $this->assertEmpty($contractsData);
    }

    /** @group getContractsBasedOnAssetAttachedToTicket */
    public function test_getContractsBasedOnAssetAttachedToTicket_WithTicketIdAndAssetAttachedToContractAndTicket_returnsAssociatedContractData()
    {
        $this->getLoggedInUserForWeb('agent');
        $contractId = factory(SdContract::class)->create()->id;
        $assetId = factory(SdAssets::class)->create()->id;
        $ticketId = factory(Tickets::class)->create()->id;
        //same asset is attached to contract and ticket
        CommonAssetRelation::create(['asset_id' => $assetId, 'type_id' => $contractId, 'type' => 'sd_contracts']);
        CommonTicketRelation::create(['ticket_id' => $ticketId, 'type_id' =>  $assetId, 'type'=> 'sd_assets']);

        $response = $this->call('GET', url("service-desk/api/contract/ticket/$ticketId"));
        $contractsData = json_decode($response->content())->data->data;
        $contract = reset($contractsData);
        $response->assertStatus(200);
        $this->assertCount(1, $contractsData);
        $this->assertDatabaseHas('sd_contracts', ['id' => $contractId, 'name' => $contract->name, 'cost' => $contract->cost, 'contract_start_date' => $contract->contract_start_date, 'contract_end_date' => $contract->contract_end_date]);
    }

    /** @group getContractsBasedOnAssetAttachedToTicket */
    public function test_getContractsBasedOnAssetAttachedToTicket_WithTicketIdAndAssetAttachedToContractOnly_returnsEmptyContractData()
    { 
        $this->getLoggedInUserForWeb('agent');
        $contractId = factory(SdContract::class)->create()->id;
        $assetId = factory(SdAssets::class)->create()->id;
        $ticketId = factory(Tickets::class)->create()->id;
        //asset is attached to contract
        CommonAssetRelation::create(['asset_id' => $assetId, 'type_id' => $contractId, 'type' => 'sd_contracts']);

        $response = $this->call('GET', url("service-desk/api/contract/ticket/$ticketId"));
        $contractsData = json_decode($response->content())->data->data;
        $response->assertStatus(200);
        $this->assertEmpty($contractsData);
    }

    /** @group getContractsBasedOnAssetAttachedToTicket */
    public function test_getContractsBasedOnAssetAttachedToTicket_WithTicketIdAndAssetAttachedToTicketOnly_returnsEmptyContractData()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdContract::class)->create();
        $assetId = factory(SdAssets::class)->create()->id;
        $ticketId = factory(Tickets::class)->create()->id;
        //asset is attached to ticket
        CommonTicketRelation::create(['ticket_id' => $ticketId, 'type_id' =>  $assetId, 'type'=> 'sd_assets']);

        $response = $this->call('GET', url("service-desk/api/contract/ticket/$ticketId"));
        $contractsData = json_decode($response->content())->data->data;
        $response->assertStatus(200);
        $this->assertEmpty($contractsData);
    }

    /** @group getContractsBasedOnAssetAttachedToTicket */
    public function test_getContractsBasedOnAssetAttachedToTicket_WithTicketIdAndSearchQueryAsContractName_returnsAssociatedContractData()
    {
        $this->getLoggedInUserForWeb('agent');
        $contractName = "Laptop Contract";
        $contractId = factory(SdContract::class)->create(['name' => $contractName])->id;
        $assetId = factory(SdAssets::class)->create()->id;
        $ticketId = factory(Tickets::class)->create()->id;
        //same asset is attached to contract and ticket
        CommonAssetRelation::create(['asset_id' => $assetId, 'type_id' => $contractId, 'type' => 'sd_contracts']);
        CommonTicketRelation::create(['ticket_id' => $ticketId, 'type_id' =>  $assetId, 'type'=> 'sd_assets']);

        $response = $this->call('GET', url("service-desk/api/contract/ticket/$ticketId"), ['search-query' => $contractName]);
        $contractsData = json_decode($response->content())->data->data;
        $contract = reset($contractsData);
        $response->assertStatus(200);
        $this->assertCount(1, $contractsData);
        $this->assertDatabaseHas('sd_contracts', ['id' => $contractId, 'name' => $contractName, 'cost' => $contract->cost, 'contract_start_date' => $contract->contract_start_date, 'contract_end_date' => $contract->contract_end_date]);
    }

    /** @group getContractsBasedOnAssetAttachedToTicket */
    public function test_getContractsBasedOnAssetAttachedToTicket_WithTicketIdAndSearchQueryAsWrongContractName_returnsEmptyContractData()
    {
        $this->getLoggedInUserForWeb('agent');
        $wrongContractName = "Laptop Contract";
        $contractId = factory(SdContract::class)->create()->id;
        $assetId = factory(SdAssets::class)->create()->id;
        $ticketId = factory(Tickets::class)->create()->id;
        //same asset is attached to contract and ticket
        CommonAssetRelation::create(['asset_id' => $assetId, 'type_id' => $contractId, 'type' => 'sd_contracts']);
        CommonTicketRelation::create(['ticket_id' => $ticketId, 'type_id' =>  $assetId, 'type'=> 'sd_assets']);

        $response = $this->call('GET', url("service-desk/api/contract/ticket/$ticketId"), ['search-query' => $wrongContractName]);
        $contractsData = json_decode($response->content())->data->data;
        $response->assertStatus(200);
        $this->assertEmpty($contractsData);
    }

    /** @group getContractsBasedOnAssetAttachedToTicket */
    public function test_getContractsBasedOnAssetAttachedToTicket_WithTicketIdAndSearchQueryAsContractCost_returnsAssociatedContractData()
    {
        $this->getLoggedInUserForWeb('agent');
        $contractCost = 80000;
        $contractId = factory(SdContract::class)->create(['cost' => $contractCost])->id;
        $assetId = factory(SdAssets::class)->create()->id;
        $ticketId = factory(Tickets::class)->create()->id;
        //same asset is attached to contract and ticket
        CommonAssetRelation::create(['asset_id' => $assetId, 'type_id' => $contractId, 'type' => 'sd_contracts']);
        CommonTicketRelation::create(['ticket_id' => $ticketId, 'type_id' =>  $assetId, 'type'=> 'sd_assets']);

        $response = $this->call('GET', url("service-desk/api/contract/ticket/$ticketId"), ['search-query' => $contractCost]);
        $contractsData = json_decode($response->content())->data->data;
        $contract = reset($contractsData);
        $response->assertStatus(200);
        $this->assertCount(1, $contractsData);
        $this->assertDatabaseHas('sd_contracts', ['id' => $contractId, 'name' => $contract->name, 'cost' => $contractCost, 'contract_start_date' => $contract->contract_start_date, 'contract_end_date' => $contract->contract_end_date]);
    }

    /** @group getContractsBasedOnAssetAttachedToTicket */
    public function test_getContractsBasedOnAssetAttachedToTicket_WithTicketIdAndSearchQueryAsWrongContractCost_returnsEmptyContractData()
    {
        $this->getLoggedInUserForWeb('agent');
        $wrongContractCost = '1111';
        $contractId = factory(SdContract::class)->create()->id;
        $assetId = factory(SdAssets::class)->create()->id;
        $ticketId = factory(Tickets::class)->create()->id;
        //same asset is attached to contract and ticket
        CommonAssetRelation::create(['asset_id' => $assetId, 'type_id' => $contractId, 'type' => 'sd_contracts']);
        CommonTicketRelation::create(['ticket_id' => $ticketId, 'type_id' =>  $assetId, 'type'=> 'sd_assets']);

        $response = $this->call('GET', url("service-desk/api/contract/ticket/$ticketId"), ['search-query' => $wrongContractCost]);
        $contractsData = json_decode($response->content())->data->data;
        $response->assertStatus(200);
        $this->assertEmpty($contractsData);
    }

    /** @group getContractsBasedOnAssetAttachedToTicket */
    public function test_getContractsBasedOnAssetAttachedToTicket_WithTicketIdAndSearchQueryAsContractIdentifier_returnsAssociatedContractData()
    {
        $this->getLoggedInUserForWeb('agent');
        $contractIdentifier = '#CNTR-12';
        $contractId = factory(SdContract::class)->create(['identifier' => $contractIdentifier])->id;
        $assetId = factory(SdAssets::class)->create()->id;
        $ticketId = factory(Tickets::class)->create()->id;
        //same asset is attached to contract and ticket
        CommonAssetRelation::create(['asset_id' => $assetId, 'type_id' => $contractId, 'type' => 'sd_contracts']);
        CommonTicketRelation::create(['ticket_id' => $ticketId, 'type_id' =>  $assetId, 'type'=> 'sd_assets']);

        $response = $this->call('GET', url("service-desk/api/contract/ticket/$ticketId"), ['search-query' => $contractIdentifier]);
        $contractsData = json_decode($response->content())->data->data;
        $contract = reset($contractsData);
        $response->assertStatus(200);
        $this->assertCount(1, $contractsData);
        $this->assertDatabaseHas('sd_contracts', ['id' => $contractId, 'name' => $contract->name, 'cost' => $contract->cost, 'identifier' => $contractIdentifier,'contract_start_date' => $contract->contract_start_date, 'contract_end_date' => $contract->contract_end_date]);
    }

    /** @group getContractsBasedOnAssetAttachedToTicket */
    public function test_getContractsBasedOnAssetAttachedToTicket_WithTicketIdAndSearchQueryAsWrongContractIdentifier_returnsEmptyContractData()
    {
        $this->getLoggedInUserForWeb('agent');
        $wrongContractIdentifier = '#CNTR-14';
        $contractId = factory(SdContract::class)->create()->id;
        $assetId = factory(SdAssets::class)->create()->id;
        $ticketId = factory(Tickets::class)->create()->id;
        //same asset is attached to contract and ticket
        CommonAssetRelation::create(['asset_id' => $assetId, 'type_id' => $contractId, 'type' => 'sd_contracts']);
        CommonTicketRelation::create(['ticket_id' => $ticketId, 'type_id' =>  $assetId, 'type'=> 'sd_assets']);

        $response = $this->call('GET', url("service-desk/api/contract/ticket/$ticketId"), ['search-query' => $wrongContractIdentifier]);
        $contractsData = json_decode($response->content())->data->data;
        $response->assertStatus(200);
        $this->assertEmpty($contractsData);
    }

    /** @group getContractsBasedOnAssetAttachedToTicket */
    public function test_getContractsBasedOnAssetAttachedToTicket_WithTicketIdAndLimit_returnsAssociatedContractData()
    {
        $this->getLoggedInUserForWeb('agent');
        $limit = 1;
        $contractId1 = factory(SdContract::class)->create()->id;
        $assetId1 = factory(SdAssets::class)->create()->id;
        $contractId2 = factory(SdContract::class)->create()->id;
        $assetId2 = factory(SdAssets::class)->create()->id;
        $ticketId = factory(Tickets::class)->create()->id;
        //same asset is attached to contract and ticket
        CommonAssetRelation::create(['asset_id' => $assetId1, 'type_id' => $contractId1, 'type' => 'sd_contracts']);
        CommonTicketRelation::create(['ticket_id' => $ticketId, 'type_id' =>  $assetId1, 'type'=> 'sd_assets']);
        CommonAssetRelation::create(['asset_id' => $assetId2, 'type_id' => $contractId2, 'type' => 'sd_contracts']);
        CommonTicketRelation::create(['ticket_id' => $ticketId, 'type_id' =>  $assetId2, 'type'=> 'sd_assets']);

        $response = $this->call('GET', url("service-desk/api/contract/ticket/$ticketId"), ['limit' => $limit]);
        $contractsData = json_decode($response->content())->data->data;
        $contract = reset($contractsData);
        $response->assertStatus(200);
        $this->assertCount(1, $contractsData);
        $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => $contract->name, 'cost' => $contract->cost, 'contract_start_date' => $contract->contract_start_date, 'contract_end_date' => $contract->contract_end_date]);
    }

    /** @group getContractsBasedOnAssetAttachedToTicket */
    public function test_getContractsBasedOnAssetAttachedToTicket_WithTicketIdAndNoLimit_returnsAssociatedContractData()
    {
        $this->getLoggedInUserForWeb('agent');
        $contractId1 = factory(SdContract::class)->create()->id;
        $assetId1 = factory(SdAssets::class)->create()->id;
        $contractId2 = factory(SdContract::class)->create()->id;
        $assetId2 = factory(SdAssets::class)->create()->id;
        $ticketId = factory(Tickets::class)->create()->id;
        //same asset is attached to contract and ticket
        CommonAssetRelation::create(['asset_id' => $assetId1, 'type_id' => $contractId1, 'type' => 'sd_contracts']);
        CommonTicketRelation::create(['ticket_id' => $ticketId, 'type_id' =>  $assetId1, 'type'=> 'sd_assets']);
        CommonAssetRelation::create(['asset_id' => $assetId2, 'type_id' => $contractId2, 'type' => 'sd_contracts']);
        CommonTicketRelation::create(['ticket_id' => $ticketId, 'type_id' =>  $assetId2, 'type'=> 'sd_assets']);

        $response = $this->call('GET', url("service-desk/api/contract/ticket/$ticketId"));
        $contractsData = json_decode($response->content())->data->data;
        $response->assertStatus(200);
        $this->assertCount(2, $contractsData);
        foreach ($contractsData as $contract) {
          $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => $contract->name, 'cost' => $contract->cost, 'contract_start_date' => $contract->contract_start_date, 'contract_end_date' => $contract->contract_end_date]);
        }
    }

    /** @group getContractsBasedOnAssetAttachedToTicket */
    public function test_getContractsBasedOnAssetAttachedToTicket_WithTicketIdAndSort_returnsAssociatedContractData()
    {
        $this->getLoggedInUserForWeb('agent');
        $sortOrder = 'asc';
        $sortField = 'id';
        $contractId1 = factory(SdContract::class)->create()->id;
        $assetId1 = factory(SdAssets::class)->create()->id;
        $contractId2 = factory(SdContract::class)->create()->id;
        $assetId2 = factory(SdAssets::class)->create()->id;
        $ticketId = factory(Tickets::class)->create()->id;
        //same asset is attached to contract and ticket
        CommonAssetRelation::create(['asset_id' => $assetId1, 'type_id' => $contractId1, 'type' => 'sd_contracts']);
        CommonTicketRelation::create(['ticket_id' => $ticketId, 'type_id' =>  $assetId1, 'type'=> 'sd_assets']);
        CommonAssetRelation::create(['asset_id' => $assetId2, 'type_id' => $contractId2, 'type' => 'sd_contracts']);
        CommonTicketRelation::create(['ticket_id' => $ticketId, 'type_id' =>  $assetId2, 'type'=> 'sd_assets']);

        $response = $this->call('GET', url("service-desk/api/contract/ticket/$ticketId"), ['sort-order' => $sortOrder, 'sort-field' => $sortField]);
        $contractsData = json_decode($response->content())->data->data;
        $response->assertStatus(200);
        $this->assertCount(2, $contractsData);
        foreach ($contractsData as $contract) {
          $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => $contract->name, 'cost' => $contract->cost, 'contract_start_date' => $contract->contract_start_date, 'contract_end_date' => $contract->contract_end_date]);
        }
        $this->assertTrue($contractsData[0]->id < $contractsData[1]->id);
    }

    /** @group getContractsBasedOnAssetAttachedToTicket */
    public function test_getContractsBasedOnAssetAttachedToTicket_WithTicketIdAndPage_returnsAssociatedContractData()
    {
        $this->getLoggedInUserForWeb('agent');
        $sortOrder = 'asc';
        $page = 2;
        $limit = 1;
        $contractId1 = factory(SdContract::class)->create()->id;
        $assetId1 = factory(SdAssets::class)->create()->id;
        $contractId2 = factory(SdContract::class)->create()->id;
        $assetId2 = factory(SdAssets::class)->create()->id;
        $ticketId = factory(Tickets::class)->create()->id;
        //same asset is attached to contract and ticket
        CommonAssetRelation::create(['asset_id' => $assetId1, 'type_id' => $contractId1, 'type' => 'sd_contracts']);
        CommonTicketRelation::create(['ticket_id' => $ticketId, 'type_id' =>  $assetId1, 'type'=> 'sd_assets']);
        CommonAssetRelation::create(['asset_id' => $assetId2, 'type_id' => $contractId2, 'type' => 'sd_contracts']);
        CommonTicketRelation::create(['ticket_id' => $ticketId, 'type_id' =>  $assetId2, 'type'=> 'sd_assets']);

        $response = $this->call('GET', url("service-desk/api/contract/ticket/$ticketId"), ['sort-order' => $sortOrder, 'limit' => $limit, 'page' => $page]);
        $contractsData = json_decode($response->content())->data->data;
        $contract = reset($contractsData);
        $response->assertStatus(200);
        $this->assertCount(1, $contractsData);
        $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => $contract->name, 'cost' => $contract->cost, 'contract_start_date' => $contract->contract_start_date, 'contract_end_date' => $contract->contract_end_date]);
    }

    /** @group getContractsBasedOnAssetAttachedToTicket */
    public function test_getContractsBasedOnAssetAttachedToTicket_WithTicketIdAndWrongPage_returnsEmptyContractData()
    {
        $this->getLoggedInUserForWeb('agent');
        $sortOrder = 'asc';
        $page = 3;
        $limit = 1;
        $contractId1 = factory(SdContract::class)->create()->id;
        $assetId1 = factory(SdAssets::class)->create()->id;
        $contractId2 = factory(SdContract::class)->create()->id;
        $assetId2 = factory(SdAssets::class)->create()->id;
        $ticketId = factory(Tickets::class)->create()->id;
        //same asset is attached to contract and ticket
        CommonAssetRelation::create(['asset_id' => $assetId1, 'type_id' => $contractId1, 'type' => 'sd_contracts']);
        CommonTicketRelation::create(['ticket_id' => $ticketId, 'type_id' =>  $assetId1, 'type'=> 'sd_assets']);
        CommonAssetRelation::create(['asset_id' => $assetId2, 'type_id' => $contractId2, 'type' => 'sd_contracts']);
        CommonTicketRelation::create(['ticket_id' => $ticketId, 'type_id' =>  $assetId2, 'type'=> 'sd_assets']);

        $response = $this->call('GET', url("service-desk/api/contract/ticket/$ticketId"), ['sort-order' => $sortOrder, 'limit' => $limit, 'page' => $page]);
        $contractsData = json_decode($response->content())->data->data;
        $response->assertStatus(200);
        $this->assertEmpty($contractsData);
    }

    /** @group deleteContract */
    public function test_deleteContract_withContractId_returnContractDeleteSuccessfully()
    {
        $this->getLoggedInUserForWeb('admin');
        $contract = factory(SdContract::class)->create();
        $this->assertCount(1, SdContract::get()->toArray());
        $response = $this->call('DELETE', url("service-desk/api/contract/$contract->id"));
        $response->assertStatus(200);
        $this->assertDatabaseMissing('sd_contracts', ['id' => $contract->id]);
    }

    /** @group deleteContract */
    public function test_deleteContract_withWrongContractId_returnsPageNotFoundBladePage()
    {
        $this->getLoggedInUserForWeb('admin');
        $response = $this->call('DELETE', url("service-desk/api/contract/wrong-contract-id"));
        $response->assertStatus(404);
    }

    /** @group createUpdateContract */
    public function test_createUpdateContract_withRequiredFieldAndWithPermission_returnContractSavedSucccessfully()
    {
        $this->getLoggedInUserForWeb('agent');
        $permission = 'create_contract';
        $vendorId = factory(SdVendors::class)->create()->id;
        $assetIds = factory(SdAssets::class)->create()->id;
        $ownerId = Auth::user()->id;
        $this->AddPermission($permission);
        $initialContractsCount = SdContract::count();
        $initialAgentCount = SdContractSdUser::count();
        $initialAssetCount = CommonAssetRelation::count();
        $statusId = 1;
        $licenseTypeId = 1;
        $licensceCount = 3;
        $contractTypeId = 1;
        $approverId = 1;
        $contractStartDate = '2018-09-06 16:32:00';
        $contractEndDate = '2018-10-06 16:32:00';
        $notifyBefore = 3;
        $notifyAgentIds = 1;

        $response = $this->call('POST', url('service-desk/api/contract'), [
                 'name' => 'Apple',
                 'status_id' => $statusId,
                 'contract_type_id' => $contractTypeId,
                 'identifier' => 1,
                 'cost' => 34,
                 'approver_id' => $approverId,
                 'description' => 'Test_Contract',
                 'license_type_id' => $licenseTypeId,
                 'licensce_count' => $licensceCount,
                 'vendor_id' => $vendorId,
                 'contract_start_date' => $contractStartDate,
                 'contract_end_date' => $contractEndDate,
                 'notify_before' => $notifyBefore,
                 'notify_agent_ids' => $notifyAgentIds,
                 'asset_ids' => $assetIds,
                 'owner_id' => $ownerId,
             ]
           );
        $response->assertStatus(200);
        $this->assertDatabaseHas('sd_contracts', [
                 'name' => 'Apple',
                 'status_id' => $statusId,
                 'contract_type_id' => $contractTypeId,
                 'identifier' => 1,
                 'cost' => 34,
                 'approver_id' => $approverId,
                 'description' => 'Test_Contract',
                 'license_type_id' => $licenseTypeId,
                 'licensce_count' => $licensceCount,
                 'vendor_id' => $vendorId,
                 'contract_start_date' => convertDateTimeToUtc($contractStartDate),
                 'contract_end_date' => convertDateTimeToUtc($contractEndDate),
                 'notify_before' => $notifyBefore,
                 'owner_id' => $ownerId,
        ]);
        $contractId = SdContract::where('name','Apple')->value('id');
        $this->assertDatabaseHas('sd_contract_user', [
                 'contract_id' => $contractId,
                 'agent_id' => $notifyAgentIds,
        ]);
        $this->assertDatabaseHas('sd_common_asset_relation', [
                 'asset_id' => $assetIds,
        ]);
        $this->assertEquals($initialContractsCount+1, SdContract::count());
        $this->assertEquals($initialAgentCount+1, SdContractSdUser::count());
        $this->assertEquals($initialAssetCount+1, CommonAssetRelation::count());
    }

    /** @group createUpdateContract */
    public function test_createUpdateContract_withUserAndOrganization_returnContractSavedSucccessfully()
    {
        $this->getLoggedInUserForWeb('agent');
        $permission = 'create_contract';
        $vendorId = factory(SdVendors::class)->create()->id;
        $assetIds = factory(SdAssets::class)->create()->id;
        $ownerId = Auth::user()->id;
        $this->AddPermission($permission);
        $initialContractsCount = SdContract::count();
        $initialAgentCount = SdContractSdUser::count();
        $initialAssetCount = CommonAssetRelation::count();
        $initialUserCount = ContractUserOrganization::count();
        $statusId = 1;
        $licenseTypeId = 1;
        $licensceCount = 3;
        $contractTypeId = 1;
        $approverId = 1;
        $contractStartDate = '2018-09-06 16:32:00';
        $contractEndDate = '2018-10-06 16:32:00';
        $notifyBefore = 3;
        $notifyAgentIds = 1;
        $userId=1;
        $organizationId=factory(Organization::class)->create()->id;

        $response = $this->call('POST', url('service-desk/api/contract'), [
                 'name' => 'Apple',
                 'status_id' => $statusId,
                 'contract_type_id' => $contractTypeId,
                 'identifier' => 1,
                 'cost' => 34,
                 'approver_id' => $approverId,
                 'description' => 'Test_Contract',
                 'license_type_id' => $licenseTypeId,
                 'licensce_count' => $licensceCount,
                 'vendor_id' => $vendorId,
                 'contract_start_date' => $contractStartDate,
                 'contract_end_date' => $contractEndDate,
                 'notify_before' => $notifyBefore,
                 'notify_agent_ids' => $notifyAgentIds,
                 'asset_ids' => $assetIds,
                 'owner_id' => $ownerId,
                 'user_ids' => $userId,
                 'organization_ids' => $organizationId,
             ]
           );
        $response->assertStatus(200);
        $this->assertDatabaseHas('sd_contracts', [
                 'name' => 'Apple',
                 'status_id' => $statusId,
                 'contract_type_id' => $contractTypeId,
                 'identifier' => 1,
                 'cost' => 34,
                 'approver_id' => $approverId,
                 'description' => 'Test_Contract',
                 'license_type_id' => $licenseTypeId,
                 'licensce_count' => $licensceCount,
                 'vendor_id' => $vendorId,
                 'contract_start_date' => convertDateTimeToUtc($contractStartDate),
                 'contract_end_date' => convertDateTimeToUtc($contractEndDate),
                 'notify_before' => $notifyBefore,
                 'owner_id' => $ownerId,
        ]);
        $contractId = SdContract::where('name','Apple')->value('id');
        $this->assertDatabaseHas('sd_contract_user', [
                 'contract_id' => $contractId,
                 'agent_id' => $notifyAgentIds,
        ]);
        $this->assertDatabaseHas('sd_common_asset_relation', [
                 'asset_id' => $assetIds,
        ]);
        $this->assertDatabaseHas('sd_contract_user_organization', [
                'contract_id' => $contractId,
                'user_id' => $userId,
                'organization_id' => $organizationId,

        ]);

        $this->assertEquals($initialContractsCount+1, SdContract::count());
        $this->assertEquals($initialAgentCount+1, SdContractSdUser::count());
        $this->assertEquals($initialAssetCount+1, CommonAssetRelation::count());
        $this->assertEquals($initialUserCount+1, ContractUserOrganization::count());
    }

    /** @group createUpdateContract */
    public function test_createUpdateContract_withUser_returnContractSavedSucccessfully()
    {
        $this->getLoggedInUserForWeb('agent');
        $permission = 'create_contract';
        $vendorId = factory(SdVendors::class)->create()->id;
        $assetIds = factory(SdAssets::class)->create()->id;
        $ownerId = Auth::user()->id;
        $this->AddPermission($permission);
        $initialContractsCount = SdContract::count();
        $initialAgentCount = SdContractSdUser::count();
        $initialAssetCount = CommonAssetRelation::count();
        $initialUserCount = ContractUserOrganization::count();
        $statusId = 1;
        $licenseTypeId = 1;
        $licensceCount = 3;
        $contractTypeId = 1;
        $approverId = 1;
        $contractStartDate = '2018-09-06 16:32:00';
        $contractEndDate = '2018-10-06 16:32:00';
        $notifyBefore = 3;
        $notifyAgentIds = 1;
        $userId=1;

        $response = $this->call('POST', url('service-desk/api/contract'), [
                 'name' => 'Apple',
                 'status_id' => $statusId,
                 'contract_type_id' => $contractTypeId,
                 'identifier' => 1,
                 'cost' => 34,
                 'approver_id' => $approverId,
                 'description' => 'Test_Contract',
                 'license_type_id' => $licenseTypeId,
                 'licensce_count' => $licensceCount,
                 'vendor_id' => $vendorId,
                 'contract_start_date' => $contractStartDate,
                 'contract_end_date' => $contractEndDate,
                 'notify_before' => $notifyBefore,
                 'notify_agent_ids' => $notifyAgentIds,
                 'asset_ids' => $assetIds,
                 'owner_id' => $ownerId,
                 'user_ids' => $userId,
             ]
           );
        $response->assertStatus(200);
        $this->assertDatabaseHas('sd_contracts', [
                 'name' => 'Apple',
                 'status_id' => $statusId,
                 'contract_type_id' => $contractTypeId,
                 'identifier' => 1,
                 'cost' => 34,
                 'approver_id' => $approverId,
                 'description' => 'Test_Contract',
                 'license_type_id' => $licenseTypeId,
                 'licensce_count' => $licensceCount,
                 'vendor_id' => $vendorId,
                 'contract_start_date' => convertDateTimeToUtc($contractStartDate),
                 'contract_end_date' => convertDateTimeToUtc($contractEndDate),
                 'notify_before' => $notifyBefore,
                 'owner_id' => $ownerId,
        ]);
        $contractId = SdContract::where('name','Apple')->value('id');
        $this->assertDatabaseHas('sd_contract_user', [
                 'contract_id' => $contractId,
                 'agent_id' => $notifyAgentIds,
        ]);
        $this->assertDatabaseHas('sd_common_asset_relation', [
                 'asset_id' => $assetIds,
        ]);
        $this->assertDatabaseHas('sd_contract_user_organization', [
                'contract_id' => $contractId,
                'user_id' => $userId,

        ]);

        $this->assertEquals($initialContractsCount+1, SdContract::count());
        $this->assertEquals($initialAgentCount+1, SdContractSdUser::count());
        $this->assertEquals($initialAssetCount+1, CommonAssetRelation::count());
        $this->assertEquals($initialUserCount+1, ContractUserOrganization::count());
    }

    /** @group createUpdateContract */
    public function test_createUpdateContract_withoutFewRequiredFieldsAndWithPermission_returnFieldRequiredException()
    {
        $this->getLoggedInUserForWeb('agent');
        $permission = 'create_contract';
        $this->AddPermission($permission);
        $initialCount = SdContract::count();
        $assetIds = factory(SdAssets::class)->create()->id;

        $response = $this->call('POST', url('service-desk/api/contract'), [
                 'name' => 'Apple',
                 'description' => 'Test_Contract',
                 'notify_before' => 3,
                 'notify_agent_ids' => 1,
                 'asset_ids' => $assetIds,
                 'owner_id' => 1,
             ]
           );
        $response->assertStatus(412);
        $this->assertDatabaseMissing('sd_contracts', [
                 'name' => 'Apple',
                 'description' => 'Test_Contract',
                 'notify_before' => 3,
                 'owner_id' => 1,
        ]);
        $this->assertEquals($initialCount, SdContract::count());  
    }

    /** @group createUpdateContract */
    public function test_createUpdateContract_withRequiredField_returnsPermisssionDeniedError()
    {
        $this->getLoggedInUserForWeb('agent');
        $vendorId = factory(SdVendors::class)->create()->id;
        $ownerId = Auth::user()->id;
        $statusId = 1;
        $licenseTypeId = 1;
        $licensceCount = 3;
        $contractTypeId = 1;
        $approverId = 1;
        $contractStartDate = '2018-09-06 16:32:00';
        $contractEndDate = '2018-10-06 16:32:00';
        $notifyBefore = 3;
        $notifyAgentIds = 1;
        $assetIds = factory(SdAssets::class)->create()->id;
        $response = $this->call('POST', url('service-desk/api/contract'), [
                 'name' => 'Apple',
                 'status_id' => $statusId,
                 'contract_type_id' => $contractTypeId,
                 'identifier' => 1,
                 'cost' => 34,
                 'approver_id' => $approverId,
                 'description' => 'Test_Contract',
                 'license_type_id' => $licenseTypeId,
                 'licensce_count' => $licensceCount,
                 'vendor_id' => $vendorId,
                 'contract_start_date' => $contractStartDate,
                 'contract_end_date' => $contractEndDate,
                 'notify_before' => $notifyBefore,
                 'notify_agent_ids' => $notifyAgentIds,
                 'asset_ids' => $assetIds,
                 'owner_id' => $ownerId,
             ]
           );
        // permission denied error
        $response->assertStatus(400);
    }

    /**
    * method to add permission
    * @param string $permission
    * @return null
    */
    private function AddPermission($permission)
    {
        // adding permission to logged in agent
        $this->user->sdPermission()->create(['user_id' => $this->user->id, 'permission' => [$permission => 1]]);
    } 


    /** @group createUpdateContract */
    public function test_createUpdateChange_withAddAttachment_returnsContractSavedSuccessfully()
    {
        $this->getLoggedInUserForWeb('agent');
        $permission = 'create_contract';
        $this->AddPermission($permission);
        $vendorId = factory(SdVendors::class)->create()->id;
        $contract = factory(SdContract::class)->create(['name' => 'Downgrade Web Server']);
        $ownerId = Auth::user()->id;
        $initialContractsCount = SdContract::count();
        $initialAttachmentsCount = Attachments::count();
        $initialAgentCount = SdContractSdUser::count();
        $initialAssetCount = CommonAssetRelation::count();
        $statusId = 1;
        $licenseTypeId = 1;
        $licensceCount = 3;
        $contractTypeId = 1;
        $approverId = 1;
        $contractStartDate = '2018-09-06 16:32:00';
        $contractEndDate = '2018-10-06 16:32:00';
        $notifyBefore = 3;
        $notifyAgentIds = 1;
        $assetIds = factory(SdAssets::class)->create()->id;
        $fileName = 'document.pdf';
        Storage::fake($fileName);
        $response = $this->call('POST', url('service-desk/api/contract'), [
                 'name' => 'Apple',
                 'status_id' => $statusId,
                 'contract_type_id' => $contractTypeId,
                 'identifier' => 1,
                 'cost' => 34,
                 'approver_id' => $approverId,
                 'description' => 'Test_Contract',
                 'license_type_id' => $licenseTypeId,
                 'licensce_count' => $licensceCount,
                 'vendor_id' => $vendorId,
                 'contract_start_date' => $contractStartDate,
                 'contract_end_date' => $contractEndDate,
                 'notify_before' => $notifyBefore,
                 'notify_agent_ids' => $notifyAgentIds,
                 'asset_ids' => $assetIds,
                 'owner_id' => $ownerId,
                 'attachment' => UploadedFile::fake()->create($fileName, 20),
             ]
           );
        $response->assertStatus(200);
        $this->assertDatabaseHas('sd_contracts', [
                 'name' => 'Apple',
                 'status_id' => $statusId,
                 'contract_type_id' => $contractTypeId,
                 'identifier' => 1,
                 'cost' => 34,
                 'approver_id' => $approverId,
                 'description' => 'Test_Contract',
                 'license_type_id' => $licenseTypeId,
                 'licensce_count' => $licensceCount,
                 'vendor_id' => $vendorId,
                 'contract_start_date' => convertDateTimeToUtc($contractStartDate),
                 'contract_end_date' => convertDateTimeToUtc($contractEndDate),
                 'notify_before' => $notifyBefore,
                 'owner_id' => $ownerId,
        ]);

        $this->assertEquals($initialContractsCount+1, SdContract::count());
        $this->assertEquals($initialAttachmentsCount+1, Attachments::count());
    }


    /** @group createUpdateContract */
    public function test_createUpdateContract_withNotifyToEmailAddress_returnContractSavedSucccessfully()
    {
        $this->getLoggedInUserForWeb('agent');
        $permission = 'create_contract';
        $vendorId = factory(SdVendors::class)->create()->id;
        $ownerId = Auth::user()->id;
        $this->AddPermission($permission);
        $initialEmailCount = Email::count();
        $initialContractsCount = SdContract::count();
        $initialAgentCount = SdContractSdUser::count();
        $initialAssetCount = CommonAssetRelation::count();
        $statusId = 1;
        $licenseTypeId = 1;
        $licensceCount = 3;
        $contractTypeId = 1;
        $approverId = 1;
        $contractStartDate = '2018-09-06 16:32:00';
        $contractEndDate = '2018-10-06 16:32:00';
        $notifyBefore = 3;
        $notifyAgentIds = 1;
        $assetIds = factory(SdAssets::class)->create()->id;
        $email = ['kabhishek427.ak@gmail.com'];

        $response = $this->call('POST', url('service-desk/api/contract'), [
                 'name' => 'Apple',
                 'status_id' => $statusId,
                 'contract_type_id' => $contractTypeId,
                 'identifier' => 1,
                 'cost' => 34,
                 'approver_id' => $approverId,
                 'description' => 'Test_Contract',
                 'license_type_id' => $licenseTypeId,
                 'licensce_count' => $licensceCount,
                 'vendor_id' => $vendorId,
                 'contract_start_date' => $contractStartDate,
                 'contract_end_date' => $contractEndDate,
                 'notify_before' => $notifyBefore,
                 'notify_agent_ids' => $notifyAgentIds,
                 'asset_ids' => $assetIds,
                 'owner_id' => $ownerId,
                 'email_ids' => $email,
             ]
           );
        $response->assertStatus(200);
        $this->assertDatabaseHas('sd_contracts', [
                 'name' => 'Apple',
                 'status_id' => $statusId,
                 'contract_type_id' => $contractTypeId,
                 'identifier' => 1,
                 'cost' => 34,
                 'approver_id' => $approverId,
                 'description' => 'Test_Contract',
                 'license_type_id' => $licenseTypeId,
                 'licensce_count' => $licensceCount,
                 'vendor_id' => $vendorId,
                 'contract_start_date' => convertDateTimeToUtc($contractStartDate),
                 'contract_end_date' => convertDateTimeToUtc($contractEndDate),
                 'notify_before' => $notifyBefore,
                 'owner_id' => $ownerId,
        ]);
        $contractId = SdContract::where('name','Apple')->value('id');
        $this->assertDatabaseHas('sd_contract_user', [
                 'contract_id' => $contractId,
                 'agent_id' => $notifyAgentIds,
        ]);
        $this->assertDatabaseHas('sd_common_asset_relation', [
                 'asset_id' => $assetIds,
        ]);

        $this->assertDatabaseHas('sd_emails', [
                 'email' => 'kabhishek427.ak@gmail.com',
        ]);
        $emailId = Email::where('email' , 'kabhishek427.ak@gmail.com')->value('id');
        $this->assertDatabaseHas('sd_emails_sources',[
                 'email_id' => $emailId,
                 'source_id' => $contractId,
                 'source_type' => 'App\Plugins\ServiceDesk\Model\Contract\SdContract',
        ]);
        $this->assertEquals($initialContractsCount+1, SdContract::count());
        $this->assertEquals($initialEmailCount+1, Email::count());
        $this->assertEquals($initialAgentCount+1, SdContractSdUser::count());
        $this->assertEquals($initialAssetCount+1, CommonAssetRelation::count());
    }

    /** @group getContract */
    public function test_getContract_withContractId_returnsContractDataBasedonContractId()
    {
        $this->getLoggedInUserForWeb('agent');
        $permission = 'edit_contract';
        $this->AddPermission($permission);
        $contractInDb = factory(SdContract::class)->create(['license_type_id'=>1,'notify_before'=>3,]);
        $assetId = factory(SdAssets::class)->create()->id;
        $contractInDb->attachAsset()->attach([$assetId => ['type' => 'sd_contracts']]);
        $asset = CommonAssetRelation::get();
        
        $auth = Auth::user();
        $contractInDb->attachAgents()->attach($auth->id);
        
        $response = $this->call('GET', url("service-desk/api/contract/$contractInDb->id"));
        $response->assertStatus(200);
        $contractInResponse = json_decode($response->content())->data->contract;
        $this->assertDatabaseHas('sd_contracts', [
                 'id' => $contractInDb->id,
                 'name' => $contractInResponse->name,
                 'status_id' => $contractInResponse->status->id,
                 'contract_type_id' => $contractInResponse->contractType->id,
                 'identifier' => $contractInResponse->identifier,
                 'cost' => $contractInResponse->cost,
                 'approver_id' => $contractInResponse->approver->id,
                 'description' => $contractInResponse->description,
                 'license_type_id' => $contractInResponse->licence->id,
                 'licensce_count' => $contractInResponse->licensce_count,
                 'vendor_id' => $contractInResponse->vendor->id,
                 'contract_start_date' => $contractInResponse->contract_start_date,
                 'contract_end_date' => $contractInResponse->contract_end_date,
                 'notify_before' => $contractInResponse->notify_before,
                 'owner_id' => $contractInResponse->owner->id,
        ]);
    }

    /** @group getContract */
    public function test_getContract_withWrongContractId_returnsContractNotFound()
    {
        $this->getLoggedInUserForWeb('agent');
        $permission = 'edit_contract';
        $this->AddPermission($permission);
        $response = $this->call('GET', url("service-desk/api/contract/wrong-contract-id"));
        $response->assertStatus(404);
    }

    /** @group attachAsset */
    public function test_attachAsset_withContractIdAndAssetId_returnsAssetAttachedSuccessfully()
    {
        $this->getLoggedInUserForWeb('admin');
        $contractId = factory(SdContract::class)->create()->id;
        $assetId = factory(SdAssets::class)->create()->id;
        $response = $this->call('POST', url("service-desk/api/contract-attach-asset"),['contract_id' => $contractId,'asset_ids' => [$assetId]]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('sd_common_asset_relation', ['type_id' => $contractId,'asset_id' => $assetId,'type' => 'sd_contracts']);
    }

    /** @group attachAsset */
    public function test_attachAsset_withWrongContractIdAndAssetId_returnsContracNotFound()
    {
        $this->getLoggedInUserForWeb('admin');
        $asset = factory(SdAssets::class)->create();
        $response = $this->call('POST', url("service-desk/api/contract-attach-asset"),['contract_id' => 'wrong-contract-id','asset_ids' => [$asset->id]]);
        $response->assertStatus(400);
    }

    /** @group detachAsset */
    public function test_detachAsset_withWrongContractIdAndAssetId_returnsContractNotFound()
    {
        $this->getLoggedInUserForWeb('admin');
        $response = $this->call('DELETE', url("service-desk/api/contract-detach-asset/wrong-contractId/wrong-assetId"));
        $response->assertStatus(404);
    }

    /** @group detachAsset */
    public function test_detachAsset_withContractIdAndAssetId_returnsAssetDetachedSuccessfully()
    {
        $this->getLoggedInUserForWeb('admin');
        $assetId = factory(SdAssets::class)->create()->id;
        $contractId = factory(SdContract::class)->create()->id;
        CommonAssetRelation::create(['asset_id' => $assetId, 'type_id' => $contractId, 'type' => 'sd_contracts']);
        $this->assertDatabaseHas('sd_common_asset_relation', ['asset_id' => $assetId, 'type_id' => $contractId, 'type' => 'sd_contracts']);
        $response = $this->call('DELETE', url("service-desk/api/contract-detach-asset/$contractId/$assetId"));
        $this->assertDatabaseMissing('sd_common_asset_relation', ['asset_id' => $assetId, 'type_id' => $contractId, 'type' => 'sd_contracts']);
        $response->assertStatus(200);
    }

    /** @group terminateContract */
    public function test_terminateContract_withContractId_returnsContractTerminatedSuccessfully()
    {
        $this->getLoggedInUserForWeb('admin');
        $contract = factory(SdContract::class)->create(['status_id' => 3,'owner_id' =>Auth::user()->id]);
        $response = $this->call('POST', url("service-desk/api/contract-terminate/$contract->id"));
        $response->assertStatus(200);
        $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id,'status_id' => 4,'renewal_status_id'=>null]);
    }

    /** @group terminateContract */
    public function test_terminateContract_withWrongContractId_returnsPageNotFound()
    {
        $this->getLoggedInUserForWeb('admin');
        $response = $this->call('POST', url("service-desk/api/contract-terminate/wrong-contract-id"));
        $response->assertStatus(404);
    }

    /** @group extendContract */
    public function test_extendContract_withContractId_returnsContractExtendedSuccessfully()
    {
        $this->getLoggedInUserForWeb('admin');
        $approverId = 1;
        $contractStartDate = '2018-09-06 16:32:00';
        $contractEndDate = '2018-10-06 16:32:00';
        $contract = factory(SdContract::class)->create(['status_id' => 3,'owner_id' =>Auth::user()->id]);
        $response = $this->call('POST', url("service-desk/api/contract-extend/{$contract->id}"),[
                 'cost' => 34,
                 'approver_id' => $approverId,
                 'contract_start_date' => $contractStartDate,
                 'contract_end_date' => $contractEndDate,
             ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id,'renewal_status_id' => '10']);
    }

    /** @group extendContract */
    public function test_extendContract_withWrongContractId_returnsPageNotFound()
    {
        $this->getLoggedInUserForWeb('admin');
        $approverId = 1;
        $contractStartDate = '2018-09-06 16:32:00';
        $contractEndDate = '2018-10-06 16:32:00';
        $contract = factory(SdContract::class)->create(['status_id' => 3,'owner_id' =>Auth::user()->id]);
        $response = $this->call('POST', url("service-desk/api/contract-extend/wrong-contract-id"),[
                 'cost' => 34,
                 'approver_id' => $approverId,
                 'contract_start_date' => $contractStartDate,
                 'contract_end_date' => $contractEndDate,
             ]);
        $response->assertStatus(404);
    }

    /** @group rejectContract */
    public function test_rejectContract_withContractId_returnsContractRejectedSuccessfully()
    {
        $this->getLoggedInUserForWeb('admin');
        $approverId = 1;
        $contract = factory(SdContract::class)->create([
          'status_id' => 1,
          'approver_id' => Auth::user()->id,
          'renewal_status_id' => null,
          'contract_start_date' => '2020-03-06 16:32:00',
          'contract_end_date' => '2020-10-06 16:32:00',
        ]);

        $thread = [
          'contract_id' => $contract->id,
          'status_id' => $contract->status_id,
          'renewal_status_id' => null,
          'cost' => $contract->cost,
          'contract_start_date' => $contract->contract_start_date,
          'contract_end_date' => $contract->contract_end_date,
          'owner_id' => $contract->owner_id,
          'approver_id' => $contract->approver_id
        ];
        SdContractThread::create($thread);
        $response = $this->call('POST', url("service-desk/api/contract-reject/{$contract->id}"),[
                'purpose_of_rejection' => 'Rejected',
             ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id,'status_id' => '6']);
    }

    /** @group rejectContract */
    public function test_rejectContract_withWrongContractId_returnsPageNotFound()
    {
        $this->getLoggedInUserForWeb('admin');
        $approverId = 1;
        $contractStartDate = '2018-09-06 16:32:00';
        $contractEndDate = '2018-10-06 16:32:00';
        $contract = factory(SdContract::class)->create([
          'status_id' => 1,
          'approver_id' => Auth::user()->id,
          'renewal_status_id' => null,
          'contract_start_date' => '2020-03-06 16:32:00',
          'contract_end_date' => '2020-10-06 16:32:00',
        ]);

        $thread = [
          'contract_id' => $contract->id,
          'status_id' => $contract->status_id,
          'renewal_status_id' => null,
          'cost' => $contract->cost,
          'contract_start_date' => $contract->contract_start_date,
          'contract_end_date' => $contract->contract_end_date,
          'owner_id' => $contract->owner_id,
          'approver_id' => $contract->approver_id
        ];
        SdContractThread::create($thread);
        $response = $this->call('POST', url("service-desk/api/contract-reject/wrong-contract-id"),[
                'purpose_of_rejection' => 'Rejected',
             ]);
        $response->assertStatus(404);
    }

    /** @group renewContract */
    public function test_renewContract_withContractId_returnsContractRenewedSuccessfully()
    {
        $this->getLoggedInUserForWeb('admin');
        $approverId = 1;
        $contractStartDate = '2018-09-06 16:32:00';
        $contractEndDate = '2018-10-06 16:32:00';
        $contract = factory(SdContract::class)->create(['status_id' => 3,'owner_id' =>Auth::user()->id]);
        $response = $this->call('POST', url("service-desk/api/contract-renew/{$contract->id}"),[
                 'cost' => 34,
                 'approver_id' => $approverId,
                 'contract_start_date' => $contractStartDate,
                 'contract_end_date' => $contractEndDate,
             ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id,'renewal_status_id' => '7']);
    }

    /** @group renewContract */
    public function test_renewContract_withWrongContractId_returnsPageNotFound()
    {
        $this->getLoggedInUserForWeb('admin');
        $approverId = 1;
        $contractStartDate = '2018-09-06 16:32:00';
        $contractEndDate = '2018-10-06 16:32:00';
        $contract = factory(SdContract::class)->create(['status_id' => 3,'owner_id' =>Auth::user()->id]);
        $response = $this->call('POST', url("service-desk/api/contract-renew/wrong-contract-id"),[
                 'cost' => 34,
                 'approver_id' => $approverId,
                 'contract_start_date' => $contractStartDate,
                 'contract_end_date' => $contractEndDate,
             ]);
        $response->assertStatus(404);
    }

    /** @group approveContract */
    public function test_approveContract_withContractId_returnsContractApprovedSuccessfully()
    {
        $this->getLoggedInUserForWeb('admin');
        $contract = factory(SdContract::class)->create([
          'status_id' => 1,
          'approver_id' => Auth::user()->id,
          'renewal_status_id' => null,
          'contract_start_date' => '2020-05-06 16:32:00',
          'contract_end_date' => '2020-06-06 16:32:00',
        ]);

        $thread = [
          'contract_id' => $contract->id,
          'status_id' => $contract->status_id,
          'renewal_status_id' => null,
          'cost' => $contract->cost,
          'contract_start_date' => $contract->contract_start_date,
          'contract_end_date' => $contract->contract_end_date,
          'owner_id' => $contract->owner_id,
          'approver_id' => $contract->approver_id
        ];

        SdContractThread::create($thread);
        $response = $this->call('POST', url("service-desk/api/contract-approve/{$contract->id}"));
        $response->assertStatus(200);
        $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id,'renewal_status_id' =>null,'status_id' => '5']);
        $this->assertDatabaseHas('sd_contract_threads', ['contract_id' => $contract->id,'renewal_status_id' =>null,'status_id' => '5', 'contract_start_date' => $contract->contract_start_date, 'contract_end_date' => $contract->contract_end_date]);
    }

    /** @group approveContract */
    public function test_approveContract_withWrongContractId_returnsPageNotFound()
    {
        $this->getLoggedInUserForWeb('admin');
        $contract = factory(SdContract::class)->create([
          'status_id' => 1,
          'approver_id' => Auth::user()->id,
          'renewal_status_id' => null,
          'contract_start_date' => '2018-09-06 16:32:00',
          'contract_end_date' => '2018-10-06 16:32:00',
        ]);

        $thread = [
          'contract_id' => $contract->id,
          'status_id' => $contract->status_id,
          'renewal_status_id' => null,
          'cost' => $contract->cost,
          'contract_start_date' => $contract->contract_start_date,
          'contract_end_date' => $contract->contract_end_date,
          'owner_id' => $contract->owner_id,
          'approver_id' => $contract->approver_id
        ];

        SdContractThread::create($thread);
        $response = $this->call('POST', url("service-desk/api/contract-approve/wrong-contract-id"));
        $response->assertStatus(404);
    }
}
