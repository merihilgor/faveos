<?php

namespace App\Model\helpdesk\Form;

use Illuminate\Database\Eloquent\Model;
use App\Model\helpdesk\Agent\Department;
use App\Model\helpdesk\Manage\Help_topic as HelpTopic;
use App\Traits\Observable;
use App\Model\helpdesk\Form\FormCategory;
use App\Model\helpdesk\Form\FormField;

class FormFieldOption extends Model
{
    use Observable;

    protected $table = 'form_field_options';

    protected $hidden = ['form_field_id','created_at','updated_at'];

    protected $fillable = ['form_field_id','value', 'sort_order'];

    protected $appends = ['form_identifier'];

    /**
     * This identifier will be used at frontend to know if it is a form_field, form field option, help topic option, department option or label
     * @return string
     */
    public function getFormIdentifierAttribute()
    {
        return "form_option_".$this->id;
    }

    //for nested structures
    public function formFields()
    {
        //one to many relationship
        return $this->belongsTo('App\Model\helpdesk\Form\FormField','form_field_id');
    }

    /**
     * using polymorphic relation for binding
     * @return array      array of labels
     */
    public function labels()
    {
        return $this->morphMany('App\Model\helpdesk\Form\FormFieldLabel', 'labelable')
                    ->where('meant_for','option');
    }

    /**
     * maps to all the formField entries embedded inside nodes
     */
    public function nodes()
    {
        return $this->hasMany('App\Model\helpdesk\Form\FormField','option_id','id');
    }

    private function beforeDelete($model){
        $model->labels()->delete();

        //deleting one by one will make sure that it fires delete event in the child model
        foreach ($this->nodes as $node) {
          $node->delete();
        }
    }

    public function formGroups()
    {
      return $this->belongsToMany('App\Model\helpdesk\Form\FormGroup')->withPivot('sort_order','id');;
    }
}
