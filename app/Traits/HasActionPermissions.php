<?php

namespace App\Traits;

trait HasActionPermissions
{
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'create',
            'update',
            'delete',
        ];
    }

    public static function allowedResources()
    {
        return [
            'dashboard',
            'role',
            'user',
        ];
    }
}
