<?php

namespace App\Http\Controllers\Utility;

use App;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Custom\Form;
use DB;
use Auth;
use App\User;
use App\Model\helpdesk\Agent\Teams;
use App\Model\helpdesk\Agent\Assign_team_agent;
use App\Model\helpdesk\Ticket\Ticket_Status;
use App\Model\helpdesk\Form\FormCategory;
use App\Model\helpdesk\Form\FormField;
use App\Model\helpdesk\Form\FormFieldOption;
use App\Model\helpdesk\Form\FormFieldLabel;
use App\Model\helpdesk\Form\FormGroup;
use App\Http\Requests\helpdesk\FormBuilder\FormGroupRequest;
use Lang;
use Input;
use Exception;
use Cache;
use App\Model\helpdesk\Agent\Department;
use App\Model\helpdesk\Manage\Help_topic as HelpTopic;
use App\Model\helpdesk\Settings\CommonSettings;
use Illuminate\Database\Query\Builder as QueryBuilder;
use App\Http\Requests\helpdesk\Common\FormBuilder\FormFieldRequest;
use App\Http\Requests\helpdesk\Common\DataTableRequest;

class FormController extends Controller
{
    /**
     * gets form for category type where category is passed as parameter
     * @param Request $request      request object with category as parameter.
     *                              for eg. user (for user form),
     *                              ticket (for ticket form), organisation (for organisation form)
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFormFieldsByCategory(Request $request)
    {
        $category = $request->input('category');

        $mode = $request->input('mode');

        $isConfiguringForm = $request->config ?: false;

        $baseQuery = FormCategory::where('category',$category);

        $formFieldObject = new FormField;

        $formCategory = $formFieldObject->getFormQueryByParentQuery($baseQuery, $mode, $isConfiguringForm)->first();

        if(!$formCategory){
            return errorResponse(Lang::get('lang.category_not_found'));
        }

        $formFieldObject->formatFormElements($formCategory, $isConfiguringForm);

        $response = ['form_fields'=> $formCategory->formFields];

        $this->appendMetaDataToResponse($response);

        return successResponse('', $response);
    }

    /**
     * Appends extra data which is not directly related for form fields to the response
     * @param  array  $response
     * @return null
     */
    private function appendMetaDataToResponse(array &$response)
    {
      $batchTicketStatus = (bool)CommonSettings::where('option_name','batch_tickets')->where('status', 1)->count();

      $captchaKey = (string)CommonSettings::where('option_name', 'google')->where('optional_field', 'service_key')->value('option_value');

      $response['language'] = App::getLocale();

      $response['captcha_key'] = $captchaKey;

      $response['batch_ticket_status'] = $batchTicketStatus;

      $response['post_max_size'] = parse_size(ini_get('post_max_size'));

      $response['upload_max_filesize'] = parse_size(ini_get('upload_max_filesize'));

      $response['max_file_uploads'] = parse_size(ini_get('max_file_uploads'));
    }

    /**
     * deletes a given form field id
     * @param integer $formFieldId      formFieldId of the formfield that has to be deleted
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteFormFieldData($type,$id)
    {
        switch ($type) {
          case 'form-field':
            // only custom field can be deleted
            $deletable = FormField::where('default', 0)->find($id);
            break;

          case 'form-option':
            $deletable = FormFieldOption::find($id);
            break;

          case 'form-label':
            $deletable = FormFieldLabel::find($id);
            break;

          default:
            return errorResponse('Wrong parameter passed');
        }

        if(!$deletable){
          return errorResponse(Lang::get('lang.record_not_found'));
        }

        if(!$this->isAllowedToBeDeleted($type, $deletable)){
          return errorResponse(Lang::get('lang.cannot_delete_the_only_child', ['type' => $type]));
        }

        $deletable->delete();

        return successResponse(Lang::get('lang.successfully_deleted'));
    }

    /**
     * Checks if a field can be deleted or not
     * @param  string  $type 'form-option', 'form-label'
     * @param  FormFieldOption/  $type 'form-option', 'form-label'
     * @return boolean
     */
    public function isAllowedToBeDeleted(string $type, $deletable) : bool
    {
      switch ($type) {
        case 'form-option':
          return FormFieldOption::where('form_field_id', $deletable->form_field_id)->count() > 1;

        case 'form-label':
          return FormFieldLabel::where('labelable_id', $deletable->labelable_id)
            ->where('labelable_type', $deletable->labelable_type)
            ->where('meant_for', $deletable->meant_for)
            ->count() > 1;
      }

      return true;
    }

    /**
     * @param FormFieldRequest $request      request with paramters as 'category'(form category for. eg ticket, user, organisation)
     *                              and 'form_fields'(form-field is an array which has all the data regarding form fields)
     * @return Response             success if successfully updated else failure
     */
    public function postFormFieldsByCategory(FormFieldRequest $request){
        try{
            $categoryName = $request->input('category');

            $category = FormCategory::where('category',$categoryName)->first();

            if(!$category){
                return errorResponse(Lang::get('lang.category_not_found'));
            }

            $formFields = $request->input('form-fields');

            foreach($formFields as $formField){

                // logic to handle form group too
                if($formField['type'] == 'group'){

                  $this->linkFormGroup($category, $formField);

                } else {

                  $parentFormField = $category->formFields()->updateOrCreate(['id'=>$formField['id']],$formField);
                  // handle department and helptopic seperately
                  // create different form field entries for them and store their
                  // ids into department/helptopic table
                  // wherever val
                  if($formField['title'] == 'Department' || $formField['title'] == 'Help Topic'){
                    // passing 3rd argument as false to make it avoid handling options and child field
                    $this->handleLabelPossibleCases($formField, $parentFormField, false);
                    $this->handleDefaultFieldChildForm($formField);
                    continue;
                  }
                  $this->handleFormField($formField, $parentFormField);
                }
            }

            return successResponse(Lang::get('lang.success_update'));
        }
        catch(Exception $e){
            return exceptionResponse($e);
        }
    }

