@extends('layouts.app')
<?php $pageTitle = $pageTitle ?? __('accountings::app.menu.accounting'); ?>
@section('content')
@php
use Modules\Accountings\Services\ReceivablesService;
use Modules\Accountings\Services\PayablesService;
$rx = (new ReceivablesService())->summary(14);
$px = (new PayablesService())->summary(14);
@endphp

<div class="container-fluid">
<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card"><div class="card-body">
            <div class="text-muted">Money Owed (Overdue)</div>
            <h4>{{ number_format($rx['overdue_total'] ?? 0,2) }}</h4>
            <small class="text-muted">{{ (int)($rx['overdue_count'] ?? 0) }} invoices</small>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card"><div class="card-body">
            <div class="text-muted">Due Next 14 Days</div>
            <h4>{{ number_format($rx['due_soon_total'] ?? 0,2) }}</h4>
            <small class="text-muted">{{ (int)($rx['due_soon_count'] ?? 0) }} invoices</small>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card"><div class="card-body">
            <div class="text-muted">Bills Next 14 Days</div>
            <h4>{{ number_format($px['due_soon_total'] ?? 0,2) }}</h4>
            <small class="text-muted">{{ (int)($px['due_soon_count'] ?? 0) }} bills</small>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card"><div class="card-body">
            <div class="text-muted">Net (14 Days)</div>
            <h4>{{ number_format(($rx['due_soon_total'] ?? 0) - ($px['due_soon_total'] ?? 0),2) }}</h4>
            <small class="text-muted">Money in minus bills</small>
        </div></div>
    </div>
</div>

<div class="mb-3">
    <a href="{{ route('cashflow.runway_weekly') }}" class="btn btn-sm btn-outline-primary">Weekly Runway</a>
    <a href="{{ route('cashflow.forecast') }}" class="btn btn-sm btn-outline-primary ms-2">Forecast</a>
    <a href="{{ route('cashflow.receivables') }}" class="btn btn-sm btn-outline-primary ms-2">Money Owed</a>
    <a href="{{ route('cashflow.payables') }}" class="btn btn-sm btn-outline-primary ms-2">Bills To Pay</a>
    <a href="{{ route('cashflow.collections') }}" class="btn btn-sm btn-outline-primary ms-2">Collections Helper</a>
    <a href="{{ route('cashflow.ar_aging') }}" class="btn btn-sm btn-outline-primary ms-2">A/R Aging</a>
    <a href="{{ route('cashflow.top_overdue') }}" class="btn btn-sm btn-outline-primary ms-2">Top 20 Overdue</a>
    <a href="{{ route('cashflow.planner') }}" class="btn btn-sm btn-outline-primary ms-2">Weekly Planner</a>
    <a href="{{ route('acc-settings.cashflow') }}" class="btn btn-sm btn-primary ms-2">Cashflow Settings</a>
</div>

<h4>Accounting Dashboard</h4>
<div class="row g-3">
@foreach([
    'Cash In'=>$cashIn,
    'Cash Out'=>$cashOut,
    'Net Cash'=>$netCash,
    'Revenue'=>$revenue,
    'Expenses'=>$expenses
] as $label=>$val)
<div class="col-md-3"><div class="card"><div class="card-body">
<div class="text-muted">{{ $label }}</div>
<h4>{{ number_format($val,2) }}</h4>
</div></div></div>
@endforeach
</div>
<div class="mt-3">
<a href="{{ route('cashflow.index') }}" class="btn btn-sm btn-outline-primary">Cashflow</a>
<a href="{{ route('pnl.index') }}" class="btn btn-sm btn-outline-primary">P&L</a>
<a href="{{ route('balance-sheet.index') }}" class="btn btn-sm btn-outline-primary">Balance Sheet</a>
</div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <h6>3‑month forecast (budget + recurring)</h6>
        <table class="table table-sm">
            <thead><tr><th>Month</th><th class="text-end">Expected In</th><th class="text-end">Expected Out</th><th class="text-end">Net</th></tr></thead>
            <tbody>
            @foreach(($forecast3['months'] ?? []) as $m => $row)
                <tr>
                    <td>{{ $m }}</td>
                    <td class="text-end">{{ number_format($row['expected_inflow'],2) }}</td>
                    <td class="text-end">{{ number_format($row['expected_outflow'],2) }}</td>
                    <td class="text-end">{{ number_format($row['net'],2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <a href="{{ route('cashflow.forecast') }}" class="btn btn-sm btn-outline-primary">Open forecast</a>
        <a href="{{ route('cashflow.runway') }}" class="btn btn-sm btn-outline-primary ms-2">Cash Runway</a>
        <a href="{{ route('cashflow.runway_weekly') }}" class="btn btn-sm btn-outline-primary ms-2">Weekly Runway</a>
        <a href="{{ route('acc-settings.cashflow') }}" class="btn btn-sm btn-outline-secondary">Cashflow setup</a>
    </div>
</div>

@endsection

