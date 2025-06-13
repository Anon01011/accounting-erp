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
        if (Schema::hasTable('inventory')) {
            Schema::table('inventory', function (Blueprint $table) {
                // Tax Group Relationship
                $table->foreignId('tax_group_id')->nullable()->after('id')->constrained('tax_groups');
                
                // Tax Fields
                $table->decimal('tax_rate', 5, 2)->nullable()->comment('Tax rate percentage');
                $table->string('tax_type', 50)->nullable()->comment('Type of tax (e.g., VAT, GST)');
                $table->boolean('tax_exempt')->default(false)->comment('Whether the item is tax exempt');
                $table->text('tax_notes')->nullable()->comment('Additional tax-related notes');
                
                // Tax Calculation Fields
                $table->decimal('tax_amount', 15, 2)->default(0)->comment('Calculated tax amount');
                $table->decimal('price_before_tax', 15, 2)->nullable()->comment('Price before tax');
                $table->decimal('price_after_tax', 15, 2)->nullable()->comment('Price after tax');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('inventory')) {
            Schema::table('inventory', function (Blueprint $table) {
                $table->dropForeign(['tax_group_id']);
                $table->dropColumn([
                    'tax_group_id',
                    'tax_rate',
                    'tax_type',
                    'tax_exempt',
                    'tax_notes',
                    'tax_amount',
                    'price_before_tax',
                    'price_after_tax'
                ]);
            });
        }
    }
};
