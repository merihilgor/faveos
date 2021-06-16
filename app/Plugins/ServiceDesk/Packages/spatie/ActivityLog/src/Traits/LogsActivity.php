<?php

namespace App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\ActivityLogger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\ActivitylogServiceProvider;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Models\Activity;
use App\Plugins\ServiceDesk\Packages\spatie\ActivityLog\src\Models\ActivityBatch;

trait LogsActivity
{
    use DetectsChanges;

    protected $enableLoggingModelsEvents = true;

    protected static function bootLogsActivity()
    {
        static::eventsToBeRecorded()->each(function ($eventName) {
            return static::$eventName(function (Model $model) use ($eventName) {
                if (! $model->shouldLogEvent($eventName)) {
                    return;
                }

                $logName = $model->getLogNameToUse($eventName);

                $attrs = $model->attributeValuesToBeLogged($eventName);

                if ($model->isLogEmpty($attrs) && ! $model->shouldSubmitEmptyLogs()) {
                    return;
                }
                
                $batch = ActivityBatch::create(['log_name' => $logName])->id;

                foreach ($attrs['attributes'] as $attributeKey => $attributeValue) {
                    if (is_null($attributeValue) && $eventName != 'updated') {
                            continue;
                        } 
                        $isRelation = (int) method_exists($model,$attributeKey);

                    $logger = app(ActivityLogger::class)
                        ->useBatch($batch)
                        ->useLog($logName)
                        ->performedOn($model)
                        ->useIsRelation($isRelation)
                        ->useFieldOrRelation($attributeKey)
                        ->useFinalValue($attributeValue)
                        ->useEventName($eventName);

                    if (method_exists($model, 'tapActivity')) {
                        $logger->tap([$model, 'tapActivity'], $eventName);
                    }
                    if ((array_key_exists('old', $attrs) && $attrs['old'][$attributeKey])&& ($attrs['old'][$attributeKey] != $attrs['attributes'][$attributeKey])) {
                        $logger = $logger->useInitialValue($attrs['old'][$attributeKey]);
                    }

                    $logger->log();
                }
            });
        });
    }

    public function shouldSubmitEmptyLogs(): bool
    {
        return ! isset(static::$submitEmptyLogs) ? true : static::$submitEmptyLogs;
    }

    public function isLogEmpty($attrs): bool
    {
        return empty($attrs['attributes'] ?? []) && empty($attrs['old'] ?? []);
    }

    public function disableLogging()
    {
        $this->enableLoggingModelsEvents = false;

        return $this;
    }

    public function enableLogging()
    {
        $this->enableLoggingModelsEvents = true;

        return $this;
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(ActivitylogServiceProvider::determineActivityModel(), 'source');
    }

    public function getLogNameToUse(string $eventName = ''): string
    {
        if (isset(static::$logName)) {
            return static::$logName;
        }

        return config('activitylog.default_log_name');
    }

    /*
     * Get the event names that should be recorded.
     */
    protected static function eventsToBeRecorded(): Collection
    {
        if (isset(static::$recordEvents)) {
            return collect(static::$recordEvents);
        }

        $events = collect([
            'created',
            'updated',
        ]);

        if (collect(class_uses_recursive(static::class))->contains(SoftDeletes::class)) {
            $events->push('restored');
        }

        return $events;
    }

    public function attributesToBeIgnored(): array
    {
        if (! isset(static::$ignoreChangedAttributes)) {
            return [];
        }

        return static::$ignoreChangedAttributes;
    }

    protected function shouldLogEvent(string $eventName): bool
    {
        if (! $this->enableLoggingModelsEvents) {
            return false;
        }

        if (! in_array($eventName, ['created', 'updated'])) {
            return true;
        }

        if (Arr::has($this->getDirty(), 'deleted_at')) {
            if ($this->getDirty()['deleted_at'] === null) {
                return false;
            }
        }

        //do not log update event if only ignored attributes are changed
        return (bool) count(Arr::except($this->getDirty(), $this->attributesToBeIgnored()));
    }

}
