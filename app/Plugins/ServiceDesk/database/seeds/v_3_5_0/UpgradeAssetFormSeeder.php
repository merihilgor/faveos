<?php

namespace App\Plugins\ServiceDesk\database\seeds\v_3_5_0;

use App\Model\helpdesk\Form\FormField;
use App\Model\helpdesk\Form\FormFieldLabel;
use App\Model\helpdesk\Form\FormFieldOption;
use DB;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\Model\helpdesk\Form\CustomFormValue;

/**
 * UpgradeAssetFormSeeder upgrades old asset form to new asset form
 *
 */
class UpgradeAssetFormSeeder
{
  protected $assetId;

  /**
   * method to convert old asset form to new asset form
   * @return null
   */
  public function convertOldAssetFormToNewAssetFormSeeder()
  {
    $assetsFormInJsonWithValues = DB::table('sd_assets_frombilder')->get();
    foreach ($assetsFormInJsonWithValues as $assetForm) {
      $formValues = $assetForm->json != 'null' ? json_decode($assetForm->json) : [];
      $this->assetId = $assetForm->asset_id;
      $asset = SdAssets::where('id', $this->assetId)->with('assetType.formGroups.formFields')->first(['name','asset_type_id']);
      if ($asset) {
        $arrayData = $asset->toArray();
        $keys = ['asset_type', 'form_groups', 'form_fields'];
        while (isset($arrayData[current($keys)])) {
          $key = current($keys);
          $arrayData = $arrayData[$key];
          array_shift($keys);
          if (($key == 'form_groups') && !empty($arrayData) && array_key_exists('form_fields', current($arrayData))) {
            $formGroupFields = current($arrayData)['form_fields'];
          }
        }
      }

      if (isset($formGroupFields)) {
        $this->parseFields($formValues, $formGroupFields);
      }
        
    }
  }

  /**
   * method to parse fields
   * @param $formValues
   * @param $formGroupFields
   * @param $formFieldId
   * @return null
   */
  private function parseFields($formValues, $formGroupFields = null, $formFieldId = null)
  {
    for ($i=0; $i < count($formValues); $i++) { 
        $formValue = $formValues[$i];
        $formFieldId = $formFieldId ?: $formGroupFields[$i]['id'];
        $value = '';
        if (($formValue->type == 'select') || ($formValue->type == 'radio')) {
          $this->setFormFieldsFromNodes($formValue, $formFieldId);
        }
        elseif (($formValue->type == 'checkbox')) {
          $value = $this->addCheckBoxCheckedValues($formValue);
        }else {
          $value = $formValue->value;
        }
        if ($value) {
          $this->addCustomFormValue($formFieldId, $value);
        }
        $formFieldId = null;
      }
  }

  /**
   * method to set form fields from nodes or form
   * @param $nodes
   * @param $formFieldId
   * @return null
   */
  private function setFormFieldsFromNodes($nodes, $formFieldId)
  {
    if ($nodes->title == 'Radio') {
      $nodes->value = json_decode($nodes->value);
    }
    $this->addCustomFormValue($formFieldId, $nodes->value->optionvalue);
    if (isset($nodes->value->nodes)) {
      $formValue = reset($nodes->value->nodes);
      $fieldType = $formValue->type;
      $optionIds = FormFieldOption::where('form_field_id', $formFieldId)->pluck('id');
      $formField = FormField::whereIn('option_id', $optionIds)->where('type', $fieldType)->first();
      if ($formField) {
        $this->parseFields($nodes->value->nodes, '', $formField->id);
      }
    }
  }

  /**
   * method to add custom value to custom_form_value tavle
   * @param $formFieldId
   * @param $value
   * @return null
   */
  private function addCustomFormValue($formFieldId, $value)
  {
    CustomFormValue::updateOrCreate([
        'form_field_id' => $formFieldId,
        'custom_id' => $this->assetId,
        'custom_type' => SdAssets::class
      ],
      [
        'form_field_id' => $formFieldId,
        'value' => $value,
        'custom_id' => $this->assetId,
        'custom_type' => SdAssets::class,
        'type' => 'asset_type'
      ]);
  }

  /**
   * method to handle checkbox field values
   * @param $formValue
   * @return $checkedCheckbox;
   */
  private function addCheckBoxCheckedValues($formValue)
  {
    $checkedCheckbox = [];
    foreach ($formValue->options as $option) {
      if ($option->value == 'true') {
        array_push($checkedCheckbox, $option->optionvalue);
      }
    }
    return $checkedCheckbox;
  }

}
