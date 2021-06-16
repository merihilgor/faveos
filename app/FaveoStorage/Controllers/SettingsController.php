<?php

namespace App\FaveoStorage\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\helpdesk\Settings\CommonSettings;
use Exception;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Artisan;
use Lang;

class SettingsController extends Controller {
    /**
     * render html in admin settings for storage configuration
     * @return string
     */
    public function settingsIcon() {
        return ' <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="' . url('storage') . '">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-save fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >' . Lang::get("storage::lang.storage") . '</p>
                    </div>
                </div>';
    }
    /**
     * render the setting view
     * @return view
     */
    public function settings() {
        try {
            $settings = new CommonSettings();
            $def = $settings->getOptionValue('storage', 'default');
            $private_root = $settings->getOptionValue('storage', 'private-root');
            $pubic_root = $settings->getOptionValue('storage', 'public-root');
            $default = 'local';
            $root = storage_path('app');
            $private_folder = $root . '/private';
            $pubic_folder = public_path();
            if ($def) {
                $default = $def->option_value;
            }
            if ($private_root) {
                $private_folder = $private_root->option_value;
            }
            if ($pubic_root) {
                $pubic_folder = $pubic_root->option_value;
            }
            return view('storage::settings', compact('default', 'private_folder', 'pubic_folder'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }
    /**
     * update the configuration in database
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    public function postSettings(Request $request) {
        try {
            if($request->filled('private-root') && !is_dir($request->input('private-root'))){
                $dir = $request->input('private-root');
                throw new \Exception("'$dir'"." is not a valid directory");
            }
            if($request->filled('private-root') && !is_writable($request->input('private-root'))){
                $dir = $request->input('private-root');
                throw new \Exception("'$dir'"." hasn't write permission");
            }
            $requests = $request->except('_token');
            $this->delete();
            if (count($requests) > 0) {
                foreach ($requests as $key => $value) {
                    if ($value) {
                        $this->save($key, $value);
                    }
                }
            }
            return redirect()->back()->with('success',Lang::get('lang.storage_saved_successfully'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }
    /**
     * delete the configuration
     */
    public function delete() {
        $settings = CommonSettings::where('option_name', 'storage')->get();
        if ($settings->count() > 0) {
            foreach ($settings as $setting) {
                $setting->delete();
            }
        }
    }
    /**
     * save the configuration
     * @param string $key
     * @param string $value
     */
    public function save($key, $value) {
        CommonSettings::updateOrCreate([ 'option_name' => 'storage',
            'optional_field' => $key],[
            'option_name' => 'storage',
            'optional_field' => $key,
            'option_value' => $value,
        ]);
    }
    /**
     * get the directories
     * @param string $root
     * @return array
     */
    public function directories($root = "") {
        if ($root == "") {
            $root = base_path();
        }

        $iter = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST, RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
        );

        $paths = array($root);
        foreach ($iter as $path => $dir) {
            if ($dir->isDir()) {
                $paths[$path] = $path;
            }
        }

        return $paths;
    }
    /**
     * upload attachment
     */
    public function attachment() {
        $storage = new StorageController();
        $storage->upload();
    }
    /**
     * activate the module with migrations and seeding
     */
    public function activate() {
        if (!\Schema::hasColumn('ticket_attachment', 'driver')) {
            $path = "app" . DIRECTORY_SEPARATOR . "FaveoStorage" . DIRECTORY_SEPARATOR . "database" . DIRECTORY_SEPARATOR . "migrations";
            Artisan::call('migrate', [
                '--path' => $path,
                '--force' => true,
            ]);
        }
        $this->seed();
    }
    /**
     * seeding of the module
     */
    public function seed() {
        $settings = new CommonSettings();
        $def = $settings->getOptionValue('storage', 'private-root');
        if (!$def) {
            $options = [
                'default' => 'local',
                'private-root' => storage_path('app/private'),
                'public-root' => public_path()
            ];
            foreach ($options as $key => $value) {
                $this->save($key, $value);
            }
        }
    }

}
