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
            'rent::in',
            'rent::out',
            'goods::incoming::goods',
            'goods::outcoming::goods',
            'master::partner',
            'master::rack',
            'master::rate',
            'master::warehouse',
            'role',
            'user',
        ];
    }
}
