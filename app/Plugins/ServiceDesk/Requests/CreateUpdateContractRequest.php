<?php

namespace App\Plugins\ServiceDesk\Requests;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

/**
 * validates create and update contract request
 *
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 *
 * @author Abhishek Kumar Shashi <abhishek.shashi@ladybirdweb.com>
 */
class CreateUpdateContractRequest extends Request {

    use RequestJsonValidation;

    /**
     * method to update email validation message
     * @param Validator $validator
     * @return null
     */
    protected function failedValidation(Validator $validator) {
        $errors = $validator->errors()->messages();
    
        $this->updateEmailValidationMessage($errors);

        foreach ($errors as $key => $message) {
            $errors[$key] = reset($message);
        }
    
        throw new HttpResponseException(errorResponse($errors, 412));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {

        return [
             'name' => 'required|max:50|unique:sd_contracts,name,'. $this->id,
             'cost' => 'required|integer|min:1',
             'license_type_id' => 'sometimes|integer|min:1|exists:sd_license_types,id',
             'licensce_count' => 'sometimes|integer|min:1',
             'description' => 'required',
             'contract_type_id' => 'required|exists:sd_contract_types,id',
             'approver_id' => 'sometimes|exists:users,id',
             'vendor_id' => 'exists:sd_vendors,id',
             'user_ids' => 'exists:users,id',
             'organziation_ids' => 'exists:organization,id',
             'contract_start_date' => 'required',
             'contract_end_date' => 'required',
             'notify_before' => 'sometimes|integer|min:1',
             'identifier' => 'sometimes',
             'notify_agent_ids' => 'sometimes|exists:users,id',
             'asset_ids' => 'sometimes|exists:sd_assets,id',
             'email_ids.*' => 'sometimes|email',
             'status_id' => 'required|integer|exists:sd_contract_statuses,id',
             'renewal_status_id' => 'sometimes|exists:sd_contract_statuses,id'

        ];
    }

    /**
     * method to upadte email validation message
     * @param array $errors
     * @return null
     */
    private function updateEmailValidationMessage(array &$errors) {
        $emailIdValidationMessages = array_filter(array_keys($errors), function($error){
                            if (strpos($error, 'email_ids') === 0) {
                                return $error;
                            }
                        });

        if ($emailIdValidationMessages) {
            $errors['notify_to'] = ['Email Address is invalid'];
            array_walk($emailIdValidationMessages, function($messageKey) use(&$errors){
                unset($errors[$messageKey]);
            });
        }
    }
}
