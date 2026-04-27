@php($pageTitle = $pageTitle ?? __('accountings::app.menu.accounting'))
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-3 d-flex justify-content-between align-items-end">
        <div>
            <h4 class="mb-0">Weekly Cashflow Planner</h4>
            <small class="text-muted">Combines invoices + expenses + (optional) budget + recurring. Scenario toggles help you sanity-check runway.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('cashflow.dashboard') }}" class="btn btn-sm btn-outline-secondary">Cashflow Dashboard</a>
        </div>
    </div>

    <form method="GET" class="card mb-3">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small">Weeks</label>
                    <select name="weeks" class="form-select form-select-sm">
                        @foreach([4,6,8,10,12,16,20,26] as $w)
                            <option value="{{ $w }}" @if((int)$weeks===$w) selected @endif>{{ $w }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small">Scenario</label>
                    <select name="scenario" class="form-select form-select-sm">
                        <option value="expected" @if($scenario==='expected') selected @endif>Expected</option>
                        <option value="best" @if($scenario==='best') selected @endif>Best (in +10%, out -10%)</option>
                        <option value="worst" @if($scenario==='worst') selected @endif>Worst (in -15%, out +10%)</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small">Starting cash (bank)</label>
                    <input type="number" step="0.01" name="starting_cash" value="{{ $starting_cash }}" class="form-control form-control-sm" />
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" value="1" id="use_last_bank" name="use_last_bank" @if(request('use_last_bank')==1) checked @endif>
                        <label class="form-check-label small" for="use_last_bank">Use last known bank balance (best effort)</label>
                    </div>
                    @if(!empty($bank_meta) && !empty($bank_meta['source']))
                        <div class="text-muted small mt-1">Source: {{ $bank_meta['source'] }}</div>
                    @endif
                </div>

                <div class="col-md-2">
                    <label class="form-label small">GST buffer</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="gst_buffer" name="gst_buffer" @if(!empty($gst_buffer)) checked @endif>
                        <label class="form-check-label small" for="gst_buffer">Add 10% to outflows</label>
                    </div>
                    <button class="btn btn-sm btn-primary w-100 mt-2">Update</button>
                </div>

                <div class="col-md-2 text-end">
                    <div class="text-muted small">End cash</div>
                    <div class="fw-bold @if($totals['cash_end']<0) text-danger @endif">{{ number_format($totals['cash_end'],2) }}</div>
                </div>
            </div>
        </div>
    </form>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card"><div class="card-body">
                <div class="text-muted">Total inflows</div>
                <h4 class="mb-0">{{ number_format($totals['in_total'],2) }}</h4>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card"><div class="card-body">
                <div class="text-muted">Total outflows</div>
                <h4 class="mb-0">{{ number_format($totals['out_total'],2) }}</h4>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card"><div class="card-body">
                <div class="text-muted">Net</div>
                <h4 class="mb-0">{{ number_format($totals['net'],2) }}</h4>
            </div></div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th>Week</th>
                            <th class="text-end">In (A/R)</th>
                            <th class="text-end">In (Budget)</th>
                            <th class="text-end">Out (A/P)</th>
                            <th class="text-end">Out (Budget)</th>
                            <th class="text-end">Out (Recurring)</th>
                            <th class="text-end">GST buffer</th>
                            <th class="text-end">In total</th>
                            <th class="text-end">Out total</th>
                            <th class="text-end">Net</th>
                            <th class="text-end">Cash end</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $r)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $r['week_start'] }}</div>
                                    <div class="text-muted small">to {{ $r['week_end'] }}</div>
                                    @if(!empty($r['warn_materials_heavy']))
                                        <span class="badge bg-warning text-dark mt-1">Materials-heavy week</span>
                                    @endif
                                    @if(!empty($r['warn_cash_negative']))
                                        <span class="badge bg-danger mt-1">Cash goes negative</span>
                                    @endif
                                </td>
                                <td class="text-end">{{ number_format($r['in_ar'],2) }}</td>
                                <td class="text-end">{{ number_format($r['in_budget'],2) }}</td>
                                <td class="text-end">{{ number_format($r['out_ap'],2) }}</td>
                                <td class="text-end">{{ number_format($r['out_budget'],2) }}</td>
                                <td class="text-end">{{ number_format($r['out_recurring'],2) }}</td>
                                <td class="text-end">{{ number_format($r['out_gst_buffer'],2) }}</td>
                                <td class="text-end fw-semibold">{{ number_format($r['in_total'],2) }}</td>
                                <td class="text-end fw-semibold">{{ number_format($r['out_total'],2) }}</td>
                                <td class="text-end @if($r['net']<0) text-danger @endif">{{ number_format($r['net'],2) }}</td>
                                <td class="text-end @if($r['cash_end']<0) text-danger @endif">{{ number_format($r['cash_end'],2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="text-muted small">
                Notes: Budget values come from the active Cashflow Budget (spread evenly by week). Recurring outflows come from active Recurring Expenses (approx spread). GST buffer is a safety buffer (not an accounting posting).
            </div>
        </div>
    </div>
</div>
@endsection
