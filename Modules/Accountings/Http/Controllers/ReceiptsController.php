<?php

namespace Modules\Accountings\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AccountBaseController;
use Modules\Accountings\Entities\Receipt;
use Modules\Accountings\Traits\ResolvesCompany;

class ReceiptsController extends AccountBaseController
{
    use ResolvesCompany;

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Receipts';
        $this->pageIcon = 'ti-clip';

        $this->middleware(function ($request, $next) {
            abort_403(!in_array('accountings', $this->user->modules));
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $receipts = Receipt::query()->orderByDesc('id')->paginate(25);
        return view('accountings::receipts.index', compact('receipts'));
    }

    public function create()
    {
        return view('accountings::receipts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'attachable_type' => 'nullable|string|max:191',
            'attachable_id' => 'nullable|integer',
            'file_path' => 'nullable|string|max:500',
            'file_name' => 'nullable|string|max:191',
            'mime' => 'nullable|string|max:191',
            'file_size' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        $receipt = new Receipt();
        $receipt->fill($validated);
        $receipt->save();

        return redirect()->route('receipts.index')->with('message', 'Receipt saved');
    }

    public function destroy($id)
    {
        $receipt = Receipt::where('company_id', $this->currentCompanyId())->findOrFail($id);
        $receipt->delete();
        return redirect()->route('receipts.index')->with('message', 'Receipt deleted');
    }
}