<?php

namespace App\Http\Middleware;
use Closure;
use Auth;
use Lang;
use  App\Policies\TicketPolicy;

/**
 * Check Kb Access.
 *
 * @author   Arindam Jana <arindam.jana@ladybirdweb.com>
 */

class CheckKbAccess {


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $policy = new TicketPolicy();

        if ($request->user() && (!$policy->kb() ||  $request->user()->role == 'user')) {

                return redirect('dashboard')->with('fails', Lang::get('lang.permission_denied'));
            }
        
        return $next($request);
    }

}
