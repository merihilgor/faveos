<?php
namespace App\Plugins\ServiceDesk\Model\Common;

use Illuminate\Database\Eloquent\Model;
use App\Plugins\ServiceDesk\Model\Products\SdProducts;
use App\Plugins\ServiceDesk\Model\Vendor\SdVendors;

class ProductVendorRelation extends Model
{
    protected $table = 'sd_product_vendor_relation';
    protected $fillable = ['product_id','vendor_id'];

    /**
     * relationship with product
     */
    public function products(){
        return $this->hasMany(SdProducts::class,'id','product_id');
    }

    /**
     * relationship with vendor
     */
    public function vendors(){
        return $this->hasMany(SdVendors::class,'id','vendor_id');
    }
    
}