<?php
namespace App\Plugins\ServiceDesk\Model\Common;

use App\User;
use App\Plugins\ServiceDesk\Model\Permission\AgentPermission;

// extends helpdesk user model
class SdUser extends User
{
    /**
    * relationship with Agents to notify about contract expiry
    */
    public function contracts()
    {
        return $this->belongsToMany('App\Plugins\ServiceDesk\Model\Contract\SdContract');
    }

    /**
    * relationship with contract for contract approver
    */
    public function contractApprover()
    {
        return $this->hasOne('App\Plugins\ServiceDesk\Model\Contract\SdContract', 'approver_id', 'id');
    }

    /**
    * relationship with contract for contract owner
    */
    public function contractOwner()
    {
        return $this->hasOne('App\Plugins\ServiceDesk\Model\Contract\SdContract', 'owner_id', 'id');
    }

    /**
    * relationship with contract for contract Customer
    */
    public function contractCustomer()
    {
        return $this->hasMany('App\Plugins\ServiceDesk\Model\Contract\SdContract', 'customer_id', 'id');
    }

    /**
    * relationship with asset for used by
    */
    public function usedByAsset()
    {
        return $this->hasMany('App\Plugins\ServiceDesk\Model\Assets\SdAssets', 'used_by', 'id');
    }

    /**
    * relationship with asset for managed by
    */
    public function managedByAsset()
    {
        return $this->hasMany('App\Plugins\ServiceDesk\Model\Assets\SdAssets', 'managed_by', 'id');
    }
}