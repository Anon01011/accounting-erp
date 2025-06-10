<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('asset_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('chart_of_accounts')->onDelete('cascade');
            $table->string('serial_number')->nullable();
            $table->date('purchase_date');
            $table->decimal('purchase_price', 15, 2);
            $table->date('warranty_expiry')->nullable();
            $table->string('depreciation_method')->default('straight_line');
            $table->decimal('depreciation_rate', 5, 2);
            $table->integer('useful_life');
            $table->string('location')->nullable();
            $table->string('condition')->default('good');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('serial_number');
            $table->index('purchase_date');
            $table->index('warranty_expiry');
            $table->index('condition');
        });
    }

    public function down()
    {
        Schema::dropIfExists('asset_details');
    }
}; 