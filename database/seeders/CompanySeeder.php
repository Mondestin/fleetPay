<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users who don't have companies yet
        $users = User::doesntHave('company')->get();

        foreach ($users as $user) {
            Company::create([
                'id' => Str::uuid(),
                'user_id' => $user->id,
                'name' => fake()->company(),
                'address' => fake()->address(),
                'phone' => fake()->phoneNumber(),
                'status' => 'active',
                'logo' => null, // You can set a default logo path if needed
            ]);
        }
    }
} 