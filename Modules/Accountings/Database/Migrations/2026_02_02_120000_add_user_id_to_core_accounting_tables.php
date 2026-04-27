<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = ['acc_coa','acc_journalh','acc_journald','acc_map_pnl','acc_map_bs','acc_type_journal'];
        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'user_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->unsignedBigInteger('user_id')->nullable()->after('company_id')->index();
                    $t->index(['company_id', 'user_id']);
                });
            } elseif (Schema::hasTable($table) && Schema::hasColumn($table, 'user_id')) {
                try {
                    Schema::table($table, function (Blueprint $t) {
                        $t->index(['company_id', 'user_id']);
                    });
                } catch (\Throwable $e) {}
            }
        }
    }

    public function down(): void
    {
        $tables = ['acc_coa','acc_journalh','acc_journald','acc_map_pnl','acc_map_bs','acc_type_journal'];
        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'user_id')) {
                Schema::table($table, function (Blueprint $t) { $t->dropColumn('user_id'); });
            }
        }
    }
};