    /**
     * Links existing form group to the passed parent
     * @param  FormCategory|HelpTopic|Department|FormOption $parent
     * @param  array $group associative array with `id` and `sort_order` as key
     * @return void
     */
    private function linkFormGroup($parent, array $group)
    {
      $parent->formGroups()->syncWithoutDetaching([$group['id'] => ['sort_order'=> $group['sort_order']]]);
    }

    /**
     * gets form for category type where category is passed as parameter
     * @param $groupId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGroupFormFields($groupId)
    {
        $baseQuery = FormGroup::with('helpTopics:help_topic.id,topic as name','departments:department.id,name')
          ->whereId($groupId);

        $formGroup = (new FormField)->getFormQueryByParentQuery($baseQuery)->first();

        if(!$formGroup){
            return errorResponse(Lang::get('lang.form_group_not_found'));
        }

        return successResponse('', $formGroup);
    }

    /**
     * @param Request $request      request with paramters as 'name'(form-group name for. eg ticket, user, organisation)
     *                              and 'form_fields'(form-field is an array which has all the data regarding form fields)
     * @return Response             success if successfully updated else failure
     */
    public function postGroupFormFields(FormGroupRequest $request)
    {
        try{
            $formGroup = new FormGroup();
            $formGroupArray = $request->toArray();
            $formGroupArray['group_type'] = 'ticket';
            $formGroup = $this->postFormGroup($formGroupArray, $formGroup);
            $helpTopicIds = $request->input('help_topic_ids') ? : [];

            $departmentIds = $request->input('department_ids') ? : [];

            $formGroup->helpTopics()->sync($helpTopicIds);
            $formGroup->departments()->sync($departmentIds);

            return successResponse(Lang::get('lang.success_update'));
        }
        catch(Exception $e){
            return exceptionResponse($e);
        }
    }

    /**
     * Gets list of form groups
     * @return Response
     */
    public function getFormGroupList(DataTableRequest $request)
    {
      $searchQuery = $request->input('search-query') ? : '';
      $limit = $request->input('limit') ? : 10;
      $sortOrder = $request->input('sort-order') ? : 'desc';
      $sortField = $request->input('sort-field') ? : 'updated_at';

      $groupList = FormGroup::where(function($subQueryOne) use ($searchQuery) {
        $subQueryOne->where('name', 'LIKE', "%$searchQuery%")
            ->orWhere('group_type', 'LIKE', "%$searchQuery%");
        });

      $groupList = $groupList->orderBy($sortField, $sortOrder)->paginate($limit);

      return successResponse('', $groupList);
    }

    /**
     * Deletes form group by its Id
     * @param int $groupId  Id of the group which has to be deleted
     * @return Response
     */
    public function deleteFormGroup($groupId)
    {
      $formGroup = FormGroup::find($groupId);

      if(!$formGroup){
        return errorResponse(Lang::get('lang.form_group_not_found'));
      }

      $formGroup->delete();

      return successResponse(Lang::get('lang.successfully_deleted'));
    }

    /**
     * Unlinks form group from a parent form
     * @param Request $request
     * @return Response
     */
    public function unlinkFormGroup($referenceType, $referenceId)
    {
      try{
        // referenceType will be name of the pivot table from which entry has to be deleted
        // REASON: it is tricky to get the parent of the form group which is going to
        // get unlinked, so the easiest way is to ask for table name from which it has to be deleted
        $valueReferenceTypes = ['form_category_form_group','form_field_option_form_group',
          'help_topic_form_group', 'department_form_group'];

        if(!in_array($referenceType, $valueReferenceTypes)){
          // if someone is trying to delete any other table than above mentioned,
          throw new Exception('Invalid reference_type');
        }

        DB::table($referenceType)->whereId($referenceId)->delete();

        return successResponse(Lang::get('lang.successfully_deleted'));
      } catch (Exception $e){
        return errorResponse($e->getMessage());
      }
    }

    /**
     * Stores department form into form_field table and map that to department model
     * @param  array $formField
     * @return
     */
    private function handleDefaultFieldChildForm($formField)
    {
      //only thing that is cared about is options
      $title = $formField['title'];
      foreach ($formField['options'] as $option) {
        // value is department_id or helptopic id
        $parentId = $option['value'];
        $parentCategory = $this->getParentModelForNestedDefaultFields($title, $parentId);

        if($parentCategory){
          foreach ($option['nodes'] as $node) {
            if($node['type'] == 'group'){
              $this->linkFormGroup($parentCategory, $node);
            } else {
              // create a form field, make that as parent and call `handleFormField`
              $parentFormField = $parentCategory->nodes()->updateOrCreate(['id'=>$node['id']],$node);
              $this->handleFormField($node, $parentFormField);
            }
          }
        }
      }
    }

