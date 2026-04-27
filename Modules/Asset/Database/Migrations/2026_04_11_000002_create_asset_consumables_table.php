<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the asset_consumables table for consumable inventory tracking
 * (sprays, cloths, vacuum bags, etc.) with low-stock alerts.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('asset_consumables')) {
            return;
        }

        Schema::create('asset_consumables', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('company_id')->index();
            $table->string('name', 191);
            $table->string('sku', 100)->nullable()->index();
            $table->string('unit', 50)->nullable()->comment('e.g. litres, units, rolls');
            $table->decimal('quantity_on_hand', 15, 4)->default(0);
            $table->decimal('low_stock_threshold', 15, 4)->default(0)
                ->comment('Alert when quantity_on_hand falls below this value');
            $table->decimal('unit_cost', 15, 4)->nullable();
            $table->unsignedInteger('supplier_id')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedInteger('added_by')->nullable();
            $table->unsignedInteger('last_updated_by')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_consumables');
    }
};
