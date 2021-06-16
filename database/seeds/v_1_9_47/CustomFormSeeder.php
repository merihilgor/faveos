<?php

namespace database\seeds\v_1_9_47;

use database\seeds\DatabaseSeeder as Seeder;
class CustomFormSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        \DB::table('forms')->truncate();
        \App\Model\Custom\Required::truncate();
        $this->seedTicketForm();
        $this->seedUserForm();
        $this->seedOrganisationForm();
        //$this->seedRequired();
    }
    public function seedTicketForm() {

        $json = "[{
        'title': 'Requester',
        'agentlabel':[
                 {'language':'en','label':'Requester','flag':'".asset('lb-faveo/flags/en.png')."'}
                ],
        'clientlabel':[
                 {'language':'en','label':'Requester','flag':'".asset('lb-faveo/flags/en.png')."'}
                ],
        'type':'email',
        'agentCCfield':true,
        'customerCCfield':false,
        'customerDisplay':true,
        'agentDisplay':true,
        'agentRequiredFormSubmit':true,
        'customerRequiredFormSubmit':true,
        'default':'yes',
        'value':'',
        'unique':'requester'
        },{
        'title': 'Subject',
        'agentlabel':[
                 {'language':'en','label':'Subject','flag':'".asset('lb-faveo/flags/en.png')."'}
                ],
        'clientlabel':[
                 {'language':'en','label':'Subject','flag':'".asset('lb-faveo/flags/en.png')."'}
                ],
        'type':'text',
        'agentRequiredFormSubmit':true,
        'customerDisplay':true,
        'agentDisplay':true,
        'customerRequiredFormSubmit':true,
        'default':'yes',
        'value':'',
        'unique':'subject'
        },{
        'title': 'Status',
        'agentlabel':[
                 {'language':'en','label':'Status','flag':'".asset('lb-faveo/flags/en.png')."'}
                ],
        'clientlabel':[
                 {'language':'en','label':'Status','flag':'".asset('lb-faveo/flags/en.png')."'}
                ],
        'type':'select',
        'agentRequiredFormSubmit':true,
        'customerDisplay':false,
        'agentDisplay':true,
        'customerRequiredFormSubmit':false,
        'value':'',
        'api':'status',
        'options':[

        ],
        'default':'yes',
        'unique':'status'
        },{
        'title': 'Priority',
        'agentlabel':[
                 {'language':'en','label':'Priority','flag':'".asset('lb-faveo/flags/en.png')."'}
                ],
        'clientlabel':[
                 {'language':'en','label':'Priority','flag':'".asset('lb-faveo/flags/en.png')."'}
                ],
        'type':'select',
        'agentRequiredFormSubmit':true,
        'customerDisplay':true,
        'agentDisplay':true,
        'customerRequiredFormSubmit':true,
        'value':'',
        'api':'priority',
        'options':[

        ],
        'default':'yes',
        'unique':'priority'
        },
       {
        'title': 'Location',
        'agentlabel':[
                 {'language':'en','label':'Location','flag':'".asset('lb-faveo/flags/en.png')."'}
                ],
        'clientlabel':[
                 {'language':'en','label':'Location','flag':'".asset('lb-faveo/flags/en.png')."'}
                ],
        'type':'select',
        'agentRequiredFormSubmit':false,
        'customerDisplay':false,
        'agentDisplay':true,
        'customerRequiredFormSubmit':false,
        'value':'',
        'api':'location',
        'options':[

        ],
        'default':'yes',
        'unique':'location'
        },
        {
        'title': 'Help Topic',
        'agentlabel':[
                 {'language':'en','label':'Help Topic','flag':'".asset('lb-faveo/flags/en.png')."'}
                ],
        'clientlabel':[
                 {'language':'en','label':'Help Topic','flag':'".asset('lb-faveo/flags/en.png')."'}
                ],
        'type':'multiselect',
        'agentRequiredFormSubmit':true,
        'customerDisplay':true,
        'agentDisplay':true,
        'customerRequiredFormSubmit':true,
        'value':'',
        'api':'helptopic',
        'options':[

        ],
        'default':'yes',
        'unique':'help_topic',
        'link':false
        },{
        'title': 'Department',
        'agentlabel':[
                 {'language':'en','label':'Department','flag':'".asset('lb-faveo/flags/en.png')."'}
                ],
        'clientlabel':[
                 {'language':'en','label':'Department','flag':'".asset('lb-faveo/flags/en.png')."'}
                ],
        'type':'multiselect',
        'agentRequiredFormSubmit':true,
        'agentDisplay':true,
        'customerDisplay':false,
        'customerRequiredFormSubmit':false,
        'value':'',
        'api':'department',
        'options':[

        ],
        'default':'yes',
        'unique':'department',
        'link':false
        },{
        'title': 'Type',
        'agentlabel':[
                 {'language':'en','label':'Type','flag':'".asset('lb-faveo/flags/en.png')."'}
                ],
        'clientlabel':[
                 {'language':'en','label':'Type','flag':'".asset('lb-faveo/flags/en.png')."'}
                ],
        'type':'select',
        'agentRequiredFormSubmit':false,
        'customerDisplay':true,
        'agentDisplay':true,
        'customerRequiredFormSubmit':false,
        'value':'',
        'api':'type',
        'options':[
        ],
        'default':'yes',
        'unique':'type'
        },{
        'title': 'Assigned',
        'agentlabel':[
                 {'language':'en','label':'Assigned','flag':'".asset('lb-faveo/flags/en.png')."'}
                ],
        'clientlabel':[
                 {'language':'en','label':'Assigned','flag':'".asset('lb-faveo/flags/en.png')."'}
                ],
        'type':'select',
        'agentRequiredFormSubmit':false,
        'agentDisplay':false,
        'customerDisplay':false,
        'customerRequiredFormSubmit':false,
        'value':'',
        'api':'assigned_to',
        'options':[

        ],
        'default':'yes',
        'unique':'assigned'
        },{
        'title': 'Description',
        'agentlabel':[
                 {'language':'en','label':'Description','flag':'".asset('lb-faveo/flags/en.png')."'}
                ],
        'clientlabel':[
                 {'language':'en','label':'Description','flag':'".asset('lb-faveo/flags/en.png')."'}
                ],
        'type':'textarea',
        'agentRequiredFormSubmit':true,
        'agentDisplay':true,
        'customerDisplay':true,
        'customerRequiredFormSubmit':false,
        'default':'yes',
        'value':'',
        'unique':'description',
        'media_option':true
        },{
        'title': 'Captcha',
        'agentDisplay':true,
        'customerDisplay':true,
        'default':'yes',
        'value':''
        },{
        'title': 'Organization',
        'agentlabel':[
                 {'language':'en','label':'Organization','flag':'".asset('lb-faveo/flags/en.png')."'}
                ],
        'clientlabel':[
                 {'language':'en','label':'Organization','flag':'".asset('lb-faveo/flags/en.png')."'}
                ],
        'type':'select',
        'agentRequiredFormSubmit':false,
        'agentDisplay':true,
        'customerDisplay':false,
        'customerRequiredFormSubmit':false,
        'value':'',
        'api':'company',
        'options':[

        ],
        'default':'yes',
        'unique':'company'
        },{
        'title': 'Organisation Department',
        'agentlabel':[
                 {'language':'en','label':'Organisation Department','flag':'".asset('lb-faveo/flags/en.png')."'}
                ],
        'clientlabel':[
                 {'language':'en','label':'Organisation Department','flag':'".asset('lb-faveo/flags/en.png')."'}
                ],
        'type':'select',
        'agentRequiredFormSubmit':false,
        'agentDisplay':true,
        'customerDisplay':false,
        'customerRequiredFormSubmit':false,
        'value':'',
        'api':'org_dept',
        'options':[

        ],
        'default':'yes',
        'unique':'org_dept'
        }
        ]
