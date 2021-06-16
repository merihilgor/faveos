<?php

namespace App\Plugins\ServiceDesk\database\seeds\v_3_5_0;

use App\Model\helpdesk\Form\FormField;
use App\Model\helpdesk\Form\FormFieldLabel;
use DB;
use App\Model\helpdesk\Form\FormCategory;

/**
 * AssetFormSeeder to seed Asset Form into Helpdesk Formbuilder
 *
 */
class AssetFormSeeder
{
  private $sortOrder, $categoryId;

  /**
   * method to seed asset form
   * 
   * @return null
   */
  public function assetFormSeed()
  {
    $this->categoryId = FormCategory::updateOrCreate(['category' => 'asset', 'type' => 'servicedesk', 'name' => 'Asset'])->id;
    $this->sortOrder = 0;
    $this->createForm('Name', 'text', true, 'name', false);
    $this->createForm('Identifier', 'text', false, 'identifier', true);
    $this->createForm('Department', 'api', true, 'department_id', false, '/api/dependency/departments');
    $this->createForm('Impact Type', 'api', false, 'impact_type_id', true, '/service-desk/api/dependency/impacts');
    $this->createForm('Organization', 'api', false, 'organization_id', true, '/api/dependency/organizations');
    $this->createForm('Location', 'api', false, 'location_id', true, '/api/dependency/locations');
    $this->createForm('Managed By', 'api', false, 'managed_by_id', true, '/api/dependency/agents?meta=true');
    $this->createForm('Used By', 'api', false, 'used_by_id', true, '/api/dependency/users?meta=true');
    $this->createForm('Product', 'api', false, 'product_id', true, '/service-desk/api/dependency/products');
    $this->createForm('Asset Type', 'api', true, 'asset_type_id', false, '/service-desk/api/dependency/asset_types?meta=true');
    $this->createForm('Assigned On', 'date', false, 'assigned_on', true);
    $this->createForm('Description', 'textarea', true, 'description', false);
    $this->createForm('Attachments', 'file', false, 'attachments', true);
  }

  /*
   * NOTE: this method uses large number of parameters and this approach should not be used until it is very much required
   * REASON FOR HAVING LARGE NUMBER OF PARAMETERS : so we can know just looking the the documentation that which field is
   * required in which order
   * method to create form data based on the parameters passed
   * @param string $title
   * @param string $type
   * @param boolean $requiredForUser
   * @param string $unique
   * @param boolean $isAgentConfig
   * @param string $apiEndPoint
   * @return null
  */
   
  private function createForm($title, $type, $requiredForAgent, $unique, $isAgentConfig, $apiEndPoint = NULL)
  {
    $form['category_id'] = $this->categoryId;
    $form['title'] = $title;
    $form['type'] = $type;
    $form['required_for_agent'] = $requiredForAgent;
    $form['unique'] = $unique;
    $form['is_agent_config'] = $isAgentConfig;
    $form['api_info'] = "url:=$apiEndPoint;;";
    $form['sort_order'] = ++$this->sortOrder;

    $this->formFieldDefaultParameters($form);
    $formFieldId = FormField::updateOrCreate($form)->id;
    $formFieldLabel['labelable_id'] = $formFieldId;
    $formFieldLabel['label'] = $form['title'];
    $this->formFieldLabelDefaultParameters($formFieldLabel);
    FormFieldLabel::updateOrCreate($formFieldLabel);
  }

  /**
   * method to update default fields in $form array
   * @param array $form
   * @return null
   */
  public function formFieldDefaultParameters(&$form)
  {
    // for form fields
    $form['category_type'] = FormCategory::class;
    $form['required_for_user'] = false;
    $form['display_for_agent'] = true;
    $form['display_for_user'] = false;
    $form['default'] = true;
    $form['is_linked'] = false;
    $form['media_option'] = false;
    $form['pattern'] = '';
    $form['option_id'] = NULL;
    $form['is_edit_visible'] = true;
    $form['is_active'] = true;
    $form['is_locked'] = false;
    $form['form_group_id'] = NULL;
    $form['is_deletable'] = false;
    $form['is_customizable'] = true;
    $form['is_observable'] = true;
    $form['is_filterable'] = true;
    $form['is_user_config'] = false;
  }

  /**
   * method to update field label default values in $formFieldLabel Array
   * @param array $formFieldLabel
   * @return null
   */
  public function formFieldLabelDefaultParameters(&$formFieldLabel)
  {
    // for form field labels
    $formFieldLabel['language'] = 'en';
    $formFieldLabel['meant_for'] = 'form_field';
    $formFieldLabel['labelable_type'] = FormField::class;
    $formFieldLabel['description'] = '';
  }

}
