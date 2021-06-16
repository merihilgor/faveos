<?php

use App\Plugins\ServiceDesk\Model\Contract\SdContract;
use App\Plugins\ServiceDesk\Model\Vendor\SdVendors;

$factory->define(SdContract::class, function () {
	static $order = 1;
    return [
    	'name' => str_random(),
	  	'status_id' => 3,
	  	'contract_type_id' => 1,
	  	'cost' => 77777,
	  	'approver_id' => 1,
	  	'owner_id' => 1,
	  	'description' => str_random(),
	  	'vendor_id' => factory(SdVendors::class)->create()->id,
	  	'contract_start_date' => date('Y-m-d h:i:s', strtotime(date('Y-m-d h:i:s') . '-2 day')),
	  	'contract_end_date' => date('Y-m-d h:i:s', strtotime(date('Y-m-d h:i:s') . '+100 day')),
	  	'identifier' => $order++
    ];
});
