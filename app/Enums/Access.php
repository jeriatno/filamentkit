<?php

namespace App\Enums;

use App\Models\User\User;

enum Access
{
    const SUPER_ADMIN = 'super_admin';

    public static function list()
    {
        return [
            self::SUPER_ADMIN,
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
}
