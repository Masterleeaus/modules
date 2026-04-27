<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('journal_lines')) {
            Schema::create('journal_lines', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('journal_entry_id')->index();
                $table->unsignedBigInteger('account_id')->index();
                $table->decimal('debit', 12, 2)->default(0);
                $table->decimal('credit', 12, 2)->default(0);
                $table->text('description')->nullable();
                $table->unsignedBigInteger('tax_code_id')->nullable()->index();

                $table->foreign('journal_entry_id')
                    ->references('id')
                    ->on('journal_entries')
                    ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_lines');
    }
};
