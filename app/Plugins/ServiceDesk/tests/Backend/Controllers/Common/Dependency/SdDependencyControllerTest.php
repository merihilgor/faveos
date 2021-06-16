<?php

namespace App\Plugins\ServiceDesk\Tests\Unit\Backend\Common\Dependency;

use Tests\AddOnTestCase;
use App\Plugins\ServiceDesk\Model\Assets\SdImpactypes as Impact;
use App\Plugins\ServiceDesk\Model\Assets\SdAssettypes;
use App\Plugins\ServiceDesk\Model\Products\SdProducts;
use App\Plugins\ServiceDesk\Model\Products\SdProductstatus;
use App\Plugins\ServiceDesk\Model\Products\SdProductprocmode;
use App\Plugins\ServiceDesk\Model\Procurment\SdProcurment;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\Plugins\ServiceDesk\Model\Releases\SdReleasestatus;
use App\Plugins\ServiceDesk\Model\Releases\SdReleasepriorities;
use App\Plugins\ServiceDesk\Model\Releases\SdReleasetypes;
use App\Plugins\ServiceDesk\Model\Common\ProductVendorRelation;
use App\Plugins\ServiceDesk\Model\Changes\SdChangetypes;
use App\Plugins\ServiceDesk\Model\Changes\SdChangestatus;
use App\Plugins\ServiceDesk\Model\Changes\SdChangepriorities;
use App\Plugins\ServiceDesk\Model\Releases\SdReleases;
use App\Plugins\ServiceDesk\Model\Changes\SdChanges;
use App\Plugins\ServiceDesk\Model\Problem\SdProblem;
use App\Plugins\ServiceDesk\Model\Contract\License;
use App\Plugins\ServiceDesk\Model\Vendor\SdVendors;
use App\Model\helpdesk\Workflow\ApprovalWorkflow as Cab;
use App\Model\helpdesk\Ticket\Tickets as Ticket;
use App\Plugins\ServiceDesk\Model\Common\Ticket as SdTicket;
use App\Plugins\ServiceDesk\Model\Contract\SdContractStatus;
use App\Plugins\ServiceDesk\Model\Contract\SdContract;
use App\Plugins\ServiceDesk\Model\Contract\ContractType;


class SdDependencyControllerTest extends AddOnTestCase
{
    const DEPENDENCY_URL = 'service-desk/api/dependency/';

    /** @group impacts */
    public function test_impacts_withoutSearchString()
    {
        $this->getLoggedInUserForWeb('agent');
        $response = $this->call('GET', self::DEPENDENCY_URL . 'impacts');
        $impacts = json_decode($response->content())->data->impacts;
        $response->assertStatus(200);
        $this->assertDatabaseHas('sd_impact_types', (array) reset($impacts));
        $this->assertCount(Impact::count(), $impacts);
    }

    /** @group impacts */
    public function test_impacts_withSearchString()
    {
        $this->getLoggedInUserForWeb('agent');
        $response = $this->call('GET', self::DEPENDENCY_URL . 'impacts',['search-query'=>'low']);
        $response->assertStatus(200);
        $impacts = json_decode($response->content())->data->impacts;
        $this->assertDatabaseHas('sd_impact_types', (array) reset($impacts));
        $this->assertCount(1, $impacts);
    }

    /** @group impacts */
    public function test_impacts_withConfig()
    {
        $this->getLoggedInUserForWeb('admin');
        $response = $this->call('GET', self::DEPENDENCY_URL . 'impacts',['config'=>true]);
        $response->assertStatus(200);
        $impacts = json_decode($response->content())->data->impacts;
        $this->assertDatabaseHas('sd_impact_types', (array) reset($impacts));
        $this->assertTrue(array_key_exists('id', (array) reset($impacts)));
        $this->assertTrue(array_key_exists('name', (array) reset($impacts)));
    }

    /** @group assetTypes */
    public function test_assetTypes_withoutSearchString()
    {
        $this->getLoggedInUserForWeb('agent');
        $response = $this->call('GET', self::DEPENDENCY_URL . 'asset_types');
        $assetTypes = json_decode($response->content())->data->asset_types;
        $response->assertStatus(200);
        $this->assertDatabaseHas('sd_asset_types', (array) reset($assetTypes));
        // default value of limit is 10
        $this->assertCount(10, $assetTypes);
    }

    /** @group assetTypes */
    public function test_assetTypes_withMeta()
    {
        $this->getLoggedInUserForWeb('agent');
        $response = $this->call('GET', self::DEPENDENCY_URL . 'asset_types', ['meta' => 'true']);
        $assetTypes = json_decode($response->content())->data->asset_types;
        $response->assertStatus(200);
        $assetType = reset($assetTypes);
        $this->assertDatabaseHas('sd_asset_types', ['id' => $assetType->id, 'name' => $assetType->name]);
        $this->assertCount(SdAssettypes::count(), $assetTypes);
    }

    /** @group assetTypes */
    public function test_assetTypes_withSearchString()
    {
        $this->getLoggedInUserForWeb('agent');
        $response = $this->call('GET', self::DEPENDENCY_URL . 'asset_types',['search-query'=>'hardware']);
        $response->assertStatus(200);
        $assetTypes = json_decode($response->content())->data->asset_types;
        $this->assertDatabaseHas('sd_asset_types', (array) reset($assetTypes));
        $this->assertCount(1, $assetTypes);
    }

    /** @group assetTypes */
    public function test_assetTypes_withConfig()
    {
        $this->getLoggedInUserForWeb('admin');
        $response = $this->call('GET', self::DEPENDENCY_URL . 'asset_types',['config'=>true]);
        $response->assertStatus(200);
        $assetTypes = json_decode($response->content())->data->asset_types;
        $this->assertDatabaseHas('sd_asset_types', (array) reset($assetTypes));
        $this->assertTrue(array_key_exists('id', (array) reset($assetTypes)));
        $this->assertTrue(array_key_exists('name', (array) reset($assetTypes)));
    }

