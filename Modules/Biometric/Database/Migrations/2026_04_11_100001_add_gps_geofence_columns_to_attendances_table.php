<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Extends the core attendances table with biometric/GPS/geofence fields.
     */
    public function up(): void
    {
        if (! Schema::hasTable('attendances')) {
            return;
        }

        Schema::table('attendances', function (Blueprint $table) {
            if (! Schema::hasColumn('attendances', 'clock_in_lat')) {
                $table->decimal('clock_in_lat', 10, 7)->nullable()->after('longitude');
            }

            if (! Schema::hasColumn('attendances', 'clock_in_lng')) {
                $table->decimal('clock_in_lng', 10, 7)->nullable()->after('clock_in_lat');
            }

            if (! Schema::hasColumn('attendances', 'clock_out_lat')) {
                $table->decimal('clock_out_lat', 10, 7)->nullable()->after('clock_in_lng');
            }

            if (! Schema::hasColumn('attendances', 'clock_out_lng')) {
                $table->decimal('clock_out_lng', 10, 7)->nullable()->after('clock_out_lat');
            }

            if (! Schema::hasColumn('attendances', 'clock_in_method')) {
                // fingerprint, face, nfc, gps, pin, manual
                $table->string('clock_in_method')->default('manual')->after('clock_out_lng');
            }

            if (! Schema::hasColumn('attendances', 'clock_out_method')) {
                $table->string('clock_out_method')->default('manual')->after('clock_in_method');
            }

            if (! Schema::hasColumn('attendances', 'geofence_passed')) {
                $table->boolean('geofence_passed')->default(true)->after('clock_out_method');
            }

            if (! Schema::hasColumn('attendances', 'booking_id')) {
                // nullable FK – links biometric clock-in to an active BookingModule booking
                $table->unsignedBigInteger('booking_id')->nullable()->after('geofence_passed');
            }

            if (! Schema::hasColumn('attendances', 'biometric_device_id')) {
                $table->string('biometric_device_id')->nullable()->after('booking_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('attendances')) {
            return;
        }

        Schema::table('attendances', function (Blueprint $table) {
            $columns = [
                'clock_in_lat',
                'clock_in_lng',
                'clock_out_lat',
                'clock_out_lng',
                'clock_in_method',
                'clock_out_method',
                'geofence_passed',
                'booking_id',
                'biometric_device_id',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('attendances', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
