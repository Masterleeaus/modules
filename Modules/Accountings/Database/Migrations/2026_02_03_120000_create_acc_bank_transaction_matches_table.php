<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('acc_bank_transaction_matches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('bank_transaction_id');
            $table->string('match_type', 30);
            $table->unsignedBigInteger('match_id');
            $table->decimal('matched_amount', 14, 2)->nullable();
            $table->timestamps();

            $table->index(['company_id','user_id']);
            $table->index(['bank_transaction_id']);
            $table->unique(['bank_transaction_id','match_type','match_id'], 'acc_bank_txn_match_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acc_bank_transaction_matches');
    }
};
