@extends('layouts.app')
<?php $pageTitle = $pageTitle ?? 'Job Profitability'; ?>
@section('content')
@include('accountings::partials.nav')


<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Job Profitability</h4>
        <a href="{{ route('accountings.dashboard') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>

    <div class="card"><div class="card-body">
        <form class="row g-2 mb-3" method="get" action="{{ route('accountings.reports.job_profitability') }}">
            <div class="col-md-2">
                <input type="date" name="from" value="{{ $from }}" class="form-control form-control-sm" />
            </div>
            <div class="col-md-2">
                <input type="date" name="to" value="{{ $to }}" class="form-control form-control-sm" />
            </div>
            <div class="col-md-3 d-flex align-items-center">
                <div class="form-check me-3">
                    <input class="form-check-input" type="checkbox" name="include_revenue" value="1" id="include_revenue" {{ ($includeRevenue ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label small" for="include_revenue">Include revenue (best-effort)</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="paid_only" value="1" id="paid_only" {{ ($paidOnly ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label small" for="paid_only">Paid only</label>
                </div>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary btn-sm">Run</button>
            </div>
            <div class="col-md-3 text-muted small d-flex align-items-center">
                Costs come from bills/expenses with a <code>job_ref</code>. Revenue is resolved from host <code>invoices</code> table when possible.
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead>
                    <tr>
                        <th>Job Ref</th>
                        <th class="text-end">Revenue</th>
                        <th class="text-end">Cost</th>
                        <th class="text-end">Profit</th>
                        <th class="text-end">Margin</th>
                        <th class="text-end">Lines</th>
                        <th>Last Cost</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $r)
                        <tr>
                            <td class="fw-semibold">{{ $r['job_ref'] }}</td>
                            <td class="text-end">
                                @if($r['revenue'] !== null)
                                    {{ number_format((float)$r['revenue'],2) }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end">{{ number_format((float)$r['total_cost'],2) }}</td>
                            <td class="text-end">
                                @if($r['profit'] !== null)
                                    {{ number_format((float)$r['profit'],2) }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($r['margin'] !== null)
                                    {{ number_format((float)$r['margin'],2) }}%
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end">{{ (int)$r['line_count'] }}</td>
                            <td>{{ $r['last_cost_at'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-muted">No job costs allocated yet. Add a Job Ref on bills/expenses.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="text-muted small">
            Note: revenue mapping is best-effort because schemas differ between modules. If you want perfect profitability, tell me your jobs/invoices schema (tables + columns) and I’ll hard-wire the join.
        </div>
    </div></div>
</div>

@endsection
