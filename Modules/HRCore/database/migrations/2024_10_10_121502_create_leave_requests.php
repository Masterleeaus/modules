<?php

use App\Enums\LeaveRequestStatus;
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
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->date('from_date');
            $table->date('to_date');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('leave_type_id')->constrained('leave_types')->onDelete('cascade');

            // Half-day support
            $table->boolean('is_half_day')->default(false);
            $table->enum('half_day_type', ['first_half', 'second_half'])->nullable();
            $table->decimal('total_days', 4, 2)->default(1); // Support 0.5 for half days

            // Enhanced fields
            $table->text('document')->nullable();
            $table->string('user_notes', 500)->nullable();
            $table->string('emergency_contact', 100)->nullable();
            $table->string('emergency_phone', 50)->nullable();
            $table->boolean('is_abroad')->default(false);
            $table->string('abroad_location', 200)->nullable();

            // Approval fields
            $table->bigInteger('approved_by_id')->nullable();
            $table->bigInteger('rejected_by_id')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('rejected_at')->nullable();
            $table->string('status', 50)->default(LeaveRequestStatus::PENDING->value);
            $table->string('approval_notes', 500)->nullable();
            $table->string('notes', 500)->nullable();
            $table->string('cancel_reason', 500)->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->bigInteger('cancelled_by_id')->nullable();

            // System fields
            $table->foreignId('created_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('tenant_id', 191)->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['from_date', 'to_date']);
            $table->index('leave_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
