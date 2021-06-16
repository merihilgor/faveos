<?php

namespace App\Plugins\ServiceDesk\database\seeds\v_3_5_0;

use database\seeds\DatabaseSeeder as Seeder;
use DB;
use App\Plugins\ServiceDesk\database\seeds\v_3_5_0\AssetFormSeeder;
use App\Plugins\ServiceDesk\database\seeds\v_3_5_0\UpgradeOldFormSeeder;
use App\Plugins\ServiceDesk\database\seeds\v_3_5_0\UpgradeAssetFormSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->deleteCreaterNullActivityLog();
        $this->seedProblemIdentifierValue();
        $this->seedChangesIdentifierValue();
        $this->seedReleasesIdentifierValue();
        (new AssetFormSeeder)->assetFormSeed();
        (new UpgradeOldFormSeeder)->convertOldFormToNewFormSeeder();
        (new UpgradeAssetFormSeeder)->convertOldAssetFormToNewAssetFormSeeder();
    }

    /**
     * method to delete problem identifier activity log where causer_id and cauesr_type is null as it will cause error in Activity log
     * @return null
     */
    private function deleteCreaterNullActivityLog()
    {
        DB::table('sd_activity_logs')->whereNull(['causer_id','causer_type'])->delete();
    }

    /**
     * method to seed default identifier values in sd_problem table
     * @return null
     */
    private function seedProblemIdentifierValue()
    {
      $problems = DB::table('sd_problem')->get();
      foreach ($problems as $problem) {
        $identifier = implode('', ['PRB-', $problem->id]);
        DB::table('sd_problem')->where('id', $problem->id)->update(['identifier' => $identifier]);
      }

    }

    /**
     * method to seed default identifier values in sd_changes table
     * @return null
     */
    private function seedChangesIdentifierValue()
    {
      $changes = DB::table('sd_changes')->get();
      foreach ($changes as $change) {
        $identifier = implode('', ['CHN-', $change->id]);
        DB::table('sd_changes')->where('id', $change->id)->update(['identifier' => $identifier]);
      }

    }

    /**
     * method to seed default identifier values in sd_releases table
     * @return null
     */
    private function seedReleasesIdentifierValue()
    {
      $releases = DB::table('sd_releases')->get();
      foreach ($releases as $release) {
        $identifier = implode('', ['REL-', $release->id]);
        DB::table('sd_releases')->where('id', $release->id)->update(['identifier' => $identifier]);
      }

    }
}
