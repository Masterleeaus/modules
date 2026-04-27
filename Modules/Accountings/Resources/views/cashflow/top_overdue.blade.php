@php($pageTitle = $pageTitle ?? __('accountings::app.menu.accounting'))
@extends('layouts.app')

@section('content')
@php
use Illuminate\Support\Facades\Route;
@endphp
<div class="container-fluid">
    <div class="mb-3 d-flex justify-content-between align-items-end">
        <div>
            <h4 class="mb-0">Top 20 Overdue</h4>
            <small class="text-muted">Biggest overdue balances — chase these first.</small>
        </div>
        <a href="{{ route('cashflow.collections') }}" class="btn btn-sm btn-outline-secondary">Collections Helper</a>
    </div>

    @if(!empty($note))
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
                            <th class="text-end">Owed</th>
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
                            <tr><td colspan="5" class="text-muted">No overdue invoices.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
