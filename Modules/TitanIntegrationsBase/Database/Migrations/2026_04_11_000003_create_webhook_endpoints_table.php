<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_endpoints', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->string('url', 500);
            $table->json('events');                                  // ['booking.created', 'invoice.paid']
            $table->string('secret', 64);                           // HMAC signing secret
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_triggered_at')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });

        Schema::create('integration_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->string('provider', 50)->nullable();
            $table->enum('direction', ['inbound', 'outbound'])->default('outbound');
            $table->string('event_type', 100)->nullable();
            $table->json('payload')->nullable();
            $table->enum('status', ['success', 'failed', 'pending'])->default('pending');
            $table->smallInteger('http_status')->nullable();
            $table->text('error_message')->nullable();
            $table->tinyInteger('attempts')->default(1);
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['company_id', 'provider']);
            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integration_logs');
        Schema::dropIfExists('webhook_endpoints');
    }
};
