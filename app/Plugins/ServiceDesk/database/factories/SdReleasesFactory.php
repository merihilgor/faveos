<?php

use App\Plugins\ServiceDesk\Model\Releases\SdReleases;
use App\User;

$factory->define(SdReleases::class, function () {
    static $order = 1;
    return [
        'subject' => str_random(),
        'description' => str_random(),
        'status_id' => 1,
        'priority_id' => 1,
        'release_type_id' => 1,
        'location_id' => 1,
        'planned_start_date' => '2018-07-12 05:12:00',
        'planned_end_date' => '2019-07-12 05:12:00',
        'identifier' => $order++,
    ];
});