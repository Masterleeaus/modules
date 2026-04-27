@php
$addPermission = user()->permission('add_job_application');
$viewPermission = user()->permission('view_job_application');
$editPermission = user()->permission('edit_job_application');
$deletePermission = user()->permission('delete_job_application');
@endphp

<!-- ROW START -->
<div id="interview-table">
    <div class="row p-20">
        <div class="col-xl-12 col-lg-12 col-md-12 mb-4 mb-xl-0 mb-lg-4 mb-md-0">
            <input type="hidden" id="candidate_id" name="candidate_id" value="{{ $application->id }}">

            @if ($viewPermission == 'all' || $viewPermission == 'added')
                <x-cards.data :title="__('recruit::modules.front.interviewSchedule')"
                    otherClasses="border-0 p-0 d-flex justify-content-between align-items-center table-responsive-sm">
                    <x-table class="border-0 pb-3 admin-dash-table table-hover">

                        <x-slot name="thead">
                            <th class="pl-20">#</th>
                            <th>@lang('recruit::modules.front.fullName')</th>
                            <th>@lang('recruit::modules.interviewSchedule.interviewer')</th>
                            <th>@lang('recruit::modules.interviewSchedule.scheduleDate')</th>
                            <th>@lang('app.status')</th>
                            <th class="text-right pr-20">@lang('app.action')</th>
                        </x-slot>

                        @forelse($interviewSchedule as $key => $follow)
                            <tr id="row-interview{{ $follow->id }}">
                                <td class="pl-20">{{ $key + 1 }}</td>
                                <td>
                                    {!! $follow->full_name != '' ? (nl2br($follow->full_name)) : '--' !!}
                                <td>
                                    @php
                                         $emp = $follow->employeesData;
                                    @endphp
                                    <div class="position-relative">
                                        @if (count($emp) > 0)
                                            @foreach ($emp as $key => $member)
                                                @if ($key < 4)
                                                    @php
                                                        $position = $key > 0 ? 'position-absolute' : '';
                                                    @endphp
                                                    <div class="taskEmployeeImg rounded-circle {{ $position }}" style="left:{{ $key * 13 }}px"><a href="{{ route('employees.show', $member->user->id) }}">
                                                    <img data-toggle="tooltip" data-original-title="{{ $member->user->name }}" src="{{ $member->user->image_url }}">
                                                    </a>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif

                                        @if (count($emp) > 4)
                                            <div class="taskEmployeeImg more-user-count text-center rounded-circle bg-amt-grey position-absolute" style="left:  52px"><a href=" {{ route('interview-schedule.show', [$follow->id]) }} ?tab=details" class="text-dark f-10"> {{(count($emp) - 4)}}</a></div>
                                            </div>
                                        @endif
                                </td>
                                <td>
                                    {{ $follow->schedule_date->translatedFormat(company()->date_format . ' ' . company()->time_format) }}
                                </td>
                                <td>
                                    @if ($editInterviewSchedulePermission != 'none' && (
                                            $editInterviewSchedulePermission == 'all'
                                            || ($editInterviewSchedulePermission == 'added' && $row->added_by == user()->id)
                                            || ($editInterviewSchedulePermission == 'owned' && $row->user_id == user()->id)
                                            || ($editInterviewSchedulePermission == 'both' && ($row->user_id == user()->id || $row->added_by == user()->id))
                                        ))
                                        <select class="form-control select-picker" id="change-interview-status"  data-interview-id = "{{$follow->id}}">
                                            @foreach ($applicationStatuses as $status)
                                                <option @if ($status == $follow->status) selected @endif value="{{$status}}"
                                                        data-content="<i class='fa fa-circle mr-2 @if ($status == 'pending') text-yellow @elseif ($status == 'hired') text-light-green @elseif ($status == 'canceled') text-black @elseif ($status == 'completed') text-light-green @elseif ($status == 'rejected') text-red @endif'></i> {{ ($status) }}"></option>
                                            @endforeach
                                        </select>
                                    @endif
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
                                                @if ($viewPermission == 'all' || ($viewPermission == 'added' && $follow->added_by == user()->id))
                                                    <a class="dropdown-item openRightModal"
                                                        href="{{ route('interview-schedule.show', $follow->id) }}">
                                                        <i class="fa fa-eye mr-2"></i>
                                                        @lang('app.view')
                                                    </a>
                                                @endif
                                                @if ($editPermission == 'all' || ($editPermission == 'added' && $follow->added_by == user()->id))
                                                    <a class="dropdown-item openRightModal"
                                                        data-interview-id="{{ $follow->id }}" href="{{ route('interview-schedule.edit' ,$follow->id) }}">
                                                        <i class="fa fa-edit mr-2"></i>
                                                        @lang('app.edit')
                                                    </a>
                                                @endif
                                                @if ($deletePermission == 'all' || ($deletePermission == 'added' && $follow->added_by == user()->id))
                                                    <a class="dropdown-item delete-table-row-interview" href="javascript:;"
                                                        data-interview-id="{{ $follow->id }}">
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
                            <x-cards.no-record-found-list colspan="6"/>
                        @endforelse
                    </x-table>
                </x-cards.data>
            @endif

        </div>
    </div>
</div>
<!-- ROW END -->

<script>

    $(document).ready(function () {
        $(".select-picker").selectpicker();
        // Delete lead followup
        $('body').on('click', '.delete-table-row-interview', function() {
            var id = $(this).data('interview-id');
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
                    var url = "{{ route('interview-schedule.destroy', ':id') }}";
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
                                $('#row-interview'+id).remove();
                                $(".select-picker").selectpicker();
                            }
                        }
                    });
                }
            });
        });

        /* change status */
        $('body').on('change', '#change-interview-status', function () {
            var id = $(this).data('interview-id');
            var url = "{{ route('interview-schedule.change_interview_status') }}";

            var token = "{{ csrf_token() }}";
            var status = $(this).val();
            if (typeof id !== 'undefined') {
                $.easyAjax({
                    url: "{{ route('interview-schedule.change_interview_status') }}",
                    type: "POST",
                    data: {
                        '_token': token,
                        interviewId: id,
                        status: status
                    },
                    success: function (response) {
                        if (response.status == "success") {
                            showTable();
                            resetActionButtons();
                            deSelectAll();

                            $(".select-picker").selectpicker();

                        }
                    }
                });
            }
        });
    });
</script>
