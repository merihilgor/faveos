<?php

namespace App\Plugins\ServiceDesk\database\seeds\v_1_9_53;

use database\seeds\DatabaseSeeder as Seeder;
use App\Plugins\ServiceDesk\Model\Contract\SdContractStatus;
use App\Model\Common\TemplateType;
use App\Model\Common\Template;
use App\Model\helpdesk\Settings\Alert;
use App\Model\MailJob\Condition;
use App\Plugins\ServiceDesk\Model\Common\SdDefault;
use App\Model\Common\TemplateSet;
use App\Model\Common\TemplateShortCode;
use Lang;

/**
 * Seeder for maintaing contract status, alert, template type and template and default asset type value
 *
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $this->contractStatusSeeder();
      $this->alertSeeder();
      $this->templateShortCodesSeeder();
      $this->templateTypeSeeder();
      $this->templateSeeder();;
      $this->contractExpiryNotifySeeder();
      $this->contractStatusActiveSeeder();
      $this->contractStatusExpiredSeeder();
      $this->sdDefaultSeeder();
    }

    /**
     * Contract Status Seeder
     * @return 
     */
    private function contractStatusSeeder()
    {
      SdContractStatus::updateOrCreate(['name' => 'draft'], ['name' => 'draft']);
      SdContractStatus::updateOrCreate(['name' => 'approved'], ['name' => 'approved']);
      SdContractStatus::updateOrCreate(['name' => 'active'], ['name' => 'active']);
      SdContractStatus::updateOrCreate(['name' => 'terminated'], ['name' => 'terminated']);
      SdContractStatus::updateOrCreate(['name' => 'expired'], ['name' => 'expired']);
      SdContractStatus::updateOrCreate(['name' => 'rejected'], ['name' => 'rejected']);
      SdContractStatus::updateOrCreate(['name' => 'renewal approval request'], ['name' => 'renewal approval request']);
      SdContractStatus::updateOrCreate(['name' => 'renewed'], ['name' => 'renewed']);
      SdContractStatus::updateOrCreate(['name' => 'renewal rejected'],['name' => 'renewal rejected']);
      SdContractStatus::updateOrCreate(['name' => 'extension approval request'], ['name' => 'extension approval request']);
      SdContractStatus::updateOrCreate(['name' => 'extension rejected'], ['name' => 'extension rejected']);
      SdContractStatus::updateOrCreate(['name' => 'extended'], ['name' => 'extended']);
    }

    /**
     * Alert And Notice seeder
     * @return 
     */
    private function alertSeeder()
    {
      $alert = ['approved_contract', 'rejected_contract'];
      $append = ['', '_mode', '_persons'];
      $values = [1, 'email,system', 'agent,admin'];
      for ($templateType = 0, $appendType = 0, $value = 0; $templateType < count($alert); $appendType++, $value++) { 
          if ($appendType < count($append)) {
              Alert::create(['key' => $alert[$templateType] . $append[$appendType], 'value' => $values[$value]]);
          }
          else {
              $appendType = -1;
              $value = -1;
              ++$templateType;
          }
      }
    }

    /**
     * Template Short Codes Seeder
     * @return 
     */
    private function templateShortCodesSeeder()
    {
      $shortCodeKeys = [
        ['key_name' => 'contract_id'],
        ['key_name' => 'contract_name'],
        ['key_name' => 'contract_link'],
        ['key_name' => 'approver_name'],
        ['key_name' => 'contract_expiry'],
        ['key_name' => 'contract_reason_rejection']
      ];

      foreach ($shortCodeKeys as $shortCodeKey) {
        TemplateShortCode::updateOrCreate(['key_name' => $shortCodeKey['key_name']], [
          'plugin_name'          => 'ServiceDesk',
          'shortcode'            => '{!! $' . $shortCodeKey['key_name'] . ' !!}',           
          'description_lang_key' => 'ServiceDesk::lang.shortcode_' . $shortCodeKey['key_name'] . '_description',
          'key_name'             => $shortCodeKey['key_name']
        ]);
      }
    }

    /**
     * Template Type Seeder
     * @return 
     */
    private function templateTypeSeeder()
    {
      TemplateType::updateOrCreate(['name' => 'approve-contract'], ['name' => 'approve-contract', 'plugin_name' => 'ServiceDesk']);
      TemplateType::updateOrCreate(['name' => 'approved-contract'], ['name' => 'approved-contract', 'plugin_name' => 'ServiceDesk']);
      TemplateType::updateOrCreate(['name' => 'rejected-contract'], ['name' => 'rejected-contract', 'plugin_name' => 'ServiceDesk']);
      TemplateType::updateOrCreate(['name' => 'notify-expiry-contract'], ['name' => 'notify-expiry-contract', 'plugin_name' => 'ServiceDesk']);
    }


    /**
     * Template Seeder
     * @return 
     */
    private function templateSeeder()
    {
      $templates = $this->templateArray();
      $setIds = TemplateSet::all()->pluck('id')->toArray();

      for ($setId = 0; $setId < count($setIds); $setId++)
      {

        foreach ($templates as $template) {
          $template['set_id'] = $setIds[$setId];
          Template::updateOrCreate($template, $template);
        }
      }
    }

    /**
     * Template array creator
     * @return array $temple 
     */
    private function templateArray()
    {
      $template = [];
      $this->contractApproveTemplate($template);
      $this->contractApprovedTemplate($template);
      $this->contractRejectedTemplate($template);
      $this->contractExpiryNotifyTemplate($template);

      return $template;
    }

    /**
     * contract approve template
     * @return 
     */
    private function contractApproveTemplate(&$template)
    {
      array_push($template, [
          'name' => 'template-contract-approval-by-agent-or-admin',
          'variable' => 1,
          'type' => TemplateType::where('name', 'approve-contract')->first()->id,
          'subject' => 'Request for contract approval',
          'message' => 'Hello {!! $receiver_name !!} <br/> <br/>'
                        .'A new Contract {!! $contract_name !!} (#CNTR-{!! $contract_id !!}) has been submitted for your approval. Please verify the contract and confirm your approval. <br/> <br/>Contract : {!! $contract_link !!} <br/> <br/>'
                        .'Please click <a href={!! $contract_approval_link !!}>here</a> to review the contract.<br /><br />'
                        .'Kind Regards, <br>'
                        .'{!! $company_name !!}',
          'description' => 'template to send notification for contract approval to contract approver',
          'template_category' => 'agent-templates'
      ]);
    }

    /**
     * contract approved template
     * @return 
     */
    private function contractApprovedTemplate(&$template)
    {
      array_push($template, [
          'name' => 'template-contract-approved-by-agent-or-admin',
          'variable' => 1,
          'type' => TemplateType::where('name', 'approved-contract')->first()->id,
          'subject' => 'Your contract is approved',
          'message' => 'Hello {!! $receiver_name !!} <br/> <br/>'
                      .'Contract {!! $contract_name !!} (#CNTR-{!! $contract_id !!}) has been approved by {!! $approver_name !!}.'
                      .'You can go through the contract. <br/> <br/>'
                      .'Contract : {!! $contract_link !!} <br/> <br/>'
                      .'Kind Regards, <br>'
                      .'{!! $company_name !!}',
          'description' => 'template to send notification to contract owner, regarding contract got approved',
          'template_category' => 'agent-templates'
      ]);
    }

    /**
     * contract rejected template
     * @return 
     */
    private function contractRejectedTemplate(&$template)
    {
      array_push($template, [
          'name' => 'template-contract-rejected-by-agent-or-admin',
          'variable' => 1,
          'type' => TemplateType::where('name', 'rejected-contract')->first()->id,
          'subject' => 'Your Contract is rejected',
          'message' => 'Hello {!! $receiver_name !!} <br/> <br/>'
                      .'Contract {!! $contract_name !!} (#CNTR-{!! $contract_id !!}) has been rejected by {!! $approver_name !!}. <br/>'
                      .'Purpose of Rejection : {!! $contract_purpose_of_rejection !!} <br/>'
                      .'You can go through the contract and take necessary actions. <br/> <br/>'
                      .'Contract : {!! $contract_link !!} <br/> <br/>'
                      .'Kind Regards, <br>'
                      .'{!! $company_name !!}',
          'description' => 'template to send notification to contract owner, regarding contract got rejected',
          'template_category' => 'agent-templates'
      ]);
    }

    /**
     * contract expiry template
     * @return 
     */
    private function contractExpiryNotifyTemplate(&$template)
    {
      array_push($template, [
          'name' => 'template-contract-notify-agent-and-admin',
          'variable' => 1,
          'type' => TemplateType::where('name', 'notify-expiry-contract')->first()->id,
          'subject' => 'Your contract will expire on {!! $contract_expiry !!}',
          'message' => 'Hello {!! $receiver_name !!} <br/> <br/>'
                      .'Contract {!! $contract_name !!} (#CNTR-{!! $contract_id !!}) will expire on {!! $contract_expiry !!}.'
                      .'You can go through the contract and take necessary actions. <br/> <br/>'
                      .'Contract : {!! $contract_link !!} <br/> <br/>'
                      .'Kind Regards, <br>'
                      .'{!! $company_name !!}',
          'description' => 'template to send notification to agent and admin regarding contract expiry',
          'template_category' => 'agent-templates'
      ]);
    }

    /**
     * Contract  expiry notification seeder
     * @return 
     */
    private function contractExpiryNotifySeeder()
    {
      Condition::updateOrCreate(['job' => 'contract-expiry-notify',  'plugin_name' => 'ServiceDesk'], [
        'job' => 'contract-expiry-notify',
        'value' => 'daily',
        'icon' => 'fa fa-cloud-download',
        'command' => 'contract:notification-expiry',
        'job_info' => 'contract-expiry-notify-info',
        'active' => 1,
        'plugin_job' => 1,
        'plugin_name' => 'ServiceDesk'
      ]); 
    }

    /**
     * Make contract status active seeder
     * @return 
     */
    private function contractStatusActiveSeeder()
    {
      Condition::updateOrCreate(['job' => 'contract-status-active', 'plugin_name' => 'ServiceDesk'], [
        'job' => 'contract-status-active',
        'value' => 'daily',
        'icon' => 'fa fa-cloud-download',
        'command' => 'contract:status-active',
        'job_info' => 'contract-status-active-info',
        'active' => 1,
        'plugin_job' => 1,
        'plugin_name' => 'ServiceDesk'
      ]); 
    }

    /**
     * Make contract status expired seeder
     * @return 
     */
    private function contractStatusExpiredSeeder()
    {
      Condition::updateOrCreate(['job' => 'contract-status-expired', 'plugin_name' => 'ServiceDesk'], [
        'job' => 'contract-status-expired',
        'value' => 'daily',
        'icon' => 'fa fa-cloud-download',
        'command' => 'contract:status-expired',
        'job_info' => 'contract-status-expired-info',
        'active' => 1,
        'plugin_job' => 1,
        'plugin_name' => 'ServiceDesk'
      ]); 
    }

    /**
     * Contract Status Seeder
     * @return 
     */
    private function sdDefaultSeeder()
    {
      SdDefault::updateOrCreate(['id' =>  1], ['asset_type_id' => 1]);
    }
}

