<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ext_chatbots')) {
            return;
        }

        if (Schema::hasColumn('ext_chatbots', 'voice_call_enabled')) {
            return;
        }

        Schema::table('ext_chatbots', function (Blueprint $table) {
            $table->boolean('voice_call_enabled')->default(false);
            $table->text('voice_call_first_message')->nullable();
            $table->string('voice_call_provider')->nullable();
            $table->string('voice_call_voice_id')->nullable();
            $table->string('voice_call_agent_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('ext_chatbots', function (Blueprint $table) {
            $table->dropColumn([
                'voice_call_enabled',
                'voice_call_first_message',
                'voice_call_provider',
                'voice_call_voice_id',
                'voice_call_agent_id',
            ]);
        });
    }
};
