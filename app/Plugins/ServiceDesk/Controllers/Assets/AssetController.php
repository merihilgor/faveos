<?php

namespace App\Plugins\ServiceDesk\Controllers\Assets;

use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use App\Plugins\ServiceDesk\Controllers\Library\UtilityController;
use App\Plugins\ServiceDesk\Model\Assets\SdImpactypes;
use App\Plugins\ServiceDesk\Model\Assets\SdAssettypes;
use App\Model\helpdesk\Agent\Department;
use App\Location\Models\Location as SdLocations;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\Plugins\ServiceDesk\Requests\CreateAssetRequest;
use App\Plugins\ServiceDesk\Requests\CreateAssetUpdateRequest;
use Illuminate\Http\Request;
use App\Plugins\ServiceDesk\Model\Assets\AssetForm;
use Exception;
use App\Plugins\ServiceDesk\Model\Products\SdProducts;
use App\Plugins\ServiceDesk\Model\Assets\AssetFormBilder;
use App\Plugins\ServiceDesk\Model\Assets\AssetFormRelation;
use App\Plugins\ServiceDesk\Model\FormBuilder\Form;
use App\Plugins\ServiceDesk\Model\Assets\CommonAssetRelation;
use App\Plugins\ServiceDesk\Model\Common\CommonTicketRelation;
use DB;
use File;
use Lang;
use DataTables;
use Input;
use App\Plugins\ServiceDesk\Policies\AgentPermissionPolicy;
use App\Plugins\ServiceDesk\Model\Common\SdUser;


class AssetController extends BaseServiceDeskController {

    protected $agentPermission; 

    public function __construct() {
        $this->middleware('role.agent');
        $this->agentPermission = new AgentPermissionPolicy();
    }

