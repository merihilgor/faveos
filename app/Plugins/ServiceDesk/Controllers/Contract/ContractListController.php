<?php

namespace App\Plugins\ServiceDesk\Controllers\Contract;

use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use App\Plugins\ServiceDesk\Model\Contract\SdContract;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use App\Plugins\ServiceDesk\Traits\BaseFilterList;
use Carbon\Carbon;
use App\Plugins\ServiceDesk\Policies\AgentPermissionPolicy;

/**
* Handles Contracts list view by filtering/searching/arranging Contracts
* USAGE :
* Request can have following parameters:
* 
* NAME | Possible values
* search-query (string, optional) : any string that has to be searched in Contract in the current Contracts
* sort-order (string, optional) : asc/desc ( ascending/descending ) for sorting. By default its value is 'desc'
* sort-field (string, optional) : The field that is required to be sorted. By default its value is 'updated_at'
* limit (string, optional) : Number of Releases that are reuired to be displayed on a partiular page. By default its value is 10
* page (string, optional) : current page in the Release list.By default its value is 1
*
* 
* 
* 
* contract_ids (array, optional)
* contract_type_ids (array, optional)
* approver_ids (array, optional)
* vendor_ids (array, optional)
* license_type_ids (array, optional)
* license_counts (array, optional)
* status_ids (array, optional)
* renewal_status_ids (array, optional)
* owner_ids (array, optional)
* asset_ids (array, optional)
* notify_agent_ids (array, optional)
* cost_begin (unsigned integer)
* cost_end (usnigned integer)
* notify_in_days_begin (unsignged integer)
* notify_in_days_end (unsigned integer)
* contract_start_date (datetime in agent's timezone in faveo date format (See FaveoDateParser's doc for format), optional)
* contract_end_date (datetime in agent's timezone in faveo date format (See FaveoDateParser's doc for format), optional)
* created_at (datetime in agent's timezone in faveo date format (See FaveoDateParser's doc for format), optional)
* updated_at (datetime in agent's timezone in faveo date format (See FaveoDateParser's doc for format), optional)
*
* @author Krishna Vishwakarma <krishna.vishwakarma@ladybirdweb.com>
*/

class ContractListController extends BaseServiceDeskController
{
    use BaseFilterList;
    
    protected $request;

    public function __construct()
    {
        $this->middleware('auth');
        $this->agentPermissionPolicy = new AgentPermissionPolicy();
    } 

    /**
    * Gets contract-list depending upon which parameter will be passed
    * @return array => success response with filtered assets
    */
    public function getContractList(Request $request)
    {
        $this->request = $request;

        $searchString = $request->input('search-query') ?: '';
        $limit = $request->input('limit') ?: 10;
        $sortField = $request->input('sort-field') ?: 'updated_at';
        $sortField = $sortField ==  'expiry' ? 'contract_end_date' : $sortField;
        $sortOrder = $request->input('sort-order') ?: 'desc'; 

        $baseQueryWithoutSearch = $this->baseQueryForContracts();
        $baseQuery = $searchString ? $this->generalSearchQuery($baseQueryWithoutSearch, $searchString) : $baseQueryWithoutSearch;

        $contracts = $baseQuery->select('id', 'name', 'status_id', 'renewal_status_id', 'cost', 'contract_end_date as expiry', 'contract_type_id', 'vendor_id', 'approver_id', 'identifier','contract_start_date','contract_end_date')
            ->orderBy($sortField, $sortOrder)
            ->paginate($limit)
            ->toArray();

        $this->formatContracts($contracts);

        return successResponse('', $contracts); 
    }

    /** 
     * method to format contracts
     * @param array $contracts
     * @return null
     */
    private function formatContracts(&$contracts)
    {
        $contracts['contracts'] = $contracts['data'];
        unset($contracts['data']);

        foreach ($contracts['contracts'] as &$contract) {
            $contract['is_delete'] = ((bool) $this->agentPermissionPolicy->contractDelete());
            if (Carbon::now()->toDateTimeString() > $contract['expiry']) {
                $title = 'Expired';
            } else {
                $numberOfDaysInExpiry = $this->countNumberOfDays($contract['expiry']);
                $title = ($numberOfDaysInExpiry > 99) ? $contract['expiry'] : "Within $numberOfDaysInExpiry Days";
                if(!$numberOfDaysInExpiry) {
                    $title = "Today";
                }
            }
            $contract['expiry'] = [
                'title' => $title,
                'timestamp' => $contract['expiry']
            ];
            $contract['is_edit'] = ($contract['contract_status']['name'] == 'Draft');
            unset($contract['status_id'], $contract['renewal_status_id'], $contract['contract_type_id'], $contract['vendor_id'], $contract['approver_id']);
        }

        $this->addMinAndMaxValueForContractCostAndIdentifierField($contracts);
    }


