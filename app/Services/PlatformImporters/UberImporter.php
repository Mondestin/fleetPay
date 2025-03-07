<?php

namespace App\Services\PlatformImporters;

use App\Models\Driver;
use App\Models\PlatformEarning;
use App\Models\Setting;
use App\Helpers\NameCheck;

class UberImporter implements PlatformImporterInterface
{
    public function importDriver(array $driverData): Driver
    {
        $fullName = $driverData['fullName'];
        $matchedDrivers = [];
        
        // First check by name
        foreach (Driver::all() as $existingDriver) {
            if (NameCheck::matchName($fullName, $existingDriver->full_name)) {
                $matchedDrivers[] = $existingDriver;
            }
        }

        // If exactly one match found, return that driver
        if (count($matchedDrivers) === 1) {
            return $matchedDrivers[0];
        }

        // If multiple matches or no matches, try to find by uber_id
        if ($driverData['uberId']) {
            $driverByUberId = Driver::where('driver_uber_id', $driverData['uberId'])->first();
            if ($driverByUberId) {
                return $driverByUberId;
            }
        }

        // Create new driver if no match found
        return Driver::create([
            'first_name' => $driverData['firstName'],
            'last_name' => $driverData['lastName'],
            'full_name' => $driverData['fullName'],
            'driver_uber_id' => $driverData['uberId'],
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
                'platform' => 'uber'
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