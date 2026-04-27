@php
    $addPermission = user()->permission('add_recruiter');
    $editPermission = user()->permission('edit_recruiter');
    $deletePermission = user()->permission('delete_recruiter');
@endphp

<div class="table-responsive p-20">
    <div id="table-actions" class="d-block d-lg-flex align-items-center">
        @if ($addPermission == 'all')
            <x-forms.button-primary icon="plus" id="addRecruiter" class="mb-2">
                @lang('app.addNew')
                @lang('recruit::modules.setting.recruiter')
            </x-forms.button-primary>
        @endif

    </div>
    <x-table class="table-bordered">
        <x-slot name="thead">
            <th>@lang('app.name')</th>
            <th>@lang('app.status')</th>
            <th class="text-right">@lang('app.action')</th>
        </x-slot>
        @forelse($recruiters as $recruiter)
            <tr class="row{{ $recruiter->user_id }}">
                <td>
                    <x-employee :user="$recruiter->user"/>
                </td>
                <td>
                    @if ($editPermission == 'all')
                        <select class="change-recruiter-status form-control select-picker"
                                data-recruiter-id="{{ $recruiter->id }}">
                            <option @if ($recruiter->status == 'enabled') selected @endif>@lang('app.enabled')</option>
                            <option
                                @if ($recruiter->status == 'disabled') selected @endif>@lang('app.disabled')</option>
                        </select>
                    @else
                        @if ($recruiter->status == 'enabled')
                            @lang(($recruiter->status))
                        @else
                            @lang(($recruiter->status))
                        @endif
                    @endif
                </td>
                <td class="text-right">
                    <div class="task_view">
                        @if ($deletePermission == 'all')
                            <a href="javascript:;" data-recruiter-id="{{ $recruiter->id }}"
                               class="delete-recruiter task_view_more d-flex align-items-center justify-content-center dropdown-toggle">
                                <i class="fa fa-trash icons mr-2"></i> @lang('app.delete')
                            </a>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <x-cards.no-record-found-list :colspan="3" />
        @endforelse
    </x-table>
</div>

<script>
    /* delete recruiter */
    $('body').on('click', '.delete-recruiter', function () {
        var id = $(this).data('recruiter-id');
        Swal.fire({
            title: "@lang('messages.sweetAlertTitle')",
            text: "@lang('messages.removeAgentText')",
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
                var url = "{{ route('recruiter.destroy', ':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    blockUI: true,
                    data: {
                        '_token': token,
                        '_method': 'DELETE'
                    },
                    success: function (response) {
                        if (response.status == "success") {
                            $('.row' + id).fadeOut(100);
                            location.reload();
                        }
                    }
                });
            }
        });
    });

    /* change recruiter status */
    $('body').on('change', '.change-recruiter-status', function () {

        var agentId = $(this).data('recruiter-id');
        var status = $(this).val();

        var token = '{{ csrf_token() }}';
        var url = "{{ route('recruiter.update', ':id') }}";
        url = url.replace(':id', agentId);

        if (typeof agentId !== 'undefined') {
            $.easyAjax({
                type: 'PUT',
                url: url,
                blockUI: true,
                data: {
                    '_token': token,
                    'status': status
                }
            });
        }
    });

    /* open add agent modal */
    $('body').off('click', "#addRecruiter").on('click', '#addRecruiter', function () {
        var url = "{{ route('recruiter.create') }}";
        $(MODAL_DEFAULT + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_DEFAULT, url);
    });
</script>
