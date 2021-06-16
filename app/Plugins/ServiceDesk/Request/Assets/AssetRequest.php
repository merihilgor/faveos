<?php

namespace App\Plugins\ServiceDesk\Request\Assets;

use App\Http\Requests\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\RequestJsonValidation;

/**
 * validates the asset request
 *
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */

class AssetRequest extends Request
{
	use RequestJsonValidation;

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
		$rules = [
            'name' => 'required|unique:sd_assets,name,'. $this->id .'|max:255',
            'identifier' => 'sometimes|unique:sd_assets,identifier,'. $this->id,
            'department_id' => 'required|exists:department,id',
            'impact_type_id' => 'sometimes|exists:sd_impact_types,id',
            'organization_id' => 'sometimes|exists:organization,id',
            'location_id' => 'sometimes|exists:location,id',
            'managed_by_id' => 'sometimes|exists:users,id',
            'used_by_id' => 'sometimes|exists:users,id',
            'product_id' => 'sometimes|exists:sd_products,id',
            'asset_type_id' => 'required|exists:sd_asset_types,id',
            'assigned_on' => 'sometimes',
            'description' => 'required',
            'status_id' => 'sometimes|exists:sd_asset_statuses,id'
        ];
        return $rules;
    }
}
