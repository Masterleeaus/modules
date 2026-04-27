@php($pageTitle = $pageTitle ?? __('accountings::app.menu.accounting'))
@extends('layouts.app')

@section('content')
@php
use Illuminate\Support\Facades\Route;
@endphp
<div class="container-fluid">
    <div class="mb-3 d-flex justify-content-between align-items-end">
        <div>
            <h4 class="mb-0">Money Owed To You</h4>
            <small class="text-muted">Outstanding invoices (best effort, subtracts payments where possible).</small>
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

    @if($note)
        <div class="alert alert-warning">{{ $note }}</div>
    @endif

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
                                <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ $invoiceUrl }}">Open</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-muted">No records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">Accounting term: Accounts Receivable (A/R).</div>
                <a href="{{ route('cashflow.collections') }}" class="btn btn-sm btn-outline-secondary">Collections Helper</a>
            </div>
        </div>
    </div>
</div>
@endsection
