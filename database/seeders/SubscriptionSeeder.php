<?php

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $subscriptions = [
            [
                'id' => 'sub1',
                'user_id' => 'user2',
                'start_date' => '2024-03-01',
                'end_date' => '2024-03-31',
                'amount' => 299.99,
                'status' => 'active',
                'payment_status' => 'paid',
            ],
            [
                'id' => 'sub2',
                'user_id' => 'user1',
                'start_date' => '2024-03-01',
                'end_date' => '2024-03-31',
                'amount' => 299.99,
                'status' => 'active',
                'payment_status' => 'pending',
            ]
          
        ];

        foreach ($subscriptions as $subscription) {
            Subscription::create($subscription);
        }
    }
} 