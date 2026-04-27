@php($pageTitle = $pageTitle ?? __('accountings::app.menu.accounting'))
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Cashflow</h4>
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
        'fields' => ['context' => 'overview', 'note' => 'Monthly net summary + quick actions'],
        'user_id' => user()->id ?? null,
        'company_id' => user()->company_id ?? null,
    ];
@endphp

@if (function_exists('user') && user() && user()->can('titanzero.use'))
    @php
        $base = [
            'return_url' => url()->current(),
            'page' => [
                'route_name' => request()->route()?->getName(),
                'url' => url()->current(),
            ],
            'record' => [
                'record_type' => 'cashflow',
                'record_id' => null,
            ],
            'user_id' => user()->id ?? null,
            'company_id' => user()->company_id ?? null,
        ];

        $kpiPack = [
            'today' => $kpis['today'] ?? null,
            'overdue_total' => $kpis['overdue_total'] ?? 0,
            'overdue_count' => $kpis['overdue_count'] ?? 0,
            'next7_inflows' => $kpis['next7_inflows'] ?? 0,
            'next7_outflows' => $kpis['next7_outflows'] ?? 0,
        ];

        $overduePack = $overdue ?? [];
    @endphp

    <div class="row g-3 mb-3">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Ask Titan Zero</span>
                    <span class="text-muted small">Option A — Titan Zero shows results, then you return here.</span>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <div class="p-2 border rounded">
                                <div class="text-muted small">Overdue (total)</div>
                                <div class="h5 mb-0">{{ number_format((float)($kpiPack['overdue_total'] ?? 0), 2) }}</div>
                                <div class="text-muted small">{{ (int)($kpiPack['overdue_count'] ?? 0) }} invoices</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-2 border rounded">
                                <div class="text-muted small">Next 7 days (inflows)</div>
                                <div class="h5 mb-0">{{ number_format((float)($kpiPack['next7_inflows'] ?? 0), 2) }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-2 border rounded">
                                <div class="text-muted small">Next 7 days (outflows)</div>
                                <div class="h5 mb-0">{{ number_format((float)($kpiPack['next7_outflows'] ?? 0), 2) }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-2 border rounded">
                                <div class="text-muted small">Net next 7 days</div>
                                @php $net7 = (float)($kpiPack['next7_inflows'] ?? 0) - (float)($kpiPack['next7_outflows'] ?? 0); @endphp
                                <div class="h5 mb-0">{{ number_format($net7, 2) }}</div>
                            </div>
                        </div>
                    </div>

                    @if (\Illuminate\Support\Facades\Route::has('titan.zero.intent.run') && \Illuminate\Support\Facades\View::exists('titanzero::partials.ask_button'))
                        <div class="d-flex flex-wrap gap-2">
                            <form method="POST" action="{{ route('titan.zero.intent.run') }}">
                                @csrf
                                <input type="hidden" name="payload" value='@json(array_merge($base, [
                                    "intent" => "cashflow_risk_scan",
                                    "fields" => [
                                        "context" => "cashflow_overview",
                                        "kpis" => $kpiPack,
                                        "top_overdue" => array_slice($overduePack, 0, 10),
                                        "goal" => "identify risks + quick wins"
                                    ],
                                    "hero_key" => "business"
                                ]))'>
                                <button class="btn btn-sm btn-primary">Risk scan</button>
                            </form>

                            <form method="POST" action="{{ route('titan.zero.intent.run') }}">
                                @csrf
                                <input type="hidden" name="payload" value='@json(array_merge($base, [
                                    "intent" => "prioritise_overdue",
                                    "fields" => [
                                        "context" => "collections_priority",
                                        "kpis" => $kpiPack,
                                        "top_overdue" => $overduePack,
                                        "goal" => "who to chase first"
                                    ],
                                    "hero_key" => "collections"
                                ]))'>
                                <button class="btn btn-sm btn-outline-primary">Who do I chase first?</button>
                            </form>

                            <form method="POST" action="{{ route('titan.zero.intent.run') }}">
                                @csrf
                                <input type="hidden" name="payload" value='@json(array_merge($base, [
                                    "intent" => "improve_cashflow",
                                    "fields" => [
                                        "context" => "cashflow_improvements",
                                        "kpis" => $kpiPack,
                                        "goal" => "reduce cash stress",
                                        "ideas" => ["deposits", "staged payments", "tighter terms", "weekly collections routine"]
                                    ],
                                    "hero_key" => "business"
                                ]))'>
                                <button class="btn btn-sm btn-outline-secondary">Improve cashflow</button>
                            </form>

                            <form method="POST" action="{{ route('titan.zero.intent.run') }}">
                                @csrf
                                <input type="hidden" name="payload" value='@json(array_merge($base, [
                                    "intent" => "create_followup_task",
                                    "fields" => [
                                        "context" => "collections_followup",
                                        "kpis" => $kpiPack,
                                        "top_overdue" => $overduePack,
                                        "channel" => "sms_or_email",
                                        "goal" => "draft follow-up sequence for overdue invoices"
                                    ],
                                    "hero_key" => "collections"
                                ]))'>
                                <button class="btn btn-sm btn-warning">Draft follow-up</button>
                            </form>

                            @if (\Illuminate\Support\Facades\Route::has('titan.zero.heroes.index'))
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('titan.zero.heroes.index') }}">Pick a Hero</a>
                            @endif

    @if(!empty($overdue) && count($overdue))
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                <span>Top overdue invoices</span>
                <a class="small" href="{{ route('cashflow.collections') }}">Open Collections</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Due</th>
                                <th class="text-end">Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($overdue as $o)
                                <tr>
                                    <td>#{{ $o['invoice_id'] }}</td>
                                    <td>{{ $o['due_date'] }}</td>
                                    <td class="text-end">{{ number_format((float)$o['total'],2) }}</td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('cashflow.invoice_action', $o['invoice_id']) }}">Follow up</a>
                                        @if(\Illuminate\Support\Facades\Route::has('cashflow.invoice_customerconnect'))
                                            <a class="btn btn-sm btn-outline-secondary ms-2" href="{{ route('cashflow.invoice_customerconnect', $o['invoice_id']) }}">Message</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

                        </div>

                        <div class="small text-muted mt-2">
                            Tip: use <strong>Collections</strong> for follow-ups and <strong>Business</strong> for runway planning.
                        </div>
                    @else
                        <div class="alert alert-light mb-0">
                            Titan Zero run endpoint (or ask_button partial) not detected. You can still open Titan Zero:
                            @if (\Illuminate\Support\Facades\Route::has('titan.zero.index'))
                                <a href="{{ route('titan.zero.index') }}">Titan Zero dashboard</a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif

                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif

@if (user() && user()->can('titanzero.use') && \Illuminate\Support\Facades\View::exists('titanzero::partials.ask_button'))
    @include('titanzero::partials.ask_button', ['payload' => $titanZeroPayload])
@endif

            <div class="text-muted small">Simple cash + bank cashflow from journals (heuristic until bank feeds are added)</div>
        </div>
    </div>

    <form class="card mb-3" method="GET">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-3">
                    <label class="form-label">From</label>
                    <input type="date" name="from" value="{{ $from }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">To</label>
                    <input type="date" name="to" value="{{ $to }}" class="form-control">
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <button class="btn btn-primary btn-sm">Apply</button>
                    <a href="{{ route('cashflow.index') }}" class="btn btn-outline-secondary btn-sm ms-2">Reset</a>
                </div>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="card-body">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th class="text-end">Inflow</th>
                        <th class="text-end">Outflow</th>
                        <th class="text-end">Net</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($series as $month => $row)
                        <tr>
                            <td>{{ $month }}</td>
                            <td class="text-end">{{ number_format($row['inflow'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['outflow'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['net'], 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-muted">No cash/bank journal lines found. Make sure your COA descriptions include “cash” or “bank”.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
