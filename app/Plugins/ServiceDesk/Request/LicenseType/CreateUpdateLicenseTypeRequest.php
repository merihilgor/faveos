<?php

namespace App\Plugins\ServiceDesk\Request\LicenseType;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

/**
 * validates the create and update license type request
 *
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */

class CreateUpdateLicenseTypeRequest extends Request
{
	use RequestJsonValidation;

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {

		$rules = [
            'name' => 'required|unique:sd_license_types,name,'. $this->id .'|regex:/^[^\s]+[\pL\s]*[^\s]+$/u|max:50'
        ];
        return $rules;
    }
}