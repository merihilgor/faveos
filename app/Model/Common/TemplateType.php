<?php

namespace App\Model\Common;

use App\BaseModel;

class TemplateType extends BaseModel
{
    protected $table = 'template_types';
    protected $fillable = ['id', 'name', 'plugin_name'];
}
