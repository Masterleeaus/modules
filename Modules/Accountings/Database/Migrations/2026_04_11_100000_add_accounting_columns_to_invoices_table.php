<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Extends the core `invoices` table with accounting-specific columns.
 * These columns are owned by the Accountings module and are dropped on rollback.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('invoices')) {
            return;
        }

        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'accounting_code')) {
                $table->string('accounting_code')->nullable()->comment('Revenue account code');
            }
            if (!Schema::hasColumn('invoices', 'gst_treatment')) {
                $table->string('gst_treatment')->default('inclusive')->comment('inclusive|exclusive|exempt|zero');
            }
            if (!Schema::hasColumn('invoices', 'gst_amount')) {
                $table->decimal('gst_amount', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('invoices', 'exported_to_xero')) {
                $table->boolean('exported_to_xero')->default(false)->index();
            }
            if (!Schema::hasColumn('invoices', 'xero_invoice_id')) {
                $table->string('xero_invoice_id')->nullable()->index();
            }
            if (!Schema::hasColumn('invoices', 'exported_to_myob')) {
                $table->boolean('exported_to_myob')->default(false)->index();
            }
            if (!Schema::hasColumn('invoices', 'myob_invoice_id')) {
                $table->string('myob_invoice_id')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'financial_year')) {
                $table->string('financial_year')->nullable()->comment('Auto-set e.g. FY2025');
            }
            if (!Schema::hasColumn('invoices', 'payment_terms')) {
                $table->string('payment_terms')->default('due_on_receipt');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('invoices')) {
            return;
        }

        Schema::table('invoices', function (Blueprint $table) {
            $columns = [
                'accounting_code',
                'gst_treatment',
                'gst_amount',
                'exported_to_xero',
                'xero_invoice_id',
                'exported_to_myob',
                'myob_invoice_id',
                'financial_year',
                'payment_terms',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('invoices', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
