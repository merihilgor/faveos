<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Broadcast::routes();

        require base_path('routes/channels.php');

        /**
         * Requiring custom plugins channel
         */
        foreach (scandir(app_path('Plugins')) as $value) {
            $channels = app_path('Plugins').DIRECTORY_SEPARATOR.$value.DIRECTORY_SEPARATOR."channels.php";
            if(file_exists($channels)) {
                require $channels;
            }
        }
    }
}