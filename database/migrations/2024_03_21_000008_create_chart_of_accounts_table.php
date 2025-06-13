<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('chart_of_accounts')) {
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
        } else {
            Schema::table('chart_of_accounts', function (Blueprint $table) {
                if (!Schema::hasColumn('chart_of_accounts', 'type_code')) {
                    $table->string('type_code', 2)->comment('Account Type Code (e.g., 01 for Assets)');
                }
                if (!Schema::hasColumn('chart_of_accounts', 'group_code')) {
                    $table->string('group_code', 2)->comment('Account Group Code');
                }
                if (!Schema::hasColumn('chart_of_accounts', 'class_code')) {
                    $table->string('class_code', 3)->comment('Account Class Code');
                }
                if (!Schema::hasColumn('chart_of_accounts', 'account_code')) {
                    $table->string('account_code', 9)->comment('Account Code');
                }
                if (!Schema::hasColumn('chart_of_accounts', 'name')) {
                    $table->string('name');
                }
                if (!Schema::hasColumn('chart_of_accounts', 'description')) {
                    $table->text('description')->nullable();
                }
                if (!Schema::hasColumn('chart_of_accounts', 'parent_id')) {
                    $table->foreignId('parent_id')->nullable()->constrained('chart_of_accounts')->onDelete('restrict');
                }
                if (!Schema::hasColumn('chart_of_accounts', 'is_active')) {
                    $table->boolean('is_active')->default(true);
                }
                if (!Schema::hasColumn('chart_of_accounts', 'created_by')) {
                    $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                }
                if (!Schema::hasColumn('chart_of_accounts', 'updated_by')) {
                    $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
                }
                if (!Schema::hasColumn('chart_of_accounts', 'deleted_at')) {
                    $table->softDeletes();
                }

                // Add indexes if they don't exist
                if (!Schema::hasIndex('chart_of_accounts', 'chart_of_accounts_type_code_group_code_class_code_index')) {
                    $table->index(['type_code', 'group_code', 'class_code']);
                }
                if (!Schema::hasIndex('chart_of_accounts', 'chart_of_accounts_account_code_index')) {
                    $table->index('account_code');
                }
                if (!Schema::hasIndex('chart_of_accounts', 'chart_of_accounts_is_active_index')) {
                    $table->index('is_active');
                }
                if (!Schema::hasIndex('chart_of_accounts', 'chart_of_accounts_parent_id_index')) {
                    $table->index('parent_id');
                }

                // Add unique constraint if it doesn't exist
                if (!Schema::hasIndex('chart_of_accounts', 'unique_account_code')) {
                    $table->unique(['type_code', 'group_code', 'class_code', 'account_code'], 'unique_account_code');
                }
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('chart_of_accounts');
    }
}; 