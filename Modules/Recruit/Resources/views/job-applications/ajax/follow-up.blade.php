@php
$addPermission = user()->permission('add_job_application');
$viewPermission = user()->permission('view_job_application');
$editPermission = user()->permission('edit_job_application');
$deletePermission = user()->permission('delete_job_application');
@endphp

<!-- ROW START -->
<div id="follow-up-table">
    <div class="row p-20">
        <div class="col-xl-12 col-lg-12 col-md-12 mb-4 mb-xl-0 mb-lg-4 mb-md-0">
            <input type="hidden" id="candidate_id" name="candidate_id" value="{{ $application->id }}">

            @if (($addPermission == 'all' || $addPermission == 'added'))
                <x-forms.button-primary icon="plus" id="add-candidate-followup" data-application-id="{{ $application->id }}" data-datatable="false" class="type-btn mb-3">
                    @lang('modules.followup.newFollowUp')
                </x-forms.button-primary>
            @endif

            @if ($viewPermission == 'all' || $viewPermission == 'added')
                <x-cards.data :title="__('modules.lead.followUp')"
                    otherClasses="border-0 p-0 d-flex justify-content-between align-items-center table-responsive-sm">
                    <x-table class="border-0 pb-3 admin-dash-table table-hover">

                        <x-slot name="thead">
                            <th class="pl-20">#</th>
                            <th>@lang('app.createdOn')</th>
                            <th>@lang('modules.lead.nextFollowUp')</th>
                            <th>@lang('app.remark')</th>
                            <th>@lang('app.status')</th>
                            <th class="text-right pr-20">@lang('app.action')</th>
                        </x-slot>

                        @forelse($followUps as $key => $follow)
                            <tr id="row-{{ $follow->id }}">
                                <td class="pl-20">{{ $key + 1 }}</td>
                                <td>
                                    {{ $follow->created_at->timezone(company()->timezone)->translatedFormat(company()->date_format . ' ' . company()->time_format) }}
                                </td>
                                <td>
                                    {{ $follow->next_follow_up_date->translatedFormat(company()->date_format . ' ' . company()->time_format) }}
                                </td>
                                <td>
                                    {!! $follow->remark != '' ? (nl2br($follow->remark)) : '--' !!}
                                </td>
                                <td>
                                    <select class="form-control select-picker" id="change-follow-up-status" data-id = "{{$follow->id}}">
                                        <option value="incomplete"  @if($follow->status == 'incomplete') selected @endif  data-content="<i class='fa fa-circle mr-2 text-red'></i> @lang('app.incomplete') " >@lang('app.incomplete')</option>
                                        <option value="completed" @if($follow->status == 'completed') selected @endif data-content="<i class='fa fa-circle mr-2 text-dark-green'></i> @lang('app.completed') " >@lang('app.completed')</option>
                                    </select>
                                </td>
                                <td class="text-right pr-20">
                                    <div class="task_view">
                                        <div class="dropdown">
                                            <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle"
                                                type="link" id="dropdownMenuLink-3" data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                <i class="icon-options-vertical icons"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                @if ($editPermission == 'all' || ($editPermission == 'added' && $follow->added_by == user()->id))
                                                    <a class="dropdown-item edit-candidate-followup"
                                                        data-follow-id="{{ $follow->id }}" href="javascript:;">
                                                        <i class="fa fa-edit mr-2"></i>
                                                        @lang('app.edit')
                                                    </a>
                                                @endif
                                                @if ($deletePermission == 'all' || ($deletePermission == 'added' && $follow->added_by == user()->id))
                                                    <a class="dropdown-item delete-table-row-followup" href="javascript:;"
                                                        data-follow-id="{{ $follow->id }}">
                                                        <i class="fa fa-trash mr-2"></i>
                                                        @lang('app.delete')
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-cards.no-record-found-list colspan="5"/>
                        @endforelse
                    </x-table>
                </x-cards.data>
            @endif

        </div>
    </div>
</div>
<!-- ROW END -->

<script>
    $(".select-picker").selectpicker();

    // Delete lead followup
    $('body').on('click', '.delete-table-row-followup', function() {
        var id = $(this).data('follow-id');
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
                var url = "{{ route('candidate-follow-up.destroy', ':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'DELETE',
                    url: url,
                    blockUI: true,
                    data: {
                        '_token': token,
                    },
                    success: function(response) {
                        if (response.status == "success") {
                            document.getElementById('follow-up-table').innerHTML = response.view;
                            $(".select-picker").selectpicker();

                            $('#add-candidate-followup').click(function() {
                                let applicationId = $(this).data('application-id');
                                let datatable = $(this).data('datatable');
                                let searchQuery = "?id=" + applicationId + "&datatable=" + datatable;
                                let url = "{{ route('candidate-follow-up.create') }}" + searchQuery;

                                $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
                                $.ajaxModal(MODAL_LG, url);
                            })

                            $('.edit-candidate-followup').click(function() {
                                var id = $(this).data('follow-id');
                                var url = "{{ route('candidate-follow-up.edit', ':id') }}";
                                url = url.replace(':id', id);
                                $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
                                $.ajaxModal(MODAL_LG, url);
                            });
                        }
                    }
                });
            }
        });
    });

    $('#add-candidate-followup').click(function() {
        let applicationId = $(this).data('application-id');
        let datatable = $(this).data('datatable');
        let searchQuery = "?id=" + applicationId + "&datatable=" + datatable;
        let url = "{{ route('candidate-follow-up.create') }}" + searchQuery;

        $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_LG, url);
    })

    $('.edit-candidate-followup').click(function() {
        var id = $(this).data('follow-id');
        var url = "{{ route('candidate-follow-up.edit', ':id') }}";
        url = url.replace(':id', id);
        $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_LG, url);
    });

    /* change status */
    $('body').on('change', '#change-follow-up-status', function () {
        var followUpId = $(this).data('id');
        var status = $(this).val();
        var token = '{{ csrf_token() }}';
        var url = "{{ route('candidate-follow-up.change_follow_up_status') }}?id=" + followUpId;

        if (typeof followUpId !== 'undefined') {
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

</script>
