@extends('layouts.app')

@section('title', 'Edit Equipment')

@section('content')
    <section class="content-header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">Edit Equipment</h1>
            <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>
    </section>

    <section class="content">
        <div class="px-4 asset-ui-padding-wrap">
        <div class="card">
            <div class="card-body">
                <form id="asset_form" method="POST" action="{{ route('assets.update', $asset->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Equipment Name *</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $asset->name) }}" class="form-control" required>
                                @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="asset_type_id">Equipment Type *</label>
                                <button type="button" class="btn btn-sm btn-outline-primary ml-2" onclick="openRightModal('{{ route('asset-type.create') }}')">
                                    <i class="fa fa-plus"></i> Quick add
                                </button>
                                <a href="javascript:;" class="btn btn-sm btn-outline-primary ms-2" onclick="openRightModal('{{ route('asset-type.create') }}')">
                                    <i class="fa fa-plus"></i> Quick add
                                <select name="asset_type_id" id="asset_type_id" class="form-control" required>
                                    <option value="">Select</option>
                                    @foreach(($assetType ?? []) as $type)
                                        <option value="{{ $type->id }}" @selected(old('asset_type_id', $asset->asset_type_id)==$type->id)>
                                            {{ $type->type ?? $type->name ?? ('#'.$type->id) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('asset_type_id')<small class="text-danger">{{ $message }}</small>@enderror
                                <small class="form-text text-muted">
                                </small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="serial_number">Serial Number (optional)</label>
                                <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number', $asset->serial_number) }}" class="form-control">
                                @error('serial_number')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status *</label>
                                <select name="status" id="status" class="form-control" required>
                                    @php($st = old('status', $asset->status ?? 'available'))
                                    <option value="available" @selected($st==='available')>Available</option>
                                    <option value="lent" @selected($st==='lent')>Lent</option>
                                    <option value="non-functional" @selected($st==='non-functional')>Non-functional</option>
                                </select>
                                @error('status')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="value">Value (optional)</label>
                                <input type="text" name="value" id="value" value="{{ old('value', $asset->value) }}" class="form-control">
                                @error('value')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="location">Location (optional)</label>
                                <input type="text" name="location" id="location" value="{{ old('location', $asset->location) }}" class="form-control">
                                @error('location')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description">Description (optional)</label>
                                <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $asset->description) }}</textarea>
                                @error('description')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="image">Photo (optional)</label>
                                <input type="file" name="image" id="image" class="form-control">
                                @error('image')<small class="text-danger">{{ $message }}</small>@enderror
                                @if(!empty($asset->image))
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" value="yes" id="image_delete" name="image_delete">
                                        <label class="form-check-label" for="image_delete">Remove current photo</label>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Update Equipment
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>


@push('scripts')
<script>
    // When the right modal closes after creating a type, reload so the new type shows up everywhere.
    // (Keeps it compatible with the platform's modal system.)
    (function () {
        // Many Worksuite themes trigger this custom event; if not present, no harm.
        document.addEventListener('rightModalClosed', function () {
            // Only reload if we just created a type (flag set by modal response JS if available)
            if (window.__assetTypeCreated) {
                window.__assetTypeCreated = false;
                window.location.reload();
            }
        });
    })();
</script>
@endpush

@endsection