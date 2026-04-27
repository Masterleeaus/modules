<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('supplier_ratings')) {
            Schema::create('supplier_ratings', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('supplier_id');
                $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
                $table->unsignedBigInteger('rated_by')->nullable();
                $table->foreign('rated_by')->references('id')->on('users')->onDelete('set null');
                $table->tinyInteger('rating')->unsigned()->comment('1-5 star rating');
                $table->string('category')->nullable()->comment('quality, reliability, price');
                $table->text('comment')->nullable();
                $table->timestamp('rated_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_ratings');
    }
};
