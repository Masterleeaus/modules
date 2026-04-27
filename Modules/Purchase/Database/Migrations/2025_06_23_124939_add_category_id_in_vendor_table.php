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

        // purchase_vendors in this table add category_id
        if (Schema::hasTable('purchase_vendors')) {
        Schema::table('purchase_vendors', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_vendors', 'category_id')) {
                $table->integer('category_id')->unsigned()->nullable();
            }
            $table->foreign('category_id')->references('id')->on('purchase_vendor_categories')->onDelete('set null');
        });
    }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        // drop category_id from purchase_vendors table
        if (Schema::hasTable('purchase_vendors')) {
        Schema::table('purchase_vendors', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            if (Schema::hasColumn('purchase_vendors', 'category_id')) {
                $table->dropColumn('category_id');
            }
        });
    }
    }
};
