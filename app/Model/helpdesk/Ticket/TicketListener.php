<?php

namespace App\Model\helpdesk\Ticket;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Observable;

class TicketListener extends Model
{
    use Observable;

    protected $table = 'ticket_listeners';

    protected $fillable = [
      /**
       * name of the listener
       */
      'name',

      /**
       * Agent or client
       */
      'triggered_by',

      /**
       * from which source tickets are targetted in the listener
       */
      'target',

      /**
       * If status is active or not
       */
      'status',

      /**
       * Order of the listener
       */
      'order',

      /**
       * Any or all
       */
      'matcher',

      /**
       * internal notes for description purpose
       */
      'internal_notes'
    ];

    public function rules()
    {
        return $this->morphMany('App\Model\helpdesk\Ticket\TicketRule', 'reference');
    }

    public function actions()
    {
        return $this->morphMany('App\Model\helpdesk\Ticket\TicketAction', 'reference');
    }

    public function events(){
        return $this->hasMany('App\Model\helpdesk\Ticket\TicketEvent');
    }

    //deletes child data as soon as workflow is deleted
    public function beforeDelete($model){
      foreach ($this->rules as $rule) {
        $rule->delete();
      }

      foreach ($this->actions as $action) {
        $action->delete();
      }
    }

    public function getTargetAttribute($value)
    {
        $data    = \App\Model\helpdesk\Ticket\Ticket_source::where('id',$value)->select('id','name')->first();
        return $data;
    }

    public function getStatusAttribute($value)
    {
      return (bool)$value;
    }
}