    /**
    * method to add minimum and maximum value for contract cost and identifier
    * @param array $contracts
    * @return null
    */
    private function addMinAndMaxValueForContractCostAndIdentifierField(array &$contracts)
    {
        $contractObject = new SdContract();
        $contracts['minimum_cost'] = $contractObject->min('cost');
        $contracts['maximum_cost'] = $contractObject->max('cost');
        $contracts['minimum_notify_before'] = $contractObject->min('notify_before');
        $contracts['maximum_notify_before'] = $contractObject->max('notify_before');
    }

    /**
    * Gets the base query for contracts. This query can be appended with searchQuery to get desired result
    * @return QueryBuilder
    */
    private function baseQueryForContracts() : QueryBuilder
    {
        $contractsQuery = SdContract::with([
            'contractStatus:id,name',
            'contractRenewalStatus:id,name',
            'vendor:id,name',
            'contractType:id,name'
        ]);  

        //if search filter is required
        $filteredContracts = $this->filteredContracts($contractsQuery);
        return $filteredContracts;
    }

    /**
    * Takes contract's base query and appends to it search query according to whether that search parameter
    * is present in the request or not
    * @param $contractsQuery
    * @return object => query
    */
    private function filteredContracts(QueryBuilder $contractsQuery) : QueryBuilder
    {
        //contracts based on contract ids
        $this->viewByFieldValueTypeContractQuery('contract_ids', 'id', $contractsQuery);

        //contracts based on contract type ids
        $this->viewByFieldValueTypeContractQuery('contract_type_ids', 'contract_type_id', $contractsQuery);

        //contracts based on approver ids
        $this->viewByFieldValueTypeContractQuery('approver_ids', 'approver_id', $contractsQuery);

        //contracts based on vendor ids
        $this->viewByFieldValueTypeContractQuery('vendor_ids', 'vendor_id', $contractsQuery);

         //contracts based on license type ids
        $this->viewByFieldValueTypeContractQuery('license_type_ids', 'license_type_id', $contractsQuery);

        //contracts based on license counts
        $this->viewByFieldValueTypeContractQuery('license_counts', 'licensce_count', $contractsQuery);

        //contracts based on status ids
        $this->viewByFieldValueTypeContractQuery('status_ids', 'status_id', $contractsQuery);

        //contracts based on renewal status ids
        $this->viewByFieldValueTypeContractQuery('renewal_status_ids', 'renewal_status_id', $contractsQuery);

        //contracts based on owner ids
        $this->viewByFieldValueTypeContractQuery('owner_ids', 'owner_id', $contractsQuery);

        //contracts with contract_start_date in a given time range
        $this->filteredServiceQueryModifierForTimeRange('contract_start_date', 'contract_start_date', $contractsQuery);

        //contracts with contract_end_date in a given time range
        $this->filteredServiceQueryModifierForTimeRange('contract_end_date', 'contract_end_date', $contractsQuery);

        //contracts with created_at in a given time range
        $this->filteredServiceQueryModifierForTimeRange('created_at', 'created_at', $contractsQuery);

        //contracts with updated_at in a given time range
        $this->filteredServiceQueryModifierForTimeRange('updated_at', 'updated_at', $contractsQuery);

        //contracts based on asset ids
        $this->viewByContractAttachedRelationsQuery('asset_ids', 'sd_assets.id', 'attachAsset', $contractsQuery);

        //contracts based on notify agent ids
        $this->viewByContractAttachedRelationsQuery('notify_agent_ids', 'users.id', 'notifyAgents', $contractsQuery);

        //contracts with cost range
        $this->filteredContractQueryModifierForValueRange('cost_begin', 'cost_end', 'cost', $contractsQuery);

        //contracts with notify in days range
        $this->filteredContractQueryModifierForValueRange('notify_in_days_begin', 'notify_in_days_end', 'notify_before', $contractsQuery);

        //contracts based on user ids
        $this->viewByContractsAttachedRelationsQuery('user_ids', 'user_id', 'attachUser', $contractsQuery);

        return $contractsQuery;
    }

    /**
    * check for the passed fieldName in request and appends it query to assetsQuery from DB
    * NOTE: it is just a helper method for  filteredAssets method and should not be used by other methods
    * @param $fieldNameInRequest string => field name in the request coming from front end
    * @param $fieldNameInDB string => field name in the db by which we query
    * @param &$assetsQuery => it is the base query to which search queries has to be appended.
    *                       This is passed by reference, so at the end of the method it gets updated
    * @return object => query
    */
    private function viewByContractsAttachedRelationsQuery($fieldNameInRequest, $fieldNameInDB, $fieldRelation, &$contractsQuery)
    {
        if ($this->request->has($fieldNameInRequest)) {
          $queryIds = (array)$this->request->input($fieldNameInRequest);
          $contractsQuery = $contractsQuery->WhereHas($fieldRelation, function($q) use($fieldNameInDB, $queryIds) {
                                $q->whereIn($fieldNameInDB, $queryIds);
                              });
        }
    }

