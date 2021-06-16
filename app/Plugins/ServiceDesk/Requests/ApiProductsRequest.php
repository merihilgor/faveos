<?php 
namespace App\Plugins\ServiceDesk\Requests;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;


class ApiProductsRequest extends Request {

	use RequestJsonValidation;
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			//
            'name' => 'required|max:50',
            //'asset_type' => 'required',
            'manufacturer'=>'required|max:40',
            'Product_status'=>'required',
            'mode_procurement'=>'required',
           	'department_access'=>'required',
           'description'=>'required',
           	'status'=>'required',
           

		];
	}

}