<?php

use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\Model\helpdesk\Agent_panel\Organization;
use App\Plugins\ServiceDesk\Model\Products\SdProducts;

$factory->define(SdAssets::class, function () {
	static $order = 1;
    return [
    	'name' => str_random(),
	  	'identifier' => $order++,
	  	'organization_id' => factory(Organization::class)->create()->id,
	  	'description' => str_random(),
	  	'department_id' => 1,
	  	'asset_type_id' => 1,
	  	'product_id' => factory(SdProducts::class)->create()->id,
	  	'impact_type_id' => 1,
	  	'managed_by_id' => 1,
	  	'used_by_id' => 1,
	  	'location_id' => 1,
	  	'assigned_on' => '2018-07-12 05:12:00',
	  	'status_id' => 1
    ];
});
