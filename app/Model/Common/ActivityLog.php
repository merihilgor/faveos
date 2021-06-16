<?php 

namespace App\Model\Common;

use Illuminate\Database\Eloquent\Model;

/**
 * @author  avinash kumar <avinash.kumar@ladybirdeb.com>
 */
class ActivityLog extends Model
{
    protected $table = "activity_logs";
    
    protected $fillable = [
    	
    	/**
    	 * type of the activity log based on module
    	 * for eg. for ticket module, it will be 'ticket'
    	 */
    	'type',

    	/**
    	 * id of the activity type (for type=ticket, type_id will be ticket_id)
    	 */
    	'type_id',

    	/**
    	 * name of the field which has changed
    	 * it will remain empty if something is created or deleted
    	 */
    	'field',


    	//NOTE: we are not using polymorphic relationship because value of action_taker_type 
    	//can be system or cron
    	/**
    	 * id of the person who is the cause of the action
    	 * it will be null in case where action taker does not have a id (for eg system)
    	 */
    	'action_taker_id',

    	/**
    	 * model of the person who
    	 */
    	'action_taker_type',
    	
    	/**
    	 * intial value of the field
    	 */
    	'initial_value',
    	
    	/**
    	 * final value of the field
    	 */
    	'final_value',

    	/**
    	 * whether a new record has been created
    	 */
    	'is_created',

    	/**
    	 * whether a record has been updated
    	 */
    	'is_updated',
    	
    	/**
    	 * whether record has been deleted
    	 */
    	'is_deleted'
    ];

    protected $attributes = [
    	'is_created' => 0,

    	'is_updated'=>0,

    	'is_deleted'=>0
    ];

    public function ticket()
    {
    	return $this->where('type','ticket')->belongsTo('App\Model\helpdesk\Ticket\Tickets','type_id');
    }

}
