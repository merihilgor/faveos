<?php
namespace App\Plugins\ServiceDesk\Controllers\Releses;

use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use App\Plugins\ServiceDesk\Model\Releases\SdReleases;
use App\Plugins\ServiceDesk\Traits\BaseFilterList;
use App\Plugins\ServiceDesk\Policies\AgentPermissionPolicy;

/**
 * Handles Releases list view by filtering/searching/arranging Releases
 * USAGE :
 * Request can have following parameters:
 * 
 * NAME | Possible values
 * search-query (string, optional) : any string that has to be searched in Releases in the current Release
 * sort-order (string, optional) : asc/desc ( ascending/descending ) for sorting. By default its value is 'desc'
 * sort-field (string, optional) : The field that is required to be sorted. By default its value is 'updated_at'
 * limit (string, optional) : Number of Releases that are reuired to be displayed on a partiular page. By default  its value is 10
 * page (string, optional) : current page in the Release list.By default its value is 1
 *
 * release_ids (array, optional)
 * status_ids (array, optional)
 * location_ids (array, optional)
 * priority_ids (array, optional)
 * release_type_ids (array, optional)
 * planned_start_date (datetime in agent's timezone in faveo date format (See FaveoDateParser's doc for format), optional)
 * planned_end_date (datetime in agent's timezone in faveo date format (See FaveoDateParser's doc for format), optional)
 * created_at (datetime in agent's timezone in faveo date format (See FaveoDateParser's doc for format), optional)
 * updated_at (datetime in agent's timezone in faveo date format (See FaveoDateParser's doc for format), optional)
 * asset_ids (array, optional)
 * change_ids (array, optional)
 * 
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */

class ReleaseListController extends BaseServiceDeskController
{
    use BaseFilterList;

    protected $request;

    public function __construct()
    {
        $this->middleware('auth');
        $this->agentPermissionPolicy = new AgentPermissionPolicy();
    } 

    /**
     * Gets release-list depending upon which parameter will be passed
     * @return array => success response with filtered assets
     */
    public function getReleaseList(Request $request)
    {
        $this->request = $request;

        $searchString = $request->input('search-query') ?: '';
        $limit = $request->input('limit') ?: 10;
        $sortField = $request->input('sort-field') ?: 'updated_at';
        $sortOrder = $request->input('sort-order') ?: 'desc'; 

        $baseQueryWithoutSearch = $this->baseQueryForReleases();
        $baseQuery = $searchString ? $this->generalSearchQuery($baseQueryWithoutSearch, $searchString) : $baseQueryWithoutSearch;

        $releases = $baseQuery->select('id', 'subject', 'planned_start_date', 'planned_end_date', 'priority_id', 'status_id', 'release_type_id', 'identifier')
            ->orderBy($sortField, $sortOrder)
            ->paginate($limit)
            ->toArray();

        $this->formatReleaseList($releases);

        return successResponse('', $releases); 
    }

    /**
     * Simply, formats unformatted Releases
     * @param array $releases
     * @return null
     */
    private function formatReleaseList(&$releases)
    {
        $releases['releases'] = $releases['data'];
        unset($releases['data']);

        foreach ($releases['releases'] as &$release) {
            $release['is_edit'] = ((bool) $this->agentPermissionPolicy->releaseEdit());
            $release['is_delete'] = ((bool) $this->agentPermissionPolicy->releaseDelete());
            unset($release['status_id'], $release['release_type_id'], $release['priority_id']);
        }
    }

    /**
     * Gets the base query for releases. This query can be appended with searchQuery to get desired result
     * @return QueryBuilder
     */
    private function baseQueryForReleases() : QueryBuilder
    {
        $releasesQuery = SdReleases::with(['releaseType:id,name','priority:id,name','status:id,name']); 

        //if search filter is required
        $filteredReleases = $this->filteredReleases($releasesQuery);
        return $filteredReleases;
    }

