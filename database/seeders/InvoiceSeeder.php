<?php

namespace Database\Seeders;

use App\Models\Invoice;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $invoices = [
            [
                'id' => 'inv1',
                'invoice_number' => 'INV-20240217-ABCD',
                'subscription_id' => 'sub1',
                'amount' => 1500.00,
                'status' => 'pending',
                'issue_date' => '2024-02-17',
                'due_date' => '2024-03-17',
          
            ],
            [
                'id' => 'inv2',
                'invoice_number' => 'INV-20240217-EFGH',
                'subscription_id' => 'sub2',
                'amount' => 2000.00,
                'status' => 'paid',
                'issue_date' => '2024-02-17',
                'due_date' => '2024-03-17',
              
            ],
        ];

        foreach ($invoices as $invoice) {
            Invoice::create($invoice);
        }
    }
} 