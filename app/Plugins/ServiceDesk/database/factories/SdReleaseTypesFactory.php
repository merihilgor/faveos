<?php

use App\Plugins\ServiceDesk\Model\Releases\SdReleasetypes;

$factory->define(SdReleasetypes::class, function () {
    return [
    	'name' => str_random(),
    ];
});