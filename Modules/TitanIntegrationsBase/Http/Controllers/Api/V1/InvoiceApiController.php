<?php

namespace Modules\TitanIntegrations\Http\Controllers\Api\V1;

use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class InvoiceApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $companyId = $request->attributes->get('api_company_id');

        $query = Invoice::where('company_id', $companyId);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('from')) {
            $query->whereDate('issue_date', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('issue_date', '<=', $request->input('to'));
        }

        $invoices = $query->with(['items'])
            ->latest()
            ->paginate(50);

        return response()->json($invoices);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $companyId = $request->attributes->get('api_company_id');

        $invoice = Invoice::where('company_id', $companyId)
            ->with(['items'])
            ->findOrFail($id);

        return response()->json($invoice);
    }
}
