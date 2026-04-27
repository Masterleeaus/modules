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
        Schema::create('attendance_breaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained('attendances')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Date field for easier querying
            $table->date('date')->comment('Break date');

            // Break timing
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->time('start_time_only')->virtualAs('TIME(start_time)')->nullable();
            $table->time('end_time_only')->virtualAs('TIME(end_time)')->nullable();

            // Duration tracking
            $table->decimal('duration', 8, 2)->nullable()->comment('Duration in minutes');
            $table->decimal('scheduled_duration', 8, 2)->nullable()->comment('Scheduled break duration in minutes');
            $table->boolean('exceeded_limit')->default(false);

            // Break type and reason
            $table->string('break_type', 50)->default('other')->comment('lunch, tea, personal, emergency, other');
            $table->string('reason')->nullable();
            $table->text('notes')->nullable();

            // Status tracking
            $table->string('status', 50)->default('ongoing')->comment('ongoing, completed, cancelled, disputed');
            $table->boolean('is_paid')->default(false)->comment('Whether this break is paid or unpaid');
            $table->boolean('is_scheduled')->default(false)->comment('Whether this was a scheduled break');

            // Location tracking for break start
            $table->decimal('start_latitude', 10, 8)->nullable();
            $table->decimal('start_longitude', 11, 8)->nullable();
            $table->string('start_location')->nullable();

            // Location tracking for break end
            $table->decimal('end_latitude', 10, 8)->nullable();
            $table->decimal('end_longitude', 11, 8)->nullable();
            $table->string('end_location')->nullable();

            // Device tracking
            $table->string('start_device_type', 50)->nullable()->comment('mobile, web, biometric');
            $table->string('end_device_type', 50)->nullable();
            $table->string('start_ip')->nullable();
            $table->string('end_ip')->nullable();

            // Validation
            $table->boolean('is_valid')->default(true);
            $table->string('validation_error')->nullable();
            $table->boolean('requires_approval')->default(false);
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('approved_at')->nullable();
            $table->text('approval_notes')->nullable();

            // Alert tracking
            $table->boolean('alert_sent')->default(false);
            $table->dateTime('alert_sent_at')->nullable();

            // Metadata
            $table->json('metadata')->nullable()->comment('Additional flexible data');

            $table->foreignId('created_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('tenant_id', 191)->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Indexes for performance
            $table->index(['attendance_id', 'status']);
            $table->index(['user_id', 'date']);
            $table->index(['date', 'break_type']);
            $table->index(['user_id', 'start_time']);
            $table->index('status');
            $table->index(['is_paid', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_breaks');
    }
};
