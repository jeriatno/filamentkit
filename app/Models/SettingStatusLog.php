<?php

namespace App\Models;

use App\User;
use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SettingStatusLog extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */
    protected $table = 'setting_status_logs';
    protected $guarded = ['id'];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function loggable()
    {
        return $this->morphTo();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    /**
     * Scope a query to filter by polymorphic relationship.
     */
    public function scopeWhereLoggable($query, $type, $id): Builder
    {
        return $query->where(function ($query) use ($type, $id) {
            $query->where("loggable_type", $type)
                ->where("loggable_id", $id);
        });
    }

    public function scopeWhereAction($query, $data, $action = [])
    {
        return $query->with('createdBy')
            ->where('module', get_class($data)::app)
            ->where("loggable_type", get_class($data))
            ->where("loggable_id", $data->id)
            ->whereIn("action", $action);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
