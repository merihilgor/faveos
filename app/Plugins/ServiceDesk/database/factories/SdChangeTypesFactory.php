<?php

use App\Plugins\ServiceDesk\Model\Changes\SdChangetypes;

$factory->define(SdChangetypes::class, function () {
    return [
    	'name' => str_random(),
    ];
});