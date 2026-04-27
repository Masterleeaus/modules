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
            $table->json('booking_assistant_conditions')->nullable();
            $table->boolean('is_booking_assistant')->default(false);
            $table->longText('booking_assistant_iframe')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ext_chatbots', function (Blueprint $table) {
            $table->dropColumn('booking_assistant_conditions');
            $table->dropColumn('is_booking_assistant');
            $table->dropColumn('booking_assistant_iframe');
        });
    }
};
