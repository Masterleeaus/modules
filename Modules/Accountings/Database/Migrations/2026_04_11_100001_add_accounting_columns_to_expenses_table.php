<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Extends the core `expenses` table with accounting-specific columns.
 * These columns are owned by the Accountings module and are dropped on rollback.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('expenses')) {
            return;
        }

        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'account_code')) {
                $table->string('account_code')->nullable()->comment('Expense account code');
            }
            if (!Schema::hasColumn('expenses', 'gst_treatment')) {
                $table->string('gst_treatment')->default('inclusive')->comment('inclusive|exclusive|exempt|zero');
            }
            if (!Schema::hasColumn('expenses', 'gst_amount')) {
                $table->decimal('gst_amount', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('expenses', 'is_tax_deductible')) {
                $table->boolean('is_tax_deductible')->default(true);
            }
            if (!Schema::hasColumn('expenses', 'expense_class')) {
                $table->string('expense_class')->nullable()->comment('operating|capital|personal');
            }
            if (!Schema::hasColumn('expenses', 'exported_to_xero')) {
                $table->boolean('exported_to_xero')->default(false)->index();
            }
            if (!Schema::hasColumn('expenses', 'xero_expense_id')) {
                $table->string('xero_expense_id')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('expenses')) {
            return;
        }

        Schema::table('expenses', function (Blueprint $table) {
            $columns = [
                'account_code',
                'gst_treatment',
                'gst_amount',
                'is_tax_deductible',
                'expense_class',
                'exported_to_xero',
                'xero_expense_id',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('expenses', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
