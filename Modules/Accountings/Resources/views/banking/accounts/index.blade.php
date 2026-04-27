@extends('layouts.app')

@section('content')
@include('accountings::partials.nav')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Bank Accounts</h4>
        <a href="{{ route('bank-accounts.create') }}" class="btn btn-primary">Add Bank Account</a>
    </div>

    <div class="card">
        <div class="card-body">
            @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Institution</th>
                        <th>Currency</th>
                        <th>Last4</th>
                        <th>BSB</th>
                        <th width="90">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($accounts as $a)
                        <tr>
                            <td>{{ $a->name }}</td>
                            <td>{{ $a->institution }}</td>
                            <td>{{ $a->currency }}</td>
                            <td>{{ $a->account_number_last4 }}</td>
                            <td>{{ $a->bsb }}</td>
                            <td>
                                <form method="POST" action="{{ route('bank-accounts.destroy', $a->id) }}" onsubmit="return confirm('Delete this bank account?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">No bank accounts yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
