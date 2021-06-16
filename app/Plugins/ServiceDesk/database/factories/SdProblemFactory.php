<?php

use App\Plugins\ServiceDesk\Model\Problem\SdProblem;
use App\User;

$factory->define(SdProblem::class, function () {
    static $order = 1;
    return [
    	'requester_id' => factory(User::class)->create()->id,
        'subject' => str_random(),
        'department_id' => 1,
        'description' => str_random(),
        'status_type_id' => 1,
        'priority_id' => 1,
        'impact_id' => 1,
        'location_id' => 1,
        'assigned_id'=>1,
        'identifier' => $order++,
    ];
});
