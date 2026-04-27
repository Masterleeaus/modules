@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">{{ __('supplychain::labels.movements') }}</h1>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @can('supplychain.manage')
    <div class="card shadow mb-4">
        <div class="card-header">Record Movement</div>
        <div class="card-body">
            <form method="POST" action="{{ route('supplychain.movements.store') }}" class="row g-2">
                @csrf
                <div class="col-md-3">
                    <select name="warehouse_id" class="form-control" required>
                        <option value="">— Warehouse —</option>
                        @foreach (\Modules\SupplyChain\Entities\Warehouse::orderBy('name')->get() as $wh)
                        <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="item_id" class="form-control" required>
                        <option value="">— Item —</option>
                        @foreach (\Modules\SupplyChain\Entities\Item::orderBy('name')->get() as $itm)
                        <option value="{{ $itm->id }}">{{ $itm->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" name="quantity" step="0.0001" min="0.0001" class="form-control" placeholder="Qty" required>
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-control" required>
                        <option value="in">In</option>
                        <option value="out">Out</option>
                        <option value="adjust">Adjust</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="text" name="note" class="form-control" placeholder="Note (optional)">
                </div>
                <div class="col-12 mt-2">
                    <button class="btn btn-primary">Record</button>
                </div>
            </form>
        </div>
    </div>
    @endcan

    <div class="card shadow">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Warehouse</th>
                        <th>Item</th>
                        <th>Type</th>
                        <th>Qty</th>
                        <th>User</th>
                        <th>Note</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($movements as $mv)
                    <tr>
                        <td>{{ $mv->id }}</td>
                        <td>{{ optional($mv->warehouse)->name ?? '—' }}</td>
                        <td>{{ optional($mv->item)->name ?? '—' }}</td>
                        <td>
                            <span class="badge badge-{{ $mv->type === 'in' ? 'success' : ($mv->type === 'out' ? 'danger' : 'secondary') }}">
                                {{ strtoupper($mv->type) }}
                            </span>
                        </td>
                        <td>{{ $mv->quantity }}</td>
                        <td>{{ optional($mv->user)->name ?? '—' }}</td>
                        <td>{{ $mv->note ?? '—' }}</td>
                        <td>{{ $mv->created_at->format('d M Y H:i') }}</td>
                        <td>
                            @can('supplychain.manage')
                            <form method="POST" action="{{ route('supplychain.movements.destroy', $mv) }}" class="d-inline"
                                  onsubmit="return confirm('Delete movement?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">No movements recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($movements->hasPages())
        <div class="card-footer">{{ $movements->links() }}</div>
        @endif
    </div>
</div>
@endsection
