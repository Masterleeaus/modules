@extends('layouts.app')

@section('content')
@include('accountings::partials.nav')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Bank Reconciliation</h4>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h6>Create reconciliation</h6>
            <form method="POST" action="{{ route('banking.reconciliation.store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Bank account</label>
                        <select name="bank_account_id" class="form-control" required>
                            <option value="">Select...</option>
                            @foreach($accounts as $a)
                                <option value="{{ $a->id }}">{{ $a->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="form-label">From</label>
                        <input type="date" name="from_date" class="form-control" required>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="form-label">To</label>
                        <input type="date" name="to_date" class="form-control" required>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="form-label">Opening</label>
                        <input type="number" step="0.01" name="opening_balance" class="form-control" required>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="form-label">Closing</label>
                        <input type="number" step="0.01" name="closing_balance" class="form-control" required>
                    </div>
                    <div class="col-md-1 mb-2 d-flex align-items-end">
                        <button class="btn btn-primary w-100">Create</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Account</th>
                        <th>Period</th>
                        <th>Status</th>
                        <th>Matched</th>
                        <th>Difference</th>
                        <th width="90">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recs as $r)
                        <tr>
                            <td>{{ $r->id }}</td>
                            <td>{{ optional($r->bankAccount)->name }}</td>
                            <td>{{ $r->from_date->format('Y-m-d') }} → {{ $r->to_date->format('Y-m-d') }}</td>
                            <td>{{ $r->status }}</td>
                            <td>{{ number_format($r->matched_total,2) }}</td>
                            <td>{{ number_format($r->difference,2) }}</td>
                            <td>
                                <a class="btn btn-sm btn-primary" href="{{ route('banking.reconciliation.show',$r->id) }}">Open</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">No reconciliations yet.</td></tr>
                    @endforelse
                </tbody>
            </table>

            {{ $recs->links() }}
        </div>
    </div>
</div>
@endsection
