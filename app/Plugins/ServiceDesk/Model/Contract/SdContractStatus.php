<?php

namespace App\Plugins\ServiceDesk\Model\Contract;

use Illuminate\Database\Eloquent\Model;
use App\Plugins\ServiceDesk\Model\Contract\SdContract;

/**
 * SdContractStatus Model
 * 
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
*/
class SdContractStatus extends Model
{
	protected $table = 'sd_contract_statuses';
    protected $fillable = ['id','name','type'];
    
    /**
    * relationship with contract for contract status
    */
    public function contractStatus()
    {
        return $this->hasOne(SdContract::class, 'status_id', 'id')->where('type', 'status');
    }

    /**
    * relationship with contract for contract renewal status
    */
    public function contractRenewalStatus()
    {
        return $this->hasOne(SdContract::class, 'renewal_status_id', 'id')->where('type', 'renewal_status');
    }
}

