@php($pageTitle = $pageTitle ?? __('accountings::app.menu.accounting'))
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-3 d-flex justify-content-between align-items-end">
        <div>
            <h4 class="mb-0">Weekly Runway</h4>
            <small class="text-muted">Week-by-week A/R and A/P (best effort). Monday-start weeks.</small>
        </div>
        <form method="GET" class="d-flex gap-2">
            <select name="weeks" class="form-select form-select-sm" style="width: 150px;">
                @foreach([4,8,12] as $w)
                    <option value="{{ $w }}" @if($weeks==$w) selected @endif>Next {{ $w }} weeks</option>
                @endforeach
            </select>
            <button class="btn btn-sm btn-primary">Apply</button>
        </form>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card"><div class="card-body">
                <div class="text-muted">Total Receivables (A/R)</div>
                <h4>{{ number_format($total_ar,2) }}</h4>
                <small class="text-muted">{{ $from }} → {{ $to }}</small>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card"><div class="card-body">
                <div class="text-muted">Total Payables (A/P)</div>
                <h4>{{ number_format($total_ap,2) }}</h4>
                <small class="text-muted">{{ $from }} → {{ $to }}</small>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card"><div class="card-body">
                <div class="text-muted">Net</div>
                <h4>{{ number_format($total_net,2) }}</h4>
                <small class="text-muted">A/R minus A/P</small>
            </div></div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Week</th>
                            <th class="text-end">A/R</th>
                            <th class="text-end">A/P</th>
                            <th class="text-end">Net</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $r)
                            <tr>
                                <td>{{ $r['week_start'] }} → {{ $r['week_end'] }}</td>
                                <td class="text-end">{{ number_format($r['ar'],2) }}</td>
                                <td class="text-end">{{ number_format($r['ap'],2) }}</td>
                                <td class="text-end">{{ number_format($r['net'],2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="text-muted small mt-2">
                Notes: A/R uses invoice due_date (fallback invoice_date/issue_date/date) and subtracts payments where possible.
                A/P uses expenses date columns and excludes paid where possible.
            </div>
        </div>
    </div>
</div>
@endsection
