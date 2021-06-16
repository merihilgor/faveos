<?php
namespace App\Plugins\ServiceDesk\Model\Common;

use App\Model\helpdesk\Agent_panel\Organization;
use App\Plugins\ServiceDesk\Model\Common\SdUser;

// extends helpdesk organization model
class SdOrganization extends Organization
{

    /**
     * relationship with sd_asset
     */
    public function assets(){
        return $this->hasMany('App\Plugins\ServiceDesk\Model\Assets\SdAssets', 'organization', 'id');
    }

    /**
     * This relationship is for members/managers who belongs to an organization
     *
     */
    public function members()
    {
        return $this->belongsToMany(SdUser::class, 'user_assign_organization', 'org_id', 'user_id')->where([
            ['is_delete', 0],
            ['active', 1]
        ])->withTimeStamps();
    }
  
}
