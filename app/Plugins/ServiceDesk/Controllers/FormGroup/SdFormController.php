<?php
namespace App\Plugins\ServiceDesk\Controllers\FormGroup;

use App\Http\Controllers\Utility\FormController;
use App\Plugins\ServiceDesk\Model\FormGroup\FormGroup as SdFormGroup;
use App\Plugins\ServiceDesk\Request\FormGroup\CreateUpdateFormGroupRequest;
use App\Plugins\ServiceDesk\Model\FormGroup\FormField;
use Illuminate\Http\Request;

/**
 * SdFormController class to maintain CRUD functionality for servicedesk admin panel formbuilder
 * SdFormController is extends to FormController of Helpdesk
 *
 */
class SdFormController extends FormController
{
    public function __construct() {
        $this->middleware('role.admin');
    }

    /**
     * gets form group
     * @param int $groupId
     * @return array  $formGroup  
     */
    public function getFormGroupFormFields(SdFormGroup $formGroup)
    {
        $baseQuery = SdFormGroup::with('assetTypes:sd_asset_types.id,name')
            ->whereId($formGroup->id);

        $formGroup = (new FormField)->getFormQueryByParentQuery($baseQuery)->first();

        return successResponse('', $formGroup);
    }

    /**
     * method to create or update form group
     * @param CreateUpdateFormGroupRequest $request   
     * @return Response 
     */
    public function createUpdateFormGroupAndFormFields(CreateUpdateFormGroupRequest $request)
    {   
        $formGroup = new SdFormGroup();
        $formGroupArray = $request->toArray();
        $formGroupArray['group_type'] = 'asset';
        $formGroup = $this->postFormGroup($formGroupArray, $formGroup);

        if ($request->has('asset_type_ids')) {
            $formGroup->assetTypes()->sync($formGroupArray['asset_type_ids']);
        }

        return successResponse(trans('lang.success_update'));
    }

    /**
     * method for form builder create blade page
     * @return view
     */
    public function formGroupCreatePage() {
        return view('service::form-group.create-and-edit');
    }

    /**
     * method for form builder edit blade page
     * @param SdFormGroup $formGroup
     * @return view
     */
    public function formGroupEditPage(SdFormGroup $formGroup) {
        $formGroupId = $formGroup->id;
        return view("service::form-group.create-and-edit", compact('formGroupId'));
    }

    /**
     * method to get all the asset related custom fields including child fields
     * of asset type along with their current label
     * @return Response
     */
    public function getAssetCustomFieldList()
    {
      $customFields = FormField::getAssetCustomFieldList();
      return successResponse('', $customFields);
    }

    /**
     * method to update to append extra prefix for asset formgroup edit URL
     * @param $otherUrlPrefix
     * @return null
     */
    public function updateServiceDeskPrefix(&$otherUrlPrefix)
    {
        $otherUrlPrefix = '/service-desk';
    }

}
