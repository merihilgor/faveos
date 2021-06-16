<?php

namespace App\Plugins\ServiceDesk\Request\ProcurementMode;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

/**
 * validates the create and update procurement mode request
 *
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */

class CreateUpdateProcurementModeRequest extends Request
{
	use RequestJsonValidation;

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {

		$rules = [
            'name' => 'required|unique:sd_product_proc_mode,name,'. $this->id .'|regex:/^[\pL\s]+$/u|max:50'
        ];
        return $rules;
    }
}