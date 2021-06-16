<?php

namespace App\Http\Controllers\Auth;

// controllers
use App\Http\Controllers\Common\PhpMailController;
use App\Http\Controllers\Common\SettingsController;
use App\Http\Controllers\Controller;
// request
use App\User;
// model
// classes
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Lang;
use DB;
use Hash;
use Validator;
use Auth;
/**
 * PasswordController.
 *
 * @author      Ladybird <info@ladybirdweb.com>
 */
class PasswordController extends Controller
{
    use ResetsPasswords;

    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    public function __construct(PhpMailController $PhpMailController)
    {
        $this->PhpMailController = $PhpMailController;
        $this->middleware('guest')->except(['changePassword']);
    }

    /**
     * Display the form to request a password reset link.
     *
     * @return Response
     */
    public function getEmail()
    {
        return view('auth.password');
    }

     /**
     * Display the form to request a password reset link.
     *
     * @return Response
     */
    public function postEmail(Request $request)
    {
        try{
            $validate = \Validator::make($request->all(), ['email' => 'required']);
            if ($validate->fails()) {
                return errorResponse($validate->errors());
            }
            $email = $request->all('email');
            $date = date('Y-m-d H:i:s');
            \Event::dispatch('reset.password',array());
            $user = User::where('email', $email)->first();
            if(!$user){
                return errorResponse(Lang::get("lang.we_can't_find_a_user_with_that_e-mail_address"));
            }
            
            $user1 = $user->email;
            //gen new code and pass
            $code = str_random(60);
            $password_reset_table = \DB::table('password_resets')->where('email', '=', $user->email)->first();
            if (isset($password_reset_table)) {
                $password_reset_table = \DB::table('password_resets')->where('email', '=', $user->email)->update(['token' => $code, 'created_at' => $date]);
            } else {
                $create_password_reset = \DB::table('password_resets')->insert(['email' => $user->email, 'token' => $code, 'created_at' => $date]);
            }
            
            $this->PhpMailController->sendmail($from = $this->PhpMailController->mailfrom('1', '0'), $to = ['name' => $user->user_name, 'email' => $user->email], $message = ['subject' => 'Your Password Reset Link', 'scenario' => 'reset-password'], $template_variables = ['user' => $user->first_name, 'email_address' => $user->email, 'password_reset_link' => url('reset/password/'.$code.'?email='.$user->email)],false);

            if ($user->mobile != '' && $user->mobile != null) {
                if($user->first_name) {
                    $name = $user->first_name;
                } else {
                    $name = $user->user_name;
                }

                $value = [
                'url'    => url('password/reset/'.$code),
                'name'   => $name,
                'mobile' => $user->mobile,
                'code'   => $user->country_code];
                \Event::dispatch('reset.password2',array($value));
            }

            return successResponse(Lang::get('lang.we_have_e-mailed_your_password_reset_link'));
        } catch (\Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return json
     */
    public function passwordReset(Request $request){
        $validate = \Validator::make($request->all(), ['token' => 'required','password'=>'required']);
        
        if ($validate->fails()) {
            return errorResponse($validate->errors());
        }
        $token = $request->input('token');

        $query = DB::table('password_resets')->where('token',$token);
        $passwordResetUser = $query->first();
        if(!$passwordResetUser){
            return errorResponse(Lang::get('lang.reset-token-expired-or-not-found'));
        }
        
        $newPassword = $request->input('password');
        
        if(!$this->updatePassword($passwordResetUser->email, $newPassword)){
            return errorResponse(Lang::get('lang.fails'));
        }

        $query->delete();
        return successResponse(Lang::get('lang.success'));
    }

    /**
     * overwrites user's password
     *
     * @param  $email String
     * @param  $newPassword String
     * @return bool
     */
    private function updatePassword($email, $newPassword){
        $password = bcrypt($newPassword);
        $isSuccess = User::where('email',$email)->update(['password'=>$password]);
        if(!$isSuccess){
            return false;
        }
        return true;
    } 

    
    public function changePassword(Request $request)
    {
        $validate = Validator::make($request->all(), (['old_password' => 'required','new_password'=>'required|min:6', 'confirm_password' => 'required|same:new_password']));

        if ($validate->fails()) {

            $fail = $validate->errors();
                foreach (json_decode($fail) as $key => $value) {
                    return errorResponse(implode(',',$value));
                }
        }           
        $oldPassword = $request->input('old_password');
        $user = User::find(Auth::user()->id);
        $passwordHash = $user->password;

        if(!Hash::check($oldPassword, $passwordHash)){
            return errorResponse(Lang::get('lang.password_was_not_updated_incorrect_old_password'));
        }

        $newPassword = $request->input('new_password');
        $newPasswordHash = bcrypt($newPassword);
        $user->password = $newPasswordHash;
        $user->save();
        return successResponse(Lang::get('lang.password_updated_sucessfully'));
    }  
}
