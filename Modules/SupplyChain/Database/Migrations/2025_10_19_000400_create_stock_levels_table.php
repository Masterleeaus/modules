<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('stock_levels', function (Blueprint $t) {
            $t->id();
            $t->foreignId('item_id')->constrained('inventory_items')->cascadeOnDelete();
            $t->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $t->decimal('on_hand',14,4)->default(0);
            $t->decimal('min_qty',14,4)->default(0);
            $t->decimal('max_qty',14,4)->default(0);
            $t->timestamps();
            $t->unique(['item_id','warehouse_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('stock_levels'); }
};
