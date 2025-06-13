<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
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
        } else {
            Schema::table('assets', function (Blueprint $table) {
                if (!Schema::hasColumn('assets', 'chart_of_account_id')) {
                    $table->foreignId('chart_of_account_id')->constrained('chart_of_accounts')->onDelete('restrict');
                }
                if (!Schema::hasColumn('assets', 'name')) {
                    $table->string('name');
                }
                if (!Schema::hasColumn('assets', 'code')) {
                    $table->string('code')->unique();
                }
                if (!Schema::hasColumn('assets', 'category_id')) {
                    $table->foreignId('category_id')->constrained('asset_categories')->onDelete('restrict');
                }
                if (!Schema::hasColumn('assets', 'warehouse_id')) {
                    $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->onDelete('restrict');
                }
                if (!Schema::hasColumn('assets', 'description')) {
                    $table->text('description')->nullable();
                }
                if (!Schema::hasColumn('assets', 'purchase_date')) {
                    $table->date('purchase_date')->nullable();
                }
                if (!Schema::hasColumn('assets', 'purchase_price')) {
                    $table->decimal('purchase_price', 15, 2)->default(0);
                }
                if (!Schema::hasColumn('assets', 'acquisition_cost')) {
                    $table->decimal('acquisition_cost', 15, 2)->default(0);
                }
                if (!Schema::hasColumn('assets', 'salvage_value')) {
                    $table->decimal('salvage_value', 15, 2)->default(0);
                }
                if (!Schema::hasColumn('assets', 'accumulated_depreciation')) {
                    $table->decimal('accumulated_depreciation', 15, 2)->default(0);
                }
                if (!Schema::hasColumn('assets', 'current_value')) {
                    $table->decimal('current_value', 15, 2)->default(0);
                }
                if (!Schema::hasColumn('assets', 'disposal_date')) {
                    $table->date('disposal_date')->nullable();
                }
                if (!Schema::hasColumn('assets', 'disposal_value')) {
                    $table->decimal('disposal_value', 15, 2)->default(0);
                }
                if (!Schema::hasColumn('assets', 'disposal_method')) {
                    $table->string('disposal_method')->nullable();
                }
                if (!Schema::hasColumn('assets', 'tax_depreciation_method')) {
                    $table->string('tax_depreciation_method')->nullable();
                }
                if (!Schema::hasColumn('assets', 'tax_depreciation_rate')) {
                    $table->decimal('tax_depreciation_rate', 5, 2)->default(0);
                }
                if (!Schema::hasColumn('assets', 'tax_useful_life')) {
                    $table->integer('tax_useful_life')->default(0);
                }
                if (!Schema::hasColumn('assets', 'tax_accumulated_depreciation')) {
                    $table->decimal('tax_accumulated_depreciation', 15, 2)->default(0);
                }
                if (!Schema::hasColumn('assets', 'tax_current_value')) {
                    $table->decimal('tax_current_value', 15, 2)->default(0);
                }
                if (!Schema::hasColumn('assets', 'status')) {
                    $table->boolean('status')->default(true);
                }
                if (!Schema::hasColumn('assets', 'created_by')) {
                    $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('restrict');
                }
                if (!Schema::hasColumn('assets', 'updated_by')) {
                    $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('restrict');
                }
                if (!Schema::hasColumn('assets', 'deleted_at')) {
                    $table->softDeletes();
                }

                // Add indexes if they don't exist
                if (!Schema::hasIndex('assets', 'assets_code_index')) {
                    $table->index('code');
                }
                if (!Schema::hasIndex('assets', 'assets_purchase_date_index')) {
                    $table->index('purchase_date');
                }
                if (!Schema::hasIndex('assets', 'assets_disposal_date_index')) {
                    $table->index('disposal_date');
                }
                if (!Schema::hasIndex('assets', 'assets_status_index')) {
                    $table->index('status');
                }
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('assets');
    }
}; 