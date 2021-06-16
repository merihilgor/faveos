<?php

namespace App\Plugins\ServiceDesk\Middleware;

use App\Http\Middleware\ActionPolicies;
use Closure;
/**
 * Custom Middleware to check permission for agents to perform
 * various actions bases on routes.
 *
 * @since v3.6.0
 * @author Manish Verma<manish.verma@ladybirdweb.com>
 */
class SdAccessPolicy extends ActionPolicies
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
        $this->policyClass = 'App\Plugins\ServiceDesk\Policies\AgentPermissionPolicy';
        $this->policyMethod = 'checkAgentPermission'; 
        $this->routePermissions = $this->getRoutePermissionArray();
        return parent::handle($request, $next);
    }

    /**
     * Method to return array containing route and corresponding permissions
     * @return array
     */
    private function getRoutePermissionArray():array
    {
        return [
            'DELETE' => [
                '/service-desk/api/asset-delete/*'    => 'delete_asset',
                '/service-desk/api/problem-delete/*'  => 'delete_problem',
                '/service-desk/api/contract/*'        => 'delete_contract',
                '/service-desk/api/release/*'         => 'delete_release',
                '/service-desk/api/change/*'          => 'delete_change'
            ],
        ];
    }
}