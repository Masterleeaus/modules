@extends('layouts.app')
<?php $pageTitle = $pageTitle ?? 'GST Report'; ?>
@section('content')
@include('accountings::partials.nav')


<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">GST Summary</h4>
        <a href="{{ route('accountings.dashboard') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>

    <div class="card"><div class="card-body">
        <form class="row g-2 mb-3" method="get" action="{{ route('accountings.reports.gst') }}">
            <div class="col-md-2">
                <input type="date" name="from" value="{{ $from }}" class="form-control form-control-sm" placeholder="From" />
            </div>
            <div class="col-md-2">
                <input type="date" name="to" value="{{ $to }}" class="form-control form-control-sm" placeholder="To" />
            </div>
            <div class="col-md-2">
                <select name="basis" class="form-select form-select-sm">
                    <option value="accrual" {{ ($basis ?? 'accrual') === 'accrual' ? 'selected' : '' }}>Accrual basis</option>
                    <option value="cash" {{ ($basis ?? 'accrual') === 'cash' ? 'selected' : '' }}>Cash basis</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary btn-sm">Run</button>
                <a class="btn btn-outline-primary btn-sm"
                   href="{{ route('accountings.reports.gst_export', ['from' => $from, 'to' => $to, 'basis' => $basis ?? 'accrual']) }}">
                    Export CSV
                </a>
            </div>
            <div class="col-md-4 text-muted small d-flex align-items-center">
                Accrual = bills by bill_date; Cash = bills by payment date (requires bill payments).
            </div>
        </form>

        <div class="row g-3">
            <div class="col-md-3"><div class="border rounded p-3">
                <div class="text-muted">GST Collected</div>
                <div class="fs-4 fw-semibold">{{ number_format((float)$gst_collected,2) }}</div>
            </div></div>
            <div class="col-md-3"><div class="border rounded p-3">
                <div class="text-muted">GST Paid</div>
                <div class="fs-4 fw-semibold">{{ number_format((float)$gst_paid,2) }}</div>
                <div class="text-muted small">Bills: {{ number_format((float)$gst_paid_bills,2) }} · Expenses: {{ number_format((float)$gst_paid_expenses,2) }}</div>
            </div></div>
            <div class="col-md-3"><div class="border rounded p-3">
                <div class="text-muted">Net GST</div>
                <div class="fs-4 fw-semibold">{{ number_format((float)$net_gst,2) }}</div>
                <div class="text-muted small">Collected - Paid</div>
            </div></div>
        </div>

        <div class="text-muted small mt-3">
            GST collected is best-effort from the host <code>invoices</code> table (schema varies).
        </div>
    </div></div>
</div>

@endsection
