<?php

namespace App\Utils;

use App\Models\Master\Rate;
use App\Models\Master\Warehouse;
use App\Models\Rent\RentIn;
use App\Models\Rent\RentOut;
use App\Models\SettingNumbering;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GenerateNumber
{
    /*
    |--------------------------------------------------------------------------
    | DOCUMENT NUMBER CONVERSION
    |--------------------------------------------------------------------------
    */
    public static function convert($forType, $defaultFormat = null, $number = null, $withKey = null, $withIncrement = false, $withTrashed = null)
    {
        $for = SettingNumbering::for[$forType];
        $module = SettingNumbering::module[$forType];
        $model = new SettingNumbering::$forModel[$forType];
        $date = Carbon::now();

        // get global numbering
        $data = SettingNumbering::query()
            ->where('module', $module)
            ->where('for', $for)
            ->where('is_active', 1)
            ->first();

        // get format & prefix
        $format = $data->format ?? $defaultFormat;
        $prefix = $data->prefix ?? null;

        // get key
        if ($withKey == SettingNumbering::ROMAN) {
            $yearKey = roman();
        } elseif ($withKey == SettingNumbering::ALPHABET) {
            $yearKey = getAlphabetFromYear($date->format('Y'));
        } elseif ($withKey == SettingNumbering::FORMAT) {
            $yearKey = $date->format('Y');
        } else {
            $yearKey = $date->format('y');
        }

        if ($withKey == SettingNumbering::FORMAT) {
            $monthKey = $date->format('m');
        } else {
            $monthKey = $date->format('n');
        }

        // get sequence number
        if ($withKey == SettingNumbering::RESET) {
            $year = $date->format('Y');
            $monthKey = $date->format('m');
            $dateFormat = $prefix.substr($year, 2).$monthKey;

            $splitFormat = preg_split('/(\[[^\]]+\])/', $format, -1, PREG_SPLIT_DELIM_CAPTURE);
            $splitFormat = array_filter($splitFormat, function($value) {
                return !empty($value);
            });

            $splitFormat = array_values($splitFormat);

            $paddingSection = collect($splitFormat)->first(function ($segment) {
                return str_contains($segment, 'i');
            });

            $countPadding = $paddingSection ? substr_count($paddingSection, 'i') : 1;

            $lastRecord = $model::where(DB::raw("LEFT($for, " . strlen($dateFormat) . ")"), $dateFormat)
                ->orderBy(DB::raw("RIGHT($for, $countPadding)"), 'desc')
                ->first();

            if ($lastRecord) {
                $lastSequence = (int)substr($lastRecord->$for, -$countPadding);
                $seqNumber = $lastSequence + 1;
            } else {
                $seqNumber = 1;
            }
        } else {
            if ($withIncrement) {
                $latestModel = $model->orderBy('id', 'desc')->first();

                if ($latestModel) {
                    $seqNumber = $latestModel->id + 1;
                } else {
                    $seqNumber = 1;
                }
            } else {
                if($number) {
                    $seqNumber = $number;
                } else {
                    if ($withTrashed) {
                        $seqNumber = $model->query()->withTrashed()->count('id') + 1;
                    } else {
                        $seqNumber = $model->query()->count('id') + 1;
                    }
                }
            }
        }

        return $prefix . str_replace(
                ['[d]', '[m]', '[Y]', '[i]', '[ii]', '[iii]', '[iiii]', '[iiiii]', '[iiiiii]', '[iiiiiii]', '[iiiiiiii]'],
                [
                    $date->format('d'),
                    $monthKey ?? null,
                    $yearKey ?? null,
                    str_pad($seqNumber, 1, '', STR_PAD_LEFT),
                    str_pad($seqNumber, 2, '0', STR_PAD_LEFT),
                    str_pad($seqNumber, 3, '0', STR_PAD_LEFT),
                    str_pad($seqNumber, 4, '0', STR_PAD_LEFT),
                    str_pad($seqNumber, 5, '0', STR_PAD_LEFT),
                    str_pad($seqNumber, 6, '0', STR_PAD_LEFT),
                    str_pad($seqNumber, 7, '0', STR_PAD_LEFT),
                    str_pad($seqNumber, 8, '0', STR_PAD_LEFT),
                ],
                $format
            );
    }

    /*
    |--------------------------------------------------------------------------
    | REGISTER DOCUMENT NUMBER
    |--------------------------------------------------------------------------
    */
    public static function get($class)
    {
        return SettingNumbering::where('module', $class)->where('is_active', 1)->first();
    }

    public static function generate($model, $number, $withKey, $withIncrement)
    {
        if ($model instanceof Warehouse) {
            return self::warehouse($number, $withKey, $withIncrement);
        } elseif ($model instanceof Rate) {
            return self::rate($number, $withKey, $withIncrement);
        } elseif ($model instanceof RentIn) {
            return self::rentIn($number, $withKey, $withIncrement);
        } elseif ($model instanceof RentOut) {
            return self::rentOut($number, $withKey, $withIncrement);
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | SPECIFIC DOCUMENT FORMATS
    |--------------------------------------------------------------------------
    */
    public static function warehouse($number = null, $withKey = null, $withIncrement = true)
    {
        return self::convert('warehouse', '[i]', $number, $withKey, $withIncrement);
    }

    public static function rate($number = null, $withKey = null, $withIncrement = true)
    {
        return self::convert('rate', '[iiii]', $number, $withKey, $withIncrement);
    }

    public static function rentIn($number = null, $withKey = null, $withIncrement = true)
    {
        return self::convert('rent_in', '[iiiiii]', $number, $withKey, $withIncrement);
    }

    public static function rentOut($number = null, $withKey = null, $withIncrement = true)
    {
        return self::convert('rent_out', '[iiiiii]', $number, $withKey, $withIncrement);
    }
}
