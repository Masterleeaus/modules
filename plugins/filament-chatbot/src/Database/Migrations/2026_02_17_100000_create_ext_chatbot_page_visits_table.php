<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ext_chatbot_page_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chatbot_id')->constrained('ext_chatbots')->cascadeOnDelete();
            $table->string('session_id')->index();
            $table->text('page_url');
            $table->string('page_title')->nullable();
            $table->timestamp('entered_at');
            $table->timestamp('left_at')->nullable();
            $table->timestamps();

            $table->index(['chatbot_id', 'session_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ext_chatbot_page_visits');
    }
};