    /** @group products */
    public function test_products_withoutSearchString()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdProducts::class, 5)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'products');
        $products = json_decode($response->content())->data->products;
        $response->assertStatus(200);
        $this->assertDatabaseHas('sd_products', (array) reset($products));
        // default value of limit is 10
        $this->assertCount(5, $products);
    }

    /** @group products */
    public function test_products_withMeta()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdProducts::class, 5)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'products', ['meta' => 'true']);
        $products = json_decode($response->content())->data->products;
        $response->assertStatus(200);
        $this->assertDatabaseHas('sd_products', (array) reset($products));
        $this->assertCount(SdProducts::count(), $products);
    }

    /** @group products */
    public function test_products_withSearchString()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdProducts::class)->create();
        factory(SdProducts::class)->create(['name' => 'chair']);
        $response = $this->call('GET', self::DEPENDENCY_URL . 'products',['search-query'=>'chair']);
        $response->assertStatus(200);
        $products = json_decode($response->content())->data->products;
        $this->assertDatabaseHas('sd_products', (array) reset($products));
        $this->assertCount(1, $products);
    }

    /** @group products */
    public function test_products_withConfig()
    {
        $this->getLoggedInUserForWeb('admin');
        factory(SdProducts::class, 5)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'products',['config'=>true]);
        $response->assertStatus(200);
        $products = json_decode($response->content())->data->products;
        $this->assertDatabaseHas('sd_products', (array) reset($products));
        $this->assertTrue(array_key_exists('id', (array) reset($products)));
        $this->assertTrue(array_key_exists('name', (array) reset($products)));
    }

    /** @group vendors */
    public function test_vendors_withoutSearchString()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdVendors::class, 5)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'vendors');
        $vendors = json_decode($response->content())->data->vendors;
        $response->assertStatus(200);
        $this->assertDatabaseHas('sd_vendors', (array) reset($vendors));
        // default value of limit is 10
        $this->assertCount(5, $vendors);
    }

    /** @group vendors */
    public function test_vendors_withMeta()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdVendors::class, 5)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'vendors', ['meta' => 'true']);
        $vendors = json_decode($response->content())->data->vendors;
        $response->assertStatus(200);
        $this->assertDatabaseHas('sd_vendors', (array) reset($vendors));
        $this->assertCount(SdVendors::count(), $vendors);
    }

    /** @group vendors */
    public function test_vendors_withSearchString()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdVendors::class)->create();
        factory(SdVendors::class)->create(['name' => 'chair']);
        $response = $this->call('GET', self::DEPENDENCY_URL . 'vendors',['search-query'=>'chair']);
        $response->assertStatus(200);
        $vendors = json_decode($response->content())->data->vendors;
        $this->assertDatabaseHas('sd_vendors', (array) reset($vendors));
        $this->assertCount(1, $vendors);
    }

    /** @group vendors */
    public function test_vendors_withConfig()
    {
        $this->getLoggedInUserForWeb('admin');
        factory(SdVendors::class, 5)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'vendors',['config'=>true]);
        $response->assertStatus(200);
        $vendors = json_decode($response->content())->data->vendors;
        $this->assertDatabaseHas('sd_vendors', (array) reset($vendors));
        $this->assertTrue(array_key_exists('id', (array) reset($vendors)));
        $this->assertTrue(array_key_exists('name', (array) reset($vendors)));
    }

    /** @group assets */
    public function test_assets_withoutSearchString_returnsAssetListWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdAssets::class, 5)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'assets');
        $assets = json_decode($response->content())->data->assets;
        $response->assertStatus(200);
        foreach ($assets as $asset) {
            // only id and name comes in asset list by default
            $this->assertTrue(count((array) $asset) == 2);
            [$identifier, $name] = explode(" ", $asset->name);
            $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $name]);
        }
        // default value of limit is 10
        $this->assertCount(5, $assets);
    }

    /** @group assets */
    public function test_assets_withMeta_returnsAssetListWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdAssets::class, 5)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'assets', ['meta' => 'true']);
        $assets = json_decode($response->content())->data->assets;
        $response->assertStatus(200);
        foreach ($assets as $asset) {
            // only id and name comes in asset list by default
            $this->assertTrue(count((array) $asset) == 2);
            [$identifier, $name] = explode(" ", $asset->name);
            $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $name, 'identifier' => $identifier]);
        }
        $this->assertCount(SdAssets::count(), $assets);
    }

    /** @group assets */
    public function test_assets_withSearchString_returnsAssetListWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdAssets::class)->create();
        factory(SdAssets::class)->create(['name' => 'chair']);
        $response = $this->call('GET', self::DEPENDENCY_URL . 'assets',['search-query'=>'chair']);
        $response->assertStatus(200);
        $assets = json_decode($response->content())->data->assets;
        foreach ($assets as $asset) {
            // only id and name comes in asset list by default
            $this->assertTrue(count((array) $asset) == 2);
            [$identifier, $name] = explode(" ", $asset->name);
            $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $name, 'identifier' => $identifier]);
        }
        $this->assertCount(1, $assets);
    }

     /** @group assets */
    public function test_assets_withWrongSearchString_returnsEmptyAssetList()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdAssets::class)->create(['name' => 'chair']);
        $response = $this->call('GET', self::DEPENDENCY_URL . 'assets',['search-query'=>'wrong-asset-name']);
        $response->assertStatus(200);
        $assets = json_decode($response->content())->data->assets;
        $this->assertEmpty($assets);
    }

    /** @group assets */
    public function test_assets_withConfig_returnsAssetListWithIdAndName()
    {
        $this->getLoggedInUserForWeb('admin');
        factory(SdAssets::class, 5)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'assets',['config'=>true]);
        $response->assertStatus(200);
        $assets = json_decode($response->content())->data->assets;
        foreach ($assets as $asset) {
            // totally 16 asset field exists
            $this->assertTrue(count((array) $asset) > 2);
            [$identifier, $name] = explode(" ", $asset->name);
            $this->assertDatabaseHas('sd_assets', ['id' => $asset->id, 'name' => $name, 'identifier' => $identifier]);
        }
    }

     /** @group releaseTypes */
    public function test_releaseTypes_withoutSearchString_returnsTenReleaseTypesWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdReleasetypes::class, 15)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'release_types');
        $releaseTypes = json_decode($response->content())->data->release_types;
        $response->assertStatus(200);
        foreach ($releaseTypes as $releaseType) {
            $this->assertDatabaseHas('sd_release_types', ['id' => $releaseType->id, 'name' => $releaseType->name]);
        }
        // default value of limit is 10
        $this->assertCount(10, $releaseTypes);
    }   

    /** @group releaseTypes */
    public function test_releaseTypes_withMeta_returnsAllReleaseTypesWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdReleasetypes::class, 15)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'release_types', ['meta' => 'true']);
        $releaseTypes = json_decode($response->content())->data->release_types;
        $response->assertStatus(200);
        foreach ($releaseTypes as $releaseType) {
            $this->assertDatabaseHas('sd_release_types', ['id' => $releaseType->id, 'name' => $releaseType->name]);
        }
        // default value of limit is 10
        $this->assertCount(SdReleasetypes::count(), $releaseTypes);
    }

    /** @group releaseTypes */
    public function test_releaseTypes_withSearchString_returnsReleaseTypesWithIdAndNameBasedOnString()
    {
        $this->getLoggedInUserForWeb('agent');
        $releaseTypeInDb = factory(SdReleasetypes::class)->create(['name' => 'spam']);
        factory(SdReleasetypes::class)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'release_types', ['search-query' => $releaseTypeInDb->name]);
        $releaseTypes = json_decode($response->content())->data->release_types;
        $response->assertStatus(200);
        $this->assertCount(1, $releaseTypes);
        $releaseType = reset($releaseTypes);
        $this->assertDatabaseHas('sd_release_types', ['id' => $releaseType->id, 'name' => $releaseTypeInDb->name]);
    }

    /** @group releaseTypes */
    public function test_releaseTypes_withConfigAndWithLimit_returnsReleaseTypesWithAllFields()
    {
        $this->getLoggedInUserForWeb('admin');
        factory(SdReleasetypes::class, 12)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'release_types',['config'=>true, 'limit' => 11]);
        $response->assertStatus(200);
        $releaseTypes = json_decode($response->content())->data->release_types;
        // default value of limit is 10 but 11 entries are coming becuase limit is set to 11
        $this->assertCount(11, $releaseTypes);
        foreach ($releaseTypes as $releaseType) {
            $this->assertDatabaseHas('sd_release_types', ['id' => $releaseType->id, 'name' => $releaseType->name]);
        }
        $this->assertTrue(array_key_exists('id', (array) reset($releaseTypes)));
        $this->assertTrue(array_key_exists('name', (array) reset($releaseTypes)));
    }

    /** @group releaseStatuses */
    public function test_releaseStatuses_withoutSearchString_returnsTenreleaseStatusesWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdReleaseStatus::class, 15)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'release_statuses');
        $releaseStatuses = json_decode($response->content())->data->release_statuses;
        $response->assertStatus(200);
        foreach ($releaseStatuses as $releaseStatus) {
            $this->assertDatabaseHas('sd_release_status', ['id' => $releaseStatus->id, 'name' => $releaseStatus->name]);
        }
        // default value of limit is 10
        $this->assertCount(10, $releaseStatuses);
    }

    /** @group releaseStatuses */
    public function test_releaseStatuses_withMeta_returnsAllreleaseStatusesWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdReleaseStatus::class, 15)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'release_statuses', ['meta' => 'true']);
        $releaseStatuses = json_decode($response->content())->data->release_statuses;
        $response->assertStatus(200);
        foreach ($releaseStatuses as $releaseStatus) {
            $this->assertDatabaseHas('sd_release_status', ['id' => $releaseStatus->id, 'name' => $releaseStatus->name]);
        }
        // default value of limit is 10
        $this->assertCount(SdReleaseStatus::count(), $releaseStatuses);
    }

    /** @group releaseStatuses */
    public function test_releaseStatuses_withSearchString_returnsReleaseStatusesWithIdAndNameBasedOnString()
    {
        $this->getLoggedInUserForWeb('agent');
        $releaseStatusInDb = factory(SdReleaseStatus::class)->create(['name' => 'spam']);
        factory(SdReleaseStatus::class)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'release_statuses', ['search-query' => $releaseStatusInDb->name]);
        $releaseStatuses = json_decode($response->content())->data->release_statuses;
        $response->assertStatus(200);
        $this->assertCount(1, $releaseStatuses);
        $releaseStatus = reset($releaseStatuses);
        $this->assertDatabaseHas('sd_release_status', ['id' => $releaseStatus->id, 'name' => $releaseStatusInDb->name]);
    }

    /** @group releaseStatuses */
    public function test_releaseStatuses_withConfigAndWithLimit_returnsReleaseStatusesWithAllFields()
    {
        $this->getLoggedInUserForWeb('admin');
        factory(SdReleaseStatus::class, 12)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'release_statuses',['config'=>true, 'limit' => 11]);
        $response->assertStatus(200);
        $releaseStatuses = json_decode($response->content())->data->release_statuses;
        // default value of limit is 10 but 11 entries are coming becuase limit is set to 11
        $this->assertCount(11, $releaseStatuses);
        foreach ($releaseStatuses as $releaseStatus) {
            $this->assertDatabaseHas('sd_release_status', ['id' => $releaseStatus->id, 'name' => $releaseStatus->name]);
        }
        $this->assertTrue(array_key_exists('id', (array) reset($releaseStatuses)));
        $this->assertTrue(array_key_exists('name', (array) reset($releaseStatuses)));
    }

    /** @group releasePriorities */
    public function test_releasePriorities_withoutSearchString_returnsTenReleasePrioritiesWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdReleasepriorities::class, 15)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'release_priorities');
        $releasePriorities = json_decode($response->content())->data->release_priorities;
        $response->assertStatus(200);
        foreach ($releasePriorities as $releasePriority) {
            $this->assertDatabaseHas('sd_release_priorities', ['id' => $releasePriority->id, 'name' => $releasePriority->name]);
        }
        // default value of limit is 10
        $this->assertCount(10, $releasePriorities);
    }

    /** @group releasePriorities */
    public function test_releasePriorities_withMeta_returnsAllReleasePrioritiesWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdReleasepriorities::class, 15)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'release_priorities', ['meta' => 'true']);
        $releasePriorities = json_decode($response->content())->data->release_priorities;
        $response->assertStatus(200);
        foreach ($releasePriorities as $releasePriority) {
            $this->assertDatabaseHas('sd_release_priorities', ['id' => $releasePriority->id, 'name' => $releasePriority->name]);
        }
        // default value of limit is 10
        $this->assertCount(SdReleasepriorities::count(), $releasePriorities);
    }

    /** @group releasePriorities */
    public function test_releasePriorities_withSearchString_returnsReleasePrioritiesWithIdAndNameBasedOnString()
    {
        $this->getLoggedInUserForWeb('agent');
        $releasePriorityInDb = factory(SdReleasepriorities::class)->create(['name' => 'spam']);
        factory(SdReleasepriorities::class)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'release_priorities', ['search-query' => $releasePriorityInDb->name]);
        $releasePriorities = json_decode($response->content())->data->release_priorities;
        $response->assertStatus(200);
        $this->assertCount(1, $releasePriorities);
        $releasePriority = reset($releasePriorities);
        $this->assertDatabaseHas('sd_release_priorities', ['id' => $releasePriority->id, 'name' => $releasePriorityInDb->name]);
    }

    /** @group releasePriorities */
    public function test_releasePriorities_withConfigAndWithLimit_returnsReleasePrioritiesWithAllFields()
    {
        $this->getLoggedInUserForWeb('admin');
        factory(SdReleasepriorities::class, 12)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'release_priorities',['config'=>true, 'limit' => 11]);
        $response->assertStatus(200);
        $releasePriorities = json_decode($response->content())->data->release_priorities;
        // default value of limit is 10 but 11 entries are coming becuase limit is set to 11
        $this->assertCount(11, $releasePriorities);
        foreach ($releasePriorities as $releasePriority) {
            $this->assertDatabaseHas('sd_release_priorities', ['id' => $releasePriority->id, 'name' => $releasePriority->name]);
        }
        $this->assertTrue(array_key_exists('id', (array) reset($releasePriorities)));
        $this->assertTrue(array_key_exists('name', (array) reset($releasePriorities)));
    }

    /** @group changeTypes */
    public function test_changeTypes_withoutSearchString_returnsTenChangeTypesWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdChangetypes::class, 15)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'change_types');
        $changeTypes = json_decode($response->content())->data->change_types;
        $response->assertStatus(200);
        foreach ($changeTypes as $changeType) {
            $this->assertDatabaseHas('sd_change_types', ['id' => $changeType->id, 'name' => $changeType->name]);
        }
        // default value of limit is 10
        $this->assertCount(10, $changeTypes);
    }

    /** @group changeTypes */
    public function test_changeTypes_withMeta_returnsAllChangeTypesWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdChangetypes::class, 15)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'change_types', ['meta' => 'true']);
        $changeTypes = json_decode($response->content())->data->change_types;
        $response->assertStatus(200);
        foreach ($changeTypes as $changeType) {
            $this->assertDatabaseHas('sd_change_types', ['id' => $changeType->id, 'name' => $changeType->name]);
        }
        // default value of limit is 10
        $this->assertCount(SdChangetypes::count(), $changeTypes);
    }

     /** @group changeTypes */
    public function test_changeTypes_withSearchString_returnsChangeTypesWithIdAndNameBasedOnString()
    {
        $this->getLoggedInUserForWeb('agent');
        $changeTypeInDb = factory(SdChangetypes::class)->create(['name' => 'laptop']);
        factory(SdChangetypes::class)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'change_types', ['search-query' => $changeTypeInDb->name]);
        $changeTypes = json_decode($response->content())->data->change_types;
        $response->assertStatus(200);
        $this->assertCount(1, $changeTypes);
        $changeType = reset($changeTypes);
        $this->assertDatabaseHas('sd_change_types', ['id' => $changeType->id, 'name' => $changeTypeInDb->name]);
    }

    /** @group changeTypes */
    public function test_changeTypes_withConfigAndWithLimit_returnsChangeTypesWithAllFields()
    {
        $this->getLoggedInUserForWeb('admin');
        factory(SdChangetypes::class, 12)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'change_types',['config'=>true, 'limit' => 11]);
        $response->assertStatus(200);
        $changeTypes = json_decode($response->content())->data->change_types;
        // default value of limit is 10 but 11 entries are coming becuase limit is set to 11
        $this->assertCount(11, $changeTypes);
        foreach ($changeTypes as $changeType) {
            $this->assertDatabaseHas('sd_change_types', ['id' => $changeType->id, 'name' => $changeType->name]);
        }
        $this->assertTrue(array_key_exists('id', (array) reset($changeTypes)));
        $this->assertTrue(array_key_exists('name', (array) reset($changeTypes)));
    }

    /** @group changeStatuses */
    public function test_changeStatuses_withoutSearchString_returnsTenChangeStatusesWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdChangeStatus::class, 15)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'change_statuses');
        $changeStatuses = json_decode($response->content())->data->change_statuses;
        $response->assertStatus(200);
        foreach ($changeStatuses as $changeStatus) {
            $this->assertDatabaseHas('sd_change_status', ['id' => $changeStatus->id, 'name' => $changeStatus->name]);
        }
        // default value of limit is 10
        $this->assertCount(10, $changeStatuses);
    }

    /** @group changeStatuses */
    public function test_changeStatuses_withMeta_returnsAllChangeStatusesWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdChangeStatus::class, 15)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'change_statuses', ['meta' => 'true']);
        $changeStatuses = json_decode($response->content())->data->change_statuses;
        $response->assertStatus(200);
        foreach ($changeStatuses as $changeStatus) {
            $this->assertDatabaseHas('sd_change_status', ['id' => $changeStatus->id, 'name' => $changeStatus->name]);
        }
        // default value of limit is 10
        $this->assertCount(SdChangeStatus::count(), $changeStatuses);
    }

    /** @group changeStatuses */
    public function test_changeStatuses_withSearchString_returnsChangeStatusesWithIdAndNameBasedOnString()
    {
        $this->getLoggedInUserForWeb('agent');
        $changeStatusInDb = factory(SdChangeStatus::class)->create(['name' => 'spam']);
        factory(SdChangeStatus::class)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'change_statuses', ['search-query' => $changeStatusInDb->name]);
        $changeStatuses = json_decode($response->content())->data->change_statuses;
        $response->assertStatus(200);
        $this->assertCount(1, $changeStatuses);
        $changeStatus = reset($changeStatuses);
        $this->assertDatabaseHas('sd_change_status', ['id' => $changeStatus->id, 'name' => $changeStatusInDb->name]);
    }

    /** @group changeStatuses */
    public function test_changeStatuses_withConfigAndWithLimit_returnsChangeStatusesWithAllFields()
    {
        $this->getLoggedInUserForWeb('admin');
        factory(SdChangeStatus::class, 12)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'change_statuses',['config'=>true, 'limit' => 11]);
        $response->assertStatus(200);
        $changeStatuses = json_decode($response->content())->data->change_statuses;
        // default value of limit is 10 but 11 entries are coming becuase limit is set to 11
        $this->assertCount(11, $changeStatuses);
        foreach ($changeStatuses as $changeStatus) {
            $this->assertDatabaseHas('sd_change_status', ['id' => $changeStatus->id, 'name' => $changeStatus->name]);
        }
        $this->assertTrue(array_key_exists('id', (array) reset($changeStatuses)));
        $this->assertTrue(array_key_exists('name', (array) reset($changeStatuses)));
    }

    /** @group changePriorities */
    public function test_changePriorities_withoutSearchString_returnsTenChangePrioritiesWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdChangepriorities::class, 15)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'change_priorities');
        $changePriorities = json_decode($response->content())->data->change_priorities;
        $response->assertStatus(200);
        foreach ($changePriorities as $changePriority) {
            $this->assertDatabaseHas('sd_change_priorities', ['id' => $changePriority->id, 'name' => $changePriority->name]);
        }
        // default value of limit is 10
        $this->assertCount(10, $changePriorities);
    }

    /** @group changePriorities */
    public function test_changePriorities_withMeta_returnsAllChangePrioritiesWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdChangepriorities::class, 15)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'change_priorities', ['meta' => 'true']);
        $changePriorities = json_decode($response->content())->data->change_priorities;
        $response->assertStatus(200);
        foreach ($changePriorities as $changePriority) {
            $this->assertDatabaseHas('sd_change_priorities', ['id' => $changePriority->id, 'name' => $changePriority->name]);
        }
        // default value of limit is 10
        $this->assertCount(SdChangepriorities::count(), $changePriorities);
    }

    /** @group changePriorities */
    public function test_changePriorities_withSearchString_returnsChangePrioritiesWithIdAndNameBasedOnString()
    {
        $this->getLoggedInUserForWeb('agent');
        $changePriorityInDb = factory(SdChangepriorities::class)->create(['name' => 'spam']);
        factory(SdChangepriorities::class)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'change_priorities', ['search-query' => $changePriorityInDb->name]);
        $changePriorities = json_decode($response->content())->data->change_priorities;
        $response->assertStatus(200);
        $this->assertCount(1, $changePriorities);
        $changePriority = reset($changePriorities);
        $this->assertDatabaseHas('sd_change_priorities', ['id' => $changePriority->id, 'name' => $changePriorityInDb->name]);
    }

    /** @group changePriorities */
    public function test_changePriorities_withConfigAndWithLimit_returnsChangePrioritiesWithAllFields()
    {
        $this->getLoggedInUserForWeb('admin');
        factory(SdChangepriorities::class, 12)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'change_priorities',['config'=>true, 'limit' => 11]);
        $response->assertStatus(200);
        $changePriorities = json_decode($response->content())->data->change_priorities;
        // default value of limit is 10 but 11 entries are coming becuase limit is set to 11
        $this->assertCount(11, $changePriorities);
        foreach ($changePriorities as $changePriority) {
            $this->assertDatabaseHas('sd_change_priorities', ['id' => $changePriority->id, 'name' => $changePriority->name]);
        }
        $this->assertTrue(array_key_exists('id', (array) reset($changePriorities)));
        $this->assertTrue(array_key_exists('name', (array) reset($changePriorities)));
    }

    /** @group changes */
    public function test_changes_withoutSearchString_returnsTenChangesWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdChanges::class, 15)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'changes');
        $changes = json_decode($response->content(), true)['data']['changes'];
        $response->assertStatus(200);
        $limit = 10;
        $changesInDb = SdChanges::select('id', 'subject', 'identifier')->orderBy('subject', 'asc')->take($limit)->get()->toArray();
        for ($changeIndex=0; $changeIndex < $limit; $changeIndex++) { 
            $this->assertEquals("{$changesInDb[$changeIndex]['identifier']} {$changesInDb[$changeIndex]['subject']}", $changes[$changeIndex]['name']);
        }
        // default value of limit is 10
        $this->assertCount($limit, $changes);
    }

     /** @group changes */
    public function test_changes_withMeta_returnsAllChangesWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdChanges::class, 15)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'changes', ['meta' => true]);
        $changes = json_decode($response->content(), true)['data']['changes'];
        $response->assertStatus(200);
        $changesInDb = SdChanges::select('id', 'subject', 'identifier')->orderBy('subject', 'asc')->get()->toArray();
        $limit = SdChanges::count();
        for ($changeIndex=0; $changeIndex < $limit; $changeIndex++) { 
            $this->assertEquals("{$changesInDb[$changeIndex]['identifier']} {$changesInDb[$changeIndex]['subject']}", $changes[$changeIndex]['name']);
        }
        $this->assertCount($limit, $changes);
    }

    /** @group changes */
    public function test_changes_withSearchString_returnsChangesWithIdAndNameBasedOnPassedSubject()
    {
        $this->getLoggedInUserForWeb('agent');
        $subject = 'spam';
        factory(SdChanges::class)->create(['subject' => $subject]);
        factory(SdChanges::class)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'changes', ['search-query' => $subject]);
        $changes = json_decode($response->content(), true)['data']['changes'];
        $response->assertStatus(200);
        $changesInDb = SdChanges::where('subject', $subject)->orderBy('subject', 'asc')->select('id', 'subject', 'identifier')->get()->toArray();
        for ($changeIndex=0; $changeIndex < count($changes); $changeIndex++) { 
            $this->assertEquals("{$changesInDb[$changeIndex]['identifier']} {$changesInDb[$changeIndex]['subject']}", $changes[$changeIndex]['name']);
        }
        $this->assertCount(count($changesInDb), $changes);
    }

      /** @group changes */
    public function test_changes_withConfigAndLimit_returnsChangesWithAllFields()
    {
        $this->getLoggedInUserForWeb('admin');
        factory(SdChanges::class,12)->create();
        $limit = 11;
        $response = $this->call('GET', self::DEPENDENCY_URL . 'changes', ['config' => true, 'limit' => $limit]);
        $changes = json_decode($response->content(), true)['data']['changes'];
        $response->assertStatus(200);
        $changesInDb = SdChanges::take($limit)->orderBy('subject', 'asc')->get()->toArray();
        for ($changeIndex=0; $changeIndex < $limit; $changeIndex++) { 
            $this->assertDatabaseHas('sd_changes', ['id' => $changes[$changeIndex]['id'], 'subject' => $changesInDb[$changeIndex]['subject']]);
            $this->assertCount(count($changesInDb[$changeIndex]),$changes[$changeIndex]);
        }
        $this->assertCount($limit, $changes);
    }

    /** @group releases */
    public function test_releases_withoutSearchString_returnsTenReleasesWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdReleases::class, 15)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'releases', ['sort-order'=>'desc', 'sort-field'=>'id']);
        $releases = json_decode($response->content(), true)['data']['releases'];
        $response->assertStatus(200);
        $limit = 10;
        $releasesInDb = SdReleases::select('id', 'subject', 'identifier')->orderBy('id', 'desc')->take($limit)->get()->toArray();
        for ($releaseIndex=0; $releaseIndex < $limit; $releaseIndex++) { 
            $this->assertEquals(implode(' ', [$releasesInDb[$releaseIndex]['identifier'], $releasesInDb[$releaseIndex]['subject']]), $releases[$releaseIndex]['name']);
        }
        // default value of limit is 10
        $this->assertCount($limit, $releases);
    }

    /** @group releases */
    public function test_releases_withMeta_returnsTenReleasesWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdReleases::class, 15)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'releases', ['meta' => true]);
        $releases = json_decode($response->content(), true)['data']['releases'];
        $response->assertStatus(200);
        $limit = SdReleases::count();
        $releasesInDb = SdReleases::select('id', 'subject', 'identifier')->orderBy('subject', 'asc')->get()->toArray();
        for ($releaseIndex=0; $releaseIndex < $limit; $releaseIndex++) { 
            $this->assertEquals(implode(' ', [$releasesInDb[$releaseIndex]['identifier'], $releasesInDb[$releaseIndex]['subject']]), $releases[$releaseIndex]['name']);
        }
        // default value of limit is 10
        $this->assertCount($limit, $releases);
    }

    /** @group releases */
    public function test_releases_withSearchString_returnsReleasesWithIdAndNameBasedOnPassedSubject()
    {
        $this->getLoggedInUserForWeb('agent');
        $subject = 'spam';
        factory(SdReleases::class)->create(['subject' => $subject]);
        factory(SdReleases::class)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'releases', ['search-query' => $subject]);
        $releases = json_decode($response->content(), true)['data']['releases'];
        $response->assertStatus(200);
        $releasesInDb = SdReleases::where('subject', $subject)->select('id', 'subject', 'identifier')->get()->toArray();
        for ($releaseIndex=0; $releaseIndex < count($releases); $releaseIndex++) { 
            $this->assertEquals(implode(' ', [$releasesInDb[$releaseIndex]['identifier'], $releasesInDb[$releaseIndex]['subject']]), $releases[$releaseIndex]['name']);
        }
        // default value of limit is 10
        $this->assertCount(count($releasesInDb), $releases);
    }

    /** @group releases */
    public function test_releases_withConfigAndLimit_returnsReleasesWithAllFields()
    {
        $this->getLoggedInUserForWeb('admin');
        factory(SdReleases::class, 15)->create();
        $limit = 11;
        $response = $this->call('GET', self::DEPENDENCY_URL . 'releases', ['config' => true, 'limit' => $limit]);
        $releases = json_decode($response->content(), true)['data']['releases'];
        $response->assertStatus(200);
        $releasesInDb = SdReleases::take($limit)->orderBy('subject', 'asc')->get()->toArray();
        for ($releaseIndex=0; $releaseIndex < $limit; $releaseIndex++) { 
            $this->assertDatabaseHas('sd_releases', ['id' => $releases[$releaseIndex]['id'], 'subject' => $releasesInDb[$releaseIndex]['subject']]);
            $this->assertCount(count($releasesInDb[$releaseIndex]),$releases[$releaseIndex]);
            
        }
        $this->assertCount($limit, $releases);
    }

    /** @group cabs */
    public function test_cabs_withoutSearchString_returnsTenCabsWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(Cab::class, 15)->create(['type' => 'cab']);
        $response = $this->call('GET', self::DEPENDENCY_URL . 'cabs');
        $cabs = json_decode($response->content(), true)['data']['cabs'];
        $response->assertStatus(200);
        $limit = 10;
        $cabsInDb = Cab::select('id', 'name')->orderBy('name', 'asc')->take($limit)->get()->toArray();
        for ($cabIndex=0; $cabIndex < $limit; $cabIndex++) { 
            $this->assertEquals($cabsInDb[$cabIndex]['name'], $cabs[$cabIndex]['name']);
        }
        // default value of limit is 10
        $this->assertCount($limit, $cabs);
    }

    /** @group cabs */
    public function test_cabs_withMeta_returnsTenCabsWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(Cab::class, 15)->create(['type' => 'cab']);
        $response = $this->call('GET', self::DEPENDENCY_URL . 'cabs', ['meta' => true]);
        $cabs = json_decode($response->content(), true)['data']['cabs'];
        $response->assertStatus(200);
        $limit = Cab::count();
        $cabsInDb = Cab::where('type', 'cab')->select('id', 'name')->orderBy('name', 'asc')->get()->toArray();
        for ($cabIndex=0; $cabIndex < $limit; $cabIndex++) { 
            $this->assertEquals($cabsInDb[$cabIndex]['name'], $cabs[$cabIndex]['name']);
        }
        // default value of limit is 10
        $this->assertCount($limit, $cabs);
    }

    /** @group cabs */
    public function test_cabs_withSearchString_returnsCabsWithIdAndNameBasedOnPassedName()
    {
        $this->getLoggedInUserForWeb('agent');
        $name = 'spam';
        factory(Cab::class)->create(['name' => $name, 'type' => 'cab']);
        factory(Cab::class)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'cabs', ['search-query' => $name]);
        $cabs = json_decode($response->content(), true)['data']['cabs'];
        $response->assertStatus(200);
        $cabsInDb = Cab::where([['name', $name], ['type', 'cab']])->select('id', 'name')->orderBy('name', 'asc')->get()->toArray();
        for ($cabIndex=0; $cabIndex < count($cabs); $cabIndex++) { 
            $this->assertEquals($cabsInDb[$cabIndex]['name'], $cabs[$cabIndex]['name']);
        }
        // default value of limit is 10
        $this->assertCount(count($cabsInDb), $cabs);
    }

    /** @group cabs */
    public function test_cabs_withConfigAndLimit_returnsCabsWithAllFields()
    {
        $this->getLoggedInUserForWeb('admin');
        factory(Cab::class, 15)->create(['type' => 'cab']);
        $limit = 11;
        $response = $this->call('GET', self::DEPENDENCY_URL . 'cabs', ['config' => true, 'limit' => $limit]);
        $cabs = json_decode($response->content(), true)['data']['cabs'];
        $response->assertStatus(200);
        $cabsInDb = Cab::where('type','cab')->orderBy('name', 'asc')->take($limit)->get()->toArray();
        for ($cabIndex=0; $cabIndex < $limit; $cabIndex++) { 
            $this->assertDatabaseHas('approval_workflows', ['id' => $cabs[$cabIndex]['id'], 'name' => $cabsInDb[$cabIndex]['name']]);
            
        }
        $this->assertCount($limit, $cabs);
    }

    /** @group userTypes */
    public function test_userTypes_withoutSearchString()
    {
        $this->getLoggedInUserForWeb('admin');

        $response = $this->call('GET', self::DEPENDENCY_URL . 'user_types');

        $response->assertStatus(200);
    }

    /** @group userTypes */
    public function test_userTypes_withSearch()
    {
        $this->getLoggedInUserForWeb('admin');

        $response = $this->call('GET', self::DEPENDENCY_URL . 'user_types',['search-query'=>'user','meta'=>true]);

        $response->assertStatus(200);

        $userTypes = json_decode($response->content())->data->user_types;

        $this->assertCount(1, $userTypes);
    }

    /** @group changesBasedOnTicket */
    public function test_changesBasedOnTicket_withoutTicketId_returnsTenChangesWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdChanges::class, 14)->create();
        $change = factory(SdChanges::class)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'changes_based_on_ticket');
        $changes = json_decode($response->content(), true)['data']['changes'];
        $response->assertStatus(200);
        $limit = 10;
        $changesInDb = SdChanges::select('id', 'subject', 'identifier')->orderBy('subject', 'asc')->take($limit)->get()->toArray();
        for ($changeIndex=0; $changeIndex < $limit; $changeIndex++) { 
            $this->assertEquals("{$changesInDb[$changeIndex]['identifier']} {$changesInDb[$changeIndex]['subject']}", $changes[$changeIndex]['name']);
        }
        // default value of limit is 10
        $this->assertCount($limit, $changes);
    }

    /** @group changesBasedOnTicket */
    public function test_changesBasedOnTicket_withoutSearchStringAndWithTicketId_returnsTenChangesWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdChanges::class, 14)->create();
        $change = factory(SdChanges::class)->create();
        $ticketId = factory(Ticket::class)->create()->id;
        $type = 'initiating';
        $change->attachTickets()->attach([$ticketId => ['type' => $type]]);
        $response = $this->call('GET', self::DEPENDENCY_URL . 'changes_based_on_ticket', ['ticket_id' => $ticketId, 'sort-field'=>'id', 'sort-order'=>'desc']);
        $changes = json_decode($response->content(), true)['data']['changes'];
        $response->assertStatus(200);
        $limit = 10;
        $changesInDb = SdChanges::whereDoesntHave('attachTickets')->select('id', 'subject', 'identifier')->orderBy('id', 'desc')->take($limit)->get()->toArray();
        for ($changeIndex=0; $changeIndex < $limit; $changeIndex++) {
            $this->assertEquals("{$changesInDb[$changeIndex]['identifier']} {$changesInDb[$changeIndex]['subject']}", $changes[$changeIndex]['name']);
        }
        // default value of limit is 10
        $this->assertCount($limit, $changes);
    }

    /** @group changesBasedOnTicket */
    public function test_changesBasedOnTicket_withMetaAndWithTicketId_returnsAllChangesWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdChanges::class, 15)->create();
        $change = factory(SdChanges::class)->create();
        $ticketId = factory(Ticket::class)->create()->id;
        $type = 'initiating';
        $change->attachTickets()->attach([$ticketId => ['type' => $type]]);
        $response = $this->call('GET', self::DEPENDENCY_URL . 'changes', ['meta' => true, 'ticket_id' => $ticketId]);
        $changes = json_decode($response->content(), true)['data']['changes'];
        $response->assertStatus(200);
        $changesInDb = SdChanges::select('id', 'subject', 'identifier')->orderBy('subject', 'asc')->get()->toArray();
        $limit = SdChanges::count();
        for ($changeIndex=0; $changeIndex < $limit; $changeIndex++) { 
            $this->assertEquals("{$changesInDb[$changeIndex]['identifier']} {$changesInDb[$changeIndex]['subject']}", $changes[$changeIndex]['name']);
        }
        $this->assertCount($limit, $changes);
    }

    /** @group changesBasedOnTicket */
    public function test_changesBasedOnTicket_withSearchStringAndWithTicketId_returnsChangesWithIdAndNameBasedOnPassedSubject()
    {
        $this->getLoggedInUserForWeb('agent');
        $subject = 'spam';
        factory(SdChanges::class)->create(['subject' => $subject]);
        factory(SdChanges::class)->create();
        $change = factory(SdChanges::class)->create();
        $ticketId = factory(Ticket::class)->create()->id;
        $type = 'initiating';
        $change->attachTickets()->attach([$ticketId => ['type' => $type]]);
        $response = $this->call('GET', self::DEPENDENCY_URL . 'changes', ['search-query' => $subject, 'ticket_id' => $ticketId]);
        $changes = json_decode($response->content(), true)['data']['changes'];
        $response->assertStatus(200);
        $changesInDb = SdChanges::where('subject', $subject)->select('id', 'subject', 'identifier')->get()->toArray();
        for ($changeIndex=0; $changeIndex < count($changes); $changeIndex++) { 
            $this->assertEquals("{$changesInDb[$changeIndex]['identifier']} $subject", $changes[$changeIndex]['name']);
        }
        $this->assertCount(count($changesInDb), $changes);
    }

    /** @group changesBasedOnTicket */
    public function test_changesBasedOnTicket_withConfigAndLimitAndWithTicketId_returnsChangesWithAllFields()
    {
        $this->getLoggedInUserForWeb('admin');
        factory(SdChanges::class,12)->create();
        $change = factory(SdChanges::class)->create();
        $ticketId = factory(Ticket::class)->create()->id;
        $type = 'initiating';
        $change->attachTickets()->attach([$ticketId => ['type' => $type]]);
        $limit = 11;
        $response = $this->call('GET', self::DEPENDENCY_URL . 'changes', ['config' => true, 'limit' => $limit, 'ticket_id' => $ticketId]);
        $changes = json_decode($response->content(), true)['data']['changes'];
        $response->assertStatus(200);
        $changesInDb = SdChanges::take($limit)->orderBy('subject', 'asc')->get()->toArray();
        for ($changeIndex=0; $changeIndex < $limit; $changeIndex++) { 
            $this->assertDatabaseHas('sd_changes', ['id' => $changes[$changeIndex]['id'], 'subject' => $changesInDb[$changeIndex]['subject']]);
            $this->assertCount(count($changesInDb[$changeIndex]),$changes[$changeIndex]);
        }
        $this->assertCount($limit, $changes);
    }

    /** @group ticketsBasedOnChange */
    public function test_ticketsBasedOnChange_withoutChangeId_returnsTenTicketsWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(Ticket::class, 14)->create();
        $change = factory(Ticket::class)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'tickets_based_on_change');
        $tickets = json_decode($response->content(), true)['data']['tickets'];
        $response->assertStatus(200);
        $limit = 10;
        for ($ticketIndex=0; $ticketIndex < $limit; $ticketIndex++) {
            $this->assertDatabaseHas('tickets', ['id' => $tickets[$ticketIndex]['id']]);
        }
        // default value of limit is 10
        $this->assertCount($limit, $tickets);
    }

    /** @group ticketsBasedOnChange */
    public function test_ticketsBasedOnChange_withoutSearchStringAndWithChangeId_returnsTenTicketsWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(Ticket::class, 14)->create();
        $ticketId = factory(Ticket::class)->create()->id;
        $ticket = SdTicket::find($ticketId);
        $changeId = factory(SdChanges::class)->create()->id;
        $type = 'initiating';
        $ticket->changes()->attach([$changeId => ['type' => $type]]);
        $response = $this->call('GET', self::DEPENDENCY_URL . 'tickets_based_on_change', ['change_id' => $changeId]);
        $tickets = json_decode($response->content(), true)['data']['tickets'];
        $response->assertStatus(200);
        $limit = 10;
        for ($ticketIndex=0; $ticketIndex < $limit; $ticketIndex++) {
            $this->assertDatabaseHas('tickets', ['id' => $tickets[$ticketIndex]['id']]);
        }
        // default value of limit is 10
        $this->assertCount($limit, $tickets);
    }

    /** @group ticketsBasedOnChange */
    public function test_ticketsBasedOnChange_withMetaAndWithChangeId_returnsAllTicketsWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(Ticket::class, 14)->create();
        $ticketId = factory(Ticket::class)->create()->id;
        $ticket = SdTicket::find($ticketId);
        $changeId = factory(SdChanges::class)->create()->id;
        $type = 'initiating';
        $ticket->changes()->attach([$changeId => ['type' => $type]]);
        $response = $this->call('GET', self::DEPENDENCY_URL . 'tickets_based_on_change', ['meta' => true, 'change_id' => $changeId]);
        $tickets = json_decode($response->content(), true)['data']['tickets'];
        $response->assertStatus(200);
        $limit = Ticket::count()-1;
        for ($ticketIndex=0; $ticketIndex < $limit; $ticketIndex++) {
            $this->assertDatabaseHas('tickets', ['id' => $tickets[$ticketIndex]['id']]);
        }
        // default value of limit is 10
        $this->assertCount($limit, $tickets);
    }

    /** @group ticketsBasedOnChange */
    public function test_ticketsBasedOnChange_withSearchStringAndWithChangeId_returnsTicketsWithIdAndNameBasedOnPassedTicketNumber()
    {
        $this->getLoggedInUserForWeb('agent');
        $tickeOne = factory(Ticket::class)->create();
        $ticketTwo = factory(Ticket::class)->create();
        $change = factory(SdChanges::class)->create();
        $ticket = factory(Ticket::class)->create();
        $type = 'initiating';
        $change->attachTickets()->attach([$ticket->id => ['type' => $type]]);
        $response = $this->call('GET', self::DEPENDENCY_URL . 'tickets_based_on_change', ['search-query' => $tickeOne->ticket_number, 'change_id' => $change->id]);
        $tickets = json_decode($response->content(), true)['data']['tickets'];
        $this->assertCount(1,$tickets);
        $response->assertStatus(200);
    }


    /** @group productStatuses */
    public function test_productStatuses_withSearchString_returnsProductStatusesWithIdAndNameBasedOnString()
    {
        $this->getLoggedInUserForWeb('agent');
        $productStatusInDb = factory(SdProductstatus::class)->create(['name' => 'In Production']);
        factory(SdProductstatus::class)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'product_statuses', ['search-query' => $productStatusInDb->name]);
        $productStatuses = json_decode($response->content())->data->product_statuses;
        $response->assertStatus(200);
        $this->assertCount(2, $productStatuses);
        $productStatus = reset($productStatuses);
        $this->assertDatabaseHas('sd_product_status', ['id' => $productStatus->id, 'name' => $productStatusInDb->name]);
    }

    /** @group productStatuses */
    public function test_productStatuses_withoutSearchString_returnsAllProductStatusesWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdProductstatus::class,3)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'product_statuses');
        $productStatuses = json_decode($response->content())->data->product_statuses;
        $response->assertStatus(200);

        foreach ($productStatuses as $productStatus) {
            $this->assertDatabaseHas('sd_product_status', ['id' => $productStatus->id, 'name' => $productStatus->name]);
        }
        // default value of limit is 10
        $this->assertCount(6, $productStatuses); 
    }

    /** @group productStatuses */
    public function test_productStatuses_withMeta_returnsAllProductStatusesWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdProductstatus::class,3)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'product_statuses',['meta'=>'true']);
        $productStatuses = json_decode($response->content())->data->product_statuses;
        $response->assertStatus(200);

        foreach ($productStatuses as $productStatus) {
            $this->assertDatabaseHas('sd_product_status', ['id' => $productStatus->id, 'name' => $productStatus->name]);
        }
        $this->assertCount(SdProductstatus::count(), $productStatuses); 
    }

    /** @group productStatuses */
    public function test_productStatuses_withConfigAndLimit_returnsAllProductStatusesWithAllFields()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdProductstatus::class,3)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'product_statuses',['config'=>'true','limit'=>2]);
        $productStatuses = json_decode($response->content())->data->product_statuses;
        $response->assertStatus(200);

        foreach ($productStatuses as $productStatus) {
            $this->assertDatabaseHas('sd_product_status', ['id' => $productStatus->id, 'name' => $productStatus->name]);
        }
        $this->assertCount(2, $productStatuses); 
        $this->assertTrue(array_key_exists('id', (array) reset($productStatuses)));
        $this->assertTrue(array_key_exists('name', (array) reset($productStatuses)));
    }

    /** @group productProcurementModes */
    public function test_productProcurementModes_withSearchString_returnsProductrodcurementModesWithIdAndNameBasedOnString()
    {
        $this->getLoggedInUserForWeb('agent');
        $productProcModeInDb = factory(SdProcurment::class)->create(['name' => 'Lease']);
        factory(SdProcurment::class)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'product_proc_mode', ['search-query' => $productProcModeInDb->name]);
        $productProcModes = json_decode($response->content())->data->product_proc_mode;
        $response->assertStatus(200);
        $this->assertCount(2, $productProcModes);
        $productProcMode = reset($productProcModes);
        $this->assertDatabaseHas('sd_product_proc_mode', ['id' => $productProcMode->id, 'name' => $productProcModeInDb->name]);
    }

    /** @group productProcurementModes */
    public function test_productProcurementModes_withoutSearchString_returnsAllProductrodcurementModesWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdProcurment::class,2)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'product_proc_mode');
        $productProcModes = json_decode($response->content())->data->product_proc_mode;
        $response->assertStatus(200);

            foreach ($productProcModes as $productProcMode) {
            $this->assertDatabaseHas('sd_product_proc_mode', ['id' => $productProcMode->id, 'name' => $productProcMode->name]);
        }
        $this->assertCount(SdProcurment::count(), $productProcModes);
        
    }

    /** @group productProcurementModes */
    public function test_productProcurementModes_withMeta_returnsAllProductrodcurementModesWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdProcurment::class,2)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'product_proc_mode',['meta'=>'true']);
        $productProcModes = json_decode($response->content())->data->product_proc_mode;
        $response->assertStatus(200);

            foreach ($productProcModes as $productProcMode) {
            $this->assertDatabaseHas('sd_product_proc_mode', ['id' => $productProcMode->id, 'name' => $productProcMode->name]);
        }
        $this->assertCount(SdProcurment::count(), $productProcModes);
        
    }

    /** @group productProcurementModes */
    public function test_productProcurementModes_withConfigAndLimit_returnsAllProductrodcurementModesWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdProcurment::class,2)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'product_proc_mode',['config'=>'true','limit'=>1]);
        $productProcModes = json_decode($response->content())->data->product_proc_mode;
        $response->assertStatus(200);

            foreach ($productProcModes as $productProcMode) {
            $this->assertDatabaseHas('sd_product_proc_mode', ['id' => $productProcMode->id, 'name' => $productProcMode->name]);
        }
        $this->assertCount(1, $productProcModes);
        $this->assertTrue(array_key_exists('id', (array) reset($productProcModes)));
        $this->assertTrue(array_key_exists('name', (array) reset($productProcModes)));
        
    }

    /** @group changesBasedOnTicket */
    public function test_vendorsBasedOnProduct_withoutProductId_returnsTenVendorsWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdVendors::class, 14)->create();
        $vendor = factory(SdVendors::class)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'vendors_based_on_product', ['sort-order'=>'desc', 'sort-field'=>'id']);
        $vendors = json_decode($response->content(), true)['data']['vendors'];
        $response->assertStatus(200);
        $limit = 10;
        $vendorsInDb = SdVendors::select('id', 'name')->orderBy('id', 'desc')->take($limit)->get()->toArray();
        for ($vendorIndex=0; $vendorIndex < $limit; $vendorIndex++) { 
            $this->assertEquals($vendorsInDb[$vendorIndex]['name'], $vendors[$vendorIndex]['name']);
        }
        // default value of limit is 10
        $this->assertCount($limit, $vendors);
    }

    /** @group changesBasedOnTicket */
    public function test_vendorsBasedOnProduct_withoutSearchStringAndWithProductId_returnsTenVendorWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdVendors::class, 14)->create();
        $vendor = factory(SdVendors::class)->create();
        $productId = factory(SdProducts::class)->create()->id;
        $vendor->attachProducts()->attach([$productId]);
        $response = $this->call('GET', self::DEPENDENCY_URL . 'vendors_based_on_product', ['product_id' => $productId]);
        $vendors = json_decode($response->content(), true)['data']['vendors'];
        $response->assertStatus(200);
        $limit = 10;
        $vendorsInDb = SdVendors::select('id', 'name')->orderBy('name', 'asc')->take($limit)->get()->toArray();
        for ($vendorIndex=0; $vendorIndex < $limit; $vendorIndex++) { 
            $this->assertEquals($vendorsInDb[$vendorIndex]['name'], $vendors[$vendorIndex]['name']);
        }
        // default value of limit is 10
        $this->assertCount($limit, $vendors);
    }

    /** @group changesBasedOnTicket */
    public function test_vendorsBasedOnproduct_withMetaAndWithProductId_returnsAllVendorsWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdVendors::class, 15)->create();
        $vendor = factory(SdVendors::class)->create();
        $productId = factory(SdProducts::class)->create()->id;
        $vendor->attachProducts()->attach([$productId]);
        $response = $this->call('GET', self::DEPENDENCY_URL . 'vendors_based_on_product', ['meta' => true, 'product_id' => $productId]);
        $vendors = json_decode($response->content(), true)['data']['vendors'];
        $response->assertStatus(200);
        $vendorsInDb = SdVendors::select('id', 'name')->orderBy('name', 'asc')->get()->toArray();
        $limit = count($vendors);
        for ($vendorIndex=0; $vendorIndex < $limit; $vendorIndex++) { 
            $this->assertEquals($vendorsInDb[$vendorIndex]['name'], $vendors[$vendorIndex]['name']);
        }
        // default value of limit is 10
        $this->assertCount($limit, $vendors);
    }

    /** @group changesBasedOnTicket */
    public function test_vendorsBasedOnProduct_withSearchStringAndWithProductId_returnsVendorsWithIdAndNameBasedOnPassedName()
    {
        $this->getLoggedInUserForWeb('agent');
        $name = 'spam';
        factory(SdVendors::class)->create(['name' => $name]);
        factory(SdVendors::class)->create();
        $vendor = factory(SdVendors::class)->create();
        $productId = factory(SdProducts::class)->create()->id;
        $vendor->attachProducts()->attach([$productId]);
        $response = $this->call('GET', self::DEPENDENCY_URL . 'vendors_based_on_product', ['search-query' => $name, 'product_id' => $productId]);
        $vendors = json_decode($response->content(), true)['data']['vendors'];
        $response->assertStatus(200);
        $vendorsInDb = SdVendors::where('name', $name)->select('id', 'name')->orderBy('name', 'asc')->get()->toArray();
        for ($vendorIndex=0; $vendorIndex < count($vendors); $vendorIndex++) { 
            $this->assertEquals($vendorsInDb[$vendorIndex]['name'], $vendors[$vendorIndex]['name']);
        }
        $this->assertCount(count($vendorsInDb), $vendors);
    }

    /** @group problems */
    public function test_problems_withoutSearchString_returnsTenProblemsWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdProblem::class, 15)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'problems');
        $problems = json_decode($response->content(), true)['data']['problems'];
        $response->assertStatus(200);
        $limit = 10;
        $problemsInDb = SdProblem::select('id', 'subject','identifier')->orderBy('subject', 'asc')->take($limit)->get()->toArray();
        for ($problemIndex=0; $problemIndex < $limit; $problemIndex++) {
            $this->assertEquals($problemsInDb[$problemIndex]['identifier'].' '.$problemsInDb[$problemIndex]['subject'], $problems[$problemIndex]['name']);
        }
        // default value of limit is 10
        $this->assertCount($limit, $problems);
    }

     /** @group problems */
    public function test_problems_withMeta_returnsAllProblemsWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdProblem::class, 15)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'problems', ['meta' => true]);
        $problems = json_decode($response->content(), true)['data']['problems'];
        $response->assertStatus(200);
        $problemsInDb = SdProblem::select('id', 'subject','identifier')->orderBy('subject', 'asc')->get()->toArray();
        $limit = SdProblem::count();
        for ($problemIndex=0; $problemIndex < $limit; $problemIndex++) { 
            $this->assertEquals($problemsInDb[$problemIndex]['identifier'].' '.$problemsInDb[$problemIndex]['subject'], $problems[$problemIndex]['name']);
        }
        $this->assertCount($limit, $problems);
    }

    /** @group problems */
    public function test_problems_withSearchString_returnsProblemsWithIdAndNameBasedOnPassedSubject()
    {
        $this->getLoggedInUserForWeb('agent');
        $subject = 'spam';
        factory(SdProblem::class)->create(['subject' => $subject]);
        factory(SdProblem::class)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'problems', ['search-query' => $subject]);
        $problems = json_decode($response->content(), true)['data']['problems'];
        $response->assertStatus(200);
        $problemsInDb = SdProblem::where('subject', $subject)->orderBy('updated_at', 'desc')->select('id', 'subject', 'identifier')->get()->toArray();
        for ($problemIndex=0; $problemIndex < count($problems); $problemIndex++) { 
            $this->assertEquals($problemsInDb[$problemIndex]['identifier'].' '.$subject, $problems[$problemIndex]['name']);
        }
        $this->assertCount(count($problemsInDb), $problems);
    }

      /** @group problems */
    public function test_problems_withConfigAndLimit_returnsProblemsWithAllFields()
    {
        $this->getLoggedInUserForWeb('admin');
        factory(SdProblem::class,12)->create();
        $limit = 11;
        $response = $this->call('GET', self::DEPENDENCY_URL . 'problems', ['config' => true, 'limit' => $limit]);
        $problems = json_decode($response->content(), true)['data']['problems'];
        $response->assertStatus(200);
        $problemsInDb = SdProblem::take($limit)->orderBy('subject', 'asc')->get()->toArray();
        for ($problemIndex=0; $problemIndex < $limit; $problemIndex++) { 
            $this->assertDatabaseHas('sd_problem', ['id' => $problems[$problemIndex]['id'], 'subject' => $problemsInDb[$problemIndex]['subject']]);
        }
        $this->assertCount($limit, $problems);
    }

    /** @group ticketsBasedOnProblem */
    public function test_ticketsBasedOnProblem_withoutProblemId_returnsTenTicketsWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(Ticket::class, 14)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'tickets_based_on_problem');
        $tickets = json_decode($response->content(), true)['data']['tickets'];
        $response->assertStatus(200);
        $limit = 10;
        $ticketsInDb = Ticket::select('id', 'ticket_number')->take($limit)->get()->toArray();
        for ($ticketIndex=0; $ticketIndex < $limit; $ticketIndex++) { 
            $ticket_name = str_replace(array( '(', ')' ), '', $tickets[$ticketIndex]['name']);
            $this->assertDatabaseHas('tickets', ['id' => $tickets[$ticketIndex]['id'], 'ticket_number' => $ticket_name ]);
        }
        // default value of limit is 10
        $this->assertCount($limit, $tickets);
    }

    /** @group ticketsBasedOnProblem */
    public function test_ticketsBasedOnProblem_withoutSearchStringAndWithProblemId_returnsTenTicketsWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(Ticket::class, 14)->create();
        $ticketId = factory(Ticket::class)->create()->id;
        $ticket = SdTicket::find($ticketId);
        $problemId = factory(SdProblem::class)->create()->id;
        $type = 'initiating';
        $ticket->problems()->attach([$problemId => ['type' => $type]]);
        $response = $this->call('GET', self::DEPENDENCY_URL . 'tickets_based_on_problem', ['problem_id' => $problemId]);
        $tickets = json_decode($response->content(), true)['data']['tickets'];
        $response->assertStatus(200);
        $limit = 10;
        $ticketsInDb = Ticket::select('id', 'ticket_number')->take($limit)->get()->toArray();
        for ($ticketIndex=0; $ticketIndex < $limit; $ticketIndex++) { 
            $ticket_name = str_replace(array( '(', ')' ), '', $tickets[$ticketIndex]['name']);
            $this->assertDatabaseHas('tickets', ['id' => $tickets[$ticketIndex]['id'], 'ticket_number' =>  $ticket_name]);
        }
        // default value of limit is 10
        $this->assertCount($limit, $tickets);
    }

    /** @group ticketsBasedOnProblem */
    public function test_ticketsBasedOnProblem_withMetaAndWithProblemId_returnsAllTicketsWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(Ticket::class, 14)->create();
        $ticketId = factory(Ticket::class)->create()->id;
        $ticket = SdTicket::find($ticketId);
        $problemId = factory(SdProblem::class)->create()->id;
        $type = 'initiating';
        $ticket->problems()->attach([$problemId => ['type' => $type]]);
        $response = $this->call('GET', self::DEPENDENCY_URL . 'tickets_based_on_problem', ['meta' => true, 'problem_id' => $problemId]);
        $tickets = json_decode($response->content(), true)['data']['tickets'];
        $response->assertStatus(200);
        $limit = Ticket::count();
        $ticketsInDb = Ticket::select('id', 'ticket_number')->take($limit)->get()->toArray();
        for ($ticketIndex=0; $ticketIndex < $limit; $ticketIndex++) { 
            $ticket_name = str_replace(array( '(', ')' ), '', $tickets[$ticketIndex]['name']);
            $this->assertDatabaseHas('tickets', ['id' => $tickets[$ticketIndex]['id'], 'ticket_number' =>  $ticket_name]);
        }
        // default value of limit is 10
        $this->assertCount($limit, $tickets);
    }

    /** @group ticketsBasedOnProblem */
    public function test_ticketsBasedOnProblem_withSearchStringAndWithProblemId_returnsTicketsWithIdAndNameBasedOnPassedTicketNumber()
    {
        $this->getLoggedInUserForWeb('agent');
        $ticketOne = factory(Ticket::class)->create();
        $ticketTwo = factory(Ticket::class)->create();
        $problem = factory(SdChanges::class)->create();
        $ticket = factory(Ticket::class)->create();
        $type = 'initiating';
        $problem->attachTickets()->attach([$ticket->id => ['type' => $type]]);
        $response = $this->call('GET', self::DEPENDENCY_URL . 'tickets_based_on_problem', ['search-query' => $ticketOne->ticket_number, 'problem_id' => $problem->id]);
        $tickets = json_decode($response->content(), true)['data']['tickets'];
        $this->assertCount(1,$tickets);
        $response->assertStatus(200);
    }

    /** @group contracts */
    public function test_contracts_withoutSearchString_returnsContractListWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdContract::class, 5)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'contracts');
        $contracts = json_decode($response->content())->data->contracts;
        $response->assertStatus(200);
        foreach ($contracts as $contract) {
            // only id and name comes in contract list by default
            $this->assertTrue(count((array) $contract) == 2);
            [$identifier, $name] = explode(" ", $contract->name);
            $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => $name, 'identifier' => $identifier]);
        }
        // default value of limit is 10
        $this->assertCount(5, $contracts);
    }

    /** @group contracts */
    public function test_contracts_withMeta_returnsContractListWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdContract::class, 5)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'contracts', ['meta' => 'true']);
        $contracts = json_decode($response->content())->data->contracts;
        $response->assertStatus(200);
        foreach ($contracts as $contract) {
            // only id and name comes in contract list by default
            $this->assertTrue(count((array) $contract) == 2);
            [$identifier, $name] = explode(" ", $contract->name);
            $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => $name, 'identifier' => $identifier]);
        }
        $this->assertCount(SdContract::count(), $contracts);
    }

    /** @group contracts */
    public function test_contracts_withSearchString_returnsContractListWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdContract::class)->create();
        factory(SdContract::class)->create(['name' => 'chair']);
        $response = $this->call('GET', self::DEPENDENCY_URL . 'contracts',['search-query'=>'chair']);
        $response->assertStatus(200);
        $contracts = json_decode($response->content())->data->contracts;
        foreach ($contracts as $contract) {
            // only id and name comes in contract list by default
            $this->assertTrue(count((array) $contract) == 2);
            [$identifier, $name] = explode(" ", $contract->name);
            $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => $name, 'identifier' => $identifier]);
        }
        $this->assertCount(1, $contracts);
    }

    /** @group contracts */
    public function test_contracts_withWrongSearchString_returnsEmptyContractList()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdContract::class)->create(['name' => 'chair']);
        $response = $this->call('GET', self::DEPENDENCY_URL . 'contracts',['search-query'=>'wrong-contract-name']);
        $response->assertStatus(200);
        $contracts = json_decode($response->content())->data->contracts;
        $this->assertEmpty($contracts);
    }

    /** @group contracts */
    public function test_contracts_withConfig_returnsContractListWithIdAndName()
    {
        $this->getLoggedInUserForWeb('admin');
        factory(SdContract::class, 5)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'contracts',['config'=>true]);
        $response->assertStatus(200);
        $contracts = json_decode($response->content())->data->contracts;
        foreach ($contracts as $contract) {
            // totally 20 contract field exists
            $this->assertTrue(count((array) $contract) > 2);
            [$identifier, $name] = explode(" ", $contract->name);
            $this->assertDatabaseHas('sd_contracts', ['id' => $contract->id, 'name' => $name, 'identifier' => $identifier]);
        }
    }

    /** @group contractTypes */
    public function test_contractTypes_withoutSearchString_returnsContractTypeListWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(ContractType::class, 5)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'contract_types');
        $contractTypes = json_decode($response->content())->data->contract_types;
        $response->assertStatus(200);
        foreach ($contractTypes as $contractType) {
            // only id and name comes in contract type list by default
            $this->assertTrue(count((array) $contractType) == 2);
            $this->assertDatabaseHas('sd_contract_types', ['id' => $contractType->id, 'name' => $contractType->name]);
        }
        // default value of limit is 10 and 3 contract types are seeded. So, adding extra 3 while count
        $this->assertCount(5+3, $contractTypes);
    }

    /** @group contractTypes */
    public function test_contractTypes_withMeta_returnsContractTypeListWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(ContractType::class, 5)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'contract_types', ['meta' => 'true']);
        $contractTypes = json_decode($response->content())->data->contract_types;
        $response->assertStatus(200);
        foreach ($contractTypes as $contractType) {
            // only id and name comes in contract type list by default
            $this->assertTrue(count((array) $contractType) == 2);
            $this->assertDatabaseHas('sd_contract_types', ['id' => $contractType->id, 'name' => $contractType->name]);
        }
        $this->assertCount(ContractType::count(), $contractTypes);
    }

    /** @group contractTypes */
    public function test_contractTypes_withSearchString_returnsContractTypeWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(ContractType::class)->create();
        factory(ContractType::class)->create(['name' => 'chair']);
        $response = $this->call('GET', self::DEPENDENCY_URL . 'contract_types',['search-query'=>'chair']);
        $response->assertStatus(200);
        $contractTypes = json_decode($response->content())->data->contract_types;
        foreach ($contractTypes as $contractType) {
            // only id and name comes in contract type list by default
            $this->assertTrue(count((array) $contractType) == 2);
            $this->assertDatabaseHas('sd_contract_types', ['id' => $contractType->id, 'name' => $contractType->name]);
        }
        $this->assertCount(1, $contractTypes);
    }

    /** @group contractTypes */
    public function test_contractTypes_withWrongSearchString_returnsEmptyContractTypeList()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(ContractType::class)->create(['name' => 'chair']);
        $response = $this->call('GET', self::DEPENDENCY_URL . 'contract_types',['search-query'=>'wrong-contract-type-name']);
        $response->assertStatus(200);
        $contractTypes = json_decode($response->content())->data->contract_types;
        $this->assertEmpty($contractTypes);
    }

    /** @group contractTypes */
    public function test_contractTypes_withConfig_returnsContractTypeListWithIdAndName()
    {
        $this->getLoggedInUserForWeb('admin');
        factory(ContractType::class, 5)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'contract_types',['config'=>true]);
        $response->assertStatus(200);
        $contractTypes = json_decode($response->content())->data->contract_types;
        foreach ($contractTypes as $contractType) {
            // totally 4 contract type field exists
            $this->assertTrue(count((array) $contractType) > 2);
            $this->assertDatabaseHas('sd_contract_types', ['id' => $contractType->id, 'name' => $contractType->name]);
        }
    }

    /** @group contractStatuses */
    public function test_contractStatuses_withoutSearchString_returnsContractStatusListWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdContractStatus::class, 5)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'contract_statuses');
        $contractStatuses = json_decode($response->content())->data->contract_statuses;
        $response->assertStatus(200);
        foreach ($contractStatuses as $contractStatus) {
            // only id and name comes in contract status list by default
            $this->assertTrue(count((array) $contractStatus) == 2);
            $this->assertDatabaseHas('sd_contract_statuses', ['id' => $contractStatus->id, 'name' => $contractStatus->name]);
        }
        // default value of limit is 10 and 5 contract statuses are seeded. So, adding extra 5 while count
        $this->assertCount(5+5, $contractStatuses);
    }

    /** @group contractStatuses */
    public function test_contractStatuses_withMeta_returnsContractStatusListWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdContractStatus::class, 5)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'contract_statuses', ['meta' => 'true']);
        $contractStatuses = json_decode($response->content())->data->contract_statuses;
        $response->assertStatus(200);
        foreach ($contractStatuses as $contractStatus) {
            // only id and name comes in contract status list by default
            $this->assertTrue(count((array) $contractStatus) == 2);
            $this->assertDatabaseHas('sd_contract_statuses', ['id' => $contractStatus->id, 'name' => $contractStatus->name]);
        }
        $this->assertCount(SdContractStatus::where('type', 'status')->count(), $contractStatuses);
    }

    /** @group contractStatuses */
    public function test_contractStatuses_withSearchString_returnsContractStatusWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdContractStatus::class)->create();
        factory(SdContractStatus::class)->create(['name' => 'chair']);
        $response = $this->call('GET', self::DEPENDENCY_URL . 'contract_statuses',['search-query'=>'chair']);
        $response->assertStatus(200);
        $contractStatuses = json_decode($response->content())->data->contract_statuses;
        foreach ($contractStatuses as $contractStatus) {
            // only id and name comes in contract status list by default
            $this->assertTrue(count((array) $contractStatus) == 2);
            $this->assertDatabaseHas('sd_contract_statuses', ['id' => $contractStatus->id, 'name' => $contractStatus->name]);
        }
        $this->assertCount(1, $contractStatuses);
    }

    /** @group contractStatuses */
    public function test_contractStatuses_withWrongSearchString_returnsEmptyContractStatusList()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdContractStatus::class)->create(['name' => 'chair']);
        $response = $this->call('GET', self::DEPENDENCY_URL . 'contract_statuses',['search-query'=>'wrong-license-type-name']);
        $response->assertStatus(200);
        $contractStatuses = json_decode($response->content())->data->contract_statuses;
        $this->assertEmpty($contractStatuses);
    }

    /** @group contractStatuses */
    public function test_contractStatuses_withConfig_returnsContractStatusListWithIdAndName()
    {
        $this->getLoggedInUserForWeb('admin');
        factory(SdContractStatus::class, 5)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'contract_statuses',['config'=>true]);
        $response->assertStatus(200);
        $contractStatuses = json_decode($response->content())->data->contract_statuses;
        foreach ($contractStatuses as $contractStatus) {
            // totally 4 contract status field exists
            $this->assertTrue(count((array) $contractStatus) > 2);
            $this->assertDatabaseHas('sd_contract_statuses', ['id' => $contractStatus->id, 'name' => $contractStatus->name]);
        }
    }

    /** @group contractStatuses */
    public function test_contractRenewalStatuses_withoutSearchString_returnsContractRenewalStatusListWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdContractStatus::class, 5)->create(['type' => 'renewal_status']);
        $response = $this->call('GET', self::DEPENDENCY_URL . 'contract_renewal_statuses');
        $contractStatuses = json_decode($response->content())->data->contract_renewal_statuses;
        $response->assertStatus(200);
        foreach ($contractStatuses as $contractStatus) {
            // only id and name comes in contract status list by default
            $this->assertTrue(count((array) $contractStatus) == 2);
            $this->assertDatabaseHas('sd_contract_statuses', ['id' => $contractStatus->id, 'name' => $contractStatus->name, 'type' => 'renewal_status']);
        }
        // default value of limit is 10 and 5 contract statuses are seeded. So, adding extra 5 while count
        $this->assertCount(5+5, $contractStatuses);
    }

    /** @group contractStatuses */
    public function test_contractRenewalStatuses_withMeta_returnsContractRenewalStatusListWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdContractStatus::class, 5)->create(['type' => 'renewal_status']);
        $response = $this->call('GET', self::DEPENDENCY_URL . 'contract_renewal_statuses', ['meta' => 'true']);
        $contractStatuses = json_decode($response->content())->data->contract_renewal_statuses;
        $response->assertStatus(200);
        foreach ($contractStatuses as $contractStatus) {
            // only id and name comes in contract status list by default
            $this->assertTrue(count((array) $contractStatus) == 2);
            $this->assertDatabaseHas('sd_contract_statuses', ['id' => $contractStatus->id, 'name' => $contractStatus->name, 'type' => 'renewal_status']);
        }
        $this->assertCount(SdContractStatus::where('type', 'renewal_status')->count(), $contractStatuses);
    }

    /** @group contractRenewalStatuses */
    public function test_contractRenewalStatuses_withSearchString_returnsContractRenewalStatusWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdContractStatus::class)->create(['type' => 'renewal_status']);
        factory(SdContractStatus::class)->create(['name' => 'chair', 'type' => 'renewal_status']);
        $response = $this->call('GET', self::DEPENDENCY_URL . 'contract_renewal_statuses',['search-query'=>'chair']);
        $response->assertStatus(200);
        $contractStatuses = json_decode($response->content())->data->contract_renewal_statuses;
        foreach ($contractStatuses as $contractStatus) {
            // only id and name comes in contract status list by default
            $this->assertTrue(count((array) $contractStatus) == 2);
            $this->assertDatabaseHas('sd_contract_statuses', ['id' => $contractStatus->id, 'name' => $contractStatus->name, 'type' => 'renewal_status']);
        }
        $this->assertCount(1, $contractStatuses);
    }

    /** @group contractRenewalStatuses */
    public function test_contractRenewalStatuses_withWrongSearchString_returnsEmptyContractRenewalStatusList()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(SdContractStatus::class)->create(['name' => 'chair', 'type' => 'renewal_status']);
        $response = $this->call('GET', self::DEPENDENCY_URL . 'contract_renewal_statuses',['search-query'=>'wrong-license-type-name']);
        $response->assertStatus(200);
        $contractStatuses = json_decode($response->content())->data->contract_renewal_statuses;
        $this->assertEmpty($contractStatuses);
    }

    /** @group contractRenewalStatuses */
    public function test_contractRenewalStatuses_withConfig_returnsContractRenewalStatusListWithIdAndName()
    {
        $this->getLoggedInUserForWeb('admin');
        factory(SdContractStatus::class, 5)->create(['type' => 'renewal_status']);
        $response = $this->call('GET', self::DEPENDENCY_URL . 'contract_renewal_statuses',['config'=>true]);
        $response->assertStatus(200);
        $contractStatuses = json_decode($response->content())->data->contract_renewal_statuses;
        foreach ($contractStatuses as $contractStatus) {
            // totally 4 contract status field exists
            $this->assertTrue(count((array) $contractStatus) > 2);
            $this->assertDatabaseHas('sd_contract_statuses', ['id' => $contractStatus->id, 'name' => $contractStatus->name, 'type' => 'renewal_status']);
        }
    }

    /** @group licenseTypes */
    public function test_licenseTypes_withoutSearchString_returnsLicenseTypeListWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(License::class, 5)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'license_types');
        $licenseTypes = json_decode($response->content())->data->license_types;
        $response->assertStatus(200);
        foreach ($licenseTypes as $licenseType) {
            // only id and name comes in license type list by default
            $this->assertTrue(count((array) $licenseType) == 2);
            $this->assertDatabaseHas('sd_license_types', ['id' => $licenseType->id, 'name' => $licenseType->name]);
        }
        // default value of limit is 10 and 2 license types are seeded. So, adding extra 2 while count
        $this->assertCount(5+2, $licenseTypes);
    }

    /** @group licenseTypes */
    public function test_licenseTypes_withMeta_returnsLicenseTypeListWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(License::class, 5)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'license_types', ['meta' => 'true']);
        $licenseTypes = json_decode($response->content())->data->license_types;
        $response->assertStatus(200);
        foreach ($licenseTypes as $licenseType) {
            // only id and name comes in license type list by default
            $this->assertTrue(count((array) $licenseType) == 2);
            $this->assertDatabaseHas('sd_license_types', ['id' => $licenseType->id, 'name' => $licenseType->name]);
        }
        $this->assertCount(License::count(), $licenseTypes);
    }

    /** @group licenseTypes */
    public function test_licenseTypes_withSearchString_returnsLicenseTypeWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(License::class)->create();
        factory(License::class)->create(['name' => 'chair']);
        $response = $this->call('GET', self::DEPENDENCY_URL . 'license_types',['search-query'=>'chair']);
        $response->assertStatus(200);
        $licenseTypes = json_decode($response->content())->data->license_types;
        foreach ($licenseTypes as $licenseType) {
            // only id and name comes in license type list by default
            $this->assertTrue(count((array) $licenseType) == 2);
            $this->assertDatabaseHas('sd_license_types', ['id' => $licenseType->id, 'name' => $licenseType->name]);
        }
        $this->assertCount(1, $licenseTypes);
    }

    /** @group licenseTypes */
    public function test_licenseTypes_withWrongSearchString_returnsEmptyLicenseTypeList()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(License::class)->create(['name' => 'chair']);
        $response = $this->call('GET', self::DEPENDENCY_URL . 'license_types',['search-query'=>'wrong-license-type-name']);
        $response->assertStatus(200);
        $licenseTypes = json_decode($response->content())->data->license_types;
        $this->assertEmpty($licenseTypes);
    }

    /** @group licenseTypes */
    public function test_licenseTypes_withConfig_returnsLicenseTypeListWithIdAndName()
    {
        $this->getLoggedInUserForWeb('admin');
        factory(License::class, 5)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'license_types',['config'=>true]);
        $response->assertStatus(200);
        $licenseTypes = json_decode($response->content())->data->license_types;
        foreach ($licenseTypes as $licenseType) {
            // totally 4 license type field exists
            $this->assertTrue(count((array) $licenseType) > 2);
            $this->assertDatabaseHas('sd_license_types', ['id' => $licenseType->id, 'name' => $licenseType->name]);
        }
    }

    /** @group tickets */
    public function test_tickets_withoutAssetIdAndExtraParameters_returnsTenTicketsWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(Ticket::class, 14)->create();
        $asset = factory(SdAssets::class)->create();
        $response = $this->call('GET', self::DEPENDENCY_URL . 'tickets');
        $tickets = json_decode($response->content(), true)['data']['tickets'];
        $response->assertStatus(200);
        $limit = 10;
        for ($ticketIndex=0; $ticketIndex < $limit; $ticketIndex++) {
            $this->assertDatabaseHas('tickets', ['id' => $tickets[$ticketIndex]['id']]);
        }
        // default value of limit is 10
        $this->assertCount($limit, $tickets);
    }

    /** @group tickets */
    public function test_tickets_withoutSearchStringAndWithAssetIdAsAttributeValueAndWithRelationNameAndAttributeName_returnsTenTicketsWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(Ticket::class, 14)->create();
        $ticketId = factory(Ticket::class)->create()->id;
        $ticket = SdTicket::find($ticketId);
        $assetId = factory(SdChanges::class)->create()->id;
        $ticket->assets()->sync([$assetId => ['type' => 'sd_assets']]);
        $response = $this->call('GET', self::DEPENDENCY_URL . 'tickets', ['supplements' => ['relation_name' => 'assets', 'attribute_name' => 'type_id', 'attribute_value' => $assetId]]);
        $tickets = json_decode($response->content(), true)['data']['tickets'];
        $response->assertStatus(200);
        $limit = 10;
        for ($ticketIndex=0; $ticketIndex < $limit; $ticketIndex++) {
            $this->assertDatabaseHas('tickets', ['id' => $tickets[$ticketIndex]['id']]);
        }
        // default value of limit is 10
        $this->assertCount($limit, $tickets);
    }

    /** @group tickets */
    public function test_tickets_withMetaAndWithAssetIdAsAttributeValueAndWithRelationNameAndAttributeName_returnsAllTicketsWithIdAndName()
    {
        $this->getLoggedInUserForWeb('agent');
        factory(Ticket::class, 14)->create();
        $ticketId = factory(Ticket::class)->create()->id;
        $ticket = SdTicket::find($ticketId);
        $assetId = factory(SdAssets::class)->create()->id;
        $ticket->assets()->sync([$assetId => ['type' => 'sd_assets']]);
        $response = $this->call('GET', self::DEPENDENCY_URL . 'tickets', ['meta' => true, 'supplements' => ['relation_name' => 'assets', 'attribute_name' => 'type_id', 'attribute_value' => $assetId]]);
        $tickets = json_decode($response->content(), true)['data']['tickets'];
        $response->assertStatus(200);
        $limit = Ticket::count()-1;
        for ($ticketIndex=0; $ticketIndex < $limit; $ticketIndex++) {
            $this->assertDatabaseHas('tickets', ['id' => $tickets[$ticketIndex]['id']]);
        }
        // default value of limit is 10
        $this->assertCount($limit, $tickets);
    }

    /** @group tickets */
    public function test_tickets_withSearchStringAndWithAssetIdAsAttributeValueAndWithRelationNameAndAttributeName_returnsTicketsWithIdAndNameBasedOnPassedTicketNumber()
    {
        $this->getLoggedInUserForWeb('agent');
        $ticketOne = factory(Ticket::class)->create();
        $ticketTwo = factory(Ticket::class)->create();
        $asset = factory(SdAssets::class)->create();
        $ticket = factory(Ticket::class)->create();
        $asset->tickets()->sync([$ticket->id => ['type' => 'sd_assets']]);
        $response = $this->call('GET', self::DEPENDENCY_URL . 'tickets', ['search-query' => $ticketOne->ticket_number, 'supplements' => ['relation_name' => 'assets', 'attribute_name' => 'type_id', 'attribute_value' => $asset->id]]);
        $tickets = json_decode($response->content(), true)['data']['tickets'];
        $this->assertCount(1,$tickets);
        $response->assertStatus(200);
    }
    
}
