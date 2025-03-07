<?php

namespace App\Services\PlatformImporters;

interface PlatformImporterInterface
{
    public function importDriver(array $driverData): \App\Models\Driver;
    public function importEarnings(\App\Models\Driver $driver, array $earningData): \App\Models\PlatformEarning;
} 