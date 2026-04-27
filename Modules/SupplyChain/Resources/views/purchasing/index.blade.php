@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">{{ __('supplychain::labels.purchase_orders') }}</h1>
        @can('supplychain.purchasing.manage')
        <a href="{{ route('supplychain.purchasing.create') }}" class="btn btn-primary">
            <i class="fa fa-plus"></i> New Purchase Order
        </a>
        @endcan
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card shadow">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Reference</th>
                        <th>Supplier</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Ordered At</th>
                        <th>Expected Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->reference ?? 'PO-'.$order->id }}</td>
                        <td>{{ optional($order->supplier)->name ?? '—' }}</td>
                        <td>
                            @php
                                $s = $order->status;
                                $badge = ['draft'=>'secondary','ordered'=>'info','received'=>'success','cancelled'=>'danger'][$s] ?? 'light';
                            @endphp
                            <span class="badge badge-{{ $badge }}">{{ ucfirst($s) }}</span>
                        </td>
                        <td>{{ number_format($order->total, 2) }} {{ $order->currency }}</td>
                        <td>{{ optional($order->ordered_at)->format('d M Y') ?? '—' }}</td>
                        <td>{{ $order->expected_date ? \Carbon\Carbon::parse($order->expected_date)->format('d M Y') : '—' }}</td>
                        <td>
                            @can('supplychain.purchasing.manage')
                            @if ($order->status !== 'received')
                            <a href="{{ route('supplychain.purchasing.receive.form', $order) }}" class="btn btn-sm btn-success">
                                <i class="fa fa-inbox"></i> Receive
                            </a>
                            @endif
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No purchase orders found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($orders->hasPages())
        <div class="card-footer">{{ $orders->links() }}</div>
        @endif
    </div>
</div>
@endsection
