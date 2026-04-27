@php($pageTitle = $pageTitle ?? __('accountings::app.menu.accounting'))
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-3 d-flex justify-content-between align-items-end">
        <div>
            <h4 class="mb-0">Cash Runway</h4>
            <small class="text-muted">Tradie-friendly view: what’s due in the next N days (best effort from invoices + expenses).</small>
        </div>
        <form method="GET" class="d-flex gap-2">
            <select name="days" class="form-select form-select-sm" style="width: 140px;">
                @foreach([14,30,60] as $d)
                    <option value="{{ $d }}" @if($days==$d) selected @endif>Next {{ $d }} days</option>
                @endforeach
            </select>
            <button class="btn btn-sm btn-primary">Apply</button>
        </form>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card"><div class="card-body">
                <div class="text-muted">Receivables (A/R)</div>
                <h4>{{ number_format($ar,2) }}</h4>
                <small class="text-muted">{{ $from }} → {{ $to }}</small>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card"><div class="card-body">
                <div class="text-muted">Payables (A/P)</div>
                <h4>{{ number_format($ap,2) }}</h4>
                <small class="text-muted">{{ $from }} → {{ $to }}</small>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card"><div class="card-body">
                <div class="text-muted">Net</div>
                <h4>{{ number_format($net,2) }}</h4>
                <small class="text-muted">A/R minus A/P</small>
            </div></div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <div class="text-muted small">
                Notes: Uses invoice due date (or issue date fallback) and expense date (or fallback). If your schema differs, we’ll map columns in next pass.
            </div>
        </div>
    </div>
</div>
@endsection
