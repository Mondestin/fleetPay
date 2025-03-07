<?php

namespace Database\Seeders;

use App\Models\Driver;
use Illuminate\Database\Seeder;

class DriverSeeder extends Seeder
{
    public function run(): void
    {
        $drivers = [
            [
                'id' => 'd1',
                'first_name' => 'Jean',
                'last_name' => 'Dupont',
                'full_name' => 'Jean Dupont',
                'email' => 'jean.dupont@example.com',
                'phone_number' => '+33 6 12 34 56 78',
                'status' => 'active',
            ],
            [
                'id' => 'd2',
                'first_name' => 'Marie',
                'last_name' => 'Laurent',
                'full_name' => 'Marie Laurent',
                'email' => 'marie.laurent@example.com',
                'phone_number' => '+33 6 23 45 67 89',
                'status' => 'active',
            ],
            [
                'id' => 'd3',
                'first_name' => 'Thomas',
                'last_name' => 'Martin',
                'full_name' => 'Thomas Martin',
                'email' => 'thomas.martin@example.com',
                'phone_number' => '+33 6 34 56 78 90',
                'status' => 'active',
            ],
        ];

        foreach ($drivers as $driver) {
            Driver::create($driver);
        }
    }
} 