    /**
     * Gives parent model for handling further nesting
     * @param  string $title  title of the form field for which model is required
     * @param  int $parentId  Id of the parent which is required to be queried from parent table
     * @return object|null  model intance of parent or null
     */
    private function getParentModelForNestedDefaultFields(string $title, $parentId)
    {
        switch ($title) {
          case 'Department':
            return Department::find($parentId);

          case 'Help Topic':
            return HelpTopic::find($parentId);

          default:
            return;
        }
    }

    /**
     * @param object $formField  formData object
     * @param object $parentFormField    parent object, could be a formData object or formDataOption object
     * @return void
     */
    private function handleFormField($formField, $parentFormField, $recursion = false)
    {
        if($recursion){
          if($formField['type'] == 'group'){
            $this->linkFormGroup($parentFormField, $formField);
          } else {
            $parentFormField = $parentFormField->nodes()->updateOrCreate(['id'=>$formField['id']],$formField);
          }
        }

        $this->handleLabelPossibleCases($formField, $parentFormField);
    }


    /**
     * @param array $labels      array of label that should be passed
     * @param object $parent    the parent object that is supposed to write to
     *                          labels(it can be formField objet or formFieldOption object)
     * @param string $meantFor      can be 'agent','user' or 'option'
     * @return void
     */
    private function handleLabels($labels, $parent, $meantFor)
    {
        foreach($labels as $label){
            $label['meant_for'] = $meantFor;
            $parent->labels()->updateOrCreate(['id'=>$label['id']],$label);
        }
    }

    /**
     * @param array $options    array of options from form-field array
     * @param object $parent    parent query which is the reference to the formField under which
     *                          options are created or updated
     */
    private function handleOptions($options, $parent){
        foreach($options as $option){
            $optionObject = $parent->options()->updateOrCreate(['id'=>$option['id']], $option);
            $this->handleLabelPossibleCases($option, $optionObject);
        }
    }

    /**
     * handles saving of child fields
     * @param  array $nodes
     * @param  FormOption $parent
     * @return null
     */
    private function handleNodes($nodes, $parent){
        foreach($nodes as $node){
            $this->handleFormField($node, $parent, true);
        }
    }

    /**
     * Handles cases where labels/options needs to be saved based on their parent
     * @param  array  $dataArray
     * @param  FormField|FormFieldOption|Helptopic|Department  $parent            can be an instance of FormField, FormFieldOption, Helptopic or Department
     * @param  boolean $shouldHandleOptions if passed option needs to be saved (in case of helptopic and department, it doesn't)
     * @return null
     */
    private function handleLabelPossibleCases($dataArray, $parent, $shouldHandleOptions = true)
    {
        if(array_key_exists('labels_for_form_field', $dataArray)){
            $this->handleLabels($dataArray['labels_for_form_field'], $parent, 'form_field');
        }

        if(array_key_exists('labels_for_validation', $dataArray)){
            $this->handleLabels($dataArray['labels_for_validation'], $parent, 'validation');
        }

        if(!$shouldHandleOptions){
          return;
        }

        // option handling happens here, including its child field
        // =====================================================================

        if(array_key_exists('labels', $dataArray)){
            $this->handleLabels($dataArray['labels'], $parent, 'option');
        }

        if(array_key_exists('options', $dataArray)){
            $this->handleOptions($dataArray['options'], $parent);
        }

        if(array_key_exists('nodes', $dataArray)){
            $this->handleNodes($dataArray['nodes'], $parent);
        }
        // =====================================================================
    }

    /**
     * gets all ticket related custom fields including child fields of helptopic and department along
     * with their current label
     * @return Response
     */
    public function getTicketCustomFieldsFlattened()
    {
      $customFields = FormField::getTicketCustomFieldList();
      return successResponse('', $customFields);
    }

    public function getFormBuilderFieldMenu($category)
    {
        $formMenu = [
            (object)["type"=>"text", "title"=>"Text Field", "icon_class"=>"fa-text-width"],
            (object)["type"=>"textarea", "title"=>"Text Area", "icon_class"=>"fa-paragraph"],
            (object)["type"=>"number", "title"=>"Number", "icon_class"=>"fa-sort-numeric-asc"],
            (object)["type"=>"email", "title"=>"Email", "icon_class"=>"fa fa-envelope-o"],
            (object)["type"=>"select", "title"=>"Select", "icon_class"=>"fa-list-alt", "options"=>[["id"=> null, "labels" => [["id"=> null, "language"=>"en", "label"=> "value"]], "nodes"=> []]]],
            (object)["type"=>"radio", "title"=>"Radio", "icon_class"=>"fa-dot-circle-o", "options"=>[["id"=> null, "labels" => [["id"=> null, "language"=>"en", "label"=> "value"]], "nodes"=> []]]],
            (object)["type"=>"checkbox", "title"=>"Checkbox", "icon_class"=>"fa-check-square", "options"=>[["id"=> null, "labels" => [["id"=> null, "language"=>"en", "label"=> "value"]], "nodes"=> []]]],
            (object)["type"=>"date", "title"=>"Date", "icon_class"=>"fa-calendar"],
        ];

        if($category == 'ticket') {
            $formMenu[] = (object)["type"=>"select", "title"=>"Api", "icon_class"=>"fa-code-fork"];
            $formMenu[] = (object)["type"=>"file", "title"=>"Attachments", "icon_class"=>"fa-upload"];
        }

        $formMenuObjects = [];

        $isUserConfig = in_array($category, ["user", "organisation", "group", "ticket"]) ?: false;

        $defaultObject = (object) [
            "required_for_agent"=>true, "display_for_agent"=>true, "display_for_user"=>true, "required_for_user"=> true,
            "is_deletable"=>true, "is_customizable"=> true, "is_agent_config"=>true, "is_user_config"=> $isUserConfig,
            "labels_for_validation"=>[["id"=> null, "language"=>"en", "label"=> ""]], "pattern"=> "",
        ];

        foreach ($formMenu as $index => $item) {
            $formMenuObject = clone $defaultObject;
            $formMenuObject->id = "form_menu_".$index;
            $formMenuObject->type = $item->type;
            $formMenuObject->title = $formMenuObject->label = $item->title;
            $formMenuObject->icon_class = $item->icon_class;
            $formMenuObject->labels_for_form_field = [(object)["id"=> null, "language"=>"en", "label"=> $item->title, "description"=> ""]];
            $formMenuObject->options = isset($item->options) ? $item->options : [];
            $formMenuObjects[] = $formMenuObject;
        }

        return successResponse("", $formMenuObjects);
    }

