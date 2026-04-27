<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->string('source', 50);  // 'internal', 'webhook', 'ai', 'device'
            $table->string('type');        // signal contract identifier
            $table->json('payload');
            $table->enum('status', ['pending', 'approved', 'rejected', 'dispatched', 'failed'])->default('pending');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('processed_at')->nullable();

            $table->index(['organization_id', 'type']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signals');
    }
};
