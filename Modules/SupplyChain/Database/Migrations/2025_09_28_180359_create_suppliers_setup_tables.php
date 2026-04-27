<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('supplier_items')) {
            Schema::create('supplier_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('supplier_id');
                $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
                $table->unsignedBigInteger('item_id')->nullable();
                $table->string('supplier_sku', 191)->nullable();
                $table->decimal('unit_price', 12, 2)->nullable();
                $table->unsignedSmallInteger('lead_days')->nullable();
                $table->boolean('is_preferred')->default(false);
                $table->timestamps();

                $table->unique(['supplier_id', 'item_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_items');
    }
};
