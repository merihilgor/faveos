<?php

namespace App\Plugins\ServiceDesk\Model\Assets;
use Illuminate\Database\Eloquent\Model;
use App\Plugins\ServiceDesk\Model\Contract\SdContract;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Traits\LogsActivity;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Models\Activity;
use App\Traits\Observable;

class CommonAssetRelation extends Model
{
    use LogsActivity;

    use Observable;
    
    protected $table = 'sd_common_asset_relation';
    protected $fillable = ['asset_id','type_id','type'];


   // Activity log name for changes
    protected static $logName = 'attached-asset';

    protected static $submitEmptyLogs = false;

    protected static $logOnlyDirty = true;

    // Attributes included inside change activity log 
    protected static $logAttributes = [
        'asset',
        'type_id',
        'type'
    ];
  
    /**
     * relationship with asset
     */
    public function asset(){
        return $this->hasMany(\App\Plugins\ServiceDesk\Model\Assets\SdAssets::class,'id','asset_id');
    }

    /**
     * relationship with problem
     */
    public function problem(){
        return $this->hasMany(\App\Plugins\ServiceDesk\Model\Problem\SdProblem::class,'id','type_id');
    }

    /**
     * relationship with change
     */
    public function change(){
        return $this->hasMany(\App\Plugins\ServiceDesk\Model\Changes\SdChanges::class,'id','type_id');
    }

    /**
     * relationship with release
     */
    public function release(){
        return $this->hasMany(\App\Plugins\ServiceDesk\Model\Releases\SdReleases::class,'id','type_id');
    }

    /**
     * relationship with contract
     */
    public function contract(){
        return $this->hasOne(SdContract::class, 'id','type_id');
    }

    /**
     * generates activity log for attached assets
     * @param $evenName
     * @return $eventName
     */
    public function getDescriptionForEvent(string $eventName): string
    {
        if ($eventName == 'created') {
            return 'Asset  <strong> #ASSET-'.$this->asset_id.' </strong> was attached';
        }

        if ($eventName == 'updated') {
            return 'Asset <strong> #ASSET-'.$this->asset_id.'</strong> was updated';
        }

        if ($eventName == 'deleted') {
            return 'Asset <strong> #ASSET-'.$this->asset_id.' </strong> was detached';
        }

        return '';
    }

    /**
     * delete attach asset enteries
     * @param $model
     * @return null
     */
    public function beforeDelete($model)
    {
        parent::delete();
    }

}
