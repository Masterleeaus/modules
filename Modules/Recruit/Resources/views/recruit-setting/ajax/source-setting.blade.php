@php
    $addPermission = user()->permission('add_application_status');
    $editPermission = user()->permission('edit_application_status');
    $deletePermission = user()->permission('delete_application_status');
@endphp

<div class="table-responsive p-20">
    <div id="table-actions" class="d-block d-lg-flex align-items-center">
        @if ($addPermission == 'all')
            <x-forms.button-primary icon="plus" id="add-source" class="mb-2">
                @lang('app.add')
            </x-forms.button-primary>
        @endif
    </div>
    <x-table class="table-bordered">
        <x-slot name="thead">
            <th>@lang('source')</th>
            <th class="text-right">@lang('app.action')</th>
        </x-slot>
        @forelse($sources as $source)
            <tr class="row{{ $source->id }}">
                <td>
                    {{ $source->application_source }}
                </td>
                <td class="text-right">
                    @if ($editPermission == 'all' && !$source->is_predefined)
                    <div class="task_view">
                        <a href="javascript:;" data-source-id="{{ $source->id }}"
                            class="edit-source task_view_more d-flex align-items-center justify-content-center">
                            <i class="fa fa-edit icons mr-1"></i> @lang('app.edit')
                        </a>
                    </div>
                    <div class="task_view">
                        <a href="javascript:;" data-source-id="{{ $source->id }}"
                            class="delete-source task_view_more d-flex align-items-center justify-content-center dropdown-toggle">
                            <i class="fa fa-trash icons mr-2"></i> @lang('app.delete')
                        </a>
                    </div>
                    @else
                       <p>@lang('recruit::messages.predefinedSourceDelete')</p>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="2">
                    <x-cards.no-record icon="user" :message="__('messages.noRecordFound')"/>
                </td>
            </tr>
        @endforelse
    </x-table>
</div>

<script>
    /* delete status */
    $('body').on('click', '.delete-source', function () {
        var id = $(this).data('source-id');
        var url = "{{ route('source-setting.destroy', ':id') }}";
        url = url.replace(':id', id);

        Swal.fire({
            title: "@lang('messages.sweetAlertTitle')",
            text: "@lang('messages.recoverRecord')",
            icon: 'warning',
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: "@lang('messages.confirmDelete')",
            cancelButtonText: "@lang('app.cancel')",
            customClass: {
                confirmButton: 'btn btn-primary mr-3',
                cancelButton: 'btn btn-secondary'
            },
            showClass: {
                popup: 'swal2-noanimation',
                backdrop: 'swal2-noanimation'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                $.easyAjax({
                    url: url,
                    type: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        '_method': 'DELETE'
                    },
                    success: function (response) {
                        if (response.status == 'success') {
                            window.location.reload();
                        }
                    }
                });
            }
        });

    });

    $('body').on('click', '.edit-source', function () {
        var id = $(this).data('source-id');
        var url = "{{ route('source-setting.edit', ':id') }}";
        url = url.replace(':id', id);

        $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_LG, url);
    });

    /* open add status modal */
     $('body').on('click', '#add-source', function () {
        const url = "{{ route('source-setting.create') }}";

        $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_LG, url);
    });
</script>
