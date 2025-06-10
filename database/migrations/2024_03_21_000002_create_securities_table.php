<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('securities')) {
            Schema::create('securities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->boolean('two_factor_enabled')->default(false);
                $table->string('two_factor_secret')->nullable();
                $table->timestamp('last_password_change')->nullable();
                $table->integer('password_expiry_days')->default(90);
                $table->integer('session_timeout_minutes')->default(30);
                $table->integer('login_attempts')->default(0);
                $table->timestamp('last_login_attempt')->nullable();
                $table->json('ip_whitelist')->nullable();
                $table->boolean('status')->default(true);
                $table->foreignId('created_by')->nullable()->constrained('users');
                $table->foreignId('updated_by')->nullable()->constrained('users');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('securities');
    }
}; 