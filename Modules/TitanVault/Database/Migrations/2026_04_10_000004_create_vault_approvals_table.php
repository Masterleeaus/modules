<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('vault_approvals')) {
            Schema::create('vault_approvals', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('document_id');
                $table->foreign('document_id')
                    ->references('id')->on('vault_documents')
                    ->onDelete('cascade')->onUpdate('cascade');

                $table->string('approver_name');
                $table->string('approver_email');
                $table->string('ip_address')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->longText('signature_data')->nullable();

                $table->enum('action', ['approved', 'revision_requested'])->default('approved');
                $table->text('revision_notes')->nullable();

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('vault_approvals');
    }
};
