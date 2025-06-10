<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            AccountingSeeder::class,
            ChartOfAccountsSeeder::class,
            InventoryProductSeeder::class,
            PurchaseOrderSeeder::class,
            SupplierSeeder::class,
            TestDataSeeder::class,
            QuotationSeeder::class,
            CustomerSeeder::class,
            SalesOrderSeeder::class,
            AssetCategorySeeder::class,
        ]);
    }
}
