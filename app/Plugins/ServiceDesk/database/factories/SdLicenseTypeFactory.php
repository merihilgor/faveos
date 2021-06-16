<?php

use App\Plugins\ServiceDesk\Model\Contract\License;

$factory->define(License::class, function (){
 	 return [
 	 	   "name"=> str_random(),
 	 ];

 });

