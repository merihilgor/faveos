<?php
namespace App\Plugins\Calendar\Requests;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

class TaskListRequest extends Request{

    use RequestJsonValidation;

	public function authorize(){
        return true;
    }

    public function rules(){

        $rules =  [
            'name' => 'required|string',
            'project_id' => 'required',
        ];

        if ($this->getMethod() == 'POST') {
            $rules['name'] = $rules['name']."|unique:task_lists";
        }

        return $rules;
    }

}

