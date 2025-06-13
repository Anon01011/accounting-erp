<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use Illuminate\Support\Str;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Tech Solutions Inc.',
                'code' => 'TSI-001',
                'contact_person' => 'John Smith',
                'email' => 'john@techsolutions.com',
                'phone' => '+1-555-0123',
                'address' => '123 Tech Street, Silicon Valley, CA',
                'tax_number' => 'TAX123456',
                'payment_terms' => 'Net 30',
                'is_active' => true,
            ],
            [
                'name' => 'Global Electronics Ltd.',
                'code' => 'GEL-001',
                'contact_person' => 'Sarah Johnson',
                'email' => 'sarah@globalelectronics.com',
                'phone' => '+1-555-0124',
                'address' => '456 Circuit Road, Tech City, TX',
                'tax_number' => 'TAX789012',
                'payment_terms' => 'Net 45',
                'is_active' => true,
            ],
            [
                'name' => 'Office Supplies Co.',
                'code' => 'OSC-001',
                'contact_person' => 'Mike Brown',
                'email' => 'mike@officesupplies.com',
                'phone' => '+1-555-0125',
                'address' => '789 Stationery Ave, Business Park, NY',
                'tax_number' => 'TAX345678',
                'payment_terms' => 'Net 15',
                'is_active' => true,
            ],
            [
                'name' => 'Furniture World',
                'code' => 'FW-001',
                'contact_person' => 'Lisa Chen',
                'email' => 'lisa@furnitureworld.com',
                'phone' => '+1-555-0126',
                'address' => '321 Design Blvd, Creative District, CA',
                'tax_number' => 'TAX901234',
                'payment_terms' => 'Net 30',
                'is_active' => true,
            ],
            [
                'name' => 'IT Equipment Pro',
                'code' => 'IEP-001',
                'contact_person' => 'David Wilson',
                'email' => 'david@itequipmentpro.com',
                'phone' => '+1-555-0127',
                'address' => '654 Hardware Lane, Tech Hub, WA',
                'tax_number' => 'TAX567890',
                'payment_terms' => 'Net 30',
                'is_active' => true,
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
} 