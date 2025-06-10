<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

class QuotationSeeder extends Seeder
{
    public function run()
    {
        $customers = Customer::all();
        $products = Product::all();
        $users = User::all();

        if ($customers->isEmpty() || $products->isEmpty() || $users->isEmpty()) {
            $this->command->info('No customers, products, or users found. Skipping quotation seeding.');
            return;
        }

        // Create 20 quotations
        for ($i = 1; $i <= 20; $i++) {
            $quotationDate = Carbon::now()->subDays(rand(1, 30));
            $validUntil = $quotationDate->copy()->addDays(rand(7, 30));
            $status = ['draft', 'sent', 'accepted', 'rejected', 'expired'][array_rand(['draft', 'sent', 'accepted', 'rejected', 'expired'])];

            $quotation = Quotation::create([
                'reference_number' => 'QT-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'customer_id' => $customers->random()->id,
                'quotation_date' => $quotationDate,
                'valid_until' => $validUntil,
                'status' => $status,
                'total_amount' => 0, // Will be updated after adding items
                'notes' => rand(0, 1) ? 'Sample notes for quotation ' . $i : null,
                'created_by' => $users->random()->id,
                'updated_by' => $users->random()->id,
            ]);

            // Add 1-5 items to each quotation
            $numItems = rand(1, 5);
            $totalAmount = 0;

            for ($j = 0; $j < $numItems; $j++) {
                $product = $products->random();
                $quantity = rand(1, 10);
                $unitPrice = $product->sale_price;
                $discount = rand(0, 10);
                $tax = 5; // 5% tax rate

                $subtotal = $quantity * $unitPrice;
                $discountAmount = ($subtotal * $discount) / 100;
                $taxAmount = (($subtotal - $discountAmount) * $tax) / 100;
                $total = $subtotal - $discountAmount + $taxAmount;

                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount' => $discount,
                    'tax' => $tax,
                    'subtotal' => $total,
                    'description' => rand(0, 1) ? 'Sample description for item ' . ($j + 1) : null,
                    'created_by' => $users->random()->id,
                    'updated_by' => $users->random()->id,
                ]);

                $totalAmount += $total;
            }

            // Update the quotation's total amount
            $quotation->update(['total_amount' => $totalAmount]);
        }
    }
} 