@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header">
                    <h4 class="mb-0">Receive Goods — PO #{{ $order->id }}
                        @if ($order->reference) ({{ $order->reference }}) @endif
                    </h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Supplier:</strong> {{ optional($order->supplier)->name ?? '—' }}
                        &nbsp;|&nbsp;
                        <strong>Status:</strong> {{ ucfirst($order->status) }}
                    </div>

                    <form method="POST" action="{{ route('supplychain.purchasing.receive', $order) }}">
                        @csrf
                        <div class="form-group">
                            <label>Receiving Warehouse <span class="text-danger">*</span></label>
                            <select name="warehouse_id" class="form-control" required>
                                <option value="">— Select Warehouse —</option>
                                @foreach ($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <table class="table table-bordered mt-3">
                            <thead class="thead-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Ordered</th>
                                    <th>Qty to Receive</th>
                                    <th>Unit Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $i => $poi)
                                <input type="hidden" name="items[{{ $i }}][po_item_id]" value="{{ $poi->id }}">
                                <tr>
                                    <td>{{ optional($poi->item)->name ?? 'Item #'.$poi->item_id }}</td>
                                    <td>{{ $poi->qty_ordered }}</td>
                                    <td>
                                        <input type="number" name="items[{{ $i }}][qty_received]"
                                               step="0.0001" min="0.0001" max="{{ $poi->qty_ordered }}"
                                               class="form-control" value="{{ $poi->qty_ordered }}" required>
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $i }}][unit_cost]"
                                               step="0.01" min="0" class="form-control"
                                               value="{{ $poi->unit_cost }}" required>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="d-flex justify-content-between mt-3">
                            <a href="{{ route('supplychain.purchasing.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-inbox"></i> Confirm Receipt
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
