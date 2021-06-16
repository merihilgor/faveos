<?php

namespace App\Plugins\ServiceDesk\Request\AssetType;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

/**
 * validates the create and update asset type request
 *
 */

class CreateUpdateAssetTypeRequest extends Request
{
	use RequestJsonValidation;

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {

		$rules = [
            'name' => 'required|unique:sd_asset_types,name,'. $this->id .'|regex:/^[^\s]+[\pL\s]*[^\s]+$/u|max:50',
            'parent_id' => 'sometimes|exists:sd_asset_types,id|not_in:'.$this->id,
            'is_default' => 'sometimes',
        ];
        return $rules;
    }

    /**
     * Changes validation message for parent_id attribute
     * @return  array
     */
    public function messages()
    {
        return [
            'parent_id.not_in' => trans('ServiceDesk::lang.cannot_make_self_parent_kindly_select_different_parent'),
        ];
    }
}
