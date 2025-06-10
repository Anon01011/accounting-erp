<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;

class InventoryProductSeeder extends Seeder
{
    public function run()
    {
        // Seed some units if not exist
        $units = [
            ['name' => 'Piece', 'code' => 'PCS', 'description' => 'Piece', 'status' => true],
            ['name' => 'Box', 'code' => 'BOX', 'description' => 'Box', 'status' => true],
            ['name' => 'Kilogram', 'code' => 'KG', 'description' => 'Kilogram', 'status' => true],
        ];
        foreach ($units as $unitData) {
            Unit::firstOrCreate(['code' => $unitData['code']], $unitData);
        }

        // Seed some categories if not exist
        $categories = [
            ['name' => 'Electronics', 'code' => 'ELEC', 'description' => 'Electronic items', 'status' => true],
            ['name' => 'Stationery', 'code' => 'STAT', 'description' => 'Office stationery', 'status' => true],
            ['name' => 'Groceries', 'code' => 'GROC', 'description' => 'Grocery items', 'status' => true],
        ];
        foreach ($categories as $catData) {
            Category::firstOrCreate(['code' => $catData['code']], $catData);
        }

        $unitIds = Unit::pluck('id', 'code');
        $categoryIds = Category::pluck('id', 'code');

        // Seed products
        $products = [
            [
                'name' => 'Wireless Mouse',
                'code' => 'ELEC-001',
                'description' => '2.4GHz Wireless Mouse',
                'category_id' => $categoryIds['ELEC'],
                'unit_id' => $unitIds['PCS'],
                'purchase_price' => 8.50,
                'sale_price' => 15.00,
                'min_stock' => 10,
                'max_stock' => 100,
                'current_stock' => 50,
                'status' => true,
                'image' => null,
            ],
            [
                'name' => 'A4 Paper Ream',
                'code' => 'STAT-001',
                'description' => '500 sheets A4 size',
                'category_id' => $categoryIds['STAT'],
                'unit_id' => $unitIds['BOX'],
                'purchase_price' => 3.00,
                'sale_price' => 5.00,
                'min_stock' => 20,
                'max_stock' => 200,
                'current_stock' => 120,
                'status' => true,
                'image' => null,
            ],
            [
                'name' => 'Rice 1kg',
                'code' => 'GROC-001',
                'description' => 'Premium Basmati Rice',
                'category_id' => $categoryIds['GROC'],
                'unit_id' => $unitIds['KG'],
                'purchase_price' => 0.80,
                'sale_price' => 1.20,
                'min_stock' => 50,
                'max_stock' => 500,
                'current_stock' => 300,
                'status' => true,
                'image' => null,
            ],
            [
                'name' => 'USB Keyboard',
                'code' => 'ELEC-002',
                'description' => 'Standard USB Keyboard',
                'category_id' => $categoryIds['ELEC'],
                'unit_id' => $unitIds['PCS'],
                'purchase_price' => 7.00,
                'sale_price' => 12.00,
                'min_stock' => 10,
                'max_stock' => 80,
                'current_stock' => 30,
                'status' => true,
                'image' => null,
            ],
            [
                'name' => 'Notebook',
                'code' => 'STAT-002',
                'description' => '200 pages ruled notebook',
                'category_id' => $categoryIds['STAT'],
                'unit_id' => $unitIds['PCS'],
                'purchase_price' => 1.20,
                'sale_price' => 2.00,
                'min_stock' => 30,
                'max_stock' => 150,
                'current_stock' => 60,
                'status' => true,
                'image' => null,
            ],
        ];

        foreach ($products as $data) {
            Product::firstOrCreate(['code' => $data['code']], $data);
        }
    }
}