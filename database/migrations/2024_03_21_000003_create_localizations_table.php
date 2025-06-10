<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('localizations')) {
            Schema::create('localizations', function (Blueprint $table) {
                $table->id();
                $table->string('language');
                $table->string('locale');
                $table->string('date_format')->default('Y-m-d');
                $table->string('time_format')->default('H:i:s');
                $table->string('timezone')->default('UTC');
                $table->string('currency')->default('USD');
                $table->string('currency_symbol')->default('$');
                $table->string('currency_position')->default('before');
                $table->string('thousand_separator')->default(',');
                $table->string('decimal_separator')->default('.');
                $table->integer('decimal_places')->default(2);
                $table->boolean('status')->default(true);
                $table->boolean('is_default')->default(false);
                $table->foreignId('created_by')->nullable()->constrained('users');
                $table->foreignId('updated_by')->nullable()->constrained('users');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('localizations');
    }
}; 