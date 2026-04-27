@php
    $addPermission = user()->permission('add_footer_link');
    $editPermission = user()->permission('edit_footer_link');
    $deletePermission = user()->permission('delete_footer_link');
@endphp

<div class="table-responsive p-20">
    <div id="table-actions" class="d-block d-lg-flex align-items-center">

        @if ($addPermission == 'all')
            <x-forms.button-primary icon="plus" id="addlink" class="mb-2">
                @lang('recruit::modules.footerlinks.addfooterlinks')
            </x-forms.button-primary>
        @endif

    </div>
    <x-table class="table-bordered">
        <x-slot name="thead">
            <th>@lang('app.title')</th>
            <th>@lang('recruit::modules.footerlinks.description')</th>
            <th>@lang('app.status')</th>
            <th class="text-right">@lang('app.action')</th>
        </x-slot>
        @forelse($footerLinks as $link)
            <tr class="row{{ $link->id }}">
                <td>
                    {{ $link->title }}
                </td>
                <td class="col-md-8">
                    {!! $link->description !!}
                </td>
                <td>
                    @if ($addPermission == 'all')
                        <select class="change-footer-status form-control select-picker"
                                data-footer-id="{{ $link->id }}">
                            <option @if ($link->status == 'active') selected @endif>@lang('app.active')</option>
                            <option @if ($link->status == 'inactive') selected @endif>@lang('app.inactive')</option>
                        </select>
                    @else
                        @if ($link->status == 'active')
                            <i class="fa fa-circle mr-1 text-light-green f-10"></i>@lang(($link->status))
                        @else
                            <i class="fa fa-circle mr-1 text-red f-10"></i>@lang(($link->status))
                        @endif
                    @endif
                </td>
                <td class="text-right col-md-2">
                    <div class="task_view">
                        @if ($editPermission == 'all')
                            <a href="javascript:;" data-footer-id="{{ $link->id }}"
                               class="editLink task_view_more d-flex align-items-center justify-content-center">
                                <i class="fa fa-edit icons mr-1"></i> @lang('app.edit')
                            </a>
                        @endif
                    </div>
                    <div class="task_view">
                        @if ($deletePermission == 'all')
                            <a href="javascript:;" data-footer-id="{{ $link->id }}"
                               class="delete-footer task_view_more d-flex align-items-center justify-content-center dropdown-toggle">
                                <i class="fa fa-trash icons mr-2"></i> @lang('app.delete')
                            </a>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <x-cards.no-record-found-list :colspan="4" />
        @endforelse
    </x-table>
</div>

<script>
    /* delete link */
    $('body').off('click', ".delete-footer").on('click', '.delete-footer', function () {

        var id = $(this).data('footer-id');
        Swal.fire({
            title: "@lang('messages.sweetAlertTitle')",
            text: "@lang('messages.confirmRemove')",
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
                var url = "{{ route('footer-settings.destroy', ':id') }}";
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

    /* change links status */
    $('body').on('change', '.change-footer-status', function () {
        var agentId = $(this).data('footer-id');
        var status = $(this).val();
        var token = '{{ csrf_token() }}';
        var url = "{{ route('footer-settings.change_status', ':id') }}";
        url = url.replace(':id', agentId);

        if (typeof agentId !== 'undefined') {
            $.easyAjax({
                type: 'POST',
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
    $('body').off('click', "#addlink").on('click', '#addlink', function () {
        var url = "{{ route('footer-settings.create') }}";
        $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_LG, url);
    });

    // add new leave type
    $('body').off('click', ".editLink").on('click', '.editLink', function () {

        var id = $(this).data('footer-id');

        var url = "{{ route('footer-settings.edit', ':id') }}";
        url = url.replace(':id', id);

        $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_LG, url);
    });

</script>
