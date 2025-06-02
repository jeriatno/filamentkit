<?php

namespace App\Traits;

trait StatsConfig
{
    /**
     * Get the stats configuration.
     * @param  array  $params
     * @return array
     */
    public static function getStatsConfig(array $params = []): array
    {
        $statusList = defined('self::STATUS_STATS') ? self::STATUS_STATS : self::STATUS_LIST;

        return array_map(function ($status) use ($statusList, $params) {
            return [
                'theme'   => self::STATUS_BADGE[$status] ?? 'bg-primary',
                'icon'    => self::STATUS_ICONS[$status],
                'label'   => str_replace('_', ' ', $status),
                'number'  => str_replace(' ', '_', strtolower($status)),
                'subnumber'  => 'sub_'.str_replace(' ', '_', strtolower($status)),
                'onClick' => false,
                'useGradient' => false
            ];
        }, $statusList);
    }
}
