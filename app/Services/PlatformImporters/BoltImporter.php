<?php

namespace App\Services\PlatformImporters;

use App\Models\Driver;
use App\Models\PlatformEarning;
use App\Models\Setting;
use App\Helpers\NameCheck;

class BoltImporter implements PlatformImporterInterface
{
    /**
     * Import a driver from Bolt
     * @param array $driverData
     * @param string $user
     * @return Driver
     */
    public function importDriver(array $driverData, string $user): Driver
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

        // If multiple matches or no matches, try to find by email
        if ($driverData['email']) {
            $driverByEmail = Driver::where('email', $driverData['email'])->first();
            if ($driverByEmail) {
                return $driverByEmail;
            }
        }

        // Create new driver if no match found
        return Driver::create([
            'first_name' => $driverData['firstName'],
            'last_name' => $driverData['lastName'],
            'full_name' => $driverData['fullName'],
            'email' => $driverData['email'] ?? null,
            'phone_number' => $driverData['phoneNumber'] ?? null,
            'user_id' => $user
        ]);
    }

    /**
     * Import earnings from Bolt
     * @param Driver $driver
     * @param array $earningData
     * @param string $user
     * @return PlatformEarning
     */
    public function importEarnings(Driver $driver, array $earningData, string $user): PlatformEarning
    {
        $commission = Setting::where('name', 'commission')->first()->value;

        return PlatformEarning::firstOrCreate(
            [
                'driver_id' => $driver->id,
                'week_start_date' => $earningData['weekDate'],
                'platform' => 'bolt'
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