<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('acc_cashflow_budgets') && !Schema::hasColumn('acc_cashflow_budgets', 'company_id')) {
            Schema::table('acc_cashflow_budgets', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('acc_coa') && !Schema::hasColumn('acc_coa', 'company_id')) {
            Schema::table('acc_coa', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('acc_journald') && !Schema::hasColumn('acc_journald', 'company_id')) {
            Schema::table('acc_journald', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('acc_journalh') && !Schema::hasColumn('acc_journalh', 'company_id')) {
            Schema::table('acc_journalh', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('acc_map_bs') && !Schema::hasColumn('acc_map_bs', 'company_id')) {
            Schema::table('acc_map_bs', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('acc_map_pnl') && !Schema::hasColumn('acc_map_pnl', 'company_id')) {
            Schema::table('acc_map_pnl', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('acc_recurring_expenses') && !Schema::hasColumn('acc_recurring_expenses', 'company_id')) {
            Schema::table('acc_recurring_expenses', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('acc_type_journal') && !Schema::hasColumn('acc_type_journal', 'company_id')) {
            Schema::table('acc_type_journal', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        // intentionally non-destructive
    }
};
