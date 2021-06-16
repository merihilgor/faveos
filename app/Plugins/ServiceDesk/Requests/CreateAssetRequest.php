<?php

namespace App\Plugins\ServiceDesk\Requests;

use App\Http\Requests\Request;

class CreateAssetRequest extends Request {

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
        $id = $this->segment(3);
        return [
            'name' => 'required|max:50|unique:sd_assets',
            'external_id'=>'unique:sd_assets,external_id,'.$id,
            'department_id'=> 'required',
            'used_by'=>'sometimes',
            'managed_by'=>'sometimes',
            'asset_type_id' => 'required',
            'description' => 'required',
            // 'location_id' => 'required',
        ];
    }

    public function messages() {
        return[
            // 'name.required' => 'Name field is required',
            // 'description.required' => 'Description field is required',
            // 'asset_type_id.required' => 'Asset Type field is required',
            // 'external_id.unique'=>'This Identifirer has already taken, Try different',
            // 'location_id.required' => 'Location field is required',
        ];
    }

}
