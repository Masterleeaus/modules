<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('acc_coa')) {
            return;
        }
        Schema::table('acc_coa', function (Blueprint $table) {
            if (!Schema::hasColumn('acc_coa', 'is_cash_account')) {
                $table->boolean('is_cash_account')->default(false)->after('coa_desc');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('acc_coa')) {
            return;
        }
        Schema::table('acc_coa', function (Blueprint $table) {
            if (Schema::hasColumn('acc_coa', 'is_cash_account')) {
                $table->dropColumn('is_cash_account');
            }
        });
    }
};
