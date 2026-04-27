<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Only create if missing AND prerequisites exist (prevents FK blow-ups)
        if (!Schema::hasTable('transfers')
            && Schema::hasTable('inventory_items')
            && Schema::hasTable('warehouses')) {

            Schema::create('transfers', function (Blueprint $t) {
                $t->id();

                // Ensure these match BIGINT UNSIGNED on the target tables
                $t->unsignedBigInteger('item_id');
                $t->unsignedBigInteger('from_warehouse_id');
                $t->unsignedBigInteger('to_warehouse_id');

                $t->decimal('quantity', 14, 4);
                $t->string('status', 32)->default('draft'); // draft, approved
                $t->text('note')->nullable();
                $t->timestamps();

                // Indices + FKs (added inside the create to avoid a second pass)
                $t->foreign('item_id')->references('id')->on('inventory_items')->cascadeOnDelete();
                $t->foreign('from_warehouse_id')->references('id')->on('warehouses')->cascadeOnDelete();
                $t->foreign('to_warehouse_id')->references('id')->on('warehouses')->cascadeOnDelete();
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('transfers');
    }
};