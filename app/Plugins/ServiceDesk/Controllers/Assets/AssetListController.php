<?php

namespace App\Plugins\ServiceDesk\Controllers\Assets;

use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\Plugins\ServiceDesk\Model\Releases\SdReleases;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Carbon;
use DB;
use App\Plugins\ServiceDesk\Model\Report\SdReportFilter;
use App\Plugins\ServiceDesk\Model\Report\SdReportFilterMeta;
use Cache;
use App\Model\helpdesk\Form\FormField;
use App\Plugins\ServiceDesk\Traits\BaseFilterList;

/**
 * Handles asset list view by filtering/searching/arranging assets
 * USAGE :
 * Request can have following parameters:
 * 
 *               NAME          |        Possible values
 * search_query (string, optional) : any string that has to be searched in assets in the current asset type
 * sort_order (string, optional) : asc/desc ( ascending/descending ) for sorting. By default its value is 'desc'
 * sort_field (string, optional) : The field that is required to be sorted. By default its value is 'updated_at'
 * limit (string, optional) : Number of assets that are reuired to be displayed on a partiular page. By default its value is 10
 * page (string, optional) : current page in the asset list.By default its value is 1
 * config : to get all the asset fields by setting config to true
 * 
 * 
 * Extra Parameters for Asset Reports
 * count (boolean, optional) : get associated assets count categorized by asset type
 * filter_id (int, optional) : get associated assets data based on saved filter in asset report
 * config (boolean, optional) : get all fields of asset
 * column (boolean, optional) : get all column list for asset except description (reason front end issue)
 * report (boolean, optional) : get formatted data for report (consists of columns and asset data)
 *                              report_data parameter is managed internally by setting only report parameter
 *                              *** while setting report even filter_id paramter to be set to valid filter_id
 *                              wrong filter_id or not setting filter_id parameter will lead to defualt asset-list api response
 *
 * 
 * ADVANCED SEARCH FILTER
 * asset_ids (array, optional)
 * asset_type_ids (array, optional) 
 * used_by_ids (array, optional)
 * managed_by_ids (array, optional)
 * location_ids(array, optional)
 * dept_ids (array, optional)
 * org_ids (array, optional)
 * product_ids (array, optional)
 * impact_type_ids (array, optional)
 * ticket_ids(array, optional)
 * change_ids (array, optional)
 * problem_ids (array, optional)
 * release_ids(array, optional)
 * contract_ids(array, optional)
 * custom_formFieldId (string, optional)   formFieldId could be 1, 2, 3, etc
 * assigned_on (datetime in agent's timezone in faveo date format, optional)
 * created_at (datetime in agent's timezone in faveo date format, optional)
 * updated_at (datetime in agent's timezone in faveo date format, optional)
 * asset_status_ids (array, optional) 
 *
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */

class AssetListController extends BaseServiceDeskController
{
  use BaseFilterList;
  
  public function __construct()
  {
    $this->middleware('auth');
  }

  protected $request;

  /**
   * Gets asset-list depending upon which parameter will be passed
   * @return array => success response with filtered assets
  */
  public function getAssetList(Request $request)
  {
    $this->request = $request;

    if ($request->column) {
      $assetColumnsList = $this->assetColumnsList();
      return successResponse('', $assetColumnsList); 
    }

    $searchString = $limit = $sortField = $sortOrder = $baseQueryWithoutSearch = $baseQuery = $formattedAssets = $assets = null;

    $this->manageColumnReportAndFilterParameters();
    $this->manageParametersIntitializationAndBasicQuery($searchString, $limit, $sortField, $sortOrder, $baseQueryWithoutSearch, $baseQuery);
    $this->manageAssetFormattingParameters($searchString, $limit, $sortField, $sortOrder, $baseQuery, $formattedAssets, $assets);

    return successResponse('',$formattedAssets); 

  }

