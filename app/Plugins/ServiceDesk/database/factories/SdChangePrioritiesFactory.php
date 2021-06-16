<?php

use App\Plugins\ServiceDesk\Model\Changes\SdChangepriorities;

$factory->define(SdChangepriorities::class, function () {
    return [
    	'name' => str_random(),
    ];
});