<?php

namespace App\Plugins\ServiceDesk\Request\Contract;

use App\Http\Requests\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\RequestJsonValidation;

/**
 * validates the contract reject request
 *
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */

class RejectContractRequest extends Request
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
            'purpose_of_rejection' => 'sometimes'
        ];
        return $rules;
    }
}
