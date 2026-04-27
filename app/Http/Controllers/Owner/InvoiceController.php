<?php

namespace App\Http\Controllers\Owner;

use App\Actions\Invoices\GenerateFromJobAction;
use App\Actions\Invoices\RecordPaymentAction;
use App\Actions\Invoices\VoidInvoiceAction;
use App\Events\InvoiceSent;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Job;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Response;
use Inertia\ResponseFactory;

class InvoiceController extends Controller
{
    // ── Index ──────────────────────────────────────────────────────────────────

    public function index(Request $request): Response|ResponseFactory
    {
        $orgId = $request->user()->organization_id;

        $invoices = Invoice::where('organization_id', $orgId)
            ->with(['customer', 'job'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->search, function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('invoice_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', fn ($q) =>
                            $q->where('first_name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%")
                        );
                });
            })
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        return inertia('Owner/Invoices/Index', [
            'invoices' => $invoices,
            'filters'  => $request->only(['search', 'status']),
            'statuses' => Invoice::statuses(),
        ]);
    }

    // ── Show ───────────────────────────────────────────────────────────────────

    public function show(Request $request, Invoice $invoice): Response|ResponseFactory
    {
        abort_unless($invoice->organization_id === $request->user()->organization_id, 403);

        $invoice->load(['customer', 'job', 'lineItems', 'payments.recordedBy']);

        return inertia('Owner/Invoices/Show', [
            'invoice'  => $invoice,
            'statuses' => Invoice::statuses(),
        ]);
    }

    // ── Generate from Job ──────────────────────────────────────────────────────

    public function generateFromJob(Request $request, Job $job): RedirectResponse
    {
        abort_unless($job->organization_id === $request->user()->organization_id, 403);
        abort_unless($job->isCompleted(), 422);
        abort_unless($job->invoice === null, 422);

        $invoice = app(GenerateFromJobAction::class)->execute($job);

        return redirect()->route('owner.invoices.show', $invoice)
            ->with('success', 'Invoice generated.');
    }

    // ── Send ───────────────────────────────────────────────────────────────────

    public function send(Request $request, Invoice $invoice): RedirectResponse
    {
        abort_unless($invoice->organization_id === $request->user()->organization_id, 403);
        abort_unless(in_array($invoice->status, [Invoice::STATUS_DRAFT, Invoice::STATUS_OVERDUE]), 422);

        $invoice->update([
            'status'  => Invoice::STATUS_SENT,
            'sent_at' => now(),
        ]);

        InvoiceSent::dispatch($invoice);

        return redirect()->route('owner.invoices.show', $invoice)
            ->with('success', 'Invoice sent.');
    }

    // ── Void ──────────────────────────────────────────────────────────────────

    public function void(Request $request, Invoice $invoice): RedirectResponse
    {
        abort_unless($invoice->organization_id === $request->user()->organization_id, 403);
        abort_unless($invoice->status !== Invoice::STATUS_VOID, 422);
        abort_unless($invoice->status !== Invoice::STATUS_PAID, 422);

        app(VoidInvoiceAction::class)->execute($invoice);

        return redirect()->route('owner.invoices.show', $invoice)
            ->with('success', 'Invoice voided.');
    }

    // ── Record Payment ────────────────────────────────────────────────────────

    public function recordPayment(Request $request, Invoice $invoice): RedirectResponse
    {
        abort_unless($invoice->organization_id === $request->user()->organization_id, 403);
        abort_unless(! in_array($invoice->status, [Invoice::STATUS_VOID, Invoice::STATUS_PAID]), 422);

        $data = $request->validate([
            'amount'    => ['required', 'numeric', 'min:0.01', 'max:' . (float) $invoice->balance_due],
            'method'    => ['required', Rule::in([
                Payment::METHOD_CASH,
                Payment::METHOD_CHECK,
                Payment::METHOD_CARD,
                Payment::METHOD_BANK_TRANSFER,
            ])],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes'     => ['nullable', 'string', 'max:1000'],
            'paid_at'   => ['required', 'date'],
        ]);

        app(RecordPaymentAction::class)->execute($invoice, $data, $request->user()->id);

        return redirect()->route('owner.invoices.show', $invoice)
            ->with('success', 'Payment recorded.');
    }

    // ── Destroy ───────────────────────────────────────────────────────────────

    public function destroy(Request $request, Invoice $invoice): RedirectResponse
    {
        abort_unless($invoice->organization_id === $request->user()->organization_id, 403);
        abort_unless($invoice->status === Invoice::STATUS_DRAFT, 422);

        $invoice->delete();

        return redirect()->route('owner.invoices.index')
            ->with('success', 'Invoice deleted.');
    }
}
