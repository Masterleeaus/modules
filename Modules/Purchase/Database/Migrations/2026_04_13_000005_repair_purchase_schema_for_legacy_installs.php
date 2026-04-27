<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('purchase_management_settings')) {
            Schema::table('purchase_management_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('purchase_management_settings', 'supported_until')) {
                    $table->timestamp('supported_until')->nullable();
                }
                if (!Schema::hasColumn('purchase_management_settings', 'notify_update')) {
                    $table->boolean('notify_update')->default(1);
                }
                if (!Schema::hasColumn('purchase_management_settings', 'license_type')) {
                    $table->string('license_type', 20)->nullable();
                }
                if (!Schema::hasColumn('purchase_management_settings', 'purchased_on')) {
                    $table->timestamp('purchased_on')->nullable();
                }
            });
        }

        if (Schema::hasTable('purchase_orders')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('purchase_orders', 'supplier_id')) {
                    $table->unsignedBigInteger('supplier_id')->nullable();
                }
                if (!Schema::hasColumn('purchase_orders', 'ordered_at')) {
                    $table->dateTime('ordered_at')->nullable();
                }
                if (!Schema::hasColumn('purchase_orders', 'ordered_by')) {
                    $table->unsignedBigInteger('ordered_by')->nullable();
                }
                if (!Schema::hasColumn('purchase_orders', 'expected_date')) {
                    $table->date('expected_date')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        // Intentionally non-destructive repair migration.
    }
};
