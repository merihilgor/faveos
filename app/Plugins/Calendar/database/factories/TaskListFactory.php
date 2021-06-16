<?php

use App\Plugins\Calendar\Model\TaskList;
use Faker\Generator as Faker;

$factory->define(TaskList::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'project_id' => 1
    ];
});
