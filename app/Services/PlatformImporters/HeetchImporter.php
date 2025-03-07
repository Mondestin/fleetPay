<?php

namespace App\Services\PlatformImporters;

use App\Models\Driver;
use App\Models\PlatformEarning;
use App\Models\Setting;
use App\Helpers\NameCheck;

class HeetchImporter implements PlatformImporterInterface
{
    public function importDriver(array $driverData): Driver
    {
        $fullName = $driverData['fullName'];
        $found = false;
        
        // Check existing drivers for name match
        foreach (Driver::all() as $existingDriver) {
            if (NameCheck::matchName($fullName, $existingDriver->full_name)) {
                return $existingDriver;
            }
        }

        // Create new driver if no match found
        $nameParts = explode(' ', $fullName, 3);
        return Driver::create([
            'first_name' => $nameParts[0],
            'last_name' => ($nameParts[1] ?? '') . ' ' . ($nameParts[2] ?? ''),
            'full_name' => $fullName,
            'email' => $driverData['email'] ?? null,
            'phone_number' => $driverData['phoneNumber'] ?? null
        ]);
    }

    public function importEarnings(Driver $driver, array $earningData): PlatformEarning
    {
        $commission = Setting::where('name', 'commission')->first()->value;

        return PlatformEarning::firstOrCreate(
            [
                'driver_id' => $driver->id,
                'week_start_date' => $earningData['weekDate'],
                'platform' => 'heetch'
            ],
            [
                'created_by' => $earningData['user'],
                'earnings' => $earningData['totalRevenue'] ?? 0,
                'commission_amount' => $commission,
                'validated' => 1,
                'status' => 'pending'
            ]
        );
    }
} 