<?php
namespace App\Plugins\ServiceDesk\Controllers\Problem;

use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use App\Plugins\ServiceDesk\Model\Problem\SdProblem;
use App\Plugins\ServiceDesk\Policies\AgentPermissionPolicy;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use DB;
use App\Plugins\ServiceDesk\Traits\BaseFilterList;

/**
 * Handles problem list view by filtering/searching/arranging problems
 * USAGE :
 * Request can have following parameters:
 * 
 *               NAME          |        Possible values
 * search-query (string, optional) : any string that has to be searched in problems in the current problem
 * sort-order (string, optional) : asc/desc ( ascending/descending ) for sorting. By default its value is 'desc'
 * sort-field (string, optional) : The field that is required to be sorted. By default its value is 'updated_at'
 * limit (string, optional) : Number of problems that are reuired to be displayed on a partiular page. By default its value is 10
 * page (string, optional) : current page in the problem list.By default its value is 1
 *
 * 
 * meta : true    (without pagination)
 * format : true (Eg: #PRB-129 :: Pendrive Not Working :: Open)
 * 
 * ADVANCED SEARCH FILTER
 * problem_ids (array, optional)
 * dept_ids (array, optional)
 * impact_ids (array, optional)
 * status_ids (array, optional)
 * location_ids (array, optional)
 * priority_ids (array, optional)
 * assigned_ids (array, optional)
 * asset_ids (array, optional)
 * ticket_ids(array, optional)
 * from_ids (array, optional)
 * 
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */

class ProblemListController extends BaseServiceDeskController
{
  use BaseFilterList;

  public function __construct()
  {
    $this->middleware('auth');
    $this->agentPermissionPolicy = new AgentPermissionPolicy();
  }
  
  protected $request;

  /**
   * Gets asset-list depending upon which parameter will be passed
   * @return array => success response with filtered assets
  */
  public function getProblemList(Request $request)
  {
    $this->request = $request;

    $searchString = $request->input('search-query') ? $request->input('search-query') : '';

    $limit = $request->input('limit') ? ((int)$request->input('limit') < 0 ? 0 : (int)$request->input('limit')) : 10;

    $sortField = $request->input('sort-field') ? $request->input('sort-field') : 'updated_at';
    $sortField = $sortField ==  'problem_number' ? 'id' : $sortField;

    $sortOrder = $request->input('sort-order') ? $request->input('sort-order') : 'desc';

    $baseQueryWithoutSearch = $this->baseQueryForProblems();
        
    $baseQuery = $searchString ? $this->generalSearchQuery($baseQueryWithoutSearch, $searchString) : $baseQueryWithoutSearch;

      $problems = $baseQuery->select('id', 'subject', 'requester_id', 'department_id', 'status_type_id','priority_id','assigned_id','identifier')
                ->orderBy($sortField, $sortOrder)
                ->paginate($limit);

    $formattedProblems = $this->formatProblems($problems->toArray());

    return successResponse('', $formattedProblems); 

  }

  /**
   * Gets the base query for problems. This query can be appended with searchQuery to get desired result
   * @return QueryBuilder
  */
  private function baseQueryForProblems() : QueryBuilder
  {
    $problemsQuery = SdProblem::with([
        'requester:id,email,first_name,last_name',
        'department:id,name',
        'status:id,name',
        'priority:priority_id,priority',
        'assignedTo:id,email,first_name,last_name'
      ]
    ); 
      
    //if search filter is required
    $filteredProblems = $this->filteredProblems($problemsQuery);
    return $filteredProblems;
  }

  /**
   * Simply, formats unformatted Problems
   * @param $problems => problems with extra fields
   * @return array => formatted list of problems with limited fields
  */
  private function formatProblems($problems)
  {
    if (array_key_exists('data', $problems)) {
      $problems['problems'] = [];
      foreach ($problems['data'] as &$problem) {
        $problem['is_edit'] = ((bool) $this->agentPermissionPolicy->problemEdit());
        $problem['is_delete'] = ((bool) $this->agentPermissionPolicy->problemDelete());
        $problem['problem_number'] = "#PRB-{$problem['id']}";
        $problem['assignedTo'] = $problem['assigned_to'];
        $problem['priority']['id'] = $problem['priority']['priority_id'];
        unset($problem['status_type_id'],$problem['requester_id'],$problem['department_id'],$problem['priority_id'],$problem['assigned_id'],$problem['assigned_to'],$problem['priority']['priority_id']);
        array_push($problems['problems'], $problem);
      }
      unset($problems['data']);
    }
    else {
      $problems['problems'] = $problems;
      foreach ($problems['problems'] as &$problem) {
        unset($problem['department_id'], $problem['from'], $problem['status_type_id']);
      }
      $problems = $this->metaFormatProblems($problems);
    }
    return $problems;
  }

