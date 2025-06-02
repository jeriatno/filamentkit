<?php


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

if (!function_exists('clearTable')) {
    /**
     * Truncate table
     *
     * @param $modelOrTableName
     * @param  string  $mode
     * @param  array  $conditions
     * @param  bool  $forceDelete
     * @return void
     */
    function clearTable($modelOrTableName, $mode = 'truncate', $conditions = [], $forceDelete = false): void
    {
        if(config('custom.clear_foreign')) {
            DB::statement("SET FOREIGN_KEY_CHECKS=0;");
        }

        if ($modelOrTableName instanceof \Illuminate\Database\Eloquent\Model) {
            $query = $modelOrTableName->query();

            foreach ($conditions as $condition) {
                if ($condition[1] === 'in') {
                    $query->whereIn($condition[0], $condition[2]);
                } else {
                    $query->where(...$condition);
                }
            }

            if ($mode === 'delete') {
                if ($forceDelete && method_exists($modelOrTableName, 'forceDelete')) {
                    $query->forceDelete();
                } else {
                    $query->delete();
                }
            } else {
                $query->truncate();
            }
        } else {
            $query = DB::table($modelOrTableName);

            foreach ($conditions as $condition) {
                if ($condition[1] === 'in') {
                    $query->whereIn($condition[0], $condition[2]);
                } else {
                    $query->where(...$condition);
                }
            }

            if ($mode === 'delete') {
                if ($forceDelete) {
                    $query->delete();
                } else {
                    $query->delete();
                }
            } else {
                $query->truncate();
            }
        }

        if(config('custom.clear_foreign')) {
            DB::statement("SET FOREIGN_KEY_CHECKS=1;");
        }
    }

    if (!function_exists('pathLast')) {
        /**
         * pathLast
         *
         * @return mixed|string
         */
        function pathLast()
        {
            $path = array_slice(explode('/', URL::current()), -1, 1);

            return $path[0];
        }
    }

    if (!function_exists('redirectTo')) {
        function redirectTo($model)
        {
            return redirect()->route('filament.admin.resources.'.$model.'.index');
        }
    }

    if (!function_exists('dropdownOptions')) {
        function dropdownOptions(Builder $query, string $label = 'name', string $value = 'id'): array
        {
            return $query
                ->whereNotNull($label)
                ->pluck($label, $value)
                ->toArray();
        }
    }

    if (!function_exists('parseDateRange')) {
        function parseDateRange($range): array
        {
            [$start, $end] = explode(' - ', $range);

            return [
                'start' => Carbon::createFromFormat('d/m/Y', trim($start))->startOfDay(),
                'end' => Carbon::createFromFormat('d/m/Y', trim($end))->endOfDay(),
            ];
        }
    }

    if (!function_exists('isEnv')) {
        /**
         * check environment
         * @return string|null
         */
        function isEnv(): ?string
        {
            $env = null;
            if (config('app.env') != 'production') {
                $env = '['.substr(strtoupper(config('app.env')), 0, 3).'] ';
            }

            return $env;
        }
    }

    if (!function_exists('___')) {
        /**
         * Replace placeholders in a string with actual values.
         *
         * @param  string  $template
         * @param  array  $replacements
         * @return string
         */
        function ___(string $template, array $replacements = []): string
        {
            $template = __($template);

            $placeholders = collect($replacements)
                ->mapWithKeys(fn($value, $key) => [":$key" => $value])
                ->toArray();

            return strtr($template, $placeholders);
        }
    }
}
