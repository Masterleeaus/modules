<?php

namespace Modules\SupplyChain\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\SupplyChain\Entities\Supplier;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::latest()->paginate(20);
        return view('supplychain::suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('supplychain::suppliers.create');
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name'  => 'required|max:190',
            'email' => 'nullable|email',
            'phone' => 'nullable|max:50',
            'abn'   => 'nullable|max:50',
            'notes' => 'nullable|string',
        ]);
        $data['company_id'] = company()->id ?? null;
        Supplier::create($data);
        return redirect()->route('supplychain.suppliers.index')->with('status', 'Supplier created');
    }

    public function edit(Supplier $supplier)
    {
        return view('supplychain::suppliers.edit', compact('supplier'));
    }

    public function show(Supplier $supplier)
    {
        return view('supplychain::suppliers.edit', compact('supplier'));
    }

    public function update(Request $r, Supplier $supplier)
    {
        $data = $r->validate([
            'name'  => 'required|max:190',
            'email' => 'nullable|email',
            'phone' => 'nullable|max:50',
            'abn'   => 'nullable|max:50',
            'notes' => 'nullable|string',
        ]);
        $supplier->update($data);
        return back()->with('status', 'Supplier updated');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return back()->with('status', 'Supplier deleted');
    }
}
