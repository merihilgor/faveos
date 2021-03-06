<?php

namespace App\Plugins\ServiceDesk\database\seeds\v_1_9_48;

use database\seeds\DatabaseSeeder as Seeder;
use DB;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class SdReleasePriority extends Seeder {

    public function run() {
              if (!DB::table('sd_release_priorities')
                        ->where('name', "Medium")
                        ->first()) {
        $created_at = date('Y-d-m H:m:i');
        $updated_at = date('Y-d-m H:m:i');

        DB::table('sd_release_priorities')
                ->insert(['name' => 'Low',
                    'created_at' => $created_at,
                    'updated_at' => $updated_at,
        ]);
        DB::table('sd_release_priorities')
                ->insert(['name' => 'Medium',
                    'created_at' => $created_at,
                    'updated_at' => $updated_at,
        ]);
        DB::table('sd_release_priorities')
                ->insert(['name' => 'High',
                    'created_at' => $created_at,
                    'updated_at' => $updated_at,
        ]);
        DB::table('sd_release_priorities')
                ->insert(['name' => 'Urgent',
                    'created_at' => $created_at,
                    'updated_at' => $updated_at,
        ]);
        
        
    }
}

}
