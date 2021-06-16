<?php

namespace App\Plugins\ServiceDesk\Request\Contract;

use App\Http\Requests\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\RequestJsonValidation;

/**
 * validates the contract thread create and update request
 *
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */

class CreateUpdateContractThreadRequest extends Request
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
            'contract_id' => 'required|numeric|exists:sd_contracts,id',
            'status_id' => 'required|numeric|exists:sd_contract_statuses,id',
            'renewal_status_id' => 'sometimes',
            'cost' => 'required|numeric',
            'contract_start_date' => 'required',
            'contract_end_date' => 'required',
            'approver_id' => 'required|numeric|exists:users,id'
        ];
        return $rules;
    }
}
