<?php

namespace App\Plugins\ServiceDesk\Controllers\Changes;

use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use App\Plugins\ServiceDesk\Model\Changes\SdChanges;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use App\Plugins\ServiceDesk\Traits\BaseFilterList;


/**
 * Handles change list view by filtering/searching/arranging changes
 * USAGE :
 * Request can have following parameters:
 * 
 *               NAME          |        Possible values
 * search_query (string, optional) : any string that has to be searched in changes
 * sort_order (string, optional) : asc/desc ( ascending/descending ) for sorting. By default its value is 'desc'
 * sort_field (string, optional) : The field that is required to be sorted. By default its value is 'updated_at'
 * limit (string, optional) : Number of assets that are reuired to be displayed on a partiular page. By default its value is 10
 * page (string, optional) : current page in the change list.By default its value is 1
 * 
 * 
 * 
 * ADVANCED SEARCH FILTER
 * change_ids (array, optional)
 * department_ids (array, optional) 
 * team_ids (array, optional)
 * requester_ids (array, optional)
 * status_ids(array, optional)
 * priority_ids (array, optional)
 * change_type_ids (array, optional)
 * impact_ids (array, optional)
 * location_ids (array, optional)
 * asset_ids (array, optional)
 * release_ids (array, optional)
 * ticket_ids (array, optional)
 * problem_ids (array, optional)
 * created_at (datetime in agent's timezone in faveo date format (See FaveoDateParser's doc for format), optional)
 * updated_at (datetime in agent's timezone in faveo date format (See FaveoDateParser's doc for format), optional)
 * 
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
*/

class ChangeListController extends BaseServiceDeskController
{
  use BaseFilterList;

  protected $request;

  public function __construct()
  {
    $this->middleware('role.agent');
  }

  /**
   * Gets change-list depending upon which parameter will be passed
   * @return array => success response with filtered assets
  */
  public function getChangeList(Request $request)
  {
    $this->request = $request;

    $searchString = $request->input('search-query') ?: '';
    $limit = $request->input('limit') ?: 10;
    $sortField = $request->input('sort-field') ?: 'updated_at';
    $sortOrder = $request->input('sort-order') ?: 'desc'; 

    $baseQueryWithoutSearch = $this->baseQueryForChanges();
    $baseQuery = $searchString ? $this->generalSearchQuery($baseQueryWithoutSearch, $searchString) : $baseQueryWithoutSearch;

    $changes = $baseQuery->select('id', 'subject', 'created_at', 'priority_id', 'status_id', 'department_id', 'requester_id', 'identifier')
      ->orderBy($sortField, $sortOrder)
      ->paginate($limit)
      ->toArray();

    $this->formatChanges($changes);

    return successResponse('', $changes); 
  }

  /**
   * method to format changes
   * @param $changes
   * @return null
   */
  private function formatChanges(&$changes)
  { 
    $changes['changes'] = $changes['data'];
    unset($changes['data']);

    foreach ($changes['changes'] as &$change) {
      unset($change['priority_id'], $change['status_id'], $change['department_id'], $change['requester_id']);
    }

  }

  /**
   * Gets the base query for changes. This query can be appended with searchQuery to get desired result
   * @return QueryBuilder
  */
  private function baseQueryForChanges() : QueryBuilder
  {
    $changesQuery = SdChanges::with(
      [
        'priority:id,name',
        'status:id,name',
        'department:id,name',
        'requester:id,first_name,last_name,email,profile_pic'
      ]
    ); 
      
    //if search filter is required
    $filteredChanges = $this->filteredChanges($changesQuery);
    return $filteredChanges;
  }

  /**
   * Takes change's base query and appends to it search query according to whether that search parameter
   * is present in the request or not
   * @param $changesQuery
   * @return object => query
  */
  private function filteredChanges(QueryBuilder $changesQuery) : QueryBuilder
  {
    //changes based on change ids
    $this->viewByFieldValueTypeChangeQuery('change_ids', 'id', $changesQuery);

    //changes based on department ids
    $this->viewByFieldValueTypeChangeQuery('department_ids', 'department_id', $changesQuery);

    //changes based on team ids
    $this->viewByFieldValueTypeChangeQuery('team_ids', 'team_id', $changesQuery);

    //changes based on requester ids
    $this->viewByFieldValueTypeChangeQuery('requester_ids', 'requester_id', $changesQuery);

    //changes based on status ids
    $this->viewByFieldValueTypeChangeQuery('status_ids', 'status_id', $changesQuery);

    //changes based on priority ids
    $this->viewByFieldValueTypeChangeQuery('priority_ids', 'priority_id', $changesQuery);

    //changes based on change type ids
    $this->viewByFieldValueTypeChangeQuery('change_type_ids', 'change_type_id', $changesQuery);

    //changes based on impact ids
    $this->viewByFieldValueTypeChangeQuery('impact_ids', 'impact_id', $changesQuery);

    //changes based on location ids
    $this->viewByFieldValueTypeChangeQuery('location_ids', 'location_id', $changesQuery);

    //changes based on asset ids
    $this->viewByChangeAttachedRelationsQuery('asset_ids', 'sd_assets.id', 'attachAssets', $changesQuery);

    //changes based on release ids
    $this->viewByChangeAttachedRelationsQuery('release_ids', 'sd_releases.id', 'attachReleases', $changesQuery);

    //changes based on ticket ids
    $this->viewByChangeAttachedRelationsQuery('ticket_ids', 'tickets.id', 'attachTickets', $changesQuery);

    //changes based on problem ids
    $this->viewByChangeAttachedRelationsQuery('problem_ids', 'sd_problem.id', 'attachProblems', $changesQuery);

    //changes with created_at in a given time range
    $this->filteredServiceQueryModifierForTimeRange('created_at', 'created_at', $changesQuery);

    //changes with updated_at in a given time range
    $this->filteredServiceQueryModifierForTimeRange('updated_at', 'updated_at', $changesQuery);

    //excluding change attached to release based on release id
    $this->viewByExcludingChangeAttachedRelationsQuery('release_id', 'release_id', 'attachReleases', $changesQuery);


    return $changesQuery;
  }