  /**
   * Simply, formats unformatted Problems for meta or formmat(PRB) parameter
   * @param $problems => problems with extra fields
   * @return array => formatted list of problems with limited fields
  */
  private function metaFormatProblems($problems)
  {
    if ($this->request->meta) {
      foreach ($problems['problems'] as &$problem) {
        unset($problem['status']);
      }
    }
    if ($this->request->format) {
      foreach ($problems['problems'] as &$problem) {
        $problem['name'] = '#PRB-' . $problem['id'] . ' :: ' . $problem['name'] . ' :: ' . $problem['status']['name'];
        unset($problem['status']);
      }
    }
    return ['problems' => $problems['problems']];
  }

  /**
   * Takes problem's base query and appends to it search query according to whether that search parameter is present in the 
   * request or not
   * @param $problemsQuery
   * @return object => query
  */
  private function filteredProblems(QueryBuilder $problemsQuery) : QueryBuilder
  {
    //problems based on managed by ids
    $this->viewByFieldValueTypeProblemQuery('problem_ids', 'id', $problemsQuery);

    //problems based on managed by ids
    $this->viewByFieldValueTypeProblemQuery('dept_ids', 'department_id', $problemsQuery);

    //problems based on organization ids
    $this->viewByFieldValueTypeProblemQuery('impact_ids', 'impact_id', $problemsQuery);

    //problems based on problem ids
    $this->viewByFieldValueTypeProblemQuery('status_ids', 'status_type_id', $problemsQuery);

    //problems based on department ids
    $this->viewByFieldValueTypeProblemQuery('location_ids', 'location_id', $problemsQuery);

    //problems based on asset type ids
    $this->viewByFieldValueTypeProblemQuery('priority_ids', 'priority_id', $problemsQuery);

    //problems based on product ids
    $this->viewByFieldValueTypeProblemQuery('assigned_ids', 'assigned_id', $problemsQuery);

    //problems based on assigned on time
    $this->viewByFieldValueAssetsProblemQuery('asset_ids', 'asset_id', $problemsQuery);

    //problems based on used by ids
    $this->viewByFieldValueFromProblemQuery('requester_ids', 'id', $problemsQuery);

    //problems based on assigned on time
    $this->viewByFieldValueTicketProblemQuery('ticket_ids', 'ticket_id', $problemsQuery);

    //problems with created_at in a given time range
    $this->filteredServiceQueryModifierForTimeRange('created_at', 'created_at', $problemsQuery);

    //problems with updated_at in a given time range
    $this->filteredServiceQueryModifierForTimeRange('updated_at', 'updated_at', $problemsQuery);

    //problems based on created_at
    $this->viewByFieldValueChangeProblemQuery('change_ids', 'change_id', $problemsQuery);

    return $problemsQuery;
  }

  /**
   * check for the passed fieldName in request and appends it query to problemsQuery from DB
   * NOTE: it is just a helper method for  filteredProblems method and should not be used by other methods
   * @param $fieldNameInRequest string => field name in the request coming from front end
   * @param $fieldNameInDB string => field name in the db by which we query
   * @param &$problemsQuery => it is the base query to which search queries has to be appended.
   *                       This is passed by reference, so at the end of the method it gets updated
   * @return object => query
  */
  private function viewByFieldValueTypeProblemQuery($fieldNameInRequest, $fieldNameInDB, &$problemsQuery)
  {
    if ($this->request->has($fieldNameInRequest)) {
      $queryIds = (array)$this->request->input($fieldNameInRequest);
      $problemsQuery = $problemsQuery->whereIn($fieldNameInDB, $queryIds);
    }
  }

  /**
   * check for the passed fieldName in request and appends it query to problemsQuery from DB
   * NOTE: it is just a helper method for  filteredProblems method and should not be used by other methods
   * @param $fieldNameInRequest string => field name in the request coming from front end
   * @param $fieldNameInDB string => field name in the db by which we query
   * @param &$problemsQuery => it is the base query to which search queries has to be appended.
   *                       This is passed by reference, so at the end of the method it gets updated
   * @return object => query
  */
  private function viewByFieldValueAssetsProblemQuery($fieldNameInRequest, $fieldNameInDB, &$problemsQuery)
  {
    if ($this->request->has($fieldNameInRequest)) {
      $queryIds = (array)$this->request->input($fieldNameInRequest);
      $problemsQuery = $problemsQuery->WhereHas('attachAssets', function($q) use($fieldNameInDB, $queryIds) {
                            $q->whereIn($fieldNameInDB, $queryIds);
                          });
    }
  }

