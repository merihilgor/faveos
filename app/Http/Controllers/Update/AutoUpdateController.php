<?php

namespace App\Http\Controllers\Update;

use File;
use Artisan;
use Updater; //self Updater facede
use Exception;
use Schema;
use App\Backup_path;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Database\Connection;
use Symfony\Component\Finder\Finder;
use App\Http\Controllers\Controller;
use App\Model\Update\BarNotification;
use App\Model\helpdesk\Settings\System;
use Codedge\Updater\Events\UpdateFailed;
use App\Http\Controllers\Utility\LibraryController;
use App\Http\Controllers\Update\SyncFaveoToLatestVersion;

/**
 * ---------------------------------------------------
 * AutoUpdateController
 * ---------------------------------------------------
 * This controller handles all the auto update functions
 *
 * @author      Ladybird <info@ladybirdweb.com>,  Abhishek Gurung <abhishek.gurung@ladybirdweb.com>
 * @author      Ladybird <info@ladybirdweb.com>,  Ashutosh Pathak <ashutosh.pathak@ladybirdweb.com>
 */

class AutoUpdateController extends Controller
{
    const GITHUB_API_URL = 'https://api.github.com';
    const NEW_VERSION_FILE = 'self-updater-new-version';

    protected $updater;
    protected $system;
    protected $client;

    public function __construct(Updater $updater, Client $client)
    {
        require_once(public_path('script/update_core_configuration.php'));
        require_once(public_path('script/update_core_functions.php'));
        $this->client = $client; //initialize guzzle client
        $this->updater = $updater; // initialize updater
        $this->system = System::first();
    }


    /**
     * check for updates.
     *
     * This function checks for update based on
     * product_order_number, serial_key and domain_name
     *
     */
    protected function checkForUpdates()
    {
        if (\Auth::user()->role != 'admin') {
            return view('themes.default1.admin.helpdesk.auto-updates.index')->with(['error' => 'Permission Denied']);
        }
        if ($this->system->version == trim(\Storage::get('self-updater-new-version'))) {
            return view('themes.default1.admin.helpdesk.auto-updates.index')->with(['no_update' => "Sorry no updates available "]);
        }
        if (!$this->system->serial_key && !Schema::hasTable('faveo_license')) {
            return redirect('update-order-details');
        }
        $data = Schema::hasTable('faveo_license') ? ['license_code' => \DB::table('faveo_license')->pluck('LICENSE_CODE')->first(),
        ] : ['license_code' => $this->system->serial_key];

        $response = $this->checkUpdatesExpiry($data);
        $result =json_decode($response->getBody()->getContents());
        if ($result->status == "success") {
            $update_avaiable = true;
            return view('themes.default1.admin.helpdesk.auto-updates.index', compact('response', 'update_avaiable'));
        } else {
            return view('themes.default1.admin.helpdesk.auto-updates.index');
        }
    }

    private function checkUpdatesExpiry($data)
    {
        try {//Check from Billing if the Auto Updates have  expired
            $expiryCheck = $this->client->request(
                'POST',
                'https://billing.faveohelpdesk.com/v1/checkUpdatesExpiry',
                [
                'form_params' => $data
                ]
            );
            return $expiryCheck;
        } catch (\Exception $e) {
            return view('themes.default1.admin.helpdesk.auto-updates.index')->with(['error' => $e->getMessage()]);
        }
    }


    public function update(Request $request)
    {
         if(\Event::dispatch('helpdesk.apply.whitelabel')){
            return ['success' => false, "message" => "Update cannot be proceed due to whitelable enabled"];
        }
        set_time_limit(0);
        \Config::set('app.debug', true);
         
        try {
           Artisan::call('down');
            $response=ausDownloadFile();//Download ann extract the files here
            
            if ($response['notification_case']=="notification_operation_ok") { //'notification_operation_ok' case returned - operation succeeded
               
                //Clear bootstrap/cache after new Files are replaced 
                 $files = glob(base_path().'/bootstrap/cache/*'); // get all file names
                    foreach($files as $file){ // iterate files
                      if(is_file($file))
                        unlink($file); // delete file
                    }
             //   System::first()->update(['version'=> $request->input('update_version')]);
               Artisan::call('up');
                return ["success" => true, "message" => $response['notification_text']];
            } else {
               Artisan::call('up');
                return ["success" => false, "message" => "Oops something went wrong during update. ".$response['notification_text']];
            }
        } catch (\Exception $e) {
            Artisan::call('up');
            return ["success" => false, "message" => "Oops something went wrong during update. ".$e->getMessage()];
        }
    }

    public function getOrderDetails()
    {
        return view('themes.default1.admin.helpdesk.auto-updates.order-details');
    }


    public function updateOrderDetails(Request $request)
    {
        $data = $request->except('_token');
        if ($this->system->update($data)) {
            return redirect('check-updates');
        }
    }



    public static function getLatestRelease()
    {
        if (empty(\Config::get('self-update.app_name'))) {
            throw new \Exception('No repository specified. Please enter a valid Github repository owner and name in your config.');
        } else {
            $client = new Client([]);
            $source = 'https://billing.faveohelpdesk.com/version/latest?title='.\Config::get('self-update.app_name');
            $response = $client->request(
                'GET',
                $source
            );
            
            $releaseCollection = collect(\GuzzleHttp\json_decode($response->getBody()));
            if ($releaseCollection->isEmpty()) {
                throw new \Exception('Cannot find a release to update. Please check the repository you\'re pulling from');
            }
            $release = 'v'.$releaseCollection['version'];

            \Storage::put(static::NEW_VERSION_FILE, $release);

            // generate a notification
            self::createNewReleaseNotification();
        }
    }

    private static function createNewReleaseNotification()
    {
        if(version_compare(\Storage::get(static::NEW_VERSION_FILE), System::value('version'), '>')){
            BarNotification::updateOrCreate(['key' => 'new-version'],[
                "key" => "new-version",
                "value" => "A new update is available. Please <a href='".url('check-updates')."'> click here </a> to update your system to ".\Storage::get(static::NEW_VERSION_FILE)
            ]);
        }
    }

    protected function updateDatabase()
    {
        try {
            $latestConfigVersion = \Config::get('app.tags');
            $latestVersion = trim(\Storage::get('self-updater-new-version'));
            if ($latestConfigVersion == $latestVersion) {
                (new SyncFaveoToLatestVersion)->sync();
                 $notify = BarNotification::where('key', 'new-version')->delete();
                 return ["success" => true, "message" => "Operation Successful"];
            }
            return ["success" => false, "message" => 'Update Failed. Contact Faveo Team'];
           
        } catch (\Exception $ex) {
            return ["success" => false, "message" => 'Database cannot be updated'];
        }
        
    }

}
