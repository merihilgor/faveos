<?php

namespace App\Plugins\ServiceDesk\Controllers\Procurment;

use App\Plugins\ServiceDesk\Model\Procurment\SdProcurment;
use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use App\Plugins\ServiceDesk\Request\ProcurementMode\CreateUpdateProcurementModeRequest;
use Illuminate\Http\Request;
use App\Plugins\ServiceDesk\Model\Products\SdProducts;
use Exception;

/**
 * ApiProcurementModeController is updated version of ProcurmentController
 * 
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class ApiProcurementModeController extends BaseServiceDeskController
{

    public function __construct() {
        $this->middleware('role.admin');
    }

    /**
     * method to create and update procurement mode 
     * @param CreateUpdateProcurementModeRequest $request
     * @return Response
     */
    public function createUpdateProcurementMode(CreateUpdateProcurementModeRequest $request)
    {
        $procurementMode = $request->toArray();

        SdProcurment::updateOrCreate(['id' => $request->id], $procurementMode);

        return successResponse(trans('ServiceDesk::lang.procurement_mode_saved_successfully'));
    }

    /**
     * method to get procurement mode
     * @param $procurementModeId 
     * @return Response
     */
    public function getProcurementMode($procurementModeId)
    {
        $baseQuery = SdProcurment::where('id', $procurementModeId);

        if ($baseQuery->get()->isEmpty()) {
            return errorResponse(trans('ServiceDesk::lang.procurement_mode_not_found'));
        }

        $procurementMode = $baseQuery->first()->toArray();
        $procurementMode = ['procurement_mode' =>  $procurementMode];

        return successResponse('', $procurementMode);
    
    }

    /**
     * method to get procurement mode list
     * @param Request $request
     * @return Response
     */
    public function getProcurementModeList(Request $request)
    {
        $searchString = $request->input('search-query') ? $request->input('search-query') : '';
        $limit = $request->input('limit') ? ((int)$request->input('limit') < 0 ? 0 : (int)$request->input('limit')) : 10;
        $sortField = $request->input('sort-field') ? $request->input('sort-field') : 'updated_at';
        $sortOrder = $request->input('sort-order') ? $request->input('sort-order') : 'desc';
        $procurementModes = SdProcurment::where('name', 'LIKE', "%$searchString%")
            ->orderBy($sortField, $sortOrder)
            ->paginate($limit)
            ->toArray();
        $procurementModes['procurement_modes'] = $procurementModes['data'];
        unset($procurementModes['data']);
        
        return successResponse('', $procurementModes);
    
    }

    /**
     * method to delete procurement mode
     * @param SdProcurment $procurementMode
     * @return Response
     */
    public function deleteProcurementMode(SdProcurment $procurementMode)
    {
        try
        {
            $procurementMode->delete();
            return successResponse(trans('ServiceDesk::lang.procurement_mode_deleted_successfully'));
        }
        catch (Exception $e)
        {
            return errorResponse($e->getMessage());
        }

    }

    /**
     * method for procurement modes index blade page
     * @return view
     */
    public function procurementModesIndexPage() 
    {
        return view('service::procurment.index');
    }

    /**
     * method for procurement mode create blade page
     * @return view
     */
    public function procurementModeCreatePage() 
    {
        return view('service::procurment.create');
    }

    /**
     * method for procurement mode edit blade page
     * @param $procurementModeId
     * @return view
     */
    public function procurementModeEditPage($procurementModeId) 
    {
        return view('service::procurment.edit', compact('procurementModeId'));
    }

}
