<?php

namespace App\Plugins\ServiceDesk\Model\Permission;

use Illuminate\Database\Eloquent\Model;

class AgentPermission extends Model
{

	protected $table = 'sd_agent_permission';
	protected $fillable = [
            'user_id','permission'
	    ];


    public function getPermissionAttribute($value){
        if($value){
            $value = json_decode($value,true);
        }
        return $value;
    }
    
    public function setPermissionAttribute($value){

        if(is_array($value)){
            $value = json_encode($value);
        }
        $this->attributes['permission'] = $value;
    }

}

