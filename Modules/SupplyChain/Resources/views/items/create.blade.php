@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header"><h4 class="mb-0">Add Inventory Item</h4></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('supplychain.items.store') }}">
                        @csrf
                        @if ($fieldItems->isNotEmpty())
                        <div class="form-group">
                            <label>Link to FieldItems Catalogue</label>
                            <select name="field_item_id" class="form-control">
                                <option value="">— None (manual entry) —</option>
                                @foreach ($fieldItems as $fi)
                                <option value="{{ $fi->id }}" {{ old('field_item_id') == $fi->id ? 'selected' : '' }}>
                                    {{ $fi->name }}{{ $fi->sku ? ' ('.$fi->sku.')' : '' }}
                                </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Linking to a FieldItems record uses the catalogue as source-of-truth.</small>
                        </div>
                        @endif
                        <div class="form-group">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>SKU</label>
                            <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror"
                                   value="{{ old('sku') }}">
                            @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Unit</label>
                            <input type="text" name="unit" class="form-control" value="{{ old('unit') }}" placeholder="e.g. each, litre, kg">
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('supplychain.items.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Item</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
