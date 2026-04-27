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
            $table->boolean('is_gdpr')->default(false)->after('is_links');
            $table->string('privacy_policy_link')->nullable()->after('is_gdpr');
            $table->string('terms_of_service_link')->nullable()->after('privacy_policy_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ext_chatbots', function (Blueprint $table) {
            $table->dropColumn(['is_gdpr', 'privacy_policy_link', 'terms_of_service_link']);
        });
    }
};
