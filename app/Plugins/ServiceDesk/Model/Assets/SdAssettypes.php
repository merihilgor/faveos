<?php

namespace App\Plugins\ServiceDesk\Model\Assets;

use Illuminate\Database\Eloquent\Model;
use App\Plugins\ServiceDesk\Model\FormGroup\FormGroup;
use App\Model\helpdesk\Form\FormField;
use App\Plugins\ServiceDesk\Model\Common\SdDefault;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\Traits\Observable;

class SdAssettypes extends Model
{
    use Observable;

    protected $table = 'sd_asset_types';

    protected $fillable = ['id','name','parent_id'];

    protected $appends = ['is_default'];
    
    /**
     * relation with asset
     */
    public function asset() {
        return $this->hasMany(SdAssets::class, 'asset_type_id');
    }

    /** relation with asset type (parent and child relation)
     * Eg: Laptop is asset type name then it's parent can be Computer asset type
     */
    public function parent() {
        return $this->hasOne($this,'id', 'parent_id');
    }

    /**
     * method to get default asset type
     */
    public function defaultAssetType()
    {
        $defaultAssetTypeId = SdDefault::first()->asset_type_id;
        $defaultAssetType = $this->find($defaultAssetTypeId);

        return $defaultAssetType;
    }

    /**
     * delete asset type
     * @param $model
     * @return null
     */
    public function beforeDelete()
    {
        $defaultAssetType = $this->defaultAssetType();
        $this->asset()->where('asset_type_id', $this->id)->update(['asset_type_id' => $defaultAssetType->id]);
        $this->where('parent_id', $this->id)->update(['parent_id' => $defaultAssetType->id]);
    }

    /**
     * reference to form group
     */
    public function formGroups()
    {
      // where will sort
      return $this->belongsToMany(FormGroup::class,'sd_asset_type_form_group', 'asset_type_id', 'form_group_id');
    }

    //gives an array of form fields with same category id
    public function nodes(){
        return $this->morphMany(FormField::class, 'category');
    }

    /**
     * appends is_default attribute for asset type
     */
    public function getIsDefaultAttribute()
    {
      $defaultAssetTypeId = SdDefault::value('asset_type_id');

      return ($defaultAssetTypeId == $this->id);
    }
}
