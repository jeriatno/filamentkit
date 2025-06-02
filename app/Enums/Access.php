<?php

namespace App\Enums;

use App\Models\User\User;

enum Access
{
    const SUPER_ADMIN = 'super_admin';
    const LOGISTIC = 'logistic';
    const PARTNER = 'partner';

    public static function list()
    {
        return [
            self::SUPER_ADMIN,
            self::LOGISTIC,
            self::PARTNER,
        ];
    }

    /**
     * Employee list based on role
     */

    public static function Superadmin()
    {
        return User::whereHas('roles', function ($query) {
            $query->where('name', self::SUPER_ADMIN);
        })->pluck('id')->toArray();
    }

    public static function Logistic()
    {
        return User::whereHas('roles', function ($query) {
            $query->whereIn('name', self::LOGISTIC);
        })->pluck('id')->toArray();
    }

    public static function Partner()
    {
        return User::whereHas('roles', function ($query) {
            $query->where('name', self::PARTNER);
        })->pluck('id')->toArray();
    }
}
