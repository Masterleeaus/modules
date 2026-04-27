<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('movements', function (Blueprint $t) {
            $t->id();
            $t->foreignId('item_id')->constrained('inventory_items')->cascadeOnDelete();
            $t->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $t->decimal('quantity', 14, 4)->default(0);
            $t->enum('type', ['in','out','adjust']);
            $t->text('note')->nullable();
            $t->softDeletes();
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('movements');
    }
};
