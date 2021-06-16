<?php

namespace App\FaveoReport\Traits;

use App\Model\helpdesk\Ticket\Ticket_Status as Status;
use App\Model\Common\ActivityLog;
use Illuminate\Support\Carbon;
use Lang;

/**
 * Contains methods which are allowed in report short codes
 * NOTE: this trait has to be developed to handle more short codes
 * related to activity logs.
 * This trait exploits naming conventions set by us so that it will
 * be easier to differentiate b/w short code methds and normal methods
 */
trait CustomEquationFunctions
{
  function lastStatusChange(string $statusName, object $row) : ?int
  {
    // extract ticket id from row, extract status id using statusName,
    // check ticket activity for that statusId and fetch time of last
    // activity with that status
    $statusId = Status::where('name', $statusName)->value('id');
    if(!$statusId || !isset($row->id)){
      return null;
    }

    $ticketId = $row->id;

    $activityTime = ActivityLog::where('type', 'ticket')->where('type_id', $ticketId)
      ->where('field','status')
      ->where('final_value', $statusId)
      ->orderBy('id','desc')
      ->value('created_at');

    // if instance of carbon, convert into timestamp
    if($activityTime INSTANCEOF Carbon){
      return $activityTime->timestamp;
    }

    return $activityTime;
  }
}
