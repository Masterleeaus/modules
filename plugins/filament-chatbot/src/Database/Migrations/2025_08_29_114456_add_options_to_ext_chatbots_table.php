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
            $table->boolean('is_email_collect')->default(true)->after('is_demo');
            $table->boolean('is_contact')->default(true)->after('is_email_collect');
            $table->boolean('is_attachment')->default(true)->after('is_contact');
            $table->boolean('is_emoji')->default(true)->after('is_attachment');
            $table->boolean('is_articles')->default(true)->after('is_emoji');
            $table->boolean('is_links')->default(true)->after('is_articles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ext_chatbots', function (Blueprint $table) {
            //
        });
    }
};
