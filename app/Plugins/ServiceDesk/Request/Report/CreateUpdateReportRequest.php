<?php

namespace App\Plugins\ServiceDesk\Request\Report;

use App\Http\Requests\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\RequestJsonValidation;

/**
 * validates the report controller
 *
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */

class CreateUpdateReportRequest extends Request
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
            'name' => 'required|unique:sd_report_filters,name,'. $this->id .'|max:25|regex:/(^[^\s][a-zA-Z0-9 ]+$)+/',
            'description' => 'sometimes|max:255',
            'type' => 'sometimes|max:10',
            'creator_id' => 'sometimes|numeric|exists:users,id'
        ];

        return $rules;
    }
}
