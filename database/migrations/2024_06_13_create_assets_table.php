<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->foreignId('category_id')->constrained('asset_categories');
            $table->text('description')->nullable();
            $table->date('purchase_date');
            $table->decimal('purchase_price', 15, 2);
            $table->decimal('current_value', 15, 2);
            $table->string('location');
            $table->enum('status', ['active', 'inactive', 'maintenance', 'disposed'])->default('active');
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->foreignId('tax_group_id')->constrained('tax_groups');
            $table->date('warranty_expiry')->nullable();
            $table->enum('depreciation_method', ['straight_line', 'declining_balance', 'sum_of_years']);
            $table->decimal('depreciation_rate', 5, 2);
            $table->integer('useful_life');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
}; 