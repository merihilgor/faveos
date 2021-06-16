<?php

namespace App\Plugins\ServiceDesk\Request\Contract;

use App\Http\Requests\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\RequestJsonValidation;

/**
 * validates the contract extend request
 *
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */

class ExtendContractRequest extends Request
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
            'approver_id' => 'required',
            'cost' => 'required',
            'contract_end_date' => 'required'
        ];
        return $rules;
    }
}
