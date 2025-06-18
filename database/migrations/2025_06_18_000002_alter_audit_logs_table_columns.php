<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            if (Schema::hasColumn('audit_logs', 'model_type')) {
                $table->renameColumn('model_type', 'auditable_type');
            }
            if (Schema::hasColumn('audit_logs', 'model_id')) {
                $table->renameColumn('model_id', 'auditable_id');
            }
            if (!Schema::hasColumn('audit_logs', 'event')) {
                $table->string('event')->after('user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            if (Schema::hasColumn('audit_logs', 'auditable_type')) {
                $table->renameColumn('auditable_type', 'model_type');
            }
            if (Schema::hasColumn('audit_logs', 'auditable_id')) {
                $table->renameColumn('auditable_id', 'model_id');
            }
            if (Schema::hasColumn('audit_logs', 'event')) {
                $table->dropColumn('event');
            }
        });
    }
};
