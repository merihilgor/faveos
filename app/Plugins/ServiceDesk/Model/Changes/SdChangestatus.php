<?php
namespace App\Plugins\ServiceDesk\Model\Changes;
use Illuminate\Database\Eloquent\Model;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Traits\LogsActivity;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Models\Activity;

class SdChangestatus extends Model
{
	// trait to log change status activity
    use LogsActivity;

    protected $table = 'sd_change_status';
    protected $fillable = ['id','name','created_at','updated_at'];

    // Activity log name for changes
    protected static $logName = 'change_status';

    protected static $submitEmptyLogs = false;

    protected static $logOnlyDirty = true;

    // Attributes included inside change activity log 
    protected static $logAttributes = ['name'];

       /**
     * generates activity log for change status
     * @param $evenName
     * @return $eventName
    */
    public function getDescriptionForEvent(string $eventName): string
    {
        if ($eventName == 'created') {
            return 'Change Status  <strong> $this->name </strong> was created';
        }

        if ($eventName == 'updated') {
            return 'Change Status <strong> $this->name </strong> was updated';
        }

        if ($eventName == 'deleted') {
            return 'Change Status <strong> $this->name </strong> was deleted';
        }

        return '';
    }


}