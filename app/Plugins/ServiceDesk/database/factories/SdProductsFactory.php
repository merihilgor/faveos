<?php

use App\Plugins\ServiceDesk\Model\Products\SdProducts;

$factory->define(SdProducts::class, function () {
    return [
    	'name' => str_random(),
      	'description' => str_random(),
      	'manufacturer' => str_random(),
      	'product_status_id' => 1,
      	'product_mode_procurement_id' => 1,
      	'all_department' => 1,
      	'status' => 1,
      	'asset_type_id' => 1,
    ];
});
