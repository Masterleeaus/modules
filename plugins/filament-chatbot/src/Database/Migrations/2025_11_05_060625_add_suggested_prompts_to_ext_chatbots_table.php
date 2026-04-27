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
            if (! Schema::hasColumn('ext_chatbots', 'suggested_prompts')) {
                $table->json('suggested_prompts')->nullable();
            }

            if (! Schema::hasColumn('ext_chatbots', 'suggested_prompts_enabled')) {
                $table->boolean('suggested_prompts_enabled')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ext_chatbots', function (Blueprint $table) {
            if (Schema::hasColumn('ext_chatbots', 'suggested_prompts')) {
                $table->dropColumn('suggested_prompts');
            }

            if (Schema::hasColumn('ext_chatbots', 'suggested_prompts_enabled')) {
                $table->dropColumn('suggested_prompts_enabled');
            }
        });
    }
};