";
        $json = trim(preg_replace('/\s+/', ' ', $json));
        $form = "ticket";
        \DB::table('forms')->insert(['form' => $form, 'json' => $json]);
        $form_controller = new \App\Http\Controllers\Utility\FormController();
        $form_controller->saveRequired($form);
    }

    public function seedUserForm(){
        $json = "[{
        'title': 'First Name',
        'agentlabel':[
                 {'language':'en','label':'First Name','flag':'".asset("lb-faveo/flags/en.png")."'}
                ],
        'clientlabel':[
                 {'language':'en','label':'First Name','flag':'".asset("lb-faveo/flags/en.png")."'}
                ],
        'type':'text',
        'customerDisplay':true,
        'agentDisplay':true,
        'agentRequiredFormSubmit':true,
        'customerRequiredFormSubmit':true,
        'default':'yes',
        'value':'',
        'unique':'first_name'
        },{
        'title': 'Last Name',
        'agentlabel':[
                 {'language':'en','label':'Last Name','flag':'".asset("lb-faveo/flags/en.png")."'}
                ],
        'clientlabel':[
                 {'language':'en','label':'Last Name','flag':'".asset("lb-faveo/flags/en.png")."'}
                ],
        'type':'text',
        'customerDisplay':true,
        'agentDisplay':true,
        'agentRequiredFormSubmit':true,
        'customerRequiredFormSubmit':true,
        'default':'yes',
        'value':'',
        'unique':'last_name'
        },
        {
        'title': 'User Name',
        'agentlabel':[
                 {'language':'en','label':'User Name','flag':'".asset("lb-faveo/flags/en.png")."'}
                ],
        'clientlabel':[
                 {'language':'en','label':'User Name','flag':'".asset("lb-faveo/flags/en.png")."'}
                ],
        'type':'text',
        'customerDisplay':false,
        'agentDisplay':true,
        'agentRequiredFormSubmit':false,
        'customerRequiredFormSubmit':false,
        'default':'yes',
        'value':'',
        'unique':'user_name'
        },{
        'title': 'Work Phone',
        'agentlabel':[
                 {'language':'en','label':'Work Phone','flag':'".asset("lb-faveo/flags/en.png")."'}
                ],
        'clientlabel':[
                 {'language':'en','label':'Work Phone','flag':'".asset("lb-faveo/flags/en.png")."'}
                ],
        'type':'number',
        'agentRequiredFormSubmit':false,
        'agentDisplay':true,
        'customerDisplay':false,
        'customerRequiredFormSubmit':false,
        'default':'yes',
        'value':'',
        'unique':'phone_number'
        },{
        'title': 'Email',
        'agentlabel':[
                 {'language':'en','label':'Email','flag':'".asset("lb-faveo/flags/en.png")."'}
                ],
        'clientlabel':[
                 {'language':'en','label':'Email','flag':'".asset("lb-faveo/flags/en.png")."'}
                ],
        'type':'email',
        'agentRequiredFormSubmit':true,
        'agentDisplay':true,
        'customerDisplay':true,
        'customerRequiredFormSubmit':true,
        'value':'',
        'default':'yes',
        'unique':'email'
        },{
        'title': 'Mobile Phone',
        'agentlabel':[
                 {'language':'en','label':'Mobile Phone','flag':'".asset("lb-faveo/flags/en.png")."'}
                ],
        'clientlabel':[
                 {'language':'en','label':'Mobile Phone','flag':'".asset("lb-faveo/flags/en.png")."'}
                ],
        'type':'number',
        'agentRequiredFormSubmit':false,
        'agentDisplay':true,
        'customerDisplay':false,
        'customerRequiredFormSubmit':false,
        'value':'',
        'default':'yes',
        'unique':'mobile'
        },{
        'title': 'Address',
        'agentlabel':[
                 {'language':'en','label':'Address','flag':'".asset("lb-faveo/flags/en.png")."'}
                ],
        'clientlabel':[
                 {'language':'en','label':'Address','flag':'".asset("lb-faveo/flags/en.png")."'}
                ],
        'type':'textarea',
        'agentRequiredFormSubmit':false,
        'agentDisplay':true,
        'customerDisplay':false,
        'customerRequiredFormSubmit':false,
        'value':'',
        'default':'no',
        'unique':'address'
        },{
        'title': 'Organisation',
        'agentlabel':[
                 {'language':'en','label':'Organisation','flag':'".asset("lb-faveo/flags/en.png")."'}
                ],
        'clientlabel':[
                 {'language':'en','label':'Organisation','flag':'".asset("lb-faveo/flags/en.png")."'}
                ],
        'type':'select2',
        'agentRequiredFormSubmit':false,
        'agentDisplay':true,
        'customerDisplay':false,
        'customerRequiredFormSubmit':false,
        'default':'yes',
        'value':'',
        'unique':'organisation'

        },{
        'title': 'Department Name',
        'agentlabel':[
                 {'language':'en','label':'Department Name','flag':'".asset("lb-faveo/flags/en.png")."'}
                ],
        'clientlabel':[
                 {'language':'en','label':'Department Name','flag':'".asset("lb-faveo/flags/en.png")."'}
                ],
        'type':'select',
        'agentRequiredFormSubmit':false,
        'agentDisplay':true,
        'customerDisplay':false,
        'customerRequiredFormSubmit':false,
        'default':'yes',
        'value':'',
        'unique':'department',
        'options':[

        ],
        'api':'organisationdept'
        },
        {
        'title': 'Captcha',
        'agentDisplay':true,
        'customerDisplay':true,
        'default':'yes',
        'value':''
        }]";
        $json = trim(preg_replace('/\s+/', ' ', $json));
        $form = "user";
        \DB::table('forms')->insert(['form' => $form, 'json' => $json]);
        $form_controller = new \App\Http\Controllers\Utility\FormController();
        $form_controller->saveRequired($form);
    }

    public function seedOrganisationForm(){
        $json = "[{
        'title': 'Organization Name',
        'agentlabel':[
                 {'language':'en','label':'Organization Name','flag':'".asset("lb-faveo/flags/en.png")."'}
                ],
        'clientlabel':[
                 {'language':'en','label':'Organization Name','flag':'".asset("lb-faveo/flags/en.png")."'}
                ],
        'type':'text',
        'customerDisplay':true,
        'agentDisplay':true,
        'agentRequiredFormSubmit':true,
        'customerRequiredFormSubmit':true,
        'default':'yes',
        'value':'',
        'unique':'name'
        },{
        'title': 'Phone',
        'agentlabel':[
                 {'language':'en','label':'Phone','flag':'".asset("lb-faveo/flags/en.png")."'}
                ],
        'clientlabel':[
                 {'language':'en','label':'Phone','flag':'".asset("lb-faveo/flags/en.png")."'}
                ],
        'type':'number',
        'customerDisplay':true,
        'agentDisplay':true,
        'agentRequiredFormSubmit':false,
        'customerRequiredFormSubmit':false,
        'default':'yes',
        'value':'',
        'unique':'phone'
        },{
        'title': 'Organization Domain Name',
        'agentlabel':[
                 {'language':'en','label':'Organization Domain Name','flag':'".asset("lb-faveo/flags/en.png")."'}
                ],
        'clientlabel':[
                 {'language':'en','label':'Organization Domain Name','flag':'".asset("lb-faveo/flags/en.png")."'}
                ],
        'type':'select2',
        'agentRequiredFormSubmit':false,
        'customerDisplay':true,
        'agentDisplay':true,
        'customerRequiredFormSubmit':false,
        'default':'yes',
        'value':'',
        'unique':'domain'
        },{
        'title': 'Description',
        'agentlabel':[
                 {'language':'en','label':'Description','flag':'".asset("lb-faveo/flags/en.png")."'}
                ],
        'clientlabel':[
                 {'language':'en','label':'Description','flag':'".asset("lb-faveo/flags/en.png")."'}
                ],
        'type':'textarea',
        'agentRequiredFormSubmit':false,
        'customerDisplay':true,
        'agentDisplay':true,
        'customerRequiredFormSubmit':false,
        'value':'',
        'default':'yes',
        'unique':'internal_notes'
        },{
        'title': 'Address',
        'agentlabel':[
                 {'language':'en','label':'Address','flag':'".asset("lb-faveo/flags/en.png")."'}
                ],
        'clientlabel':[
                 {'language':'en','label':'Address','flag':'".asset("lb-faveo/flags/en.png")."'}
                ],
        'type':'textarea',
        'agentRequiredFormSubmit':false,
        'customerDisplay':false,
        'agentDisplay':true,
        'customerRequiredFormSubmit':false,
        'value':'',
        'default':'yes',
        'unique':'address'
        },

        {
        'title': 'Department',
        'agentlabel':[
                 {'language':'en','label':'Department','flag':'".asset("lb-faveo/flags/en.png")."'}
                ],
        'clientlabel':[
                 {'language':'en','label':'Department','flag':'".asset("lb-faveo/flags/en.png")."'}
                ],
        'type':'select2',
        'agentRequiredFormSubmit':false,
        'customerDisplay':true,
        'agentDisplay':true,
        'customerRequiredFormSubmit':false,
        'default':'yes',
        'value':'',
        'unique':'department'
        }]";
        $json = trim(preg_replace('/\s+/', ' ', $json));
        $form = "organisation";
        \DB::table('forms')->insert(['form' => $form, 'json' => $json]);
        $form_controller = new \App\Http\Controllers\Utility\FormController();
        $form_controller->saveRequired($form);
    }
    public function seedRequired() {
        \DB::table('required_fields')->truncate();
        $fields = [
            ['name' => 'Requester', 'is_agent_required' => 1, 'is_client_required' => 1],
            ['name' => 'Subject', 'is_agent_required' => 1, 'is_client_required' => 1],
            ['name' => 'Type', 'is_agent_required' => 1, 'is_client_required' => 1],
            ['name' => 'Status', 'is_agent_required' => 1, 'is_client_required' => 1],
            ['name' => 'Priority', 'is_agent_required' => 1, 'is_client_required' => 1],
            ['name' => 'Group', 'is_agent_required' => 1, 'is_client_required' => 1],
            ['name' => 'Agent', 'is_agent_required' => 1, 'is_client_required' => 1],
            ['name' => 'Description', 'is_agent_required' => 1, 'is_client_required' => 1],
            ['name' => 'Company', 'is_agent_required' => 1, 'is_client_required' => 1],
        ];
        $form = "ticket";
        foreach ($fields as $field) {
            \DB::table('required_fields')->insert(['name' => $field['name'], 'form' => $form, 'is_agent_required' => $field['is_agent_required'], 'is_client_required' => $field['is_client_required']]);
        }
    }

}
