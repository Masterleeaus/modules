@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h3">{{ __('supplychain::labels.warehouses') }}</h1>
                @can('supplychain.manage')
                <a href="{{ route('supplychain.warehouses.create') }}" class="btn btn-primary">
                    <i class="fa fa-plus"></i> Add Warehouse
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
                                <th>Code</th>
                                <th>Type</th>
                                <th>Address</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($warehouses as $warehouse)
                            <tr>
                                <td>{{ $warehouse->id }}</td>
                                <td>{{ $warehouse->name }}</td>
                                <td>{{ $warehouse->code ?? '—' }}</td>
                                <td>
                                    <span class="badge badge-{{ $warehouse->type === 'depot' ? 'primary' : ($warehouse->type === 'van' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($warehouse->type ?? 'depot') }}
                                    </span>
                                </td>
                                <td>{{ $warehouse->address ?? '—' }}</td>
                                <td>
                                    @if ($warehouse->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @can('supplychain.manage')
                                    <a href="{{ route('supplychain.warehouses.edit', $warehouse) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('supplychain.warehouses.destroy', $warehouse) }}" class="d-inline"
                                          onsubmit="return confirm('Delete this warehouse?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No warehouses found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($warehouses->hasPages())
                <div class="card-footer">
                    {{ $warehouses->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