    public function index() {
        try {
            if (!$this->agentPermission->assetsView()) {
            return redirect('dashboard')->with('fails', Lang::get('ServiceDesk::lang.permission-denied'));
            }
            $sdPolicy = $this->agentPermission;
            return view('service::assets.index',compact('sdPolicy'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @return type
     */
    public function getAsset() {
        try {
            $asset = new SdAssets();
            $assets = $asset->select('id', 'name', 'description', 'department_id', 'asset_type_id', 'impact_type_id', 'managed_by', 'used_by', 'location_id', 'assigned_on')->get();
            return DataTables::Collection($assets)
                            ->addColumn('name', function($model) {
                                     return "<span title='".$model->name."'>". str_limit(ucfirst($model->name), 20)."</span>";
                            })
                            ->addColumn('managed_by', function($model) {
                                if($model->managed_by){
                                    $managed = SdUser::where('id',$model['managed_by'])->first();
                                    $managedBy = ($managed != null) ? str_limit($managed->fullName, 30) : null ;
                                    $managedByTitle = ($managed != null) ? $managed->fullName : null ;
                                    return "<a href=".url('user/'.$model['managed_by']). " title ='$managedByTitle'>".$managedBy."</a>";
                                }     
                            })
                            ->addColumn('used_by', function($model) {
                                if($model->used_by){
                                    $used = SdUser::where('id',$model['used_by'])->first();
                                    $usedByTitle = ($used  != null) ?  $used->fullName : null ;
                                    $usedBy = ($used != null) ? str_limit($used->fullName, 30) : null ;
                                    return "<a href=".url('user/'.$model['used_by']). " title ='$usedByTitle'>".$usedBy."</a>";
                                }
                            })   
                            ->addColumn('action', function($model) {
                                  $url = url('service-desk/assets/' . $model->id . '/delete');
                                  $delete = $this->agentPermission->assetDelete() ? UtilityController::deletePopUp($model->id, $url, "Delete $model->subject") : '';
                                  $edit = $this->agentPermission->assetEdit() ? "<a href=" . url('service-desk/assets/' . $model->id . '/edit') . " class='btn btn-primary btn-xs'><i class='fa fa-edit'>&nbsp;&nbsp;</i>".Lang::get('ServiceDesk::lang.edit')."</a>&nbsp; " : '';
                                  $view = $this->agentPermission->assetsView() ? " <a href=" . url('service-desk/assets/' . $model->id . '/show') . " class='btn btn-primary btn-xs'><i class='fa fa-eye'>&nbsp;&nbsp;</i>".Lang::get('ServiceDesk::lang.view')."</a>": '';
                                  return $edit.$delete.$view;
                            })
                            ->rawColumns(['name','managed_by','used_by','action'])
                            ->make();
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @return type
     */
    public function create() {
        try {
            if (!$this->agentPermission->assetCreate()) {
            return redirect('dashboard')->with('fails', Lang::get('ServiceDesk::lang.permission-denied'));
            }
            $sd_impact_types = SdImpactypes::pluck('name', 'id')->toArray();
            $sd_asset_types = SdAssettypes::pluck('name', 'id')->toArray();
            $departments = Department::pluck('name', 'id')->toArray();
            $products = SdProducts::where('status','=', '1')->pluck('name','id')->toArray();
            $users = SdUser::where('role', '!=' ,'user')->where('active', 1)->where('is_delete', 0)->pluck('email', 'id')->toArray();
            $usedBind = SdUser::where('active', 1)->where('is_delete', 0)->pluck('email', 'id')->toArray();
            $sd_locations = SdLocations::pluck('title', 'id')->toArray();
            $organizations = \App\Model\helpdesk\Agent_panel\Organization::pluck('name', 'id')->toArray();
            return view('service::assets.create', compact('organizations', 'products', 'sd_impact_types', 'sd_asset_types', 'users', 'departments', 'sd_locations','usedBind'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @param CreateAssetRequest $request
     * @return type
     */
    public function handleCreate(CreateAssetRequest $request) {
        //dd($request->all());
        try {
             
            $sd_assets = new SdAssets;
            $sd_assets->fill($request->except('assigned_on'))->save();

            if($request->assigned_on){

              $assignedOn = \Carbon\Carbon::createFromFormat(dateTimeFormat(), $request->assigned_on, timezone())->setTimezone('UTC');
              $assignedOndate =  $assignedOn->toDateTimeString();
              $sd_assets->assigned_on = $assignedOndate;
              $sd_assets->save();
            }
            
             $attachment[]=$request->file('attachments');
            
            $this->saveExternalId($sd_assets);
            UtilityController::attachment($sd_assets->id, 'sd_assets', $attachment );
            $check_array = $request->except('organization', 'identifier', 'product_id', '_token', 'name', 'description', 'department_id', 'asset_type_id', 'impact_type_id', 'managed_by', 'used_by', 'location_id', 'assigned_on', 'EditJson','attachments');

            if ($check_array) {
                foreach ($check_array as $key => $value) {

                    $form[] = $value;
                }
            } else {
                $form = null;
            }

            // dd( json_encode($request->EditJson));
            $sd_asset_frombilder = new AssetFormBilder();
            $sd_asset_frombilder->asset_id = $sd_assets->id;
            $sd_asset_frombilder->json = json_encode($request->EditJson);
            $sd_asset_frombilder->save();

            // $check_array=array($form);
            // $check_array1=array_flatten($check_array);
            // dd($sd_asset_frombilder);
            // /
            $this->storeAssetForm($sd_assets->id, $form);
            $result = ["success" =>  Lang::get('ServiceDesk::lang.asset_created_successfully')];
            return response()->json(compact('result'));
            return Redirect()->back()->with('message', Lang::get('ServiceDesk::lang.asset_created_successfully'));
        } catch (Exception $ex) {
            // dd($ex);
            $result = ["fails" => $ex->getMessage()];
            return response()->json(compact('result'));
            //return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @param type $id
     * @return type
     */
    public function edit($id) {
        try {
            if (!$this->agentPermission->assetEdit()) {
            return redirect('dashboard')->with('fails', Lang::get('ServiceDesk::lang.permission-denied'));
            }
            $asset = SdAssets::find($id);
            $sd_impact_types = SdImpactypes::pluck('name', 'id')->toArray();
            $sd_asset_types = SdAssettypes::pluck('name', 'id')->toArray();
            $departments = Department::pluck('name', 'id')->toArray();
//            $products = SdProducts::pluck('name', 'id')->toArray();
            $products = SdProducts::where('status','=', '1')->pluck('name','id')->toArray();
            $users = SdUser::where('role', '!=' ,'user')->where('active', 1)->where('is_delete', 0)->pluck('email', 'id')->toArray();
            $usedBind = SdUser::where('active', 1)->where('is_delete', 0)->pluck('email', 'id')->toArray();
            $sd_locations = SdLocations::pluck('title', 'id')->toArray();
            $organizations = \App\Model\helpdesk\Agent_panel\Organization::pluck('name', 'id')->toArray();
            $assignedOn =($asset->assigned_on && $asset->assigned_on != 'CURRENT_TIMESTAMP')?faveoDate($asset->assigned_on):null;
            return view('service::assets.edit', compact('organizations', 'products', 'sd_impact_types', 'sd_asset_types', 'users', 'departments', 'sd_locations', 'asset','assignedOn','usedBind'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @param type $id
     * @return type
     */
    public function editapi($asset_type) {


        try {
            $check_form_bilder = AssetFormBilder::where('asset_id', $asset_type)->select('json as form')->first();

            $asset_type_id=SdAssets::where('id', $asset_type)->first();
            $customform_id=AssetFormRelation::where('asset_type_id', $asset_type_id->asset_type_id)->first();
            $form=Form::where('id', $customform_id->form_id)->select('title')->first();
            $title=json_encode($form,JSON_FORCE_OBJECT);
            $form_title[]=json_decode($form,true);



            if ($check_form_bilder) {
            $form_array=json_encode($check_form_bilder,JSON_FORCE_OBJECT);
            $form_array_json[]=json_decode($form_array,true);
            $array=array_merge($form_title,$form_array_json);

                return response()->json($array);
            } else {
                $form_array = null;
                return response()->json($array);
            }
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @param CreateAssetRequest $request
     * @return type
     */
    public function handleEdit($id, CreateAssetUpdateRequest $request) {
        try {

            $sd_assets = SdAssets::findOrFail($id);
         
            $sd_assets->fill($request->except('assigned_on','used_by','managed_by'))->save();

            $managedBy = ($request->input('managed_by'))?$request->input('managed_by'):null;
            SdAssets::where('id',$id)->update(['managed_by'=>$managedBy]);

            $usedby = ($request->input('used_by'))?$request->input('used_by'):null;
            SdAssets::where('id',$id)->update(['used_by'=>$usedby]);

            if($request->assigned_on){

                 $assignedOn = \Carbon\Carbon::createFromFormat(dateTimeFormat(), $request->assigned_on, agentTimeZone())->setTimezone('UTC');
                $assignedOndate =  $assignedOn->toDateTimeString();
                
                SdAssets::where('id',$id)->update(['assigned_on'=>$assignedOndate]);
            }else{
                SdAssets::where('id',$id)->update(['assigned_on'=>null]);

            }

            if($request->file('attachments')){
            $attachment =DB::table('sd_attachments')->where('owner', '=','sd_assets:'.$id)->first();
            if($attachment){
            $file=$attachment->value;
            File::delete(public_path('uploads/service-desk/attachments/' . $file));
             $attachment =DB::table('sd_attachments')->where('owner', '=','sd_assets:'.$id)->delete();
             }
            }
            $attachment = [$request->file('attachments')];
            $this->saveExternalId($sd_assets);
            UtilityController::attachment($sd_assets->id, 'sd_assets',$attachment);
            $check_array = $request->except('organization', 'identifier', 'product_id', '_token', 'name', 'description', 'department_id', 'asset_type_id', 'impact_type_id', 'managed_by', 'used_by', 'location_id', 'assigned_on', 'EditJson','attachments', 'uploadfile');

          if ($check_array) {

                foreach ($check_array as $key => $value) {
                    $form[] = $value;
                }
            } else {
                $form = null;
            }
            $sd_asset_formbilder = new AssetFormBilder();
            $sd_asset_formbilder->updateOrCreate(['asset_id' => $sd_assets->id], [
              'asset_id' => $sd_assets->id,
              'json' => json_encode($request->EditJson)
            ]);

            $this->storeAssetForm($sd_assets->id, $form);
            $result = ["success" => Lang::get('ServiceDesk::lang.asset_updated_successfully')];
            return response()->json(compact('result'));
            //return \Redirect::route('service-desk.asset.index')->with('message', 'Asset successfully Edit !!!');
        } catch (Exception $ex) {
            dd($ex);
            $result = ["fails" => $ex->getMessage()];
            return response()->json(compact('result'));
            //return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @param type $id
     * @return type
     */
    public function assetHandledelete($id) {
        try {

            $sd_assets = SdAssets::where('id', '=', $id)->delete();
            $ticketRelation = CommonTicketRelation::where('type', 'sd_assets')->where('type_id', $id);
            $ticketRelation->delete();
            $assetRelation = CommonAssetRelation::where('asset_id', $id);
            $assetRelation->delete();
         return \Redirect::route('service-desk.asset.index')->with('message',Lang::get('ServiceDesk::lang.asset_deleted_successfully'));
            
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @param Request $request
     * @return type
     */
    public function search(Request $request) {
        try {
            $format = $request->input('format');
            $query = $request->input('query');
            $assets = UtilityController::assetSearch($query, $format);
            return $assets;
        } catch (Exception $ex) {
            
        }
    }

    /**
     * 
     * @param Request $request
     * @return type
     */
    public function attachAssetToTicket(Request $request) {
             $this->validate($request, ['asset' => 'required']);
        try {
                $data = CommonTicketRelation::where([ ['ticket_id', request('tiketid')],['type','sd_assets'] ])
                                         ->whereIn('type_id',request('asset'))->get()->toArray();
            if(count($data)> 0){
                 return redirect()->back()->with('fails',Lang::get('ServiceDesk::lang.please_remove_existing_asset_while_asset_selection'));
            }
               $threadid = $request->input('tiketid');
               $ticket   = UtilityController::getTicketByThreadId($threadid);
               $ticketid = $ticket->id;
           if($request->has('asset')){
                 $assetIds = $request->input('asset');
             foreach ($assetIds as $value) {
                        UtilityController::commonTicketAttachRelation($ticketid, $value, 'sd_assets');
                        UtilityController::commonAssetAttach($assetIds, $ticketid, 'tickets'); 
                      }
                        
          } 
          return redirect()->back()->with('success',Lang::get('ServiceDesk::lang.asset_added_successfully'));
        } catch (Exception $ex) {
            // dd($ex);
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @param Request $request
     * @return type
     */
    public function assetType(Request $request) {
        try {
            $asset_type_id = $request->input('asset_type');
            if ($asset_type_id) {
                return \Datatable::table()
                                ->addColumn('#', 'Assets', 'Used By')
                                ->setUrl(url('service-desk/asset-type/' . $asset_type_id))
                                ->render();
            }
        } catch (Exception $ex) {
            // dd($ex);
        }
    }

    /**
     * 
     * @param type $id
     * @return type
     */
    public function getAssetType($id = "") {
        if ($id) {
            $assets = UtilityController::assetByTypeId($id);
        } else {
            $assets = new SdAssets();
        }
      return $this->createChumper($assets->get());
    }


    /**
     * Functin to get assets to attach assets based on type, usedById, managedById and organizationId
     * @param object $request
     * @return data table
     */
    public function getAssetsForAttachAssets(Request $request) {
      $linkedAssets = CommonAssetRelation::where('type', 'sd_contracts')->pluck('asset_id')->toArray();
      $assets = new SdAssets();
      // initialiazing variables with request parameters
      $assetTypeId = $request->asset_type_id;
      $usedById = $request->used_by_id;
      $managedById = $request->managed_by_id;
      $organizationId = $request->organization_id;
      $assets = $assets->when($assetTypeId, function($q)use($assetTypeId) {
          $q->where('asset_type_id', $assetTypeId);
        })
        ->when($usedById, function($q)use($usedById) {
          $q->where('used_by_id', $usedById);
        })
        ->when($managedById, function($q)use($managedById) {
          $q->where('managed_by_id', $managedById);
        })
        ->when($organizationId, function($q)use($organizationId) {
          $q->where('organization_id', $organizationId);
        })
        ->whereNotIn('id', $linkedAssets)
        ->get();

      return $this->createChumper($assets);
    }

    /**
      * Function to get make data table of asset
      * @param object $assets
      * @return data table
      */
    public function createChumper($assets) {

      return DataTables::Collection($assets)
        ->addColumn('id', function($model) {
          return "<input type='checkbox' name='asset[]'  class='selectval icheckbox_flat-blue' value='" . $model->id . "'></input>";
        })
        ->addColumn('name', function($model) {
          return str_limit($model->name, 20);
        })
        ->addColumn('used_by', function($model) {
          $usedByName = '';
          if ($model->usedBy()->first()) {
            $usedByName = str_limit($model->usedBy()->first()->full_name, 20);
          }
          return $usedByName;
        })
        ->rawColumns(['id','name','used_by'])
        ->make();
    }

    /**
     * 
     * @param type $threadid
     */
    public function timelineMarble($asset, $ticketid) {

      if ($asset) {
          echo view("service::interface.agent.attached-assets", ['ticketid'=> $ticketid]);
        }
        echo "";
    }

    /**
     * 
     * @param type $asset
     * @param type $ticketid
     * @return type
     */
    public function marble($asset, $ticketid) {
        $user = UtilityController::getUserByAssetId($asset->id);
        $managed = UtilityController::getManagedByAssetId($asset->id);
        $asset_name = $asset->name;
        $user_name = $user->first_name . ' ' . $user->last_name;
        $managed_by = $managed->first_name . ' ' . $managed->last_name;
        return $this->marbleHtml($ticketid, $asset_name, $user_name, $managed_by, $asset->id);
    }

    /**
     * 
     * @param type $ticketid
     * @param type $asset_name
     * @param type $user_name
     * @param type $managed_by
     * @return type
     */
    public function marbleHtml($ticketid, $asset_name, $user_name, $managed_by, $assetid) {
        $url = url('service-desk/asset/detach/' . $ticketid.'/'.$assetid);
        $class = "btn btn-primary btn";
        $btn_name = "<i class='fa fa-trash'>&nbsp;&nbsp;</i>".Lang::get('ServiceDesk::lang.delete');
        $detach_popup = UtilityController::deletePopUp($ticketid, $url, "",$class,$btn_name,true);

        return "<div class='box box-primary'>"
                . "<div class='box-header'>"
                . "<h3 class='box-title'>".Lang::get('ServiceDesk::lang.associated_assets')."</h3>"
                . "</div>"
                . "<div class='box-body row'>"
                . "<div class='col-md-12'>"
                . "<table class='table'>"
                . "<tr>"
                . "<td title='".$asset_name."'>" . str_limit(ucfirst($asset_name),20) . "</td>"
                . "<td><i>".Lang::get('ServiceDesk::lang.used_by').": </i> " . ucfirst($user_name) . "</td>"
                . "<td><i>".Lang::get('ServiceDesk::lang.managed_by').": </i> " . ucfirst($managed_by) . "</td>"
                . "<th>" . $detach_popup
                . "&nbsp;&nbsp;&nbsp;<a href=" . url('service-desk/assets/' . $assetid . '/show/') . " class='btn btn-primary btn'><i class='fa fa-eye' style='color:white;'> &nbsp;".Lang::get('ServiceDesk::lang.view')."</a></th>"
                . "</table>"
                . "</div>"
                . "</div>"
                . "</div>";
    }

    /**
     * 
     * @param type $ticketid,$assetId
     * @return type
     */
    public function detach($ticketId, $assetId) {
        $relation = UtilityController::getRelationOfTicketByTable($ticketId, $assetId, 'sd_assets');
        if ($relation) {
            $relation->delete();
        }
        return redirect()->back()->with('success', Lang::get('ServiceDesk::lang.detached_successfully'));
       }

    public function storeAssetForm($assetid, $request) {

        $asset_form = new AssetForm();
        $asset_forms = $asset_form->where('asset_id', $assetid)->get();
        if ($asset_forms->count() > 0) {
            foreach ($asset_forms as $form) {
                $form->delete();
            }
        }

        if ($request != null) {
            foreach ($request as $key => $value) {

                // dd(key($value), $value[key($value)]);
                $asset_form->updateOrCreate(['asset_id' => $assetid, 'key' => key($value)], [
                    'asset_id' => $assetid,
                    'key' => key($value),
                    'value' => $value[key($value)],
                ]);
            }
        }
    }

    public function getAssetFormContent($id) {
        $form_fiedls = new AssetForm();
        $fields = $form_fiedls->where('asset_id', $id)->pluck('value', 'key')->toArray();
        return $fields;
    }

    public function show($id) {
        try {
            if(!$this->agentPermission->assetsView()) {
            return redirect('dashboard')->with('fails', Lang::get('ServiceDesk::lang.permission-denied'));
            }
            $assets = new SdAssets();
            $asset = $assets->find($id);
            $sdPolicy = $this->agentPermission;
            if ($asset) {
                return view("service::assets.show", compact('asset', 'sdPolicy'));
            } else {
                throw new \Exception('Sorry we can not find your request');
            }
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    public function saveExternalId($asset) {
        $extid = \Input::get('identifier');
        if ($extid == "") {
            //dd('yes');
            $asset->identifier = "Asset-".$asset->id;
            $asset->save();
        }
    }

    public function requestersToArray($requesters) {
        for ($i = 0; $i < count($requesters); $i++) {
            if (is_object($requesters[$i])) {
                $array[$i]['subject'] = class_basename($requesters[$i]) == 'SdChanges' ? '<a href='.faveoUrl("service-desk/changes/{$requesters[$i]->id}/show/") .'>'. $requesters[$i]->subject . '</a>' : $requesters[$i]->subject();

                $array[$i]['request'] = ucfirst(str_replace('sd_', "", $requesters[$i]->getTable()));

                $array[$i]['status'] = class_basename($requesters[$i]) == 'SdChanges' ? $requesters[$i]->status->name : $requesters[$i]->statuses();

                $array[$i]['created'] = faveoDate($requesters[$i]->created_at);
            }
        }

        return $array;
    }

    public function getRequesters($id) {
        $assets = new SdAssets();
        $asset = $assets->find($id);
        $requesters = $asset->requests();
        $array = $this->requestersToArray($requesters);
        return $array;
    }

    public function ajaxRequestTable(Request $request) {
        $id = $request->input('assetid');
        $array = $this->getRequesters($id);
        $collection = new \Illuminate\Support\Collection($array);
        return DataTables::Collection($collection)
                          
                          ->addColumn('subject', function($model) {
                                  return $model['subject'];
                            })
                           ->addColumn('request', function($model) {
                                    return $model['request'];
                            })
                           ->addColumn('status', function($model) {
                                     return $model['status'];
                            })
                            ->addColumn('created', function($model) {
                                      return $model['created'];
                            })
                            ->rawColumns(['subject','request','status','created'])
                          
                           ->make();
    }

    public function export() {
        try {
            return view('service::assets.export');
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    public function exportAsset(Request $request) {
        try {
            $date = $request->input('date');
            $date = str_replace(' ', '', $date);
            $date_array = explode(':', $date);
            $first = $date_array[0] . " 00:00:00";
            $second = $date_array[1] . " 23:59:59";
            $first_date = $this->convertDate($first);
            $second_date = $this->convertDate($second);
            $assets = $this->getAssets($first_date, $second_date);
            $excel_controller = new \App\Http\Controllers\Common\ExcelController();
            $filename = "assets" . $date;
            $excel_controller->export($filename, $assets);
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    public function convertDate($date) {
        $converted_date = date('Y-m-d H:i:s', strtotime($date));
        return $converted_date;
    }

    public function getAssets($first, $last) {
        $asset = new SdAssets();
        $assets = $asset->leftJoin('department', 'sd_assets.department_id', '=', 'department.id')
                ->leftJoin('sd_asset_types', 'sd_assets.asset_type_id', '=', 'sd_asset_types.id')
                ->leftJoin('sd_products', 'sd_assets.product_id', '=', 'sd_products.id')
                ->leftJoin('users as used', 'sd_assets.used_by', '=', 'used.id')
                ->leftJoin('users as managed', 'sd_assets.managed_by', '=', 'managed.id')
                ->leftJoin('organization', 'sd_assets.organization', '=', 'organization.id')
                ->leftJoin('sd_locations', 'sd_assets.location_id', '=', 'sd_locations.id')
                ->whereBetween('sd_assets.created_at', [$first, $last])
                ->select('sd_assets.name as Name', 'sd_assets.identifier as Identifier', 'sd_assets.description as Description', 'department.name as Department', 'sd_asset_types.name as Type', 'sd_products.name as Product', 'used.email as Usedby', 'managed.email as Managedby', 'organization.name as Organization', 'sd_locations.title as Location', 'sd_assets.assigned_on as Assignedat')
                ->get()
                ->toArray();
        return $assets;
    }
public function deleteUploadfile($id,Request $request) {
        try {
           
           $file=$request->filename;
           $attachment =DB::table('sd_attachments')->where('owner', '=','sd_assets:'.$id)->delete();
            File::delete(public_path('uploads/service-desk/attachments/' . $file));
          return Lang::get('lang.your_status_updated_successfully');
           
            // return redirect()->back()->with('success', 'Updated');
        } catch (\Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }
public function downloadDocs($filename,Request $request) {
       
        $file_path = public_path() . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'service-desk' . DIRECTORY_SEPARATOR . 'attachments' . DIRECTORY_SEPARATOR . $filename;

        return response()->download($file_path);
    }

//it returns all asset list

public function getAllAsset(){
       $assets = SdAssets::select('id','name')->get()->toArray();
       return $assets;

    }
//it returns all asset list

public function getAssetsBasedOnContract(){

        $linkedAssets = CommonAssetRelation::where('type', 'sd_contracts')->select('asset_id')->get()->toArray();
        $assets = SdAssets::whereNotIn('id', $linkedAssets)->select('id','name')->get()->toArray();
        return $assets;
    }


//This function will retrive attached asset details in yajra data table format based on ticketId
    
    /**
     * @param $ticketId
     * @return type
     */
    public function getAttachedAssetsBasedTicket($ticketId) {

          $assetIds = CommonTicketRelation::where('ticket_id', $ticketId)->where('type', 'sd_assets')->pluck('type_id')->toArray();
          $selectedassets = SdAssets::whereIn('id', $assetIds)->select('id','name','managed_by','used_by')->get()->toArray();
          
          return DataTables::of($selectedassets)
                    ->addColumn('name', function($model) {
                        $name = $model['name'];
                        if($name){
                          return '<a href="'.url("service-desk/assets/".$model["id"]."/show"). '" title="'.$name.'">'.str_limit($name,30).'</a>';
                        }
                    })
                    ->addColumn('managed_by', function($model) {
                         $managed = SdUser::where('id',$model['managed_by'])->first();
                         $managedBy = ($managed != null) ? $managed->fullName : null ;
                         return "<a href=".url('user/'.$model['managed_by']). ">".$managedBy."</a>";
                    })
                    ->addColumn('used_by', function($model) {
                        $used = SdUser::where('id',$model['used_by'])->first();
                        $usedBy = ($used  != null) ?  $used->fullName : null ;
                        return "<a href=".url('user/'.$model['used_by']). ">".$usedBy."</a>";
                    })
                    ->addColumn('action', function($model) use ($ticketId) {

                          $url    = url('service-desk/asset/detach/' . $ticketId.'/'.$model['id']);
                          $detach = ($this->agentPermission->assetDetach()) ? UtilityController::detachPopUp($model['id'], $url, "Detach") : ''; 
                         return $detach;
                    })
                    ->rawColumns(['name','managed_by','used_by','action'])
                    ->make(true);
    }


    //This function will retrive associated assets details in yajra data table format based on organizationId
    
    /**
     * @param $OrgId
     */
    public function getAttachedAssetsBasedOrganization($OrgId) {
    
        $assetInfo = UtilityController::getAssetsByOrganizationId($OrgId);
          
            return DataTables::of($assetInfo)
                    ->addColumn('name', function($model) {
                        $name = $model['name'];
                        $assetType = SdAssettypes::where('id', $model['asset_type_id'])->value('name');

                        if($name){
                          return '<a href="'.url("service-desk/assets/".$model["id"]."/show"). '" title="'.$name.'">'.str_limit($name,30).'</a>&nbsp;<span style ="opacity:0.7">('.$assetType.')</span>';
                        }
                    })
                    ->addColumn('managed_by', function($model) {
                         $managed = SdUser::where('id',$model['managed_by'])->first();
                         $managedBy = ($managed != null) ? $managed->fullName : null ;
                         return "<a href=".url('user/'.$model['managed_by']). ">".$managedBy."</a>";
                    })
                    ->addColumn('used_by', function($model) {
                        $used = SdUser::where('id',$model['used_by'])->first();
                        $usedBy = ($used  != null) ?  $used->fullName : null ;
                        return "<a href=".url('user/'.$model['used_by']). ">".$usedBy."</a>";
                    })
                    ->rawColumns(['name','managed_by','used_by'])
                    ->make(true);
    }
    
    /**
     * Get Assets Based on User based on used_by of sd_asset
     * @param $userId
     * @return data table (yajra)
     */
    public function getAttachedAssetsBasedUser($userId) {
        $assets = SdUser::find($userId)->usedByAsset()->with(['assetType:id,name','usedBy:id,first_name,last_name','managedBy:id,first_name,last_name','contracts:sd_contracts.id,name'])->get()->toArray();
          
        return \DataTables::of($assets)
                ->addColumn('name', function($model) {
                  return "<a href=" . url("service-desk/assets/" . $model['id'] . "/show") . " title=" . $model['name'] . ">".str_limit(ucfirst($model['name']), 30) . '</a>&nbsp;<span style ="opacity:0.7">(' . ucfirst($model['asset_type']['name']) . ')</span>';
                })
                ->addColumn('managed_by', function($model) {
                     return "<a href=" . url('user/'.$model['managed_by']['id']) . ">" . str_limit($model['managed_by']['full_name'], 30) . "</a>";
                })
                ->addColumn('used_by', function($model) {
                  return "<a href=" . url('user/' . $model['used_by']['id']) . ">" . str_limit($model['used_by']['full_name'], 30) . "</a>";
                })
                ->addColumn('contract', function($model) {
                  $contract = $model['contracts'];
                  $contractHtmlCode = (!empty($contract)) ? "<a href=" . url('service-desk/contracts/' . reset($contract)['id'] . '/show') . ">" . str_limit(ucfirst(reset($contract)['name']), 30) . "</a>" : null;
                  return $contractHtmlCode;
                })
                ->rawColumns(['name','managed_by','used_by', 'contract'])
                ->make(true);
    }

}
