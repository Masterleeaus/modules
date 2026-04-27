@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header"><h4 class="mb-0">Create Purchase Order</h4></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('supplychain.purchasing.store') }}" id="po-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Supplier <span class="text-danger">*</span></label>
                                    <select name="supplier_id" class="form-control" required>
                                        <option value="">— Select Supplier —</option>
                                        @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Reference</label>
                                    <input type="text" name="reference" class="form-control" value="{{ old('reference') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Currency</label>
                                    <input type="text" name="currency" class="form-control" value="{{ old('currency', 'AUD') }}" maxlength="3">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Expected Date</label>
                                    <input type="date" name="expected_date" class="form-control" value="{{ old('expected_date') }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Notes</label>
                                    <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-3">Items</h5>
                        <div id="po-items">
                            <div class="row po-item-row mb-2">
                                <div class="col-md-5">
                                    <select name="items[0][item_id]" class="form-control" required>
                                        <option value="">— Select Item —</option>
                                        @foreach ($items as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" name="items[0][qty_ordered]" step="0.0001" min="0.0001"
                                           class="form-control" placeholder="Qty" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" name="items[0][unit_cost]" step="0.01" min="0"
                                           class="form-control" placeholder="Unit Cost" required>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="add-item" class="btn btn-sm btn-outline-secondary mb-3">
                            <i class="fa fa-plus"></i> Add Item
                        </button>

                        <script id="po-items-data" type="application/json">
                            {!! json_encode($items->map(fn($i) => ['id' => $i->id, 'name' => $i->name])) !!}
                        </script>

                        <div class="d-flex justify-content-between mt-2">
                            <a href="{{ route('supplychain.purchasing.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Purchase Order</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var idx = 1;
    var itemsData = document.getElementById('po-items-data');
    var items = JSON.parse(itemsData.textContent || itemsData.innerText);
    document.getElementById('add-item').addEventListener('click', function() {
        var select = document.createElement('select');
        select.name = 'items[' + idx + '][item_id]';
        select.className = 'form-control';
        select.required = true;
        var defaultOpt = document.createElement('option');
        defaultOpt.value = '';
        defaultOpt.textContent = '— Item —';
        select.appendChild(defaultOpt);
        items.forEach(function(item) {
            var opt = document.createElement('option');
            opt.value = item.id;
            opt.textContent = item.name;
            select.appendChild(opt);
        });

        var row = document.createElement('div');
        row.className = 'row po-item-row mb-2';

        var col1 = document.createElement('div'); col1.className = 'col-md-5'; col1.appendChild(select);
        var col2 = document.createElement('div'); col2.className = 'col-md-3';
        var qtyInput = document.createElement('input');
        qtyInput.type = 'number'; qtyInput.name = 'items[' + idx + '][qty_ordered]';
        qtyInput.step = '0.0001'; qtyInput.min = '0.0001'; qtyInput.className = 'form-control';
        qtyInput.placeholder = 'Qty'; qtyInput.required = true;
        col2.appendChild(qtyInput);
        var col3 = document.createElement('div'); col3.className = 'col-md-3';
        var costInput = document.createElement('input');
        costInput.type = 'number'; costInput.name = 'items[' + idx + '][unit_cost]';
        costInput.step = '0.01'; costInput.min = '0'; costInput.className = 'form-control';
        costInput.placeholder = 'Unit Cost'; costInput.required = true;
        col3.appendChild(costInput);
        var col4 = document.createElement('div'); col4.className = 'col-md-1';
        var rmBtn = document.createElement('button');
        rmBtn.type = 'button'; rmBtn.className = 'btn btn-sm btn-outline-danger remove-row';
        rmBtn.innerHTML = '<i class="fa fa-times"></i>';
        rmBtn.addEventListener('click', function() { row.remove(); });
        col4.appendChild(rmBtn);

        row.appendChild(col1); row.appendChild(col2); row.appendChild(col3); row.appendChild(col4);
        document.getElementById('po-items').appendChild(row);
        idx++;
    });
})();
</script>
@endsection
