<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SettingEnvironment extends Model
{
    use SoftDeletes;

    protected $table = 'setting_environments';
    public $guarded = [
        "created_at",
        "updated_at"
    ];

    public static function getValueByKey($key)
    {
        $globalEnvironment = self::where('key_name', $key)->first();

        return $globalEnvironment ? $globalEnvironment->value : null;
    }

    public static function getValueByKeyAndAppName($key, $appName)
    {
        $globalEnvironment = self::where('key_name', $key)
            ->where('application_name', $appName)
            ->first();

        return $globalEnvironment ? $globalEnvironment->value : null;
    }

    public static function getEverythingByKeyAndAppName($key, $appName)
    {
        $globalEnvironment = self::where('key_name', $key)
            ->where('application_name', $appName)
            ->get()
            ->toArray();

        return $globalEnvironment ? $globalEnvironment: null;
    }

     public static function findByKey($appName, $key, $val = null)
    {
        $globalEnvironment = self::where('key_name', $key)
            ->where('application_name', $appName);

        if (isset($val)) {
            $globalEnvironment->where('value', $val);
        }

        return $globalEnvironment->first() ?? null;
    }

    public static function getByKeys($appName, $key, $orderBy = null)
    {
        $globalEnvironment = self::where('key_name', $key)
            ->where('application_name', $appName);

        if (isset($orderBy)) {
            $globalEnvironment->orderBy('created_at', $orderBy);
        }

        return $globalEnvironment->get()->toArray() ?? null;
    }
}
