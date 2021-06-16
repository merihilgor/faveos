<?php

namespace App;

use App\Http\Controllers\Common\PhpMailController;
use App\Http\Controllers\Common\TemplateVariablesController;
use App\Model\Common\Template;
use App\Model\Common\TemplateSet;
use App\Model\helpdesk\Import\Import;
use App\Traits\UserImport;
use Illuminate\Support\Facades\Log;

class ImportProcessor
{
    use UserImport;

    private $mappings;

    private $reader;

    private $filePathName;

    private $headerColumns;

    public function __construct($reader = null, $filePathName = null, $mappings = null, $headerColumns = null)
    {
        $this->reader = $reader;
        $this->filePathName = $filePathName;
        $this->mappings = $mappings;
        $this->headerColumns = $headerColumns;
        $this->sendEmailNotification = true; //for sending email notifications, it is trait property
    }

    /**
     * @param $attribute
     * @param $userObject
     * @return string|array
     */
    protected function getAttributeValue($attribute, $userObject)
    {
        $thirdPartyAttributeValue = $this->getThirdPartyAttributeByFaveoAttribute($attribute);

        if (!$thirdPartyAttributeValue or $thirdPartyAttributeValue === 'Do not Import') {
            return $this->getDefaultAttributeValue($attribute, $userObject);
        }

        $key = array_search($thirdPartyAttributeValue, $this->headerColumns);

        $attributeValue = $userObject[$key];

        if ($attribute == 'department') {
            return (array)$this->createOrUpdateDepartment($attributeValue);
        }

        if ($attribute == 'organization') {
            return (array)$this->createOrUpdateOrganization($attributeValue);
        }

        if ($attribute == 'role') {

            if (!in_array(strtolower((string)$attributeValue), ['user','admin','agent'])) {
                return 'user';
            } else {
                return strtolower((string)$attributeValue);
            }

        }

        return $attributeValue;
    }

    /**
     * @param $faveoAttribute
     * @return string|null
     */
    protected function getThirdPartyAttributeByFaveoAttribute($faveoAttribute): ?string
    {
        $key = array_search($faveoAttribute, array_column($this->mappings, 'name'));
        return isset($this->mappings[$key]['mapped_to']) ? $this->mappings[$key]['mapped_to'] : '' ;
    }

    /**
     * Determines whether the attribute can be over writeable
     * @param $faveoAttribute
     * @return bool
     */
    protected function isOverwriteAllowed($faveoAttribute): bool
    {
        $key = array_search($faveoAttribute, array_column($this->mappings, 'name'));
        return (bool) $this->mappings[$key]['overwrite'];
    }

    /**
     * This method runs in queue and performs importing
     */
    public function importHandler()
    {
        $spreadSheet = $this->reader->load($this->filePathName);

        $spreadSheetData = $spreadSheet->getActiveSheet()->toArray();

        array_shift($spreadSheetData); //removing header row; otherwise it will also be created as user

        $this->handleBulk((object)$spreadSheetData);

        unlink($this->filePathName);

    }

    /**
     * Returns collection of faveo attributes
     * @return \Illuminate\Support\Collection
     */
    public function getFaveoAttributes()
    {
        return $this->getFaveoAttributeList();
    }

    /**
     * gets default attributes values
     * @param  string $faveoAttribute
     * @return string|array
     */
    private function getDefaultAttributeValue($faveoAttribute, $user)
    {
        switch ($faveoAttribute) {
            case 'import_identifier':
                return $this->getAttributeValue('user_name', $user);
                break;
            case 'role':
                return 'user';
                break;
            case 'department':
            case 'organization':
            case 'org_dept':
                return [];
                break;

            default:
                return '';
        }
    }

    protected function sendRegistrationMails(User $user)
    {
        $defaultTemplateSet = TemplateSet::where('active', '=', 1)->first(['id']);
        $template = Template::where(
            [
                'name' => 'template-register-confirmation-with-account-details',
                'set_id' => $defaultTemplateSet->id])->first(['subject', 'message'
            ]
        );

        if ($template) {
            $passwordString = str_random(8);

            $user->password = \Hash::make($passwordString);
            $user->save();

            $templateVariables = [
                'receiver_name' => $user->user_name,
                'new_user_email' => $user->email,
                'user_password' => $passwordString,

            ];
            $templateVariableValues = (new TemplateVariablesController)->getVariableValues($templateVariables);
            $message = $template->message;

            foreach ($templateVariableValues as $k => $v) {
                $replaced = str_replace($k, $v, $message);
                $message = $replaced;
            }

            $mailBody = $message;

            $toAddress = [
                'name' => "",
                'email' => $user->email
            ];

            $message = [
                'subject' => $template->subject,
                'scenario' => null,
                'body' => $mailBody
            ];

            $mail = new PhpMailController();

            $from = $mail->mailfrom('1', '0');

            $mail->sendmail($from, $toAddress, $message,[],[]);

        }
    }

}
