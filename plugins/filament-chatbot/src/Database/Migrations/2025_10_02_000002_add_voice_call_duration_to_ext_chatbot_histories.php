<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ext_chatbot_histories')) {
            return;
        }

        if (Schema::hasColumn('ext_chatbot_histories', 'voice_call_duration')) {
            return;
        }

        Schema::table('ext_chatbot_histories', function (Blueprint $table) {
            $table->unsignedInteger('voice_call_duration')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('ext_chatbot_histories', function (Blueprint $table) {
            $table->dropColumn('voice_call_duration');
        });
    }
};
