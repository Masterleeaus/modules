<?php

namespace Modules\SupplyChain\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\SupplyChain\Entities\{Transfer, StockLevel, Movement, Item, Warehouse};

class TransferController extends Controller
{
    public function index()
    {
        $transfers = Transfer::with(['item', 'from', 'to'])->latest()->paginate(20);
        return view('supplychain::transfers.index', compact('transfers'));
    }

    public function create()
    {
        $items      = Item::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();
        return view('supplychain::transfers.create', compact('items', 'warehouses'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'item_id'           => 'required|exists:inventory_items,id',
            'from_warehouse_id' => 'required|exists:warehouses,id|different:to_warehouse_id',
            'to_warehouse_id'   => 'required|exists:warehouses,id',
            'quantity'          => 'required|numeric|min:0.0001',
            'note'              => 'nullable|string',
        ]);

        $data['company_id'] = company()->id ?? null;
        $data['status']     = 'pending';

        Transfer::create($data);
        return redirect()->route('supplychain.transfers.index')->with('status', 'Transfer created');
    }

    /**
     * Move a transfer to "in_transit" (stock leaves source warehouse).
     */
    public function dispatch(Transfer $transfer)
    {
        if ($transfer->status !== 'pending') {
            return back()->with('error', 'Transfer can only be dispatched from pending status');
        }

        $this->processDispatch($transfer);

        return back()->with('status', 'Transfer dispatched (in transit)');
    }

    /**
     * Receive a transfer — stock arrives at destination warehouse.
     */
    public function receive(Transfer $transfer)
    {
        if ($transfer->status !== 'in_transit') {
            return back()->with('error', 'Transfer must be in_transit to receive');
        }

        $this->processReceive($transfer);

        return back()->with('status', 'Transfer received');
    }

    /**
     * Legacy approve endpoint — kept for backward compat; maps to dispatch then receive.
     */
    public function approve(Transfer $transfer)
    {
        if ($transfer->status === 'received') {
            return back()->with('status', 'Already received');
        }

        if ($transfer->status === 'pending') {
            $this->processDispatch($transfer);
            $transfer->refresh();
        }

        if ($transfer->status === 'in_transit') {
            $this->processReceive($transfer);
        }

        return back()->with('status', 'Transfer approved');
    }

    /** Business logic: dispatch a pending transfer to in_transit. */
    private function processDispatch(Transfer $transfer): void
    {
        DB::transaction(function () use ($transfer) {
            $from = StockLevel::firstOrCreate(
                ['item_id' => $transfer->item_id, 'warehouse_id' => $transfer->from_warehouse_id],
                ['on_hand' => 0, 'qty_reserved' => 0, 'qty_available' => 0, 'min_qty' => 0, 'max_qty' => 0, 'company_id' => $transfer->company_id]
            );
            $from->on_hand     = max(0, $from->on_hand - $transfer->quantity);
            $from->qty_available = max(0, $from->on_hand - $from->qty_reserved);
            $from->save();

            Movement::create([
                'company_id'   => $transfer->company_id,
                'user_id'      => auth()->id(),
                'item_id'      => $transfer->item_id,
                'warehouse_id' => $transfer->from_warehouse_id,
                'quantity'     => $transfer->quantity,
                'type'         => 'out',
                'note'         => 'Transfer dispatched',
                'reference'    => 'TRF-' . $transfer->id,
            ]);

            $transfer->status = 'in_transit';
            $transfer->save();
        });
    }

    /** Business logic: receive an in_transit transfer. */
    private function processReceive(Transfer $transfer): void
    {
        DB::transaction(function () use ($transfer) {
            $to = StockLevel::firstOrCreate(
                ['item_id' => $transfer->item_id, 'warehouse_id' => $transfer->to_warehouse_id],
                ['on_hand' => 0, 'qty_reserved' => 0, 'qty_available' => 0, 'min_qty' => 0, 'max_qty' => 0, 'company_id' => $transfer->company_id]
            );
            $to->on_hand     += $transfer->quantity;
            $to->qty_available = max(0, $to->on_hand - $to->qty_reserved);
            $to->save();

            Movement::create([
                'company_id'   => $transfer->company_id,
                'user_id'      => auth()->id(),
                'item_id'      => $transfer->item_id,
                'warehouse_id' => $transfer->to_warehouse_id,
                'quantity'     => $transfer->quantity,
                'type'         => 'in',
                'note'         => 'Transfer received',
                'reference'    => 'TRF-' . $transfer->id,
            ]);

            $transfer->status = 'received';
            $transfer->save();
        });
    }

    public function destroy(Transfer $transfer)
    {
        $transfer->delete();
        return back()->with('status', 'Transfer deleted');
    }
}
