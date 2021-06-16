<?php

use App\Plugins\ServiceDesk\Model\Changes\SdChanges;
use App\User;

$factory->define(SdChanges::class, function () {
    static $order = 1;
    return [
    	'requester_id' => factory(User::class)->create()->id,
        'subject' => str_random(),
        'description' => str_random(),
        'status_id' => 1,
        'priority_id' => 1,
        'change_type_id' => 1,
        'impact_id' => 1,
        'location_id' => 1,
        'identifier' => $order++
    ];
});