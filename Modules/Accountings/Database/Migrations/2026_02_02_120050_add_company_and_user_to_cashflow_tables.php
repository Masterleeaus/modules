<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * HOTFIX (legacy-safe):
     * Add tenant columns ONLY. Do NOT create indexes here.
     * Existing environments may already have legacy indexes which would cause
     * MySQL 1061 "Duplicate key name" if we attempt to add them again.
     */
    public function up(): void
    {
        $tables = ['acc_recurring_expenses', 'acc_cashflow_budgets'];

        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $t) use ($table) {
                if (!Schema::hasColumn($table, 'company_id')) {
                    $t->unsignedBigInteger('company_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn($table, 'user_id')) {
                    $t->unsignedBigInteger('user_id')->nullable()->after('company_id');
                }
            });
        }
    }

    public function down(): void
    {
        // Intentionally non-destructive (no column drops).
    }
};
