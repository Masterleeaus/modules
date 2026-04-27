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
            $table->boolean('is_shop')->default(false);
            $table->string('shop_source')->nullable();
            $table->json('shop_features')->nullable();
            $table->string('shopify_domain')->nullable();
            $table->string('shopify_access_token')->nullable();
            $table->string('woocommerce_domain')->nullable();
            $table->string('woocommerce_consumer_key')->nullable();
            $table->string('woocommerce_consumer_secret')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ext_chatbots', function (Blueprint $table) {
            $table->dropColumn([
                'is_shop',
                'shop_source',
                'shop_features',
                'shopify_domain',
                'shopify_access_token',
                'woocommerce_domain',
                'woocommerce_consumer_key',
                'woocommerce_consumer_secret',
            ]);
        });
    }
};
