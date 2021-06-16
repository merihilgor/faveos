<?php

use App\Plugins\ServiceDesk\Model\Procurment\SdProcurment;

$factory->define(SdProcurment::class, function (){
	return [
		   "name"=> str_random(),
	];

});

