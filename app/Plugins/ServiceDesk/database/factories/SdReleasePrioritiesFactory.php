<?php

use App\Plugins\ServiceDesk\Model\Releases\SdReleasepriorities;

$factory->define(SdReleasepriorities::class, function () {
    return [
    	'name' => str_random(),
    ];
});