<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the chart_of_accounts table.
 *
 * This is a NEW table with no existing core equivalent.
 * The legacy acc_coa table remains for journal/BS/P&L mapping;
 * chart_of_accounts is the canonical accounting chart used for
 * revenue/expense account codes on invoices and expenses.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('chart_of_accounts')) {
            Schema::create('chart_of_accounts', function (Blueprint $table) {
                $table->id();
                $table->string('code', 20)->comment('e.g. 4000, 6100');
                $table->string('name', 191)->comment('e.g. Cleaning Revenue, Vehicle Expenses');
                $table->string('type', 30)->comment('asset|liability|equity|revenue|expense');
                $table->string('sub_type', 50)->nullable()->comment('current_asset|accounts_receivable|...');
                $table->boolean('is_active')->default(true)->index();
                $table->unsignedBigInteger('company_id')->index();
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                // A company's account codes must be unique within that company
                $table->unique(['company_id', 'code'], 'coa_company_code_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};
