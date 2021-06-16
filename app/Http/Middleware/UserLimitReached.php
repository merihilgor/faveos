<?php

namespace App\Http\Middleware;

use Closure;
use Lang;

class UserLimitReached
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
        if(!isInstall()){
            return $next($request);
        }
        if (isInstall() && !$this->isUserExces() && !$this->isAgentExces()) {
            return $next($request);
        }
        return redirect()->back()->with('fails', Lang::get('lang.user-limit-reached-message'));

    }
    
     public function isLimitedUser() {
        $user_limit = \Config::get('auth.user_limit');
        $check = false;
        if ($user_limit !== 0) {
            $check = true;
        }
        return $check;
    }

    public function isLimitedAgent() {
        $agent_limit = \Config::get('auth.agent_limit');
        $check = false;
        if ($agent_limit  !== 0) {
            $check = true;
        }
        return $check;
    }

    public function isUserExces() {
        $check = false;
        $user_limit = \Config::get('auth.user_limit');

        if ($this->isLimitedUser()) {
            $user_count = \App\User::where('role', 'user')->select('id')->count();
            if ($user_limit <= $user_count) {
                $check = true;
            }
        }
        return $check;
    }

    public function isAgentExces() {
        $check = false;
        $user_limit = $this->getAllowedAgentLimit();

        if ($this->isLimitedAgent()) {
            $user_count = \App\User::where('role', '!=', 'user')->where('active', '=', 1)->select('id')->count();
            if ($user_limit <= $user_count) {
                $check = true;
            }
        }
        return $check;
    }

    /**
     * This function is used to exempt agent limit for version with limited editions are
     * installed using dummy data.
     * This is effectively restrict any business to avoid abuse user limit in the system
     * at the same time it will allow them to test the system with dummy data.
     *
     * Assumption: No professional business will have agents named as Ironman/Batman unless
     * business is dealing in commic or Maravel or DC starts using our system.
     */
    private function getAllowedAgentLimit()
    {
        if (commonSettings('dummy_data_installation', '', 'status')) {
            $allowedAgentsName = ['Demo', 'Batman', 'Huntress', 'Ironman', 'Catwoman', 'Supergirl', 'Superwoman'];
            $demoAgentsName = \App\User::where('role', '!=', 'user')->pluck('first_name')->toArray();
            if(!count(array_diff($allowedAgentsName, $demoAgentsName))) {
                return 8; 
            }
        }

        return \Config::get('auth.agent_limit');
    }
}