    /**
     * @internal moved from web.php
     * @since v3.4.0
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getFormGroupIndex()
    {
        return view('themes.default1.admin.helpdesk.manage.formgroup.form-groups');
    }

    /**
     * @internal moved from web.php
     * @since v3.4.0
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getFormGroupCreate()
    {
        return view('themes.default1.admin.helpdesk.manage.formgroup.create-edit-form-group');
    }

    /**
     * @internal moved from web.php
     * @since v3.4.0
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getFormGroupEdit($groupId)
    {
        return view('themes.default1.admin.helpdesk.manage.formgroup.create-edit-form-group');
    }

//    ==================================================== OLD CODE AHEAD ===============================================

    /*
     * @depreciated
     * get the form in json format
     * @param boolean $array
     * @return array|json
     */
    public function getTicketFormJson($type = "ticket", $array = false)
    {
        $form   = new Form();
        $custom = $form->where('form', $type)->select('json')->first();
        $json   = "";
        if ($custom) {
            if(stripos($custom->json, "'unique':'requester'") || strpos($custom->json, "'unique':'first_name'") ||
                strpos($custom->json, "'unique':'name'")){
                $json = str_replace("'", '"', $custom->json);
            } else {
                $json = str_replace("'", '\'', $custom->json);
            }
        }
        $json = $this->addAdditionalFields($json, Input::all());
        $jsonEvent = checkArray('0', event(new \App\Events\ClientTicketForm($json, $type)));
        if ($jsonEvent) {
            $json = json_encode($jsonEvent);
        }
        return ($array) ? $json : '[' . $json . ']';
    }

    /**
     * Function to add custom fields of department and helptopic in ticket form json
     * @param   String  json encoded string of forms fields
     * @param   Array   array containing input data from ajax call
     * @return  String  json encoded string after adding additional fields
     */
    private function addAdditionalFields(string $json, array $array=[])
    {
        $decodedJson = json_decode($json);
        foreach ($array as $table => $id) {
            if($table === 'department' || $table === 'help_topic') {
                $additionalJson = \DB::table($table)->where('id', $id)->value('nodes');
                if(json_decode($additionalJson)) {
                    $decodedJson = array_merge($decodedJson, json_decode($additionalJson));
                }
            }
        }
        return json_encode($decodedJson);
    }

