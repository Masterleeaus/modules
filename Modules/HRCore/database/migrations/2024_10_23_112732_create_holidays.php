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
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->date('date');
            $table->string('code', 50)->unique();
            $table->year('year')->index(); // Year for easy filtering
            $table->string('day', 20)->nullable(); // Day of week (Monday, Tuesday, etc.)

            // Holiday type and categorization
            $table->enum('type', ['public', 'religious', 'regional', 'optional', 'company', 'special'])->default('public');
            $table->enum('category', ['national', 'state', 'cultural', 'festival', 'company_event', 'other'])->nullable();
            $table->boolean('is_optional')->default(false); // Optional holiday that employees can choose
            $table->boolean('is_restricted')->default(false); // Restricted holiday (floater)
            $table->boolean('is_recurring')->default(false); // Recurring every year

            // Applicability settings
            $table->enum('applicable_for', ['all', 'department', 'location', 'employee_type', 'custom'])->default('all');
            $table->json('departments')->nullable(); // Array of department IDs
            $table->json('locations')->nullable(); // Array of location names/IDs
            $table->json('employee_types')->nullable(); // Array of employee types (permanent, contract, etc.)
            $table->json('branches')->nullable(); // Array of branch IDs
            $table->json('specific_employees')->nullable(); // Array of specific employee IDs

            // Additional details
            $table->text('description')->nullable(); // Detailed description
            $table->string('notes', 500)->nullable(); // Short notes
            $table->string('image')->nullable(); // Holiday image/banner
            $table->string('color', 7)->nullable(); // Color code for calendar display
            $table->integer('sort_order')->default(0); // Display order

            // Working day compensation
            $table->boolean('is_compensatory')->default(false); // If true, employees get comp-off
            $table->date('compensatory_date')->nullable(); // Alternative working day if holiday falls on weekend

            // Half day settings
            $table->boolean('is_half_day')->default(false); // Half day holiday
            $table->enum('half_day_type', ['morning', 'afternoon'])->nullable(); // Which half
            $table->time('half_day_start_time')->nullable(); // Start time for half day
            $table->time('half_day_end_time')->nullable(); // End time for half day

            // Status and visibility
            $table->boolean('is_active')->default(true);
            $table->boolean('is_visible_to_employees')->default(true); // Show in employee calendar

            // Notification settings
            $table->boolean('send_notification')->default(true); // Send notification to employees
            $table->integer('notification_days_before')->default(7); // Days before to send notification

            // Metadata
            $table->foreignId('created_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->string('tenant_id', 191)->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Indexes for performance
            $table->index(['date', 'year']);
            $table->index(['type', 'is_active']);
            $table->index('applicable_for');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};
