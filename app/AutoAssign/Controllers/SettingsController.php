<?php

namespace App\AutoAssign\Controllers;

use App\Http\Controllers\Controller;
use App\Model\helpdesk\Settings\CommonSettings;
use Illuminate\Http\Request;
use App\AutoAssign\Requests\AssignmentRequest;
use App\Model\helpdesk\Agent\Department;
use Exception;
use Lang;

/**
 * Assigning the ticket via Auto assign module
 * 
 * @abstract controller
 * @author Ladybird Web Solution <admin@ladybirdweb.com>
 * @name SettingsController
 * 
 */


class SettingsController extends Controller {

    public function __contructor() {

        $this->middleware(['roles', 'install']);
    }
    /**
     * 
     * get the settings blade view
     * 
     * @return view
     */
    public function getSettings() {
        $departments = Department::all();
        $dept = Department::where('en_auto_assign', 1)->pluck('id')->toArray();
        return view('assign::setting', compact('departments', 'dept'));
    }
    /**
     * 
     * Posting the settings request
     * 
     * @param Request $request
     * @return string
     */
    public function postSettings(AssignmentRequest $request) {
        try {
            $all = $request->except('_token');
            ($request->filled('department_list')) && $all = $request->except('department_list');
            if (count($all) > 0) {
                $this->delete();
                $this->save($all);
            }
            ($request->filled('department_list')) ? $this->enableAutoAssignInDepartment($request->get('department_list')) : $this->disableAutoAssignInDepartment();
            return redirect()->back()->with('success',Lang::get('lang.auto_assign_updated'));
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }
    /**
     * 
     * get the icon on admin panel
     * 
     * @return string
     */
    public function settingsView() {
        return ' <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="' . url('auto-assign') . '" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-check-square-o fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >'.Lang::get('lang.auto_assign').'</p>
                    </div>
                </div>';
    }
    /**
     * deleting the settings
     */
    public function delete() {
        $setting = new CommonSettings();
        $settings = $setting->where('option_name', 'auto_assign')->get();
        if ($settings->count() > 0) {
            foreach ($settings as $assign) {
                $assign->delete();
            }
        }
    }
    /**
     * 
     * Saving the posted values
     * 
     * @param array $fields
     */
    public function save($fields) {
        $setting = new CommonSettings();
        foreach ($fields as $optional => $value) {
            $setting->create([
                'option_name' => 'auto_assign',
                'optional_field' => $optional,
                'option_value' => $value
            ]);
        }
    }

    /**
     * function to enable auto assignment for selected department lsit
     * @param    Array    $deptArray  Array list of department ids
     * @return   Boolean  true
     */
    public function enableAutoAssignInDepartment($deptArray = [])
    {
        if (count($deptArray) > 0) {
            $this->disableAutoAssignInDepartment();
            Department::whereIn('id', $deptArray)
            ->update([
                'en_auto_assign' => 1
            ]);
        }
        return true;
    }

    /**
     * function to disable auto-assignment for departments when all is selected in auto-assignment settings
     * @param    void
     * @return   boolean  true
     */
    public function disableAutoAssignInDepartment()
    {
        Department::where('en_auto_assign', 1)->update([
            'en_auto_assign' => 0
        ]);
        return true;
    }
}
