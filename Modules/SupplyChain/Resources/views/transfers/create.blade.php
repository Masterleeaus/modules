@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header"><h4 class="mb-0">New Transfer</h4></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('supplychain.transfers.store') }}">
                        @csrf
                        <div class="form-group">
                            <label>Item <span class="text-danger">*</span></label>
                            <select name="item_id" class="form-control" required>
                                <option value="">— Select Item —</option>
                                @foreach ($items as $item)
                                <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>From Warehouse <span class="text-danger">*</span></label>
                            <select name="from_warehouse_id" class="form-control" required>
                                <option value="">— Select Source —</option>
                                @foreach ($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ old('from_warehouse_id') == $wh->id ? 'selected' : '' }}>
                                    {{ $wh->name }} ({{ ucfirst($wh->type ?? 'depot') }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>To Warehouse <span class="text-danger">*</span></label>
                            <select name="to_warehouse_id" class="form-control" required>
                                <option value="">— Select Destination —</option>
                                @foreach ($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ old('to_warehouse_id') == $wh->id ? 'selected' : '' }}>
                                    {{ $wh->name }} ({{ ucfirst($wh->type ?? 'depot') }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" step="0.0001" min="0.0001" class="form-control"
                                   value="{{ old('quantity') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Note</label>
                            <textarea name="note" class="form-control" rows="2">{{ old('note') }}</textarea>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('supplychain.transfers.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Transfer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
