<?php

namespace Modules\Purchase\Services;

use App\Models\Expense;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Handles the stock-replenishment lifecycle within the Purchase module:
 *
 *  1. detectReorderCandidates()   – find Products below reorder_point
 *  2. createDraftPurchaseOrder()  – create Order with order_type='purchase' as draft
 *  3. receiveGoods()              – mark PO received + update product stock (atomic)
 *  4. matchInvoiceAndCreateExpense() – match supplier invoice + auto-create Expense
 *  5. enforceCreditLimit()        – guard against PO creation that would breach limit
 */
class PurchaseReorderService
{
    /**
     * Return Products whose current stock is at or below their reorder_point.
     *
     * @param  int $companyId
     * @return \Illuminate\Database\Eloquent\Collection<int, Product>
     */
    public function detectReorderCandidates(int $companyId)
    {
        return Product::where('company_id', $companyId)
            ->whereNotNull('reorder_point')
            ->whereNotNull('stock_quantity')
            ->whereColumn('stock_quantity', '<=', 'reorder_point')
            ->get();
    }

    /**
     * Create a draft purchase order (order_type='purchase') on the core orders
     * table for the given supplier and product lines.
     *
     * @param  array{
     *   company_id: int,
     *   supplier_id: int,
     *   po_number: string,
     *   currency_id: int|null,
     *   items: array<int, array{product_id: int, qty: int, unit_price: float}>,
     *   payment_terms: string|null,
     *   expected_delivery_date: string|null,
     * } $data
     * @return Order
     *
     * @throws \RuntimeException if the supplier credit limit would be exceeded
     */
    public function createDraftPurchaseOrder(array $data): Order
    {
        $this->enforceCreditLimit(
            $data['supplier_id'],
            $data['company_id'],
            $data['total'] ?? 0
        );

        return DB::transaction(function () use ($data) {
            $subTotal = 0;

            foreach ($data['items'] ?? [] as $item) {
                $subTotal += ($item['qty'] ?? 1) * ($item['unit_price'] ?? 0);
            }

            $order = Order::create([
                'company_id'            => $data['company_id'],
                'order_type'            => 'purchase',
                'purchase_status'       => 'draft',
                'supplier_id'           => $data['supplier_id'],
                'po_number'             => $data['po_number'] ?? null,
                'currency_id'           => $data['currency_id'] ?? null,
                'order_date'            => now()->toDateString(),
                'sub_total'             => $subTotal,
                'total'                 => $subTotal,
                'due_amount'            => $subTotal,
                'payment_terms'         => $data['payment_terms'] ?? null,
                'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
                'status'                => 'pending',
                'gst_applicable'        => $data['gst_applicable'] ?? true,
                'gst_amount'            => $data['gst_amount'] ?? 0,
            ]);

            return $order;
        });
    }

    /**
     * Mark a purchase order as received and update product stock quantities.
     *
     * Both the PO status change and the stock update are wrapped in a single
     * DB transaction to guarantee atomicity.
     *
     * @param  Order $order   The purchase order being received
     * @param  array<int, array{product_id: int, qty_received: int}> $receivedItems
     * @return Order
     */
    public function receiveGoods(Order $order, array $receivedItems): Order
    {
        return DB::transaction(function () use ($order, $receivedItems) {
            foreach ($receivedItems as $item) {
                $product = Product::find($item['product_id'] ?? null);

                if ($product && isset($item['qty_received'])) {
                    $product->increment('stock_quantity', (int) $item['qty_received']);
                }
            }

            $order->update([
                'purchase_status'    => 'received',
                'actual_delivery_date' => now()->toDateString(),
            ]);

            return $order->fresh();
        });
    }

    /**
     * Match a supplier invoice to a purchase order and auto-create an Expense.
     *
     * Idempotent: if an expense has already been created for this PO it will
     * not create a duplicate.
     *
     * @param  Order  $order
     * @param  string $invoiceReference   Supplier's invoice number
     * @return Expense|null  The newly created Expense, or null if already matched.
     */
    public function matchInvoiceAndCreateExpense(Order $order, string $invoiceReference): ?Expense
    {
        if ($order->expense_created || $order->invoice_matched) {
            Log::info("Purchase: invoice already matched for order #{$order->id}");
            return null;
        }

        return DB::transaction(function () use ($order, $invoiceReference) {
            $expense = Expense::create([
                'company_id'   => $order->company_id,
                'item_name'    => "PO #{$order->po_number} – supplier invoice {$invoiceReference}",
                'price'        => $order->total,
                'total'        => $order->total,
                'date'         => now()->toDateString(),
                'purchase_from' => $order->supplier_id
                    ? optional($order->supplier)->name
                    : null,
                'status'       => 'approved',
                'added_by'     => $order->added_by,
            ]);

            $order->update([
                'invoice_reference' => $invoiceReference,
                'invoice_matched'   => true,
                'expense_created'   => true,
                'created_expense_id' => $expense->id,
            ]);

            return $expense;
        });
    }

    /**
     * Enforce supplier credit limit.
     *
     * Throws a RuntimeException if adding $orderTotal to the supplier's current
     * outstanding PO value would exceed their credit_limit.
     *
     * We intentionally use the query builder (not the Supplier Eloquent model)
     * here because:
     *  – The Suppliers module may be disabled or not present.
     *  – The `suppliers` table always exists if a supplier_id was set, so raw
     *    DB access is safe and avoids a class-existence guard.
     *
     * @param  int   $supplierId
     * @param  int   $companyId
     * @param  float $orderTotal
     *
     * @throws \RuntimeException
     */
    public function enforceCreditLimit(int $supplierId, int $companyId, float $orderTotal): void
    {
        if (!$supplierId) {
            return;
        }

        $supplier = DB::table('suppliers')
            ->where('id', $supplierId)
            ->first();

        if (!$supplier || empty($supplier->credit_limit)) {
            return; // no limit configured
        }

        // Sum outstanding (non-cancelled, non-received) PO totals for this supplier
        $outstanding = Order::where('company_id', $companyId)
            ->where('supplier_id', $supplierId)
            ->where('order_type', 'purchase')
            ->whereNotIn('purchase_status', ['received', 'cancelled'])
            ->sum('total');

        if (($outstanding + $orderTotal) > (float) $supplier->credit_limit) {
            throw new \RuntimeException(
                "Purchase order would exceed supplier credit limit of {$supplier->credit_limit}."
            );
        }
    }
}
