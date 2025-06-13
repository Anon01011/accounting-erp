<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('assets')) {
            Schema::create('assets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('chart_of_account_id')->constrained('chart_of_accounts')->onDelete('restrict');
                $table->string('name');
                $table->string('code')->unique();
                $table->foreignId('category_id')->constrained('asset_categories')->onDelete('restrict');
                $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->onDelete('restrict');
                $table->text('description')->nullable();
                $table->date('purchase_date')->nullable();
                $table->decimal('purchase_price', 15, 2)->default(0);
                $table->decimal('acquisition_cost', 15, 2)->default(0);
                $table->decimal('salvage_value', 15, 2)->default(0);
                $table->decimal('accumulated_depreciation', 15, 2)->default(0);
                $table->decimal('current_value', 15, 2)->default(0);
                $table->date('disposal_date')->nullable();
                $table->decimal('disposal_value', 15, 2)->default(0);
                $table->string('disposal_method')->nullable();
                $table->string('tax_depreciation_method')->nullable();
                $table->decimal('tax_depreciation_rate', 5, 2)->default(0);
                $table->integer('tax_useful_life')->default(0);
                $table->decimal('tax_accumulated_depreciation', 15, 2)->default(0);
                $table->decimal('tax_current_value', 15, 2)->default(0);
                $table->boolean('status')->default(true);
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('restrict');
                $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('restrict');
                $table->timestamps();
                $table->softDeletes();

                // Indexes
                $table->index('code');
                $table->index('purchase_date');
                $table->index('disposal_date');
                $table->index('status');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
}; 