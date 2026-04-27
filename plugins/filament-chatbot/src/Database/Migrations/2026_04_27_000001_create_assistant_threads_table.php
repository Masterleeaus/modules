<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_assistant_threads', function (Blueprint $table) {
            $table->id();
            $table->string('user_identifier')->index();
            $table->string('assistant_key');
            $table->json('context')->nullable();
            $table->timestamps();

            $table->index(['user_identifier', 'assistant_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_assistant_threads');
    }
};
