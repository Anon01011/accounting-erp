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
            if (!Schema::hasColumn('asset_details', 'asset_id')) {
                $table->foreignId('asset_id')->after('id')->constrained('assets')->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('asset_details', 'serial_number')) {
                $table->string('serial_number')->nullable()->after('asset_id');
            }
            
            if (!Schema::hasColumn('asset_details', 'purchase_date')) {
                $table->date('purchase_date')->nullable()->after('serial_number');
            }
            
            if (!Schema::hasColumn('asset_details', 'purchase_price')) {
                $table->decimal('purchase_price', 15, 2)->default(0)->after('purchase_date');
            }
            
            if (!Schema::hasColumn('asset_details', 'supplier')) {
                $table->string('supplier')->nullable()->after('purchase_price');
            }
            
            if (!Schema::hasColumn('asset_details', 'warranty_period')) {
                $table->string('warranty_period')->nullable()->after('supplier');
            }
            
            if (!Schema::hasColumn('asset_details', 'warranty_expiry')) {
                $table->date('warranty_expiry')->nullable()->after('warranty_period');
            }
            
            if (!Schema::hasColumn('asset_details', 'notes')) {
                $table->text('notes')->nullable()->after('warranty_expiry');
            }
            
            if (!Schema::hasColumn('asset_details', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('notes')->constrained('users')->onDelete('restrict');
            }
            
            if (!Schema::hasColumn('asset_details', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->onDelete('restrict');
            }
            
            if (!Schema::hasColumn('asset_details', 'deleted_at')) {
                $table->softDeletes();
            }
            
            if (!Schema::hasColumn('asset_details', 'depreciation_method')) {
                $table->string('depreciation_method')->default('straight_line')->after('warranty_expiry');
            }
            
            if (!Schema::hasColumn('asset_details', 'depreciation_rate')) {
                $table->decimal('depreciation_rate', 5, 2)->default(0)->after('depreciation_method');
            }
            
            if (!Schema::hasColumn('asset_details', 'useful_life')) {
                $table->integer('useful_life')->default(0)->after('depreciation_rate');
            }
            
            if (!Schema::hasColumn('asset_details', 'location')) {
                $table->string('location')->nullable()->after('useful_life');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_details', function (Blueprint $table) {
            $table->dropForeign(['asset_id']);
            $table->dropColumn([
                'asset_id',
                'serial_number',
                'purchase_date',
                'purchase_price',
                'supplier',
                'warranty_period',
                'warranty_expiry',
                'notes',
                'created_by',
                'updated_by',
                'deleted_at',
                'depreciation_method',
                'depreciation_rate',
                'useful_life',
                'location'
            ]);
        });
    }
};
