<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use App\User;
use Illuminate\Http\Request;
use Lang;
use Validator;
use Auth;
use App\Traits\UserVerificationHelper;


/**
 * This controller handles the verification of email for new users.
 *
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
*/
class VerifyAccountController extends Controller
{
    use UserVerificationHelper;
    /**
     *post updateEmail by the passed token
     *@param $request Request (oldEmail, newEmail)
     *@return Response
    */
    public function postUpdateEmailVerification(Request $request)
    {
        $user = User::where('email',  $request->oldEmail)->first();
        $newEmail = $request->newEmail;
        $validation = $this->validateEmail($request);
        
        if (!$user) {
            return errorResponse(Lang::get('lang.user_not_found_with_this_email'));
        }
        
        if ($validation) {
            $validation = array('email_address' => 'The email has already been taken.');
            return errorResponse($validation, 412);
        }
        
        if ($newEmail) {
            $user->email = $newEmail;
            $user->user_name = ($user->user_name == $request->oldEmail) ? $newEmail : $user->user_name;
        }
        
        $code = str_random(60);
        $user->email_verify = $code;
        $user->save();
        $this->sendVerificationEmail($user);
        return successResponse(Lang::get('lang.verify-email-message', ['email' => substr_replace($user->email, '***', strpos($user->email, '@')-3, 3)]));
    }

    /**
     * Function to validate email
     *
     * @param \Illuminate\Http\Request  $request
     * @return $errors
    */
    public function validateEmail($request)
    {   
        if (($request->oldEmail == $request->newEmail) || empty($request->newEmail)) {
            return null;
        }
        $validator = Validator::make($request->all(), [
            'newEmail'  =>  'email|unique:users,email|unique:emails,email_address',
            ]);

        if ($validator->fails()) {
            $errors  =  $validator->errors()->messages();
            return $errors;
        }
    }

    /**
     *post verifyEmail by passed token
     *@param string $token
     *@return Response
    */
    public function postAccountActivate($token)
    {   
        $auth = Auth::user();
        $user = User::where('email_verify', $token)->first();
        
        if (!is_null($auth)) {
            if (($auth->active == 1) && ($auth->role != 'user')) {
                return errorResponse(['redirect_to' => faveoUrl('dashboard')], 302);
            }
            if (($auth->active == 1) && ($auth->role == 'user')) {
                return errorResponse(['redirect_to' => faveoUrl('')], 302);
            }
        }

        if ($token == 1) {
            return errorResponse(Lang::get('lang.sorry_you_are_not_allowed_token_expired'));
        }

        if (!$user) {
            return errorResponse(Lang::get('lang.user_not_found'));
        }
    
        $user->update(['active'=>1, 'email_verify'=>1]);

        return successResponse('Your email: '.$user->email. ' is verified and account activated.');

    }

     

}