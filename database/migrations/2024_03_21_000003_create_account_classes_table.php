<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('account_classes', function (Blueprint $table) {
            $table->id();
            $table->string('type_code', 2);
            $table->string('group_code', 2);
            $table->string('code', 3);
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('type_code')
                ->references('code')
                ->on('account_types')
                ->onDelete('cascade');

            $table->foreign(['type_code', 'group_code'])
                ->references(['type_code', 'code'])
                ->on('account_groups')
                ->onDelete('cascade');

            $table->unique(['type_code', 'group_code', 'code']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('account_classes');
    }
}; 