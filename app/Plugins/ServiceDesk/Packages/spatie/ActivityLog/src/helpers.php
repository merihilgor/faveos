<?php

use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\ActivityLogger;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\ActivityLogStatus;

if (! function_exists('activity')) {
    function activity(string $logName = null): ActivityLogger
    {
        $defaultLogName = config('activitylog.default_log_name');

        $logStatus = app(ActivityLogStatus::class);

        return app(ActivityLogger::class)
            ->useLog($logName ?? $defaultLogName)
            ->setLogStatus($logStatus);
    }
}
