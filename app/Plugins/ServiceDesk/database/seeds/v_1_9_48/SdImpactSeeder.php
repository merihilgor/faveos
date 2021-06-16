<?php
namespace App\Plugins\ServiceDesk\database\seeds\v_1_9_48;
use database\seeds\DatabaseSeeder as Seeder;
use App\Plugins\ServiceDesk\Model\Assets\SdImpactypes as Impact;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class SdImpactSeeder extends Seeder {

    public function run() {

        $names = ['Low', 'Medium', 'High'];
        foreach($names as $name){
            Impact::updateOrCreate(
                ['name' => $name],
                ['name' => $name]
            );
        }
    }
}
