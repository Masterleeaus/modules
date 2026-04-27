@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('vendor/full-calendar/main.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/css/bootstrap-colorpicker.css') }}"/>
@endpush
@section('filter-section')
    <x-filters.filter-box>

        <!-- STATUS START -->
        {{-- <div class="select-box d-flex py-2 pr-2 border-right-grey border-right-grey-sm-0 align-items-center">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.status')</p>
            <div class="select-status">
                <select class="form-control select-picker " name="status" data-container="body" id="status">
                    <option value="not finished">@lang('recruit::modules.interviewSchedule.hideFinishedMeetings')
                    </option>
                    <option value="all">@lang('app.all')</option>
                    <option value="rejected">@lang('app.rejected')</option>
                    <option value="hired">@lang('recruit::app.menu.hired')</option>
                    <option value="pending">@lang('app.pending')</option>
                    <option value="canceled">@lang('app.canceled')</option>
                </select>
            </div>
        </div> --}}
        <!-- STATUS END -->

        <!-- SEARCH BY TASK START -->
        <div class="task-search d-flex  py-1 px-lg-3 px-0 border-right-grey align-items-center">
            <form class="w-100 mr-1 mr-lg-0 mr-md-1 ml-md-1 ml-0 ml-lg-0">
                <div class="input-group bg-grey rounded">
                    <div class="input-group-prepend">
                        <span class="input-group-text border-0 bg-additional-grey">
                            <i class="fa fa-search f-13 text-dark-grey"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control f-14 p-1 border-additional-grey" id="search-text-field"
                           placeholder="@lang('app.startTyping')">
                </div>
            </form>
        </div>
        <!-- SEARCH BY TASK END -->

        <!-- RESET START -->
        <div class="select-box d-flex py-2 px-lg-3 px-md-3 px-0">
            <x-forms.button-secondary class="btn-xs d-none" id="reset-filters" icon="times-circle">
                @lang('app.clearFilters')
            </x-forms.button-secondary>
        </div>
        <!-- RESET END -->
    </x-filters.filter-box>
@endsection
@php
    $addInterviewPermission = user()->permission('add_interview_schedule');
    $editInterviewSchedulePermission = user()->permission('edit_interview_schedule');
    $deleteInterviewSchedulePermission = user()->permission('delete_interview_schedule');
