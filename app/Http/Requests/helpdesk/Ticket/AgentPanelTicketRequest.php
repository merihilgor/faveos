<?php

namespace App\Http\Requests\helpdesk\Ticket;

use App\Http\Requests\Request;
use App\Traits\CustomFieldBaseRequest;
use App\User;
use App\Traits\RequestJsonValidation;
use Auth;
use App\Policies\TicketPolicy;
use Illuminate\Http\Exceptions\HttpResponseException;
use Lang;

/**
 * Ticket create request from agent panel
 * @author  avinash kumar <avinash.kumar@ladybirdweb.com>
 */

class AgentPanelTicketRequest extends Request
{
    use CustomFieldBaseRequest, RequestJsonValidation;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
      if(Auth::user() && Auth::user()->role != 'user' && (new TicketPolicy)->create()){
        return true;
      }
      return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // checking if requester is valid
        $this->validateRequester();

        $this->checkTicketAssignPermission($this->assigned_id);

        $ticketValidation = $this->fieldsValidation('ticket','agent_panel');

        // removes all the attachments from custom fields and put it in `attachments` key
        $this->request->replace($this->getFormattedParameterWithAttachments($this->request->all()));

        return $ticketValidation;
    }

    /**
     * Checks if the requester is valid
     * @return null
     * @throw HttpResponseException
     */
    private function validateRequester()
    {
      if(!$this->file('requester')){

        if(!User::whereId($this->requester)->count()){
            throw new HttpResponseException(errorResponse(['requester' => Lang::get('lang.requester_does_not_exist_create_new')], 412));
        }
      }
    }
}