  /**
   * Takes asset's base query and appends to it search query according to whether that search parameter is present in the 
   * request or not
   * @param $assetsQuery
   * @return object => query
  */
  private function filteredAssets(QueryBuilder $assetsQuery) : QueryBuilder
  {
    //assets based on asset ids
    $this->viewByFieldValueTypeAssetQuery('asset_ids', 'sd_assets.id', $assetsQuery);

    //assets based on asset type ids
    $this->viewByFieldValueTypeAssetQuery('asset_type_ids', 'asset_type_id', $assetsQuery);

    // assets based on used by ids
    $this->viewByFieldValueTypeAssetQuery('used_by_ids', 'used_by_id', $assetsQuery);

    //assets based on managed by ids
    $this->viewByFieldValueTypeAssetQuery('managed_by_ids', 'managed_by_id', $assetsQuery);

    //assets based on organization ids
    $this->viewByFieldValueTypeAssetQuery('org_ids', 'organization_id', $assetsQuery);

    //assets based on department ids
    $this->viewByFieldValueTypeAssetQuery('dept_ids', 'department_id', $assetsQuery);

    //assets based on location ids
    $this->viewByFieldValueTypeAssetQuery('location_ids', 'location_id', $assetsQuery);

    //assets based on product ids
    $this->viewByFieldValueTypeAssetQuery('product_ids', 'product_id', $assetsQuery);

    //assets based on impact type ids
    $this->viewByFieldValueTypeAssetQuery('impact_type_ids', 'impact_type_id', $assetsQuery);

    //assets based on ticket ids
    $this->viewByAssetAttachedRelationsQuery('ticket_ids', 'tickets.id', 'tickets', $assetsQuery);

    //assets based on change ids
    $this->viewByAssetAttachedRelationsQuery('change_ids', 'sd_changes.id', 'changes', $assetsQuery);

    //assets based on release ids
    $this->viewByAssetAttachedRelationsQuery('release_ids', 'sd_releases.id', 'releases', $assetsQuery);

    //assets based on problem ids
    $this->viewByAssetAttachedRelationsQuery('problem_ids', 'sd_problem.id', 'problems', $assetsQuery);

    //assets based on contract ids
    $this->viewByAssetAttachedRelationsQuery('contract_ids', 'sd_contracts.id', 'contracts', $assetsQuery);

    // assets with assigned_on in a given time range
    $this->filteredServiceQueryModifierForTimeRange('assigned_on', 'assigned_on', $assetsQuery);

    // assets with created_at in a given time range
    $this->filteredServiceQueryModifierForTimeRange('created_at', 'sd_assets.created_at', $assetsQuery);

    // assets with updated_at in a given time range
    $this->filteredServiceQueryModifierForTimeRange('updated_at', 'sd_assets.updated_at', $assetsQuery);

    //assets based on asset status ids
    $this->viewByFieldValueTypeAssetQuery('asset_status_ids', 'status_id', $assetsQuery);

    // search assets based on custom field value
    $this->searchByCustomField($assetsQuery);

    //excluding assets attached to problem based on problem id
    $this->viewByExcludingAssetAttachedRelationsQuery('problem_id', 'sd_problem', 'problems', $assetsQuery);

    //assets based on release ids
    $this->viewByAssetAttachedRelationsQuery('release_ids', 'sd_releases.id', 'releases', $assetsQuery);

    //excluding assets attached to release based on release id
    $this->viewByExcludingAssetAttachedRelationsQuery('release_id', 'sd_releases', 'releases', $assetsQuery);

    //assets based on contract ids
    $this->viewByAssetAttachedRelationsQuery('contract_ids', 'sd_contracts.id', 'contracts', $assetsQuery);

    //excluding assets attached to contarct based on contract id
    $this->viewByExcludingAssetAttachedRelationsQuery('contract_id', 'sd_contracts', 'contracts', $assetsQuery);

    return $assetsQuery;
  }


  /**
   * helper method for filtering assets which are not associated with other module (problem)
   *
   * @param $fieldNameInRequest string => field name in the request coming from front end
   * @param $typeValue string => type of model to which asset is attached
   * @param $fieldRelation string => relation between assets and model
   * @param &$assetsQuery => it is the base query to which search queries has to be appended.
   *                       This is passed by reference, so at the end of the method it gets updated
   * @return object => query
   */
  private function viewByExcludingAssetAttachedRelationsQuery($fieldNameInRequest, $typeValue, $fieldRelation, &$assetsQuery)
  {
      $typeId = $this->request->input($fieldNameInRequest);
      if ($this->request->has($fieldNameInRequest)) {
          $assetsQuery = $assetsQuery->whereDoesntHave($fieldRelation, function($q) use($typeValue,$typeId) {
                                  $q->where([['type_id', $typeId], ['type', $typeValue]]);
                                });
      }
  }

