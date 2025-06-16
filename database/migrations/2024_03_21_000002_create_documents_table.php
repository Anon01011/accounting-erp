<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // public function up()
    // {
    //     Schema::create('documents', function (Blueprint $table) {
    //         $table->id();
    //         $table->string('name');
    //         $table->string('file_path');
    //         $table->string('file_type');
    //         $table->bigInteger('file_size');
    //         $table->string('documentable_type');
    //         $table->unsignedBigInteger('documentable_id');
    //         $table->unsignedBigInteger('user_id');
    //         $table->text('description')->nullable();
    //         $table->timestamps();
    //         $table->softDeletes();
    //     });
    // }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
}; 