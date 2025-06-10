<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SalesOrder;
use App\Models\Customer;
use App\Models\Product;
use Carbon\Carbon;

class SalesOrderSeeder extends Seeder
{
    public function run()
    {
        $customers = Customer::all();
        $products = Product::all();
        $statuses = ['draft', 'confirmed', 'processing', 'completed', 'cancelled'];

        for ($i = 0; $i < 10; $i++) {
            $orderDate = Carbon::now()->subDays(rand(0, 30));
            $expectedDeliveryDate = $orderDate->copy()->addDays(rand(1, 30));
            $status = $statuses[array_rand($statuses)];
            $totalAmount = 0;

            $order = SalesOrder::create([
                'reference_number' => 'SO-' . date('Y') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'customer_id' => $customers->random()->id,
                'order_date' => $orderDate,
                'expected_delivery_date' => $expectedDeliveryDate,
                'status' => $status,
                'notes' => 'Dummy order notes for testing.',
                'created_by' => 1
            ]);

            // Create 1-3 items for each order
            $numItems = rand(1, 3);
            for ($j = 0; $j < $numItems; $j++) {
                $product = $products->random();
                $quantity = rand(1, 5);
                $unitPrice = $product->sale_price;
                $subtotal = $quantity * $unitPrice;
                $totalAmount += $subtotal;

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                    'description' => 'Sample item description',
                    'created_by' => 1
                ]);
            }

            $order->update(['total_amount' => $totalAmount]);
        }
    }
}  