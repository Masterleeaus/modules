<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('suppliers', function (Blueprint $table) {
            if (!Schema::hasColumn('suppliers', 'fsm_rating')) {
                $table->unsignedTinyInteger('fsm_rating')->nullable()->after('email');
            }
            if (!Schema::hasColumn('suppliers', 'fsm_lead_time_days')) {
                $table->unsignedSmallInteger('fsm_lead_time_days')->nullable()->after('fsm_rating');
            }
            if (!Schema::hasColumn('suppliers', 'fsm_payment_terms')) {
                $table->string('fsm_payment_terms')->nullable()->after('fsm_lead_time_days');
            }
        });
    }
    public function down(): void {
        Schema::table('suppliers', function (Blueprint $table) {
            $cols = array_filter(
                ['fsm_rating', 'fsm_lead_time_days', 'fsm_payment_terms'],
                fn($c) => Schema::hasColumn('suppliers', $c)
            );
            if ($cols) {
                $table->dropColumn(array_values($cols));
            }
        });
    }
};
