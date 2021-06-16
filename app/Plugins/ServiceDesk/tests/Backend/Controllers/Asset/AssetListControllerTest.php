<?php

namespace App\Plugins\ServiceDesk\tests\Backend\Controllers\Asset;

use Tests\AddOnTestCase;
use App\Model\helpdesk\Agent_panel\Organization;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\Plugins\ServiceDesk\Model\Products\SdProducts;
use App\Plugins\ServiceDesk\Model\Problem\SdProblem;
use App\Plugins\ServiceDesk\Model\Common\CommonTicketRelation;
use App\Model\helpdesk\Ticket\Tickets;
use App\Plugins\ServiceDesk\Model\Report\SdReportFilter;
use App\Plugins\ServiceDesk\Model\Report\SdReportFilterMeta;
use DB;
use App\Plugins\ServiceDesk\Model\Changes\SdChanges;
use App\Plugins\ServiceDesk\Model\Releases\SdReleases;
use App\Plugins\ServiceDesk\Model\Assets\CommonAssetRelation;
use App\Plugins\ServiceDesk\Model\Contract\SdContract;
use App\Traits\FaveoDateParser;

/**
 * Tests AssetListController
 * 
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
*/

class AssetListControllerTest extends AddOnTestCase
{
  use FaveoDateParser;

