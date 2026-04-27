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
            if (!Schema::hasColumn('biometric_employees', 'has_photo')) {
                $table->boolean('has_photo')->default(false)->after('user_id');
            }
            if (!Schema::hasColumn('biometric_employees', 'photo')) {
                $table->text('photo')->nullable()->after('has_photo');
            }
            if (!Schema::hasColumn('biometric_employees', 'has_card')) {
                $table->boolean('has_card')->default(false)->before('card_number');
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
            if (Schema::hasColumn('biometric_employees', 'has_photo')) {
                $table->dropColumn('has_photo');
            }
            if (Schema::hasColumn('biometric_employees', 'photo')) {
                $table->dropColumn('photo');
            }
            if (Schema::hasColumn('biometric_employees', 'has_card')) {
                $table->dropColumn('has_card');
            }
        });
    }
    }
};
