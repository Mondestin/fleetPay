<?php

namespace App\Services\PlatformImporters;

interface PlatformImporterInterface
{
    public function importDriver(array $driverData, string $user): \App\Models\Driver;
    public function importEarnings(\App\Models\Driver $driver, array $earningData, string $user): \App\Models\PlatformEarning;
} 