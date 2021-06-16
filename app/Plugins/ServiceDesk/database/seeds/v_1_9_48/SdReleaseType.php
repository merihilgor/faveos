<?php

namespace App\Plugins\ServiceDesk\database\seeds\v_1_9_48;

use database\seeds\DatabaseSeeder as Seeder;
use DB;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class SdReleaseType extends Seeder {

    public function run() {
         if (!DB::table('sd_release_types')
                        ->where('name', "Minor")
                        ->first()) {
        $created_at = date('Y-d-m H:m:i');
        $updated_at = date('Y-d-m H:m:i');

        DB::table('sd_release_types')
                ->insert(['name' => 'Minor',
                    'created_at' => $created_at,
                    'updated_at' => $updated_at,
        ]);
        DB::table('sd_release_types')
                ->insert(['name' => 'Standard',
                    'created_at' => $created_at,
                    'updated_at' => $updated_at,
        ]);
        DB::table('sd_release_types')
                ->insert(['name' => 'Major',
                    'created_at' => $created_at,
                    'updated_at' => $updated_at,
        ]);
        DB::table('sd_release_types')
                ->insert(['name' => 'Emergency',
                    'created_at' => $created_at,
                    'updated_at' => $updated_at,
        ]);
        
    }
}

}
