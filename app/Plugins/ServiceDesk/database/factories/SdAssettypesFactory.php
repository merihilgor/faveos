<?php

use App\Plugins\ServiceDesk\Model\Assets\SdAssettypes;

$factory->define(SdAssettypes::class, function (){
 	 return [
 	 	   "name" => str_random(),
 	 	   "parent_id" => 1,
 	 ];
 });
