@php($pageTitle = $pageTitle ?? __('accountings::app.menu.accounting'))
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col">
            <h4 class="mb-0">{{ __('accountings::app.menu.cashflow') }}</h4>
            <div class="text-muted small">Net movement in cash/bank accounts by month (proxy cashflow).</div>
        </div>
    </div>

    <form method="GET" class="card mb-3">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-4">
                    <label class="form-label">From</label>
                    <input type="date" name="from" value="{{ $from }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">To</label>
                    <input type="date" name="to" value="{{ $to }}" class="form-control">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-primary">Apply</button>
                </div>
            </div>
            <div class="mt-2 text-muted small">Cash/Banks mapped COA count: {{ $cash_coa_count }}</div>
        </div>
    </form>

    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card"><div class="card-body">
                <div class="text-muted small">Total Inflow</div>
                <div class="h4 mb-0">{{ $inflow }}</div>
            </div></div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card"><div class="card-body">
                <div class="text-muted small">Total Outflow</div>
                <div class="h4 mb-0">{{ $outflow }}</div>
            </div></div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card"><div class="card-body">
                <div class="text-muted small">Net</div>
                <div class="h4 mb-0">{{ $net }}</div>
            </div></div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h6>Monthly cashflow (net)</h6>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th class="text-end">Net</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($series as $month => $value)
                            <tr>
                                <td>{{ $month }}</td>
                                <td class="text-end">{{ number_format($value, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-muted">No data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="text-muted small mt-2">
                Note: This is a practical cashflow proxy using movements in cash/bank accounts. For true cashflow statements, add dedicated transaction categories + bank feeds.
            </div>
        </div>
    </div>
</div>
@endsection
