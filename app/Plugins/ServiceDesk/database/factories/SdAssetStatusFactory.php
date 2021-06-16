<?php

use App\Plugins\ServiceDesk\Model\Assets\SdAssetStatus;

$factory->define(SdAssetStatus::class, function (){
 	 return [
 	 	   "name" => str_random(),
 	 	   "description" => str_random()
 	 ];

 });

