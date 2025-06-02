<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[{"name":"super_admin","guard_name":"web","permissions":[
            "view_role",
            "create_role",
            "update_role",
            "delete_role",
            "view_goods::incoming::goods",
            "create_goods::incoming::goods",
            "update_goods::incoming::goods",
            "delete_goods::incoming::goods",
            "view_goods::outcoming::goods",
            "create_goods::outcoming::goods",
            "update_goods::outcoming::goods",
            "delete_goods::outcoming::goods",
            "view_master::partner",
            "create_master::partner",
            "update_master::partner",
            "delete_master::partner",
            "view_master::rack",
            "create_master::rack",
            "update_master::rack",
            "delete_master::rack",
            "view_master::rate",
            "create_master::rate",
            "update_master::rate",
            "delete_master::rate",
            "view_master::warehouse",
            "create_master::warehouse",
            "update_master::warehouse",
            "delete_master::warehouse",
            "view_rent::in",
            "create_rent::in",
            "update_rent::in",
            "delete_rent::in",
            "view_rent::out",
            "create_rent::out",
            "update_rent::out",
            "delete_rent::out",
            "view_user",
            "create_user",
            "update_user",
            "delete_user"
        ]}]';
        $directPermissions = '[]';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn ($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
}
