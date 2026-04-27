<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acc_bank_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();

            $table->string('name', 191);
            $table->string('institution', 191)->nullable();
            $table->string('currency', 8)->default('AUD');

            $table->string('account_number_last4', 8)->nullable();
            $table->string('bsb', 16)->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });

        Schema::create('acc_bank_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();

            $table->unsignedBigInteger('bank_account_id')->index();
            $table->date('txn_date')->index();
            $table->string('description', 255)->nullable();
            $table->string('reference', 191)->nullable();
            $table->decimal('amount', 12, 2); // +in, -out
            $table->decimal('balance', 12, 2)->nullable();

            $table->string('import_batch', 64)->nullable()->index();
            $table->string('source', 32)->default('csv');

            // matching hook (optional)
            $table->string('matched_type', 32)->nullable(); // invoice|bill|expense|journal|manual
            $table->unsignedBigInteger('matched_id')->nullable()->index();
            $table->timestamp('matched_at')->nullable();

            // dedupe key
            $table->string('hash', 64)->nullable()->index();

            $table->timestamps();

            $table->foreign('bank_account_id')->references('id')->on('acc_bank_accounts')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acc_bank_transactions');
        Schema::dropIfExists('acc_bank_accounts');
    }
};
