<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add fields to assets table
        Schema::table('assets', function (Blueprint $table) {
            if (!Schema::hasColumn('assets', 'acquisition_cost')) {
                $table->decimal('acquisition_cost', 15, 2)->default(0)->after('purchase_price');
            }
            if (!Schema::hasColumn('assets', 'salvage_value')) {
                $table->decimal('salvage_value', 15, 2)->default(0)->after('acquisition_cost');
            }
            if (!Schema::hasColumn('assets', 'disposal_date')) {
                $table->date('disposal_date')->nullable()->after('salvage_value');
            }
            if (!Schema::hasColumn('assets', 'disposal_value')) {
                $table->decimal('disposal_value', 15, 2)->default(0)->after('disposal_date');
            }
            if (!Schema::hasColumn('assets', 'disposal_method')) {
                $table->string('disposal_method')->nullable()->after('disposal_value');
            }
            if (!Schema::hasColumn('assets', 'tax_depreciation_method')) {
                $table->string('tax_depreciation_method')->nullable()->after('disposal_method');
            }
            if (!Schema::hasColumn('assets', 'tax_depreciation_rate')) {
                $table->decimal('tax_depreciation_rate', 5, 2)->default(0)->after('tax_depreciation_method');
            }
            if (!Schema::hasColumn('assets', 'tax_useful_life')) {
                $table->integer('tax_useful_life')->default(0)->after('tax_depreciation_rate');
            }
            if (!Schema::hasColumn('assets', 'tax_accumulated_depreciation')) {
                $table->decimal('tax_accumulated_depreciation', 15, 2)->default(0)->after('tax_useful_life');
            }
            if (!Schema::hasColumn('assets', 'tax_current_value')) {
                $table->decimal('tax_current_value', 15, 2)->default(0)->after('tax_accumulated_depreciation');
            }
        });

        // Add fields to asset_details table
        Schema::table('asset_details', function (Blueprint $table) {
            if (!Schema::hasColumn('asset_details', 'residual_value')) {
                $table->decimal('residual_value', 15, 2)->default(0)->after('useful_life');
            }
            if (!Schema::hasColumn('asset_details', 'revaluation_frequency')) {
                $table->string('revaluation_frequency')->nullable()->after('residual_value');
            }
            if (!Schema::hasColumn('asset_details', 'last_revaluation_date')) {
                $table->date('last_revaluation_date')->nullable()->after('revaluation_frequency');
            }
            if (!Schema::hasColumn('asset_details', 'next_revaluation_date')) {
                $table->date('next_revaluation_date')->nullable()->after('last_revaluation_date');
            }
            if (!Schema::hasColumn('asset_details', 'depreciation_start_date')) {
                $table->date('depreciation_start_date')->nullable()->after('next_revaluation_date');
            }
            if (!Schema::hasColumn('asset_details', 'depreciation_end_date')) {
                $table->date('depreciation_end_date')->nullable()->after('depreciation_start_date');
            }
        });

        // Create asset_revaluations table
        if (!Schema::hasTable('asset_revaluations')) {
            Schema::create('asset_revaluations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
                $table->date('revaluation_date');
                $table->decimal('previous_value', 15, 2);
                $table->decimal('new_value', 15, 2);
                $table->text('revaluation_reason');
                $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('restrict');
                $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->onDelete('restrict');
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('restrict');
                $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('restrict');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // Create asset_impairments table
        if (!Schema::hasTable('asset_impairments')) {
            Schema::create('asset_impairments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
                $table->date('impairment_date');
                $table->decimal('impairment_amount', 15, 2);
                $table->text('impairment_reason');
                $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('restrict');
                $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->onDelete('restrict');
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('restrict');
                $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('restrict');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // Add fields to asset_transactions table
        Schema::table('asset_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('asset_transactions', 'transaction_type')) {
                $table->string('transaction_type')->after('type');
            }
            if (!Schema::hasColumn('asset_transactions', 'journal_entry_id')) {
                $table->foreignId('journal_entry_id')->nullable()->after('reference_id')->constrained('journal_entries')->onDelete('restrict');
            }
            if (!Schema::hasColumn('asset_transactions', 'tax_related')) {
                $table->boolean('tax_related')->default(false)->after('journal_entry_id');
            }
            if (!Schema::hasColumn('asset_transactions', 'tax_amount')) {
                $table->decimal('tax_amount', 15, 2)->default(0)->after('tax_related');
            }
        });
    }

    public function down()
    {
        // Remove fields from assets table
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn([
                'acquisition_cost',
                'salvage_value',
                'disposal_date',
                'disposal_value',
                'disposal_method',
                'tax_depreciation_method',
                'tax_depreciation_rate',
                'tax_useful_life',
                'tax_accumulated_depreciation',
                'tax_current_value'
            ]);
        });

        // Remove fields from asset_details table
        Schema::table('asset_details', function (Blueprint $table) {
            $table->dropColumn([
                'residual_value',
                'revaluation_frequency',
                'last_revaluation_date',
                'next_revaluation_date',
                'depreciation_start_date',
                'depreciation_end_date'
            ]);
        });

        // Remove fields from asset_transactions table
        Schema::table('asset_transactions', function (Blueprint $table) {
            $table->dropColumn([
                'transaction_type',
                'journal_entry_id',
                'tax_related',
                'tax_amount'
            ]);
        });

        // Drop tables
        Schema::dropIfExists('asset_revaluations');
        Schema::dropIfExists('asset_impairments');
    }
}; 