    /**
     * get all dependencies
     * @param Request $request
     * @return mixed
     */
    public function dependancy(Request $request)
    {

        try{
            $linked_topic = ($request->has('linkedtopic')) ? $request->get('linkedtopic')
                        : '';
            $dependency   = $request->input('dependency', 'priority');

            return $this->getDependancy($dependency, $linked_topic, $request);
        }  catch (\Exception $e){
            return response()->json(['error'=>$e->getMessage()]);
        }
    }
    /**
     * get the model for every dependency
     * @param string $dependency
     * @return mixed
     */
    public function getDependancyApproval($dependency, $term)
    {

        switch ($dependency) {
            case "priority":
                $auth_user = \Auth::user();
                if ($auth_user && $auth_user->role != 'user') {
                    return DB::table('ticket_priority')->where('priority', 'LIKE', '%' . $term . '%')->where('status', '=', 1)->select('priority_id as id', 'priority as optionvalue')->get()->toJson();
                }
                else {
                    return DB::table('ticket_priority')->where('priority', 'LIKE', '%' . $term . '%')->where('status', '=', 1)->where('ispublic', 1)->select('priority_id as id', 'priority as optionvalue')->get()->toJson();
                }
            case "faveo_department":
                return DB::table('department')->where('name', 'LIKE', '%' . $term . '%')->select('id', 'name as optionvalue')->get()->toJson();
            case "type":
                $auth_user = \Auth::user();
                if ($auth_user && $auth_user->role != 'user') {

                    return DB::table('ticket_type')->where('name', 'LIKE', '%' . $term . '%')->where('status', '=', 1)->select('id', 'name as optionvalue')->get()->toJson();
                }
                else {
                    return DB::table('ticket_type')->where('name', 'LIKE', '%' . $term . '%')->where('status', '=', 1)->where('ispublic', '=', 1)->select('id', 'name as optionvalue')->get()->toJson();
                }
            case "assigned_to":
                return DB::table('users')->where('role', '!=', 'user')->where('is_delete', '!=', 1)->select('id', 'user_name as optionvalue')->get()->toJson();
        }
    }
    /**
     * get the model for every dependency
     * @param string $dependency
     * @return mixed
     */
    public function getDependancy($dependency, $linked_topic = '', $request = '')
    {
     switch ($dependency) {
            case "priority":
                $auth_user = \Auth::user();
                if ($auth_user && $auth_user->role != 'user') {
                    return DB::table('ticket_priority')->where('status', '=', 1)->select('priority_id as id', 'priority as optionvalue')->get()->toJson();
                }
                else {
                    return DB::table('ticket_priority')->where('status', '=', 1)->where('ispublic', 1)->select('priority_id as id', 'priority as optionvalue')->get()->toJson();
                }
            case "department":
                $departments = DB::table('department')->select('id', 'name as optionvalue', 'nodes');
                if ($linked_topic != '') {
                    $help_topic = DB::table('help_topic')->select('linked_departments', 'department')->where('topic', '=', $linked_topic)->first();
                    if ($help_topic->linked_departments == null || $help_topic->linked_departments
                            == '') {
                        if(Auth::user() && Auth::user()->role=='admin' || Auth::user() && Auth::user()->role=='agent'){
                            $departments = $departments->where('id', '=', $help_topic->department)->orderBy('optionvalue')->get();
                        }
                        else{
                            $departments = $departments->where('type','!=',0)->where('id', '=', $help_topic->department)->orderBy('optionvalue')->get();
                        }
                    }
                    else {
                        $dept_ids    = explode(",", $help_topic->linked_departments);
                         if(Auth::user() && Auth::user()->role=='admin' || Auth::user() && Auth::user()->role=='agent'){
                        $departments = $departments->whereIn('id', $dept_ids)->orderBy('optionvalue')->get();
                          }
                          else{
                             $departments = $departments->where('type','!=',0)->whereIn('id', $dept_ids)->orderBy('optionvalue')->get();
                          }
                    }

                }
                else {

                         if(Auth::user() && Auth::user()->role=='admin' || Auth::user() && Auth::user()->role=='agent'){

                        $departments = $departments->orderBy('optionvalue')->get();
                    }
                    else{
                        $departments = $departments->where('type',1)
                        ->orderBy('optionvalue')->get();
                    }
                }
                foreach ($departments as $value) {
                    $value->optionvalue = [['language' => 'en', 'option' => $value->optionvalue, 'flag' => asset("lb-faveo/flags/en.png")]];
                    if ($value->nodes != null) {
                        $value->nodes = json_decode(str_replace("'", '"', $value->nodes));
                    }
                    else {
                        $value->nodes = [];
                    }
                }

                return response()->json($departments);
            case "faveo_department":
                return DB::table('department')->select('id', 'name as optionvalue')->get()->toJson();
            case "type":
                $auth_user = \Auth::user();
                if ($auth_user && $auth_user->role != 'user') {

                    return DB::table('ticket_type')->where('status', '=', 1)->select('id', 'name as optionvalue')->get()->toJson();
                }
                else {
                    return DB::table('ticket_type')->where('status', '=', 1)->where('ispublic', '=', 1)->select('id', 'name as optionvalue')->get()->toJson();
                }
            case "assigned_to":
                return DB::table('users')->where('role', '!=', 'user')
                                ->where([
                                    ['active', '=', 1],
                                    ['is_delete', '!=', 1],
                                ])->select('id', 'user_name as optionvalue')->orderBy('optionvalue')->get()->toJson();
                return DB::table('users')->where('role', '!=', 'user')->where('active', '=', 1)->where('is_delete', '!=', 1)->select('id', 'user_name as optionvalue')->get()->toJson();
            case "company":
                return DB::table('organization')->where('name','LIKE','%'.$request->term.'%')->select('id', 'name as optionvalue')->get()->toJson();
            case "status":
                if (Auth::user() && Auth::user()->role != 'user') {
                    $obj = new \App\Policies\TicketPolicy();
                    $status = Ticket_Status::select('id', 'name as optionvalue');

                    // if (!$obj->viewUnapprovedTickets()) {
                    // AVINASH : permission has been commented as we are not sure whether unapproved
                    // should be visible or not
                        $status = $status->where('purpose_of_status', '!=', 7);
                    // }

                    return $status->get()->toJson();
                }
                return Ticket_Status::where('visibility_for_client', 1)->where('allow_client', 1)->select('id', 'name as optionvalue')->whereIn('purpose_of_status', [1,2])->get()->toJson();

            //all status shows all the statuses irrespective of its purpose of status. It will be used in workflow of list all status including unapproved. Only admin will be able to see it
            case "all-status":
                if (Auth::user() && Auth::user()->role == 'admin') {
                    return Ticket_Status::select('id', 'name as optionvalue')->get()->toJson();
                }
                return [];

            case "help_topic":
                $auth_user = \Auth::user();
                if ($auth_user && $auth_user->role != 'user') {
                    $help_topics = DB::table('help_topic')->where('status', '=', 1)->select('id', 'topic as optionvalue', 'nodes')->get()->toJson();
                }
                else {
                    $help_topics = DB::table('help_topic')->where('status', '=', 1)->where('type', '=', 1)->select('id', 'topic as optionvalue', 'nodes')->get()->toJson();
                }
                return $help_topics;
            // case "status":
            //     return DB::table('ticket_status')->select('id', 'name as optionvalue')->get()->toJson();
            case "helptopic":
                $help_topics = DB::table('help_topic');
                if ($linked_topic != '') {
                    $dept        = DB::table('department')->where('name', '=', $linked_topic)->pluck('id')->toArray()[0];
                    $help_topics = $help_topics->whereRaw("find_in_set('" . $dept . "', linked_departments)")->orWhere('department', '=', $dept);
                    if ($help_topics->count() == 0) {
                        $help_topics = DB::table('help_topic');
                    }
                }
                $auth_user = \Auth::user();
                if ($auth_user && $auth_user->role != 'user') {
                    $help_topics = $help_topics->where('status', '=', 1)->select('id', 'topic as optionvalue', 'nodes')->orderBy('optionvalue')->get();
                }
                else {
                    $help_topics = $help_topics->where('status', '=', 1)->where('type', '=', 1)->select('id', 'topic as optionvalue', 'nodes')->orderBy('optionvalue')->get();
                }
                foreach ($help_topics as $value) {
                    $value->optionvalue = [['language' => 'en', 'option' => $value->optionvalue, 'flag' => asset("lb-faveo/flags/en.png")]];
                    if ($value->nodes != null) {
                        $value->nodes = json_decode(str_replace("'", '"', $value->nodes));
                    }
                    else {
                        $value->nodes = [];
                    }
                }
                return response()->json($help_topics);
            case "source":
                return DB::table('ticket_source')->select('id', 'value as optionvalue')->get()->toJson();
            case "approval":
                return $this->getDependancyApproval($request->get('req'), $request->get('term'));
            case "location":
                // return DB::table('location')->select('title', 'title as optionvalue')->get()->toJson();
                $auth_user = \Auth::user();
                if ($auth_user && $auth_user->role != 'user') {
                    if (\Auth::user()->role === 'admin') {
                        return DB::table('location')->orderBy('title', 'asc')->select('title as id', 'title as optionvalue')->get()->toJson();
                    }
                    else {
                        $agent_location = User::where('id', '=', \Auth::user()->id)->select('location')->first();

                        if ($agent_location->location) {
                            return DB::table('location')->orderBy('title', 'asc')->where('id', '=', $agent_location->location)->select('title as id', 'title as optionvalue')->get()->toJson();
                        }
                        else {
                            return DB::table('location')->orderBy('title', 'asc')->select('title as id', 'title as optionvalue')->get()->toJson();
                        }
                    }
                }
                else {
                    return DB::table('location')->orderBy('title', 'asc')->select('title as id', 'title as optionvalue')->get()->toJson();
                }

            case "org_dept":
            // dd($request->input('company'));
                // $test = \App\Model\helpdesk\Agent_panel\OrganizationDepartment::whereIn('org_id', $company)->select('id', 'org_deptname as optionvalue')->get()->toJson();
                if ($request->input('company')) {

                    $company = $request->input('company');

                    $baseQuery = DB::table('organization');

                    if($company != 'all') {
                      $baseQuery = $baseQuery->whereIn('id', $company);
                    }

                    $company_name = $baseQuery->pluck('name')->toArray();

                    $formatted_orgs = DB::table('organization_dept')
                            ->join('organization', function($q) {
                                $q->on('organization.id', '=', 'organization_dept.org_id');
                            })
                            ->whereIn('organization.name', $company_name)
                            ->where('organization_dept.org_deptname', 'LIKE', '%' . $request->term . '%')
                            ->select("organization_dept.id", "organization_dept.org_id", "organization_dept.org_deptname", "organization.name")
                            ->get();

                    foreach ($formatted_orgs as $key => $value) {
                        $display = $value->org_deptname . "(" . $value->name . ")";
                        $formatted_orgs_dept[] = ['id' => $value->id, 'optionvalue' => $display];
                    }
                    return \Response::json($formatted_orgs_dept);
                } else {
                    $formatted_orgs_dept[] = DB::table('organization_dept')->select("organization_dept.id", "organization_dept.org_id", "organization_dept.org_deptname")
                            ->get();
                    return \Response::json($formatted_orgs_dept);
                }



                 case "helpdesk-name":
                return DB::table('satellite_helpdesk')->where('status', '=', 1)->select('id', 'name as optionvalue')->get()->toJson();
                default:
                $data = \Event::dispatch(new \App\Events\TicketFormDependency($dependency))[0];
                return collect($data)->toJson();

        }
    }

