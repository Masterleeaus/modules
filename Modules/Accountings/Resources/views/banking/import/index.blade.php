@extends('layouts.app')

@section('content')
@include('accountings::partials.nav')

<div class="container-fluid">
    <h4 class="mb-3">Banking Import (CSV)</h4>

    <div class="card mb-3">
        <div class="card-body">
            @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
            <form method="POST" action="{{ route('banking.import.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Bank account</label>
                        <select name="bank_account_id" class="form-control" required>
                            <option value="">Select...</option>
                            @foreach($accounts as $a)
                                <option value="{{ $a->id }}">{{ $a->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5 mb-2">
                        <label class="form-label">CSV file</label>
                        <input type="file" name="csv_file" class="form-control" required>
                        <small class="text-muted">Headers supported: date, description, amount, balance, reference</small>
                    </div>
                    <div class="col-md-3 mb-2 d-flex align-items-end">
                        <button class="btn btn-primary w-100">Import</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h6 class="mb-2">Recent Transactions (latest 200)</h6>
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Balance</th>
                        <th>Matched</th>
                        <th>Batch</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $t)
                    <tr>
                        <td>{{ optional($t->txn_date)->format('Y-m-d') }}</td>
                        <td>{{ $t->description }}</td>
                        <td>{{ number_format($t->amount,2) }}</td>
                        <td>{{ $t->balance !== null ? number_format($t->balance,2) : '' }}</td>
                        <td>{{ $t->matched_type ? ($t->matched_type.' #'.$t->matched_id) : '' }}</td>
                        <td>{{ $t->import_batch }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted">No transactions yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