  /**
   * check for the passed fieldName in request and appends it query to problemsQuery from DB
   * NOTE: it is just a helper method for  filteredProblems method and should not be used by other methods
   * @param $fieldNameInRequest string => field name in the request coming from front end
   * @param $fieldNameInDB string => field name in the db by which we query
   * @param &$problemsQuery => it is the base query to which search queries has to be appended.
   *                       This is passed by reference, so at the end of the method it gets updated
   * @return object => query
  */
  private function viewByFieldValueFromProblemQuery($fieldNameInRequest, $fieldNameInDB, &$problemsQuery)
  {
    if ($this->request->has($fieldNameInRequest)) {
      $queryIds = (array)$this->request->input($fieldNameInRequest);
      $problemsQuery = $problemsQuery->WhereHas('requester', function($q) use($fieldNameInDB, $queryIds) {
                            $q->whereIn($fieldNameInDB, $queryIds);
                          });
    }
  }

   /**
   * check for the passed fieldName in request and appends it query to problemsQuery from DB
   * NOTE: it is just a helper method for  filteredProblems method and should not be used by other methods
   * @param $fieldNameInRequest string => field name in the request coming from front end
   * @param $fieldNameInDB string => field name in the db by which we query
   * @param &$problemsQuery => it is the base query to which search queries has to be appended.
   *                       This is passed by reference, so at the end of the method it gets updated
   * @return object => query
  */
  private function viewByFieldValueTicketProblemQuery($fieldNameInRequest, $fieldNameInDB, &$problemsQuery)
  {
    if ($this->request->has($fieldNameInRequest)) {
      $queryIds = (array)$this->request->input($fieldNameInRequest);
      $problemsQuery = $problemsQuery->WhereHas('attachTickets', function($q) use($fieldNameInDB, $queryIds) {
                            $q->whereIn($fieldNameInDB, $queryIds);
                          });
    }
  } 

  /**
   * check for the passed fieldName in request and appends it query to problemsQuery from DB
   * NOTE: it is just a helper method for  filteredProblems method and should not be used by other methods
   * @param $fieldNameInRequest string => field name in the request coming from front end
   * @param $fieldNameInDB string => field name in the db by which we query
   * @param &$problemsQuery => it is the base query to which search queries has to be appended.
   *                       This is passed by reference, so at the end of the method it gets updated
   * @return object => query
  */
  private function viewByFieldValueChangeProblemQuery($fieldNameInRequest, $fieldNameInDB, &$problemsQuery)
  {
    if ($this->request->has($fieldNameInRequest)) {
      $queryIds = (array)$this->request->input($fieldNameInRequest);
      $problemsQuery = $problemsQuery->WhereHas('attachChange', function($q) use($fieldNameInDB, $queryIds) {
                            $q->whereIn($fieldNameInDB, $queryIds);
                          });
    }
  }

  /**
   * Gets general search query. (this will only be used by 'baseQueryForAssets' method)
   * @param  QueryBuilder $baseQuery base query
   * @param string $searchString string which has to be searched
   * @return QueryBuilder
  */
  private function generalSearchQuery(QueryBuilder $baseQuery, string $searchString) : QueryBuilder
  {
    $problemsQuery = $baseQuery->where(function($q) use ($searchString) {
              $q
                ->orWhere('identifier', 'LIKE', "%$searchString%")
                ->orWhere('subject', 'LIKE', "%$searchString%")
                ->orwhereHas('department', function($query) use ($searchString) {
                    $query->where('name', 'LIKE', "%$searchString%");
                })
                ->orWhereHas('impact', function($query) use($searchString) {
                    $query->where('name', 'LIKE', "%$searchString%");
                })
                ->orwhereHas('status', function($query) use ($searchString) {
                    $query->where('name', 'LIKE', "%$searchString%");
                })
                ->orWhereHas('location', function($query) use($searchString) {
                    $query->where('title', 'LIKE', "%$searchString%");
                })
                ->orWhereHas('attachAssets', function($query) use($searchString) {
                    $query->where('name', 'LIKE', "%$searchString%");
                })
                ->orWhereHas('priority', function($query) use($searchString) {
                    $query->where('priority', 'LIKE', "%$searchString%");
                })
                ->orWhereHas('attachTickets', function($query) use($searchString) {
                    $query->where('ticket_number', 'LIKE', "%$searchString%");
                });
        });

    // based on user relation , user could be searched
    $this->userSearchQuery($problemsQuery, 'requester', $searchString);
    $this->userSearchQuery($problemsQuery, 'assignedTo', $searchString);

    return $problemsQuery;
  }
 

}