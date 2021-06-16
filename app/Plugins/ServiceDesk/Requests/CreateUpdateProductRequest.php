<?php 
namespace App\Plugins\ServiceDesk\Requests;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;


/**
 * validates the create and update product request
 *
 * @author Abhishek Kumar Shashi <abhishek.shashi@ladybirdweb.com>
 */
class CreateUpdateProductRequest extends Request {

	use RequestJsonValidation;
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
            'name' => 'required|max:50',
            'manufacturer'=>'required|max:40',
            'product_status_id'=>'required|exists:sd_product_status,id',
            'procurement_mode_id'=>'required|exists:sd_product_proc_mode,id',
           	'department_id'=>'required|exists:department,id',
           	'description'=>'required',
           	'status_id'=>'required',
           	'asset_type_id' => 'sometimes|exists:sd_asset_types,id',
		];
	}

}