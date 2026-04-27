@extends('layouts.app')
@section('content')
@include('accountings::partials.nav')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Banking — CSV Import</h4>
        <a href="{{ route('accountings.period-locks.index') }}" class="btn btn-outline-secondary btn-sm">Period Locks</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <div class="card mb-4">
        <div class="card-header">Import CSV</div>
        <div class="card-body">
            <form method="POST" action="{{ route('accountings.banking.upload') }}" enctype="multipart/form-data">
                @csrf
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small">Bank Account ID</label>
                        <input type="number" name="bank_account_id" class="form-control form-control-sm" required placeholder="e.g. 1">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small">CSV File</label>
                        <input type="file" name="csv_file" class="form-control form-control-sm" accept=".csv,.txt" required>
                        <small class="text-muted">Columns: date, description, amount, balance (optional), reference (optional)</small>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary btn-sm w-100">Import</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <span>Recent Transactions (latest 200)</span>
        </div>
        <div class="card-body p-0">
            <table class="table table-sm table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th class="text-end">Amount</th>
                        <th class="text-end">Balance</th>
                        <th>Reference</th>
                        <th>Matched</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                        <tr>
                            <td>{{ $tx->txn_date ?? $tx->transaction_date ?? '' }}</td>
                            <td>{{ $tx->description }}</td>
                            <td class="text-end {{ ($tx->amount ?? 0) < 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format($tx->amount ?? 0, 2) }}
                            </td>
                            <td class="text-end">{{ $tx->balance !== null ? number_format($tx->balance, 2) : '—' }}</td>
                            <td>{{ $tx->reference ?? '—' }}</td>
                            <td>
                                @if($tx->matched_type)
                                    <span class="badge bg-success">{{ $tx->matched_type }} #{{ $tx->matched_id }}</span>
                                @else
                                    <span class="badge bg-secondary">Unmatched</span>
                                @endif
                            </td>
                            <td>
                                @if(! $tx->matched_type)
                                    <form method="POST" action="{{ route('accountings.banking.match') }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="transaction_id" value="{{ $tx->id }}">
                                        <select name="matched_type" class="form-select form-select-sm d-inline-block" style="width:90px">
                                            <option value="invoice">Invoice</option>
                                            <option value="bill">Bill</option>
                                            <option value="expense">Expense</option>
                                        </select>
                                        <input type="number" name="matched_id" placeholder="ID" class="form-control form-control-sm d-inline-block" style="width:70px" required>
                                        <button class="btn btn-xs btn-outline-primary btn-sm">Match</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">No transactions imported yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
