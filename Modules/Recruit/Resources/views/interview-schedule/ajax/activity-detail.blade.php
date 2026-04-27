@php
    $addInterviewPermission = user()->permission('add_interview_schedule');
    $editInterviewSchedulePermission = user()->permission('edit_interview_schedule');
    $deleteInterviewSchedulePermission = user()->permission('delete_interview_schedule');
@endphp
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
