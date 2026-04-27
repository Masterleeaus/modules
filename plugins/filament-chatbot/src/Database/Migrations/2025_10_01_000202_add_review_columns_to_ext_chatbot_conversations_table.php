<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ext_chatbot_conversations', function (Blueprint $table) {
            $table->timestamp('review_requested_at')->nullable()->after('send_email_at');
            $table->string('review_request_reason')->nullable()->after('review_requested_at');
            $table->timestamp('review_submitted_at')->nullable()->after('review_request_reason');
            $table->text('review_message')->nullable()->after('review_submitted_at');
            $table->string('review_selected_response')->nullable()->after('review_message');
        });
    }

    public function down(): void
    {
        Schema::table('ext_chatbot_conversations', function (Blueprint $table) {
            $table->dropColumn([
                'review_requested_at',
                'review_request_reason',
                'review_submitted_at',
                'review_message',
                'review_selected_response',
            ]);
        });
    }
};
