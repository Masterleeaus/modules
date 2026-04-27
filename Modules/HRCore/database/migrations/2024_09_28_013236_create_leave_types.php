<?php

use App\Enums\Status;
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
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('code', 50)->unique();
            $table->string('notes', 500)->nullable();
            $table->boolean('is_proof_required')->default(false);
            $table->string('status', 50)->default(Status::ACTIVE->value);

            // Accrual settings
            $table->boolean('is_accrual_enabled')->default(false);
            $table->enum('accrual_frequency', ['monthly', 'quarterly', 'yearly'])->default('yearly');
            $table->decimal('accrual_rate', 5, 2)->default(0); // Days per frequency
            $table->decimal('max_accrual_limit', 5, 2)->nullable(); // Maximum days that can be accrued
            $table->boolean('allow_carry_forward')->default(false);
            $table->decimal('max_carry_forward', 5, 2)->nullable(); // Maximum days that can be carried forward
            $table->integer('carry_forward_expiry_months')->nullable(); // Months after which carried forward leaves expire
            $table->boolean('allow_encashment')->default(false);
            $table->decimal('max_encashment_days', 5, 2)->nullable();
            $table->boolean('is_comp_off_type')->default(false); // If this is used for compensatory offs

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
        Schema::dropIfExists('leave_types');
    }
};
