<?php

use App\Plugins\ServiceDesk\Model\Contract\SdContractStatus;
use Faker\Generator as Faker;

$factory->define(SdContractStatus::class, function (Faker $faker) {
    return ['name' => $faker->name, 'type' => 'status'];
});