  /**
   * method to list assets based on custom field value
   * @param SdAssets $assetsQuery it is the base query to which search queries has to be appended.
   * this is passed by reference, so at the end of the method it gets updated
   * @return null
   */
  private function searchByCustomField(&$assetsQuery)
  {
    // loop over all requests and append query for custom fields
    foreach ($this->request->all() as $key => $value) {
      if (strpos($key, 'custom_') !== false && $value) {
        $formFieldId = str_replace('custom_','', $key);

        // check if value has a comma and field is a check
        // if it is a checkbox, convert passed string into array
        if(FormField::whereId($formFieldId)->value('type') == 'checkbox'){
          // exploding the values followed by a trim
          $value = array_map('trim', explode(',', $value));
        }
        $value = json_encode($value);

        // alias has to be unique while using joins. Using key will make alias unique for
        // multiple custom fields
        // in get() or select query 'sd_assets.*' is used to get id of sd_assets table only not of custom_form_value
        // while combining assets and custom field filter, if of custom_form_value field will come
        $assetsQuery = $assetsQuery
          ->leftJoin("custom_form_value as $key", "$key.custom_id", '=', 'sd_assets.id')
          ->where("$key.custom_type", 'App\Plugins\ServiceDesk\Model\Assets\SdAssets')
          ->where("$key.form_field_id", $formFieldId)
          ->where("$key.value", $value);
      }
    }
  }

