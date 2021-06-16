<?php

namespace App\Plugins\ServiceDesk\tests\Backend\Controllers;

use App\Plugins\ServiceDesk\Model\Assets\SdAssettypes;
use Tests\AddOnTestCase;

/** Testing Asset Type Controller For Asset Type Delete API */

class AssetTypeControllerTest extends AddOnTestCase
{   
    /** @group assetHandledelete */
    public function test_assetHandledelete_withoutDefaultAssetType()
    {
      $this->getLoggedInUserForWeb('admin');
      $assetTypeId = 2;
      $response  = $this->call('GET',URL("service-desk/assetstypes/$assetTypeId/delete"));
      $this->assertDatabaseMissing('sd_asset_types', ['id' => $assetTypeId]);
      $response->assertStatus(302);
    }

    /** @group assetHandledelete */
    public function test_assetHandledelete_withoutDefaultAssetTypeAndAsset()
    {
      $this->getLoggedInUserForWeb('admin');
      $assetTypeId = 2;
      $this->call('post',URL('service-desk/assets/post/create'), [
        'name' => 'Laptop',
        'department_id' => 2,
        'used_by' => 1,
        'managed_by' => 1,
        'asset_type_id' => 2,
        'description' => 'HP Laptop'
      ]);
      $response  = $this->call('GET',URL("service-desk/assetstypes/$assetTypeId/delete"));
      $this->assertDatabaseMissing('sd_asset_types', ['id' => $assetTypeId]);
      $this->assertDatabaseHas('sd_assets', ['name' => 'Laptop', 'asset_type_id' => 1]);
      $response->assertStatus(302);
    }

    /** @group assetHandledelete */
    public function test_assetHandledelete_withDefaultAssetType()
    {
      $this->getLoggedInUserForWeb('admin');
      $assetTypeId = SdAssettypes::first()->id;
      $response  = $this->call('GET',URL("service-desk/assetstypes/$assetTypeId/delete"));
      $this->assertDatabaseHas('sd_asset_types', ['id' => $assetTypeId]);
      $response->assertStatus(302);
    }

    /** @group assetHandledelete */
    public function test_assetHandledelete_withDefaultAssetTypeAndAsset()
    {
      $this->getLoggedInUserForWeb('admin');
      $assetTypeId = SdAssettypes::first()->id;
      $this->call('post',URL('service-desk/assets/post/create'), [
        'name' => 'Laptop',
        'department_id' => 2,
        'used_by' => 1,
        'managed_by' => 1,
        'asset_type_id' => 1,
        'description' => 'HP Laptop'
      ]);
      $response  = $this->call('GET',URL("service-desk/assetstypes/$assetTypeId/delete"));
      $this->assertDatabaseHas('sd_asset_types', ['id' => $assetTypeId]);
      $this->assertDatabaseHas('sd_assets', ['name' => 'Laptop', 'asset_type_id' => 1]);
      $response->assertStatus(302);
    }

     /** @group assetHandledelete */
    public function test_assetHandledelete_withWrongAssetType()
    {
      $this->getLoggedInUserForWeb('admin');
      $assetTypeId = 100;
      $response  = $this->call('GET',URL("service-desk/assetstypes/$assetTypeId/delete"));
      $response->assertStatus(302);
    }



 }
