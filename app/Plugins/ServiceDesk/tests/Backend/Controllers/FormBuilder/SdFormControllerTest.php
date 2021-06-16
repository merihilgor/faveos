<?php
namespace App\Plugins\ServiceDesk\tests\Backend\Controllers\FormBuilder;

use Tests\AddOnTestCase;
use App\Plugins\ServiceDesk\Model\FormGroup\FormGroup;
use App\Model\helpdesk\Form\FormCategory;

/**
 * SdFormControllerTest to test SdFormController CRUD functionality
 * 
 */
class SdFormControllerTest extends AddOnTestCase
{
  public function setUp() : void
    {
      parent::setUp();
      $this->getLoggedInUserForWeb('admin');
    }

    //for making nested layers in formFields array
    private $count = 1;
    private $category;

    /**
     * method to create category
     * @return null
     */
    private function createCategory(){
        $this->category = str_random(16);
        FormCategory::create(['category'=> $this->category]);
    }

    /**
     * method to create JSON for FormFields
     * @param string $title
     * @param boolean $required
     * @param boolean $display
     * @return array $form;
     */
    private function createJsonForFormFields($title = 'test_type', $required = true, $display = true)
    {
        $this->createCategory();
        $form = [];
        $form['id'] = null;
        $form['title'] = $title;
        $form['type'] = 'test type';
        $form['display_for_agent'] = $display;
        $form['display_for_user'] = $display;
        $form['required_for_agent'] = $required;
        $form['required_for_user'] = $required;
        $form['default'] = true;
        $form['is_linked'] = false;
        $form['media_option'] = false;
        $form['sort_order'] = 6;
        $form['labels_for_user'] = [$this->getLabel(null)];
        $form['labels_for_agent'] = [$this->getLabel(null)];
        $form['labels_for_validation'] = [$this->getLabel(null)];
        $form['options'] = [ $this->getOptions($required, $display)];
        return [$form];
    }

    /** 
     * method to get options
     * @param boolean $required
     * @param boolean $display
     * @return array $option
     */
    private function getOptions($required = true, $display = true){
        $option = [];
        $option['id'] = null;
        $option['labels'] = [$this->getLabel(null), $this->getLabel(null)];
        $option['nodes'] = ($this->count > 2) ? [] :[$this->getNodes($required, $display)];
        return $option;
    }

    /**
     * method to get nodes
     * @param boolean $required
     * @param boolean $display
     * @return array $form
     */
    private function getNodes($required = true, $display = true){
        $this->count++;
        $form = [];
        $form['id'] = null;
        $form['title'] = 'node';
        $form['type'] = 'node';
        $form['display_for_agent'] = $display;
        $form['display_for_user'] = $display;
        $form['required_for_agent'] = $required;
        $form['required_for_user'] = $required;
        $form['default'] = true;
        $form['is_linked'] = false;
        $form['media_option'] = false;
        $form['sort_order'] = 6;
        $form['labels_for_user'] = [$this->getLabel(null)];
        $form['labels_for_agent'] = [$this->getLabel(null)];
        $form['labels_for_validation'] = [$this->getLabel(null)];
        $form['options'] = [ $this->getOptions($required, $display)];
        return $form;
    }

    /**
     * method to get label
     * @param integer $id
     * @return array $label
     */
    private function getLabel($id){
        $label = [];
        $label['id'] = $id;
        $label['language'] = 'en';
        $label['label'] = 'test label';
        return $label;
    }

    /** @group createUpdateFormGroupAndFormFields */
    public function test_createUpdateFormGroupAndFormFields_forInvalidFormGroupId()
    {
        $data = $this->createJsonForFormFields();
        $response = $this->call('POST','service-desk/api/post-form-group',['id'=>'wrong_id','name'=>'test_name','form-fields'=>$data]);
        $response->assertStatus(412);
    }

    /** @group createUpdateFormGroupAndFormFields */
    public function test_createUpdateFormGroupAndFormFields_forDuplicateNameForNewRecords()
    {
        $form = FormGroup::create(['name'=>'test_name', 'group_type' => 'asset']);
        $data = $this->createJsonForFormFields();
        $response = $this->call('POST','service-desk/api/post-form-group',['id'=>null,'name'=>'test_name','form-fields'=>$data]);
        $response->assertStatus(412);
    }

    /** @group createUpdateFormGroupAndFormFields */
    public function test_createUpdateFormGroupAndFormFields_forDuplicateNameForExistingRecord()
    {
        $form = FormGroup::create(['name'=>'test_name', 'group_type' => 'asset']);
        $data = $this->createJsonForFormFields();
        $response = $this->call('POST','service-desk/api/post-form-group',['id'=>$form->id,'name'=>'test_name','form-fields'=>$data, 'status'=>true]);
        $response->assertStatus(200);
    }

    /** @group createUpdateFormGroupAndFormFields */
    public function test_createUpdateFormGroupAndFormFields_whenFormFieldsArePassedForANewGroup()
    {
        $data = $this->createJsonForFormFields();
        $response = $this->call('POST','service-desk/api/post-form-group',['id' => null,'form-fields'=>$data, 'name'=>'test_name', 'status'=>true]);
        $response->assertStatus(200);
        $this->assertEquals(1, FormGroup::where('group_type', 'asset')->count());
        $formFields = FormGroup::where('group_type', 'asset')->first()->formFields;
        $this->assertEquals(1, $formFields->count());
    }

    /** @group createUpdateFormGroupAndFormFields */
    public function test_createUpdateFormGroupAndFormFields_whenDepartmentIdsArePassed_shouldLinkThatDepartmentWithFormGroup()
    {
        $data = $this->createJsonForFormFields();
        $response = $this->call('POST','service-desk/api/post-form-group',['id' => null,'form-fields'=>$data, 'name'=>'test_name', 'status'=>true, 'asset_type_ids'=>[1]]);
        $response->assertStatus(200);
        $this->assertEquals(1, FormGroup::where('group_type', 'asset')->first()->assetTypes()->count());
    }

    /** @group getFormGroupFormFields */
    public function test_getFormGroupFormFields_whenWrongFormIdIsPassed()
    {
        $response = $this->call('GET','service-desk/api/get-form-group/wrongId');
        $response->assertStatus(404);
    }

    /** @group getFormGroupFormFields */
    public function test_getFormGroupFormFields_whenFormFieldsArePresentInTheGroup()
    {
        $data = $this->createJsonForFormFields();
        $response = $this->call('POST','service-desk/api/post-form-group',['id' => null,'form-fields'=>$data, 'name'=>'test_name', 'status'=>true]);
        $formGroup = FormGroup::where('group_type', 'asset')->first();
        $response = $this->call('GET','service-desk/api/get-form-group/' . $formGroup->id);
        $response->assertStatus(200);
        $formFieldData = json_decode($response->content(), true)['data']['form_fields'];
        $this->assertNotEquals(0, count($formFieldData));
        $this->assertEquals(count($formFieldData), $formGroup->formFields->count());
    }
}
