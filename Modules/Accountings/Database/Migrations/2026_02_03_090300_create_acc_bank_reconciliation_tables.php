<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acc_bank_reconciliations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();

            $table->unsignedBigInteger('bank_account_id')->index();
            $table->date('from_date');
            $table->date('to_date');

            $table->decimal('opening_balance', 12, 2)->default(0);
            $table->decimal('closing_balance', 12, 2)->default(0);

            $table->decimal('matched_total', 12, 2)->default(0);
            $table->decimal('difference', 12, 2)->default(0);

            $table->enum('status', ['draft','closed'])->default('draft');
            $table->timestamp('closed_at')->nullable();

            $table->timestamps();

            $table->foreign('bank_account_id')->references('id')->on('acc_bank_accounts')->onDelete('cascade');
        });

        Schema::create('acc_bank_reconciliation_lines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();

            $table->unsignedBigInteger('reconciliation_id')->index();
            $table->unsignedBigInteger('bank_transaction_id')->index();

            $table->timestamp('added_at')->nullable();

            $table->unique(['reconciliation_id','bank_transaction_id'], 'acc_recon_txn_unique');

            $table->foreign('reconciliation_id')->references('id')->on('acc_bank_reconciliations')->onDelete('cascade');
            $table->foreign('bank_transaction_id')->references('id')->on('acc_bank_transactions')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acc_bank_reconciliation_lines');
        Schema::dropIfExists('acc_bank_reconciliations');
    }
};
