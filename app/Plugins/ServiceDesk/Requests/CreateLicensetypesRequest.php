<?php 
namespace App\Plugins\ServiceDesk\Requests;

use App\Http\Requests\Request;

class CreateLicensetypesRequest extends Request {

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
            $id = $this->segment(3);
		return [
                    'name' =>'required|max:50|unique:sd_license_types,name,'.$id
		];
	}

}