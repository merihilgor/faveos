<?php

namespace App\Plugins\ServiceDesk\Model\Contract;

use Illuminate\Database\Eloquent\Model;

/**
 * Pivot model relation for SdUser and SdContract model
 * 
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
*/
class SdContractSdUser extends Model
{
    protected $table = 'sd_contract_user';

    protected $fillable = ['id', 'contract_id', 'agent_id', 'created_at', 'updated_at'];
}
