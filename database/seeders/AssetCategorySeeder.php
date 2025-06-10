<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssetCategorySeeder extends Seeder
{
    public function run()
    {
        $assetCategories = config('accounting.asset_categories');

        foreach ($assetCategories as $code => $name) {
            DB::table('asset_categories')->insert([
                'code' => $code,
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
} 