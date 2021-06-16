<?php
namespace App\Plugins\ServiceDesk\Model\Assets;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Observable;
use Exception;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\Model\helpdesk\Form\FormCategory;
use App\Model\helpdesk\Form\FormField;

/**
 * SdAssetStatus Model for handling asset statuses
 *
 */
class SdAssetStatus extends Model
{
	use Observable;

    protected $table = 'sd_asset_statuses';
    protected $fillable = ['name','description'];

    /**
     * relation with assets
     */
    public function assets()
    {
        return $this->hasMany(SdAssets::class, 'status_id', 'id');
    } 

    /**
     * method to delete asset status
     */
    public function beforeDelete()
    {
        $isAssetStatusFieldRequired = FormField::whereHasMorph('category',[FormCategory::class], function ($query) {
                $query->where([['category', 'asset'], ['api_info', 'url:=/service-desk/api/dependency/asset_statuses;;'], ['default', 1]]);
            })->value('required_for_agent');

        if($this->assets()->count() && $isAssetStatusFieldRequired)
        {
            throw new Exception(trans('ServiceDesk::lang.cannot_delete_asset_status_when_associated',['attribute' => $this->name]));
        }
    }
}