     /**(Dependency places [create ticket (requester),deactivate agent(assign open ticket) ,team create(team lead)])
     * get requester in form
     * @param Request $request
     * @return json
     */
    public function requester(Request $request)
    {
        $term = $request->input('term');

        // !(if user is logged in and its role is agent/admin) equals if user is not logged in or logged in as user
        // keeping it in OR logic will break if user is not logged in, because in that case Auth::user() will null and it
        // will not have role property
        if(!(Auth::user() && Auth::user()->role != 'user')){
            $users = User::where('user_name', $term)
                ->orWhere(function($q) use($term){
                    $q->where('email', $term)->where('email', '!=', null);
                })->where('is_delete', '!=', 1)
                ->select('id', 'user_name', 'first_name', 'last_name','role')
                ->first();

            return $users;
        }

        elseif($request->has('team') && Auth::check() && Auth::user()->role != 'user'){

            $id = $request->team;
            $team = new Teams();
            $teams = $team->whereId($id)->first();


            $assign_team_agent = new Assign_team_agent();
            $agent_team = $assign_team_agent->where('team_id', $id)->get();

            $agent_id = $agent_team->pluck('agent_id', 'agent_id');

            $query = User::whereIn('id', $agent_id)->where('active', '=', 1)->where('is_delete', '!=', 1)
                            ->when($term, function($q) use($term) {
                                $q->where(function($query) use($term) {
                                    $query->select('id', 'first_name', 'last_name', 'email', 'user_name', 'profile_pic')
                                    ->where('first_name', 'LIKE', '%' . $term . '%')
                                    ->orwhere('last_name', 'LIKE', '%' . $term . '%')
                                    ->orwhere('user_name', 'LIKE', '%' . $term . '%')
                                    ->orwhere('email', 'LIKE', '%' . $term . '%');
                                });
                            })->get();
            return $query;
        }
        else{
            $method   = $request->input('type', 'agent','user','admin','agent-only');
            $user_ids = explode(',', $request->input('user_id', ''));
            if (count($user_ids) == 1 && $user_ids[0] == '') {
                $user_ids = '';
            }
            //$method = 'requester';
            //$user_ids = [];
            $user     = new \App\User();
            $term     = $request->input('term');
            $query = $user
                        ->leftJoin('user_assign_organization', 'users.id', '=', 'user_assign_organization.user_id')
                        ->leftJoin('organization', 'organization.id', '=', 'user_assign_organization.org_id')
                        ->when($term, function($q) use($term) {
                           $q->where(function($query) use($term){
                               $query
                               ->where('users.first_name', 'LIKE', '%' . $term .'%')
                               ->orWhere('users.last_name','LIKE','%'. $term .'%')
                               ->orWhere('user_name', 'LIKE', '%' . $term . '%')
                               ->orWhere('email', 'LIKE', '%' . $term . '%')
                               ->orWhere('organization.address', 'LIKE', '%' . $term . '%')
                                ->orWhere('organization.name', 'LIKE', '%' . $term . '%');
                           });
                       })
                        ->when($user_ids, function($q)use($user_ids) {
                            $q->whereIn('users.id', $user_ids);
                        })

                        ->where('is_delete', '!=', 1)
                        ->where('active', '=', 1)
                        ->when($term, function($q) use($term) {
                            $q->where(function($query) use($term){
                                $query->where('users.first_name', 'LIKE', '%' . $term . '%')
                                ->orWhere('users.last_name', 'LIKE', '%' . $term . '%')
                                ->orWhere('user_name', 'LIKE', '%' . $term . '%')
                                ->orWhere('email', 'LIKE', '%' . $term . '%')
                                ->orWhere('organization.address', 'LIKE', '%' . $term . '%')
                                ->orWhere('organization.name', 'LIKE', '%' . $term . '%');
                            });
                        })

                        ->with(['org' => function($org) {
                                $org->select('id', 'org_id', 'user_id', 'role', 'org_department');
                            }, 'org.organisation' => function($company) {
                                $company->select('id', 'name as company' ,'address as address ');
                            },
                            'org.orgDepartment' => function($q) {
                               $q->select('id', 'org_deptname', 'business_hours_id');
                                }
                         ]);

    //Returns all Admins n Agents only
            if ($method == 'agent') {
                $users = $query
                        ->where('users.role', '!=', 'user')
                        ->select('users.id','users.email as name','users.first_name','users.last_name','users.profile_pic','users.email','users.role')
                        ->get();

                // dd($users);
                return $users;
            }
    //Returns all Admins n Agents n Users
            if ($method == 'requester') {
                $users = $query->groupBy('users.id')
                        ->select('users.id', 'users.email as name', 'users.first_name', 'users.last_name','users.email','users.profile_pic','users.role')
                        ->get();

               // dd($users);
                return $users;
            }
    //Returns all Users only
            if ($method == 'user') {
                $users = $query->groupBy('users.id')
                        ->where('users.role', '=', 'user')
                        ->select('users.id','users.user_name','users.first_name','users.last_name','users.profile_pic','users.email','users.role')
                        ->get();

               // dd($users);
                return $users;
            }
    //Returns all Admins only
            if ($method == 'admin') {
                $users = $query->groupBy('users.id')
                        ->where('users.role', '=', 'admin')
                        ->select('users.id','users.user_name','users.first_name','users.last_name','users.profile_pic','users.email','users.role')
                        ->get();

               // dd($users);
                return $users;
            }
    //Returns all Agents only
             if ($method == 'agent-only') {
                $users = $query->groupBy('users.id')
                        ->where('users.role', '=', 'agent')
                        ->select('users.id','users.user_name','users.first_name','users.last_name','users.profile_pic','users.email','users.role')
                        ->get();

               // dd($users);
                return $users;
            }

            //approver
            if ($method == 'approver') {
                $users = $query
                        ->where('users.role', '!=', 'user')
                        ->select('users.id','users.email as name','users.first_name','users.last_name','users.profile_pic','users.email','users.role')
                        ->get()->toArray();

            $default = array('department manager', 'team lead','Department manager', 'Team lead');
            $term     = $request->input('term');

            $defaultarray = [];
            foreach ($default as $value) {
                if (preg_match('/^' . $term . '/', $value))
                    array_push($defaultarray, $value);
            }
            $url= " ";
            if((implode(',',$defaultarray)=="team lead")|| (implode(',',$defaultarray)=="Team lead")){
                $url= url('/').'/lb-faveo/media/images/team.jpg';
            }
            if((implode(',',$defaultarray)=="department manager") || (implode(',',$defaultarray)=="Department manager")){
                $url=  url('/').'/lb-faveo/media/images/department.jpg';
            }
            $extraArray[]=['id'=>str_slug(implode(',',$defaultarray), '_'),'name'=>'','first_name'=>implode(',',$defaultarray),'last_name'=>'','profile_pic'=>$url,'email'=>'','role'=>''];

            $extraArray= ($url == " ")? [] :$extraArray;
           return array_merge($users, $extraArray);
            }
        }
   }

