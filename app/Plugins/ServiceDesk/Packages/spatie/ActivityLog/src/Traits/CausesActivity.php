<?php

namespace App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Traits;

use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\ActivitylogServiceProvider;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait CausesActivity
{
    public function actions(): MorphMany
    {
        return $this->morphMany(
            ActivitylogServiceProvider::determineActivityModel(),
            'causer'
        );
    }
}
