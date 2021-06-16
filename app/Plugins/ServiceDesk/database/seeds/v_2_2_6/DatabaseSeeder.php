<?php

namespace App\Plugins\ServiceDesk\database\seeds\v_2_2_6;

use database\seeds\DatabaseSeeder as Seeder;
use App\Model\Common\TemplateType;
use App\Model\Common\Template;
use App\Model\Common\TemplateSet;
use App\Model\Common\TemplateShortCode;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $this->templateShortCodesSeeder();
      $this->templateTypeSeeder();
      $this->templateSeeder();
    }

    /**
     * Template Short Codes Seeder
     * @return void
     */
    private function templateShortCodesSeeder()
    {
      $shortCodeKeys = [
        ['key_name' => 'change_number'],
        ['key_name' => 'change_link'],
        ['key_name' => 'change_approval_link']
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
     * @return void
     */
    private function templateTypeSeeder()
    {
      TemplateType::updateOrCreate(['name' => 'approve-change'], ['name' => 'approve-change', 'plugin_name' => 'ServiceDesk']);
    }


    /**
     * Template Seeder
     * @return void
     */
    private function templateSeeder()
    {
      $template = $this->templateArray();
      $setIds = TemplateSet::all()->pluck('id')->toArray();
      foreach ($setIds as $setId) {
        $template['set_id'] = $setId;
        Template::updateOrCreate($template, $template);
      }
    }

    /**
     * Template array creator
     * @return array $temple 
     */
    private function templateArray()
    {
      $template = [
          'name' => 'template-change-approval',
          'variable' => 1,
          'type' => TemplateType::where('name', 'approve-change')->first()->id,
          'subject' => 'Change Approval Link',
          'message' => '<p>Hello {!! $receiver_name !!},<br /><br />'
                      .'Change <a href="{!! $change_link !!}">{!! $change_number !!}</a> has been waiting for your approval.<br /><br />'
                      .'Please click <a href={!! $change_approval_link !!}>here</a> to review the change.<br /><br />'
                      .'Have a nice day.<br /><br />'
                      .'Kind Regards,<br />'
                      .'{!! $system_from !!}',
          'description' => 'template to send notification for change approval to change approver',
          'template_category' => 'agent-templates'
        ];

      return $template;
    }
}

