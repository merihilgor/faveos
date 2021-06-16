<?php

namespace App\Plugins\ServiceDesk\Controllers\Contracttypes;

use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use App\Plugins\ServiceDesk\Model\Contract\ContractType;
use App\Plugins\ServiceDesk\Request\ContractType\CreateUpdateContractTypeRequest;
use Illuminate\Http\Request;
use App\Plugins\ServiceDesk\Requests\CreateContractstypesRequest;
use Exception;

/**
 * ApiContractTypeController is updated version of ContractTypeController
 * 
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class ApiContractTypeController extends BaseServiceDeskController {

    public function __construct() {
        $this->middleware('role.admin')->except(['handleCreate','createUpdateContractType']);
    }

    /**
     * Function to create and update contract type
     * @param CreateUpdateVendorRequest $request
     * @return Response
     */
    public function createUpdateContractType(CreateUpdateContractTypeRequest $request)
    {
        $contractType = $request->toArray();

        ContractType::updateOrCreate(['id' => $request->id], $contractType);

        return successResponse(trans('ServiceDesk::lang.contract_type_saved_successfully'));
    }

    /**
     * Function to get contract type
     * @param $contractTypeId 
     * @return Response
     */
    public function getContractType($contractTypeId)
    {
        $baseQuery = ContractType::where('id', $contractTypeId);

        if ($baseQuery->get()->isEmpty()) {
            return errorResponse(trans('ServiceDesk::lang.contract_type_not_found'));
        }

        $contractType = $baseQuery->first()->toArray();
        $contractType = ['contract_type' =>  $contractType];

        return successResponse('', $contractType);
    
    }

    /**
     * Function to get contract type list
     * @param $vendorId 
     * @return Response
     */
    public function getContractTypeList(Request $request)
    {
        $searchString = $request->input('search-query') ? $request->input('search-query') : '';
        $limit = $request->input('limit') ? ((int)$request->input('limit') < 0 ? 0 : (int)$request->input('limit')) : 10;
        $sortField = $request->input('sort-field') ? $request->input('sort-field') : 'updated_at';
        $sortOrder = $request->input('sort-order') ? $request->input('sort-order') : 'desc';
        $contractTypes = ContractType::where('name', 'LIKE', "%$searchString%")
            ->orderBy($sortField, $sortOrder)
            ->paginate($limit)
            ->toArray();
        $contractTypes['contract_types'] = $contractTypes['data'];
        unset($contractTypes['data']);
        
        return successResponse('', $contractTypes);
    
    }

    /**
     * Function to delete contract type
     * @param ContractType $contractType
     * @return Response
     */
    public function deleteContractType(ContractType $contractType)
    {   
        try
        {
            $contractType->delete();
            return successResponse(trans('ServiceDesk::lang.contract_type_deleted_successfully'));
        }
        catch(Exception $e)
        {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * method for contract types index blade page
     * @return view
     */
    public function contractTypesIndexPage() 
    {
        return view('service::contractstypes.index');
    }

    /**
     * method for contract type create blade page
     * @return view
     */
    public function contractTypeCreatePage() 
    {
        return view('service::contractstypes.create');
    }

    /**
     * method for contract type edit blade page
     * @param $contractTypeId
     * @return view
     */
    public function contractTypeEditPage($contractTypeId) 
    {
        return view('service::contractstypes.edit', compact('contractTypeId'));
    }

    /**
     * 
     * @param \App\Plugins\ServiceDesk\Requests\CreateContractstypesRequest $request
     * @return type
     */
    public function handleCreate(CreateContractstypesRequest $request) {
        try {
            ContractType::create(['name' => $request->name]);
    
            return (trans('ServiceDesk::lang.contract_type_created_successfully'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

}
