<?php

namespace App\Plugins\ServiceDesk\Model\Common;

use App\Model\helpdesk\Agent\Department;
use App\User;

// extends helpdesk department model
class SdDepartment extends Department
{
    /**
     * This relationship for users who belongs in this department as a member or manager
     *
     */
    public function agents()
    {

        return $this->belongsToMany(User::class, 'department_assign_agents', 'department_id', 'agent_id')->where([
            ['is_delete', 0],
            ['active', 1]
        ])->withTimeStamps();
    }
}