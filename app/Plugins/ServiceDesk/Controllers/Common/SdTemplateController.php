<?php
namespace App\Plugins\ServiceDesk\Controllers\Common;

use App\Http\Controllers\Common\TemplateVariablesController;
use Lang;

/**
 * This controller is used to update template variables and template list
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class SdTemplateController
{
    /**
     * method to update template variables
     * @param $data
     * @return 
     */
    public function updateTemplateVariables($data)
    {   
        $data->orWhere('plugin_name', 'ServiceDesk');
    }

    /**
     * method to update template list
     * @param $data
     * @return 
     */
    public function updateTemplateList($data)
    {   
        $data->orWhere('ty.plugin_name', 'ServiceDesk');
    }

}
