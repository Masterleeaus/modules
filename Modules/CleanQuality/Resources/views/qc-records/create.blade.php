@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fa fa-plus-circle mr-1"></i>@lang('quality_control::app.new_qc_record')</h4>
                <a href="{{ route('qc-records.index') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left mr-1"></i>@lang('app.back')
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('qc-records.store') }}" id="qcRecordForm">
                    @csrf

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('quality_control::app.booking_id')</label>
                                <input type="text" name="booking_id" class="form-control" value="{{ old('booking_id') }}" maxlength="36">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('quality_control::app.template')</label>
                                <select name="template_id" class="form-control">
                                    <option value="">— @lang('quality_control::app.select_template') —</option>
                                    @foreach($templates as $tpl)
                                        <option value="{{ $tpl->id }}" {{ old('template_id') == $tpl->id ? 'selected' : '' }}>{{ $tpl->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('quality_control::app.cleaner')</label>
                                <input type="number" name="cleaner_id" class="form-control" value="{{ old('cleaner_id') }}" min="1" placeholder="User ID">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('quality_control::app.inspected_at')</label>
                                <input type="datetime-local" name="inspected_at" class="form-control" value="{{ old('inspected_at', now()->format('Y-m-d\TH:i')) }}">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>@lang('app.notes')</label>
                                <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h6 class="mb-3"><i class="fa fa-list mr-1"></i>@lang('quality_control::app.checklist_items')</h6>

                    <div id="itemsContainer">
                        <div class="row item-row mb-2">
                            <div class="col-md-4">
                                <input type="text" name="items[0][item_label]" class="form-control" placeholder="@lang('quality_control::app.item_label')" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="items[0][score]" class="form-control" placeholder="@lang('quality_control::app.score') (0-100)" min="0" max="100" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="items[0][weight]" class="form-control" placeholder="@lang('quality_control::app.weight') %" min="0" max="100">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="items[0][notes]" class="form-control" placeholder="@lang('app.notes')">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-outline-danger remove-item"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                    </div>

                    <button type="button" id="addItem" class="btn btn-outline-secondary btn-sm mb-3">
                        <i class="fa fa-plus mr-1"></i>@lang('quality_control::app.add_item')
                    </button>

                    <hr>
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('qc-records.index') }}" class="btn btn-outline-secondary mr-2">@lang('app.cancel')</a>
                        <button type="submit" class="btn btn-primary">@lang('app.save')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let itemIndex = 1;
    document.getElementById('addItem').addEventListener('click', function () {
        const container = document.getElementById('itemsContainer');
        const row = document.createElement('div');
        row.classList.add('row', 'item-row', 'mb-2');
        row.innerHTML = `
            <div class="col-md-4"><input type="text" name="items[${itemIndex}][item_label]" class="form-control" placeholder="{{ __('quality_control::app.item_label') }}" required></div>
            <div class="col-md-2"><input type="number" name="items[${itemIndex}][score]" class="form-control" placeholder="{{ __('quality_control::app.score') }} (0-100)" min="0" max="100" required></div>
            <div class="col-md-2"><input type="number" name="items[${itemIndex}][weight]" class="form-control" placeholder="{{ __('quality_control::app.weight') }} %" min="0" max="100"></div>
            <div class="col-md-3"><input type="text" name="items[${itemIndex}][notes]" class="form-control" placeholder="{{ __('app.notes') }}"></div>
            <div class="col-md-1"><button type="button" class="btn btn-outline-danger remove-item"><i class="fa fa-times"></i></button></div>
        `;
        container.appendChild(row);
        itemIndex++;
    });

    document.addEventListener('click', function (e) {
        if (e.target.closest('.remove-item')) {
            const row = e.target.closest('.item-row');
            if (document.querySelectorAll('.item-row').length > 1) {
                row.remove();
            }
        }
    });
</script>
@endpush
