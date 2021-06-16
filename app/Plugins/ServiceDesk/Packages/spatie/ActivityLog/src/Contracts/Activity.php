<?php

namespace App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Contracts;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;

interface Activity
{
    public function source(): MorphTo;

    public function causer(): MorphTo;

    public function changes(): Collection;

    public function scopeInLog(Builder $query, ...$logNames): Builder;

    public function scopeCausedBy(Builder $query, Model $causer): Builder;

    public function scopeForSource(Builder $query, Model $source): Builder;
}
