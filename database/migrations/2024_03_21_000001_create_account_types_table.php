<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('account_types')) {
            Schema::create('account_types', function (Blueprint $table) {
                $table->id();
                $table->string('code', 2)->unique();
                $table->string('name');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        } else {
            Schema::table('account_types', function (Blueprint $table) {
                if (!Schema::hasColumn('account_types', 'code')) {
                    $table->string('code', 2)->unique();
                }
                if (!Schema::hasColumn('account_types', 'name')) {
                    $table->string('name');
                }
                if (!Schema::hasColumn('account_types', 'description')) {
                    $table->text('description')->nullable();
                }
                if (!Schema::hasColumn('account_types', 'is_active')) {
                    $table->boolean('is_active')->default(true);
                }
                if (!Schema::hasColumn('account_types', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('account_types');
    }
}; 