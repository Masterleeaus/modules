<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Extends the core `orders` table with procurement/purchase-order columns.
 *
 * Existing rows are unaffected:
 *   – order_type defaults to 'sale'
 *   – all new columns are nullable or carry safe defaults
 *
 * Rollback removes ONLY the columns added here; it does NOT drop the orders table.
 */
return new class extends Migration {

    /** Columns added by this migration (used in both up and down). */
    private array $columns = [
        'order_type',
        'supplier_id',
        'po_number',
        'purchase_status',
        'expected_delivery_date',
        'actual_delivery_date',
        'delivery_address',
        'delivery_notes',
        'gst_applicable',
        'gst_amount',
        'payment_terms',
        'invoice_reference',
        'invoice_matched',
        'expense_created',
        'created_expense_id',
    ];

    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'order_type')) {
                // 'sale' keeps every existing row behaving as before
                $table->string('order_type')->default('sale')->after('id');
            }

            if (!Schema::hasColumn('orders', 'supplier_id')) {
                $table->unsignedBigInteger('supplier_id')->nullable()->after('order_type');

                // Only add the FK if the suppliers table already exists.
                // The Suppliers module may not be installed in every environment.
                if (Schema::hasTable('suppliers')) {
                    $table->foreign('supplier_id')
                          ->references('id')->on('suppliers')
                          ->onDelete('set null')->onUpdate('cascade');
                }
            }

            if (!Schema::hasColumn('orders', 'po_number')) {
                // Unique per company is enforced at the application layer, not by a
                // global unique index, because po_number is company-scoped.
                $table->string('po_number')->nullable()->after('supplier_id');
            }

            if (!Schema::hasColumn('orders', 'purchase_status')) {
                $table->string('purchase_status')->default('draft')->after('po_number');
            }

            if (!Schema::hasColumn('orders', 'expected_delivery_date')) {
                $table->date('expected_delivery_date')->nullable()->after('purchase_status');
            }

            if (!Schema::hasColumn('orders', 'actual_delivery_date')) {
                $table->date('actual_delivery_date')->nullable()->after('expected_delivery_date');
            }

            if (!Schema::hasColumn('orders', 'delivery_address')) {
                $table->string('delivery_address')->nullable()->after('actual_delivery_date');
            }

            if (!Schema::hasColumn('orders', 'delivery_notes')) {
                $table->text('delivery_notes')->nullable()->after('delivery_address');
            }

            if (!Schema::hasColumn('orders', 'gst_applicable')) {
                $table->boolean('gst_applicable')->default(true)->after('delivery_notes');
            }

            if (!Schema::hasColumn('orders', 'gst_amount')) {
                $table->decimal('gst_amount', 10, 2)->default(0)->after('gst_applicable');
            }

            if (!Schema::hasColumn('orders', 'payment_terms')) {
                $table->string('payment_terms')->nullable()->after('gst_amount');
            }

            if (!Schema::hasColumn('orders', 'invoice_reference')) {
                $table->string('invoice_reference')->nullable()->after('payment_terms');
            }

            if (!Schema::hasColumn('orders', 'invoice_matched')) {
                $table->boolean('invoice_matched')->default(false)->after('invoice_reference');
            }

            if (!Schema::hasColumn('orders', 'expense_created')) {
                $table->boolean('expense_created')->default(false)->after('invoice_matched');
            }

            if (!Schema::hasColumn('orders', 'created_expense_id')) {
                $table->unsignedBigInteger('created_expense_id')->nullable()->after('expense_created');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop FK before dropping column
            if (Schema::hasColumn('orders', 'supplier_id')) {
                try {
                    $table->dropForeign(['supplier_id']);
                } catch (\Throwable $e) {
                    // FK may not exist in all environments; ignore
                }
            }

            $existing = array_filter($this->columns, fn ($col) => Schema::hasColumn('orders', $col));

            if (!empty($existing)) {
                $table->dropColumn(array_values($existing));
            }
        });
    }
};
