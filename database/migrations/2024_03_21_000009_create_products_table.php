<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->text('description')->nullable();
                $table->foreignId('category_id')->constrained();
                $table->foreignId('unit_id')->constrained();
                $table->decimal('purchase_price', 15, 2)->default(0);
                $table->decimal('sale_price', 15, 2)->default(0);
                $table->integer('min_stock')->default(0);
                $table->integer('max_stock')->default(0);
                $table->integer('current_stock')->default(0);
                $table->boolean('status')->default(true);
                $table->string('image')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users');
                $table->foreignId('updated_by')->nullable()->constrained('users');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}; 