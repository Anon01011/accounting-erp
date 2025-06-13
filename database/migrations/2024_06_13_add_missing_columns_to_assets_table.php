<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            // Add any missing columns
            if (!Schema::hasColumn('assets', 'current_value')) {
                $table->decimal('current_value', 15, 2)->after('purchase_price');
            }
            if (!Schema::hasColumn('assets', 'location')) {
                $table->string('location')->after('current_value');
            }
            if (!Schema::hasColumn('assets', 'status')) {
                $table->enum('status', ['active', 'inactive', 'maintenance', 'disposed'])->default('active')->after('location');
            }
            if (!Schema::hasColumn('assets', 'supplier_id')) {
                $table->foreignId('supplier_id')->after('status')->constrained('suppliers');
            }
            if (!Schema::hasColumn('assets', 'tax_group_id')) {
                $table->foreignId('tax_group_id')->after('supplier_id')->constrained('tax_groups');
            }
            if (!Schema::hasColumn('assets', 'warranty_expiry')) {
                $table->date('warranty_expiry')->nullable()->after('tax_group_id');
            }
            if (!Schema::hasColumn('assets', 'depreciation_method')) {
                $table->enum('depreciation_method', ['straight_line', 'declining_balance', 'sum_of_years'])->after('warranty_expiry');
            }
            if (!Schema::hasColumn('assets', 'depreciation_rate')) {
                $table->decimal('depreciation_rate', 5, 2)->after('depreciation_method');
            }
            if (!Schema::hasColumn('assets', 'useful_life')) {
                $table->integer('useful_life')->after('depreciation_rate');
            }
            if (!Schema::hasColumn('assets', 'notes')) {
                $table->text('notes')->nullable()->after('useful_life');
            }
            if (!Schema::hasColumn('assets', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn([
                'current_value',
                'location',
                'status',
                'supplier_id',
                'tax_group_id',
                'warranty_expiry',
                'depreciation_method',
                'depreciation_rate',
                'useful_life',
                'notes',
                'is_active'
            ]);
        });
    }
}; 