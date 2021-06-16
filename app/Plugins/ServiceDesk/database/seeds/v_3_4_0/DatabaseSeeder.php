<?php

namespace App\Plugins\ServiceDesk\database\seeds\v_3_4_0;


use database\seeds\DatabaseSeeder as Seeder;
use App\Plugins\ServiceDesk\Model\Releases\SdReleases;
use App\Plugins\ServiceDesk\Model\Changes\SdChanges;
use App\Plugins\ServiceDesk\Model\Contract\SdContractStatus;
use App\Plugins\ServiceDesk\Model\Contract\SdContract;
use App\Plugins\ServiceDesk\Model\Problem\SdProblem;
use App\User;
use DB;
use App\Plugins\ServiceDesk\Model\Assets\BarcodeTemplate;
use App\Plugins\ServiceDesk\Model\Common\Attachments;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $this->releaseIdentifierSeeder();
      $this->changeIdentifierSeeder();
      $this->contractStatusesSeederUpdate();
      $this->contractIdentifierSeeder();
      $this->seedProblemIdentifierValue();
      $this->seedProblemRequesterIdValue();
      $this->addDefaultEntryForBarcodeSettings();
    }

    /**
     * method to add release identifier seeder
     * @return null
     */
    private function releaseIdentifierSeeder()
    {
      $releases = SdReleases::get();
      $releaseCount = 0;
      foreach ($releases as $release) {
        DB::table('sd_releases')->where('id',$release->id)->update(['identifier' => 'REL-'.++$releaseCount]);
      }
    }

    /**
     * method to add identifier in change through seeder
     * @return null
     */
    private function changeIdentifierSeeder()
    {
      $changes = SdChanges::get();
      $changeCount = 0;
      foreach ($changes as $change) {
        DB::table('sd_changes')->where('id',$change->id)->update(['identifier' => 'CHN-'.++$changeCount]);
      }
    }

    /**
     * Template Short Codes Seeder
     * @return void
     */
    private function contractStatusesSeederUpdate()
    {
      $sdContractStatus = new SdContractStatus();
      $sdContractStatus->where('name', 'draft')->update(['name' => 'Draft', 'type' => 'status']);
      $sdContractStatus->where('name', 'approved')->update(['name' => 'Approved', 'type' => 'status']);
      $sdContractStatus->where('name', 'active')->update(['name' => 'Active', 'type' => 'status']);
      $sdContractStatus->where('name', 'terminated')->update(['name' => 'Terminated', 'type' => 'status']);
      $sdContractStatus->where('name', 'expired')->update(['name' => 'Expired', 'type' => 'status']);
      $sdContractStatus->where('name', 'rejected')->update(['name' => 'Rejected', 'type' => 'status']);
      $sdContractStatus->where('name', 'renewal approval request')->update(['name' => 'Renewal Approval Request', 'type' => 'renewal_status']);
      $sdContractStatus->where('name', 'renewed')->update(['name' => 'Renewed', 'type' => 'renewal_status']);
      $sdContractStatus->where('name', 'renewal rejected')->update(['name' => 'Renewal Rejected', 'type' => 'renewal_status']);
      $sdContractStatus->where('name', 'extension approval request')->update(['name' => 'Extension Approval Request', 'type' => 'renewal_status']);
      $sdContractStatus->where('name', 'extension rejected')->update(['name' => 'Extension Rejected', 'type' => 'renewal_status']);
      $sdContractStatus->where('name', 'extended')->update(['name' => 'Extended', 'type' => 'renewal_status']);

    }

    /**
     * method to add contract identifier seeder
     * @return null
     */
    private function contractIdentifierSeeder()
    {
      $contractObject = new SdContract();
      $contracts = $contractObject->get();
      $contractIdentifierCount = $this->findMaxDefaultContractIdentifierCount($contractObject);
      foreach ($contracts as $contract) {
        if ($contract->identifier) {
          continue;
        }
        DB::table('sd_contracts')->where('id',$contract->id)->update(['identifier' => 'CNTR-'.++$contractIdentifierCount]);
      }
    }

    /** 
     * method to find maximum default contract identifier value
     * @param SdContract $contract
     * @return integer $identifierCount
     */
    private function findMaxDefaultContractIdentifierCount($contract)
    {
      $contractQuery = $contract->where('identifier' ,'LIKE',"%CNTR%")->get()->last();
      $identifierCount = ($contractQuery) ? abs((int) filter_var($contractQuery->identifier, FILTER_SANITIZE_NUMBER_INT)) : 0;

      return $identifierCount;
    }

    /**
     * method to seed default identifier values in sd_problem table
     * @return null
     */
    private function seedProblemIdentifierValue()
    {
      $problems = SdProblem::get();
      $problemCount = 0;
      foreach ($problems as $problem) {
        $problem->update(['identifier' => 'PRB-'.++$problemCount]);
      }

    }

    /**
     * method to seed requester values in sd_problem table when table column from is changed to requester_id
     * @return null
     */
    private function seedProblemRequesterIdValue()
    {
      $problems = SdProblem::get();
      foreach ($problems as $problem) {
        $user =  User::where('email',$problem->requester_id)->first();
        if ($user) {
          DB::table('sd_problem')->where('id',$problem->id)->update(['requester_id' => $user->id]);
        }
      }
    }

    private function addDefaultEntryForBarcodeSettings()
    {
        if (!BarcodeTemplate::count()) {
            BarcodeTemplate::create([
                'width' => '2',
                'height' => '1',
                'labels_per_row' => '4',
                'space_between_labels' => '4',
                'display_logo_confirmed' => 0
            ]);
        }
        //this renames the attachments related to barcode_templates to sd_barcode_templates.
        $existingAttachments = Attachments::where('owner', 'LIKE', 'barcode_templates%')->get();
        if ($existingAttachments->count()) {
            foreach ($existingAttachments as $existingAttachment) {
                if (($position = strpos($existingAttachment->owner, ":")) !== false) {
                    $id = substr($existingAttachment->owner, $position+1);
                    $existingAttachment->owner = "sd_barcode_templates:".$id;
                    $existingAttachment->save();
                }
            }
        }
    }
}
