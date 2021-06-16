<?php

namespace App\Plugins\ServiceDesk\Requests;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

/**
 * validates the change create update request
 *
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class ProductAttachVendorRequest extends Request
{
	use RequestJsonValidation;


    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
		$rules = [
            
            'product_id' => 'numeric|exists:sd_products,id',
            'vendor_ids' => 'array|exists:sd_vendors,id',
        ];
        return $rules;
    }
}
