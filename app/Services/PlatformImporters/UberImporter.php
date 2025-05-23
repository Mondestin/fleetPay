<?php

namespace App\Services\PlatformImporters;

use App\Models\Driver;
use App\Models\PlatformEarning;
use App\Models\Setting;
use App\Helpers\NameCheck;

class UberImporter implements PlatformImporterInterface
{

    public function importDriver(array $driverData, string $user): Driver
    {
        $fullName = NameCheck::cleanName($driverData['fullName']);
        $matchedDrivers = [];
        //get driver for a current user
        $drivers = Driver::where('user_id', $user)->get();

        // First check by name
        foreach ($drivers as $existingDriver) {
            if (NameCheck::matchName($fullName, $existingDriver->full_name)) {
                logger("match found");
                logger($existingDriver);
                $matchedDrivers[] = $existingDriver;
            }
        }

        // If exactly one match found, return that driver
        if (count($matchedDrivers) === 1) {
            logger("one match found");
            logger($matchedDrivers[0]);
            return $matchedDrivers[0];
        }

        // If multiple matches or no matches, try to find by email
        if (!empty($driverData['email'])) {
            $driverByEmail = Driver::where('email', $driverData['email'])->first();
            if ($driverByEmail) {
                // Found a driver with the same email, return it
                logger("email match found");
                logger($driverByEmail);
                return $driverByEmail;
            }
        }

        // Create new driver if no match found by name or email
        return Driver::create([
            'first_name' => $driverData['firstName'],
            'last_name' => $driverData['lastName'],
            'full_name' => $driverData['fullName'],
            'driver_uber_id' => null,
            'email' => $driverData['email'] ?? null,
            'phone_number' => $driverData['phoneNumber'] ?? null,
            'user_id' => $user
        ]); 
    }


    public function importEarnings(Driver $driver, array $earningData, string $user): PlatformEarning
    {
        $commission = Setting::where('name', 'commission')->where('user_id', $user)->first()->value;

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