<?php

namespace App\Plugins\ServiceDesk\Model\Contract;

use Illuminate\Database\Eloquent\Model;

/**
 * SdContractThread model for contract history
 * 
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
*/
class SdContractThread extends Model
{
    protected $table = 'sd_contract_threads';
    protected $fillable = [
        'id',
        'contract_id',
        'status_id',
        'renewal_status_id',
        'cost',
        'contract_start_date',
        'contract_end_date',
        'owner_id',
        'approver_id'
    ];
    
    /**
    * relationship with SdContract
    */
    public function contract()
    {
        return $this->belongsTo('App\Plugins\ServiceDesk\Model\Contract\SdContract', 'contract_id', 'id');
    }

    /**
    * relationship with SdContractStatus for contract status
    */
    public function contractStatus()
    {
        return $this->belongsTo('App\Plugins\ServiceDesk\Model\Contract\SdContractStatus', 'status_id', 'id');
    }

    /**
    * relationship with SdContractStatus for contract renewal
    */
    public function contractRenewalStatus()
    {
        return $this->belongsTo('App\Plugins\ServiceDesk\Model\Contract\SdContractStatus', 'renewal_status_id', 'id');
    }

    /**
    * relation with SdUser for contract owner
    */
    public function owner(){
        return $this->belongsTo('App\Plugins\ServiceDesk\Model\Common\SdUser','owner_id', 'id');
    }

    /**
    * relation with SdUser for contract approver
    */
    public function approver(){
        return $this->belongsTo('App\Plugins\ServiceDesk\Model\Common\SdUser','approver_id', 'id');
    }

}
