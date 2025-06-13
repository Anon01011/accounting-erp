<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('asset_categories')) {
        Schema::create('asset_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
                $table->string('depreciation_method')->default('straight_line');
                $table->decimal('default_depreciation_rate', 5, 2)->default(0);
                $table->integer('default_useful_life')->default(0);
                $table->boolean('is_active')->default(true);
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('restrict');
                $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('restrict');
            $table->timestamps();
                $table->softDeletes();
        });
        } else {
            Schema::table('asset_categories', function (Blueprint $table) {
                if (!Schema::hasColumn('asset_categories', 'code')) {
                    $table->string('code')->unique();
                }
                if (!Schema::hasColumn('asset_categories', 'name')) {
                    $table->string('name');
                }
                if (!Schema::hasColumn('asset_categories', 'description')) {
                    $table->text('description')->nullable();
                }
                if (!Schema::hasColumn('asset_categories', 'depreciation_method')) {
                    $table->string('depreciation_method')->default('straight_line');
                }
                if (!Schema::hasColumn('asset_categories', 'default_depreciation_rate')) {
                    $table->decimal('default_depreciation_rate', 5, 2)->default(0);
                }
                if (!Schema::hasColumn('asset_categories', 'default_useful_life')) {
                    $table->integer('default_useful_life')->default(0);
                }
                if (!Schema::hasColumn('asset_categories', 'is_active')) {
                    $table->boolean('is_active')->default(true);
                }
                if (!Schema::hasColumn('asset_categories', 'created_by')) {
                    $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('restrict');
                }
                if (!Schema::hasColumn('asset_categories', 'updated_by')) {
                    $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('restrict');
                }
                if (!Schema::hasColumn('asset_categories', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('asset_categories');
    }
}; 