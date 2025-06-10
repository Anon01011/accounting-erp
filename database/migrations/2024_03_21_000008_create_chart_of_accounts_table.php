<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('type_code', 2)->comment('Account Type Code (e.g., 01 for Assets)');
            $table->string('group_code', 2)->comment('Account Group Code');
            $table->string('class_code', 3)->comment('Account Class Code');
            $table->string('account_code', 9)->comment('Account Code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('chart_of_accounts')->onDelete('restrict');
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['type_code', 'group_code', 'class_code']);
            $table->index('account_code');
            $table->index('is_active');
            $table->index('parent_id');

            // Unique constraint
            $table->unique(['type_code', 'group_code', 'class_code', 'account_code'], 'unique_account_code');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chart_of_accounts');
    }
}; 