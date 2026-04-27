@php
    $addPermission = user()->permission('add_application_status');
    $editPermission = user()->permission('edit_application_status');
    $deletePermission = user()->permission('delete_application_status');
@endphp

<div class="table-responsive p-20">
    <div id="table-actions" class="d-block d-lg-flex align-items-center">
        @if (user()->permission('add_application_status') == 'all')
            <x-forms.button-primary icon="plus" id="add-column" class="mb-2">
                @lang('app.add') @lang('app.status')
            </x-forms.button-primary>
        @endif

    </div>
    <x-table class="table-bordered">
        <x-slot name="thead">
            <th>@lang('app.name')</th>
            <th>@lang('app.category')</th>
            <th>@lang('recruit::modules.jobApplication.labelColor')</th>
            <th>@lang('recruit::modules.jobApplication.position')</th>
            <th>@lang('recruit::modules.jobApplication.action')</th>
            <th class="text-right">@lang('app.action')</th>
        </x-slot>
        @forelse($statuses as $status)
            <tr class="row{{ $status->id }}">
                <td>
                    {{ $status->status }}
                </td>
                <td>
                    {{ $status->category->name }}
                </td>
                <td>
                    <i class="fa fa-circle mr-2 text-yellow" style="color: {{ $status->color }}"></i>
                    {{ $status->color }}
                </td>
                <td>
                    {{ $status->position }}
                </td>
                <td>
                    {{ $status->action }}
                </td>
                <td class="text-right">
                    <div class="task_view">
                        @if ($editPermission == 'all')
                            <a href="javascript:;" data-status-id="{{ $status->id }}"
                               class="edit-status task_view_more d-flex align-items-center justify-content-center">
                                <i class="fa fa-edit icons mr-1"></i> @lang('app.edit')
                            </a>
                        @endif
                    </div>
                    @if($deletePermission == 'all' && $status->id != 1 && $status->id != 2 && $status->id != 3 && $status->id != 4 && $status->id != 5)
                        <div class="task_view">
                            <a href="javascript:;" data-status-id="{{ $status->id }}"
                               class="delete-status task_view_more d-flex align-items-center justify-content-center dropdown-toggle">
                                <i class="fa fa-trash icons mr-2"></i> @lang('app.delete')
                            </a>
                        </div>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4">
                    <x-cards.no-record icon="user" :message="__('messages.noRecordFound')"/>
                </td>
            </tr>
        @endforelse
    </x-table>
</div>

<script>
    /* delete status */
    $('body').on('click', '.delete-status', function () {
        var id = $(this).data('status-id');
        var url = "{{ route('job-appboard.destroy', ':id') }}";
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

    $('body').on('click', '.edit-status', function () {
        var id = $(this).data('status-id');
        var url = "{{ route('job-appboard.edit', ':id') }}";
        url = url.replace(':id', id);

        $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_LG, url);
    });

    /* open add status modal */
     $('body').on('click', '#add-column', function () {
        const url = "{{ route('job-appboard.create') }}";
        $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_LG, url);
    });
</script>
