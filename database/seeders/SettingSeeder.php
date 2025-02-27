<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
   
        $settings = [
            [
                'id' => 's1',
                'user_id' => 'user1',
                'name' => 'commission',
                'value' => '50.00'
            ]
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
} 