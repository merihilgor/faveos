<?php
namespace App\Plugins\ServiceDesk\Model\Common;
use Illuminate\Database\Eloquent\Model;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Traits\LogsActivity;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Models\Activity;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\ActivityLogger;
use App\Plugins\ServiceDesk\Model\Assets\SdAssets;
use App\Plugins\ServiceDesk\Model\Changes\SdChanges;
use App\Model\helpdesk\Ticket\Tickets as Ticket;
use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Models\ActivityBatch;
use Auth;
use App\User;
use App\Traits\Observable;

class GeneralInfo extends Model
{
	use Observable;

    protected $table = 'sd_gerneral';
    protected $fillable = ['owner','key','value'];

    // Activity log name for changes
    protected static $logName = 'popup_update';

    protected static $submitEmptyLogs = false;

    // Attributes included inside popup_update activity log 
    protected static $logAttributes = ['value'];

    /**
     * fetch only changed atrributes not all attributes if logOnlyDirty is true
     * fetch all attributes even which are not changes if logOnlyDirty is false
     */
    protected static $logOnlyDirty = true;
    
    
}