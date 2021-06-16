<?php

namespace App\Plugins\ServiceDesk\Request\Announcement;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

/**
 * validates the report controller
 *
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */

class CreateUpdateAnnouncementRequest extends Request
{
	use RequestJsonValidation;

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        $rules = [
            'option' => 'required',
            'organization_id' => 'required_if:option,organization_id|exists:organization,id',
            'department_id' => 'required_if:option,department_id|exists:department,id',
            'announcement' => 'required',
            'subject'=>'required'
        ];

        return $rules;
    }
}
