<?php

namespace App\Plugins\ServiceDesk;

use App\Plugins\SyncPluginToLatestVersion;

use Config;
use Illuminate\Foundation\AliasLoader;
use App\Plugins\ServiceDesk\milon\barcode\src\Milon\Barcode\Facades\DNS1DFacade;
use App\Plugins\ServiceDesk\milon\barcode\src\Milon\Barcode\Facades\DNS2DFacade;
use App\Plugins\ServiceDesk\milon\barcode\src\Milon\Barcode\BarcodeServiceProvider;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\ActivitylogServiceProvider;

class ServiceProvider extends \App\Plugins\ServiceProvider {

    public function register() {
        parent::register('ServiceDesk');
        $this->app->register(BarcodeServiceProvider::class);
        $this->app->booting(function() {
            $loader = AliasLoader::getInstance();
            $loader->alias('DNS1D', DNS1DFacade::class);
            $loader->alias('DNS2D', DNS2DFacade::class);
        });        
        $this->app->register(ActivitylogServiceProvider::class);
        $this->registerMiddlewareOfPackage([
            'sd-permissions' => 'App\Plugins\ServiceDesk\Middleware\SdAccessPolicy'
        ]);
    }

    public function boot() {

          // syncing ServiceDesk to latest version
         (new SyncPluginToLatestVersion)->sync('ServiceDesk');

         parent::boot('ServiceDesk');

         if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Plugins\ServiceDesk\Console\Commands\ContractNotificationExpiry::class,
                \App\Plugins\ServiceDesk\Console\Commands\ContractStatusExpired::class,
                \App\Plugins\ServiceDesk\Console\Commands\ContractStatusActive::class,
            ]);
        }

         /**
          * Views
          */
         $view_path = app_path().DIRECTORY_SEPARATOR.'Plugins'.DIRECTORY_SEPARATOR.'ServiceDesk'.DIRECTORY_SEPARATOR.'views';
         $this->loadViewsFrom($view_path, 'service');

        /**
         * Translation
         */
        $trans = app_path().DIRECTORY_SEPARATOR.'Plugins'.DIRECTORY_SEPARATOR.'ServiceDesk'.DIRECTORY_SEPARATOR.'lang';
        $this->loadTranslationsFrom($trans, 'ServiceDesk');

        if (class_exists('Breadcrumbs')){
            require __DIR__ . '/breadcrumbs.php';
        }

        \Event::dispatch('servicedesk',['status'=>true]);

        parent::boot('ServiceDesk');
    }
}
