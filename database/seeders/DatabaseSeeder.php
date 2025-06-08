<?php

namespace Database\Seeders;

use App\Models\User\Role;
use App\Models\User\User;
use Database\Seeders\Settings\GeneralSettingSeeder;
use Database\Seeders\Settings\MailSettingSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create role
        $role = Role::create([
            'name' => 'super_admin',
        ]);

        // Create user
         $user = User::create([
             'name' => 'Superadmin',
             'email' => 'admin@example.com',
             'password' => Hash::make('password'),
             'email_verified_at' => now()
         ]);

        // Assign role
        $user->assignRole($role->name);

        // Assign permission
        $this->call([
            ShieldSeeder::class,
            GeneralSettingSeeder::class,
            MailSettingSeeder::class
        ]);

        Artisan::call('shield:generate --all');
    }
}
