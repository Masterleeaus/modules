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
        //create  purchase_vendor_categories table
        if (!Schema::hasTable('purchase_vendor_categories')) {
            Schema::create('purchase_vendor_categories', function (Blueprint $table) {
                    $table->increments('id');
                    $table->unsignedInteger('company_id')->nullable();
                    $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                    $table->string('category_name');
                    $table->timestamps();
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // drop purchase_vendor_categories table
        Schema::dropIfExists('purchase_vendor_categories');

    }
};
