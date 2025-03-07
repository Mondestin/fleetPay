<?php

namespace Database\Seeders;

use App\Models\PlatformEarning;
use Illuminate\Database\Seeder;

class PlatformEarningSeeder extends Seeder
{
    public function run(): void
    {
        $earnings = [
            [
                'id' => 'pe1',
                'driver_id' => 'd1',
                'platform' => 'bolt',
                'week_start_date' => '2024-02-17',
                'earnings' => 450.50,
                'commission_amount' => 100,
                'created_by' => 'user1',
                'status' => 'pending',
                'validated' => true,
            ],
            [
                'id' => 'pe2',
                'driver_id' => 'd1',
                'platform' => 'uber',
                'week_start_date' => '2024-02-17',
                'earnings' => 380.75,
                'commission_amount' => 100,
                'created_by' => 'user1',
                'status' => 'paid',
                'validated' => true,
            ],
            [
                'id' => 'pe3',
                'driver_id' => 'd2',
                'platform' => 'heetch',
                'week_start_date' => '2024-02-17',
                'earnings' => 315.75,
                'commission_amount' => 100,
                'created_by' => 'user1',
                'status' => 'pending',
                'validated' => false,
            ],
        ];

        foreach ($earnings as $earning) {
            PlatformEarning::create($earning);
        }
    }
} 