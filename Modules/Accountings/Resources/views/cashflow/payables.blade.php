@php($pageTitle = $pageTitle ?? __('accountings::app.menu.accounting'))
@extends('layouts.app')

@section('content')
@php
use Illuminate\Support\Facades\Route;
@endphp
<div class="container-fluid">
    <div class="mb-3 d-flex justify-content-between align-items-end">
        <div>
            <h4 class="mb-0">Bills To Pay</h4>
            <small class="text-muted">Upcoming expenses (best effort; excludes paid where possible).</small>
        </div>
        <form method="GET" class="d-flex gap-2">
            <select name="bucket" class="form-select form-select-sm" style="width: 240px;">
                <option value="due_14" @if($bucket==='due_14') selected @endif>Due in next 14 days</option>
                <option value="due_30" @if($bucket==='due_30') selected @endif>Due in next 30 days</option>
            </select>
            <button class="btn btn-sm btn-primary">Apply</button>
        </form>
    </div>

    @if($note)
        <div class="alert alert-warning">{{ $note }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Bill</th>
                            <th>Date</th>
                            <th class="text-end">Amount</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $r)
                            @php
                                $expenseUrl = null;
                                if (Route::has('expenses.show')) {
                                    try { $expenseUrl = route('expenses.show', $r['id']); } catch (\Throwable $e) { $expenseUrl = null; }
                                }
                                if (!$expenseUrl) $expenseUrl = url('account/expenses/' . $r['id']);
                            @endphp
                            <tr>
                                <td><a href="{{ $expenseUrl }}">{{ $r['label'] }}</a></td>
                                <td>{{ $r['date'] }}</td>
                                <td class="text-end">{{ number_format($r['amount'],2) }}</td>
                                <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ $expenseUrl }}">Open</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-muted">No records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="text-muted small">Accounting term: Accounts Payable (A/P).</div>
        </div>
    </div>
</div>
@endsection
