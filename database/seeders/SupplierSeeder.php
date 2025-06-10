<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    public function run()
    {
        $suppliers = [
            [
                'name' => 'ABC Electronics',
                'code' => 'ABC-ELEC',
                'contact_person' => 'John Smith',
                'email' => 'john@abcelectronics.com',
                'phone' => '+1-555-0123',
                'address' => '123 Tech Street, Silicon Valley, CA',
                'tax_number' => 'TAX123456',
                'status' => true,
            ],
            [
                'name' => 'Global Office Supplies',
                'code' => 'GOS-001',
                'contact_person' => 'Sarah Johnson',
                'email' => 'sarah@globalofficesupplies.com',
                'phone' => '+1-555-0124',
                'address' => '456 Office Park, New York, NY',
                'tax_number' => 'TAX789012',
                'status' => true,
            ],
            [
                'name' => 'Premium Foods Inc',
                'code' => 'PFI-001',
                'contact_person' => 'Michael Brown',
                'email' => 'michael@premiumfoods.com',
                'phone' => '+1-555-0125',
                'address' => '789 Food Court, Chicago, IL',
                'tax_number' => 'TAX345678',
                'status' => true,
            ],
            [
                'name' => 'Tech Solutions Ltd',
                'code' => 'TSL-001',
                'contact_person' => 'Emily Davis',
                'email' => 'emily@techsolutions.com',
                'phone' => '+1-555-0126',
                'address' => '321 Digital Drive, Seattle, WA',
                'tax_number' => 'TAX901234',
                'status' => true,
            ],
            [
                'name' => 'Quality Materials Co',
                'code' => 'QMC-001',
                'contact_person' => 'Robert Wilson',
                'email' => 'robert@qualitymaterials.com',
                'phone' => '+1-555-0127',
                'address' => '654 Industrial Park, Houston, TX',
                'tax_number' => 'TAX567890',
                'status' => true,
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::firstOrCreate(
                ['code' => $supplier['code']],
                $supplier
            );
        }
    }
} 