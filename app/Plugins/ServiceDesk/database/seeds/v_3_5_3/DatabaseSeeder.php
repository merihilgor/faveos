<?php

namespace App\Plugins\ServiceDesk\database\seeds\v_3_5_3;

use database\seeds\DatabaseSeeder as Seeder;
use App\Model\Common\Template;
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
    }

    /**
     * Template Short Codes Seeder
     * @return null
     */
    private function templateShortCodesSeeder()
    {
        TemplateShortCode::updateOrCreate([
            'key_name' => 'contract_approval_link'],[
            'key_name' => 'contract_approval_link',
            'plugin_name' => 'ServiceDesk',
            'description_lang_key' => 'ServiceDesk::lang.shortcode_contract_approval_link_description',
            'shortcode' => '{!! $contract_approval_link !!}'
        ]);
    }
}
