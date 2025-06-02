<?php

namespace App\Traits;

use App\User;
use Backpack\CRUD\Exception\AccessDeniedException;

trait HasAccess
{
    public function access($permission)
    {
        $permissions = auth()->user()->getAllPermissions();
        return $permissions->contains('name', $permission);
    }

    public function isPermission($user, $permission)
    {
        return $user->toPermission()->where('name', $permission)->exists();
    }

    public function isRole($user, $role)
    {
        return $user->toRole()->where('name', $role)->exists();
    }

    public function to($permission)
    {
        $request = Request();
        if (!$request->ajax()) {
            $allow = false;
            if ($this->isAdmin()) {
                $allow = true;
            } else {
                $permissions = auth()->user()->getAllPermissions();

                if (is_array($permission)) {
                    foreach ($permission as $perm) {
                        if ($permissions->contains('name', $perm)) {
                            $allow = true;
                            break;
                        }
                    }
                } else {
                    if ($permissions->contains('name', $permission)) {
                        $allow = true;
                    }
                }
            }

            if (!$allow) {
                abort(403, 'You do not have permission to access this page!');
            }
        }
    }

    /**
     * Admin access
     */
    public function isAdmin()
    {
        return auth()->user()->hasRole('Administrator');
    }

    public function toAdmin()
    {
        $request = Request();
        if (!$request->ajax()) {
            $allow = false;
            if ($this->isAdmin()) {
                $allow = true;
            }

            if (!$allow) {
                accessDenied(403, 'You do not have permission to access this page!');
            }
        }
    }

    public function role($roles)
    {
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if (auth()->user()->hasRole($role)) {
                    return true;
                }
            }
            return false;
        }

        return auth()->user()->hasRole($roles);
    }

    public function hasAccessOrFail($permissions, $permissionRequired = null)
    {
        if (!$this->hasAnyPermissions($permissions, $permissionRequired)) {
            throw new AccessDeniedException(trans('backpack::crud.unauthorized_access'));
        }

        return true;
    }

    public function hasAnyPermissions($permissions = null, $permissionRequired = null)
    {
        if ($this->isAdmin()) {
            return true;
        }

        if (!is_array($permissions)) {
            $permissions = [$permissions];
        }

        //            $hasAccess = false;
        //            $hasPermission = $this->hasAllowedRoles($permissions);
        //            if ($hasPermission) {
        //                return true;
        //            }

        $hasAccess = false;
        foreach ($permissions as $permission) {
            $hasPermission = $this->hasAllowedRoles($permission);

            if ($hasPermission) {
                $hasAccess = true;
                break;
            }
        }

        if ($permissionRequired !== null) {
            $hasRequired = $this->hasAllowedRoles($permissionRequired);

            if ($hasRequired) {
                return true;
            } else {
                $hasAccess = false;
            }
        }

        return $hasAccess;
    }

    //        public static function hasAccess($permissions = null)
    //        {
    //            if (!$permissions) {
    //                $permissions = [\App\Models\ForwardOrder\ForwardOrderRole::ACCESS];
    //            }
    //
    //            if (!is_array($permissions)) {
    //                $permissions = [$permissions];
    //            }
    //
    //            $hasAccess = false;
    //            foreach ($permissions[1] as $permission) {
    //                $permissionModel = Permission::where('name', $permission)->first();
    //
    //                dd($permission);
    //                // Check if the permission exists and if it has a roleList
    //                $hasAccess = $permissionModel && $permissionModel->roleList;
    //                if ($hasAccess) {
    //                    break;
    //                }
    //            }
    //
    //            return $hasAccess;
    //        }

    public function hasAllowedRoles($allowedRoles)
    {
        $usersByPermission = $this->getUsersByPermission($allowedRoles);
        $usersByRolePermission = $this->getUsersByRolePermission($allowedRoles);

        $mergedUsers = $usersByPermission->merge($usersByRolePermission);
        $userIds = $mergedUsers->pluck('id')->toArray();

        $loggedInUserId = auth()->id();
        $isUserInBothArrays = in_array($loggedInUserId, $userIds);

        return $isUserInBothArrays;
    }

    public function getUsersByPermission($permissionName)
    {
        if (!is_array($permissionName)) {
            $permissionName = [$permissionName];
        }

        return User::whereHas('permission', function ($query) use ($permissionName) {
            $query->whereIn('name', $permissionName);
        })->get();
    }

    public function getUsersByRolePermission($permissionName)
    {
        if (!is_array($permissionName)) {
            $permissionName = [$permissionName];
        }

        return User::whereHas('role.permission', function ($query) use ($permissionName) {
            $query->whereIn('name', $permissionName);
        })->get();
    }

    public function crud($crud, $permissions)
    {
        $actionMapping = [
            'add'    => 'create',
            'edit'   => 'update',
            'delete' => 'delete',
        ];

        foreach ($permissions as $permission) {
            foreach ($actionMapping as $key => $action) {
                if (strpos($permission, $key) !== false && !$this->permission($permission)) {
                    $crud->denyAccess($action);
                    break;
                }
            }
        }
    }

    public function permission($permissions)
    {
        $userPermissions = auth()->user()->getAllPermissions();

        if (is_array($permissions)) {
            foreach ($permissions as $permission) {
                if ($userPermissions->contains('name', $permission)) {
                    return true;
                }
            }
            return false;
        }

        return $userPermissions->contains('name', $permissions);
    }
}
