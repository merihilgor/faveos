<?php

namespace App\Plugins\ServiceDesk\Request\Contract;

use App\Http\Requests\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\RequestJsonValidation;

/**
 * validates the contract expiry reminder request
 *
 * @author Abhishek Kumar Shashi <abhishek.shashi@ladybirdweb.com>
 */

class ExpiryReminderContractRequest extends Request
{
	use RequestJsonValidation;

    /**
     * Get the validation rules that apply to the request.
     * @return array
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
             'notify_before' => 'required|integer|min:1',
             'notify_agent_ids' => 'required|exists:users,id',
             'email_ids.*' => 'required|email',
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
