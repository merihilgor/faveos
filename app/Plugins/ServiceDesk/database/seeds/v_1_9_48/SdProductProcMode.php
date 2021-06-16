<?php
namespace App\Plugins\ServiceDesk\database\seeds\v_1_9_48;
use database\seeds\DatabaseSeeder as Seeder;
use DB;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class SdProductProcMode extends Seeder{
    public function run() {
        $names = ['Buy','Lease'];
        $created_at = date('Y-d-m H:m:i');
        $updated_at = date('Y-d-m H:m:i');
        foreach($names as $name){
            DB::table('sd_product_proc_mode')
                    ->insert(['name'=>$name,
                'created_at'=>$created_at,
                'updated_at'=>$updated_at,
                ]);
        }
    }
}