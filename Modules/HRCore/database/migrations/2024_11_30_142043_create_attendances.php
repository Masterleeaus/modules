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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Core fields
            $table->date('date')->comment('Attendance date');
            $table->dateTime('check_in_time')->nullable();
            $table->dateTime('check_out_time')->nullable();

            // Shift reference
            $table->foreignId('shift_id')->nullable()->constrained('shifts')->onDelete('set null');

            // Calculated hours (updated when check-out or at day end)
            $table->decimal('working_hours', 8, 2)->default(0)->comment('Total working hours');
            $table->decimal('break_hours', 8, 2)->default(0)->comment('Total break hours');
            $table->decimal('overtime_hours', 8, 2)->default(0)->comment('Overtime hours');
            $table->decimal('late_hours', 8, 2)->default(0)->comment('Late check-in hours');
            $table->decimal('early_hours', 8, 2)->default(0)->comment('Early check-out hours');

            // Status: checked_in, checked_out, absent, leave, holiday, weekend, half_day
            $table->string('status', 50)->default('absent')->comment('Attendance status');

            // Day type flags
            $table->boolean('is_holiday')->default(false);
            $table->boolean('is_weekend')->default(false);
            $table->boolean('is_half_day')->default(false);

            // Optional notes
            $table->text('notes')->nullable();

            // Approval (for regularization)
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('approved_at')->nullable();

            // Audit fields
            $table->foreignId('created_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('tenant_id', 191)->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Indexes for performance
            $table->unique(['user_id', 'date']); // One attendance record per user per day
            $table->index(['date', 'status']);
            $table->index('status');
            $table->index('check_in_time');
            $table->index('check_out_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
