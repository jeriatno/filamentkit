<?php

use App\Models\Employee\Employee;
use App\Models\User\Permission;
use App\Models\User\Role;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

if (!function_exists('superadmin')) {
    /**
     */
    function superadmin(): string
    {
        return \App\Enums\Access::SUPER_ADMIN;
    }
}

if (!function_exists('access')) {
    /**
     * @return Authenticatable
     */
    function access(): Authenticatable
    {
        return auth()->user();
    }
}

if (!function_exists('isSuperAdmin')) {
    /**
     * Check is SuperAdmin
     */
    function isSuperAdmin()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        return access()->hasRole(superadmin());
    }
}

if (!function_exists('getRole')) {
    /**
     * Get role name based on user logged
     */
    function getRole()
    {
        return access()->getRoleNames()[0];
    }
}
