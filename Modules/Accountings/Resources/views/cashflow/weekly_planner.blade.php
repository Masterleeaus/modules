@php($pageTitle = $pageTitle ?? __('accountings::app.menu.accounting'))
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-3 d-flex justify-content-between align-items-end">
        <div>
            <h4 class="mb-0">Weekly Cash Planner</h4>
@php
    $titanZeroPayload = [
        'intent' => 'cashflow_risk_scan',
        'return_url' => url()->current(),
        'page' => [
            'route_name' => request()->route()?->getName(),
            'url' => url()->current(),
        ],
        'record' => [
            'record_type' => 'cashflow',
            'record_id' => null,
        ],
        'fields' => ['context' => 'weekly_planner', 'opening_cash' => request('opening_cash'), 'scenario' => request('scenario'), 'weeks' => request('weeks')],
        'user_id' => user()->id ?? null,
        'company_id' => user()->company_id ?? null,
    ];
@endphp
@if (user() && user()->can('titanzero.use') && \Illuminate\Support\Facades\View::exists('titanzero::partials.ask_button'))
    @include('titanzero::partials.ask_button', ['payload' => $titanZeroPayload])
@endif

            <small class="text-muted">Tradie-friendly plan: inflows, outflows, and closing cash by week.</small>
        </div>
        <div class="d-flex gap-2">
            
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Opening cash (bank balance)</label>
                    <input name="opening_cash" type="number" step="0.01" class="form-control" value="{{ $opening_cash }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Scenario</label>
                    <select name="scenario" class="form-select">
                        <option value="best" @if($scenario==='best') selected @endif>Best</option>
                        <option value="expected" @if($scenario==='expected') selected @endif>Expected</option>
                        <option value="worst" @if($scenario==='worst') selected @endif>Worst</option>
                    </select>
                    <div class="form-text">Best = higher inflows, slightly lower outflows. Worst = lower inflows, higher outflows.</div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Weeks</label>
                    <select name="weeks" class="form-select">
                        @foreach([4,6,8,10,12,16] as $w)
                            <option value="{{ $w }}" @if(count($weeks)===$w) selected @endif>{{ $w }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Update</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('cashflow.collections') }}" class="btn btn-outline-secondary w-100">Collections</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card"><div class="card-body">
                <div class="text-muted">Receivables source</div>
                <div class="small">{{ $sources['receivables'] }}</div>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card"><div class="card-body">
                <div class="text-muted">Payables source</div>
                <div class="small">{{ $sources['payables'] }}</div>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card"><div class="card-body">
                <div class="text-muted">Tip</div>
                <div class="small">If cash dips: chase overdue invoices, delay non-urgent bills, or schedule deposits.</div>
            </div></div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <span>Weekly plan ({{ ucfirst($scenario) }})</span>
            <span class="text-muted small">Best-effort estimates</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th>Week starting</th>
                            <th class="text-end">Inflows</th>
                            <th class="text-end">Outflows</th>
                            <th class="text-end">Net</th>
                            <th class="text-end">Closing cash</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($weeks as $wk)
                            <tr @if($wk['closing_cash'] < 0) class="table-warning" @endif>
                                <td>{{ $wk['week_start'] }}</td>
                                <td class="text-end">{{ number_format($wk['inflows'],2) }}</td>
                                <td class="text-end">{{ number_format($wk['outflows'],2) }}</td>
                                <td class="text-end">{{ number_format($wk['net'],2) }}</td>
                                <td class="text-end"><strong>{{ number_format($wk['closing_cash'],2) }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
