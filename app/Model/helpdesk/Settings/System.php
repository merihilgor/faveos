<?php

namespace App\Model\helpdesk\Settings;

use App\BaseModel;

class System extends BaseModel
{
    /* Using System Table */

    protected $table    = 'settings_system';
    protected $fillable = [

        'id', 'status', 'url', 'name', 'department', 'page_size', 'log_level', 'purge_log', 'name_format',
        'time_farmat', 'date_format', 'date_time_format', 'day_date_time', 'time_zone', 'content', 'api_key', 'api_enable', 'api_key_mandatory', 'version', 'serial_key', 'order_number', 'access_via_ip'
    ];

    public $timeFormats = ['g:i a'=>'12 hour format', 'H:i'=>'24 hour format'];

    public $dateFormats = ['d-m-Y'=>'d-m-Y', 'm-d-Y'=>'m-d-Y', 'Y-m-d'=>'Y-m-d', 'F j, Y'=>'F j, Y'];

    public function setUrlAttribute($value)
    {
        if (ends_with($value, '/')) {
            $value = substr($value, 0, -1);
        }
        $this->attributes['url']=$value;
    }
}
