<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('account_groups')) {
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
        } else {
            Schema::table('account_groups', function (Blueprint $table) {
                if (!Schema::hasColumn('account_groups', 'type_code')) {
                    $table->string('type_code', 2);
                }
                if (!Schema::hasColumn('account_groups', 'code')) {
                    $table->string('code', 2);
                }
                if (!Schema::hasColumn('account_groups', 'name')) {
                    $table->string('name');
                }
                if (!Schema::hasColumn('account_groups', 'description')) {
                    $table->text('description')->nullable();
                }
                if (!Schema::hasColumn('account_groups', 'is_active')) {
                    $table->boolean('is_active')->default(true);
                }
                if (!Schema::hasColumn('account_groups', 'deleted_at')) {
                    $table->softDeletes();
                }

                // Add foreign key if it doesn't exist
                if (!Schema::hasColumn('account_groups', 'type_code')) {
                    $table->foreign('type_code')
                        ->references('code')
                        ->on('account_types')
                        ->onDelete('cascade');
                }

                // Add unique constraint if it doesn't exist
                if (!Schema::hasIndex('account_groups', 'account_groups_type_code_code_unique')) {
                    $table->unique(['type_code', 'code']);
                }
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('account_groups');
    }
}; 