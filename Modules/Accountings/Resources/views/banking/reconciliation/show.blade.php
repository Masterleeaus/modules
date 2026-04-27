@extends('layouts.app')

@section('content')
@include('accountings::partials.nav')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4>Reconciliation #{{ $rec->id }} — {{ $account->name }}</h4>
            <div class="text-muted">{{ $rec->from_date->format('Y-m-d') }} → {{ $rec->to_date->format('Y-m-d') }}</div>
        </div>
        <div>
            @if($rec->status !== 'closed')
                <a href="{{ route('banking.reconciliation.close',$rec->id) }}"
                   class="btn btn-success"
                   onclick="return confirm('Close reconciliation? This will lock it.');">Close</a>
            @endif
            <a href="{{ route('banking.reconciliation.index') }}" class="btn btn-light">Back</a>
        </div>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h6>Summary</h6>
                    <div>Opening: <strong>{{ number_format($rec->opening_balance,2) }}</strong></div>
                    <div>Closing: <strong>{{ number_format($rec->closing_balance,2) }}</strong></div>
                    <div>Matched total: <strong>{{ number_format($rec->matched_total,2) }}</strong></div>
                    <div>Difference: <strong>{{ number_format($rec->difference,2) }}</strong></div>
                    <div>Status: <strong>{{ $rec->status }}</strong></div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h6>Selected transactions</h6>
                    <table class="table table-sm table-bordered">
                        <thead><tr><th>Date</th><th>Desc</th><th>Amt</th><th></th></tr></thead>
                        <tbody>
                        @php
                            $reconciliationLinesByTransactionId = collect();

                            try {
                                $reconciliationLinesByTransactionId = \Modules\Accountings\Entities\BankReconciliationLine::where('reconciliation_id', $rec->id)
                                    ->whereIn('bank_transaction_id', $selected->pluck('id'))
                                    ->get()
                                    ->keyBy('bank_transaction_id');
                            } catch (\Illuminate\Database\QueryException $e) {
                                // Table may not exist yet
                            }
                        @endphp
                        @forelse($selected as $t)
                            @php
                                $line = $reconciliationLinesByTransactionId->get($t->id);
                            @endphp
                            <tr>
                                <td>{{ $t->txn_date->format('Y-m-d') }}</td>
                                <td>{{ $t->description }}</td>
                                <td>{{ number_format($t->amount,2) }}</td>
                                <td>
                                    @if($rec->status !== 'closed' && $line)
                                    <form method="POST" action="{{ route('banking.reconciliation.lines.remove', [$rec->id, $line->id]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">x</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted">No selected transactions.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h6>Available transactions (add to reconciliation)</h6>
                    <table class="table table-sm table-bordered">
                        <thead><tr><th>Date</th><th>Description</th><th>Amount</th><th>Matched</th><th></th></tr></thead>
                        <tbody>
                        @forelse($available as $t)
                            <tr>
                                <td>{{ $t->txn_date->format('Y-m-d') }}</td>
                                <td>{{ $t->description }}</td>
                                <td>{{ number_format($t->amount,2) }}</td>
                                <td>{{ $t->matched_type ? ($t->matched_type.' #'.$t->matched_id) : '' }}</td>
                                <td>
                                    @if($rec->status !== 'closed' && !in_array($t->id, $txnIds))
                                    <form method="POST" action="{{ route('banking.reconciliation.lines.add', $rec->id) }}">
                                        @csrf
                                        <input type="hidden" name="bank_transaction_id" value="{{ $t->id }}">
                                        <button class="btn btn-sm btn-primary">Add</button>
                                    </form>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">No transactions in this period.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
