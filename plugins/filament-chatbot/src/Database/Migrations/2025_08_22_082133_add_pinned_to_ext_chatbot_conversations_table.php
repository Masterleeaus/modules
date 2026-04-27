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
            $table->integer('pinned')->default(0)->nullable()->after('ticket_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ext_chatbot_conversations', function (Blueprint $table) {
            $table->dropColumn('pinned');
        });
    }
};
