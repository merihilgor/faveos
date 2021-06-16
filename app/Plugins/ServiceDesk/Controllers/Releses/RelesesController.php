<?php

namespace App\Plugins\ServiceDesk\Controllers\Releses;

use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use App\Plugins\ServiceDesk\Model\Releases\SdReleasestatus;
use App\Plugins\ServiceDesk\Model\Releases\SdReleasepriorities;
use App\Plugins\ServiceDesk\Model\Releases\SdReleasetypes;
use App\Location\Models\Location as SdLocations;
use App\Plugins\ServiceDesk\Model\Releases\SdReleases;
use App\Plugins\ServiceDesk\Requests\CreateReleaseRequest;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use Exception;
use Lang;
use DB;
use File;
use Illuminate\Http\Request;
use App\Plugins\ServiceDesk\Model\Assets\CommonAssetRelation;
use App\Plugins\ServiceDesk\Model\Common\CommonTicketRelation;
use App\Plugins\ServiceDesk\Controllers\Library\UtilityController;
use App\Plugins\ServiceDesk\Policies\AgentPermissionPolicy;

class RelesesController extends BaseServiceDeskController {

    protected $agentPermission;

    public function __construct() {
        $this->middleware('auth');
        $this->agentPermission = new AgentPermissionPolicy();
    }

    public function releasesindex() {
        try {
            if (!$this->agentPermission->releasesView()) {
            return redirect('dashboard')->with('fails', Lang::get('ServiceDesk::lang.permission-denied'));
            }
            $sdPolicy = $this->agentPermission;
            return view('service::releases.index', compact('sdPolicy'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @return type
     */
    public function getReleases() {
        try {
            $releses = new SdReleases();
            $relese = $releses->select('id', 'description', 'subject', 'planned_start_date', 'planned_end_date', 'status_id', 'priority_id', 'release_type_id', 'location_id')->get();
            // dd( $relese);
            return \DataTables::Collection($relese)
                        ->addColumn('subject', function($model) {
                                    return "<b>#REL-".$model->id."</b> &nbsp;&nbsp;<span title='".$model->subject."'>". str_limit($model->subject, 20)."</span>";
                            })

                        ->addColumn('planned_start_date', function ($model){
                        
                                $display = ($model->planned_start_date && $model->planned_start_date != "--" )?faveoDate($model->planned_start_date):'--';
                                return  $display;
                            })

                        ->addColumn('planned_end_date',function($model){
                                $display = ($model->planned_end_date && $model->planned_end_date != "--")?faveoDate($model->planned_end_date):'--';
                                return $display;
                            })

                        ->addColumn('Action', function($model) {
                                $url = url('service-desk/releases/' . $model->id . '/delete');
                                $delete = $this->agentPermission->releaseDelete() ? UtilityController::deletePopUp($model->id, $url, "Delete $model->subject") : '';
                                $edit =  $this->agentPermission->releaseEdit() ? "<a href=" . url('service-desk/releases/' . $model->id . '/edit') . " class='btn btn-primary btn-xs'><i class='fa fa-edit'>&nbsp;&nbsp;</i>".Lang::get('ServiceDesk::lang.edit')."</a>&nbsp; " : '';
                                $view = $this->agentPermission->releasesView() ? " <a href=" . url('service-desk/releases/' . $model->id . '/show') . " class='btn btn-primary btn-xs'><i class='fa fa-eye'>&nbsp;&nbsp;</i>".Lang::get('ServiceDesk::lang.view')."</a>" : '';
                                return $edit.$delete.$view;
                            })
                            ->rawColumns(['subject','Action','planned_start_date','planned_end_date'])
                            ->make();
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @param type $id
     * @return type
     * @throws \Exception
     */
    public function view($id) {
        try {
            if (!$this->agentPermission->releasesView()) {
            return redirect('dashboard')->with('fails', Lang::get('ServiceDesk::lang.permission-denied'));
            }
            $releases = new SdReleases();
            $release = $releases->find($id);
            $release->planned_start_date = $release->planned_start_date ?: '--';
            $release->planned_end_date = $release->planned_end_date ?: '--';
            $sdPolicy = $this->agentPermission;
            if ($release) {
                return view('service::releases.show', compact('release', 'sdPolicy'));
            } else {
                throw new \Exception('Sorry we can not find your request');
            }
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @param type $id
     * @return type
     */
    public function releasesHandledelete($id) {
        try {
            $sd_releases = SdReleases::findOrFail($id);
            $sd_releases->delete();
            $assetRelation = CommonAssetRelation::where('type', 'sd_releases')->where('type_id', $id)->delete();

            return \Redirect::route('service-desk.releases.index')->with('message', Lang::get('ServiceDesk::lang.release_deleted_successfully'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @param type $id
     * @param type $cabid
     */
    public function sendCab($id, $cabid) {
        $activity = 'sd_releases';
        $owner = "$activity:$id";
        $url = url("service-desk/cabs/vote/$cabid/$owner");
        UtilityController::cabMessage($cabid, $activity, $url);
    }

    /**
     * 
     * @param type $id
     * @return type
     * @throws Exception
     */
    public function complete($id) {
        try {
            $releases = new SdReleases();
            $release = $releases->find($id);
            if ($release) {
                $release->status_id = 5;
                $release->save();
            } else {
                throw new Exception('Sorry we can not find your request');
            }
            return redirect()->back()->with('success', Lang::get('ServiceDesk::lang.completed_successfully'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @param type $id
     * @param \App\Plugins\ServiceDesk\Controllers\Releses\Request $request
     * @return type
     */
    public function deleteUploadfile($id, Request $request) {
        try {

            $file = $request->filename;
            $attachment = DB::table('sd_attachments')->where('owner', '=', 'sd_releases:' . $id)->delete();
            File::delete(public_path('uploads/service-desk/attachments/' . $file));
            return Lang::get('lang.your_status_updated_successfully');

            // return redirect()->back()->with('success', 'Updated');
        } catch (\Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

     /**
     * 
     * @param Request $request
     * @return type
     */
    public function attachAssetToRelease(Request $request) {
      
        $request->validate([
                'asset' => 'required',
                'releaseid' => 'required'
            ]);
        try{
                $data = CommonAssetRelation::whereIn('asset_id', request('asset'))->where('type_id', request('releaseid'))
                        ->where('type', 'sd_releases')->get()->toArray();
            if(count($data)> 0){
                return redirect()->back()->with('fails',Lang::get('ServiceDesk::lang.please_remove_existing_asset_while_asset_selection'));
            }
                $releaseId = $request->input('releaseid');
            if($request->has('asset')){
                $assetIds = $request->input('asset');
                        UtilityController::commonCreateAssetAttach($assetIds, $releaseId, 'sd_releases');
            } 
                return redirect()->back()->with('success',Lang::get('ServiceDesk::lang.asset_added_successfully'));
            }catch (Exception $ex) {
                return redirect()->back()->with('fails', $ex->getMessage());
            }
    }

}