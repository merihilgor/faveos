<?php

namespace App\Plugins\ServiceDesk\Model\Report;

use Illuminate\Database\Eloquent\Model;
use App\Plugins\ServiceDesk\Model\Report\SdReportFilterMeta;
use App\Traits\Observable;
use App\User;

class SdReportFilter extends Model
{
    use Observable;

    protected $table = 'sd_report_filters';
    protected $fillable = ['name', 'description', 'type', 'creator_id'];

    /**
     * Relation with report filter meta
     */
    public function filterMeta()
    {
        return $this->hasMany(SdReportFilterMeta::class, 'report_filter_id', 'id');
    }

    /**
     * Gets filter data in key value pair by filter Id
     * @param  int $filterId
     * @return array
     */
    public static function getFilterParametersByFilterId(int $filterId) : array
    {
      return SdReportFilterMeta::where('report_filter_id', $filterId)->get(['key','value'])
        ->map(function($element){
          return [ $element->key => $element->value ];
        })->collapse()->toArray();
    }

    /**
     * delete report filter
     * @param $model
     * @return 
     */
    public function beforeDelete($model)
    {
      $reportFilterMetaFields = $model->filterMeta()->get();
      // detach all attached assets
      foreach ($reportFilterMetaFields as $reportFilterMetaField) {
          $reportFilterMetaField->delete();
      }
    }

    /**
     * relationship with user for report filter creation
     */
    public function ReportFilterCreator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }


}