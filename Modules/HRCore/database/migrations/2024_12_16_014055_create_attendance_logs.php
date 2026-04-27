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
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained('attendances')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Date and time fields for easier querying
            $table->date('date')->comment('Log date');
            $table->time('time')->comment('Log time');
            $table->dateTime('logged_at')->comment('Exact timestamp of the log');

            // Type of log entry: check_in, check_out, break_start, break_end, location_update, regularization
            $table->string('type', 50)->default('check_in')->comment('Type of attendance log entry');

            // Action taken: manual, automatic, system, api, mobile, web, biometric
            $table->string('action_type', 50)->nullable()->comment('How the action was triggered');

            $table->foreignId('shift_id')->nullable()->constrained('shifts')->onDelete('set null');

            // Enhanced location tracking
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('altitude', 10, 2)->nullable();
            $table->decimal('speed', 10, 2)->nullable();
            $table->decimal('speedAccuracy', 10, 2)->nullable();
            $table->decimal('horizontalAccuracy', 10, 2)->nullable();
            $table->decimal('verticalAccuracy', 10, 2)->nullable();
            $table->decimal('course', 10, 2)->nullable();
            $table->decimal('courseAccuracy', 10, 2)->nullable();

            // Location metadata
            $table->string('address', 1000)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->decimal('distance_from_office', 10, 2)->nullable()->comment('Distance in meters from office location');

            // Device and network information
            $table->string('device_type', 50)->nullable()->comment('mobile, tablet, desktop, biometric');
            $table->string('device_model')->nullable();
            $table->string('device_id')->nullable()->comment('Unique device identifier');
            $table->string('os_type', 50)->nullable()->comment('ios, android, windows, mac, linux');
            $table->string('os_version', 50)->nullable();
            $table->string('app_version', 50)->nullable();
            $table->string('browser', 100)->nullable();
            $table->string('browser_version', 50)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('network_type', 50)->nullable()->comment('wifi, mobile, ethernet');
            $table->string('carrier', 100)->nullable();

            // Validation and verification
            $table->boolean('is_location_verified')->default(false);
            $table->boolean('is_device_verified')->default(false);
            $table->boolean('is_suspicious')->default(false);
            $table->text('suspicious_reason')->nullable();

            // Break information (if type is break_start or break_end)
            $table->string('break_type', 50)->nullable()->comment('lunch, tea, personal, other');
            $table->integer('break_duration')->nullable()->comment('Duration in minutes');

            // Regularization information (if type is regularization)
            $table->foreignId('regularization_id')->nullable()->constrained('attendance_regularizations')->onDelete('set null');
            $table->text('regularization_reason')->nullable();

            // Addon verification references (links to addon-specific verification log tables)
            // These fields store the verification log ID from the respective addon's verification table
            // The actual verification details are stored in the addon's own tables

            // GeofenceSystem addon - references geofence_verification_logs table
            $table->unsignedBigInteger('geofence_verification_log_id')->nullable()
                ->comment('References geofence_verification_logs.id when GeofenceSystem addon is enabled');

            // FaceAttendance addon - references face_data table for user's face data
            $table->unsignedBigInteger('face_data_id')->nullable()
                ->comment('References face_data.id when FaceAttendance addon is enabled');
            $table->decimal('face_confidence', 5, 2)->nullable()
                ->comment('Face match confidence % when FaceAttendance addon is used');

            // QRAttendance addon - references qr_code_verification_logs table
            $table->unsignedBigInteger('qr_verification_log_id')->nullable()
                ->comment('References qr_code_verification_logs.id when QRAttendance addon is enabled');

            // DynamicQrAttendance addon - references dynamic_qr_verification_logs table
            $table->unsignedBigInteger('dynamic_qr_verification_log_id')->nullable()
                ->comment('References dynamic_qr_verification_logs.id when DynamicQrAttendance addon is enabled');

            // IpAddressAttendance addon - references ip_address_verification_logs table
            $table->unsignedBigInteger('ip_verification_log_id')->nullable()
                ->comment('References ip_address_verification_logs.id when IpAddressAttendance addon is enabled');

            // SiteAttendance addon - references sites table
            $table->unsignedBigInteger('site_id')->nullable()
                ->comment('References sites.id when SiteAttendance addon is enabled');

            // Generic verification method field to track which addon was used
            $table->string('verification_method', 50)->nullable()
                ->comment('web, mobile, geofence, face, qr, dynamic_qr, ip, site, biometric');

            // Additional metadata
            $table->string('notes')->nullable();
            $table->string('status', 50)->default('active')->comment('active, cancelled, disputed');
            $table->json('metadata')->nullable()->comment('Additional flexible data in JSON format');

            // Performance and sync tracking
            $table->boolean('is_synced')->default(true);
            $table->dateTime('synced_at')->nullable();
            $table->string('sync_error')->nullable();
            $table->integer('retry_count')->default(0);

            // Audit fields
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('approved_at')->nullable();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('tenant_id', 191)->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['attendance_id', 'type']);
            $table->index(['user_id', 'date']);
            $table->index(['date', 'type']);
            $table->index(['user_id', 'logged_at']);
            $table->index('device_id');
            $table->index('ip_address');
            $table->index(['is_suspicious', 'date']);
            $table->index('status');
            $table->index(['is_synced', 'retry_count']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
