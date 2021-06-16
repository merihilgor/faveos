<?php

namespace App\Plugins\ServiceDesk\Controllers\Changes;

use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use App\Plugins\ServiceDesk\Model\Changes\SdChanges;
use App\Plugins\ServiceDesk\Model\Changes\SdChangestatus;
use App\Plugins\ServiceDesk\Model\Changes\SdChangepriorities;
use App\Plugins\ServiceDesk\Model\Changes\SdChangetypes;
use App\Plugins\ServiceDesk\Model\Changes\SdImpacttypes;
use App\Location\Models\Location as SdLocations;
use App\Plugins\ServiceDesk\Requests\CreateChangesRequest;
use App\User;
use App\Plugins\ServiceDesk\Model\Cab\Cab;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use Exception;
use App\Plugins\ServiceDesk\Requests\CreateReleaseRequest;
use Illuminate\Http\Request;
use Lang;
use File;
use DB;
use App\Plugins\ServiceDesk\Model\Assets\CommonAssetRelation;
use App\Plugins\ServiceDesk\Model\Common\CommonTicketRelation;
use App\Plugins\ServiceDesk\Controllers\Library\UtilityController;
use App\Plugins\ServiceDesk\Policies\AgentPermissionPolicy;

class ChangesController extends BaseServiceDeskController {

    protected $agentPermission;

    public function __construct() {
        $this->middleware('auth');
        $this->agentPermission = new AgentPermissionPolicy();
    }

    public function changeshandleCreate(CreateChangesRequest $request, $attach = false) {     
        try {
           
            $sd_changes = new SdChanges;
            $sd_changes->fill($request->input())->save();
            UtilityController::attachment($sd_changes->id, 'sd_changes', $request->file('attachments'));

            if($request->has('asset')){
                 $assetId = $request->input('asset');
                 UtilityController::commonAssetAttach($assetId, $sd_changes->id, $type="sd_changes");
            }
            if ($attach == false) {
                return \Redirect::route('service-desk.changes.index')->with('message',Lang::get('ServiceDesk::lang.changes_created'));
            }
            return $sd_changes;
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

}