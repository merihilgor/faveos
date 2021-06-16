<?php

namespace App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src;

use App\Plugins\ServiceDesk\Packages\spatie\string\src\Str;
use Illuminate\Support\Arr;
use Illuminate\Auth\AuthManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Contracts\Config\Repository;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Exceptions\CouldNotLogActivity;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Contracts\Activity as ActivityContract;

class ActivityLogger
{
    use Macroable;

    /** @var \Illuminate\Auth\AuthManager */
    protected $auth;

    protected $defaultLogName = '';

    /** @var string */
    protected $authDriver;

    /** @var \Spatie\Activitylog\ActivityLogStatus */
    protected $logStatus;

    /** @var \Spatie\Activitylog\Contracts\Activity */
    protected $activity;

    public function __construct(AuthManager $auth, Repository $config, ActivityLogStatus $logStatus)
    {
        $this->auth = $auth;

        $this->authDriver = $config['activitylog']['default_auth_driver'] ?? $auth->getDefaultDriver();

        $this->defaultLogName = $config['activitylog']['default_log_name'];

        $this->logStatus = $logStatus;
    }

    public function setLogStatus(ActivityLogStatus $logStatus)
    {
        $this->logStatus = $logStatus;

        return $this;
    }

    public function performedOn(Model $model)
    {
        $this->getActivity()->source()->associate($model);

        return $this;
    }

    public function on(Model $model)
    {
        return $this->performedOn($model);
    }

    public function causedBy($modelOrId)
    {
        if ($modelOrId === null) {
            return $this;
        }

        $model = $this->normalizeCauser($modelOrId);

        $this->getActivity()->causer()->associate($model);

        return $this;
    }

    public function by($modelOrId)
    {
        return $this->causedBy($modelOrId);
    }

    public function useLog(string $logName)
    {
        $this->getActivity()->log_name = $logName;

        return $this;
    }

    public function inLog(string $logName)
    {
        return $this->useLog($logName);
    }

    public function tap(callable $callback, string $eventName = null)
    {
        call_user_func($callback, $this->getActivity(), $eventName);

        return $this;
    }

    public function enableLogging()
    {
        $this->logStatus->enable();

        return $this;
    }

    public function disableLogging()
    {
        $this->logStatus->disable();

        return $this;
    }

    public function log()
    {
        if ($this->logStatus->disabled()) {
            return;
        }

        $activity = $this->activity;

        $activity->save();

        $this->activity = null;

        return $activity;
    }

    protected function normalizeCauser($modelOrId): Model
    {
        if ($modelOrId instanceof Model) {
            return $modelOrId;
        }

        $guard = $this->auth->guard($this->authDriver);
        $provider = method_exists($guard, 'getProvider') ? $guard->getProvider() : null;
        $model = method_exists($provider, 'retrieveById') ? $provider->retrieveById($modelOrId) : null;

        if ($model instanceof Model) {
            return $model;
        }

        throw CouldNotLogActivity::couldNotDetermineUser($modelOrId);
    }

    protected function getActivity(): ActivityContract
    {
        if (! $this->activity instanceof ActivityContract) {
            $this->activity = ActivitylogServiceProvider::getActivityModelInstance();
            $this
                ->useLog($this->defaultLogName)
                ->useFieldOrRelation('')
                ->useInitialValue('')
                ->useFinalValue('')
                ->causedBy($this->auth->guard($this->authDriver)->user());
        }

        return $this->activity;
    }

    public function useBatch(int $batch)
    {
        $this->getActivity()->batch = $batch;

        return $this;
    }

    public function useFieldOrRelation(string $fieldOrRelationName)
    {
        $this->getActivity()->field_or_relation = $fieldOrRelationName;

        return $this;
    }

    public function useInitialValue($initialValue)
    {
        $this->getActivity()->initial_value = $initialValue;

        return $this;
    }

    public function useFinalValue($finalValue)
    {
        $this->getActivity()->final_value = $finalValue;

        return $this;
    }

    public function useEventName(string $eventName)
    {
        $this->getActivity()->event_name = $eventName;

        return $this;
    }

    public function useIsRelation(int $value)
    {
        $this->getActivity()->is_relation = $value;

        return $this;
    }

}
