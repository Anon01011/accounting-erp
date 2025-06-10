<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('account_groups', function (Blueprint $table) {
            $table->id();
            $table->string('type_code', 2);
            $table->string('code', 2);
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('type_code')
                ->references('code')
                ->on('account_types')
                ->onDelete('cascade');

            $table->unique(['type_code', 'code']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('account_groups');
    }
}; 