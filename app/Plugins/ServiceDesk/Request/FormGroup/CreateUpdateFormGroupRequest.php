<?php

namespace App\Plugins\ServiceDesk\Request\FormGroup;

use App\Http\Requests\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Auth;

/**
 * validates create and update form group request
 *
 */
class CreateUpdateFormGroupRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(!Auth::check()){
          return false;
        }

        if(Auth::user()->role != 'admin'){
          return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
      $validationRules = ['name'=>'required|unique:form_groups,name,'.$this->id];

      if($this->id){
        $validationRules['id'] = 'exists:form_groups';
      }

      $validationRules['asset_type_ids'] = 'array';

      $validationRules['form-fields'] = 'required|array|min:1';

      return $validationRules;
    }

    /**
     * This method gets called automatically everytime in FormRequest class to which Request class
     * is getting inherited. So implementing this method here throws a json response and terminate
     * further processing of request which avoids a redirect (which is the default implementation).
     *
     * @param Validator $validator
     * @throw HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        //sending only the first error as object
        $errors = $validator->errors()->messages();
        $formattedErrors = [];
        foreach ($errors as $key => $message) {
          if($key == 'form-fields') {
            throw new HttpResponseException(
              errorResponse(trans('lang.form_fields_cannot_be_empty'), 400)
            );
          }
          $formattedErrors[$key] = $message[0];
        }

        throw new HttpResponseException(errorResponse($formattedErrors, 412));
    }
}
