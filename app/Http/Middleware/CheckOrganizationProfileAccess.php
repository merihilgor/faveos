<?php

namespace App\Http\Middleware;

use App\Policies\TicketPolicy;
use Closure;
use Lang;

class CheckOrganizationProfileAccess
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $policy = new TicketPolicy;

        if (!$policy->accessOrganizationProfile()) {

            return redirect('dashboard')->with('fails', Lang::get('lang.permission_denied'));
        }

        return $next($request);
    }

}
