@extends('layouts.app')
<?php $pageTitle = $pageTitle ?? 'Bill'; ?>
@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Bill: {{ $bill->bill_number ?: ('BILL-'.$bill->id) }}</h4>
        <a href="{{ route('bills.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card"><div class="card-body">
                <div class="text-muted">Vendor</div>
                <div class="fw-semibold">{{ $bill->vendor->name ?? '—' }}</div>

                <div class="text-muted mt-2">Dates</div>
                <div>Bill: {{ optional($bill->bill_date)->format('Y-m-d') }}</div>
                <div>Due: {{ optional($bill->due_date)->format('Y-m-d') }}</div>

                <div class="text-muted mt-2">Status</div>
                <span class="badge bg-light text-dark">{{ $bill->status }}</span>

                <div class="text-muted mt-2">Payments</div>
                <div>Paid: <span class="fw-semibold">{{ number_format((float)$bill->paid_total,2) }}</span></div>
                <div>Balance: <span class="fw-semibold">{{ number_format((float)$bill->balance_due,2) }}</span></div>
            </div></div>

            <div class="card mt-3"><div class="card-body">
                <h6 class="mb-2">Record Payment</h6>
                <form method="post" action="{{ route('bills.payments.store', $bill->id) }}" class="row g-2">
                    @csrf
                    <div class="col-6">
                        <input type="date" name="paid_at" class="form-control form-control-sm" value="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div class="col-6">
                        <input type="number" step="0.01" name="amount" class="form-control form-control-sm" placeholder="Amount" required>
                    </div>
                    <div class="col-6">
                        <input type="text" name="method" class="form-control form-control-sm" placeholder="Method (bank/card/cash)">
                    </div>
                    <div class="col-6">
                        <input type="text" name="reference" class="form-control form-control-sm" placeholder="Reference">
                    </div>
                    <div class="col-12">
                        <textarea name="notes" class="form-control form-control-sm" rows="2" placeholder="Notes"></textarea>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-outline-primary btn-sm">Save payment</button>
                    </div>
                </form>
            </div></div>
        </div>

        <div class="col-md-8">
            <div class="card"><div class="card-body">
                <h6>Lines</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                        <tr>
                            <th>Description</th>
                            <th>Tax</th>
                            <th>Service</th>
                            <th class="text-end">Subtotal</th>
                            <th class="text-end">Tax</th>
                            <th class="text-end">Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($bill->lines as $l)
                            <tr>
                                <td>{{ $l->description }}</td>
                                <td>{{ $l->taxCode->code ?? '—' }}</td>
                                <td>{{ $l->serviceLine->name ?? '—' }}</td>
                                <td class="text-end">{{ number_format((float)$l->line_subtotal,2) }}</td>
                                <td class="text-end">{{ number_format((float)$l->line_tax,2) }}</td>
                                <td class="text-end">{{ number_format((float)$l->line_total,2) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Totals</th>
                                <th class="text-end">{{ number_format((float)$bill->subtotal,2) }}</th>
                                <th class="text-end">{{ number_format((float)$bill->tax_total,2) }}</th>
                                <th class="text-end">{{ number_format((float)$bill->total,2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <hr class="my-3">

                <h6 class="mb-2">Payment History</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Method</th>
                            <th>Reference</th>
                            <th class="text-end">Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($bill->payments as $p)
                            <tr>
                                <td>{{ optional($p->paid_at)->format('Y-m-d') }}</td>
                                <td>{{ $p->method ?? '—' }}</td>
                                <td>{{ $p->reference ?? '—' }}</td>
                                <td class="text-end">{{ number_format((float)$p->amount,2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-muted">No payments recorded yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if($bill->notes)
                    <div class="mt-3">
                        <div class="text-muted">Notes</div>
                        <div>{{ $bill->notes }}</div>
                    </div>
                @endif
            </div></div>
        </div>
    </div>
</div>

@endsection
