<?php

namespace App\Plugins\ServiceDesk\Model\Procurment;

use Illuminate\Database\Eloquent\Model;
use App\Plugins\ServiceDesk\Model\Products\SdProducts;
use App\Traits\Observable;
use Exception;


class SdProcurment extends Model
{   
    use Observable;

    protected $table = 'sd_product_proc_mode';
    protected $fillable = ['id','name','created_at','updated_at',
        
    ];

    /**
     * relation with product
     */
    public function product()
    {
        return $this->hasMany(SdProducts::class, 'product_mode_procurement_id', 'id');
    }

    public function beforeDelete() {
        if($this->product()->count())
        {
            throw new Exception(trans('ServiceDesk::lang.cannot_delete_procurment_when_associated',['attribute' => $this->name]));
        }
    }
}
