<?php

namespace App\Plugins\ServiceDesk\Requests;

use App\Http\Requests\Request;

class CreateContractRequest extends Request {

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
        $rules = [
            'name' => 'required|max:50',
             'cost' => 'required|integer|min:1',
             'licensce_count' => 'sometimes|integer|min:1',
             'description' => 'required',
             'contract_type_id' => 'required',
             // 'product_id' => 'required',
             'approver_id' => 'required',
             'vendor_id' => 'required',
             'contract_start_date' => 'required',
             'contract_end_date' => 'required',
             'notify_before' => 'sometimes|integer|min:1',
             'identifier' => 'sometimes'
        ];

        if (!empty($this->identifier)) {
            $rules['identifier'] = 'unique:sd_contracts,identifier,'.$this->id.'|max:45';
        }
        return $rules;
    }

    public function messages() {
        return [

             // "name.required" => "Name Required",
             // "description.required" => "Description Required",
             // "cost.required" => "Cost Required",
             // "contract_type_id.required" => "Contract Type Required",
             // "approver_id.required" => "Approver Required",
             // "vendor_id.required" => "Vendor Required",
             // "license_type_id.required" => "License Type Required",
             // "licensce_count.required" => "licensce Count Required",
             // "notify_expiry.required" => "Notify Expiry Required",
             // "product_id.required" => "Product Required",
             // "contract_start_date.required" => "Contract Start Date Required",
             // "contract_end_date.required" => "Contract End Date Required",
        ];
    }

}
