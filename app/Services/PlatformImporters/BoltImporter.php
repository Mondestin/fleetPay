<?php

namespace App\Services\PlatformImporters;

use App\Models\Driver;
use App\Models\PlatformEarning;
use App\Models\Setting;

class BoltImporter implements PlatformImporterInterface
{
    public function importDriver(array $driverData): Driver
    {
        return Driver::firstOrCreate(
            ['email' => $driverData['email']],
            [
                'first_name' => $driverData['firstName'],
                'last_name' => $driverData['lastName'],
                'full_name' => $driverData['fullName'],
                'phone_number' => $driverData['phoneNumber'] ?? null
            ]
        );
    }

    public function importEarnings(Driver $driver, array $earningData): PlatformEarning
    {
        $commission = Setting::where('name', 'commission')->first()->value;

        return PlatformEarning::firstOrCreate(
            [
                'driver_id' => $driver->id,
                'week_start_date' => $earningData['weekDate'],
                'platform' => 'bolt'
            ],
            [
                'created_by' => 'user1',
                'earnings' => $earningData['totalRevenue'] ?? 0,
                'commission_amount' => $commission,
                'validated' => 1,
                'status' => 'pending'
            ]
        );
    }
} 