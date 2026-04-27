<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ext_chatbot_conversations', function (Blueprint $table) {
            $table->timestamp('send_email_at')->nullable()->after('last_activity_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ext_chatbot_conversations', function (Blueprint $table) {
            $table->dropColumn('send_email_at');
        });
    }
};
