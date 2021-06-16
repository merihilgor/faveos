<?php

namespace App\Plugins\ServiceDesk\Request\Vendor;

use App\Http\Requests\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\RequestJsonValidation;

/**
 * validates the create and update vendor request
 *
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */

class CreateUpdateVendorRequest extends Request
{
	use RequestJsonValidation;


    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {

		$rules = [
            'name' => 'required|max:50|unique:sd_vendors,name,'.$this->id,
            'email' => 'required|email|max:255|unique:emails,email_address|unique:sd_vendors,email,'.$this->id,
            'primary_contact' => 'required|unique:sd_vendors,primary_contact,'.$this->id,
            'address' => 'required|max:255',
            'status_id' => 'required',
            'description' => 'sometimes'
        ];
        return $rules;
    }
}
