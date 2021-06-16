<?php

namespace App\Plugins\ServiceDesk\Request\Change;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

/**
 * validates the change create update request
 *
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class CreateUpdateChangeRequest extends Request
{
	use RequestJsonValidation;


    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
		$rules = [
            'subject' => 'required|max:50',
            'requester_id' => 'required|numeric|exists:users,id',
            'status_id' => 'required|exists:sd_change_status,id',
            'description' => 'required',
            'priority_id' => 'sometimes|numeric|exists:sd_change_priorities,id',
            'change_type_id' => 'required|numeric|exists:sd_change_types,id',
            'location_id' => 'sometimes|numeric|exists:location,id',
            'team_id' => 'sometimes|numeric|exists:teams,id',
            'department_id' => 'sometimes|numeric|exists:department,id',
            'identifier' => 'sometimes'
        ];

        if (!empty($this->identifier)) {
            $rules['identifier'] = 'unique:sd_changes,identifier,'.$this->id.'|max:45';
        }

        return $rules;
    }
}
