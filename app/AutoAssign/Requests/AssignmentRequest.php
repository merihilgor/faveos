<?php

namespace App\AutoAssign\Requests;

use App\Http\Requests\Request;

/**
 * InstallerRequest.
 *
 * @author  Ladybird <info@ladybirdweb.com>
 */
class AssignmentRequest extends Request
{
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
        return [
            'department_list' => 'required_if:assign_department_option,specific|array'
        ];
    }

    // public function messages()
    // {
    //     return [
    //         'department_list.required_if' => 'You can not leave department field empty while auto-assignement is set for specific department'
    //     ];
    // }
}
