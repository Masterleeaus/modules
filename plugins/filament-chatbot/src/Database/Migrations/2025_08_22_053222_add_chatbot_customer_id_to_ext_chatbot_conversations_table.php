<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ext_chatbot_conversations', function (Blueprint $table) {
            $table->bigInteger('chatbot_customer_id')->after('id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('ext_chatbot_conversations', function (Blueprint $table) {
            $table->dropColumn('chatbot_customer_id');
        });
    }
};
