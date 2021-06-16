<?php

namespace App\Plugins\ServiceDesk\Controllers\AssetStatus;

use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use App\Plugins\ServiceDesk\Model\Assets\SdAssetStatus;
use App\Plugins\ServiceDesk\Request\AssetStatus\CreateUpdateAssetStatusRequest;
use Illuminate\Http\Request;
use Exception;

/**
 * ApiAssetStatusController to maintain CRUD functionality of sd_asset_statuses table
 *
 */
class ApiAssetStatusController extends BaseServiceDeskController {

    public function __construct() {
        $this->middleware('role.admin');
    }

    /**
     * method to create and update asset status
     * @param CreateUpdateAssetStatusRequest $request
     * @return Response
     */
    public function createUpdateAssetStatus(CreateUpdateAssetStatusRequest $request)
    {
        $assetStatus = $request->toArray();

        SdAssetStatus::updateOrCreate(['id' => $request->id], $assetStatus);

        return successResponse(trans('ServiceDesk::lang.asset_status_saved_successfully'));
    }

    /**
     * method to get asset status
     * @param SdAssetStatus $assetStatus
     * @return Response
     */
    public function getAssetStatus(SdAssetStatus $assetStatus)
    {
        return successResponse('', ['asset_status' => $assetStatus]);
    
    }

    /**
     * method to get asset status list
     * @param Request $request 
     * @return Response
     */
    public function getAssetStatusList(Request $request)
    {
        $searchString = $request->input('search-query') ? $request->input('search-query') : '';
        $limit = $request->input('limit') ? ((int)$request->input('limit') < 0 ? 0 : (int)$request->input('limit')) : 10;
        $sortField = $request->input('sort-field') ? $request->input('sort-field') : 'updated_at';
        $sortOrder = $request->input('sort-order') ? $request->input('sort-order') : 'desc';
        $assetStatuses = SdAssetStatus::where('name', 'LIKE', "%$searchString%")
            ->orderBy($sortField, $sortOrder)
            ->paginate($limit);
        
        return successResponse('', $assetStatuses);
    
    }

    /**
     * method to delete asset status
     * @param SdAssetStatus $assetStatus
     * @return Response
     */
    public function deleteAssetStatus(SdAssetStatus $assetStatus)
    {
        try
        {
            $assetStatus->delete();
            return successResponse(trans('ServiceDesk::lang.asset_status_deleted_successfully'));
        }
        catch(Exception $e)
        {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * method for asset status index blade page
     * @return view
     */
    public function assetStatusesIndexPage() 
    {
        return view('service::asset-status.index');
    }

    /**
     * method for asset status create blade page
     * @return view
     */
    public function assetStatusCreatePage() 
    {
        return view('service::asset-status.create');
    }

    /**
     * method for asset status edit blade page
     * @param SdAssetStatus $assetStatus
     * @return view
     */
    public function assetStatusEditPage(SdAssetStatus $assetStatus) 
    {
        return view('service::asset-status.edit', compact('assetStatus'));
    }

}
