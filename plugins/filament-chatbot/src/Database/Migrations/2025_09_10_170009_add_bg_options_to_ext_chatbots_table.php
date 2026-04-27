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
            $table->string('header_bg_type')->default('color')->nullable();
            $table->string('header_bg_color')->nullable();
            $table->string('header_bg_gradient')->nullable();
            $table->string('header_bg_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ext_chatbots', function (Blueprint $table) {
            $table->dropColumn(['header_bg_type', 'header_bg_color', 'header_bg_gradient', 'header_bg_image']);
        });
    }
};
