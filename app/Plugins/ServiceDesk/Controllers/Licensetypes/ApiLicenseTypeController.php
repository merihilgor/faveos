<?php
namespace App\Plugins\ServiceDesk\Controllers\Licensetypes;

use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use App\Plugins\ServiceDesk\Model\Contract\License;
use App\Plugins\ServiceDesk\Request\LicenseType\CreateUpdateLicenseTypeRequest;
use Illuminate\Http\Request;

/**
 * ApiLicenseTypeController is updated version of LicenseTypeController
 * 
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class ApiLicenseTypeController extends BaseServiceDeskController
{

    public function __construct() {
        $this->middleware('role.admin');
    }

    /**
     * method to create and update license type 
     * @param CreateUpdateLicenseTypeRequest $request
     * @return Response
     */
    public function createUpdateLicenseType(CreateUpdateLicenseTypeRequest $request)
    {
        $licenseType = $request->toArray();

        License::updateOrCreate(['id' => $request->id], $licenseType);

        return successResponse(trans('ServiceDesk::lang.license_type_saved_successfully'));
    }

    /**
     * method to get license type 
     * @param $licenseTypeId 
     * @return Response
     */
    public function getlicenseType($licenseTypeId)
    {
        $baseQuery = License::where('id', $licenseTypeId);

        if ($baseQuery->get()->isEmpty()) {
            return errorResponse(trans('ServiceDesk::lang.license_type_not_found'));
        }

        $licenseType = $baseQuery->first()->toArray();
        $licenseType = ['license_type' =>  $licenseType];

        return successResponse('', $licenseType);
    
    }

    /**
     * method to get license type  list
     * @param Request $request
     * @return Response
     */
    public function getlicenseTypeList(Request $request)
    {
        $searchString = $request->input('search-query') ? $request->input('search-query') : '';
        $limit = $request->input('limit') ? ((int)$request->input('limit') < 0 ? 0 : (int)$request->input('limit')) : 10;
        $sortField = $request->input('sort-field') ? $request->input('sort-field') : 'updated_at';
        $sortOrder = $request->input('sort-order') ? $request->input('sort-order') : 'desc';
        $licenseTypes = License::where('name', 'LIKE', "%$searchString%")
            ->orderBy($sortField, $sortOrder)
            ->paginate($limit)
            ->toArray();
        $licenseTypes['license_types'] = $licenseTypes['data'];
        unset($licenseTypes['data']);
        
        return successResponse('', $licenseTypes);
    
    }

    /**
     * method to delete license type 
     * @param $licenseTypeId
     * @return Response
     */
    public function deleteLicenseType($licenseTypeId)
    {
        $baseQuery = License::where('id', $licenseTypeId);

        if ($baseQuery->get()->isEmpty()) {
            return errorResponse(trans('ServiceDesk::lang.license_type_not_found'));
        }

        $baseQuery->first()->delete();

        return successResponse(trans('ServiceDesk::lang.license_type_deleted_successfully'));
    }

    /**
     * method for license type index blade page
     * @return view
     */
    public function licenseTypesIndexPage() 
    {
        return view('service::licensetypes.index');
    }

    /**
     * method for license type create blade page
     * @return view
     */
    public function licenseTypeCreatePage() 
    {
        return view('service::licensetypes.create');
    }

    /**
     * method for license type edit blade page
     * @param $licenseTypeId
     * @return view
     */
    public function licenseTypeEditPage($licenseTypeId) 
    {
        return view('service::licensetypes.edit', compact('licenseTypeId'));
    }

}