@endphp
@section('content')
    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper">
        <!-- Add Task Export Buttons Start -->
        <div class="d-flex">
            <div id="table-actions" class="flex-grow-1 align-items-center mb-2 mb-lg-0 mb-md-0">
                @if ($addInterviewPermission == 'all' || $addInterviewPermission == 'added'|| $addInterviewPermission == 'owned' || $addInterviewPermission == 'both')
                    <x-forms.link-primary :link="route('interview-schedule.create')"
                                          class="mr-3 mb-3 openRightModal float-left" icon="plus"
                                          data-redirect-url="{{ url()->full() }}">
                        @lang('app.add')
                        @lang('recruit::app.menu.interviewSchedule')
                    </x-forms.link-primary>
                @endif
            </div>

            <div class="btn-group mb-3" role="group">
                <a href="{{ route('interview-schedule.table_view') }}" class="btn btn-secondary f-14"
                   data-toggle="tooltip" data-original-title="@lang('recruit::app.menu.tableView')"><i
                        class="side-icon bi bi-list-ul"></i></a>

                <a href="{{ route('interview-schedule.index') }}" class="btn btn-secondary f-14 btn-active"
                   data-toggle="tooltip" data-original-title="@lang('recruit::app.menu.calendarView')"><i
                        class="ide-icon bi bi-calendar"></i></a>
            </div>
        </div>

        <!-- leave table Box Start -->
        <div class="row">
            <div class="col-md-8">
                <!-- CALENDAR VIEW START -->
                <div class="e-d-tasks-projects-events">
                    <div class="row">
                        <div class="col-md-12">
                            <x-cards.data>
                                <div id="calendar"></div>
                            </x-cards.data>
                        </div>
                    </div>
                </div>
                <!-- CALENDAR VIEW END -->
            </div>
            <!-- PROJECT RIGHT START -->
            <div class="col-md-4">
                <div class="bg-white">
                    <!-- ACTIVITY HEADING START -->
                    <div class="p-activity-heading d-flex align-items-center justify-content-between b-shadow-4 p-20">
                        <p class="mb-0 f-18 f-w-500">@lang('recruit::app.menu.interviewSchedule')</p>
                    </div>
                    <!-- ACTIVITY HEADING END -->

                    <!-- ACTIVITY DETAIL START -->
                    <div class="p-activity-detail cal-info b-shadow-4" data-menu-vertical="1" data-menu-scroll="1"
                         data-menu-dropdown-timeout="500" id="projectActivityDetail">

                        <div class="card border-0 b-shadow-4 p-20 rounded-0">
                            @forelse($upComingSchedules as $key => $event)
                                <div class="card-horizontal">
                                    <div class="card-header m-0 p-0 bg-white rounded">
                                        <x-date-badge :month="$event->schedule_date->format('M')"
                                                      :date="$event->schedule_date->timezone($company->timezone)->format('d')"/>
                                    </div>
                                    <div class="card-body border-0 p-0 ml-3">
                                        <a class="text-darkest-grey openRightModal" href="{{ route('interview-schedule.show', $event->id) }}">
                                        <h4 class="card-title f-14 font-weight-normal ">
                                            {{ ($event->jobApplication->full_name) }}
                                        </h4></a>
                                        <p class="card-text f-12 text-dark-grey mb-2">

                                            {{ $event->schedule_date->setTimeZone(company()->timezone)->format($company->date_format. ' , ' . $company->time_format) }}
                                        </p>
                                        <p class="card-text f-12 text-dark-grey">
                                            {{ ($event->jobApplication->job->title) }}
                                        </p>

                                        @php
                                            $secEmp = [];
                                            foreach($event->employees as $usrdt){
                                                $secEmp[] = $usrdt->id;

                                            }

                                            $employeeStatus = $event->employeesData->filter(function ($value, $key) use ($loggedEmployee)  {
                                                return $value->user_id == $loggedEmployee->id;
                                            })->first();
                                        @endphp
                                        @if (in_array($loggedEmployee->id, $secEmp))
                                            @if ($employeeStatus->user_accept_status == 'accept')
                                                <label
                                                    class="badge badge-success float-right">@lang('recruit::modules.interviewSchedule.accepted')</label>
                                            @elseif($employeeStatus->user_accept_status == 'refuse')
                                                <label
                                                    class="badge badge-danger float-right">@lang('recruit::modules.interviewSchedule.refused')</label>
                                            @else

                                                <span class="float-right">
                                            <x-forms.button-primary
                                                onclick="employeeResponse({{ $employeeStatus->id }}, 'accept')"
                                                icon="check" class="mr-2">
                                                @lang('app.accept')
                                            </x-forms.button-primary>
                                            <x-forms.button-secondary
                                                onclick="employeeResponse({{ $employeeStatus->id }}, 'refuse')"
                                                icon="fa fa-times">
                                                @lang('recruit::modules.interviewSchedule.reject')
                                            </x-forms.button-secondary>

                                            </span>
                                            @endif
                                        @endif
                                    </div>

                                    <div class="text-right">
                                        @if ($editInterviewSchedulePermission  == 'all' ||
                                        ($editInterviewSchedulePermission  == 'added' && $event->added_by == user()->id) ||
                                        ($editInterviewSchedulePermission  == 'owned' && in_array($loggedEmployee->id, $secEmp)) ||
                                        ($editInterviewSchedulePermission  == 'both' && (in_array($loggedEmployee->id, $secEmp) ||
                                        $event->added_by == user()->id)) ||
                                        ($deleteInterviewSchedulePermission == 'all' ||
                                        ($deleteInterviewSchedulePermission == 'added' && $event->added_by == user()->id) ||
                                        ($deleteInterviewSchedulePermission == 'owned' && in_array($loggedEmployee->id, $secEmp)) ||
                                        ($deleteInterviewSchedulePermission == 'both' && (in_array($loggedEmployee->id, $secEmp) ||
                                        $event->added_by == user()->id))))
                                            <div class="dropdown">
                                                <button
                                                    class="btn btn-lg f-14 px-2 py-1 text-dark-grey  rounded  dropdown-toggle"
                                                    type="button" data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                    <i class="fa fa-ellipsis-h"></i>
                                                </button>

                                                <div
                                                    class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                                                    aria-labelledby="dropdownMenuLink" tabindex="0">

                                                    @if ($editInterviewSchedulePermission == 'all' ||
                                                    ($editInterviewSchedulePermission == 'added' && $event->added_by == user()->id) ||
                                                    ($editInterviewSchedulePermission == 'owned' && in_array($loggedEmployee->id, $secEmp)) ||
                                                    ($editInterviewSchedulePermission == 'both' && (in_array($loggedEmployee->id, $secEmp) ||
                                                    $event->added_by == user()->id)))
                                                        <a class="dropdown-item openRightModal"
                                                           href="{{ route('interview-schedule.edit', $event->id) }}">@lang('app.edit')</a>
                                                    @endif
                                                    @if ($event->status == 'pending')
                                                        @if ($editInterviewSchedulePermission == 'all' ||
                                                        ($editInterviewSchedulePermission == 'added' && $event->added_by == user()->id) ||
                                                        ($editInterviewSchedulePermission == 'owned' && in_array($loggedEmployee->id, $secEmp)) ||
                                                        ($editInterviewSchedulePermission == 'both' && (in_array($loggedEmployee->id, $secEmp) ||
                                                        $event->added_by == user()->id)))
                                                            <a class="dropdown-item reschedule-interview"
                                                               data-user-id="{{ $event->id }}">@lang('recruit::modules.interviewSchedule.reSchedule')</a>
                                                        @endif
                                                    @endif

                                                    @if ($deleteInterviewSchedulePermission == 'all' ||
                                                    ($deleteInterviewSchedulePermission == 'added' && $event->added_by == user()->id) ||
                                                    ($deleteInterviewSchedulePermission == 'owned' && in_array($loggedEmployee->id, $secEmp)) ||
                                                    ($deleteInterviewSchedulePermission == 'both' && (in_array($loggedEmployee->id, $secEmp) ||
                                                    $event->added_by == user()->id)))
                                                        <a class="dropdown-item delete-table-row"
                                                           data-schedule-id="{{ $event->id }}"
                                                           data-parent-id="{{ $event->parent_id }}">@lang('app.delete')</a>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <hr>
                            @empty
                                <h4 class="card-title f-14 font-weight-normal">
                                    @lang('recruit::modules.message.noInterview')</h4>
                                <p class="card-text f-12 text-dark-grey"></p>
                            @endforelse

                        </div>

                    </div><!-- card end -->
                </div><!-- card end -->
            </div>
            <!-- ACTIVITY DETAIL END -->
        </div>
    </div>
    <!-- PROJECT RIGHT END -->


