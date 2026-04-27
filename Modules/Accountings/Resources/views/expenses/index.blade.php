@extends('layouts.app')
<?php $pageTitle = $pageTitle ?? 'Expenses'; ?>
@section('content')
@include('accountings::partials.nav')


<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Expenses</h4>
        <a href="{{ route('expenses.create') }}" class="btn btn-primary btn-sm">New Expense</a>
    </div>

    @if(session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <form class="row g-2 mb-3" method="get" action="{{ route('expenses.index') }}">
                <div class="col-md-3">
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm" placeholder="From" />
                </div>
                <div class="col-md-3">
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm" placeholder="To" />
                </div>
                <div class="col-md-3">
                    <button class="btn btn-outline-secondary btn-sm">Filter</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Vendor</th>
                            <th>Description</th>
                            <th>Job Ref</th>
                            <th class="text-end">Amount</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $e)
                            <tr>
                                <td><a href="{{ route('expenses.show', $e->id) }}">{{ optional($e->expense_date)->format('Y-m-d') }}</a></td>
                                <td>{{ $e->vendor->name ?? '—' }}</td>
                                <td>{{ $e->description }}</td>
                                <td>{{ $e->job_ref ?: '—' }}</td>
                                <td class="text-end">{{ number_format((float)$e->amount,2) }}</td>
                                <td class="text-end">
                                    <form method="post" action="{{ route('expenses.destroy', $e->id) }}" onsubmit="return confirm('Delete expense?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-muted">No expenses yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $expenses->links() }}
        </div>
    </div>
</div>

@endsection