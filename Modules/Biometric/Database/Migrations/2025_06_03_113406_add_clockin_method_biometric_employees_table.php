<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('biometric_employees')) {
        Schema::table('biometric_employees', function (Blueprint $table) {
            if (!Schema::hasColumn('biometric_employees', 'force_biometric_clockin')) {
                $table->boolean('force_biometric_clockin')->default(true)->after('company_id');
            }
        });
    }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('biometric_employees')) {
        Schema::table('biometric_employees', function (Blueprint $table) {
            if (Schema::hasColumn('biometric_employees', 'force_biometric_clockin')) {
                $table->dropColumn('force_biometric_clockin');
            }
        });
    }
    }
};
