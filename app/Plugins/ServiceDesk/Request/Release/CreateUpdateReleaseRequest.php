<?php

namespace App\Plugins\ServiceDesk\Request\Release;

use App\Http\Requests\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\RequestJsonValidation;

/**
 * validates the create update release request
 *
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */

class CreateUpdateReleaseRequest extends Request
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
            'subject' => 'required|max:250',
            'description' => 'required',
            'planned_start_date' => 'sometimes',
            'planned_end_date' => 'sometimes',
            'status_id' => 'required|exists:sd_release_status,id',
            'priority_id' => 'required|exists:sd_release_priorities,id',
            'release_type_id' => 'required|exists:sd_release_types,id',
            'location_id' => 'sometimes|exists:location,id',
            'identifier' => 'sometimes'
        ];

        if (!empty($this->identifier)) {
            $rules['identifier'] = 'unique:sd_releases,identifier,'.$this->id.'|max:45';
        }

        return $rules;
    }
}