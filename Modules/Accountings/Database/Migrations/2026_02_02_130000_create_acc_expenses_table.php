<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pass 2: Quick expenses for cleaners (fuel, chemicals, parking, small tools, etc.)
     * These can later be unified into bills; for now they provide a fast capture workflow.
     */
    public function up(): void
    {
        if (Schema::hasTable('acc_expenses')) {
            return;
        }

        Schema::create('acc_expenses', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('company_id')->nullable()->index();
            $t->unsignedBigInteger('user_id')->nullable()->index();

            $t->date('expense_date')->nullable()->index();
            $t->unsignedBigInteger('vendor_id')->nullable()->index();
            $t->unsignedBigInteger('coa_id')->nullable()->index();
            $t->unsignedBigInteger('tax_code_id')->nullable()->index();
            $t->unsignedBigInteger('service_line_id')->nullable()->index();

            $t->string('payment_method', 30)->default('cash')->index(); // cash|bank|card
            $t->decimal('amount', 16, 2)->default(0);
            $t->decimal('tax_amount', 16, 2)->default(0);
            $t->string('description', 191)->nullable();
            $t->string('job_ref', 191)->nullable()->index();
            $t->string('status', 30)->default('posted')->index(); // posted|void
            $t->text('notes')->nullable();

            $t->timestamps();
            $t->index(['company_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acc_expenses');
    }
};
