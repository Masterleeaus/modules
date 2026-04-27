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
        Schema::create('designations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('code', 50)->unique();
            $table->string('notes', 500)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->integer('level')->default(0);

            // Approvals
            $table->boolean('is_leave_approver')->default(false);
            $table->boolean('is_expense_approver')->default(false);
            $table->boolean('is_loan_approver')->default(false);
            $table->boolean('is_document_approver')->default(false);

            $table->boolean('is_advance_approver')->default(false);
            $table->boolean('is_resignation_approver')->default(false);
            $table->boolean('is_transfer_approver')->default(false);
            $table->boolean('is_promotion_approver')->default(false);
            $table->boolean('is_increment_approver')->default(false);
            $table->boolean('is_training_approver')->default(false);
            $table->boolean('is_recruitment_approver')->default(false);
            $table->boolean('is_performance_approver')->default(false);
            $table->boolean('is_disciplinary_approver')->default(false);
            $table->boolean('is_complaint_approver')->default(false);
            $table->boolean('is_warning_approver')->default(false);
            $table->boolean('is_termination_approver')->default(false);
            $table->boolean('is_confirmation_approver')->default(false);

            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->foreignId('parent_id')->nullable()->constrained('designations')->onDelete('set null');

            $table->string('tenant_id', 191)->nullable();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('designations');
    }
};
