<?php

namespace App\Plugins\ServiceDesk\Request\Assets;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

/**
 * validates the attach services to asset request
 * services like problem, change, release, contract and ticket
 *
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */

class AttachOrDetachServicesToAssetRequest extends Request
{
	use RequestJsonValidation;

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
		$rules = [
            'type' => 'required|in:sd_problem,sd_changes,sd_releases,sd_contracts,tickets'
        ];

        $types = ['sd_problem', 'sd_changes', 'sd_releases', 'sd_contracts' ,'tickets'];

        if (in_array($this->type, $types)) {
            $rules['type_ids'] = "required|exists:$this->type,id";
        }

        return $rules;
    }
}
