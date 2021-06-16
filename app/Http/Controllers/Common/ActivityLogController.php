<?php 

namespace App\Http\Controllers\Common;

use App\Model\Common\ActivityLog;
use App\Http\Controllers\Controller;
use Exception;

/**
 * Handles all activity log related operations
 * @author  avinash kumar <avinash.kumar@ladybirdeb.com>
 */
class ActivityLogController extends Controller 
{

	/**
	 * Type of the activity. It can be 'created', 'updated' or 'deleted'
	 * @var integer
	 */
	private $activity;

	/**
	 * Id of the medium who is creating it. For 'system' it will be null
	 * @var integer|null
	 */
	private $actionTakerId;

	/**
	 * Type of the medium who is creating it (can be user, workflow,listener, systen)
	 * @var string
	 */
	private $actionTakerType;

	/**
	 * If type id ticket, typeId will be ticket Id
	 * @var array
	 */
	private $typeId;

	/**
	 * @param  string   $activity    It can be 'created', 'updated' or 'deleted'
	 * @param  string   $actionTakerType Type of the medium who is creating it (can be user, workflow, 
	 *                                 	       	listener, systen)
	 * @param  integer 	$actionTakerId   Id of the medium who is creating it. For 'system' it will be null
	 * @param  integer  $typeId 		 If type id ticket, typeId will be ticket Id      
	 * @return null                  
	 */
	public function __construct(
		string $activity, string $actionTakerType, int $actionTakerId = null, int $typeId = null)
	{
		//validating activity type before proceeding
		$this->validateActivity($activity);

		$this->typeId = $typeId;

		$this->activity = $activity;
		
		$this->actionTakerId = $actionTakerId;
		
		$this->actionTakerType = $actionTakerType;
	}

	/**
	 * handles ticket related activity log.
	 * Can take care of action taker id by itself.
	 * @param  array  		  $field 				field that has changed
	 * @param  string|integer $finalValue 			final value of the field
	 * @param  string 		  $activityType 		it can be 'created', 'updated' or 'deleted'
	 * @return  null 
	 */
	public function ticketActivityLog( string $field = '', $initialValue = null, $finalValue = null)
	{
		$columnNameForActivity = 'is_'.$this->activity;
		
		ActivityLog::create([
			'type'=>'ticket',
			'type_id'=> $this->typeId,
			'field'=> $field, 
			'action_taker_id' => $this->actionTakerId, 
			'action_taker_type'=> $this->actionTakerType, 
			'initial_value'=>$initialValue,
			'final_value'=>$finalValue, 
			$columnNameForActivity => 1
		]);		
	}

	/**
	 * Validates activity. If found invalid activity type, it throws an exception
	 * @param  string $activity  
	 * @return null
	 */
	private function validateActivity(string $activity)
	{	
		$allowedValues = ['created','updated','deleted'];
		if(!in_array($activity, $allowedValues)){
			throw new Exception('Invalid activity');
		}
	}
}