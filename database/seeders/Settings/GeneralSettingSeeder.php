<?php

namespace Database\Seeders\Settings;

use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class GeneralSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'brand_name' => 'Filament Kit',
            'brand_logo' => 'sites/logo.png',
            'brand_logoHeight' => '40px !important',
            'site_active' => true,
            'site_favicon' => 'sites/logo.ico',
        ];

        foreach ($settings as $name => $value) {
            Setting::firstOrCreate([
                'group' => 'general',
                'name' => $name,
            ], [
                'payload' => json_encode($value),
            ]);
        }
    }
}