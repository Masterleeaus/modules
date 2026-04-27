@php($pageTitle = $pageTitle ?? __('accountings::app.menu.accounting'))
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-3">
        <h4 class="mb-0">Cashflow Setup</h4>
        <small class="text-muted">Set your expected monthly inflow/outflow and recurring expenses for forecasting.</small>
    </div>

    @if(session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card"><div class="card-body">
                <h6>Active Cashflow Budget</h6>
                <form method="POST" action="{{ route('acc-settings.cashflow.save') }}">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label">Budget name</label>
                        <input class="form-control" name="budget_name" value="{{ $budget->name ?? '' }}" placeholder="e.g. Tradie monthly baseline">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Expected monthly inflow</label>
                        <input class="form-control" name="expected_monthly_inflow" value="{{ $budget->expected_monthly_inflow ?? '' }}" placeholder="e.g. 45000">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Expected monthly outflow</label>
                        <input class="form-control" name="expected_monthly_outflow" value="{{ $budget->expected_monthly_outflow ?? '' }}" placeholder="e.g. 32000">
                    </div>
                    <button class="btn btn-primary btn-sm">Save</button>
                </form>
            </div></div>
        </div>

        <div class="col-lg-7">
            <div class="card"><div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Recurring Expenses</h6>
                </div>

                <form class="row g-2 mt-2" method="POST" action="{{ route('acc-settings.cashflow.recurring.add') }}">
                    @csrf
                    <div class="col-md-5"><input class="form-control form-control-sm" name="name" placeholder="e.g. Vehicle lease" required></div>
                    <div class="col-md-3"><input class="form-control form-control-sm" name="amount" placeholder="Amount" required></div>
                    <div class="col-md-3">
                        <select class="form-select form-select-sm" name="frequency" required>
                            <option value="weekly">Weekly</option>
                            <option value="monthly" selected>Monthly</option>
                            <option value="quarterly">Quarterly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div>
                    <div class="col-md-1 d-grid"><button class="btn btn-sm btn-outline-primary">+</button></div>
                </form>

                <div class="table-responsive mt-3">
                    <table class="table table-sm">
                        <thead><tr><th>Name</th><th class="text-end">Amount</th><th>Freq</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                        @forelse($recurrings as $r)
                            <tr>
                                <td>{{ $r->name }}</td>
                                <td class="text-end">{{ number_format($r->amount,2) }}</td>
                                <td>{{ ucfirst($r->frequency) }}</td>
                                <td>
                                    @if($r->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Paused</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('acc-settings.cashflow.recurring.toggle', $r->id) }}">
                                        @if($r->is_active) Pause @else Activate @endif
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-muted">No recurring expenses yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div></div>
        </div>
    </div>
</div>
@endsection
