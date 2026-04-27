<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('vault_activity_log')) {
            Schema::create('vault_activity_log', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('document_id');
                $table->foreign('document_id')
                    ->references('id')->on('vault_documents')
                    ->onDelete('cascade')->onUpdate('cascade');

                $table->unsignedInteger('user_id')->nullable();
                $table->foreign('user_id')
                    ->references('id')->on('users')
                    ->onDelete('set null')->onUpdate('cascade');

                $table->string('client_token')->nullable();

                $table->enum('action', [
                    'viewed',
                    'downloaded',
                    'approved',
                    'commented',
                    'revision_requested',
                    'shared',
                    'archived',
                ]);

                $table->json('metadata')->nullable();
                $table->string('ip_address')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('vault_activity_log');
    }
};