  /**
   * check for the passed fieldName in request and appends it query to assetsQuery from DB
   * NOTE: it is just a helper method for  filteredassets method and should not be used by other methods
   * @param $fieldNameInRequest string => field name in the request coming from front end
   * @param $fieldNameInDB string => field name in the db by which we query
   * @param &$assetsQuery => it is the base query to which search queries has to be appended.
   *                       This is passed by reference, so at the end of the method it gets updated
   * @return object => query
  */
  private function viewByFieldValueTypeAssetQuery($fieldNameInRequest, $fieldNameInDB, &$assetsQuery)
  {
    if ($this->request->has($fieldNameInRequest)) {
      $queryIds = (array)$this->request->input($fieldNameInRequest);
      $assetsQuery = $assetsQuery->whereIn($fieldNameInDB, $queryIds);
    }
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
  private function viewByAssetAttachedRelationsQuery($fieldNameInRequest, $fieldNameInDB, $fieldRelation, &$assetsQuery)
  {
    if ($this->request->has($fieldNameInRequest)) {
      $queryIds = (array)$this->request->input($fieldNameInRequest);
      $assetsQuery = $assetsQuery->WhereHas($fieldRelation, function($q) use($fieldNameInDB, $queryIds) {
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
    $assetsQuery = $baseQuery->where(function($q) use ($searchString) {
              $q
                ->where('identifier', 'LIKE', "%$searchString%")
                ->orWhere('name', 'LIKE', "%$searchString%")
                ->orWhere('assigned_on', 'LIKE', "%$searchString%")
                ->orwhereHas('department', function($query) use ($searchString) {
                    $query->where('name', 'LIKE', "%$searchString%");
                })
                ->orWhereHas('impactType', function($query) use($searchString) {
                    $query->where('name', 'LIKE', "%$searchString%");
                })
                ->orwhereHas('organization', function($query) use ($searchString) {
                    $query->where('name', 'LIKE', "%$searchString%");
                })
                ->orWhereHas('product', function($query) use($searchString) {
                    $query->where('name', 'LIKE', "%$searchString%");
                })
                ->orWhereHas('assetType', function($query) use($searchString) {
                    $query->where('name', 'LIKE', "%$searchString%");
                })
                ->orWhereHas('location', function($query) use($searchString) {
                    $query->where('title', 'LIKE', "%$searchString%");
                })
                ->orWhereHas('tickets', function($query) use($searchString) {
                    $query->where('ticket_number', 'LIKE', "%$searchString%");
                })
                ->orwhereHas('changes', function($query) use ($searchString) {
                    $query
                          ->where('subject', 'LIKE', "%$searchString%");
                })
                ->orWhereHas('assetStatus', function($query) use ($searchString) {
                    $query->where('name', 'LIKE', "%$searchString%");
                });
        });

    // based on user relation , user could be searched
    $this->userSearchQuery($assetsQuery, 'usedBy', $searchString);
    $this->userSearchQuery($assetsQuery, 'managedBy', $searchString);

    return $assetsQuery;
  }

  /**
   * method to populate baseQuery which will include all the fields especially for reports
   * @return $filteredAssets
  */
  private function baseQueryForAllFieldsAssets() : QueryBuilder
  {
    $assetsQuery = SdAssets::with([
      'usedBy:id,first_name,last_name,email',
      'managedBy:id,first_name,last_name,email',
      'organization:id,name',
      'product:id,name',
      'department:id,name',
      'assetType:id,name',
      'impactType:id,name',
      'location:id,title as name',
      'contracts:sd_contracts.id,name',
      'problems:sd_problem.id',
      'changes:sd_changes.id',
      'releases:sd_releases.id',
      'tickets:tickets.id,ticket_number',
      'assetStatus:id,name'
    ]);

    //if search filter is required
    $filteredAssets = $this->filteredAssets($assetsQuery);
    return $filteredAssets;
  }

  /** 
   * method to format all fields of assets especially for reports
   * @param $assets
   * @return $assets
  */
  private function formatAllFieldsAssets($assets)
  {
    
    if (array_key_exists('data', $assets)) {
      $assets['assets'] = [];
      foreach ($assets['data'] as &$asset) {
        $this->formatBasicAssetFields($asset, $inventory);
        $inventory['asset_type'] = $asset['asset_type'];
        $inventory['product'] = $asset['product'];
        $inventory['used_by'] = $asset['used_by'];
        $inventory['managed_by'] = $asset['managed_by'];
        $inventory['organization'] = $asset['organization'];
        $inventory['assigned_on'] = $asset['assigned_on'];
        $inventory['department'] = $asset['department'];
        $inventory['location'] = $asset['location'];
        $inventory['impact_type'] = $asset['impact_type'];
        $inventory['asset_status'] = $asset['asset_status'];
        $inventory['contract'] = ($asset['contracts']) ? reset($asset['contracts']) : [];
        $this->formatAssociatedFieldsCount($asset, $inventory);
        array_push($assets['assets'], $inventory);
      }
      unset($assets['data']);
    } 

    return $assets;
  }

  /**
   * method to format basic asset fields
   * @param array $asset
   * @param array $inventory
   * @return 
   */
  private function formatBasicAssetFields($asset, &$inventory)
  {
    // issues in front end to display id (shakti), so key change
    $id = ($this->request->config) ? 'asset_id' :'id';
    $inventory[$id] = $asset['id'];
    $inventory['name'] = $asset['name'];
    $inventory['description'] = $asset['description'];
    $inventory['created_at'] = $asset['created_at'];
    $inventory['updated_at'] = $asset['updated_at'];
    $inventory['identifier'] = $asset['identifier'];
  }

  /**
   * method to format associated fields count
   * @param array $asset
   * @param array $inventory
   * @return 
   */
  private function formatAssociatedFieldsCount($asset, &$inventory)
  {
    $inventory['problems_count'] = count($asset['problems']);
    $inventory['changes_count'] = count($asset['changes']);
    $inventory['releases_count'] = count($asset['releases']);
    $inventory['contract_count'] = (int) isset($asset['contracts']);
    $inventory['tickets_count'] = count($asset['tickets']);
  }

  /** 
   * method to format assets for asset count specially for reports graph
   * @param $assets
   * @return $assets
   */
  private function formatAssetsForCount($assets)
  {
    foreach ($assets as &$asset) {
      $asset = ['id' => $asset['asset_type']['id'], 'name' => $asset['asset_type']['name'], 'count' => $asset['count']];
    }

    return ['assets' => $assets];
  }

  /**
   * method to get asset list by filter id
   * @param  int  $filterId
   * @return response
   */
  private function getAssetListByFilterId(int $filterId)
  {
    $filter = SdReportFilter::find($filterId);
    if (is_null($filter)) {
      return errorResponse(trans('ServiceDesk::lang.report_filter_not_found'), 404);
    }
    $parameters = SdReportFilter::getFilterParametersByFilterId($filterId);
    $parameters['config'] = true;

    if ($this->request->report) {
      $parameters['report_data'] = true;
      $parameters['filter_id'] = $filterId;
    }
    $this->request = $this->request ?: new Request;

    $this->request->replace($parameters);

    return $this->getAssetList($this->request);
  }

  /**
   * method to get asset and it's associated columns list
   * @return array $assetColumnsList
   */
  private function assetColumnsList()
  {
    $assetColumnsList['asset_columns'] = [];
    $this->assetColumnListArray($assetColumnsList);

    if (Cache::has('report_column_keys'.$this->request->filter_id)) {
      $this->fetchAssetColumnValues($assetColumnsList);
    }
    
    return $assetColumnsList;
  }

  /**
   * method to make asset column list array
   * @param array $assetColumnsList
   * @return 
   */
  private function assetColumnListArray(&$assetColumnsList)
  {
    $assetColumnsList['asset_columns'] = [
      ['key' => 'asset_id', 'value' => 'Asset Id', 'display' => false],
      ['key' => 'name', 'value' => 'Name', 'display' => true],
      ['key' => 'identifier', 'value' => 'Identifier', 'display' => true],
      ['key' => 'organization', 'value' => 'Organization', 'display' => false],
      ['key' => 'department', 'value' => 'Department', 'display' => false],
      ['key' => 'asset_type', 'value' => 'Asset Type', 'display' => true],
      ['key' => 'location', 'value' => 'Location', 'display' => false],
      ['key' => 'assigned_on', 'value' => 'Assigned On', 'display' => false],
      ['key' => 'product', 'value' => 'Product', 'display' => false],
      ['key' => 'used_by', 'value' => 'Used By', 'display' => true],
      ['key' => 'managed_by', 'value' => 'Managed By', 'display' => true],
      ['key' => 'impact_type', 'value' => 'Impact Type', 'display' => false],
      ['key' => 'created_at', 'value' => 'Created At', 'display' => true],
      ['key' => 'updated_at', 'value' => 'Updated At', 'display' => true],
      ['key' => 'problems_count', 'value' => 'Associated Problems', 'display' => false],
      ['key' => 'changes_count', 'value' => 'Associated Changes', 'display' => false],
      ['key' => 'releases_count', 'value' => 'Associated Releases', 'display' => false],
      ['key' => 'contract_count', 'value' => 'Asociated Contract', 'display' => false],
      ['key' => 'tickets_count', 'value' => 'Associated Tickets', 'display' => false],
      ['key' => 'asset_status', 'value' => 'Asset Status', 'display' => true]
    ];

  }

  /**
   * method to fetch asset column values
   * @param array $assetColumnsList
   * @return 
   */
  private function fetchAssetColumnValues(&$assetColumnsList)
  {
    $columnKeys = Cache::get('report_column_keys'.$this->request->filter_id);

    foreach ($assetColumnsList['asset_columns'] as &$assetColumn) {
      if(array_search($assetColumn['key'], $columnKeys) === false)
      {
        $assetColumn['display'] = false;
        continue;
      }
      $assetColumn['display'] = true;
    }
  }

  /** 
   * method to get Asset List for Report
   * @return response
   */
  private function getAssetListForReport()
  {
    $filterId = !is_numeric($this->request->filter_id) ? 0 : $this->request->filter_id;

    $reportData = $this->getAssetListByFilterId($filterId);

    return $reportData;
  }

  /** 
   * method to format all fields of assets especially for reports
   * @param array $assets
   * @return array $reportData (formatted report with column and data)
   */
  private function formatAllFieldsAssetsForReport($assets)
  {
    $assetsArray['assets'] = [];
    if ($assets) {
      foreach ($assets as &$asset) {
        $this->formatBasicAssetFields($asset, $inventory);
        $inventory['organization'] = $asset['organization'] ? $asset['organization']['name'] : '';
        $inventory['department'] = $asset['department'] ? $asset['department']['name'] : '';
        $inventory['asset_type'] = $asset['asset_type'] ? $asset['asset_type']['name'] : '';
        $inventory['location'] = $asset['location'] ? $asset['location']['name'] : '';
        $inventory['product'] = $asset['product'] ? $asset['product']['name'] : '';
        $inventory['used_by'] = $asset['used_by'] ? $asset['used_by']['full_name'] : '';
        $inventory['managed_by'] = $asset['managed_by'] ? $asset['managed_by']['full_name'] : '';
        $inventory['impact_type'] = $asset['impact_type'] ? $asset['impact_type']['name'] : '';
        $inventory['assigned_on'] = $asset['assigned_on'] ?: '';
        $inventory['asset_status'] = $asset['asset_status'] ? $asset['asset_status']['name'] : '';
        $this->formatAssociatedFieldsCount($asset, $inventory);
        array_push($assetsArray['assets'], $inventory);
      }
    }

    $reportData = [];
    $this->formatAssetArrayForReport($reportData, $assetsArray);

    return $reportData;
  }

  /**
   * method to format asset array for report
   * @param array $reportData
   * @param array $assetsArray
   * @return 
   */
  private function formatAssetArrayForReport(&$reportData, $assetsArray)
  {
    $assetColumnsList = $this->assetColumnsList();

    $reportData['column_list'] = [];
    foreach ($assetColumnsList['asset_columns'] as $assetColumnListKey => &$assetColumnListValue) {
      if ($assetColumnListValue['display'] === false) {
        unset($assetColumnsList['asset_columns'][$assetColumnListKey]);
        continue;
      }
      $reportData['column_list'] = array_merge($reportData['column_list'], [$assetColumnsList['asset_columns'][$assetColumnListKey]['key'] => $assetColumnsList['asset_columns'][$assetColumnListKey]['value']]);
    }
    $reportData['report_data'] = [];
    foreach ($assetsArray['assets'] as &$asset) {
      $assetData = [];
      foreach ($assetColumnsList['asset_columns'] as $assetColumn) {
        $assetData = array_merge($assetData, [$assetColumn['key'] => $asset[$assetColumn['key']]]);
      }
      array_push($reportData['report_data'], $assetData);
    }
  }

  /**
   * method to manage column, report and filter parameters for asset report
   * @return response
   */
  private function manageColumnReportAndFilterParameters()
  {
    if ($this->request->report) {
      return $this->getAssetListForReport();
    }
    if (isset($this->request->filter_id) && is_null($this->request->column) && is_null($this->request->report_data)) {
      return $this->getAssetListByFilterId($this->request->filter_id);
    }

  }

  /**
   * method to perform actual initialization for basic variables, initially it is initialized to null
   * @param null $searchString
   * @param null $limit
   * @param null $sortField
   * @param null $sortOrder
   * @param null $baseQueryWithoutSearch
   * @param null $baseQuery
   * @return
   */
  private function manageParametersIntitializationAndBasicQuery(&$searchString, &$limit, &$sortField, &$sortOrder, &$baseQueryWithoutSearch, &$baseQuery)
  {
    $searchString = $this->request->input('search-query') ?: '';
    $limit = $this->request->input('limit') ?: 10;
    $sortField = $this->request->input('sort-field') ?: 'updated_at';
    // front end issues with id
    $sortField = $sortField === 'asset_id' ? 'id' : $sortField;
    $sortOrder = $this->request->input('sort-order') ?: 'desc'; 
    $baseQueryWithoutSearch = $this->baseQueryForAllFieldsAssets();
    $baseQuery = $searchString ? $this->generalSearchQuery($baseQueryWithoutSearch, $searchString) : $baseQueryWithoutSearch;
  }

  /**
   * NOTE :method need to improved to handle report data in parts (Krishna V.)
   * method to manage asset formatting parameters
   * @param string $searchString
   * @param int $limit
   * @param string $sortField
   * @param string $sortOrder
   * @param QueryBuilder $baseQueryWithoutSearch
   * @param QueryBuilder $baseQuery
   * @return
   */
  private function manageAssetFormattingParameters($searchString, $limit, $sortField, $sortOrder, $baseQuery, &$formattedAssets, &$assets)
  {
    $baseQuery = $baseQuery->orderBy("sd_assets.$sortField", $sortOrder);
    if ($this->request->report_data) {
      $assets = $baseQuery->get(['sd_assets.*']);
      $formattedAssets = $this->formatAllFieldsAssetsForReport($assets->toArray());
    } else if ($this->request->count) {
      $assets = $this->baseQueryForAllFieldsAssets()->select('asset_type_id', DB::raw('count(*) as count'))->groupBy('asset_type_id')->orderBy('asset_type_id', $sortOrder)->get(['sd_assets.*']);
      $formattedAssets = $this->formatAssetsForCount($assets->toArray());
    } else {
      $assets = $baseQuery->select('sd_assets.*')->paginate($limit);
      $formattedAssets = $this->formatAllFieldsAssets($assets->toArray());
    }

  }

}
 