<?php
namespace App\Model\helpdesk\Form;

use DB;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Observable;
use App\Model\helpdesk\Ticket\TicketAction;
use App\Model\helpdesk\Ticket\TicketRule;
use App\Model\helpdesk\TicketRecur\RecureContent as TicketRecur;
use App;
use Illuminate\Database\Eloquent\Collection;
use App\Model\helpdesk\Ticket\TicketFilterMeta;
use App\FaveoReport\Models\ReportColumn;
use App\Model\helpdesk\Form\FormGroup;
use Event;

class FormField extends Model
{
    use Observable;

    protected $table = 'form_fields';

    protected $hidden = ['category_id','category_type','option_id','created_at','updated_at', 'is_active'];

    protected $appends = ['label', 'description', 'form_identifier'];

    protected $fillable = [
        'category_id', // id for category for eg. user form, ticket form, organisation form OR department OR helptopic
        'category_type', // type for category
        'sort_order', //order in which fields should be displayed
        'title',
        'type',
        'required_for_agent',
        'required_for_user',
        'display_for_agent',
        'display_for_user',
        'default',
        'is_linked',
        'media_option',
        'api_info',
        'pattern',
        'option_id',//it will be null if it is not refering to formFieldOption model.. i.e while querying
        //for the first time(no recursion) only those records should be fetched whose option_id is null
        //it has many options but it can also belongs to an option for nested fields

        'is_edit_visible',
        'is_active',
        'is_locked',
        'is_agent_config',
        'is_user_config',
        'unique',
        'form_group_id',
        'is_deletable',
        'is_customizable',
        'is_observable',
        'is_filterable',
        'is_user_config'
    ];

    /**
     * This identifier will be used at frontend to know if it is a form_field, form field option, help topic option, department option or label
     * @return string
     */
    public function getFormIdentifierAttribute()
    {
        return "form_field_".$this->id;
    }

    public function options()
    {
        return $this->hasMany('App\Model\helpdesk\Form\FormFieldOption', 'form_field_id', 'id');
    }

    public function labelsForFormField()
    {
        return $this->labels()->where('meant_for', 'form_field');
    }

    public function labelsForValidation()
    {
        return $this->labels()->where('meant_for', 'validation');
    }

    /**
     * Morph to multiple models like category/department/helptopic
     */
    public function category()
    {
        return $this->morphTo();
    }

    /**
     * value in custom_form_value table
     */
    public function values(){
        return $this->hasMany('App\Model\helpdesk\Form\CustomFormValue', 'form_field_id');
    }

    /**
     * using polymorphic relation for binding
     */
    public function labels()
    {
        $lang = App::getLocale();

        // adding an orderBy, so that the label with current language can be at top
        return $this->morphMany('App\Model\helpdesk\Form\FormFieldLabel', 'labelable')
            ->select("*", DB::raw("(CASE when language='$lang' THEN 1 ELSE 0 END) as is_current_language"))
            ->orderBy("is_current_language", "desc")
            ->orderBy("id", "asc");
    }

    public function beforeDelete($model)
    {
        //deleting one by one will make sure that it fires delete event in the child model
        foreach ($this->options as $option) {
            $option->delete();
        }

        foreach ($model->labels as $label) {
            $label->delete();
        }
        Event::dispatch('delete-extra-entries',[$this->id]);
        //delete all custom form value also
        $model->values()->delete();

        // deleting the same in actions, rules and recur table
        TicketAction::where('field', "custom_$this->id")->delete();
        TicketRule::where('field', "custom_$this->id")->delete();
        TicketRecur::where('option', "custom_$this->id")->delete();

        // deleting associated filters
        TicketFilterMeta::where('key', "custom_$this->id")->delete();
        ReportColumn::where('key', "custom_$this->id")->delete();
    }

    /**
     * It builds a query based on how deep the nesting is. If it finds no nesting in next layer it returns the result
     * else it loops over itself again
     * It is a helper method for 'getFormFieldsByCategory' and should not be used by any other method
     * @param $query
     * @param bool $noFileMode if file is required or not
     * @return object   query for having n-layer of nested fields
     */
    public function queryBuilderForNestedFormFields($query, $noFileMode = false)
    {
        $query = $query->where('is_active', 1);
        if($query->with('options')->count()){
            return $query->orderBy('sort_order','ASC')->with([
                'options'=>function($q1){
                    $q1->orderBy('sort_order','ASC');
                },
                'options.labels',
                'options.nodes.labelsForFormField',
                'options.nodes.labelsForValidation',
                'options.nodes'=> function($q) use ($noFileMode){
                    if($noFileMode){
                        $q = $q->where('type', '!=', 'file');
                    }
                    $this->queryBuilderForNestedFormFields($q, $noFileMode);
                },
                'options.formGroups'
            ]);
        }
        return $query;
    }

    public function getRequiredForAgentAttribute($value)
    {
        return (bool)$value;
    }

    public function getRequiredForUserAttribute($value)
    {
        return (bool)$value;
    }

    public function getDisplayForAgentAttribute($value)
    {
        return (bool)$value;
    }