    /**
     * Takes release's base query and appends to it search query according to whether that search parameter
     * is present in the request or not
     * @param $releaseQuery
     * @return object => query
     */
    private function filteredReleases(QueryBuilder $releasesQuery) : QueryBuilder
    {
        //releases based on release ids
        $this->viewByFieldValueTypeReleaseQuery('release_ids', 'id', $releasesQuery);

        //releases based on status ids
        $this->viewByFieldValueTypeReleaseQuery('status_ids', 'status_id', $releasesQuery);

        //releases based on priority ids
        $this->viewByFieldValueTypeReleaseQuery('priority_ids', 'priority_id', $releasesQuery);

        //releases based on release type ids
        $this->viewByFieldValueTypeReleaseQuery('release_type_ids', 'release_type_id', $releasesQuery);

        //releases based on location ids
        $this->viewByFieldValueTypeReleaseQuery('location_ids', 'location_id', $releasesQuery);

        //releases with planned_start_date in a given time range
        $this->filteredServiceQueryModifierForTimeRange('planned_start_date', 'planned_start_date', $releasesQuery);

        //releases with planned_end_date in a given time range
        $this->filteredServiceQueryModifierForTimeRange('planned_end_date', 'planned_end_date', $releasesQuery);

        //releases with created_at in a given time range
        $this->filteredServiceQueryModifierForTimeRange('created_at', 'created_at', $releasesQuery);

        //releases with updated_at in a given time range
        $this->filteredServiceQueryModifierForTimeRange('updated_at', 'updated_at', $releasesQuery);

        //releases based on asset ids
        $this->viewByReleaseAttachedRelationsQuery('asset_ids', 'sd_assets.id', 'attachAssets', $releasesQuery);

        //releases based on change ids
        $this->viewByReleaseAttachedRelationsQuery('change_ids', 'sd_changes.id', 'attachChanges', $releasesQuery);

        return $releasesQuery;
    }

    /**
     * check for the passed fieldName in request and appends it query to releasesQuery from DB
     * NOTE: it is just a helper method for filteredreleases method and should not be used by other methods
     * @param $fieldNameInRequest string => field name in the request coming from front end
     * @param $fieldNameInDB string => field name in the db by which we query
     * @param &$releasesQuery => it is the base query to which search queries has to be appended.
     * This is passed by reference, so at the end of the method it gets updated
     * @return object => query
     */
    private function viewByFieldValueTypeReleaseQuery($fieldNameInRequest, $fieldNameInDB, &$releasesQuery)
    {
        if ($this->request->has($fieldNameInRequest)) {
            $queryIds = (array)$this->request->input($fieldNameInRequest);
            $releasesQuery = $releasesQuery->whereIn($fieldNameInDB, $queryIds);
        }
    }

    /**
     * check for the passed fieldName in request and appends it query to releasesQuery from DB
     * NOTE: it is just a helper method for  filteredReleases method and should not be used by other methods
     * @param $fieldNameInRequest string => field name in the request coming from front end
     * @param $fieldNameInDB string => field name in the db by which we query
     * @param &$releasesQuery => it is the base query to which search queries has to be appended.
     *                       This is passed by reference, so at the end of the method it gets updated
     * @return object => query
     */
    private function viewByReleaseAttachedRelationsQuery($fieldNameInRequest, $fieldNameInDB, $fieldRelation, &$releasesQuery)
    {
        if ($this->request->has($fieldNameInRequest)) {
            $queryIds = (array)$this->request->input($fieldNameInRequest);
            $releasesQuery = $releasesQuery->whereHas($fieldRelation, function($query) use($fieldNameInDB, $queryIds) {
                            $query->whereIn($fieldNameInDB, $queryIds);
                          });
        }
    }

    /**
     * Gets general search query. (this will only be used by 'baseQueryForReleases' method)
     * @param QueryBuilder $baseQuery base query
     * @param string $searchString string which has to be searched
     * @return QueryBuilder
     */
    private function generalSearchQuery(QueryBuilder $baseQuery, string $searchString) : QueryBuilder
    {
        return $baseQuery->where(function($parentQuery) use ($searchString) {
            $parentQuery
                ->Where('identifier', 'LIKE', "%$searchString%")
                ->orWhere('subject', 'LIKE', "%$searchString%")
                ->orwhereHas('status', function($query) use ($searchString) {
                    $query->where('name', 'LIKE', "%$searchString%");
                    })
                ->orwhereHas('releaseType', function($query) use ($searchString) {
                    $query->where('name', 'LIKE', "%$searchString%");
                    })
                ->orwhereHas('priority', function($query) use ($searchString) {
                    $query->where('name', 'LIKE', "%$searchString%");
                    })
                ->orwhereHas('location', function($query) use ($searchString) {
                    $query->where('title', 'LIKE', "%$searchString%");
                    })
                ->orWhereHas('attachAssets', function($query) use($searchString) {
                    $query->where('name', 'LIKE', "%$searchString%");
                })
                ->orWhereHas('attachChanges', function($query) use($searchString) {
                    $query->where('subject', 'LIKE', "%$searchString%");
                });
            });
    } 

}