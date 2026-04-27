<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('e_invoice_company_settings') && !Schema::hasColumn('e_invoice_company_settings', 'company_id')) {
            Schema::table('e_invoice_company_settings', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('e_invoice_settings') && !Schema::hasColumn('e_invoice_settings', 'company_id')) {
            Schema::table('e_invoice_settings', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        // intentionally non-destructive
    }
};
