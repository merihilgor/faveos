<?php

namespace App\Plugins\ServiceDesk\Request\Problem;

use App\Http\Requests\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\RequestJsonValidation;

/**
 * validates the problem request
 *
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */

class ProblemRequest extends Request
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
            'requester_id' => 'required|numeric|exists:users,id',
            'subject' => 'required|max:50',
            'description' => 'required',
            'status_type_id' => 'required|exists:ticket_status,id',
            'priority_id' => 'required|exists:ticket_priority,priority_id',
            'impact_id' => 'required|exists:sd_impact_types,id',
            'location_type_id' => 'sometimes|exists:location,id',
            'department_id' => 'required|exists:department,id',
            'assigned_id' => 'sometimes|exists:users,id',
            'ticket_id' => 'sometimes|exists:tickets,id',
            'identifier' => 'sometimes'
        ];

        if (!empty($this->identifier)) {
            $rules['identifier'] = 'unique:sd_problem,identifier,'.$this->id.'|max:45';
        }
        return $rules;
    }
}
