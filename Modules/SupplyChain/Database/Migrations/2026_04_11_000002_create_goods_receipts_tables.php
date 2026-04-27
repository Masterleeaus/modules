<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create goods_receipts and goods_receipt_items tables (missing from initial migration set).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('goods_receipts')) {
            Schema::create('goods_receipts', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('company_id')->nullable();
                $t->unsignedBigInteger('purchase_order_id');
                $t->unsignedBigInteger('warehouse_id');
                $t->unsignedBigInteger('received_by')->nullable(); // user_id
                $t->timestamp('received_at')->nullable();
                $t->string('reference', 191)->nullable();
                $t->text('notes')->nullable();
                $t->timestamps();

                $t->foreign('purchase_order_id')->references('id')->on('purchase_orders')->cascadeOnDelete();
                $t->foreign('warehouse_id')->references('id')->on('warehouses')->cascadeOnDelete();
            });
        }

        if (!Schema::hasTable('goods_receipt_items')) {
            Schema::create('goods_receipt_items', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('goods_receipt_id');
                $t->unsignedBigInteger('purchase_order_item_id')->nullable();
                $t->unsignedBigInteger('item_id');
                $t->decimal('qty_received', 14, 4)->default(0);
                $t->decimal('unit_cost', 14, 4)->default(0);
                $t->string('condition', 50)->default('good'); // good | damaged | rejected
                $t->text('notes')->nullable();
                $t->timestamps();

                $t->foreign('goods_receipt_id')->references('id')->on('goods_receipts')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_receipt_items');
        Schema::dropIfExists('goods_receipts');
    }
};
