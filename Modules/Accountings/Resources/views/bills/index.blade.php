@extends('layouts.app')
<?php $pageTitle = $pageTitle ?? 'Bills'; ?>
@section('content')
@include('accountings::partials.nav')


<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Bills (Accounts Payable)</h4>
        <a href="{{ route('bills.create') }}" class="btn btn-primary btn-sm">New Bill</a>
    </div>

    @if(session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <form class="row g-2 mb-3" method="get" action="{{ route('bills.index') }}">
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All statuses</option>
                        @foreach(['draft','approved','unpaid','partial','paid','void'] as $s)
                            <option value="{{ $s }}" @selected(($status ?? '') === $s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-outline-secondary btn-sm">Filter</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Vendor</th>
                            <th>Bill Date</th>
                            <th>Due</th>
                            <th>Status</th>
                            <th class="text-end">Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bills as $bill)
                            <tr>
                                <td>
                                    <a href="{{ route('bills.show', $bill->id) }}">{{ $bill->bill_number ?: ('BILL-'.$bill->id) }}</a>
                                </td>
                                <td>{{ $bill->vendor->name ?? '—' }}</td>
                                <td>{{ optional($bill->bill_date)->format('Y-m-d') }}</td>
                                <td>{{ optional($bill->due_date)->format('Y-m-d') }}</td>
                                <td><span class="badge bg-light text-dark">{{ $bill->status }}</span></td>
                                <td class="text-end">{{ number_format((float)$bill->total,2) }}</td>
                                <td class="text-end">
                                    <form method="post" action="{{ route('bills.destroy', $bill->id) }}" onsubmit="return confirm('Delete bill?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-muted">No bills yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $bills->links() }}
        </div>
    </div>
</div>

@endsection
