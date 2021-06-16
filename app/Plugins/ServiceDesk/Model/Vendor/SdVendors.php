<?php

namespace App\Plugins\ServiceDesk\Model\Vendor;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Observable;
use App\Plugins\ServiceDesk\Model\Contract\SdContract;
use App\Plugins\ServiceDesk\Model\Products\SdProducts;
use App\Plugins\ServiceDesk\Model\Common\ProductVendorRelation;
use Exception;

class SdVendors extends Model
{   
    use Observable;
    protected $table = 'sd_vendors';
    protected $fillable = ['id','name','primary_contact','email','description','address','status','created_at','updated_at',
        
    ];
    public function statuses() {
        $value = "Inactive";
        $status = $this->attributes['status'];
        if ($status==1) {
            $value = "Active";
        }
        return ucfirst($value);
    }
     /**
     * get the description of this model
     * @return string 
     */
    public function descriptions() {
        $value = "--";
        $attr = $this->attributes['description'];
        if ($attr) {
            $value = str_limit($attr, 10);
        }
        if (strlen($value) > 10) {
            $value .="  <a href=# id='show-description'>Show</a>";
        }
        return ucfirst($value);
    }

    /**
     * relation with contract
     */
    public function contract()
    {
        return $this->hasMany(SdContract::class, 'vendor_id', 'id');
    }

    /**
     * relationship of product vendor
     */
    public function productRelation(){
        return $this->hasMany(ProductVendorRelation::class, 'vendor_id', 'id');
    }

    public function attachProducts()
    {
        return $this->belongsToMany(SdVendors::class, 'sd_product_vendor_relation', 'vendor_id', 'product_id');
    
    }


    /**
     * delete vendor
     * @param $model
     * @return null
     */
    public function beforeDelete()
    {   
        $product = $this->productRelation()->count();
        $contract = $this->contract()->count();
        if($product or $contract)
        {   
            $message  = $this->getErrorMessage($product, $contract, $this->name);
            throw new Exception($message);
        }
    }

    /**
     * Function to get error message while deleting vendor if accociated with any module
     * @param $product
     * @param $contract
     * @param $name
     * @return $message
     */
    private function getErrorMessage($product, $contract, $name)
    {   
        $message = '';
        if($product)
        {   
            $model = 'Products';
        }
        elseif($contract)
        {   
            $model = 'Contracts';
        }
        return $message =  trans('ServiceDesk::lang.cannot_delete_vendor_when_associated',['attribute' => $name,'attribute1' => $model]);
    }
}
