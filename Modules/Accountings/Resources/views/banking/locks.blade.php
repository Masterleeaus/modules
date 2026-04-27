@extends('layouts.app')
@section('content')
@include('accountings::partials.nav')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Period Locks</h4>
        <a href="{{ route('accountings.banking.import') }}" class="btn btn-outline-secondary btn-sm">← Banking Import</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-header">Add Period Lock</div>
        <div class="card-body">
            <p class="text-muted small mb-2">Locking a date prevents new entries on or before that date from being posted.</p>
            <form method="POST" action="{{ route('accountings.period-locks.store') }}">
                @csrf
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small">Lock Date</label>
                        <input type="date" name="lock_date" class="form-control form-control-sm" required value="{{ old('lock_date') }}">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small">Reason (optional)</label>
                        <input type="text" name="reason" class="form-control form-control-sm" maxlength="500" value="{{ old('reason') }}" placeholder="e.g. Q1 2026 closed">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-warning btn-sm w-100">Lock Period</button>
                    </div>
                </div>
                @error('lock_date')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Active Period Locks</div>
        <div class="card-body p-0">
            <table class="table table-sm table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Lock Date</th>
                        <th>Locked By</th>
                        <th>Reason</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($locks as $lock)
                        <tr>
                            <td><strong>{{ $lock->lock_date instanceof \Carbon\Carbon ? $lock->lock_date->format('d M Y') : $lock->lock_date }}</strong></td>
                            <td>{{ optional($lock->lockedByUser)->name ?? "User #{$lock->locked_by}" }}</td>
                            <td>{{ $lock->reason ?: '—' }}</td>
                            <td>{{ $lock->created_at ? \Carbon\Carbon::parse($lock->created_at)->format('d M Y H:i') : '' }}</td>
                            <td>
                                <form method="POST" action="{{ route('accountings.period-locks.destroy', $lock->id) }}" onsubmit="return confirm('Remove this period lock?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm btn-xs">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted">No period locks set.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
