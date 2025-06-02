<?php

namespace App\Utils;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTime;

class DateFormatter
{
    public static function convertExcelDate($excelDate): string
    {
        return Carbon::createFromFormat('Y-m-d', '1899-12-30')
            ->addDays($excelDate)
            ->format('Y-m-d');
    }

    /**
     * Format a date using ISO
     * @param $date
     * @param  null  $format
     * @return string
     */
    public static function dateAt($date, $format = null): string
    {
        return Carbon::parse($date)->format($format ?? 'd F Y');
    }

    public static function getWeeksInYear($year)
    {
        $date = new DateTime();
        $date->setISODate($year, 53);
        return $date->format("W") === "53" ? 53 : 52;
    }

    public static function getWeekDates(string $dateFilter = null): array
    {
        $date = $dateFilter ?? Carbon::now();
        $startOfWeek = Carbon::parse($date)->startOfWeek();
        $endOfWeek = Carbon::parse($date)->endOfWeek();
        $datesInWeek = CarbonPeriod::create($startOfWeek, $endOfWeek)->toArray();

        return [
            'start_of_week' => $startOfWeek,
            'end_of_week' => $endOfWeek,
            'dates_in_week' => $datesInWeek,
        ];
    }
}
