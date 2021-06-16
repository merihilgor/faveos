<?php

namespace App\Plugins\ServiceDesk\database\seeds\v_1_9_48;

use database\seeds\DatabaseSeeder as Seeder;
use DB;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class SdProductStatus extends Seeder {

    public function run() {
        $created_at = date('Y-d-m H:m:i');
        $updated_at = date('Y-d-m H:m:i');

        DB::table('sd_product_status')
                ->insert(['name' => 'In Pipeline',
                    'created_at' => $created_at,
                    'updated_at' => $updated_at,
        ]);
        DB::table('sd_product_status')
                ->insert(['name' => 'In Production',
                    'created_at' => $created_at,
                    'updated_at' => $updated_at,
        ]);
        DB::table('sd_product_status')
                ->insert(['name' => 'Retired',
                    'created_at' => $created_at,
                    'updated_at' => $updated_at,
        ]);
        
    }

}
