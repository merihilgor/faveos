<?php

namespace App\Plugins\ServiceDesk\Model\Assets;
use Illuminate\Database\Eloquent\Model;

class AssetFormBilder extends Model
{
    protected $table = 'sd_assets_frombilder';
    protected $fillable = [
        'asset_id',
        'json',
        
    ];

    public function getJsonAttribute($value){
    	if(!$value){
    		$value = json_encode($value,true);
    	}
    	return $value;
    }
}
