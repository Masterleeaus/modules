@php($pageTitle = $pageTitle ?? __('accountings::app.menu.accounting'))
@extends('layouts.app')

@section('content')
@php
use Illuminate\Support\Facades\Route;
@endphp
<div class="container-fluid">
    <div class="mb-3">
        <h4 class="mb-0">A/R Aging (Overdue)</h4>
        <small class="text-muted">Overdue money grouped into buckets.</small>
    </div>

    @if(!empty($note))
        <div class="alert alert-warning">{{ $note }}</div>
    @endif

    <div class="row g-3 mb-3">
        @foreach($buckets as $b)
            <div class="col-md-3">
                <div class="card"><div class="card-body">
                    <div class="text-muted">{{ $b['label'] }}</div>
                    <h4 class="mb-1">{{ number_format($b['total'],2) }}</h4>
                    <small class="text-muted">{{ (int)$b['count'] }} invoices</small>
                </div></div>
            </div>
        @endforeach
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Top clients by overdue total</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th class="text-end">Overdue total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($clients as $c)
                                    <tr>
                                        <td>{{ $c['client_name'] }}</td>
                                        <td class="text-end">{{ number_format($c['total'],2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="text-muted">No overdue clients found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <a href="{{ route('cashflow.top_overdue') }}" class="btn btn-sm btn-outline-primary mt-2">Top 20 overdue</a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Overdue invoices (sample)</div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 420px; overflow:auto;">
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
                                @forelse(array_slice($rows,0,20) as $r)
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
                    <div class="text-muted small">Sample only (first 20) to keep this fast.</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
