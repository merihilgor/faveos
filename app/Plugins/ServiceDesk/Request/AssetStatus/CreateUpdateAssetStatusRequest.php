<?php

namespace App\Plugins\ServiceDesk\Request\AssetStatus;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

/**
 * validates the create and update asset status request
 *
 */
class CreateUpdateAssetStatusRequest extends Request
{
	use RequestJsonValidation;

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {

		$rules = [
            'name' => 'required|unique:sd_asset_statuses,name,'. $this->id .'|regex:/^[^\s]+[\pL\s]*[^\s]+$/u|max:50',
            'description' => 'sometimes'
        ];
        return $rules;
    }
}
