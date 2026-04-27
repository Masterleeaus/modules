@extends('layouts.app')
<?php $pageTitle = $pageTitle ?? 'Vendor Statement'; ?>
@section('content')
@include('accountings::partials.nav')


<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Vendor Statement</h4>
        <a href="{{ route('accountings.dashboard') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>

    <div class="card"><div class="card-body">
        <form class="row g-2 mb-3" method="get" action="{{ route('accountings.reports.vendor_statement') }}">
            <div class="col-md-3">
                <input type="date" name="from" value="{{ $from }}" class="form-control form-control-sm" />
            </div>
            <div class="col-md-3">
                <input type="date" name="to" value="{{ $to }}" class="form-control form-control-sm" />
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary btn-sm">Run</button>
            </div>
            <div class="col-md-4 text-muted small d-flex align-items-center">
                Balance = Bills (by bill_date) minus Payments (by paid_at).
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead>
                    <tr>
                        <th>Vendor</th>
                        <th class="text-end">Billed</th>
                        <th class="text-end">Paid</th>
                        <th class="text-end">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $r)
                        <tr>
                            <td class="fw-semibold">{{ $r['vendor_name'] }}</td>
                            <td class="text-end">{{ number_format((float)$r['billed_total'],2) }}</td>
                            <td class="text-end">{{ number_format((float)$r['paid_total'],2) }}</td>
                            <td class="text-end">{{ number_format((float)$r['balance'],2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-muted">No vendor bills in this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div></div>
</div>

@endsection
