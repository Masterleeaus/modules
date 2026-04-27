@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">{{ __('supplychain::labels.stock') }}</h1>
        @can('supplychain.manage')
        <a href="{{ route('supplychain.items.create') }}" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add Item
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
                        <th>Name</th>
                        <th>SKU</th>
                        <th>Unit</th>
                        <th>FieldItem Link</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->sku ?? '—' }}</td>
                        <td>{{ $item->unit ?? '—' }}</td>
                        <td>{{ $item->field_item_id ? '#'.$item->field_item_id : '—' }}</td>
                        <td>
                            @can('supplychain.manage')
                            <a href="{{ route('supplychain.items.edit', $item) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fa fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('supplychain.items.destroy', $item) }}" class="d-inline"
                                  onsubmit="return confirm('Delete this item?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No items found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($items->hasPages())
        <div class="card-footer">{{ $items->links() }}</div>
        @endif
    </div>
</div>
@endsection
