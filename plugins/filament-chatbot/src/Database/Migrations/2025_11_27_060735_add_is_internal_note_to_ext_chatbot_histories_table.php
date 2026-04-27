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
        Schema::table('ext_chatbot_histories', function (Blueprint $table) {
            $table->boolean('is_internal_note')->default(false)->after('message_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ext_chatbot_histories', function (Blueprint $table) {
            $table->dropColumn('is_internal_note');
        });
    }
};
