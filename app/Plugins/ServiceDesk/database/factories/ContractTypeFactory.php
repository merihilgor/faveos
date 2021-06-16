<?php

use App\Plugins\ServiceDesk\Model\Contract\ContractType;

$factory->define(ContractType::class, function (){
 	 return [
 	 	   "name"=> str_random(),
 	 ];

 });