    /**
    * check for the passed fieldName in request and appends it query to contractsQuery from DB
    * NOTE: it is just a helper method for filteredContracts method and should not be used by other methods
    * @param $fieldNameInRequest string => field name in the request coming from front end
    * @param $fieldNameInDB string => field name in the db by which we query
    * @param &$contractsQuery => it is the base query to which search queries has to be appended.
    * This is passed by reference, so at the end of the method it gets updated
    * @return object => query
    */
    private function viewByFieldValueTypeContractQuery($fieldNameInRequest, $fieldNameInDB, &$contractsQuery)
    {
        if ($this->request->has($fieldNameInRequest)) {
            $queryIds = (array)$this->request->input($fieldNameInRequest);
            $contractsQuery = $contractsQuery->whereIn($fieldNameInDB, $queryIds);
        }
    }

    /**
     * check for the passed fieldName in request and appends it query to contractsQuery from DB
     * NOTE: it is just a helper method for  filteredContracts method and should not be used by other methods
     * @param $fieldNameInRequest string => field name in the request coming from front end
     * @param $fieldNameInDB string => field name in the db by which we query
     * @param &$contractsQuery => it is the base query to which search queries has to be appended.
     *                       This is passed by reference, so at the end of the method it gets updated
     * @return object => query
     */
    private function viewByContractAttachedRelationsQuery($fieldNameInRequest, $fieldNameInDB, $fieldRelation, &$contractsQuery)
    {
        if ($this->request->has($fieldNameInRequest)) {
            $queryIds = (array)$this->request->input($fieldNameInRequest);
            $contractsQuery = $contractsQuery->whereHas($fieldRelation, function($query) use($fieldNameInDB, $queryIds) {
                            $query->whereIn($fieldNameInDB, $queryIds);
                          });
        }
    }

    /**
    * check for the passed fieldName in request and appends it query to contractsQuery from DB
    * NOTE: it is just a helper method for filteredContracts method and should not be used by other methods
    * NOTE: All datetime passed are assumed to be in agent's timezone
    *
    * @param string $startTimeNameInRequest The name of start_time of a field in request
    * @param string $endTimeNameInRequest The name of end_time of a field in request
    * @param string $fieldNameInDB Field name in the db by which we query
    * @param string $contractsQuery It is the base query to which search queries has to be appended.
    * This is passed by reference, so at the end of the method it gets updated
    * @return object Query after filtering in timesatmp range
    */
    private function filteredContractQueryModifierForValueRange($startValueNameInRequest, $endValueNameInRequest, $fieldNameInDB, &$contractsQuery)
    {
        $endValue = $this->request->input($endValueNameInRequest);
        $startValue = $this->request->input($startValueNameInRequest);

        if ($endValue || $startValue) {
            $startValue = $startValue ?: 1;
            $endValue = $endValue ?: (SdContract::max($fieldNameInDB) ?: $startValue + 1);
            $contractsQuery = $contractsQuery->where([[$fieldNameInDB, '<=', $endValue],[$fieldNameInDB, '>=', $startValue]]);
        }
    }

    /**
    * Gets general search query. (this will only be used by 'baseQueryForContracts' method)
    * @param QueryBuilder $baseQuery base query
    * @param string $searchString string which has to be searched
    * @return QueryBuilder
    */
    private function generalSearchQuery(QueryBuilder $baseQuery, string $searchString) : QueryBuilder
    {
        $contractsQuery = $baseQuery->where(function($parentQuery) use ($searchString) {
            $parentQuery
                ->where('name', 'LIKE', "%$searchString%")
                ->orWhere('cost', 'LIKE', "%$searchString%")
                ->orWhere('contract_end_date', 'LIKE', "%$searchString%")
                ->orWhere('identifier', 'LIKE', "%$searchString%")
                ->orWhereHas('contractType', function($query) use($searchString) {
                    $query->where('name', 'LIKE', "%$searchString%");
                })
                ->orWhereHas('vendor', function($query) use($searchString) {
                    $query->where('name', 'LIKE', "%$searchString%");
                })
                ->orWhereHas('licence', function($query) use($searchString) {
                    $query->where('name', 'LIKE', "%$searchString%");
                })
                ->orWhereHas('contractStatus', function($query) use($searchString) {
                    $query->where('name', 'LIKE', "%$searchString%");
                })
                ->orWhereHas('contractRenewalStatus', function($query) use($searchString) {
                    $query->where('name', 'LIKE', "%$searchString%");
                })
                ->orWhereHas('attachAsset', function($query) use($searchString) {
                    $query->where('name', 'LIKE', "%$searchString%");
                });
            });

        // based user relation , user could be searched
        $this->userSearchQuery($contractsQuery, 'approverRelation', $searchString);
        $this->userSearchQuery($contractsQuery, 'owner', $searchString);
        $this->userSearchQuery($contractsQuery, 'notifyAgents', $searchString);

        return $contractsQuery;
    } 

}