    public function getDisplayForUserAttribute($value)
    {
        return (bool)$value;
    }

    public function getLabelAttribute()
    {
        return $this->labelsForFormField()->value("label");
    }

    public function getDescriptionAttribute()
    {
        return $this->labelsForFormField()->value("description");
    }

    /**
     * gets all ticket related custom fields including child fields of helptopic and department along
     * with their current label without any meta data
     * NOTE: Attachment fields will not be returning, because it is not required in any of the cases
     * that we are using yet.
     * @return Collection
     */
    public static function getTicketCustomFieldList() : Collection
    {
        $customFields = FormField::where(function($q){
            $q->where('category_type','App\Model\helpdesk\Manage\Help_topic')
                ->orWhere('category_type', 'App\Model\helpdesk\Agent\Department')
                ->orWhere(function($q1){
                    $q1->where('category_type','App\Model\helpdesk\Form\FormCategory')
                        // category 1 means ticket
                        ->where('category_id', 1);
                });
        })->where('default', 0)
            ->where('is_active', 1)
            ->where('is_filterable', 1)
            ->select('id')->where('type', '!=', 'file')->get();

        //getting custom fields for groups also
        $customFields = $customFields->merge(self::getGroupFormFieldList());

        // get all options that belong to ticket, now those options form field ids should be taken,
        // now those form field can also have options, now those options should also be considered.
        // This should go ON and ON until none of the option is remaining
        self::appendRecursiveFormFields($customFields);

        // since all form fields which belongs to a can be linked to a ticket at any given time,
        // so we can consider it to be a normal ticket form field

        return $customFields;
    }

    /**
     * Gets user custom field list
     * @return Collection
     */
    public static function getUserCustomFieldList() : Collection
    {
        return FormCategory::where('category', 'user')->first()->formFields()
            ->where('default', 0)
            ->where('is_active', 1)
            ->where('is_filterable', 1)
            ->select('id')
            ->where('type', '!=', 'file')
            ->get();
    }

    /**
     * Gets all form fields which are associated with groups and not directly to ticket/user/organisation
     * @return Collection
     */
    private static function getGroupFormFieldList() : Collection
    {
        return FormField::whereHas('formGroup', function($subQuery) {
            $subQuery->whereGroupType('ticket');
          })->where('type', '!=', 'file')->get(['id']);
    }

    /**
     * Appends nested form field which doesn't belong to any category but options
     * @param  Collection &$formFields
     * @return null
     */
    protected static function appendRecursiveFormFields(
        Collection &$formFields, Collection $additionFormFields = null, bool $isIterating = false
    ){
        $fieldsToIterate = $isIterating ? $additionFormFields : $formFields;

        $formFieldIds = $fieldsToIterate->map(function($formField){
            return $formField->id;
        });

        // query into option table for formFields,
        $formFieldOption = FormFieldOption::whereIn('form_field_id', $formFieldIds)->pluck('id')->toArray();

        $additionFormFields = FormField::whereIn('option_id', $formFieldOption)->where('type', '!=', 'file')
            ->select('id')->get();

        // if no additional form field is found, abort the recursion
        if(!$additionFormFields->count()){
            return;
        }

        $formFields = $formFields->merge($additionFormFields);

        self::appendRecursiveFormFields($formFields, $additionFormFields, true);
    }

    /**
     * Gets all formFields by parent (including labels and options)
     * @param  QueryBuilder  $parent who has either nodes or formFields relation AND formGroups relation
     * @param  string  $mode
     * @param  boolean $isGroupFormat if group format is required (If not, it will convert group into form fields)
     * @return object
     */
    public function getFormQueryByParentQuery($parent, string $mode = null, bool $isGroupFormat = false)
    {
        // In case of Helptopic and Department, the relation is named as `nodes` but in
        // case of FormGroup and FormCategory, it is named as formFields
        $relationName = method_exists($parent->getModel(), 'nodes') ? 'nodes' : 'formFields';

        $baseQuery = $parent->with([
            "$relationName.labelsForFormField",
            "$relationName.labelsForValidation",
            "$relationName" => function($query) use ($mode) {
                $query->where('is_active',1);
                $this->modifyQueryByMode($mode, $query);
                return (new FormField)->queryBuilderForNestedFormFields($query);
            }
        ]);

        $doesFormGroupExist = method_exists($parent->getModel(), 'formGroups');

        if($doesFormGroupExist){
            $baseQuery->with([
                'formGroups'=> function($q) use ($mode, $isGroupFormat) {
                    // if not group format, we will query for form group fields, which is later gets converted into form render format
                    if(!$isGroupFormat){
                        $this->getFormQueryByParentQuery($q, $mode, $isGroupFormat);
                    }
                }
            ]);
        }

        return $baseQuery;
    }

