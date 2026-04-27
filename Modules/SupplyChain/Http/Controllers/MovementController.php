<?php

namespace Modules\SupplyChain\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\SupplyChain\Entities\Movement;
use Modules\SupplyChain\Entities\Item;
use Modules\SupplyChain\Entities\Warehouse;
use Modules\SupplyChain\Entities\StockLevel;

class MovementController extends Controller
{
    public function index()
    {
        $movements = Movement::with(['item', 'warehouse', 'user'])->latest()->paginate(20);
        return view('supplychain::movements.index', compact('movements'));
    }

    public function store(Request $request)
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
            // adjust: set absolute quantity
            $sl->on_hand = $data['quantity'];
        }
        $sl->qty_available = max(0, $sl->on_hand - $sl->qty_reserved);
        $sl->save();

        return back()->with('status', 'Movement recorded');
    }

    public function destroy(Movement $movement)
    {
        $movement->delete();
        return back()->with('status', 'Movement deleted');
    }
}
