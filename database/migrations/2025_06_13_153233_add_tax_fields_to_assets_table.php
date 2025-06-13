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
        Schema::table('assets', function (Blueprint $table) {
            // Tax Group Relationship
            $table->foreignId('tax_group_id')->nullable()->constrained('tax_groups')->nullOnDelete();
            
            // Tax Fields
            $table->decimal('tax_rate', 5, 2)->nullable()->comment('Tax rate percentage');
            $table->string('tax_type', 50)->nullable()->comment('Type of tax (e.g., VAT, GST)');
            $table->string('tax_number', 50)->nullable()->comment('Tax registration number');
            $table->boolean('tax_exempt')->default(false)->comment('Whether the asset is tax exempt');
            $table->text('tax_notes')->nullable()->comment('Additional tax-related notes');
            
            // Tax Depreciation Fields
            $table->string('tax_depreciation_method', 50)->nullable()->comment('Method used for tax depreciation');
            $table->decimal('tax_depreciation_rate', 5, 2)->nullable()->comment('Tax depreciation rate percentage');
            $table->integer('tax_useful_life')->nullable()->comment('Useful life for tax purposes in months');
            $table->decimal('tax_accumulated_depreciation', 15, 2)->default(0)->comment('Accumulated tax depreciation');
            $table->decimal('tax_current_value', 15, 2)->nullable()->comment('Current value for tax purposes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign(['tax_group_id']);
            $table->dropColumn([
                'tax_group_id',
                'tax_rate',
                'tax_type',
                'tax_number',
                'tax_exempt',
                'tax_notes',
                'tax_depreciation_method',
                'tax_depreciation_rate',
                'tax_useful_life',
                'tax_accumulated_depreciation',
                'tax_current_value'
            ]);
        });
    }
};
