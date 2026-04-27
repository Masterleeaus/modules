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
            $table->string('promo_banner_image')->nullable()->after('bubble_design');
            $table->string('promo_banner_title')->nullable()->after('promo_banner_image');
            $table->text('promo_banner_description')->nullable()->after('promo_banner_title');
            $table->string('promo_banner_btn_label')->nullable()->after('promo_banner_description');
            $table->string('promo_banner_btn_link')->nullable()->after('promo_banner_btn_label');
        });
    }

    public function down(): void
    {
        Schema::table('ext_chatbots', function (Blueprint $table) {
            $table->dropColumn([
                'promo_banner_image',
                'promo_banner_title',
                'promo_banner_description',
                'promo_banner_btn_label',
                'promo_banner_btn_link',
            ]);
        });
    }
};
