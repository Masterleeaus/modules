@extends('layouts.app')
<?php $pageTitle = $pageTitle ?? 'New Expense'; ?>
@section('content')
@include('accountings::partials.nav')


<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">New Expense</h4>
        <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="post" action="{{ route('expenses.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="expense_date" class="form-control" value="{{ date('Y-m-d') }}" />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Vendor</label>
                        <select name="vendor_id" class="form-select">
                            <option value="">—</option>
                            @foreach($vendors as $v)
                                <option value="{{ $v->id }}">{{ $v->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Payment</label>
                        <select name="payment_method" class="form-select">
                            @foreach(['cash','bank','card'] as $m)
                                <option value="{{ $m }}">{{ ucfirst($m) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Amount (incl GST)</label>
                        <input type="number" step="0.01" name="amount" class="form-control" value="0.00" />
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-4">
                        <label class="form-label">Account</label>
                        <select name="coa_id" class="form-select">
                            <option value="">—</option>
                            @foreach($accounts as $a)
                                <option value="{{ $a->id }}">{{ $a->coa }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tax Code</label>
                        <select name="tax_code_id" class="form-select">
                            <option value="">—</option>
                            @foreach($taxCodes as $t)
                                <option value="{{ $t->id }}">{{ $t->code }} ({{ (float)$t->rate*100 }}%)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Service Line</label>
                        <select name="service_line_id" class="form-select">
                            <option value="">—</option>
                            @foreach($serviceLines as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Job Ref</label>
                        <input type="text" name="job_ref" class="form-control" placeholder="optional" />
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-control" />
                </div>

                <div class="mt-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="3"></textarea>
                </div>

                <div class="mt-3">
                    <button class="btn btn-primary">Save Expense</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
