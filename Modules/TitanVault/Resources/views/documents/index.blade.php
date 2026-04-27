@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

@section('filter-section')
    <x-filters.filter-box>
        {{-- Status filter --}}
        <div class="select-box d-flex pr-2 border-right-grey">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('titan_vault::titan_vault.status')</p>
            <select id="filter-status" class="form-control f-14 border-additional-grey" style="min-width:130px;">
                <option value="">@lang('app.all')</option>
                @foreach($statuses as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $s)) }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Reset --}}
        <div class="select-box d-flex py-1 px-lg-2 px-0">
            <x-forms.button-secondary class="btn-xs d-none" id="reset-filters" icon="times-circle">
                @lang('app.clearFilters')
            </x-forms.button-secondary>
        </div>
    </x-filters.filter-box>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="d-grid d-lg-flex justify-content-lg-between align-items-center action-bar">
            <div id="table-actions" class="flex-grow-1 align-items-center">
                <h4 class="f-21 font-weight-normal text-capitalize mb-0 mr-2">
                    @lang('titan_vault::titan_vault.vault_documents')
                </h4>
            </div>

            @if($this->user->permission('add_vault_documents'))
                <div class="btn-group mt-2 mt-lg-0" role="group">
                    <a href="{{ route('titan-vault.documents.create') }}" class="btn btn-primary f-14">
                        <i class="fa fa-plus mr-1"></i> @lang('titan_vault::titan_vault.add_document')
                    </a>
                </div>
            @endif
        </div>

        <div class="d-flex flex-wrap w-100 mt-2">
            <div class="w-100">
                <x-table>
                    <x-slot name="thead">
                        <th>#</th>
                        <th>@lang('titan_vault::titan_vault.title')</th>
                        <th>@lang('titan_vault::titan_vault.status')</th>
                        <th>@lang('titan_vault::titan_vault.version')</th>
                        <th>@lang('titan_vault::titan_vault.created_by')</th>
                        <th>@lang('app.createdAt')</th>
                        <th class="text-right">@lang('app.action')</th>
                    </x-slot>

                    @forelse($documents as $document)
                        <tr>
                            <td>{{ $loop->iteration + ($documents->currentPage() - 1) * $documents->perPage() }}</td>
                            <td>
                                <a href="{{ route('titan-vault.documents.show', $document->id) }}" class="text-dark">
                                    {{ $document->title }}
                                </a>
                            </td>
                            <td>
                                @php
                                    $badgeMap = [
                                        'draft'     => 'secondary',
                                        'in_review' => 'warning',
                                        'approved'  => 'success',
                                        'rejected'  => 'danger',
                                        'archived'  => 'dark',
                                    ];
                                    $badge = $badgeMap[$document->status] ?? 'secondary';
                                @endphp
                                <span class="badge badge-{{ $badge }}">
                                    {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                                </span>
                            </td>
                            <td>v{{ $document->version }}</td>
                            <td>{{ optional($document->creator)->name ?? '—' }}</td>
                            <td>{{ $document->created_at->format('d M Y') }}</td>
                            <td class="text-right">
                                <a href="{{ route('titan-vault.documents.show', $document->id) }}"
                                   class="btn btn-sm btn-outline-primary mr-1">
                                    <i class="fa fa-eye"></i>
                                </a>
                                @if($this->user->permission('edit_vault_documents'))
                                    <a href="{{ route('titan-vault.documents.edit', $document->id) }}"
                                       class="btn btn-sm btn-outline-secondary mr-1">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                @endif
                                @if($this->user->permission('delete_vault_documents'))
                                    <x-forms.button-secondary
                                        class="btn-xs delete-confirm"
                                        data-row-id="{{ $document->id }}"
                                        data-confirm-url="{{ route('titan-vault.documents.destroy', $document->id) }}"
                                        icon="trash">
                                    </x-forms.button-secondary>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                @lang('titan_vault::titan_vault.no_documents')
                            </td>
                        </tr>
                    @endforelse
                </x-table>

                <div class="mt-3">
                    {{ $documents->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $('#filter-status').on('change', function () {
        const url = new URL(window.location.href);
        const val = $(this).val();
        if (val) {
            url.searchParams.set('status', val);
        } else {
            url.searchParams.delete('status');
        }
        window.location = url.toString();
    });

    $('#reset-filters').on('click', function () {
        window.location = '{{ route('titan-vault.documents.index') }}';
    });
</script>
@endpush
