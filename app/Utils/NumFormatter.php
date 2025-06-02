<?php

namespace App\Utils;

class NumFormatter
{
    public static function toAmount($number)
    {
        return (float)str_replace(',', '', $number);
    }

    public static function toCurrency($amount, $curr = null)
    {
        $decimal = $curr === 'IDR' ? 0 : 2;

        return $curr.' <span style="float:right">'.number_format((float) $amount, $decimal).'</span>';
    }

    public static function toFloat($amount, $decimal = 0)
    {
        return '<span style="float:right">'.number_format((float) $amount, $decimal).'</span>';
    }

    public static function toDecimal($amount, $decimal = 0)
    {
        return number_format((float) $amount, $decimal);
    }

    public static function formatAmount($amount, $currency)
    {
        return ($currency === 'IDR') ? floatval($amount) : number_format(floatval($amount), 2, '.', '');
    }

    public static function numberPlain($amount): float
    {
        $numberText = str_replace(',', '', $amount);
        return floatval($numberText);
    }
}
