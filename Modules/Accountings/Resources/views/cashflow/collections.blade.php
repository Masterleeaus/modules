@php($pageTitle = $pageTitle ?? __('accountings::app.menu.accounting'))
@extends('layouts.app')

@section('content')
@php
use Illuminate\Support\Facades\Route;
@endphp
<div class="container-fluid">
    <div class="mb-3 d-flex justify-content-between align-items-end">
        <div>
            <h4 class="mb-0">Collections Helper</h4>
            <small class="text-muted">No outbound sending here — this page helps you see who to chase and what to do next.</small>
        </div>
        <form method="GET" class="d-flex gap-2">
            <select name="bucket" class="form-select form-select-sm" style="width: 220px;">
                <option value="overdue" @if($bucket==='overdue') selected @endif>Overdue</option>
                <option value="due_14" @if($bucket==='due_14') selected @endif>Due in next 14 days</option>
                <option value="due_30" @if($bucket==='due_30') selected @endif>Due in next 30 days</option>
            </select>
            <button class="btn btn-sm btn-primary">Apply</button>
        </form>
    </div>

    <div class="alert alert-info">
        <strong>Suggested workflow:</strong>
        <ol class="mb-0">
            <li>Open invoice → confirm scope and payment terms.</li>
            <li>Call client → confirm payment date.</li>
            <li>If needed, resend invoice from your invoicing screen.</li>
            <li>Update notes in CRM/job record.</li>
        </ol>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Invoice</th>
                            <th>Date Due</th>
                            <th class="text-end">Amount Owed</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $r)
                            @php
                                $invoiceUrl = null;
                                if (Route::has('invoices.show')) {
                                    try { $invoiceUrl = route('invoices.show', $r['id']); } catch (\Throwable $e) { $invoiceUrl = null; }
                                }
                                if (!$invoiceUrl) $invoiceUrl = url('account/invoices/' . $r['id']);
                            @endphp
                            <tr>
                                <td>{{ $r['client_name'] ?? '—' }}</td>
                                <td><a href="{{ $invoiceUrl }}">{{ $r['invoice_number'] }}</a></td>
                                <td>{{ $r['date'] }}</td>
                                <td class="text-end">{{ number_format($r['balance'],2) }}</td>
                                <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ $invoiceUrl }}">Open invoice</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-muted">No records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="text-muted small">Fallback URL used if route names differ.</div>
        </div>
    </div>
</div>
@endsection
