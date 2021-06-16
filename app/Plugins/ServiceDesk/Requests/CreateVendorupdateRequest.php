<?php

namespace App\Plugins\ServiceDesk\Requests;

use App\Http\Requests\Request;

class CreateVendorupdateRequest extends Request {

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
       
        
            'name'            => 'required||max:50|unique:sd_vendors,name,'.$this->segment(3),
            'primary_contact' => 'required|max:20|min:10',
            'email'           => 'required',
//           'description'    => 'required',
            'address'         => 'required|max:100',
//          'all_department'  => 'required',
            'status'          => 'required',
        ];
    }

}
