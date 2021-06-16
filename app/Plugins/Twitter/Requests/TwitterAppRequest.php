<?php
namespace App\Plugins\Twitter\Requests;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;
/**
 * validates the StorageController@ckEditorUpload request
 */
class TwitterAppRequest extends Request
{
    use RequestJsonValidation;
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
        $rules =  [
            'consumer_api_secret' => 'required|string',
            'consumer_api_key'    => 'required|string',
            'access_token'        => 'required|string',
            'access_token_secret' => 'required|string',
        ];

        if($this->getMethod() == "POST") {
            $rules = array_map(function($item){
                return $item."|unique:twitter_app";
            },$rules);
        }

        $rules['hashtag_text'] = 'required';
        return $rules;
    }
}