  /** @group getAssetList */
  public function test_getAssetList_withLimit()
  {
    $this->getLoggedInUserForWeb('admin');
    $assetNameInDb = factory(SdAssets::class)->create()->name;
    $limit = 1;
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['limit' => $limit]);
    $assetName = reset(json_decode($response->content())->data->assets)->name;
    $response->status(200);
    $this->assertCount($limit, json_decode($response->content())->data->assets);
    $this->assertEquals($assetNameInDb, $assetName);
  }

  /** @group getAssetList */
  public function test_getAssetList_withSortFieldAndSortOrder()
  {
    $this->getloggedInUserForWeb('admin');
    factory(SdAssets::class, 3)->create();
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['sort-order' => 'asc', 'sort-field' => 'id']);
    $assets = json_decode($response->content())->data->assets;
    $response->status(200);
    $this->assertEquals(reset($assets)->id, SdAssets::orderBy('id', 'asc')->first()->id);
    $this->assertEquals(reset($assets)->name, SdAssets::orderBy('id', 'asc')->first()->name);
    $this->assertCount(3, $assets);
  }

  /** @group getAssetList */
  public function test_getAssetList_withPage()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdAssets::class,3)->create();
    $sortOrder = 'asc';
    $page = 2;
    $limit = 1;
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['sort-order' => $sortOrder, 'limit' => $limit, 'page' => $page]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->assets);
    $this->assertEquals($data->current_page, $page);
    $this->assertDatabaseHas('sd_assets', ['id' => reset($data->assets)->id, 'name' => reset($data->assets)->name]);
  }

  /** @group getAssetList */
  public function test_getAssetList_withSearchQuery()
  {
    $this->getloggedInUserForWeb('admin');
    factory(SdAssets::class, 3)->create();
    $searchQuery = SdAssets::orderBy('id', 'desc')->first()->name;
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['search-query' => $searchQuery]);
    $asset = json_decode($response->content())->data->assets;
    $response->status(200);
    $this->assertEquals(reset($asset)->name, $searchQuery);
    $this->assertCount(1, $asset);
  }

  /** @group getAssetList */
  public function test_getAssetList_withSearchQueryAssetName()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create();
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $asset->name]);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['search-query' => $asset->name]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->assets);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->assets)->name, $asset->name);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => reset($data->assets)->name]);
  }

  /** @group getAssetList */
  public function test_getAssetList_withSearchQueryAssignedOn()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create();
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $asset->name]);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['search-query' => $asset->assigned_on]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->assets);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->assets)->name, $asset->name);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => reset($data->assets)->name]);
  }

  /** @group getAssetList */
  public function test_getAssetList_withSearchQueryDepartmentName()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create(['department_id' => 2]);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $asset->name]);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['search-query' => 'Sales']);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->assets);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->assets)->name, $asset->name);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => reset($data->assets)->name]);
  }

  /** @group getAssetList */
  public function test_getAssetList_withSearchQueryOrganizationName()
  {
    $this->getLoggedInUserForWeb('admin');
    $organization = factory(Organization::class)->create();
    $asset = factory(SdAssets::class)->create(['organization_id' => $organization->id]);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $asset->name]);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['search-query' => $organization->name]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->assets);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->assets)->name, $asset->name);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => reset($data->assets)->name]);
  }

  /** @group getAssetList */
  public function test_getAssetList_withSearchQueryProductName()
  {
    $this->getLoggedInUserForWeb('admin');
    $product = factory(SdProducts::class)->create();
    $asset = factory(SdAssets::class)->create(['product_id' => $product->id]);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $asset->name]);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['search-query' => $product->name]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->assets);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->assets)->name, $asset->name);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => reset($data->assets)->name]);
  }

  /** @group getAssetList */
  public function test_getAssetList_withSearchQueryAssetTypeName()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create(['asset_type_id' => 2]);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $asset->name]);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['search-query' => 'Cloud']);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->assets);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->assets)->name, $asset->name);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => reset($data->assets)->name]);
  }

  /** @group getAssetList */
  public function test_getAssetList_withSearchQueryLocationName()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create(['location_id' => 2]);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $asset->name]);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['search-query' => 'Delhi']);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->assets);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->assets)->name, $asset->name);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => reset($data->assets)->name]);
  }

  /** @group getAssetList */
  public function test_getAssetList_withSearchQueryTicketNumber()
  {
    $this->getLoggedInUserForWeb('admin');
    $ticket = factory(Tickets::class)->create();
    $this->assertDatabaseHas('tickets', ['id' => $ticket->id]);
    $asset = factory(SdAssets::class)->create(['location_id' => 2]);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $asset->name]);
    CommonTicketRelation::create(['ticket_id' => $ticket->id, 'type_id' => $asset->id, 'type' => 'sd_assets']);
    $this->assertDatabaseHas('sd_common_ticket_relation', ['ticket_id' => $ticket->id, 'type_id' => $asset->id, 'type' => 'sd_assets']);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['search-query' => $ticket->ticket_number]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->assets);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->assets)->name, $asset->name);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => reset($data->assets)->name]);
  }

  /** @group getAssetList */
  public function test_getAssetList_withSearchQueryUsedByFirstName()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create(['used_by_id' => $this->user->id]);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $asset->name]);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['search-query' => $this->user->first_name]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->assets);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->assets)->name, $asset->name);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => reset($data->assets)->name]);
  }

  /** @group getAssetList */
  public function test_getAssetList_withSearchQueryUsedByLastName()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create(['used_by_id' => $this->user->id]);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $asset->name]);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['search-query' => $this->user->last_name]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->assets);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->assets)->name, $asset->name);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => reset($data->assets)->name]);
  }

  /** @group getAssetList */
  public function test_getAssetList_withSearchQueryUsedByFullName()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create(['used_by_id' => $this->user->id]);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $asset->name]);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['search-query' => $this->user->first_name . ' ' . $this->user->last_name]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->assets);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->assets)->name, $asset->name);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => reset($data->assets)->name]);
  }

  /** @group getAssetList */
  public function test_getAssetList_withSearchQueryUsedByUserName()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create(['used_by_id' => $this->user->id]);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $asset->name]);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['search-query' => $this->user->user_name]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->assets);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->assets)->name, $asset->name);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => reset($data->assets)->name]);
  }

  /** @group getAssetList */
  public function test_getAssetList_withSearchQueryUsedByEmail()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create(['used_by_id' => $this->user->id]);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $asset->name]);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['search-query' => $this->user->email]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->assets);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->assets)->name, $asset->name);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => reset($data->assets)->name]);
  }

    /** @group getAssetList */
  public function test_getAssetList_withSearchQueryManagedByFirstName()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create(['managed_by_id' => $this->user->id]);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $asset->name]);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['search-query' => $this->user->first_name]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->assets);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->assets)->name, $asset->name);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => reset($data->assets)->name]);
  }

  /** @group getAssetList */
  public function test_getAssetList_withSearchQueryManagedByLastName()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create(['managed_by_id' => $this->user->id]);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $asset->name]);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['search-query' => $this->user->last_name]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->assets);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->assets)->name, $asset->name);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => reset($data->assets)->name]);
  }

  /** @group getAssetList */
  public function test_getAssetList_withSearchQueryManagedByFullName()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create(['managed_by_id' => $this->user->id]);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $asset->name]);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['search-query' => $this->user->first_name . ' ' . $this->user->last_name]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->assets);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->assets)->name, $asset->name);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => reset($data->assets)->name]);
  }

  /** @group getAssetList */
  public function test_getAssetList_withSearchQueryManagedByUserName()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create(['managed_by_id' => $this->user->id]);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $asset->name]);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['search-query' => $this->user->user_name]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->assets);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->assets)->name, $asset->name);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => reset($data->assets)->name]);
  }

  /** @group getAssetList */
  public function test_getAssetList_withSearchQueryManagedByEmail()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create(['managed_by_id' => $this->user->id]);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $asset->name]);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['search-query' => $this->user->email]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->assets);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->assets)->name, $asset->name);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => reset($data->assets)->name]);
  }

  /** @group getAssetList */
  public function test_getAssetList_withSearchQueryAndDepartment()
  {
    $this->getloggedInUserForWeb('admin');
    factory(SdAssets::class)->create();
    factory(SdAssets::class, 2)->create(['department_id' => 2]);
    $searchQuery = SdAssets::orderBy('id', 'desc')->first()->name;
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['search-query' => $searchQuery, 'dept_ids' => 2]);
    $asset = json_decode($response->content())->data->assets;
    $assetId = SdAssets::where([['name' => $searchQuery], ['department_id' => 2]]);
    $response->status(200);
    $this->assertEquals(reset($asset)->name, $searchQuery);
    $this->assertCount(1, $asset);
  }

  /** @group getAssetList */
  public function test_getAssetList_withManyAssets()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create();
    $this->assertDatabaseHas('sd_assets', ['name' => $asset->name]);
    SdAssets::create(['name' => 'asset1', 'external_id' => 2]);
    $this->assertDatabaseHas('sd_assets', ['name' => 'asset1']);
    SdAssets::create(['name' => 'asset2', 'external_id' => 4]);
    $this->assertDatabaseHas('sd_assets', ['name' => 'asset2']);
    $limit = 3;
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['limit' => $limit, 'sort-field' => 'id', 'sort-order' => 'asc']);
    $assetName = reset(json_decode($response->content())->data->assets)->name;
    $response->status(200);
    $this->assertCount($limit, json_decode($response->content())->data->assets);
    $this->assertEquals($asset->name, $assetName);
  }

  /** @group getAssetList */
  public function test_getAssetList_withTicket()
  {
    $this->getLoggedInUserForWeb('admin');
    $limit = 1;
    $ticketId = factory(Tickets::class)->create()->id;
    $asset = factory(SdAssets::class)->create();
    CommonTicketRelation::create(['ticket_id' => $ticketId, 'type_id' => $asset->id, 'type' => 'sd_assets']);
    $this->assertDatabaseHas('sd_common_ticket_relation', ['ticket_id' => $ticketId, 'type_id' => $asset->id, 'type' => 'sd_assets']);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['ticket_ids' => $ticketId]);
    $assetName = reset(json_decode($response->content())->data->assets)->name;
    $response->status(200);
    $this->assertCount($limit, json_decode($response->content())->data->assets);
    $this->assertEquals($ticketId, Sdassets::find($asset->id)->tickets()->first()->id);
    $this->assertEquals($asset->name, $assetName);
  }

  /** @group getAssetList */
  public function test_getAssetList_withTicketAndManyAssets()
  {
    $this->getLoggedInUserForWeb('admin');
    $ticketId = factory(Tickets::class)->create()->id;
    $asset = factory(SdAssets::class)->create();
    $assetId = $asset->id;
    SdAssets::create(['name' => 'asset']);
    $this->assertDatabaseHas('sd_assets', ['name' => $asset->name]);
    CommonTicketRelation::create(['ticket_id' => $ticketId, 'type_id' => $assetId, 'type' => 'sd_assets']);
    $this->call('GET', url('service-desk/api/asset-list'), ['ticket_ids' => $ticketId]);
    $this->assertDatabaseHas('sd_common_ticket_relation', ['ticket_id' => $ticketId, 'type_id' => $assetId, 'type' => 'sd_assets']);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['ticket_ids' => $ticketId]);
    $assetName = reset(json_decode($response->content())->data->assets)->name;
    $response->status(200);
    $this->assertCount(1, json_decode($response->content())->data->assets);
    $this->assertEquals($ticketId, Sdassets::find($assetId)->tickets()->first()->id);
    $this->assertEquals($asset->name, $assetName);
  }

  /** @group getAssetList */
  public function test_getAssetList_withAssetId()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create();
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $asset->name]);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['asset_ids' => $asset->id]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->assets);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->assets)->name, $asset->name);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => reset($data->assets)->name]);
  }

  /** @group getAssetList */
  public function test_getAssetList_withWrongAssetId()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['asset_ids' => 'wrong-asset-id']);
    $assets = json_decode($response->content())->data->assets;
    $response->status(200);
    $this->assertCount(0, $assets);
    $this->assertEmpty($assets);
  }

  /** @group getAssetList */
  public function test_getAssetList_withAssetTypeId()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create(['asset_type_id' => 2]);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $asset->name]);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['asset_type_ids' => $asset->asset_type_id]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->assets);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->assets)->name, $asset->name);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => reset($data->assets)->name]);
  }

  /** @group getAssetList */
  public function test_getAssetList_withWrongAssetTypeId()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['asset_type_ids' => 'wrong-asset-type-id']);
    $assets = json_decode($response->content())->data->assets;
    $response->status(200);
    $this->assertCount(0, $assets);
    $this->assertEmpty($assets);
  }

  /** @group getAssetList */
  public function test_getAssetList_withUsedById()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create(['used_by_id' => $this->user->id]);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $asset->name]);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['used_by_ids' => $asset->used_by_id]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->assets);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->assets)->used_by->id, $asset->used_by_id);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'used_by_id' => reset($data->assets)->used_by->id]);
  }

  /** @group getAssetList */
  public function test_getAssetList_withWrongUsedById()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['used_by_ids' => 'wrong-used-by-id']);
    $assets = json_decode($response->content())->data->assets;
    $response->status(200);
    $this->assertCount(0, $assets);
    $this->assertEmpty($assets);
  }

  /** @group getAssetList */
  public function test_getAssetList_withManagedById()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create(['managed_by_id' => $this->user->id]);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $asset->name]);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['managed_by_ids' => $asset->managed_by_id]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->assets);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->assets)->managed_by->id, $asset->managed_by_id);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'managed_by_id' => reset($data->assets)->managed_by->id]);
  }

  /** @group getAssetList */
  public function test_getAssetList_withWrongManagedById()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['managed_by_ids' => 'wrong-managed-by-id']);
    $assets = json_decode($response->content())->data->assets;
    $response->status(200);
    $this->assertCount(0, $assets);
    $this->assertEmpty($assets);
  }

  /** @group getAssetList */
  public function test_getAssetList_withLocationId()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create(['location_id' => 2]);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $asset->name]);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['location_ids' => $asset->location_id]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->assets);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->assets)->name, $asset->name);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => reset($data->assets)->name]);
  }

  /** @group getAssetList */
  public function test_getAssetList_withWrongLocationId()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['lcoation_ids' => 'wrong-location-id']);
    $assets = json_decode($response->content())->data->assets;
    $response->status(200);
    $this->assertCount(0, $assets);
    $this->assertEmpty($assets);
  }

  /** @group getAssetList */
  public function test_getAssetList_withDepartmentId()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create(['department_id' => 2]);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $asset->name]);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['dept_ids' => $asset->department_id]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->assets);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->assets)->name, $asset->name);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => reset($data->assets)->name]);
  }

  /** @group getAssetList */
  public function test_getAssetList_withWrongDepartmentId()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['dept_ids' => 'wrong-department-id']);
    $assets = json_decode($response->content())->data->assets;
    $response->status(200);
    $this->assertCount(0, $assets);
    $this->assertEmpty($assets);
  }

  /** @group getAssetList */
  public function test_getAssetList_withOrganizationId()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create();
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $asset->name]);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['org_ids' => $asset->organization]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->assets);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->assets)->name, $asset->name);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => reset($data->assets)->name]);
  }

  /** @group getAssetList */
  public function test_getAssetList_withWrongOrganizationId()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['org_ids' => 'wrong-organization-id']);
    $assets = json_decode($response->content())->data->assets;
    $response->status(200);
    $this->assertCount(0, $assets);
    $this->assertEmpty($assets);
  }

  /** @group getAssetList */
  public function test_getAssetList_withProductId()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create();
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $asset->name]);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['product_ids' => $asset->product_id]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->assets);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->assets)->name, $asset->name);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => reset($data->assets)->name]);
  }

  /** @group getAssetList */
  public function test_getAssetList_withWrongProductId()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['product_ids' => 'wrong-product-id']);
    $assets = json_decode($response->content())->data->assets;
    $response->status(200);
    $this->assertCount(0, $assets);
    $this->assertEmpty($assets);
  }

  /** @group getAssetList */
  public function test_getAssetList_withImpactTypeId()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create();
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $asset->name]);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['impact_type_ids' => $asset->impact_type_id]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->assets);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->assets)->name, $asset->name);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => reset($data->assets)->name]);
  }

  /** @group getAssetList */
  public function test_getAssetList_withWrongImpactTypeId()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['impact_type_ids' => 'wrong-impact-type-id']);
    $assets = json_decode($response->content())->data->assets;
    $response->status(200);
    $this->assertCount(0, $assets);
    $this->assertEmpty($assets);
  }
  
  /** @group getAssetList */
  public function test_getAssetList_withFilterId_returnsAssetList()
  {
    $this->getLoggedInUserForWeb('agent');
    $assetId = factory(SdAssets::class)->create()->id;
    factory(SdAssets::class,2)->create();
    $response = $this->call('POST', url('service-desk/api/reports/create'), ['name' => 'asset by organization', 'description' => 'asset by organization report', 'type' => 'asset', 'fields' => [['key' => 'asset_ids', 'value' => $assetId, 'value_meta' => 'test']]]);
    $this->assertDatabaseHas('sd_report_filter_metas', ['key' => 'asset_ids', 'value' => serialize($assetId), 'value_meta' => serialize('test')]);
    $response = $this->call('GET', url('service-desk/api/reports'));
    $reportFilters = json_decode($response->content())->data->reports->data;
    $this->assertCount(1, $reportFilters);
    foreach ($reportFilters as $reportFilter) {
      $this->assertDatabaseHas('sd_report_filters', ['name' => $reportFilter->name, 'description' => 'asset by organization report', 'type' => $reportFilter->type]);
    }
    $filterId = SdReportFilter::first()->id;
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['filter_id' => $filterId]);
    $assets = json_decode($response->content())->data->assets;
    $this->assertCount(1, $assets);
    foreach ($assets as $asset) {
      $this->assertDatabaseHas('sd_assets', ['id' => $assetId, 'name' => $asset->name,]);
    }
  }

  /** @group getAssetList */
  public function test_getAssetList_withCount_returnsAssetsCountBasedOnAssetType()
  {
    $this->getLoggedInUserForWeb('admin');
    factory(SdAssets::class)->create(['asset_type_id' => 1]);
    factory(SdAssets::class, 2)->create(['asset_type_id' => 2]);
    factory(SdAssets::class, 3)->create(['asset_type_id' => 3]);
    $assetCountBasedOnAssetTypeFromDb = SdAssets::select('asset_type_id', DB::raw('count(*) as count'))->groupBy('asset_type_id')->orderBy('asset_type_id', 'desc')->get()->toArray();
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['count' => true]);
    $assetCountBasedOnAssetType = json_decode($response->content())->data->assets;
    for ($index = 0; $index < count($assetCountBasedOnAssetType); $index++) {
      $this->assertEquals($assetCountBasedOnAssetTypeFromDb[$index]['asset_type_id'], $assetCountBasedOnAssetType[$index]->id);
      $this->assertEquals($assetCountBasedOnAssetTypeFromDb[$index]['count'], $assetCountBasedOnAssetType[$index]->count);
    }
  }

  /** @group getAssetList */
  public function test_getAssetList_withConfig_returnsAssetsAllFields()
  {
    $this->getLoggedInUserForWeb('admin');
    $assetFromDb = factory(SdAssets::class)->create(['asset_type_id' => 1]);
    $assetsCountFromDb = SdAssets::count();
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['config' => true]);
    $assets = (array) json_decode($response->content())->data->assets;
    $this->assertCount($assetsCountFromDb, $assets);
    foreach ($assets as $asset) {
      $this->assertObjectHasAttribute('identifier',$asset);
      $this->assertObjectHasAttribute('organization',$asset);
      $this->assertObjectHasAttribute('department',$asset);
      $this->assertObjectHasAttribute('asset_type',$asset);
      $this->assertObjectHasAttribute('location',$asset);
      $this->assertObjectHasAttribute('assigned_on',$asset);
      $this->assertObjectHasAttribute('product',$asset);
      $this->assertObjectHasAttribute('used_by',$asset);
      $this->assertObjectHasAttribute('managed_by',$asset);
      $this->assertObjectHasAttribute('impact_type',$asset);
      $this->assertObjectHasAttribute('created_at',$asset);
      $this->assertObjectHasAttribute('updated_at',$asset);
      $this->assertObjectHasAttribute('asset_status',$asset);

      $this->assertDatabaseHas('sd_assets', ['id' => $assetFromDb->id, 'name' => $asset->name, 'description' => $assetFromDb->description]);
    }
  }

  /** @group getAssetList */
  public function test_getAssetList_withColumn_returnsAssetColumnsList()
  {
    $this->getLoggedInUserForWeb('agent');
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['column' => true]);
    $assetColumnList = (array) json_decode($response->content())->data->asset_columns;
    // totally 19 columns are set
    $this->assertCount(20, $assetColumnList);
  }

  /** @group getAssetList */
  public function test_getAssetList_withReportAndFilterId_returnsFormattedAssetReportWithColumnAndData()
  {
    $this->getLoggedInUserForWeb('agent');
    $assetId = factory(SdAssets::class)->create()->id;
    factory(SdAssets::class,2)->create();
    $response = $this->call('POST', url('service-desk/api/reports/create'), ['name' => 'asset filter', 'description' => 'asset by organization report', 'type' => 'asset', 'fields' => [['key' => 'asset_ids', 'value' => $assetId, 'value_meta' => 'test']]]);
    $filterId = SdReportFilter::where('name', 'asset filter')->first()->id;
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['filter_id' => $filterId, 'report' => true]);
    $reportData = json_decode($response->content())->data;
    $this->assertObjectHasAttribute('column_list',$reportData);
    $this->assertObjectHasAttribute('report_data',$reportData);
    $response->assertStatus(200);
  }

  /** @group getAssetList */
  public function test_getAssetList_withChangId_returnsAssetsLinkedToChanges()
  {
    $this->getLoggedInUserForWeb('admin');
    $limit = 1;
    $changeId = factory(SdChanges::class)->create()->id;
    $asset = factory(SdAssets::class)->create();
    CommonAssetRelation::create(['asset_id' => $asset->id, 'type_id' => $changeId, 'type' => 'sd_changes']);
    $this->assertDatabaseHas('sd_common_asset_relation', ['asset_id' => $asset->id, 'type_id' => $changeId, 'type' => 'sd_changes']);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['change_ids' => $changeId]);
    $assetName = reset(json_decode($response->content())->data->assets)->name;
    $response->status(200);
    $this->assertCount($limit, json_decode($response->content())->data->assets);
    $this->assertEquals($changeId, SdAssets::find($asset->id)->changes()->first()->id);
    $this->assertEquals($asset->name, $assetName);
  }

  /** @group getAssetList */
  public function test_getAssetList_withSearchQueryChangeSubject_returnsAssetsLinkedToChangesBasedOnChangeSubject()
  {
    $this->getLoggedInUserForWeb('admin');
    $change = factory(SdChanges::class)->create();
    $this->assertDatabaseHas('sd_changes', ['id' => $change->id]);
    $asset = factory(SdAssets::class)->create(['location_id' => 2]);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $asset->name]);
    CommonAssetRelation::create(['asset_id' => $asset->id, 'type_id' => $change->id, 'type' => 'sd_changes']);
    $this->assertDatabaseHas('sd_common_asset_relation', ['asset_id' => $asset->id, 'type_id' => $change->id, 'type' => 'sd_changes']);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['search-query' => $change->subject]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->assets);
    $this->assertArrayHasKey('current_page', (array) $data);
    $this->assertEquals(reset($data->assets)->name, $asset->name);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => reset($data->assets)->name]);
  }

  /** @group getAssetList */
  public function test_getAssetList_withProblemId_returnsAssetsLinkedToProblemBasedOnProblemId()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create(['location_id' => 2]);
    factory(SdAssets::class,5)->create();
    $problem = factory(SdProblem::class)->create();
    CommonAssetRelation::create(['asset_id' => $asset->id, 'type_id' => $problem->id, 'type' => 'sd_problem']);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['problem_ids' => $problem->id]);
    $assets = json_decode($response->content())->data->assets;
    $response->assertStatus(200);
    $this->assertCount(1, $assets);
  }

  /** @group getAssetList */
  public function test_getAssetList_withWrongProblemId_returnsEmptyAssetsList()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create(['location_id' => 2]);
    factory(SdAssets::class,5)->create();
    $problem = factory(SdProblem::class)->create();
    CommonAssetRelation::create(['asset_id' => $asset->id, 'type_id' => $problem->id, 'type' => 'sd_problem']);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['problem_ids' => 'gvh-wbh']);
    $assets = json_decode($response->content())->data->assets;
    $response->assertStatus(200);
    $this->assertCount(0, $assets);
  }

  /** @group getAssetList */
  public function test_getNotLinkedAssetList_withProblemId_returnsAssetsNotLinkedToProblemBasedOnProblemId()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create(['location_id' => 2]);
    factory(SdAssets::class,5)->create();
    $problem = factory(SdProblem::class)->create();
    CommonAssetRelation::create(['asset_id' => $asset->id, 'type_id' => $problem->id, 'type' => 'sd_problem']);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['problem_id' => $problem->id]);
    $assets = json_decode($response->content())->data->assets;
    $response->assertStatus(200);
    $this->assertCount(5, $assets);
  }

  /** @group getAssetList */
  public function test_getNotLinkedAssetList_withProblemId_returnsCompleteAssetList()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create(['location_id' => 2]);
    factory(SdAssets::class,5)->create();
    $problem = factory(SdProblem::class)->create();
    CommonAssetRelation::create(['asset_id' => $asset->id, 'type_id' => $problem->id, 'type' => 'sd_problem']);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['problem_id' => 'hmfuyey']);
    $assets = json_decode($response->content())->data->assets;
    $response->assertStatus(200);
    $this->assertCount(6, $assets);
  }

  /** @group getAssetList */
  public function test_getAssetList_withReleaseId_returnsAssetsLinkedToReleaseBasedOnReleaseId()
  {
    $this->getLoggedInUserForWeb('agent');
    $asset = factory(SdAssets::class)->create();
    factory(SdAssets::class,5)->create();
    $release = factory(SdReleases::class)->create();
    $asset->releases()->sync([$release->id => ['type' => 'sd_releases']]);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['release_ids' => $release->id]);
    $assets = json_decode($response->content())->data->assets;
    $response->assertStatus(200);
    $this->assertCount(1, $assets);
  }

  public function test_getAssetList_withReleaseId_returnsAssetsListLinkedToReleaseBasedOnReleaseId()
  {
      $this->getLoggedInUserForWeb('admin');
      $asset = factory(SdAssets::class)->create();
      factory(SdAssets::class,5)->create();
      $release = factory(SdReleases::class)->create();
      CommonAssetRelation::create(['asset_id' => $asset->id, 'type_id' => $release->id, 'type' => 'sd_releases']);
      $response = $this->call('GET', url('service-desk/api/asset-list'), ['release_ids' => $release->id]);
      $assets = json_decode($response->content())->data->assets;
      $response->assertStatus(200);
      $this->assertCount(1, $assets);

  }

  /** @group getAssetList */
  public function test_getAssetList_withWrongReleaseId_returnsEmptyAssetsList()
  {
    $this->getLoggedInUserForWeb('agent');
    $asset = factory(SdAssets::class)->create();
    factory(SdAssets::class,5)->create();
    $release = factory(SdReleases::class)->create();
    $asset->releases()->sync([$release->id => ['type' => 'sd_releases']]);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['release_ids' => 'wrong-release-id']);
    $assets = json_decode($response->content())->data->assets;
    $response->assertStatus(200);
    $this->assertCount(0, $assets);
  }

  /* @group getAssetList */
  public function test_getAssetList_withAssetAssignedOnTimeRange_returnsAssetList()
  {
    $this->getLoggedInUserForWeb('agent');
    factory(SdAssets::class)->create(['assigned_on' => '2020-07-12 05:12:00']);
    factory(SdAssets::class)->create(['assigned_on' => '2020-07-14 05:12:00']);
    factory(SdAssets::class)->create(['assigned_on' => '2020-07-16 05:12:00']);
    factory(SdAssets::class)->create(['assigned_on' => '2020-07-18 05:12:00']);
    $endTimestamp = '2020-07-16 05:12:00';
    $startTimestamp = '2020-07-12 05:12:00';
    $startTime = changeTimezoneForDatetime($startTimestamp, agentTimeZone(), 'UTC');
    $endTime = changeTimezoneForDatetime($endTimestamp, agentTimeZone(), 'UTC');
    $timeRange = "date::{$startTimestamp}~{$endTimestamp}";
    $formattedRange = $this->getTimeRangeObject($timeRange, "UTC");
    $initialCount = SdAssets::where([['assigned_on', '<=', $endTime],['assigned_on', '>=', $startTime]])->count();
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['assigned_on' => $timeRange]);
    $assets = json_decode($response->content())->data->assets;
    $response->status(200);
    $this->assertCount($initialCount, $assets);
    foreach ($assets as $asset) {
      $assetAssignedOn = SdAssets::find($asset->id)->assigned_on;
      $this->assertGreaterThanOrEqual($startTime, $assetAssignedOn);
      $this->assertLessThanOrEqual($endTime, $assetAssignedOn);
      $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $asset->name]);
    }
  }

  /** @group getAssetList */
  public function test_getAssetList_withContractId_returnsAssetsLinkedToContractBasedOnContractId()
  {
    $this->getLoggedInUserForWeb('agent');
    $asset = factory(SdAssets::class)->create();
    factory(SdAssets::class,5)->create();
    $contract = factory(SdContract::class)->create();
    $contract->attachAsset()->sync([$asset->id => ['type' => 'sd_contracts']]);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['contract_ids' => $contract->id]);
    $assets = json_decode($response->content())->data->assets;
    $response->assertStatus(200);
    $this->assertCount(1, $assets);
  }

  /** @group getAssetList */
  public function test_getAssetList_withWrongContractId_returnsEmptyAssetsList()
  {
    $this->getLoggedInUserForWeb('agent');
    $asset = factory(SdAssets::class)->create();
    factory(SdAssets::class,5)->create();
    $contract = factory(SdContract::class)->create();
    $contract->attachAsset()->sync([$asset->id => ['type' => 'sd_contracts']]);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['contract_ids' => 'wrong-contract-id']);
    $assets = json_decode($response->content())->data->assets;
    $response->assertStatus(200);
    $this->assertCount(0, $assets);
  }

  /** @group getAssetList */
  public function test_getAssetList_withCustomField_returnsAssetsLinkedToCustomFieldBasedOnCustomFieldValue()
  {
    $this->getLoggedInUserForWeb('agent');
    $asset1 = factory(SdAssets::class)->create();
    $asset2 = factory(SdAssets::class)->create();
    factory(SdAssets::class,5)->create();
    $customFieldId = SdAssets::find($asset1->id)->customFieldValues()->create(['form_field_id' => 1, 'value'=>'testValue1'])->form_field_id;
    SdAssets::find($asset1->id)->customFieldValues()->create(['form_field_id' => 2, 'value'=>'testValue2']);
    SdAssets::find($asset2->id)->customFieldValues()->create(['form_field_id' => 3, 'value'=>'testValue3']);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ["custom_$customFieldId" => 'testValue1']);
    $assets = json_decode($response->content())->data->assets;
    $response->assertStatus(200);
    $this->assertCount(1, $assets);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset1->id, 'name' => $asset1->name]);
  }

  /** @group getAssetList */
  public function test_getAssetList_withWrongCustomFieldValue_returnsEmptyAssetList()
  {
    $this->getLoggedInUserForWeb('agent');
    $asset1 = factory(SdAssets::class)->create();
    $asset2 = factory(SdAssets::class)->create();
    factory(SdAssets::class,5)->create();
    $customFieldId = SdAssets::find($asset1->id)->customFieldValues()->create(['form_field_id' => 1, 'value'=>'testValue1'])->form_field_id;
    SdAssets::find($asset2->id)->customFieldValues()->create(['form_field_id' => 3, 'value'=>'testValue3']);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ["custom_$customFieldId" => 'testValue4']);
    $assets = json_decode($response->content())->data->assets;
    $response->assertStatus(200);
    $this->assertEmpty($assets);
  }

  /** @group getAssetList */
  public function test_getAssetList_withWrongCustomFieldId_returnsEmptyAssetList()
  {
    $this->getLoggedInUserForWeb('agent');
    $asset1 = factory(SdAssets::class)->create();
    $asset2 = factory(SdAssets::class)->create();
    factory(SdAssets::class,5)->create();
    $customFieldId = SdAssets::find($asset1->id)->customFieldValues()->create(['form_field_id' => 1, 'value'=>'testValue1'])->form_field_id;
    SdAssets::find($asset2->id)->customFieldValues()->create(['form_field_id' => 3, 'value'=>'testValue3']);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ["custom_wrongid" => 'testValue1']);
    $assets = json_decode($response->content())->data->assets;
    $response->assertStatus(200);
    $this->assertEmpty($assets);
  }

  /** @group getAssetList */
  public function test_getNotLinkedAssetList_withReleaseId_returnsAssetsListNotLinkedToReleaseBasedOnReleaseId()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create();
    factory(SdAssets::class,5)->create();
    $release = factory(SdReleases::class)->create();
    CommonAssetRelation::create(['asset_id' => $asset->id, 'type_id' => $release->id, 'type' => 'sd_releases']);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['release_id' => $release->id]);
    $assets = json_decode($response->content())->data->assets;
    $response->assertStatus(200);
    $this->assertCount(5, $assets);
  }

  /** @group getAssetList */
  public function test_getNotLinkedAssetList_withWrongReleaseId_returnsCompleteAssetsList()
  {
    $this->getLoggedInUserForWeb('admin');
    $asset = factory(SdAssets::class)->create();
    factory(SdAssets::class,5)->create();
    $release = factory(SdReleases::class)->create();
    CommonAssetRelation::create(['asset_id' => $asset->id, 'type_id' => $release->id, 'type' => 'sd_releases']);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['release_id' => 'wrong-release-id']);
    $assets = json_decode($response->content())->data->assets;
    $response->assertStatus(200);
    $this->assertCount(6, $assets);
  }

  /** @group getAssetList */
  public function test_getAssetList_withSearchQueryAssetStatusNameMissing_returnsAssetListBasedOnMissingStatus()
  {
    $this->getLoggedInUserForWeb('admin');
    // Missing Status, status id is 2
    $statusId = 2;
    $asset = factory(SdAssets::class)->create(['asset_type_id' => 2, 'status_id' => $statusId]);
    factory(SdAssets::class)->create();
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $asset->name]);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['search-query' => 'Missing']);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->assets);
    $this->assertArrayHasKey('current_page', (array) $data);
    $assetFromResponse = reset($data->assets);
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $assetFromResponse->name, 'status_id' => $asset->status_id]);
  }

  /** @group getAssetList */
  public function test_getAssetList_withAssetStatusId_returnsAssetListBasedOnAssetStatusId()
  {
    $this->getLoggedInUserForWeb('admin');
    // Missing Status, status id is 2
    $statusId = 2;
    $asset = factory(SdAssets::class)->create(['asset_type_id' => 2, 'status_id' => $statusId]);
    factory(SdAssets::class)->create();
    $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $asset->name]);
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['asset_status_ids' => $statusId]);
    $data = json_decode($response->content())->data;
    $response->status(200);
    $this->assertCount(1, $data->assets);
    $this->assertArrayHasKey('current_page', (array) $data);
    $assetFromResponse = reset($data->assets);
    $this->assertDatabaseHas('sd_assets', ['id' => $assetFromResponse->id, 'name' => $assetFromResponse->name, 'status_id' => $asset->status_id]);
  }

  /** @group getAssetList */
  public function test_getAssetList_withWrongAssetStatusId_returnEmptyAssetList()
  {
    $this->getLoggedInUserForWeb('admin');
    $response = $this->call('GET', url('service-desk/api/asset-list'), ['asset_status_ids' => 'wrong-asset-status-id']);
    $assets = json_decode($response->content())->data->assets;
    $response->status(200);
    $this->assertCount(0, $assets);
    $this->assertEmpty($assets);
  }
  
  public function test_getNotLinkedAssetList_withContractId_returnsAssetsListNotLinkedToContractBasedOnContractId()
  {
      $this->getLoggedInUserForWeb('admin');
      $asset = factory(SdAssets::class)->create();
      factory(SdAssets::class,5)->create();
      $contract = factory(SdContract::class)->create();
      $contract->attachAsset()->sync([$asset->id => ['type' => 'sd_contracts']]);
      $response = $this->call('GET', url('service-desk/api/asset-list'), ['contract_id' => $contract->id]);
      $assets = json_decode($response->content())->data->assets;
      $response->assertStatus(200);
      $this->assertCount(5, $assets);
  }

  /** @group getAssetList */
  public function test_getNotLinkedAssetList_withWrongContractId_returnsCompleteAssetsList()
  {
      $this->getLoggedInUserForWeb('admin');
      $asset = factory(SdAssets::class)->create();
      factory(SdAssets::class,5)->create();
      $contract = factory(SdContract::class)->create();
      $contract->attachAsset()->sync([$asset->id => ['type' => 'sd_contracts']]);
      $response = $this->call('GET', url('service-desk/api/asset-list'), ['contract_id' => 'wrong-contract-id']);
      $assets = json_decode($response->content())->data->assets;
      $response->assertStatus(200);
      $this->assertCount(6, $assets);
  }

}
