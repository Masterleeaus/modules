<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Stores geofence radius (metres) in biometric_settings so it is company-configurable
     * and not hard-coded anywhere.
     */
    public function up(): void
    {
        if (! Schema::hasTable('biometric_settings')) {
            return;
        }

        Schema::table('biometric_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('biometric_settings', 'geofence_radius')) {
                // radius in metres; default 200 m is a sensible job-site boundary
                $table->unsignedInteger('geofence_radius')->default(200)->after('company_id');
            }

            if (! Schema::hasColumn('biometric_settings', 'geofence_enabled')) {
                $table->boolean('geofence_enabled')->default(false)->after('geofence_radius');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('biometric_settings')) {
            return;
        }

        Schema::table('biometric_settings', function (Blueprint $table) {
            foreach (['geofence_radius', 'geofence_enabled'] as $col) {
                if (Schema::hasColumn('biometric_settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
