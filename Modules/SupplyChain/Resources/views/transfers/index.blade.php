@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">{{ __('supplychain::labels.transfers') }}</h1>
        @can('supplychain.transfer.manage')
        <a href="{{ route('supplychain.transfers.create') }}" class="btn btn-primary">
            <i class="fa fa-plus"></i> New Transfer
        </a>
        @endcan
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Item</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Qty</th>
                        <th>Status</th>
                        <th>Note</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transfers as $transfer)
                    <tr>
                        <td>{{ $transfer->id }}</td>
                        <td>{{ optional($transfer->item)->name ?? '—' }}</td>
                        <td>{{ optional($transfer->from)->name ?? '—' }}</td>
                        <td>{{ optional($transfer->to)->name ?? '—' }}</td>
                        <td>{{ $transfer->quantity }}</td>
                        <td>
                            @php
                                $badges = ['pending'=>'secondary','in_transit'=>'warning','received'=>'success'];
                                $badge = $badges[$transfer->status] ?? 'light';
                            @endphp
                            <span class="badge badge-{{ $badge }}">{{ ucfirst(str_replace('_',' ',$transfer->status)) }}</span>
                        </td>
                        <td>{{ $transfer->note ?? '—' }}</td>
                        <td>
                            @can('supplychain.transfer.manage')
                            @if ($transfer->status === 'pending')
                            <form method="POST" action="{{ route('supplychain.transfers.dispatch', $transfer) }}" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-warning" title="Dispatch (in transit)">
                                    <i class="fa fa-truck"></i> Dispatch
                                </button>
                            </form>
                            @elseif ($transfer->status === 'in_transit')
                            <form method="POST" action="{{ route('supplychain.transfers.receive', $transfer) }}" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-success" title="Mark received">
                                    <i class="fa fa-check"></i> Receive
                                </button>
                            </form>
                            @endif
                            @if (in_array($transfer->status, ['pending']))
                            <form method="POST" action="{{ route('supplychain.transfers.destroy', $transfer) }}" class="d-inline"
                                  onsubmit="return confirm('Delete transfer?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>
                            </form>
                            @endif
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No transfers found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($transfers->hasPages())
        <div class="card-footer">{{ $transfers->links() }}</div>
        @endif
    </div>
</div>
@endsection
