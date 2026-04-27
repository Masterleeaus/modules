<?php

namespace Modules\SupplyChain\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\SupplyChain\Entities\Item;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::query()->latest()->paginate(20);
        return view('supplychain::items.index', compact('items'));
    }

    public function create()
    {
        // Provide FieldItems catalogue if available
        $fieldItems = collect();
        if (class_exists(\Modules\FieldItems\Entities\Item::class) && \Illuminate\Support\Facades\Schema::hasTable('items')) {
            try {
                $fieldItems = \Modules\FieldItems\Entities\Item::orderBy('name')->get(['id', 'name', 'sku']);
            } catch (\Illuminate\Database\QueryException $e) {
                \Illuminate\Support\Facades\Log::warning('Inventory: could not load FieldItems catalogue', ['error' => $e->getMessage()]);
            }
        }
        return view('supplychain::items.create', compact('fieldItems'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:190',
            'sku'           => 'nullable|string|max:190|unique:inventory_items,sku',
            'unit'          => 'nullable|string|max:50',
            'field_item_id' => 'nullable|integer',
        ]);
        $data['company_id'] = company()->id ?? null;
        Item::create($data);
        return redirect()->route('supplychain.items.index')->with('status', 'Item created');
    }

    public function edit(Item $item)
    {
        $fieldItems = collect();
        if (class_exists(\Modules\FieldItems\Entities\Item::class) && \Illuminate\Support\Facades\Schema::hasTable('items')) {
            try {
                $fieldItems = \Modules\FieldItems\Entities\Item::orderBy('name')->get(['id', 'name', 'sku']);
            } catch (\Illuminate\Database\QueryException $e) {
                \Illuminate\Support\Facades\Log::warning('Inventory: could not load FieldItems catalogue', ['error' => $e->getMessage()]);
            }
        }
        return view('supplychain::items.edit', compact('item', 'fieldItems'));
    }

    public function update(Request $request, Item $item)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:190',
            'sku'           => 'nullable|string|max:190|unique:inventory_items,sku,' . $item->id,
            'unit'          => 'nullable|string|max:50',
            'field_item_id' => 'nullable|integer',
        ]);
        $item->update($data);
        return redirect()->route('supplychain.items.index')->with('status', 'Item updated');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return back()->with('status', 'Item deleted');
    }
}
