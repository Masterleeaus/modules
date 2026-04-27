<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ext_chatbots', function (Blueprint $table) {
            $table->boolean('is_review_enabled')->default(false)->after('human_agent_conditions');
            $table->text('review_prompt')->nullable()->after('is_review_enabled');
            $table->json('review_responses')->nullable()->after('review_prompt');
        });
    }

    public function down(): void
    {
        Schema::table('ext_chatbots', function (Blueprint $table) {
            $table->dropColumn(['is_review_enabled', 'review_prompt', 'review_responses']);
        });
    }
};
