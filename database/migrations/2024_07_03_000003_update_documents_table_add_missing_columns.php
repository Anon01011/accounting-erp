<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (Schema::hasTable('documents')) {
            Schema::table('documents', function (Blueprint $table) {
                if (!Schema::hasColumn('documents', 'name')) {
                    $table->string('name');
                }
                if (!Schema::hasColumn('documents', 'file_path')) {
                    $table->string('file_path');
                }
                if (!Schema::hasColumn('documents', 'file_type')) {
                    $table->string('file_type');
                }
                if (!Schema::hasColumn('documents', 'file_size')) {
                    $table->bigInteger('file_size');
                }
                if (!Schema::hasColumn('documents', 'documentable_type')) {
                    $table->string('documentable_type');
                }
                if (!Schema::hasColumn('documents', 'documentable_id')) {
                    $table->unsignedBigInteger('documentable_id');
                }
                if (!Schema::hasColumn('documents', 'user_id')) {
                    $table->unsignedBigInteger('user_id');
                }
                if (!Schema::hasColumn('documents', 'description')) {
                    $table->text('description')->nullable();
                }
                if (!Schema::hasColumn('documents', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('documents', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
                if (!Schema::hasColumn('documents', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }
    }

    public function down()
    {
        // No down migration for safety
    }
}; 