<?php

namespace App\Plugins\ServiceDesk\Controllers\Assetstypes;

use App\Plugins\ServiceDesk\Controllers\BaseServiceDeskController;
use App\Plugins\ServiceDesk\Model\Assets\SdAssettypes;
use App\Plugins\ServiceDesk\Requests\CreateAssetstypesRequest;
use Illuminate\Http\Request;
use Exception;
use Lang;
use App\Plugins\ServiceDesk\Controllers\Library\UtilityController;
use App\Plugins\ServiceDesk\Model\Common\SdDefault;
use Redirect;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;

class AssetstypesController extends BaseServiceDeskController {

    protected $default;
    public function __construct() {
        $this->middleware('role.admin');
        $this->default = new SdDefault();
    }

    /**
     * 
     * @return type
     */
    public function index() {
        try {
            return view('service::assetstypes.index');
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @return type
     */
    public function getAssetstypes() {
        try {
            $asset = new SdAssettypes();
            $assets = $asset->select('id', 'name', 'parent_id', 'created_at', 'updated_at')->get();
            return \DataTables::Collection($assets)
                            ->addColumn('name', function($model) {
                                $name = $this->default->where([['id', 1], ['asset_type_id', $model->id]])->first() ? $model->name . "(Default)" : $model->name;
                                return "<span title='".$model->name."'>". str_limit($name, 25)."</span>";
                            })
                            ->addColumn('created_at', function($model) {
                                $created_at = faveoDate($model->created_at);
                                return  $created_at;    
                            })
                            ->addColumn('updated_at', function($model) {
                                $updated_at = faveoDate($model->updated_at);
                                return $updated_at;
                            })
                            ->addColumn('action', function($model) {
                                $edit  ="<a href=" . url('service-desk/assetstypes/' . $model->id . '/edit') . " class='btn btn-primary btn-xs'><i class='fa fa-edit' style='color:white;'>&nbsp;&nbsp;".Lang::get('ServiceDesk::lang.edit')."</i></a>&nbsp;&nbsp;";
                                $disable = $this->default->where([['id', 1], ['asset_type_id', $model->id]])->first() ? 'disabled' : '';
                                $url = url('service-desk/assetstypes/'. $model->id.'/delete');
                                $delete = UtilityController::deletePopUp($model->id, $url, "Delete", 'btn btn-primary btn-xs  ' . $disable);
                                return $edit.$delete;
                            })
                            ->rawColumns(['name','created_at','updated_at','action'])
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
            $types = SdAssettypes::pluck('name', 'id')->toArray();
            return view('service::assetstypes.create', compact('types'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @param CreateAssetstypesRequest $request
     * @return type
     */
    public function handleCreate(CreateAssetstypesRequest $request) {
        try {
            $sd_assetstypes = new SdAssettypes;
            $sd_assetstypes->fill($request->input())->save();
            if ($request->has('default_asset_type')) {
                $this->default->find(1)->update(['asset_type_id' => $sd_assetstypes->id]);
            }
            return \Redirect::route('service-desk.assetstypes.index')->with('message',Lang::get('ServiceDesk::lang.asset_type_created_successfully'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @param type $id
     * @return type
     */
    public function edit($id) {
        try {
            $type = SdAssettypes::findOrFail($id);
            $assetId =  $type->id;
            $types = SdAssettypes::pluck('name', 'id')->toArray();
            $sdDefault = SdDefault::first();
            return view('service::assetstypes.edit', compact('type', 'types', 'sdDefault'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    public function handleEdit($id, CreateAssetstypesRequest $request) {
        try {
            $sd_assets_types = SdAssettypes::findOrFail($id);
            if ($sd_assets_types) {
                $sd_assets_types->fill($request->input())->save();
                if ($request->has('default_asset_type')) {
                    $this->default->find(1)->update(['asset_type_id' => $sd_assets_types->id]);
                }
                return \Redirect::route('service-desk.assetstypes.index')->with('message',Lang::get('ServiceDesk::lang.asset_type_updated_successfully'));
            }
            throw new Exception("we can not find your request");
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @param type $id
     * @return type
     */
    public function assetHandledelete($id) {
        try {
            $sd_assets_types = SdAssettypes::findOrFail($id);
            if ($sd_assets_types) {
                $message = $this->checkDefaultAssetType($sd_assets_types->id, $sd_assets_types->name);
                if (strlen($message)) {
                    return Redirect::route('service-desk.assetstypes.index')->with('fails', $message);
                }
                else {
                $sd_assets_types->delete();
                $message = Lang::get('ServiceDesk::lang.associated_assets_and_asset_types_moved') . $sd_assets_types->name  . ', ' . Lang::get('ServiceDesk::lang.asset_type_deleted_successfully');
                return \Redirect::route('service-desk.assetstypes.index')->with('message', $message);
                }
            }
            throw new Exception('We can not find your request');
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /** 
     * method to check default asset type while asset type delete
     * @param $assetTypeId
     * @return 
     */
    private function checkDefaultAssetType($assetTypeId, $assetType) {
        $default = new SdDefault();
        $default = $default->first();
        $message = '';
        if (is_null($default)) {
            $message = Lang::get('ServiceDesk::lang.default_asset_type_does_not_exists') . $assetType;
        }
        else if ($assetTypeId == $default->asset_type_id) {
            $message = Lang::get('ServiceDesk::lang.cannot_delete_default_asset_type') . $assetType;
        }
        else {
              SdAssets::where('asset_type_id', $assetTypeId)->update(['asset_type_id' => $default->asset_type_id]);
              SdAssettypes::where('parent_id', $assetTypeId)->update(['parent_id' => $default->asset_type_id]);
        }

        return $message;
    }

}
