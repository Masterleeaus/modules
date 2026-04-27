@php($pageTitle = $pageTitle ?? __('accountings::app.menu.accounting') . ' - Audit')
@extends('layouts.app')

@section('content')
@include('accountings::partials.nav')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Audit Log</h4>
            <div class="text-muted">Tracks key accounting actions (bills, payments, imports, reconciliations, settings).</div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-md-4">
                    <label class="form-label">Action</label>
                    <select name="action" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach($actions as $a)
                            <option value="{{ $a }}" @selected(request('action')===$a)>{{ $a }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">From</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
                    <label class="form-label">To</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-sm btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 170px;">When</th>
                            <th style="width: 160px;">Action</th>
                            <th style="width: 120px;">User</th>
                            <th style="width: 120px;">Company</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td>{{ $log->created_at?->format('Y-m-d H:i') }}</td>
                                <td><code>{{ $log->action }}</code></td>
                                <td>{{ $log->user_id }}</td>
                                <td>{{ $log->company_id }}</td>
                                <td class="text-muted">{{ $log->message }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center p-4 text-muted">No audit entries.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