@endsection

@push('scripts')
    <script src="{{ asset('vendor/full-calendar/main.min.js') }}"></script>
    <script src="{{ asset('vendor/full-calendar/locales-all.min.js') }}"></script>

    <script>
        $('#status, #employee, #client, #search-text-field').on('change keyup',
            function () {
                if ($('#status').val() != "not finished") {
                    $('#reset-filters').removeClass('d-none');
                } else if ($('#clientID').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                } else if ($('#employee').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                } else if ($('#search-text-field').val() != "") {
                    $('#reset-filters').removeClass('d-none');
                } else {
                    $('#reset-filters').addClass('d-none');
                }
                loadData();
            });

        $('body').on('click', '#reset-filters', function () {
            $('#filter-form')[0].reset();
            $('.filter-box #status').val('not finished');
            $('.filter-box .select-picker').selectpicker("refresh");
            $('#reset-filters').addClass('d-none');
            loadData();
        });
        $('body').on('click', '#reset-filters-2', function () {
            $('#filter-form')[0].reset();
            $('.filter-box #status').val('not finished');
            $('.filter-box .select-picker').selectpicker("refresh");
            $('#reset-filters').addClass('d-none');
            loadData();
        });

        var initialLocaleCode = '{{ user()->locale }}';
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            locale: initialLocaleCode,
            // timeZone: '{{ $company->timezone }}',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            navLinks: true, // can click day/week names to navigate views
            selectable: false,
            selectMirror: true,
            select: function (arg) {
                addEventModal(arg.start, arg.end, arg.allDay);
                calendar.unselect()
            },
            eventClick: function (arg) {
                getEventDetail(arg.event.id);
            },
            editable: false,
            dayMaxEvents: true, // allow "more" link when too many events
            events: {
                url: "{{ route('interview-schedule.index') }}",
                extraParams: function () {
                    var searchText = $('#search-text-field').val();
                    var clientId = $('#client').val();
                    var employeeId = $('#employee').val();
                    var status = 'pending';

                    return {
                        searchText: searchText,
                        status: status,
                        clientId: clientId,
                        employeeId: employeeId
                    };
                }
            },
            eventDidMount: function (info) {
                $(info.el).css('background-color', info.event.extendedProps.bg_color);
                $(info.el).css('color', info.event.extendedProps.color);
            },
            eventTimeFormat: {
                hour: company.time_format == 'H:i' ? '2-digit' : 'numeric',
                minute: '2-digit',
                meridiem: company.time_format == 'H:i' ? false : true
            }
        });

        calendar.render();

        function loadData() {
            calendar.refetchEvents();
            calendar.destroy();
            calendar.render();
        }

        // show event detail in sidebar
        var getEventDetail = function (id) {
            openTaskDetail();
            var url = "{{ route('interview-schedule.show', ':id') }}";
            url = url.replace(':id', id);

            $.easyAjax({
                url: url,
                blockUI: true,
                container: RIGHT_MODAL,
                historyPush: true,
                success: function (response) {
                    if (response.status == "success") {
                        $(RIGHT_MODAL_CONTENT).html(response.html);
                        $(RIGHT_MODAL_TITLE).html(response.title);
                    }
                },
                error: function (request, status, error) {
                    if (request.status == 403) {
                        $(RIGHT_MODAL_CONTENT).html(
                            '<div class="align-content-between d-flex justify-content-center mt-105 f-21">403 | Permission Denied</div>'
                        );
                    } else if (request.status == 404) {
                        $(RIGHT_MODAL_CONTENT).html(
                            '<div class="align-content-between d-flex justify-content-center mt-105 f-21">404 | Not Found</div>'
                        );
                    } else if (request.status == 500) {
                        $(RIGHT_MODAL_CONTENT).html(
                            '<div class="align-content-between d-flex justify-content-center mt-105 f-21">500 | Something Went Wrong</div>'
                        );
                    }
                }
            });
        }

        function employeeResponse(id, type) {

            Swal.fire({
                title: "@lang('messages.sweetAlertTitle')",
                icon: 'warning',
                showCancelButton: true,
                focusConfirm: false,
                confirmButtonText: "@lang('messages.confirm')",
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
                    var url = "{{ route('interview-schedule.response',[':id',':type']) }}";
                    url = url.replace(':id', id);
                    url = url.replace(':type', type);

                    var token = "{{ csrf_token() }}";
                    $.easyAjax({
                        url: url,
                        blockUI: true,
                        type: 'GET',
                        success: function (response) {
                            if (response.status == 'success') {
                                window.location.reload();
                            }
                        }
                    });
                }
            });
        };
        $('body').on('click', '.delete-table-row', function () {
            var id = $(this).data('schedule-id');
            var parentId = $(this).data('parent-id');
            if (parentId == '') {
                Swal.fire({
                    title: "@lang('messages.sweetAlertTitle')",
                    text: "@lang('recruit::messages.relatedInterviewdelete')",
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
                            type: 'POST',
                            url: url,
                            blockUI: true,
                            data: {
                                '_token': token,
                                'parentId': parentId,
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
            } else {
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
                            type: 'POST',
                            url: url,
                            blockUI: true,
                            data: {
                                '_token': token,
                                'parentId': parentId,
                                '_method': 'DELETE',
                            },
                            success: function (response) {
                                if (response.status == "success") {
                                    window.location.href = response.redirectUrl;

                                }
                            }
                        });
                    }
                });
            }
        });

        $('body').on('click', '.reschedule-interview', function () {
            var id = $(this).data('user-id');
            const url = "{{ route('interview-schedule.reschedule') }}?id=" + id;
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        $('#status').change(function(){
            let status = $(this).val();
            $.easyAjax({
                type: 'GET',
                url: "{{route('interview-schedule.index')}}",
                blockUI: true,
                data: {
                    'status':status,
                },
                success: function (response) {
                    $('#projectActivityDetail').html(response.html);
                }
            })
        });
    </script>
@endpush
