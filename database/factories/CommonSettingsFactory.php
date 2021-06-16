<?php

use Faker\Generator as Faker;
use App\Model\helpdesk\Settings\CommonSettings;

$factory->define(CommonSettings::class, function (Faker $faker) {
    return [
        'status' => 1,
        'option_name' => $factory->name,
        'option_value' => $factory->name,
        'optional_field' => $factory->name
    ];
});
