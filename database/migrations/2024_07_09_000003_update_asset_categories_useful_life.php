<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Update any asset categories with zero useful life to have a default of 1 year
        DB::table('asset_categories')
            ->where('default_useful_life', 0)
            ->update(['default_useful_life' => 1]);
    }

    public function down()
    {
        // No need for down migration as this is a data fix
    }
}; 