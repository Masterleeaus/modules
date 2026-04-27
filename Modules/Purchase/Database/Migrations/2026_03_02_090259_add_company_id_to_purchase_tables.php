<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('purchase_bill_histories') && !Schema::hasColumn('purchase_bill_histories', 'company_id')) {
            Schema::table('purchase_bill_histories', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('purchase_bills') && !Schema::hasColumn('purchase_bills', 'company_id')) {
            Schema::table('purchase_bills', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('purchase_inventory_adjustment') && !Schema::hasColumn('purchase_inventory_adjustment', 'company_id')) {
            Schema::table('purchase_inventory_adjustment', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('purchase_inventory_files') && !Schema::hasColumn('purchase_inventory_files', 'company_id')) {
            Schema::table('purchase_inventory_files', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('purchase_inventory_histories') && !Schema::hasColumn('purchase_inventory_histories', 'company_id')) {
            Schema::table('purchase_inventory_histories', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('purchase_item_images') && !Schema::hasColumn('purchase_item_images', 'company_id')) {
            Schema::table('purchase_item_images', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('purchase_item_taxes') && !Schema::hasColumn('purchase_item_taxes', 'company_id')) {
            Schema::table('purchase_item_taxes', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('purchase_items') && !Schema::hasColumn('purchase_items', 'company_id')) {
            Schema::table('purchase_items', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('purchase_management_settings') && !Schema::hasColumn('purchase_management_settings', 'company_id')) {
            Schema::table('purchase_management_settings', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('purchase_notification_settings') && !Schema::hasColumn('purchase_notification_settings', 'company_id')) {
            Schema::table('purchase_notification_settings', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('purchase_order_files') && !Schema::hasColumn('purchase_order_files', 'company_id')) {
            Schema::table('purchase_order_files', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('purchase_order_histories') && !Schema::hasColumn('purchase_order_histories', 'company_id')) {
            Schema::table('purchase_order_histories', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('purchase_order_settings') && !Schema::hasColumn('purchase_order_settings', 'company_id')) {
            Schema::table('purchase_order_settings', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('purchase_orders') && !Schema::hasColumn('purchase_orders', 'company_id')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('purchase_payment_bills') && !Schema::hasColumn('purchase_payment_bills', 'company_id')) {
            Schema::table('purchase_payment_bills', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('purchase_payment_histories') && !Schema::hasColumn('purchase_payment_histories', 'company_id')) {
            Schema::table('purchase_payment_histories', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('purchase_product_histories') && !Schema::hasColumn('purchase_product_histories', 'company_id')) {
            Schema::table('purchase_product_histories', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('purchase_settings') && !Schema::hasColumn('purchase_settings', 'company_id')) {
            Schema::table('purchase_settings', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('purchase_stock_adjustment_reasons') && !Schema::hasColumn('purchase_stock_adjustment_reasons', 'company_id')) {
            Schema::table('purchase_stock_adjustment_reasons', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('purchase_stock_adjustments') && !Schema::hasColumn('purchase_stock_adjustments', 'company_id')) {
            Schema::table('purchase_stock_adjustments', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('purchase_vendor_categories') && !Schema::hasColumn('purchase_vendor_categories', 'company_id')) {
            Schema::table('purchase_vendor_categories', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('purchase_vendor_contacts') && !Schema::hasColumn('purchase_vendor_contacts', 'company_id')) {
            Schema::table('purchase_vendor_contacts', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('purchase_vendor_credit_histories') && !Schema::hasColumn('purchase_vendor_credit_histories', 'company_id')) {
            Schema::table('purchase_vendor_credit_histories', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('purchase_vendor_credit_item_images') && !Schema::hasColumn('purchase_vendor_credit_item_images', 'company_id')) {
            Schema::table('purchase_vendor_credit_item_images', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('purchase_vendor_credits') && !Schema::hasColumn('purchase_vendor_credits', 'company_id')) {
            Schema::table('purchase_vendor_credits', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('purchase_vendor_histories') && !Schema::hasColumn('purchase_vendor_histories', 'company_id')) {
            Schema::table('purchase_vendor_histories', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('purchase_vendor_items') && !Schema::hasColumn('purchase_vendor_items', 'company_id')) {
            Schema::table('purchase_vendor_items', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('purchase_vendor_notes') && !Schema::hasColumn('purchase_vendor_notes', 'company_id')) {
            Schema::table('purchase_vendor_notes', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('purchase_vendor_payments') && !Schema::hasColumn('purchase_vendor_payments', 'company_id')) {
            Schema::table('purchase_vendor_payments', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('purchase_vendor_user_notes') && !Schema::hasColumn('purchase_vendor_user_notes', 'company_id')) {
            Schema::table('purchase_vendor_user_notes', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('purchase_vendors') && !Schema::hasColumn('purchase_vendors', 'company_id')) {
            Schema::table('purchase_vendors', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        // intentionally non-destructive
    }
};
