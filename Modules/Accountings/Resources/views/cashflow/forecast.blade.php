@php($pageTitle = $pageTitle ?? __('accountings::app.menu.accounting'))
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-3 d-flex justify-content-between align-items-end">
        <div>
            <h4 class="mb-0">Forecast (Monthly)</h4>
            <small class="text-muted">Budget + recurring expenses forecast + outstanding invoices/expenses (best effort).</small>
        </div>
        <form method="GET" class="d-flex gap-2">
            <select name="months" class="form-select form-select-sm" style="width: 140px;">
                @foreach([3,6,9,12] as $m)
                    <option value="{{ $m }}" @if($months==$m) selected @endif>{{ $m }} months</option>
                @endforeach
            </select>
            <button class="btn btn-sm btn-primary">Apply</button>
        </form>
    </div>

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card"><div class="card-body">
                <h6>Inputs</h6>
                <div class="small text-muted mb-2">Active Budget</div>
                @if($forecast['budget'])
                    <div><strong>{{ $forecast['budget']['name'] }}</strong></div>
                    <div class="small">Expected In: {{ number_format($forecast['budget']['expected_monthly_inflow'],2) }}</div>
                    <div class="small">Expected Out: {{ number_format($forecast['budget']['expected_monthly_outflow'],2) }}</div>
                @else
                    <div class="text-muted small">No active cashflow budget set.</div>
                @endif
                <hr>
                <div class="small text-muted">Recurring monthly outflow (converted)</div>
                <div><strong>{{ number_format($forecast['recurring_monthly_outflow'],2) }}</strong></div>
                <div class="text-muted small mt-2">Manage these in Settings → Cashflow Setup (added in this pass).</div>
            </div></div>
        </div>

        <div class="col-lg-8">
            <div class="card"><div class="card-body">
                <h6>Forecast</h6>
                <table class="table table-sm">
                    <thead><tr><th>Month</th><th class="text-end">Expected In</th><th class="text-end">A/R</th><th class="text-end">Expected Out</th><th class="text-end">A/P</th><th class="text-end">Net</th></tr></thead>
                    <tbody>
                    @foreach($forecast['months'] as $m => $row)
                        <tr>
                            <td>{{ $m }}</td>
                            <td class="text-end">{{ number_format($row['expected_inflow'],2) }}</td>
                                <td class="text-end">{{ number_format($row['ar'] ?? 0,2) }}</td>
                            <td class="text-end">{{ number_format($row['expected_outflow'],2) }}</td>
                                <td class="text-end">{{ number_format($row['ap'] ?? 0,2) }}</td>
                            <td class="text-end">{{ number_format($row['net'],2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div></div>
        </div>
    </div>

    <div class="card mt-3"><div class="card-body">
        <h6>Historical (cash accounts only)</h6>
        <table class="table table-sm">
            <thead><tr><th>Month</th><th class="text-end">In</th><th class="text-end">Out</th><th class="text-end">Net</th></tr></thead>
            <tbody>
            @foreach($series as $m => $row)
                <tr>
                    <td>{{ $m }}</td>
                    <td class="text-end">{{ number_format($row['inflow'],2) }}</td>
                    <td class="text-end">{{ number_format($row['outflow'],2) }}</td>
                    <td class="text-end">{{ number_format($row['net'],2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div></div>
</div>
@endsection