    /**
     * parse the form from json to array
     * @return array
     */
    public function ticketFormBuilder($form)
    {
        $json            = $this->getTicketFormJson($form, true);
        $array           = json_decode($json, true);
        $array_title_key = collect($array)->keyBy('unique')->toArray();
        $result          = $this->parseTicketFormArray($array_title_key);
        return $result;
    }

    /**
     * get values in array after
     * @param array $array
     * @return array
     */
    public function parseTicketFormArray($array)
    {
        $result = [];
        foreach ($array as $key => $value) {
            $result[$key] = $this->parseParent($value, $key);
        }
        return $result;
    }
    /**
     * parse the parent field is it is a nested field
     * @param array $value
     * @param string $key
     * @param string $parent
     * @param string $child
     * @param string $option_value
     * @return array
     */
    public function parseParent($value, $key = "", $parent = "", $child = "", $option_value
    = "")
    {
        //dd($value);
        $agent       = checkArray('agentRequiredFormSubmit', $value);
        $client      = checkArray('customerRequiredFormSubmit', $value);
        $label       = checkArray('agentlabel', $value);
        $agent_label = "";
        if ($label && is_array($label)) {
            $agent_label = head($label)['label'];
        }
        if (is_string($label)) {
            $agent_label = $label;
        }
        $result['agent_label'] = $agent_label;
        $result['agent']       = $agent;
        $result['client']      = $client;
        $result['parent']      = $parent;
        $result['label']       = $child;
        $result['option']      = $option_value;
        $options               = checkArray('options', $value);
        if ($options && count($options) > 0) {
            $array = $this->parseOptions($options, $key);
            if (is_array($array)) {
                $result = array_merge($result, $array);
            }
        }
        return $result;
    }
    /**
     *
     * @param array $options
     * @param string $parent
     * @return type
     */
    public function parseOptions($options, $parent = "")
    {
        $result = [];
        foreach ($options as $option) {
            $nodes        = checkArray('nodes', $option);
            $option_value = checkArray('optionvalue', $option);
            if ($nodes && checkArray('0', $nodes)) {
                foreach ($nodes as $node) {
                    $result['child'][$node['unique']] = $this->parseParent($node, $node['agentlabel'], $parent, $node['agentlabel'], $option_value);
                }
            }
        }
        return $result;
    }
    public function saveRequired($form = 'ticket')
    {
        \App\Model\Custom\Required::
                where('form', $form)
                ->delete();
        $array = $this->ticketFormBuilder($form);
        foreach ($array as $parent => $values) {
            $this->saveOptions($parent, $values, $form);
        }
    }
    public function saveOptions($key, $values, $form, $option = 'required', $parent_id
    = "")
    {
        $required     = [];
        $child        = checkArray('child', $values);
        $option_value = checkArray('option', $values);
        if (!is_string($option_value)) {
            $option_value = head($option_value)['option'];
        }
        $required['option'] = $option_value;
        if (checkArray('agent', $values)) {
            $required['agent'] = $option;
        }
        if (checkArray('client', $values)) {
            $required['client'] = $option;
        }
        if ($parent_id) {
            $required['parent'] = $parent_id;
        }
        if ($required) {

            if (!is_string($key)) {
                $key = head($key)['label'];
            }
            $required['field'] = $key;
            $required['form']  = $form;
            $required['label'] = checkArray('agent_label', $values);
            $required_model    = \App\Model\Custom\Required::updateOrCreate($required);
            if ($child) {
                $parent_field_name = $key;
                $this->saveChild($child, $parent_field_name, $required_model, $form);
            }
        }
    }
    public function saveChild($child, $parent_field_name, $model, $form, $option_value
    = "")
    {
        foreach ($child as $key => $values) {
            $option = "required_if:$parent_field_name";
            $this->saveOptions($key, $values, $form, $option, $model->id);
        }
    }

    /** 
     * method to create or update form group 
     * @param  Array $formGroupArray
     * @param FormGroup $formGroup
     * @return FormGroup $formGroup
     */
    public function postFormGroup(array $formGroupArray, $formGroup)
    {
      $formGroupArray['id'] = array_key_exists('id', $formGroupArray) ? $formGroupArray['id'] : null;

      $formGroup = $formGroup->updateOrCreate(['id' => $formGroupArray['id']],$formGroupArray);

      foreach($formGroupArray['form-fields'] as $formField){
          $parentFormField = $formGroup->formFields()->updateOrCreate(['id'=>$formField['id']],$formField);

          $this->handleFormField($formField, $parentFormField);
      }

      return $formGroup;
    }
}
