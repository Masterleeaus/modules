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
        Schema::create('expense_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('code', 50)->unique();
            $table->text('description')->nullable();
            $table->decimal('default_amount', 15, 2)->nullable();
            $table->decimal('max_amount', 15, 2)->nullable();
            $table->boolean('requires_receipt')->default(false);
            $table->boolean('requires_approval')->default(true);
            $table->enum('status', [Status::ACTIVE->value, Status::INACTIVE->value])->default(Status::ACTIVE->value);

            // Accounting fields
            $table->string('gl_account_code', 50)->nullable();
            $table->string('category', 100)->nullable(); // Travel, Meals, Office Supplies, etc.

            $table->string('tenant_id', 191)->nullable();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['code', 'status']);
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_types');
    }
};
