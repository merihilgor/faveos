<?php

use App\Plugins\ServiceDesk\Model\Products\SdProductstatus;

$factory->define(SdProductstatus::class, function (){
 	 return [
 	 	   "name"=> str_random(),
 	 ];

 });