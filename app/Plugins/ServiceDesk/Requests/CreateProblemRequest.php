<?php

namespace App\Plugins\ServiceDesk\Requests;

use App\Http\Requests\Request;

class CreateProblemRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
             'from' => 'required|email',
             'subject'=>'required|max:50',
             'department' => 'required',
             'status_type_id' => 'required',
             'description' => 'required',    
//            'priority_id' => 'required',
//            'impact_id' => 'required',
//            'location_type_id' => 'required',
//            'group_id' => 'required',
//            'agent_id' => 'required',
//            'assigned_id' => 'required',
                ];
    }

    public function messages() {
        return [

            // "from.required" => "From Required",
            // "description.required" => "Description required",
            // "department.required" => "Department required",
            // "status_type_id.required" => "Status type required",
            // "priority_id.required" => "Priority required",
            // "impact_id.required" => "impact required",
            // "location_type_id.required" => "Location type required",
            // "group_id.required" => "Group required",
            // "agent_id.required" => "Agent required",
            // "assigned_id.required" => "Assigned required",
        ];
    }

}
