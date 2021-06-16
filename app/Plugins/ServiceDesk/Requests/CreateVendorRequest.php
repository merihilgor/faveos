<?php

namespace App\Plugins\ServiceDesk\Requests;

use App\Http\Requests\Request;

class CreateVendorRequest extends Request {

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

            'name'            => 'required|regex:/^[\pL\s\-]+$/u|max:50|unique:sd_vendors,name,',
            'primary_contact' => 'required|max:20',
            'email'           => 'required|email',
//          'description'     => 'required',
            'address'         => 'required|max:100',
//          'all_department'  => 'required',
            'status'          => 'required',
        ];
    }

}
