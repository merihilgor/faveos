<?php

namespace App\Traits;

use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Ticket\Ticket_Status as TicketStatus;
use App\Model\helpdesk\Form\FormField;
use App\Model\helpdesk\Manage\Help_topic as HelpTopic;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

trait EnhancedDependency
{

    /**
     * Function to get basic Query builder for getting allowed override statuses for the given status/es
     * @param  Array       $ticketIds     Array of ticket Ids for which we need allowed override statuses
     * @param  string      $searchQuery   search query string passed for status with name
     * @var    Collection  $tickets       Collection of tickets with given IDs
     * @var    Array       $allowes       Array containing unique Ids of allowed statuses
     * @var    Tickets     $baseQeury     Base query builder for ticket status list
     */
    protected function getOverrideStatuses($ticketIds = [], $searchQuery = '')
    {
        $statusIds = Tickets::whereIn('id', $ticketIds)->pluck('status')->toArray();
        $tickets   = TicketStatus::whereIn('id', $statusIds)->get();
        $allowed   = [];
        $baseQuery = TicketStatus::where('name', 'LIKE', "%$searchQuery%");
        foreach ($tickets as $ticket) {
          $targetStatusIds = $ticket->overrideStatuses()->pluck('target_status')->toArray();
          if ($targetStatusIds) {
            array_push($allowed, $targetStatusIds);
          }
        }

        if ($allowed) {
          $allowed = (count($allowed) < 2) ? reset($allowed) : call_user_func_array('array_intersect', $allowed);
          $baseQuery = $baseQuery->whereIn('id', $allowed);
        }

        return $baseQuery;
    }

    /**
     * Appends query to get helpTopic only which are linked with given departments
     * @param  QueryBuilder $baseQuery  query to which it has to appended
     * @param  array  $departmentIds
     * @return QueryBuilder
     */
    protected function limitLinkedHelpTopics($baseQuery, array $departmentIds)
    {
        // query into all helptopics, get all department ids,
        // for getting linked departments, get all departments, then query into department table
        return $baseQuery->where(function($q) use($departmentIds){
          foreach ($departmentIds as $departmentId) {
              //to avoid sql injection
              $departmentId = (int) $departmentId;
              $q = $q->orWhereRaw("FIND_IN_SET($departmentId, linked_departments)")
                ->orWhereRaw("FIND_IN_SET($departmentId, department)");
          }
          return $q;
        });
    }

    /**
     * Appends query to get departments only which are linked with given helpTopics
     * @param  QueryBuilder $baseQuery  query to which it has to appended
     * @param  array  $helptopicIds
     * @return QueryBuilder
     */
    protected function limitLinkedDepartments($baseQuery, array $helptopicIds)
    {
      //query all helptopics, get all comma seperated values
      $departmentIds = HelpTopic::whereIn('id', $helptopicIds)->get()->pluck('linked_departments');
      $formattedDeptIds = [];
      foreach ($departmentIds as $commaSeperatedDeptIds) {
        if($commaSeperatedDeptIds){
          $formattedDeptIds = array_merge(explode(',',$commaSeperatedDeptIds),$formattedDeptIds);
        }
      }
      return $baseQuery->whereIn('id',$formattedDeptIds);
    }
}
