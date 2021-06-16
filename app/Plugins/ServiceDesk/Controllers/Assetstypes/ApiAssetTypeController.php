<?php

namespace App\Plugins\ServiceDesk\Controllers\Assetstypes;

use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use App\Plugins\ServiceDesk\Model\Assets\SdAssettypes;
use App\Plugins\ServiceDesk\Request\AssetType\CreateUpdateAssetTypeRequest;
use Illuminate\Http\Request;
use App\Plugins\ServiceDesk\Model\Common\SdDefault;

/**
 * ApiAssetTypeController is updated version of AssetstypesController
 * 
 */
class ApiAssetTypeController extends BaseServiceDeskController
{

    public function __construct() {
        $this->middleware('role.admin');
    }

    /**
     * method to create and update asset type
     * @param CreateUpdateAssetTypeRequest $request
     * @return Response
     */
    public function createUpdateAssetType(CreateUpdateAssetTypeRequest $request)
    {
        $assetType = $request->toArray();

        $assetTypeId = SdAssettypes::updateOrCreate(['id' => $request->id], $assetType)->id;

        if ($request->is_default == 'true') {
            SdDefault::first()->update(['asset_type_id' => $assetTypeId]);
        }

        return successResponse(trans('ServiceDesk::lang.asset_type_saved_successfully'));
    }

    /**
     * method to get asset type
     * @param SdAssettypes $assetType
     * @return Response
     */
    public function getAssetType(SdAssettypes $assetType)
    {
        $assetType = $assetType->fresh(['parent:id,name']);
        unset($assetType->parent_id, $assetType->created_at, $assetType->updated_at);

        return successResponse('', ['asset_type' => $assetType]);
    
    }

    /**
     * method to get asset type list
     * @param Request $request
     * @return Response
     */
    public function getAssetTypeList(Request $request)
    {
        $searchString = $request->input('search-query') ? $request->input('search-query') : '';
        $limit = $request->input('limit') ?: 10;
        $sortField = $request->input('sort-field') ? $request->input('sort-field') : 'updated_at';
        $sortOrder = $request->input('sort-order') ? $request->input('sort-order') : 'desc';
        $assetTypes = SdAssettypes::with('parent:id,name')
            ->where('name', 'LIKE', "%$searchString%")
             ->orWhereHas('parent', function($query) use($searchString) {
                $query->where('name', 'LIKE', "%$searchString%");
            })
            ->orderBy($sortField, $sortOrder)
            ->select('id', 'name', 'parent_id', 'created_at')
            ->paginate($limit)
            ->toArray();
        $assetTypes['asset_types'] = $assetTypes['data'];
        unset($assetTypes['data']);
        
        return successResponse('', $assetTypes);
    
    }

    /**
     * method to delete asset type
     * @param SdAssettypes $assetType
     * @return Response
     */
    public function deleteAssetType(SdAssettypes $assetType)
    {
        $assetType->delete();

        return successResponse(trans('ServiceDesk::lang.asset_type_deleted_successfully'));
    }

}
