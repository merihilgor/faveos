<?php

use Faker\Generator as Faker;
use Illuminate\Http\UploadedFile;
use App\Plugins\ServiceDesk\Model\Assets\BarcodeTemplate;

$factory->define(BarcodeTemplate::class, function (Faker $faker){
 	 return [
        'width' => '2',
        'height' => '21',
        'labels_per_row' => '5',
        'space_between_labels' => '5',
        'display_logo_confirmed' => 1,
        'logo_image' => UploadedFile::fake()->image('avatar.jpg')
 	 ];
 });
