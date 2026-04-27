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
        Schema::create('ext_chatbot_carts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('chatbot_customer_id');
            $table->bigInteger('chatbot_id');
            $table->string('session_id')->nullable();
            $table->string('product_source')->nullable();
            $table->json('product_data')->nullable();
            $table->json('products')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ext_chatbot_carts');
    }
};