    /**
     * Modifies form query according to the mode given
     * @param  string $mode  currently avaible modes are `workflow-listener` and edit
     * @return
     */
    private function modifyQueryByMode(string $mode = null, &$parentQuery)
    {
        if($mode == 'workflow-listener'){
            $parentQuery = $parentQuery->whereNotIn('title',['CC', 'Requester','Captcha'])
                ->where('type', '!=', 'file');
        }

        if($mode == 'edit-ticket'){
            $parentQuery = $parentQuery->whereNotIn('title',['Description', 'Status','Assigned', 'CC'])
                ->where('type', '!=', 'file');
        }

        if($mode == 'fork-ticket'){
            $parentQuery = $parentQuery->whereNotIn('title',['Description', 'CC'])
                ->where('type', '!=', 'file');
        }
    }

    /**
     * Formats form element by merging form group and form fields at same level and sorting them
     * @param FormCategory|FormGroup|Helptopic|Department|FormFieldOption $formCategory
     * @param bool $isGroupFormat
     * @return void
     */
    public static function formatFormElements(&$formCategory, bool $isGroupFormat = false) : void
    {
        // for FormGroup there won't be any child groups, so to avoid code break,
        // we initialise it with empty collection
        $formGroups = $formCategory->formGroups;
        $formGroups = $formGroups ?: new Collection;

        // In case of Helptopic and Department, the relation is named as `nodes` but in
        // case of FormGroup and FormCategory, it is named as formFields
        $relationName = method_exists($formCategory, 'nodes') ? 'nodes' : 'formFields';

        $formFields = $formCategory->$relationName;

        (new self)->formatFormGroup($formGroups, $isGroupFormat);

        foreach ($formFields as &$formField) {
            $formOptions = $formField->options;
            self::formatNestedFormElements($formOptions, $isGroupFormat);
        }
        // go into form fields and go through all nodes and do the same sorting
        // and getting there values so that indexes can be recalculated
        unset($formCategory->$relationName, $formCategory->formGroups);

        $formCategory->$relationName = $formFields->concat($formGroups)->sortBy('sort_order')->values();
    }

    /**
     * Formats nested child elements by merging groups and nodes into nodes,
     * so that even group can be seen as an element of node.
     * Since, child fields has a different key than parent (parent -> form-fields, child->nodes),
     * this method is seperated
     * @param Collection $formOptions
     * @param bool $isGroupFormat
     * @return void
     * @throws \Exception
     */
    public static function formatNestedFormElements(Collection &$formOptions, bool $isGroupFormat = false) : void
    {
        // loop over all options
        // in each options, there will be nodes and formGroups, which has to be
        // merged and returned
        foreach($formOptions as &$formOption){

            // for further processing of the function, option instance must have `formGroups` and `nodes` properties
            if(!isset($formOption->formGroups) || !isset($formOption->nodes)){
                throw new \Exception("formOptions must have formGroups and nodes as properties");
            }

            $childFormGroups = $formOption->formGroups;
            $childFormFields = $formOption->nodes;

            (new self)->formatFormGroup($childFormGroups, $isGroupFormat);

            foreach ($childFormFields as $childFormField) {
                $childFormFieldOptions = $childFormField->options;
                self::formatNestedFormElements($childFormFieldOptions, $isGroupFormat);
            }

            // removing nodes and formGroups properties so that these can be readded
            // with newly merged values of form fields
            unset($formOption->nodes, $formOption->formGroups);
            $formOption->nodes = $childFormFields->concat($childFormGroups)->sortBy('sort_order')->values();
        }
    }

    /**
     * Adds additional properties to form groups required in rendering the form
     * @param Collection &$formGroups
     * @param bool $isGroupFormat
     * @return void
     */
    private function formatFormGroup(Collection &$formGroups, bool $isGroupFormat) : void
    {
        $allGroupFormFields = new Collection();
        foreach ($formGroups as &$formGroup) {
            if($isGroupFormat){
                $formGroup->sort_order = $formGroup->pivot->sort_order;
                $formGroup->type = 'group';
                $formGroup->title = $formGroup->name;

                // form group blocks should be deletable
                $formGroup->is_deletable = 1;
                $formGroup->reference_type = $formGroup->pivot->table;
                $formGroup->reference_id = $formGroup->pivot->id;
            } else {
                $groupFormFields = $formGroup->formFields;
                $allGroupFormFields = $allGroupFormFields->merge($groupFormFields);

                foreach ($groupFormFields as $formField) {
                    // assumption that there won't be more than 1000 form fields in single form group
                    $formField->sort_order = $formGroup->pivot->sort_order.'.'. str_pad( $formField->sort_order ,4,"0",STR_PAD_LEFT);
                }
            }
        }

        $formGroups = $isGroupFormat ? $formGroups : $allGroupFormFields;
    }

    /**
     * relationship with form group and form field
     */
    public function formGroup(){
        return $this->belongsTo(FormGroup::class, 'form_group_id');
    }

    /**
     * Gets form field label by its identifier
     * @param string $identifier
     * @return string
     */
    public static function getLabelByIdentifier(string $identifier)
    {
        // gives label by custom_ format
        $formFieldId = str_replace('custom_', '', $identifier);
        $formField = FormField::where('id', $formFieldId)->first();
        if($formField && $formField->label){
            return $formField->label;
        }
        return '';
    }
}
