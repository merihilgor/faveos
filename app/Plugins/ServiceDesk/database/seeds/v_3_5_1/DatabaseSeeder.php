<?php

namespace App\Plugins\ServiceDesk\database\seeds\v_3_5_1;

use database\seeds\DatabaseSeeder as Seeder;
use App\Plugins\ServiceDesk\Model\Assets\SdAssetStatus;
use App\Model\helpdesk\Form\FormField;
use App\Model\helpdesk\Form\FormFieldLabel;
use App\Model\helpdesk\Form\FormCategory;
use App\Plugins\ServiceDesk\database\seeds\v_3_5_0\AssetFormSeeder;
use App\Model\Common\Template;
use App\Model\MailJob\Condition;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->assetStatusSeeder();
        $this->addAssetStatusInAssetForm();
        $this->updateContractRejectedTemplate();
        $this->ContractStatusActiveSeeder();
    }

    /**
     * method to add asset status seeder
     * @return null
     */
    private function assetStatusSeeder()
    {
        SdAssetStatus::updateOrCreate(['name' => 'In Use'], ['name' => 'In Use', 'description' => 'Assets in use']);
        SdAssetStatus::updateOrCreate(['name' => 'Missing'], ['name' => 'Missing', 'description' => 'Assets missing']);
        SdAssetStatus::updateOrCreate(['name' => 'In Stock'], ['name' => 'In Stock', 'description' => 'Assets in stock']);
        SdAssetStatus::updateOrCreate(['name' => 'Dispose'], ['name' => 'Dispose', 'description' => 'Assets which could be disposed']);
        SdAssetStatus::updateOrCreate(['name' => 'Maintenance'], ['name' => 'Maintenance', 'description' => 'Assets under maintenance']);
        SdAssetStatus::updateOrCreate(['name' => 'Move'], ['name' => 'Move', 'description' => 'Assets which need to moved from one location/organization/department to another location/organization/department']);
    }

    /**
    * method to seed asset form
    * 
    * @return null
    */
    private function addAssetStatusInAssetForm()
    {
        $category = FormCategory::where([['category', 'asset'], ['type', 'servicedesk'], ['name', 'Asset']])->first();
        if ($category) {
          $sortOrder = FormField::where([['category_id', $category->id], ['category_type', FormCategory::class], ['api_info', 'url:=/service-desk/api/dependency/asset_types?meta=true;;']])->first()->sort_order;
          $form['category_id'] = $category->id;
          $form['title'] = 'Asset Status';
          $form['type'] = 'api';
          $form['required_for_agent'] = false;
          $form['unique'] = 'status_id';
          $form['is_agent_config'] = true;
          $form['api_info'] = 'url:=/service-desk/api/dependency/asset_statuses;;';
          $form['sort_order'] = $sortOrder;

          (new AssetFormSeeder)->formFieldDefaultParameters($form);
          $formFieldId = FormField::updateOrCreate($form)->id;
          $formFieldLabel['labelable_id'] = $formFieldId;
          $formFieldLabel['label'] = $form['title'];
          (new AssetFormSeeder)->formFieldLabelDefaultParameters($formFieldLabel);
          FormFieldLabel::updateOrCreate($formFieldLabel);


        }
    }

    /**
     * method to update contract rejected template seeder
     * @return null
     */
    private function updateContractRejectedTemplate()
    {
        Template::where('name', 'template-contract-rejected-by-agent-or-admin')->update([
            'message' => 'Hello {!! $receiver_name !!} <br/> <br/>'
                        .'Contract {!! $contract_name !!} (#CNTR-{!! $contract_id !!}) has been rejected by {!! $approver_name !!}. <br/>'
                        .'Purpose of Rejection : {!! $contract_reason_rejection !!} <br/>'
                        .'You can go through the contract and take necessary actions. <br/> <br/>'
                        .'Contract : {!! $contract_link !!} <br/> <br/> <br/> <br/> <br/> <br/> <br/> <br/> '
                        .'Kind Regards, <br>'
                        .'{!! $company_name !!}'
        ]);
    }

    /**
     * method to delete contract-status-active and contract-status-expired job
     * @return null
     */
    private function ContractStatusActiveSeeder()
    {
        Condition::where(['job' => 'contract-status-active'])->delete();
        Condition::where(['job' => 'contract-status-expired'])->delete();
    }
}