  /**
   * helper method for filtering assets which are not associated with other module (problem)
   *
   * @param $fieldNameInRequest string => field name in the request coming from front end
   * @param $fieldNameInDB string => field name in the db by which we query
   * @param $typeValue string => type of model to which asset is attached
   * @param $fieldRelation string => relation between assets and model
   * @param &$changesQuery => it is the base query to which search queries has to be appended.
   *                       This is passed by reference, so at the end of the method it gets updated
   * @return object => query
   */
  private function viewByExcludingChangeAttachedRelationsQuery($fieldNameInRequest, $fieldNameInDB, $fieldRelation, &$changesQuery)
  {
    $typeId = $this->request->input($fieldNameInRequest);
    if ($this->request->has($fieldNameInRequest)) {
      $changesQuery = $changesQuery->whereDoesntHave($fieldRelation, function($q) use($fieldNameInRequest,$typeId) {
                                $q->where([[$fieldNameInRequest, $typeId]]);
                              });
    }
  }

  /**
   * check for the passed fieldName in request and appends it query to changesQuery from DB
   * NOTE: it is just a helper method for  filteredchanges method and should not be used by other methods
   * @param $fieldNameInRequest string => field name in the request coming from front end
   * @param $fieldNameInDB string => field name in the db by which we query
   * @param &$changesQuery => it is the base query to which search queries has to be appended.
   *                       This is passed by reference, so at the end of the method it gets updated
   * @return object => query
  */
  private function viewByFieldValueTypeChangeQuery($fieldNameInRequest, $fieldNameInDB, &$changesQuery)
  {
    if ($this->request->has($fieldNameInRequest)) {
      $queryIds = (array)$this->request->input($fieldNameInRequest);
      $changesQuery = $changesQuery->whereIn($fieldNameInDB, $queryIds);
    }
  }

  /**
   * check for the passed fieldName in request and appends it query to changesQuery from DB
   * NOTE: it is just a helper method for  filteredChanges method and should not be used by other methods
   * @param $fieldNameInRequest string => field name in the request coming from front end
   * @param $fieldNameInDB string => field name in the db by which we query
   * @param &$problemsQuery => it is the base query to which search queries has to be appended.
   *                       This is passed by reference, so at the end of the method it gets updated
   * @return object => query
   */
  private function viewByChangeAttachedRelationsQuery($fieldNameInRequest, $fieldNameInDB, $fieldRelation, &$changesQuery)
  {
    if ($this->request->has($fieldNameInRequest)) {
      $queryIds = (array)$this->request->input($fieldNameInRequest);
      $changesQuery = $changesQuery->WhereHas($fieldRelation, function($q) use($fieldNameInDB, $queryIds) {
                            $q->whereIn($fieldNameInDB, $queryIds);
                          });
    }
  }

  /**
   * Gets general search query. (this will only be used by 'baseQueryForChanges' method)
   * @param  QueryBuilder $baseQuery base query
   * @param string $searchString string which has to be searched
   * @return QueryBuilder
  */
  private function generalSearchQuery(QueryBuilder $baseQuery, string $searchString) : QueryBuilder
  {
    $changesQuery = $baseQuery->where(function($parentQuery) use ($searchString) {
              $parentQuery
                ->where('subject', 'LIKE', "%$searchString%")
                ->orWhere('identifier', 'LIKE', "%$searchString%")
                ->orwhereHas('department', function($query) use ($searchString) {
                    $query->where('name', 'LIKE', "%$searchString%");
                })
                ->orwhereHas('team', function($query) use ($searchString) {
                    $query->where('name', 'LIKE', "%$searchString%");
                })
                ->orwhereHas('status', function($query) use ($searchString) {
                    $query->where('name', 'LIKE', "%$searchString%");
                })
                ->orwhereHas('priority', function($query) use ($searchString) {
                    $query->where('name', 'LIKE', "%$searchString%");
                })
                ->orwhereHas('changeType', function($query) use ($searchString) {
                    $query->where('name', 'LIKE', "%$searchString%");
                })
                ->orwhereHas('impactType', function($query) use ($searchString) {
                    $query->where('name', 'LIKE', "%$searchString%");
                })
                ->orwhereHas('location', function($query) use ($searchString) {
                    $query->where('title', 'LIKE', "%$searchString%");
                })
                ->orWhereHas('attachAssets', function($query) use($searchString) {
                    $query->where('name', 'LIKE', "%$searchString%");
                })
                ->orWhereHas('attachReleases', function($query) use($searchString) {
                    $query->where('subject', 'LIKE', "%$searchString%");
                })
                ->orWhereHas('attachProblems', function($query) use($searchString) {
                    $query->where('subject', 'LIKE', "%$searchString%");
                })
                ->orWhereHas('attachTickets', function($query) use($searchString) {
                    $query->where('ticket_number', 'LIKE', "%$searchString%");
                });
           });

    // based on user relation , user could be searched
    $this->userSearchQuery($changesQuery, 'requester', $searchString);

    return $changesQuery;
  }

}
 