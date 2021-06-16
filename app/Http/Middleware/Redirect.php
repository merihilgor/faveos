<?php

namespace App\Http\Middleware;

use App\Model\helpdesk\Settings\System;
use Closure;
use Schema;

class Redirect {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $root = $request->root(); //http://localhost/faveo/Faveo-Helpdesk-Pro-fork/public
        $url = $this->setAppUrl($request);
        if ($url == $root) {
            return $next($request);
        }
        $segments = $request->segments();
        $seg = implode('/', $segments);
        $url = strpos($url, '/probe.php') ? $url : $url .'/'. $seg;

        return redirect($url);
    }

    public function setAppUrl($request)
     {
         $url = $request->root().'/probe.php';
         if (isInstall()) {

             $baseQuery = System::select('url');

             // NOTE: access_vi_ip column has been added recently. In auto update, it break since this middleware it executes on
             // every request. If that column is not present, it should not query the column
             if(Schema::hasColumn('settings_system', 'access_via_ip')) {
                 $baseQuery->addSelect('access_via_ip');
             }

             $system = $baseQuery->first();

             if ($system && $system->url) {
                 //if access_via_ip in on we will not force system to open via URL
                 $url = ($system->access_via_ip) ? $request->root(): $system->url;
             }
         }
         return $url;
    }
}
