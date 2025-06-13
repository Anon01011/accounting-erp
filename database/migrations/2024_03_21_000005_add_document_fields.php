<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('asset_documents')) {
            Schema::table('asset_documents', function (Blueprint $table) {
                if (Schema::hasColumn('asset_documents', 'category')) {
                    $table->dropColumn('category');
                }
                if (Schema::hasColumn('asset_documents', 'file_size')) {
                    $table->dropColumn('file_size');
                }
                if (Schema::hasColumn('asset_documents', 'mime_type')) {
                    $table->dropColumn('mime_type');
                }
                if (Schema::hasColumn('asset_documents', 'version')) {
                    $table->dropColumn('version');
                }
                if (Schema::hasColumn('asset_documents', 'documentable_type')) {
                    $table->dropColumn('documentable_type');
                }
                if (Schema::hasColumn('asset_documents', 'documentable_id')) {
                    $table->dropColumn('documentable_id');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('asset_documents')) {
            Schema::table('asset_documents', function (Blueprint $table) {
                if (!Schema::hasColumn('asset_documents', 'category')) {
                    $table->string('category')->nullable();
                }
                if (!Schema::hasColumn('asset_documents', 'file_size')) {
                    $table->integer('file_size')->nullable();
                }
                if (!Schema::hasColumn('asset_documents', 'mime_type')) {
                    $table->string('mime_type')->nullable();
                }
                if (!Schema::hasColumn('asset_documents', 'version')) {
                    $table->string('version')->nullable();
                }
                if (!Schema::hasColumn('asset_documents', 'documentable_type')) {
                    $table->string('documentable_type')->nullable();
                }
                if (!Schema::hasColumn('asset_documents', 'documentable_id')) {
                    $table->unsignedBigInteger('documentable_id')->nullable();
                }
            });
        }
    }
}; 