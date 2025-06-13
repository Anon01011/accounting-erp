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
                $table->string('model')->nullable();
                $table->string('manufacturer')->nullable();
                $table->string('supplier')->nullable();
                $table->date('purchase_date')->nullable();
                $table->decimal('purchase_price', 15, 2)->default(0);
                $table->string('warranty_period')->nullable();
                $table->date('warranty_expiry')->nullable();
                $table->string('condition')->nullable();
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
                if (!Schema::hasColumn('asset_details', 'model')) {
                    $table->string('model')->nullable();
                }
                if (!Schema::hasColumn('asset_details', 'manufacturer')) {
                    $table->string('manufacturer')->nullable();
                }
                if (!Schema::hasColumn('asset_details', 'supplier')) {
                    $table->string('supplier')->nullable();
                }
                if (!Schema::hasColumn('asset_details', 'purchase_date')) {
                    $table->date('purchase_date')->nullable();
                }
                if (!Schema::hasColumn('asset_details', 'purchase_price')) {
                    $table->decimal('purchase_price', 15, 2)->default(0);
                }
                if (!Schema::hasColumn('asset_details', 'warranty_period')) {
                    $table->string('warranty_period')->nullable();
                }
                if (!Schema::hasColumn('asset_details', 'warranty_expiry')) {
                    $table->date('warranty_expiry')->nullable();
                }
                if (!Schema::hasColumn('asset_details', 'condition')) {
                    $table->string('condition')->nullable();
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

                // Add indexes if they don't exist
                if (!Schema::hasIndex('asset_details', 'asset_details_serial_number_index')) {
                    $table->index('serial_number');
                }
                if (!Schema::hasIndex('asset_details', 'asset_details_purchase_date_index')) {
                    $table->index('purchase_date');
                }
                if (!Schema::hasIndex('asset_details', 'asset_details_warranty_expiry_index')) {
                    $table->index('warranty_expiry');
                }
                if (!Schema::hasIndex('asset_details', 'asset_details_condition_index')) {
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