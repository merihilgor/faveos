<?php

use App\Plugins\ServiceDesk\Model\Vendor\SdVendors;
use Faker\Generator as Faker;

$factory->define(SdVendors::class, function (Faker $faker) {
    return [
    	'name' => str_random(),
    	'primary_contact' => str_random(10),
    	'email' => $faker->unique()->safeEmail,
	  	'status' => 1,
	  	'description' => str_random(),
	  	'address' => str_random()
    ];
});
