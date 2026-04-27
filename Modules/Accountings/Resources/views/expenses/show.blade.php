@extends('layouts.app')
<?php $pageTitle = $pageTitle ?? 'Expense'; ?>
@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Expense: {{ optional($expense->expense_date)->format('Y-m-d') }}</h4>
        <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>

    <div class="card"><div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="text-muted">Vendor</div>
                <div class="fw-semibold">{{ $expense->vendor->name ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted">Job Ref</div>
                <div class="fw-semibold">{{ $expense->job_ref ?: '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted">Amount</div>
                <div class="fw-semibold">{{ number_format((float)$expense->amount,2) }}</div>
                <div class="text-muted small">Tax: {{ number_format((float)$expense->tax_amount,2) }}</div>
            </div>
        </div>

        <hr />

        <div class="text-muted">Description</div>
        <div class="fw-semibold">{{ $expense->description ?: '—' }}</div>

        @if($expense->notes)
            <div class="mt-3">
                <div class="text-muted">Notes</div>
                <div>{{ $expense->notes }}</div>
            </div>
        @endif
    </div></div>
</div>

@endsection
