<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acc_accounting_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();

            $table->date('period_lock_date')->nullable(); // no edits on/before this date
            $table->enum('gst_basis', ['accrual','cash'])->default('accrual');
            $table->string('currency', 8)->default('AUD');

            $table->timestamps();

            $table->unique(['company_id','user_id'], 'acc_settings_company_user_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acc_accounting_settings');
    }
};
