<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('biometric_commands') && !Schema::hasColumn('biometric_commands', 'company_id')) {
            Schema::table('biometric_commands', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('biometric_device_attendances') && !Schema::hasColumn('biometric_device_attendances', 'company_id')) {
            Schema::table('biometric_device_attendances', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('biometric_devices') && !Schema::hasColumn('biometric_devices', 'company_id')) {
            Schema::table('biometric_devices', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('biometric_employees') && !Schema::hasColumn('biometric_employees', 'company_id')) {
            Schema::table('biometric_employees', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('biometric_global_settings') && !Schema::hasColumn('biometric_global_settings', 'company_id')) {
            Schema::table('biometric_global_settings', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('biometric_settings') && !Schema::hasColumn('biometric_settings', 'company_id')) {
            Schema::table('biometric_settings', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        // intentionally non-destructive
    }
};
