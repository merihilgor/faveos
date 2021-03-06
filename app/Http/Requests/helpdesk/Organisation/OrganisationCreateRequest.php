<?php

namespace App\Http\Requests\helpdesk\Organisation;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @author  avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class OrganisationCreateRequest extends BaseOrganisationRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $userValidation = $this->fieldsValidation('organisation','agent_panel');

        // removes all the attachments from custom fields and put it in `attachments` key
        $this->request->replace($this->getFormattedParameterWithAttachments($this->request->all()));

      
        // default user validations
        $defaultRules = [
          'organisation_name' => 'required|unique:organization,name',
          'organisation_domain_name' => "unique_domain|valid_domain",
        ];

        if (array_key_exists("organisation_logo",\Request::all())) {

            $defaultRules['organisation_logo'] = $this->imageFileValidation(\Request::all('organisation_logo'));
        }

        return array_merge($userValidation, $defaultRules);
    }

    private function imageFileValidation($files){
      $organisationLogo = [];
      if (is_array($files['organisation_logo'])) {
        $organisationLogo = (strpos($files['organisation_logo'][0]->getClientMimeType(),'image') !== false) ? []:$this->customValidateMessage();
      }
      return  $organisationLogo;
    }

    /*
    * for custom message with 400 status code
    */
    private function customValidateMessage(){

        throw new HttpResponseException(errorResponse(\Lang::get('lang.the_organisation_logo_must_be_a_file_of_type'), 400));
    }

}
