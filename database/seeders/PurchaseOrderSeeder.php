<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;

class PurchaseOrderSeeder extends Seeder
{
    public function run()
    {
        $suppliers = Supplier::all();
        $users = User::all();
        $statuses = ['draft', 'pending', 'approved', 'received', 'cancelled'];

        if ($suppliers->isEmpty() || $users->isEmpty()) {
            $this->command->info('No suppliers or users found. Skipping purchase order seeding.');
            return;
        }

        // Create 20 purchase orders
        for ($i = 1; $i <= 20; $i++) {
            $orderDate = Carbon::now()->subDays(rand(1, 30));
            $deliveryDate = $orderDate->copy()->addDays(rand(1, 14));
            $status = $statuses[array_rand($statuses)];
            
            PurchaseOrder::create([
                'reference_number' => 'PO-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'supplier_id' => $suppliers->random()->id,
                'order_date' => $orderDate,
                'expected_delivery_date' => $deliveryDate,
                'status' => $status,
                'total_amount' => rand(1000, 10000) + (rand(0, 99) / 100),
                'notes' => rand(0, 1) ? 'Sample notes for purchase order ' . $i : null,
                'created_by' => $users->random()->id,
                'updated_by' => $users->random()->id,
            ]);
        }
    }
} 