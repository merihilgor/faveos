<?php

namespace App\Plugins\ServiceDesk\Model\Contract;

use Illuminate\Database\Eloquent\Model;

class ContractUserOrganization extends Model
{   
    protected $table = 'sd_contract_user_organization';
    protected $fillable = ['contract_id','user_id','organization_id'];
}
