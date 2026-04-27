<!-- TAB CONTENT START -->
<div class="tab-pane fade show active" role="tabpanel" aria-labelledby="nav-email-tab">
    <div class="d-flex bg-white flex-wrap p-20" id="task-file-list">

        <x-table headType="thead-light">
            <x-slot name="thead">
                <th>#</th>
                <th>@lang('recruit::modules.interviewSchedule.candidate')</th>
                <th>@lang('recruit::modules.interviewSchedule.submittedBy')</th>
                <th>@lang('recruit::modules.footerlinks.status')</th>
                <th>@lang('recruit::modules.jobApplication.stages')</th>
                <th>@lang('recruit::app.menu.details')</th>
                <th>@lang('app.action')</th>
            </x-slot>

            @forelse ($evaluations as $key => $item)
                <tr>
                    <td>
                        {{ ++$key }}
                    </td>
                    <td>
                        {{ $item->interview->jobApplication->full_name }}
                    </td>
                    <td>
                        <x-employee :user="$item->user" />
                    </td>
                    <td>
                        <p class="mb-1">
                            {{ $item->status->status }}
                        </p>
                    </td>
                    <td>
                        <p class="mb-1">
                            {{ $item->stage->name }}
                        </p>
                    </td>
                    <td>
                        {{ $item->details }}
                    </td>
                    <td class="text-right">
                        @if ($item->submitted_by == user()->id)
                            <div class="dropdown ml-auto message-action">
                                <button
                                    class="btn btn-lg f-14 p-0 text-lightest  rounded  dropdown-toggle"
                                    type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-ellipsis-h"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                                     aria-labelledby="dropdownMenuLink" tabindex="0">
                                    <a class="cursor-pointer d-block text-dark-grey f-13 py-2 px-3 openRightModal"
                                       href="{{ route('evaluation.edit', [$item->id]) }}">@lang('app.edit')</a>
                                    <a class="cursor-pointer d-block text-dark-grey f-13 py-2 px-3 delete-evaluation"
                                       href="javascript:;"
                                       data-evaluation-id="{{ $item->id }}">@lang('app.delete')</a>
                                </div>
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">
                        <x-cards.no-record :message="__('messages.noRecordFound') . ' ' . __('recruit::messages.evaluationMessage')" icon="clock"/>
                    </td>
                </tr>
            @endforelse
        </x-table>
    </div>
</div>
<!-- TAB CONTENT END -->

<script>
    $('.delete-evaluation').click(function () {
        var id = $(this).data('evaluation-id');
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
                var url = "{{ route('evaluation.destroy', ':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {
                        '_token': token,
                        '_method': 'DELETE'
                    },
                    success: function (response) {
                        if (response.status == "success") {
                            window.location.href = response.redirectUrl;
                        }
                    }
                });
            }
        });
    });


</script>
