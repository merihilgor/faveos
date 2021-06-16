<?php

namespace App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Models;

use Illuminate\Database\Eloquent\Model;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Models\Activity;

class ActivityBatch extends Model
{
    protected $table = 'sd_activity_batches';
    protected $fillable = ['id', 'log_name'];

    /**
     * relation with activity
     */
    public function activity()
    {
        return $this->hasMany(Activity::class, 'batch');
    }

}
