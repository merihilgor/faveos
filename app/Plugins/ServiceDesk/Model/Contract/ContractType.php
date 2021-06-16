<?php

namespace App\Plugins\ServiceDesk\Model\Contract;

use Illuminate\Database\Eloquent\Model;
use App\Plugins\ServiceDesk\Model\Contract\SdContract;
use App\Traits\Observable;
use Exception;


class ContractType extends Model
{   
    use Observable;
    protected $table = 'sd_contract_types';
    protected $fillable = ['id','name','created_at','updated_at',
        
    ];
    
   /**
     * relation with contract
     */
    public function contract()
    {
        return $this->hasMany(SdContract::class, 'contract_type_id', 'id');
    }   

    public function beforeDelete() {
        if($this->contract()->count())
        {
            throw new Exception(trans('ServiceDesk::lang.cannot_delete_contract_type_when_associated',['attribute' => $this->name]));
        }
    } 
}
