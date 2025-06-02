<?php

use App\Models\SettingNumbering;
use Illuminate\Database\Seeder;

class SettingNumberingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'module'  => SettingNumbering::module['warehouse'],
                'for'     => SettingNumbering::for['warehouse'],
                'format'  => '[i]',
                'prefix'  => 'W',
                'clause'  => SettingNumbering::DEFAULT,
                'example' => 'W1',
            ],
            [
                'module'  => SettingNumbering::module['rate'],
                'for'     => SettingNumbering::for['rate'],
                'format'  => '[iiii]',
                'prefix'  => 'R',
                'clause'  => SettingNumbering::DEFAULT,
                'example' => 'R0001',
            ],
            [
                'module'  => SettingNumbering::module['rent_in'],
                'for'     => SettingNumbering::for['rent_in'],
                'format'  => '[iiiiii]',
                'prefix'  => 'RIN',
                'clause'  => SettingNumbering::DEFAULT,
                'example' => 'RIN000001',
            ],
            [
                'module'  => SettingNumbering::module['rent_out'],
                'for'     => SettingNumbering::for['rent_out'],
                'format'  => '[iiiiii]',
                'prefix'  => 'ROUT',
                'clause'  => SettingNumbering::DEFAULT,
                'example' => 'ROUT000001',
            ],
        ];

        foreach ($data as $item) {
            $globalNumber = SettingNumbering::where('module', $item['module'])->first();

            if (!$globalNumber) {
                SettingNumbering::create($item);
            }
        }
    }
}
