<?php
namespace App\Plugins\ServiceDesk\Request\Change;
use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;
/**
 * validates attach ticket to change request
 *
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class AttachTicketToChangeRequest extends Request
{
	use RequestJsonValidation;
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
		$rules = [
            'change_id' => 'required|numeric|exists:sd_changes,id',
            'ticket_id' => 'sometimes|numeric|exists:tickets,id',
            'type' => 'required'
        ];
        return $rules;
    }
}