<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('asset_details')) {
        Schema::create('asset_details', function (Blueprint $table) {
            $table->id();
                $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
            $table->string('serial_number')->nullable();
                $table->date('purchase_date')->nullable();
                $table->decimal('purchase_price', 15, 2)->default(0);
                $table->string('supplier')->nullable();
                $table->string('warranty_period')->nullable();
            $table->date('warranty_expiry')->nullable();
                $table->string('depreciation_method')->default('straight_line');
                $table->decimal('depreciation_rate', 5, 2)->default(0);
                $table->integer('useful_life')->default(0);
                $table->string('location')->nullable();
                $table->string('condition')->default('good')->nullable();
            $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('restrict');
                $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('restrict');
            $table->timestamps();
            $table->softDeletes();
            // Indexes
            $table->index('serial_number');
            $table->index('purchase_date');
            $table->index('warranty_expiry');
            $table->index('condition');
        });
        } else {
            Schema::table('asset_details', function (Blueprint $table) {
                if (!Schema::hasColumn('asset_details', 'asset_id')) {
                    $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
                }
                if (!Schema::hasColumn('asset_details', 'serial_number')) {
                    $table->string('serial_number')->nullable();
                }
                if (!Schema::hasColumn('asset_details', 'purchase_date')) {
                    $table->date('purchase_date')->nullable();
                }
                if (!Schema::hasColumn('asset_details', 'purchase_price')) {
                    $table->decimal('purchase_price', 15, 2)->default(0);
                }
                if (!Schema::hasColumn('asset_details', 'supplier')) {
                    $table->string('supplier')->nullable();
                }
                if (!Schema::hasColumn('asset_details', 'warranty_period')) {
                    $table->string('warranty_period')->nullable();
                }
                if (!Schema::hasColumn('asset_details', 'warranty_expiry')) {
                    $table->date('warranty_expiry')->nullable();
                }
                if (!Schema::hasColumn('asset_details', 'depreciation_method')) {
                    $table->string('depreciation_method')->default('straight_line');
                }
                if (!Schema::hasColumn('asset_details', 'depreciation_rate')) {
                    $table->decimal('depreciation_rate', 5, 2)->default(0);
                }
                if (!Schema::hasColumn('asset_details', 'useful_life')) {
                    $table->integer('useful_life')->default(0);
                }
                if (!Schema::hasColumn('asset_details', 'location')) {
                    $table->string('location')->nullable();
                }
                if (!Schema::hasColumn('asset_details', 'condition')) {
                    $table->string('condition')->default('good')->nullable();
                }
                if (!Schema::hasColumn('asset_details', 'notes')) {
                    $table->text('notes')->nullable();
                }
                if (!Schema::hasColumn('asset_details', 'created_by')) {
                    $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('restrict');
                }
                if (!Schema::hasColumn('asset_details', 'updated_by')) {
                    $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('restrict');
                }
                if (!Schema::hasColumn('asset_details', 'deleted_at')) {
                    $table->softDeletes();
                }
                // Indexes
                if (Schema::hasColumn('asset_details', 'serial_number')) {
                    $table->index('serial_number');
                }
                if (Schema::hasColumn('asset_details', 'purchase_date')) {
                    $table->index('purchase_date');
                }
                if (Schema::hasColumn('asset_details', 'warranty_expiry')) {
                    $table->index('warranty_expiry');
                }
                if (Schema::hasColumn('asset_details', 'condition')) {
                    $table->index('condition');
                }
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('asset_details');
    }
}; 