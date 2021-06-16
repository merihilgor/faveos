<?php 
namespace App\Plugins\ServiceDesk\Requests;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

class AssetForBarcodeRequest extends Request
{
    use RequestJsonValidation;
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            
            'ids' => 'required|array'
           
        ];
    }

    public function messages() {
        return [
            'ids.required' => trans('ServiceDesk::lang.no_asset_input')
        ];
    }
}