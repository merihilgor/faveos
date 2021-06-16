<?php

namespace App\Plugins\ServiceDesk\database\seeds\v_3_5_0;

use App\Plugins\ServiceDesk\Model\FormGroup\FormGroup;
use App\Model\helpdesk\Form\FormField;
use App\Model\helpdesk\Form\FormFieldLabel;
use App\Model\helpdesk\Form\FormFieldOption;
use DB;

/**
 * UpgradeOldFormSeeder upgrades old sd formbuilder forms to new sd formgroup forms
 *
 */
class UpgradeOldFormSeeder
{
  /**
   * method to convert old form to new form
   * @return null
   */
  public function convertOldFormToNewFormSeeder()
  {
    $oldSdForms = DB::table('sd_forms')->get();
    foreach ($oldSdForms as $form) {
      $formGroup = FormGroup::updateOrCreate(['name' => $form->title, 'group_type' => 'asset']);
      $formFields = json_decode($form->json);
      $this->setFormFieldsFromNodes($formFields, 0, $formGroup->id);
      $formLinkedWithAssetType = DB::table('sd_asset_type_form')->where('form_id', $form->id)->first();
      if ($formLinkedWithAssetType) {
        $formGroup->assetTypes()->sync($formLinkedWithAssetType->asset_type_id);
      }
    }
    $this->updateNestedSelectFormField();
  }

  /**
   * Method to set nodes from options
   * @param $options
   * @param $formFieldId
   * @return null
   */
  private function setNodesFromOptions($options, $formFieldId)
  {
    $sortOrder = 0;
    foreach ($options as $option) {
      $optionId = FormFieldOption::updateOrCreate(['form_field_id' => $formFieldId, 'sort_order' => $sortOrder])->id;
      FormFieldLabel::updateOrCreate(['language' => 'en', 'meant_for' => 'option', 'labelable_id' => $optionId, 'labelable_type' => FormFieldOption::class, 'label' => $option->optionvalue]);
      $sortOrder += 1;
      if (!empty($option->nodes)) {
        $this->setFormFieldsFromNodes($option->nodes, 0, NULL, $optionId);
      }
    }
  }

  /**
   * Method to set form fields from nodes or form
   * @param $nodes
   * @param $sortOrder
   * @param formGroupId
   * @param optionId
   * @return null
   */
  private function setFormFieldsFromNodes($nodes, $sortOrder = 0, $formGroupId = NULL, $optionId = NULL)
  {
    foreach ($nodes as $formField) {
      $formFieldId = FormField::updateOrCreate(['title' => ucfirst($formField->title), 'type' => $formField->type, 'required_for_agent' => $formField->required, 'display_for_agent' => 1, 'is_edit_visible' => 1, 'is_active' => 1, 'form_group_id' => $formGroupId, 'is_deletable' => 1, 'is_customizable' => 1, 'is_observable' => 1, 'is_filterable' => 1, 'is_agent_config' => 1, 'is_user_config' => 0, 'sort_order' => $sortOrder, 'option_id' => $optionId])->id;
      FormFieldLabel::updateOrCreate(['language' => 'en', 'label' => $formField->label, 'meant_for' => 'form_field', 'labelable_id' => $formFieldId, 'labelable_type' => FormField::class]);
      if (!empty($formField->options)) {
        $this->setNodesFromOptions($formField->options, $formFieldId);
      }
      $sortOrder += 1;
    }
  }

  /*
   * method to update Nested Select to Select in form field table
   */
  private function updateNestedSelectFormField()
  {
    FormField::where('title', 'Nested Select')->update(['title'=>'Select']);
  }

}
