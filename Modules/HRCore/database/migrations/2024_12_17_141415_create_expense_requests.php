<?php

use App\Enums\ExpenseRequestStatus;
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
        Schema::create('expense_requests', function (Blueprint $table) {
            $table->id();
            $table->string('expense_number', 50)->unique();
            $table->date('expense_date');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('expense_type_id')->constrained('expense_types')->onDelete('restrict');

            // Amount fields
            $table->decimal('amount', 15, 2);
            $table->decimal('approved_amount', 15, 2)->nullable();
            $table->string('currency', 3)->default('USD');

            // Description and attachments
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->json('attachments')->nullable(); // Store multiple file paths

            // Approval workflow
            $table->enum('status', [
                ExpenseRequestStatus::PENDING->value,
                ExpenseRequestStatus::APPROVED->value,
                ExpenseRequestStatus::REJECTED->value,
                ExpenseRequestStatus::PROCESSED->value,
            ])->default(ExpenseRequestStatus::PENDING->value);

            $table->foreignId('approved_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_remarks')->nullable();

            $table->foreignId('rejected_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();

            // Payment processing
            $table->foreignId('processed_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('processed_at')->nullable();
            $table->string('payment_reference')->nullable();
            $table->text('processing_notes')->nullable();

            // Project/Department association
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->string('project_code', 50)->nullable();
            $table->string('cost_center', 50)->nullable();

            // Metadata
            $table->foreignId('created_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('tenant_id', 191)->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['expense_date', 'status']);
            $table->index('expense_number');
            $table->index('department_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_requests');
    }
};
