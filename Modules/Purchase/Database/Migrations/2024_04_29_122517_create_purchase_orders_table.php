<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       DB::statement('ALTER TABLE `purchase_orders` CHANGE `purchase_order_number` `purchase_order_number` INT NULL DEFAULT NULL;
       ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
