<?php

use App\Plugins\ServiceDesk\Model\Changes\SdChangestatus;

$factory->define(SdChangestatus::class, function () {
    return [
    	'name' => str_random(),
    ];
});