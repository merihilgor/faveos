<?php 

namespace App\Http\Requests\helpdesk\Common;

use App\Http\Requests\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\RequestJsonValidation;

/**
 * ProfileRequest.
 * @depreciated         as it returns a redirect and cannot be used in modularity
 *
 * @author  Ladybird <info@ladybirdweb.com>
 */
class ProfileEditRequest extends Request {

    use RequestJsonValidation;

    
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        
            $validationArray = [
                'first_name' => 'required',
                'mobile' => $this->checkMobile(),
                'country_code' => 'max:5',
                'ext' => 'max:5',
                'phone_number' => 'max:15',
            ];

            if(!$this->input('profile_pic')){
                $profilePicValidation = ['profile_pic' => 'mimes:png,jpeg,jpg'];
                $validationArray = array_merge($validationArray,$profilePicValidation);
            }
            return $validationArray;
    }

    /**
     *
     * Check the mobile number is unique or not
     * @return string
     */
    public function checkMobile() {
        $rule = 'numeric';
        if (\Auth::user()->mobile != Request::get('mobile')) {
            $rule .= '|unique:users';
        }
        if (getAccountActivationOptionValue() == "mobile" || getAccountActivationOptionValue() == "email,mobile") {
            $rule .= '|required';
        }
        return $rule;
    }

}

?>