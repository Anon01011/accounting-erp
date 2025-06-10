<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            // Add new columns if they don't exist
            if (!Schema::hasColumn('chart_of_accounts', 'is_master')) {
                $table->boolean('is_master')->default(false)->after('is_active');
            }
            if (!Schema::hasColumn('chart_of_accounts', 'account_level')) {
                $table->tinyInteger('account_level')->default(4)->after('is_master');
            }
            
            // Add indexes with shorter names
            $table->index(['is_master', 'account_level'], 'coa_master_level_idx');
            $table->index(['type_code', 'group_code', 'class_code', 'is_master'], 'coa_type_group_class_master_idx');
        });
    }

    public function down()
    {
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->dropIndex('coa_master_level_idx');
            $table->dropIndex('coa_type_group_class_master_idx');
            
            // Only drop columns if they exist
            if (Schema::hasColumn('chart_of_accounts', 'is_master')) {
                $table->dropColumn('is_master');
            }
            if (Schema::hasColumn('chart_of_accounts', 'account_level')) {
                $table->dropColumn('account_level');
            }
        });
    }
}; 