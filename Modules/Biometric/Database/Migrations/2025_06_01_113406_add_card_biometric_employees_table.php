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
        if (!Schema::hasColumn('biometric_employees', 'card_number')) {
            if (Schema::hasTable('biometric_employees')) {
        Schema::table('biometric_employees', function (Blueprint $table) {
                if (!Schema::hasColumn('biometric_employees', 'card_number')) {
                    $table->string('card_number')->nullable()->after('user_id');
                }
        });
    }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('biometric_employees', 'card_number')) {
            if (Schema::hasTable('biometric_employees')) {
        Schema::table('biometric_employees', function (Blueprint $table) {
                if (Schema::hasColumn('biometric_employees', 'card_number')) {
                    $table->dropColumn('card_number');
                }
        });
    }
        }
    }
};
