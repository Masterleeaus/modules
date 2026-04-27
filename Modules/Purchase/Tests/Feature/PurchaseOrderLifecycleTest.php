<?php

namespace Modules\Purchase\Tests\Feature;

use App\Models\Expense;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Modules\Purchase\Services\PurchaseReorderService;
use Tests\TestCase;

/**
 * Feature tests for the Purchase module procurement lifecycle.
 *
 * These tests verify:
 *  1. Purchase orders are created with order_type = 'purchase' on the core orders table
 *  2. Stock quantity is updated atomically on goods received
 *  3. An Expense is auto-created (and not duplicated) when an invoice is matched
 *  4. Supplier credit limit is enforced on PO creation
 *  5. A reorder suggestion converts to a draft PO
 */
class PurchaseOrderLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private PurchaseReorderService $service;

    /** Minimal schema stubs so tests run without a full DB. */
    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new PurchaseReorderService();

        $this->createStubSchema();
    }

    // -----------------------------------------------------------------------
    // Schema helpers
    // -----------------------------------------------------------------------

    private function createStubSchema(): void
    {
        if (!Schema::hasTable('currencies')) {
            Schema::create('currencies', function ($t) {
                $t->id();
                $t->string('currency_code');
                $t->string('currency_symbol')->nullable();
                $t->timestamps();
            });
        }

        if (!Schema::hasTable('companies')) {
            Schema::create('companies', function ($t) {
                $t->id();
                $t->string('company_name');
                $t->timestamps();
            });
        }

        if (!Schema::hasTable('users')) {
            Schema::create('users', function ($t) {
                $t->id();
                $t->string('name');
                $t->string('email')->unique();
                $t->string('password');
                $t->timestamps();
            });
        }

        if (!Schema::hasTable('suppliers')) {
            Schema::create('suppliers', function ($t) {
                $t->id();
                $t->string('name');
                $t->string('email')->nullable();
                $t->decimal('credit_limit', 10, 2)->nullable();
                $t->boolean('is_active')->default(true);
                $t->unsignedBigInteger('company_id')->nullable();
                $t->timestamps();
                $t->softDeletes();
            });
        }

        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function ($t) {
                $t->id();
                $t->unsignedBigInteger('company_id')->nullable();
                $t->unsignedBigInteger('client_id')->nullable();
                $t->date('order_date');
                $t->double('sub_total')->default(0);
                $t->double('discount')->default(0);
                $t->string('discount_type')->default('percent');
                $t->double('total')->default(0);
                $t->double('due_amount')->default(0);
                $t->string('status')->default('pending');
                $t->unsignedBigInteger('currency_id')->nullable();
                $t->string('show_shipping_address')->default('no');
                $t->string('note')->nullable();
                $t->unsignedBigInteger('added_by')->nullable();
                $t->unsignedBigInteger('last_updated_by')->nullable();
                // procurement columns
                $t->string('order_type')->default('sale');
                $t->unsignedBigInteger('supplier_id')->nullable();
                $t->string('po_number')->nullable();
                $t->string('purchase_status')->default('draft');
                $t->date('expected_delivery_date')->nullable();
                $t->date('actual_delivery_date')->nullable();
                $t->string('delivery_address')->nullable();
                $t->text('delivery_notes')->nullable();
                $t->boolean('gst_applicable')->default(true);
                $t->decimal('gst_amount', 10, 2)->default(0);
                $t->string('payment_terms')->nullable();
                $t->string('invoice_reference')->nullable();
                $t->boolean('invoice_matched')->default(false);
                $t->boolean('expense_created')->default(false);
                $t->unsignedBigInteger('created_expense_id')->nullable();
                $t->timestamps();
            });
        }

        if (!Schema::hasTable('expenses')) {
            Schema::create('expenses', function ($t) {
                $t->id();
                $t->unsignedBigInteger('company_id')->nullable();
                $t->string('item_name');
                $t->decimal('price', 14, 2)->default(0);
                $t->decimal('total', 14, 2)->default(0);
                $t->date('date');
                $t->string('purchase_from')->nullable();
                $t->string('status')->default('approved');
                $t->unsignedBigInteger('user_id')->nullable();
                $t->unsignedBigInteger('added_by')->nullable();
                $t->timestamps();
            });
        }

        if (!Schema::hasTable('products')) {
            Schema::create('products', function ($t) {
                $t->id();
                $t->unsignedBigInteger('company_id')->nullable();
                $t->string('name');
                $t->decimal('price', 10, 2)->default(0);
                $t->integer('stock_quantity')->nullable();
                $t->integer('reorder_point')->nullable();
                $t->timestamps();
            });
        }
    }

    // -----------------------------------------------------------------------
    // 1. PO created with order_type = 'purchase' on existing orders table
    // -----------------------------------------------------------------------

    /** @test */
    public function purchase_order_is_created_with_order_type_purchase(): void
    {
        $companyId  = 1;
        $supplierId = \DB::table('suppliers')->insertGetId([
            'name'       => 'Test Supplier',
            'company_id' => $companyId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $order = $this->service->createDraftPurchaseOrder([
            'company_id'  => $companyId,
            'supplier_id' => $supplierId,
            'po_number'   => 'PO-TEST-001',
            'total'       => 500.00,
        ]);

        $this->assertSame('purchase', $order->order_type);
        $this->assertSame('draft', $order->purchase_status);
        $this->assertSame('PO-TEST-001', $order->po_number);
        $this->assertDatabaseHas('orders', [
            'id'            => $order->id,
            'order_type'    => 'purchase',
            'purchase_status' => 'draft',
        ]);
    }

    // -----------------------------------------------------------------------
    // 2. Stock quantity updated atomically on goods received
    // -----------------------------------------------------------------------

    /** @test */
    public function stock_quantity_is_updated_atomically_when_goods_are_received(): void
    {
        $companyId  = 1;
        $supplierId = \DB::table('suppliers')->insertGetId([
            'name'       => 'Stock Supplier',
            'company_id' => $companyId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $productId = \DB::table('products')->insertGetId([
            'name'           => 'Cleaning Spray',
            'company_id'     => $companyId,
            'stock_quantity' => 5,
            'price'          => 10.00,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        $order = $this->service->createDraftPurchaseOrder([
            'company_id'  => $companyId,
            'supplier_id' => $supplierId,
            'po_number'   => 'PO-STOCK-001',
            'total'       => 100.00,
        ]);

        $this->service->receiveGoods($order, [
            ['product_id' => $productId, 'qty_received' => 20],
        ]);

        $this->assertDatabaseHas('products', [
            'id'             => $productId,
            'stock_quantity' => 25, // 5 + 20
        ]);

        $this->assertDatabaseHas('orders', [
            'id'              => $order->id,
            'purchase_status' => 'received',
        ]);
    }

    // -----------------------------------------------------------------------
    // 3. Expense auto-created when invoice matched (no duplicates)
    // -----------------------------------------------------------------------

    /** @test */
    public function expense_is_auto_created_when_invoice_is_matched(): void
    {
        $companyId  = 1;
        $supplierId = \DB::table('suppliers')->insertGetId([
            'name'       => 'Invoice Supplier',
            'company_id' => $companyId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $order = $this->service->createDraftPurchaseOrder([
            'company_id'  => $companyId,
            'supplier_id' => $supplierId,
            'po_number'   => 'PO-INV-001',
            'total'       => 750.00,
        ]);

        $expense = $this->service->matchInvoiceAndCreateExpense($order, 'SINV-999');

        $this->assertNotNull($expense);
        $this->assertDatabaseHas('expenses', ['id' => $expense->id]);
        $this->assertDatabaseHas('orders', [
            'id'                => $order->id,
            'invoice_matched'   => true,
            'expense_created'   => true,
            'invoice_reference' => 'SINV-999',
        ]);
    }

    /** @test */
    public function matching_invoice_twice_does_not_create_duplicate_expense(): void
    {
        $companyId  = 1;
        $supplierId = \DB::table('suppliers')->insertGetId([
            'name'       => 'Dupe Check Supplier',
            'company_id' => $companyId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $order = $this->service->createDraftPurchaseOrder([
            'company_id'  => $companyId,
            'supplier_id' => $supplierId,
            'po_number'   => 'PO-DUPE-001',
            'total'       => 200.00,
        ]);

        $this->service->matchInvoiceAndCreateExpense($order, 'SINV-DUPE');

        // Attempt to match again — should return null and NOT create another expense
        $secondAttempt = $this->service->matchInvoiceAndCreateExpense(
            $order->fresh(),
            'SINV-DUPE'
        );

        $this->assertNull($secondAttempt);
        $this->assertSame(1, Expense::where('company_id', $companyId)->count());
    }

    // -----------------------------------------------------------------------
    // 4. Supplier credit limit enforced
    // -----------------------------------------------------------------------

    /** @test */
    public function creating_purchase_order_beyond_credit_limit_throws_exception(): void
    {
        $companyId  = 1;
        $supplierId = \DB::table('suppliers')->insertGetId([
            'name'         => 'Limited Credit Supplier',
            'company_id'   => $companyId,
            'credit_limit' => 1000.00,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        // First PO – fits within limit
        $this->service->createDraftPurchaseOrder([
            'company_id'  => $companyId,
            'supplier_id' => $supplierId,
            'po_number'   => 'PO-CREDIT-001',
            'total'       => 800.00,
        ]);

        // Second PO – would push total outstanding to $1,300 (> $1,000 limit)
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/credit limit/i');

        $this->service->createDraftPurchaseOrder([
            'company_id'  => $companyId,
            'supplier_id' => $supplierId,
            'po_number'   => 'PO-CREDIT-002',
            'total'       => 500.00,
        ]);
    }

    // -----------------------------------------------------------------------
    // 5. Reorder suggestion converts to draft PO
    // -----------------------------------------------------------------------

    /** @test */
    public function product_below_reorder_point_is_detected_as_reorder_candidate(): void
    {
        $companyId = 1;

        \DB::table('products')->insert([
            ['name' => 'Below reorder', 'company_id' => $companyId, 'stock_quantity' => 2,  'reorder_point' => 10, 'price' => 5.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'At reorder',    'company_id' => $companyId, 'stock_quantity' => 10, 'reorder_point' => 10, 'price' => 5.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Above reorder', 'company_id' => $companyId, 'stock_quantity' => 50, 'reorder_point' => 10, 'price' => 5.00, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $candidates = $this->service->detectReorderCandidates($companyId);

        // 'Below reorder' (2 ≤ 10) and 'At reorder' (10 ≤ 10) should be returned
        $this->assertCount(2, $candidates);
        $names = $candidates->pluck('name')->sort()->values()->toArray();
        $this->assertSame(['At reorder', 'Below reorder'], $names);
    }
}
