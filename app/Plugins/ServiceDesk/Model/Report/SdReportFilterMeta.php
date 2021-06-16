<?php

namespace App\Plugins\ServiceDesk\Model\Report;

use Illuminate\Database\Eloquent\Model;
use App\Plugins\ServiceDesk\Model\Report\SdReportFilter;

class SdReportFilterMeta extends Model
{
    public $timestamps = false;
    protected $table = 'sd_report_filter_metas';
    protected $fillable = ['report_filter_id', 'key', 'value_meta', 'value'];

    public function getValueAttribute($value)
    {
        return unserialize($value);
    }

    public function setValueAttribute($value)
    {
        $this->attributes['value'] = serialize($value);
    }

    public function getValueMetaAttribute($value)
    {
        return unserialize($value);
    }

    public function setValueMetaAttribute($value)
    {
        $this->attributes['value_meta'] = serialize($value);
    }

    public function reportFilter()
    {
        $this->belongsTo(SdReportFilter::class);
    }
}