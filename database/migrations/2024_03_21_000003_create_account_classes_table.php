<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('account_classes')) {
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
        } else {
            Schema::table('account_classes', function (Blueprint $table) {
                if (!Schema::hasColumn('account_classes', 'type_code')) {
                    $table->string('type_code', 2);
                }
                if (!Schema::hasColumn('account_classes', 'group_code')) {
                    $table->string('group_code', 2);
                }
                if (!Schema::hasColumn('account_classes', 'code')) {
                    $table->string('code', 3);
                }
                if (!Schema::hasColumn('account_classes', 'name')) {
                    $table->string('name');
                }
                if (!Schema::hasColumn('account_classes', 'description')) {
                    $table->text('description')->nullable();
                }
                if (!Schema::hasColumn('account_classes', 'is_active')) {
                    $table->boolean('is_active')->default(true);
                }
                if (!Schema::hasColumn('account_classes', 'deleted_at')) {
                    $table->softDeletes();
                }

                // Add foreign keys if they don't exist
                if (!Schema::hasColumn('account_classes', 'type_code')) {
                    $table->foreign('type_code')
                        ->references('code')
                        ->on('account_types')
                        ->onDelete('cascade');
                }

                if (!Schema::hasColumn('account_classes', ['type_code', 'group_code'])) {
                    $table->foreign(['type_code', 'group_code'])
                        ->references(['type_code', 'code'])
                        ->on('account_groups')
                        ->onDelete('cascade');
                }

                // Add unique constraint if it doesn't exist
                if (!Schema::hasIndex('account_classes', 'account_classes_type_code_group_code_code_unique')) {
                    $table->unique(['type_code', 'group_code', 'code']);
                }
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('account_classes');
    }
}; 