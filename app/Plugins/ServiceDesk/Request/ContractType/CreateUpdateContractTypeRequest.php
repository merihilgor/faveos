<?php

namespace App\Plugins\ServiceDesk\Request\ContractType;

use App\Http\Requests\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\RequestJsonValidation;

/**
 * validates the create and update contract type request
 *
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */

class CreateUpdateContractTypeRequest extends Request
{
	use RequestJsonValidation;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }


    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {

		$rules = [
            'name' => 'required|unique:sd_contract_types,name,'. $this->id .'|regex:/^[^\s]+[\pL\s]*[^\s]+$/u|max:50'
        ];
        return $rules;
    }
}
