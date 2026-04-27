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
        Schema::table('ext_chatbots', function (Blueprint $table) {
            $table->string('whatsapp_link')->nullable();
            $table->string('telegram_link')->nullable();
            $table->string('watch_product_tour_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ext_chatbots', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_link', 'telegram_link', 'watch_product_tour_link']);
        });
    }
};
