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
        Schema::create('compensatory_offs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('worked_date');
            $table->decimal('hours_worked', 4, 2);
            $table->decimal('comp_off_days', 3, 2)->default(1); // Usually 1 day for weekend/holiday work
            $table->string('reason', 500);
            $table->date('expiry_date');
            $table->boolean('is_used')->default(false);
            $table->date('used_date')->nullable();
            $table->foreignId('leave_request_id')->nullable()->constrained('leave_requests')->onDelete('set null');
            $table->string('status', 50)->default('pending'); // pending, approved, rejected, expired
            $table->bigInteger('approved_by_id')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->string('approval_notes', 500)->nullable();

            // System fields
            $table->foreignId('created_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('tenant_id', 191)->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['expiry_date', 'is_used']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compensatory_offs');
    }
};
