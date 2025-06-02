<?php

namespace App\Traits;

use App\Models\SettingStatusLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait StatusLoggable
{
    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Get log status mapping.
     *
     * @return array
     */
    protected function getLogStatus(): array
    {
        return property_exists($this, 'actionStatus') ? $this->actionStatus : [];
    }

    /**
     * Get log event mapping.
     *
     * @return array
     */
    protected function getLogEvent(): array
    {
        return property_exists($this, 'actionEvent') ? $this->actionEvent : [];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function statusLogs(): MorphMany
    {
        return $this->morphMany(SettingStatusLog::class, 'loggable');
    }

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function insertLog($action, $params = []): Model
    {
        $logStatus = $this->getLogStatus();
        $logDesc = $this->getLogEvent();
        $isReverse = isset($params['isReverse']) ?? false;

        if ($isReverse) {
            $status = $action;
            $action = array_search($action, $logStatus);
            $description = $logDesc[$action] ?? 'Status updated';
        } else {
            $status = $logStatus[$action] ?? null;
            $description = $logDesc[$action] ?? 'Action logged';
        }

        return $this->statusLogs()->create([
            'module'      => self::app,
            'action'      => $action,
            'status'      => $status,
            'description' => $description,
            'notes'       => $params['notes'] ?? null,
            'created_by'  => auth()->id(),
        ]);
    }

    public function currentLog($column = null)
    {
        $log = SettingStatusLog::where('module', self::app)
            ->whereLoggable(self::class, $this->id)
            ->orderByDesc('id')
            ->first();

        if($column) {
            return $log->{$column};
        }

        return $log;
    }

    public function showHistories($id)
    {
        return SettingStatusLog::where('module', self::app)
            ->where('loggable_type', self::class)
            ->where('loggable_id', $id)
            ->latest()
            ->get();
    }

    public function showHistoryByAction($id, $action = [])
    {
        return SettingStatusLog::where('module', self::app)
            ->where('loggable_type', self::class)
            ->where('loggable_id', $id)
            ->whereIn('action', $action)
            ->get();
    }
}
