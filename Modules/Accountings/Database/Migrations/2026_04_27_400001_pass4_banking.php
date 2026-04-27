<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('acc_bank_transactions')) {
            Schema::create('acc_bank_transactions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('company_id')->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->unsignedBigInteger('bank_account_id')->nullable()->index();
                $table->date('transaction_date')->index();
                $table->string('description')->nullable();
                $table->decimal('amount', 15, 2);
                $table->decimal('balance', 15, 2)->nullable();
                $table->string('reference')->nullable();
                $table->string('source', 32)->default('import');
                $table->boolean('is_matched')->default(false);
                $table->string('matched_type', 32)->nullable();
                $table->unsignedBigInteger('matched_id')->nullable()->index();
                $table->string('import_hash')->nullable()->unique();
                $table->timestamps();
            });
        } else {
            // Ensure pass4 columns exist on the pre-existing table
            Schema::table('acc_bank_transactions', function (Blueprint $table) {
                if (! Schema::hasColumn('acc_bank_transactions', 'is_matched')) {
                    $table->boolean('is_matched')->default(false)->after('source');
                }
                if (! Schema::hasColumn('acc_bank_transactions', 'import_hash')) {
                    $table->string('import_hash')->nullable()->unique()->after('matched_id');
                }
            });
        }

        if (! Schema::hasTable('acc_period_locks')) {
            Schema::create('acc_period_locks', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('company_id')->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->date('lock_date')->index();
                $table->unsignedBigInteger('locked_by');
                $table->text('reason')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('acc_period_locks');
        Schema::dropIfExists('acc_bank_transactions');
    }
};
