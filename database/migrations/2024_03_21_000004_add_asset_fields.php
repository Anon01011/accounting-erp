<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add fields to asset_categories table
        /*
        Schema::table('asset_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('asset_categories', 'depreciation_method')) {
                $table->string('depreciation_method')->default('straight_line')->after('description');
            }
            if (!Schema::hasColumn('asset_categories', 'default_depreciation_rate')) {
                $table->decimal('default_depreciation_rate', 5, 2)->default(0)->after('depreciation_method');
            }
            if (!Schema::hasColumn('asset_categories', 'default_useful_life')) {
                $table->integer('default_useful_life')->default(0)->after('default_depreciation_rate');
            }
            if (!Schema::hasColumn('asset_categories', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('default_useful_life');
            }
        });
        */

        // Add fields to assets table
        /*
        Schema::table('assets', function (Blueprint $table) {
            if (!Schema::hasColumn('assets', 'warehouse_id')) {
                $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete()->after('category_id');
            }
            if (!Schema::hasColumn('assets', 'accumulated_depreciation')) {
                $table->decimal('accumulated_depreciation', 15, 2)->default(0)->after('purchase_price');
            }
            if (!Schema::hasColumn('assets', 'current_value')) {
                $table->decimal('current_value', 15, 2)->default(0)->after('accumulated_depreciation');
            }
        });
        */

        // Create asset_maintenances table if it doesn't exist
        /*
        if (!Schema::hasTable('asset_maintenances')) {
            Schema::create('asset_maintenances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
                $table->date('maintenance_date');
                $table->string('maintenance_type');
                $table->text('description');
                $table->decimal('cost', 15, 2)->default(0);
                $table->string('performed_by')->nullable();
                $table->date('next_maintenance_date')->nullable();
                $table->boolean('status')->default(true);
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
            });
        }
        */

        // Create asset_transactions table if it doesn't exist
        /*
        if (!Schema::hasTable('asset_transactions')) {
            Schema::create('asset_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
                $table->string('type');
                $table->decimal('amount', 15, 2);
                $table->date('date');
                $table->text('description')->nullable();
                $table->string('reference_type')->nullable();
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['reference_type', 'reference_id']);
            });
        }
        */
    }

    public function down()
    {
        // Remove fields from asset_categories table
        Schema::table('asset_categories', function (Blueprint $table) {
            $table->dropColumn([
                'depreciation_method',
                'default_depreciation_rate',
                'default_useful_life',
                'is_active'
            ]);
        });

        // Remove fields from assets table
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn([
                'warehouse_id',
                'accumulated_depreciation',
                'current_value'
            ]);
        });

        // Drop tables if they exist
        Schema::dropIfExists('asset_maintenances');
        Schema::dropIfExists('asset_transactions');
    }
}; 