<?php

use App\Plugins\ServiceDesk\Model\Releases\SdReleasestatus;

$factory->define(SdReleasestatus::class, function () {
    return [
    	'name' => str_random(),
    ];
});