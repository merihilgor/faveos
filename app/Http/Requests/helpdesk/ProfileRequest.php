<?php

namespace App\Http\Requests\helpdesk;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

/**
 * ProfileRequest.
 *
 * @author  Ladybird <info@ladybirdweb.com>
 */
class ProfileRequest extends Request {
    
    use RequestJsonValidation;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {

        return [
            'first_name' => 'required',
            'mobile' => $this->checkMobile(),
            'country_code' => 'max:5',
            'ext' => 'max:5',
            'phone_number' => $this->ruleForPhone(),
        ];
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

    /**
     * Implies that phone number is required with extension
     * @param void
     * @return string  rule to validate phone number
     */
    protected function ruleForPhone()
    {
        return ($this->input('ext') != "") ? 'required|max:15' : 'max:15' ;
    }
}
