<?php

namespace Database\Seeders\Settings;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class MailSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'from_address' => 'noreply@example.com',
            'from_name' => 'Filament Kit',
            'driver' => 'smtp',
            'host' => 'smtp.example.com',
            'port' => 587,
            'encryption' => 'tls',
            'username' => 'your-username',
            'password' => 'your-password',
            'timeout' => 5,
            'local_domain' => null,
        ];

        foreach ($settings as $key => $value) {
            Setting::firstOrCreate([
                'group' => 'mail',
                'name' => $key,
            ], [
                'payload' => json_encode($value),
            ]);
        }
    }
}