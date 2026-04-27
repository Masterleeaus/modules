<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pass 3: bill payments to support cash-basis GST and vendor statements.
     */
    public function up(): void
    {
        if (!Schema::hasTable('acc_bill_payments')) {
            Schema::create('acc_bill_payments', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('company_id')->nullable()->index();
                $t->unsignedBigInteger('user_id')->nullable()->index();
                $t->unsignedBigInteger('bill_id')->index();
                $t->date('paid_at')->nullable()->index();
                $t->decimal('amount', 16, 2)->default(0);
                $t->string('method', 50)->nullable(); // bank|card|cash|transfer
                $t->string('reference', 191)->nullable();
                $t->text('notes')->nullable();
                $t->timestamps();
                $t->index(['company_id', 'user_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('acc_bill_payments');
    }
};
