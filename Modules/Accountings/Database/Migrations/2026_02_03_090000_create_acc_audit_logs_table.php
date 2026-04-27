<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acc_audit_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();

            $table->string('action', 64); // create|update|delete|import|reconcile|close
            $table->string('entity_type', 64); // bill|expense|journal|bank_txn|reconciliation|setting
            $table->unsignedBigInteger('entity_id')->nullable()->index();
            $table->json('meta')->nullable();

            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acc_audit_logs');
    }
};
