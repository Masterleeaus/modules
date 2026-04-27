<?php

namespace Modules\SupplyChain\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\SupplyChain\Entities\{Item, Warehouse, StockLevel, Movement, Transfer};

class InventoryApiController extends Controller
{
    public function ping()
    {
        return response()->json(['ok' => true, 'module' => 'supplychain']);
    }

    /** GET /api/supply-chain/warehouses — list warehouses for the current company */
    public function warehouses()
    {
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        return response()->json($warehouses);
    }

    /** GET /api/supply-chain/items — list inventory items */
    public function items()
    {
        $items = Item::orderBy('name')->get();
        return response()->json($items);
    }

    /** GET /api/supply-chain/stock — stock levels with item and warehouse */
    public function stock(Request $request)
    {
        $query = StockLevel::with(['item', 'warehouse']);

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }
        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        return response()->json($query->paginate(50));
    }

    /** GET /api/supply-chain/movements — recent stock movements */
    public function movements(Request $request)
    {
        $query = Movement::with(['item', 'warehouse', 'user'])->latest();

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        return response()->json($query->paginate(50));
    }

    /** POST /api/supply-chain/movements — record a movement (for van/mobile staff) */
    public function storeMovement(Request $request)
    {
        $data = $request->validate([
            'item_id'      => 'required|exists:inventory_items,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity'     => 'required|numeric|min:0.0001',
            'type'         => 'required|in:in,out,adjust',
            'note'         => 'nullable|string',
            'reference'    => 'nullable|string|max:191',
        ]);

        $data['company_id'] = company()->id ?? null;
        $data['user_id']    = auth()->id();

        $movement = Movement::create($data);

        // Update stock level
        $sl = StockLevel::firstOrCreate(
            ['item_id' => $data['item_id'], 'warehouse_id' => $data['warehouse_id']],
            ['on_hand' => 0, 'qty_reserved' => 0, 'qty_available' => 0, 'min_qty' => 0, 'max_qty' => 0, 'company_id' => $data['company_id']]
        );

        if ($data['type'] === 'in') {
            $sl->on_hand += $data['quantity'];
        } elseif ($data['type'] === 'out') {
            $sl->on_hand = max(0, $sl->on_hand - $data['quantity']);
        } else {
            $sl->on_hand = $data['quantity'];
        }
        $sl->qty_available = max(0, $sl->on_hand - $sl->qty_reserved);
        $sl->save();

        return response()->json(['ok' => true, 'movement' => $movement, 'stock_level' => $sl], 201);
    }

    /** GET /api/supply-chain/transfers — list transfers */
    public function transfers(Request $request)
    {
        $query = Transfer::with(['item', 'from', 'to'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->paginate(50));
    }
}
