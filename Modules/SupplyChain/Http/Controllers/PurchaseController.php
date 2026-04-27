<?php

namespace Modules\SupplyChain\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\SupplyChain\Entities\{PurchaseOrder, PurchaseOrderItem, GoodsReceipt, GoodsReceiptItem, Supplier, Item, StockLevel, Movement};

class PurchaseController extends Controller
{
    public function index()
    {
        $orders = PurchaseOrder::with('supplier')->latest()->paginate(20);
        return view('supplychain::purchasing.index', compact('orders'));
    }

    public function create()
    {
        $suppliers = $this->resolveSuppliers();
        $items     = Item::orderBy('name')->get();
        return view('supplychain::purchasing.create', compact('suppliers', 'items'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'supplier_id'              => 'required|integer',
            'reference'                => 'nullable|string|max:190',
            'currency'                 => 'nullable|string|max:3',
            'notes'                    => 'nullable|string',
            'expected_date'            => 'nullable|date',
            'items'                    => 'required|array|min:1',
            'items.*.item_id'          => 'required|exists:inventory_items,id',
            'items.*.qty_ordered'      => 'required|numeric|min:0.0001',
            'items.*.unit_cost'        => 'required|numeric|min:0',
        ]);

        $po = PurchaseOrder::create([
            'company_id'    => company()->id ?? null,
            'supplier_id'   => $data['supplier_id'],
            'ordered_by'    => auth()->id(),
            'status'        => 'ordered',
            'ordered_at'    => now(),
            'expected_date' => $data['expected_date'] ?? null,
            'reference'     => $data['reference'] ?? null,
            'currency'      => $data['currency'] ?? 'AUD',
            'notes'         => $data['notes'] ?? null,
            'total'         => 0,
        ]);

        $total = 0;
        foreach ($data['items'] as $row) {
            $total += $row['qty_ordered'] * $row['unit_cost'];
            PurchaseOrderItem::create([
                'purchase_order_id' => $po->id,
                'item_id'           => $row['item_id'],
                'qty_ordered'       => $row['qty_ordered'],
                'unit_cost'         => $row['unit_cost'],
            ]);
        }

        $po->update(['total' => $total]);
        return redirect()->route('supplychain.purchasing.index')->with('status', 'Purchase order created');
    }

    public function receiveForm(PurchaseOrder $order)
    {
        $order->load('items.item');
        $warehouses = \Modules\SupplyChain\Entities\Warehouse::orderBy('name')->get();
        return view('supplychain::purchasing.receive', compact('order', 'warehouses'));
    }

    public function receive(Request $r, PurchaseOrder $order)
    {
        $data = $r->validate([
            'warehouse_id'                 => 'required|exists:warehouses,id',
            'items'                        => 'required|array|min:1',
            'items.*.po_item_id'           => 'required|exists:purchase_order_items,id',
            'items.*.qty_received'         => 'required|numeric|min:0.0001',
            'items.*.unit_cost'            => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($order, $data) {
            $grn = GoodsReceipt::create([
                'company_id'         => $order->company_id,
                'purchase_order_id'  => $order->id,
                'warehouse_id'       => $data['warehouse_id'],
                'received_by'        => auth()->id(),
                'received_at'        => now(),
            ]);

            foreach ($data['items'] as $row) {
                $poi    = PurchaseOrderItem::findOrFail($row['po_item_id']);
                $itemId = $poi->item_id;

                GoodsReceiptItem::create([
                    'goods_receipt_id'        => $grn->id,
                    'purchase_order_item_id'  => $poi->id,
                    'item_id'                 => $itemId,
                    'qty_received'            => $row['qty_received'],
                    'unit_cost'               => $row['unit_cost'],
                ]);

                $sl = StockLevel::firstOrCreate(
                    ['item_id' => $itemId, 'warehouse_id' => $data['warehouse_id']],
                    ['on_hand' => 0, 'qty_reserved' => 0, 'qty_available' => 0, 'min_qty' => 0, 'max_qty' => 0, 'company_id' => $order->company_id]
                );
                $sl->on_hand       += $row['qty_received'];
                $sl->qty_available  = max(0, $sl->on_hand - $sl->qty_reserved);
                $sl->save();

                Movement::create([
                    'company_id'   => $order->company_id,
                    'user_id'      => auth()->id(),
                    'item_id'      => $itemId,
                    'warehouse_id' => $data['warehouse_id'],
                    'quantity'     => $row['qty_received'],
                    'type'         => 'in',
                    'note'         => 'GRN #' . $grn->id,
                    'reference'    => 'PO-' . $order->id,
                ]);
            }

            $order->status = 'received';
            $order->save();
        });

        return redirect()->route('supplychain.purchasing.index')->with('status', 'Goods received successfully');
    }

    /** Resolve the supplier list from Suppliers module if active, otherwise local. */
    private function resolveSuppliers()
    {
        if (class_exists(\Modules\SupplyChain\Entities\Supplier::class) && \Illuminate\Support\Facades\Schema::hasTable('suppliers')) {
            try {
                return \Modules\SupplyChain\Entities\Supplier::orderBy('name')->get(['id', 'name']);
            } catch (\Illuminate\Database\QueryException $e) {
                \Illuminate\Support\Facades\Log::warning('Inventory: could not load Suppliers module suppliers', ['error' => $e->getMessage()]);
            }
        }
        return Supplier::orderBy('name')->get(['id', 'name']);
    }
}
