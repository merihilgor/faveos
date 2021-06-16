<?php

namespace App\Plugins\ServiceDesk\database\seeds\v_1_9_48;

use App\Plugins\ServiceDesk\database\seeds\v_1_9_48\SdAssetTypeSeeder;
use App\Plugins\ServiceDesk\database\seeds\v_1_9_48\SdImpactSeeder;
use App\Plugins\ServiceDesk\database\seeds\v_1_9_48\SdProductProcMode;
use App\Plugins\ServiceDesk\database\seeds\v_1_9_48\SdAssetAttachmentTypes;
use App\Plugins\ServiceDesk\database\seeds\v_1_9_48\SdContractTypes;
use App\Plugins\ServiceDesk\database\seeds\v_1_9_48\SdLicenseTypes;
use App\Plugins\ServiceDesk\database\seeds\v_1_9_48\SdChangePriority;
use App\Plugins\ServiceDesk\database\seeds\v_1_9_48\SdChangeStatus;
use App\Plugins\ServiceDesk\database\seeds\v_1_9_48\SdChangeType;
use App\Plugins\ServiceDesk\database\seeds\v_1_9_48\SdReleasePriority;
use App\Plugins\ServiceDesk\database\seeds\v_1_9_48\SdReleaseStatus;
use App\Plugins\ServiceDesk\database\seeds\v_1_9_48\SdReleaseType;
use App\Plugins\ServiceDesk\database\seeds\v_1_9_48\SdProductStatus;
use database\seeds\DatabaseSeeder as Seeder;
use DB;

class DatabaseSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        if(!$this->wasSeederRanBefore()){

          $seed = new SdAssetTypeSeeder();
          $seed->run();

          $seed1 = new SdImpactSeeder();
          $seed1->run();

          $seed3 = new SdAssetAttachmentTypes();
          $seed3->run();

          $seed4 = new SdContractTypes();
          $seed4->run();

          $seed5 = new SdLicenseTypes();
          $seed5->run();

          // $seed6 = new SdLicenseTypes();
          // $seed6->run();

          $seed7 = new SdProductProcMode();
          $seed7->run();

          $seed8 = new SdChangePriority();
          $seed8->run();

          $seed9 = new SdChangeType();
          $seed9->run();

          $seed10 = new SdChangeStatus();
          $seed10->run();

          $seed11 = new SdReleasePriority();
          $seed11->run();

          $seed12 = new SdReleaseStatus();
          $seed12->run();

          $seed13 = new SdReleaseType();
          $seed13->run();

          $seed14 = new SdProductStatus();
          $seed14->run();
        }
    }

    /**
     * Checks if seeder was already ran before that.
     * NOTE: this will not be required in later versions
     * if any data is there in the DB, it should not run these seeders
     * REASON : If somebody activates ServiceDesk and deactivat, it in older version before v.1.9.48,
     *          he still will have seeding data and version will be 0.0.0, so in that case this seeder will run again.
     *          To avoid that we need to put a check that there database tables are empty
     * @return Boolean
     */
    private function wasSeederRanBefore()
    {
        return (bool)DB::table('sd_asset_types')->count();
    }

}
