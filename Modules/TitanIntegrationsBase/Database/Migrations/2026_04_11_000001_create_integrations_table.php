<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->string('provider', 50);                          // 'google_calendar', 'xero', etc.
            $table->enum('credential_type', ['oauth', 'api_key', 'webhook', 'none'])->default('api_key');
            $table->text('access_token')->nullable();                // AES-256 encrypted
            $table->text('refresh_token')->nullable();               // AES-256 encrypted
            $table->text('api_key')->nullable();                     // AES-256 encrypted
            $table->text('webhook_url')->nullable();                 // for Slack/Zapier/Teams
            $table->timestamp('token_expires_at')->nullable();
            $table->json('settings')->nullable();                    // list_id, calendar_id, etc.
            $table->enum('status', ['connected', 'disconnected', 'error'])->default('disconnected');
            $table->boolean('is_byo')->default(false);               // true = tenant's own key
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_synced_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'provider']);
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integrations');
    }
};
