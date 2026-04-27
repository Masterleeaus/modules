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
        Schema::table('ext_chatbot_customers', function (Blueprint $table) {
            $table->boolean('enabled_sound')->default(true)->after('chatbot_channel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ext_chatbot_customers', function (Blueprint $table) {
            $table->boolean('enabled_sound');
        });
    }
};
