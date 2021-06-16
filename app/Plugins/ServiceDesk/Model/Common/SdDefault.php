<?php
namespace App\Plugins\ServiceDesk\Model\Common;
use Illuminate\Database\Eloquent\Model;

class SdDefault extends Model
{
    protected $table = 'sd_default';
    protected $fillable = ['asset_type_id'];
    
}