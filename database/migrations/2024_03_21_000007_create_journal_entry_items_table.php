<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJournalEntryItemsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('journal_entry_items')) {
            Schema::create('journal_entry_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('journal_entry_id')->constrained()->onDelete('cascade');
                $table->foreignId('chart_of_account_id')->constrained();
                $table->decimal('debit', 15, 2)->default(0);
                $table->decimal('credit', 15, 2)->default(0);
                $table->text('description')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users');
                $table->foreignId('updated_by')->nullable()->constrained('users');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('journal_entry_items');
    }
}