<?php

namespace App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\Model;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Contracts\Activity;

class InvalidConfiguration extends Exception
{
    public static function modelIsNotValid(string $className)
    {
        return new static("The given model class `$className` does not implement `".Activity::class.'` or it does not extend `'.Model::class.'`');
    }
}
