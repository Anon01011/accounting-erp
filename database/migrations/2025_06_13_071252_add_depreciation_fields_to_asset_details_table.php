<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('asset_details', function (Blueprint $table) {
            if (!Schema::hasColumn('asset_details', 'depreciation_method')) {
                $table->string('depreciation_method')->nullable()->after('warranty_expiry');
            }
            if (!Schema::hasColumn('asset_details', 'depreciation_rate')) {
                $table->decimal('depreciation_rate', 5, 2)->default(0)->after('depreciation_method');
            }
            if (!Schema::hasColumn('asset_details', 'useful_life')) {
                $table->integer('useful_life')->default(0)->after('depreciation_rate');
            }
            if (!Schema::hasColumn('asset_details', 'residual_value')) {
                $table->decimal('residual_value', 15, 2)->default(0)->after('useful_life');
            }
            if (!Schema::hasColumn('asset_details', 'revaluation_frequency')) {
                $table->string('revaluation_frequency')->nullable()->after('residual_value');
            }
            if (!Schema::hasColumn('asset_details', 'depreciation_start_date')) {
                $table->date('depreciation_start_date')->nullable()->after('revaluation_frequency');
            }
            if (!Schema::hasColumn('asset_details', 'location')) {
                $table->string('location')->nullable()->after('depreciation_start_date');
            }
            if (!Schema::hasColumn('asset_details', 'condition')) {
                $table->string('condition')->nullable()->after('location');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_details', function (Blueprint $table) {
            $table->dropColumn([
                'depreciation_method',
                'depreciation_rate',
                'useful_life',
                'residual_value',
                'revaluation_frequency',
                'depreciation_start_date',
                'location',
                'condition'
            ]);
        });
    }
};
