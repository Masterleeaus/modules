@php
    $editInterviewSchedulePermission = user()->permission('edit_interview_schedule');
    $deleteInterviewSchedulePermission = user()->permission('delete_interview_schedule');
    $viewInterviewSchedulePermission = user()->permission('view_interview_schedule');
    $reschedulePermission = user()->permission('reschedule_interview');
@endphp

<div id="task-detail-section">
    <h3 class="heading-h1 mb-3">{{ ($interview->jobApplication->full_name) }}</h3>

    <div class="row">
        <div class="col-sm-8">
            <div class="card bg-white border-0 b-shadow-4">


                <div class="card-body">

                    <div class="col-12 px-0 pb-3 d-lg-flex d-md-flex d-block">
                        <p class="mb-0 text-lightest f-14 w-30 ">
                            @lang('recruit::modules.job.job')</p>
                        <p class="mb-0 text-dark-grey f-14 w-70 text-wrap p-0">
                            <a href="{{ route('jobs.show', [$interview->jobApplication->job->id])}}"
                               class="text-dark-grey openRightModal">{{ ($interview->jobApplication->job->title) }}</a>
                        </p>
                    </div>

                    <div class="col-12 px-0 pb-3 d-lg-flex d-md-flex d-block">
                        <p class="mb-0 text-lightest f-14 w-30 ">
                            @lang('recruit::modules.interviewSchedule.candidateName')</p>
                        <p class="mb-0 text-dark-grey f-14 w-70 text-wrap p-0">
                            <a href="{{ route('job-applications.show', [$interview->jobApplication->id])}}"
                               class="text-dark-grey openRightModal">{{ ($interview->jobApplication->full_name) }}</a>
                        </p>
                    </div>

                    <x-cards.data-row :label="__('recruit::modules.interviewSchedule.candidateEmail')"
                                      :value="$interview->jobApplication->email ?? '--'"/>
                    <x-cards.data-row :label="__('app.phone')"
                                      :value="$interview->jobApplication->phone ?? '--'"/>

                    <x-cards.data-row :label="__('recruit::modules.interviewSchedule.comment')"
                                      :value="$comments->comment ?? '--'" :html="true"/>


                    <div class="col-12 px-0 pb-3 d-lg-flex d-lg-flex d-block">
                        <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                            @lang('recruit::app.jobApplication.resume')</p>
                        <div class="row w-70">
                            @if($interview->jobApplication->files->count() > 0)
                                @forelse($interview->jobApplication->files as $file)
                                    <x-file-card :fileName="$file->filename" :dateAdded="$file->created_at->diffForHumans()">
                                        @if ($file->icon == 'images')
                                            <img src="{{ $file->file_url }}">
                                        @else
                                            <i class="fa {{ $file->icon }} text-lightest"></i>
                                        @endif

                                        <x-slot name="action">
                                            <div class="dropdown ml-auto file-action">
                                                <button class="btn btn-lg f-14 p-0 text-lightest  rounded  dropdown-toggle"
                                                        type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-ellipsis-h"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                                                    aria-labelledby="dropdownMenuLink" tabindex="0">
                                                        @if ($file->icon != 'images')
                                                            <a class="cursor-pointer d-block text-dark-grey f-13 pt-3 px-3 " target="_blank"
                                                            href="{{ $file->file_url }}">@lang('app.view')</a>
                                                            @endif
                                                        <a class="cursor-pointer d-block text-dark-grey f-13 py-3 px-3 "
                                                        href="{{ route('application-file.download', md5($file->id)) }}">@lang('app.download')</a>
                                                </div>
                                            </div>
                                        </x-slot>

                                    </x-file-card>
                                @empty
                                    <x-cards.no-record :message="__('messages.noFileUploaded')" icon="file"/>
                                @endforelse
                            @endif
                        </div>
                    </div>

                </div>
            </div>

            <!-- TASK TABS START -->
            <div class="bg-additional-grey rounded my-3">

                <div class="s-b-inner s-b-notifications bg-white b-shadow-4 rounded">

                    <x-tab-section class="task-tabs">

                        <x-tab-item class="ajax-tab" :active="(request('view') === 'file' || !request('view'))"
                        :link="route('interview-schedule.show', $interview->id).'?view=file'">@lang('app.file')</x-tab-item>

                        <x-tab-item class="ajax-tab" :active="(request('view') === 'evaluations')"
                        :link="route('interview-schedule.show', $interview->id).'?view=evaluations'">@lang('recruit::modules.interviewSchedule.evaluations')</x-tab-item>

                        <x-tab-item class="ajax-tab" :active="(request('view') === 'history')"
                        :link="route('interview-schedule.show', $interview->id).'?view=history'">@lang('modules.tasks.history')</x-tab-item>

                    </x-tab-section>


                    <div class="s-b-n-content">
                        <div class="tab-content" id="nav-tabContent">
                            @include($tab)
                        </div>
                    </div>
                </div>


            </div>
            <!-- TASK TABS END -->
        </div>

        <div class="col-sm-4">
            <x-cards.data title="Interview Rounds">

                <div class="row mb-4 pb-4 border-bottom">
                    <div class="col-sm-12 d-flex justify-content-between">
                        @if ($interview->parent_id == null)
                            <h4 class="heading-h5">{{ $interview->stage ? $interview->stage->name : '' }}</h4>
                        @else
                            <h4 class="heading-h5">{{ $parentStage->stage ? $parentStage->stage->name : '' }}</h4>
                        @endif

                        <div class="text-right">
                            @if ($interview->status == 'pending')
                                <i class="fa fa-circle mr-1 text-yellow f-14"></i> @lang('recruit::modules.interviewSchedule.' . $interview->status)
                            @elseif ($interview->status == 'completed')
                                <i class="fa fa-circle mr-1 text-blue f-14"></i>@lang('recruit::modules.interviewSchedule.' . $interview->status)
                            @elseif ($interview->status == 'hired')
                                <i class="fa fa-circle mr-1 text-light-green f-14"></i>@lang('recruit::modules.interviewSchedule.' . $interview->status)
                            @elseif ($interview->status == 'rejected')
                                <i class="fa fa-circle mr-1 text-brown f-14"></i>@lang('recruit::modules.interviewSchedule.' . $interview->status)
                            @else
                                <i class="fa fa-circle mr-1 text-red f-14"></i>@lang('recruit::modules.interviewSchedule.' . $interview->status)
                            @endif
                        </div>
                    </div>



                    <div class="col-lg-10 col-10  my-2 mb-3">
                        @if($interview->status == 'hired' || $interview->status == 'completed')
                            @php
                                $selected_employees = $interview->employees->pluck('id')->toArray();
                            @endphp
                            @foreach ($selected_employees as $attendee)
                                @if (!in_array($attendee, $interview->evaluation->pluck('submitted_by')->toArray()) && $attendee == user()->id)
                                    <div class="d-flex flex-wrap">
                                        <a href="{{ route('evaluation.create', ['id' => $interview->id]) }}" class="openRightModal p-1 rounded border text-dark-grey f-12 btn btn-primary">
                                            <i class="mr-1 fa fa-plus"></i>@lang('app.add') @lang('recruit::modules.interviewSchedule.evaluation')
                                        </a>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                        <div class="row">
                            <div class="col-sm-12">
                                @if (in_array('Zoom', $worksuitePlugins))
                                    @if ($interview->video_type == 'zoom')
                                        @php
                                            if ($zoom_setting->meeting_app == 'in_app') {
                                                $url = route('zoom-meetings.start_meeting', $interview->meeting->id);
                                            } else {
                                                $url = user()->id == $interview->meeting->created_by ? $interview->meeting->start_link : $interview->meeting->join_link;
                                            }
                                            $nowDate = now(company()->timezone)->toDateString();
                                        @endphp
                                        <div class="col-md-2">
                                            @if (user()->id == $interview->meeting->created_by)
                                                @if ($interview->meeting->status == 'waiting')
                                                    @php
                                                        $nowDate = now(company()->timezone)->toDateString();
                                                        $meetingDate = $interview->meeting->start_date_time->toDateString();
                                                    @endphp

                                                    @if (isset($url) && (is_null($interview->meeting->occurrence_id) || $nowDate == $meetingDate))
                                                        <x-forms.link-primary target="_blank" :link="$url" icon="play">
                                                            @lang('recruit::modules.interviewSchedule.startInterview')
                                                        </x-forms.link-primary>
                                                    @endif

                                                @endif
                                            @else
                                                @if ($interview->meeting->status == 'waiting' || $interview->meeting->status == 'live')
                                                    @php
                                                        $nowDate = now(company()->timezone)->toDateString();
                                                        $meetingDate = $interview->meeting->start_date_time->toDateString();
                                                    @endphp

                                                    @if (isset($url) && (is_null($interview->meeting->occurrence_id) || $nowDate == $meetingDate))
                                                        <x-forms.link-primary target="_blank" :link="$url" icon="play">
                                                            @lang('recruit::modules.interviewSchedule.joinUrl')
                                                        </x-forms.link-primary>
                                                    @endif

                                                @endif

                                            @endif
                                        </div>
                                    @endif
                                @endif
                                @if ($editInterviewSchedulePermission == 'all'
                                    || ($editInterviewSchedulePermission == 'added' && $interview->added_by == user()->id)
                                    || ($editInterviewSchedulePermission == 'owned' && in_array(user()->id, $selected_employees))
                                    || ($editInterviewSchedulePermission == 'both' && (in_array(user()->id, $selected_employees) || $interview->added_by == user()->id)))
                                    @if ($interview->status != 'completed')
                                        <a href="javascript:;" data-status="completed" data-id="{{ $interview->id }}" class="change-interview-status p-1 rounded border text-dark-grey f-12 btn btn-primary">
                                            <i class="mr-1 fa fa-check"></i>@lang('recruit::modules.interviewSchedule.markStatusComplete')
                                        </a>
                                    @endif

                                    @php
                                        $secEmp = [];

                                        foreach($interview->employees as $usrdt){
                                            $secEmp[] = $usrdt->id;
                                        }

                                        $employeeStatus = $interview->employeesData->filter(function ($value, $key) use ($loggedEmployee)  {
                                            return $value->user_id == $loggedEmployee->id;
                                        })->first();
                                    @endphp

                                    @if (in_array($loggedEmployee->id, $secEmp) && $employeeStatus->user_accept_status == 'waiting' && $interview->status == 'pending')
                                        <br>
                                        <button class="btn-primary rounded f-12 p-1 employeeResponse mr-2 mt-2"
                                            data-response-id="{{ $employeeStatus->id }} "
                                                data-response-action="accept" href="javascript:;"><i class="fa fa-check mr-1"></i>@lang('recruit::modules.interviewSchedule.acceptInterview')
                                        </button>
                                        <button class="btn-secondary rounded f-12 p-1 employeeResponse mt-2"
                                            data-response-id="{{ $employeeStatus->id }}"
                                                data-response-action="refuse" href="javascript:;"><i class="fa fa-times mr-1"></i>@lang('recruit::modules.interviewSchedule.rejectInterview')
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2 col-2 text-right">
                        @if ( $editInterviewSchedulePermission == 'all'
                            || ($editInterviewSchedulePermission == 'added' && $interview->added_by == user()->id)
                            || ($editInterviewSchedulePermission == 'owned' && in_array(user()->id, $selected_employees))
                            || ($editInterviewSchedulePermission == 'both' && (in_array(user()->id, $selected_employees) || $interview->added_by == user()->id))
                            ||($reschedulePermission == 'all'
                            || ($reschedulePermission == 'added' && $interview->added_by == user()->id)
                            || ($reschedulePermission == 'owned' && in_array(user()->id, $selected_employees))
                            || ($reschedulePermission == 'both' && (in_array(user()->id, $selected_employees) || $interview->added_by == user()->id)))
                            || ($deleteInterviewSchedulePermission == 'all'
                            || ($deleteInterviewSchedulePermission == 'added' && $interview->added_by == user()->id)
                            || ($deleteInterviewSchedulePermission == 'owned' && in_array(user()->id, $selected_employees))
                            || ($deleteInterviewSchedulePermission == 'both' && (in_array(user()->id, $selected_employees) || $interview->added_by == user()->id))))
                            <div class="dropdown">
                                <button
                                    class="btn btn-lg f-14 px-2 py-1 text-dark-grey  rounded  dropdown-toggle"
                                    type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-ellipsis-h"></i>
                                </button>

                                <div class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                                        aria-labelledby="dropdownMenuLink" tabindex="0">
                                    @if ( $editInterviewSchedulePermission == 'all'
                                    || ($editInterviewSchedulePermission == 'added' && $interview->added_by == user()->id)
                                    || ($editInterviewSchedulePermission == 'owned' && in_array(user()->id, $selected_employees))
                                    || ($editInterviewSchedulePermission == 'both' && (in_array(user()->id, $selected_employees) || $interview->added_by == user()->id)))
                                        <a class="dropdown-item openRightModal"
                                            href="{{ route('interview-schedule.edit', $interview->id) }}">@lang('app.edit')</a>
                                    @endif

                                    @php
                                        $secEmp = [];
                                        foreach($interview->employees as $usrdt){
                                            $secEmp[] = $usrdt->id;
                                        }

                                        $employeeStatus = $interview->employeesData->filter(function ($value, $key) use ($loggedEmployee)  {
                                            return $value->user_id == $loggedEmployee->id;
                                        })->first();
                                    @endphp

                                    @if (in_array($loggedEmployee->id, $secEmp) && $employeeStatus->user_accept_status == 'waiting' && $interview->status == 'pending')
                                        <a class="dropdown-item employeeResponse"
                                            data-response-id={{ $employeeStatus->id }}
                                                data-response-action="accept" href="javascript:;">@lang('recruit::modules.interviewSchedule.acceptInterview')
                                        </a>
                                        <a class="dropdown-item employeeResponse"
                                            data-response-id={{ $employeeStatus->id }}
                                                data-response-action="reject" href="javascript:;">@lang('recruit::modules.interviewSchedule.rejectInterview')
                                        </a>
                                    @endif

                                    @if ($reschedulePermission == 'all'
                                    || ($reschedulePermission == 'added' && $interview->added_by == user()->id)
                                    || ($reschedulePermission == 'owned' && in_array(user()->id, $selected_employees))
                                    || ($reschedulePermission == 'both' && (in_array(user()->id, $selected_employees) || $interview->added_by == user()->id)))
                                        @if ($interview->status == 'pending')
                                            <a class="dropdown-item reschedule-interview"
                                                data-user-id="{{ $interview->id }}">@lang('recruit::modules.interviewSchedule.reSchedule')</a>
                                        @endif
                                    @endif
                                    @if ($deleteInterviewSchedulePermission == 'all'
                                    || ($deleteInterviewSchedulePermission == 'added' && $interview->added_by == user()->id)
                                    || ($deleteInterviewSchedulePermission == 'owned' && in_array(user()->id, $selected_employees))
                                    || ($deleteInterviewSchedulePermission == 'both' && (in_array(user()->id, $selected_employees) || $interview->added_by == user()->id)))
                                        <a class="dropdown-item delete-table-row"
                                            data-user-id="{{ $interview->id }}">@lang('app.delete')</a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="col-sm-12">
                        <x-cards.data-row otherClasses="text-right" :label="__('recruit::modules.interviewSchedule.startOn')"
                        :value="$interview->schedule_date->setTimeZone(company()->timezone)->format($company->date_format. ' - ' . $company->time_format)" />
                    </div>

                    <div class="col-sm-12">
                        <x-cards.data-row otherClasses="text-right" :label="__('recruit::modules.interviewSchedule.interviewType')"
                                      :value="ucwords($interview->interview_type) ?? '--'"/>
                    </div>

                    @if ($interview->interview_type == 'video')
                        <div class="col-sm-12">
                            @if ($interview->video_type == 'other')
                                <x-cards.data-row :label="__('recruit::modules.interviewSchedule.link')"  otherClasses="text-right"
                                                :value="$interview->other_link ?? '--'"/>
                            @endif

                            @if (in_array('Zoom', $worksuitePlugins))

                                @if ($interview->video_type == 'zoom')
                                    <x-cards.data-row :label="__('recruit::modules.interviewSchedule.meetingName')"
                                                    :value="$interview->meeting->meeting_name ?? '--'"/>

                                    <x-cards.data-row :label="__('recruit::modules.interviewSchedule.meetingStatus')"
                                                    :value="$interview->meeting->status ?? '--'"/>

                                    <x-cards.data-row :label="__('modules.employees.employeePassword')"
                                                    :value="$interview->meeting->password ?? '--'"/>

                                    <x-cards.data-row :label="__('recruit::modules.interviewSchedule.startOn')"
                                                    :value="$interview->schedule_date->setTimeZone(company()->timezone)->format($company->date_format. ' - ' . $company->time_format)"/>

                                    <x-cards.data-row :label="__('zoom::modules.zoommeeting.endOn')"
                                                    :value="$interview->meeting->end_date_time->format($company->date_format. ' - ' . $company->time_format)"/>

                                    <x-cards.data-row :label="__('zoom::modules.zoommeeting.hostVideoStatus')"
                                                    :value="$interview->meeting->host_video ? __('app.enabled') : __('app.disabled')"/>

                                    <div class="col-12 px-0 pb-3 d-flex">
                                        <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                            @lang('recruit::modules.interviewSchedule.meetingHost')</p>
                                        <p class="mb-0 text-dark-grey f-14">
                                        <div class="taskEmployeeImg rounded-circle mr-1">
                                            <img data-toggle="tooltip"
                                                data-original-title="{{ $interview->meeting->host->name }}"
                                                src="{{ $interview->meeting->host->image_url }}">
                                        </div>
                                        </p>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endif

                    @if ($interview->interview_type == 'phone')
                        <div class="col-sm-12">
                            <x-cards.data-row otherClasses='text-right' :label="__('recruit::modules.interviewSchedule.interviewerPhone')"
                                        :value="$interview->phone ?? '--'"/>
                        </div>
                    @endif

                    <div class="col-sm-12 f-13 mb-2">
                        @lang('recruit::modules.interviewSchedule.assignedEmployee')
                    </div>
                    <div class="col-sm-12 f-13">
                        @foreach ($interview->employeesData as $item)
                            <div class="d-flex justify-content-between mb-1">
                                <div>
                                    <x-employee :user="$item->user" />
                                </div>
                                <div class="text-right">
                                    @if ($item->user_accept_status == 'accept')
                                    <span class="badge badge-success float-right">@lang('recruit::modules.interviewSchedule.accepted')</span>
                                    @elseif($item->user_accept_status == 'refuse')
                                    <span class="badge badge-danger float-right">@lang('recruit::modules.interviewSchedule.refused')</span>
                                    @else
                                    <span class="badge badge-warning float-right text-white">@lang('recruit::modules.interviewSchedule.awaiting')</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @foreach($childInterviews as $childInterview)
                <div class="row mb-4 pb-4 border-bottom">
                    <div class="col-sm-12 d-flex justify-content-between">
                        <h4 class="heading-h5">{{ $childInterview->stage->name }}</h4>

                        <div class="text-right">
                            @if ($childInterview->status == 'pending')
                                <i class="fa fa-circle mr-1 text-yellow f-14"></i> @lang('recruit::modules.interviewSchedule.' . $childInterview->status)
                            @elseif ($childInterview->status == 'completed')
                                <i class="fa fa-circle mr-1 text-blue f-14"></i>@lang('recruit::modules.interviewSchedule.' . $childInterview->status)
                            @elseif ($childInterview->status == 'hired')
                                <i class="fa fa-circle mr-1 text-light-green f-14"></i>@lang('recruit::modules.interviewSchedule.' . $childInterview->status)
                            @elseif ($childInterview->status == 'rejected')
                                <i class="fa fa-circle mr-1 text-brown f-14"></i>@lang('recruit::modules.interviewSchedule.' . $childInterview->status)
                            @else
                                <i class="fa fa-circle mr-1 text-red f-14"></i>@lang('recruit::modules.interviewSchedule.' . $childInterview->status)
                            @endif
                        </div>
                    </div>

                    <div class="col-lg-10 col-10  my-2 mb-3">
                        @php
                            $selected_employees = $childInterview->employees->pluck('id')->toArray();
                        @endphp
                        @if($childInterview->status == 'hired' || $childInterview->status == 'completed')
                            @foreach ($selected_employees as $attendee)
                                @if (!in_array($attendee, $childInterview->evaluation->pluck('submitted_by')->toArray()) && $attendee == user()->id)
                                    <div class="d-flex flex-wrap">
                                        <a href="{{ route('evaluation.create', ['id' => $childInterview->id]) }}" class="openRightModal p-1 rounded border text-dark-grey f-12 btn btn-primary">
                                            <i class="mr-1 fa fa-plus"></i>@lang('app.add') @lang('recruit::modules.interviewSchedule.evaluation')
                                        </a>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                        <div class="row">
                            <div class="col-sm-12">
                                @if (in_array('Zoom', $worksuitePlugins))
                                    @if ($childInterview->video_type == 'zoom')
                                        @php
                                            if ($zoom_setting->meeting_app == 'in_app') {
                                                $url = route('zoom-meetings.start_meeting', $childInterview->meeting->id);
                                            } else {
                                                $url = user()->id == $childInterview->meeting->created_by ? $childInterview->meeting->start_link : $childInterview->meeting->join_link;
                                            }
                                            $nowDate = now(company()->timezone)->toDateString();
                                        @endphp
                                        <div class="col-md-2">
                                            @if (user()->id == $childInterview->meeting->created_by)
                                                @if ($childInterview->meeting->status == 'waiting')
                                                    @php
                                                        $nowDate = now(company()->timezone)->toDateString();
                                                        $meetingDate = $childInterview->meeting->start_date_time->toDateString();
                                                    @endphp

                                                    @if (isset($url) && (is_null($childInterview->meeting->occurrence_id) || $nowDate == $meetingDate))
                                                        <x-forms.link-primary target="_blank" :link="$url" icon="play">
                                                            @lang('recruit::modules.interviewSchedule.startInterview')
                                                        </x-forms.link-primary>
                                                    @endif

                                                @endif
                                            @else
                                                @if ($childInterview->meeting->status == 'waiting' || $childInterview->meeting->status == 'live')
                                                    @php
                                                        $nowDate = now(company()->timezone)->toDateString();
                                                        $meetingDate = $childInterview->meeting->start_date_time->toDateString();
                                                    @endphp

                                                    @if (isset($url) && (is_null($childInterview->meeting->occurrence_id) || $nowDate == $meetingDate))
                                                        <x-forms.link-primary target="_blank" :link="$url" icon="play">
                                                            @lang('recruit::modules.interviewSchedule.joinUrl')
                                                        </x-forms.link-primary>
                                                    @endif

                                                @endif

                                            @endif
                                        </div>
                                    @endif
                                @endif
                                @if ($editInterviewSchedulePermission == 'all'
                                    || ($editInterviewSchedulePermission == 'added' && $childInterview->added_by == user()->id)
                                    || ($editInterviewSchedulePermission == 'owned' && in_array(user()->id, $selected_employees))
                                    || ($editInterviewSchedulePermission == 'both' && (in_array(user()->id, $selected_employees) || $childInterview->added_by == user()->id)))
                                    @if ($childInterview->status != 'completed')
                                        <a href="javascript:;" data-status="completed" data-id="{{ $childInterview->id }}" class="change-interview-status p-1 rounded border text-dark-grey f-12 btn btn-primary">
                                            <i class="mr-1 fa fa-check"></i>@lang('recruit::modules.interviewSchedule.markStatusComplete')
                                        </a>
                                        @endif
                                    @php
                                        $secEmp = [];
                                        foreach($childInterview->employees as $usrdt){
                                            $secEmp[] = $usrdt->id;
                                        }

                                        $employeeStatus = $childInterview->employeesData->filter(function ($value, $key) use ($loggedEmployee)  {
                                            return $value->user_id == $loggedEmployee->id;
                                        })->first();
                                    @endphp

                                    @if (in_array($loggedEmployee->id, $secEmp) && $employeeStatus->user_accept_status == 'waiting' && $childInterview->status == 'pending')
                                        <br>
                                        <button class="btn-primary rounded f-12 p-1 employeeResponse mr-2 mt-2"
                                            data-response-id={{ $employeeStatus->id }}
                                                data-response-action="accept" href="javascript:;"><i class="fa fa-check mr-1"></i>@lang('recruit::modules.interviewSchedule.acceptInterview')
                                        </button>
                                        <button class="btn-secondary rounded f-12 p-1 employeeResponse mt-2"
                                            data-response-id={{ $employeeStatus->id }}
                                                data-response-action="refuse" href="javascript:;"><i class="fa fa-times mr-1"></i>@lang('recruit::modules.interviewSchedule.rejectInterview')
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2 col-2 text-right">
                        @if ( $editInterviewSchedulePermission == 'all'
                            || ($editInterviewSchedulePermission == 'added' && $childInterview->added_by == user()->id)
                            || ($editInterviewSchedulePermission == 'owned' && in_array(user()->id, $selected_employees))
                            || ($editInterviewSchedulePermission == 'both' && (in_array(user()->id, $selected_employees) || $childInterview->added_by == user()->id))
                            ||($reschedulePermission == 'all'
                            || ($reschedulePermission == 'added' && $childInterview->added_by == user()->id)
                            || ($reschedulePermission == 'owned' && in_array(user()->id, $selected_employees))
                            || ($reschedulePermission == 'both' && (in_array(user()->id, $selected_employees) || $childInterview->added_by == user()->id)))
                            || ($deleteInterviewSchedulePermission == 'all'
                            || ($deleteInterviewSchedulePermission == 'added' && $childInterview->added_by == user()->id)
                            || ($deleteInterviewSchedulePermission == 'owned' && in_array(user()->id, $selected_employees))
                            || ($deleteInterviewSchedulePermission == 'both' && (in_array(user()->id, $selected_employees) || $childInterview->added_by == user()->id))))
                            <div class="dropdown">
                                <button
                                    class="btn btn-lg f-14 px-2 py-1 text-dark-grey  rounded  dropdown-toggle"
                                    type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-ellipsis-h"></i>
                                </button>

                                <div class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                                     aria-labelledby="dropdownMenuLink" tabindex="0">
                                    @if ( $editInterviewSchedulePermission == 'all'
                                    || ($editInterviewSchedulePermission == 'added' && $childInterview->added_by == user()->id)
                                    || ($editInterviewSchedulePermission == 'owned' && in_array(user()->id, $selected_employees))
                                    || ($editInterviewSchedulePermission == 'both' && (in_array(user()->id, $selected_employees) || $childInterview->added_by == user()->id)))
                                        <a class="dropdown-item openRightModal"
                                           href="{{ route('interview-schedule.edit', $childInterview->id) }}">@lang('app.edit')</a>
                                    @endif

                                    @php
                                        $secEmp = [];
                                        foreach($childInterview->employees as $usrdt){
                                            $secEmp[] = $usrdt->id;
                                        }

                                        $employeeStatus = $childInterview->employeesData->filter(function ($value, $key) use ($loggedEmployee)  {
                                            return $value->user_id == $loggedEmployee->id;
                                        })->first();
                                    @endphp

                                    @if (in_array($loggedEmployee->id, $secEmp) && $employeeStatus->user_accept_status == 'waiting' && $interview->status == 'pending')
                                        <a class="dropdown-item employeeResponse"
                                           data-response-id={{ $employeeStatus->id }}
                                               data-response-action="accept" href="javascript:;">@lang('recruit::modules.interviewSchedule.acceptInterview')
                                        </a>
                                        <a class="dropdown-item employeeResponse"
                                           data-response-id={{ $employeeStatus->id }}
                                               data-response-action="reject" href="javascript:;">@lang('recruit::modules.interviewSchedule.rejectInterview')
                                        </a>
                                    @endif

                                    @if ($reschedulePermission == 'all'
                                    || ($reschedulePermission == 'added' && $childInterview->added_by == user()->id)
                                    || ($reschedulePermission == 'owned' && in_array(user()->id, $selected_employees))
                                    || ($reschedulePermission == 'both' && (in_array(user()->id, $selected_employees) || $childInterview->added_by == user()->id)))
                                        @if ($interview->status == 'pending')
                                            <a class="dropdown-item reschedule-interview"
                                               data-user-id="{{ $interview->id }}">@lang('recruit::modules.interviewSchedule.reSchedule')</a>
                                        @endif
                                    @endif

                                    @if ($deleteInterviewSchedulePermission == 'all'
                                    || ($deleteInterviewSchedulePermission == 'added' && $childInterview->added_by == user()->id)
                                    || ($deleteInterviewSchedulePermission == 'owned' && in_array(user()->id, $selected_employees))
                                    || ($deleteInterviewSchedulePermission == 'both' && (in_array(user()->id, $selected_employees) || $childInterview->added_by == user()->id)))
                                        <a class="dropdown-item delete-table-row" data-user-id="{{ $childInterview->id }}">@lang('app.delete')</a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="col-sm-12">
                        <x-cards.data-row otherClasses="text-right" :label="__('recruit::modules.interviewSchedule.startOn')"
                        :value="$childInterview->schedule_date->setTimeZone(company()->timezone)->format($company->date_format. ' - ' . $company->time_format)" />
                    </div>

                    <div class="col-sm-12">
                        <x-cards.data-row otherClasses="text-right" :label="__('recruit::modules.interviewSchedule.interviewType')"
                                      :value="ucwords($childInterview->interview_type) ?? '--'"/>
                    </div>

                    @if ($childInterview->interview_type == 'video')
                        <div class="col-sm-12">
                            @if ($childInterview->video_type == 'other')
                                <x-cards.data-row :label="__('recruit::modules.interviewSchedule.link')"  otherClasses="text-right"
                                                :value="$childInterview->other_link ?? '--'"/>
                            @endif

                            @if (in_array('Zoom', $worksuitePlugins))

                                @if ($childInterview->video_type == 'zoom')
                                    <x-cards.data-row :label="__('recruit::modules.interviewSchedule.meetingName')"
                                                    :value="$childInterview->meeting->meeting_name ?? '--'"/>

                                    <x-cards.data-row :label="__('recruit::modules.interviewSchedule.meetingStatus')"
                                                    :value="$childInterview->meeting->status ?? '--'"/>

                                    <x-cards.data-row :label="__('modules.employees.employeePassword')"
                                                    :value="$childInterview->meeting->password ?? '--'"/>

                                    <x-cards.data-row :label="__('recruit::modules.interviewSchedule.startOn')"
                                                    :value="$childInterview->schedule_date->setTimeZone(company()->timezone)->format($company->date_format. ' - ' . $company->time_format)"/>

                                    <x-cards.data-row :label="__('zoom::modules.zoommeeting.endOn')"
                                                    :value="$childInterview->meeting->end_date_time->format($company->date_format. ' - ' . $company->time_format)"/>

                                    <x-cards.data-row :label="__('zoom::modules.zoommeeting.hostVideoStatus')"
                                                    :value="$childInterview->meeting->host_video ? __('app.enabled') : __('app.disabled')"/>

                                    <div class="col-12 px-0 pb-3 d-flex">
                                        <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                            @lang('recruit::modules.interviewSchedule.meetingHost')</p>
                                        <p class="mb-0 text-dark-grey f-14">
                                        <div class="taskEmployeeImg rounded-circle mr-1">
                                            <img data-toggle="tooltip"
                                                data-original-title="{{ $childInterview->meeting->host->name }}"
                                                src="{{ $childInterview->meeting->host->image_url }}">
                                        </div>
                                        </p>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endif

                    @if ($interview->interview_type == 'phone')
                        <div class="col-sm-12">
                            <x-cards.data-row otherClasses="text-right" :label="__('recruit::modules.interviewSchedule.interviewerPhone')"
                                            :value="$interview->phone ?? '--'"/>
                        </div>
                    @endif

                    <div class="col-sm-12 f-13 mb-2">
                        @lang('recruit::modules.interviewSchedule.assignedEmployee')
                    </div>
                    <div class="col-sm-12 f-13">
                        @foreach ($childInterview->employeesData as $item)
                            <div class="d-flex justify-content-between mb-1">
                                <div>
                                    <x-employee :user="$item->user" />
                                </div>
                                <div class="text-right">
                                    @if ($item->user_accept_status == 'accept')
                                    <span class="badge badge-success float-right">@lang('recruit::modules.interviewSchedule.accepted')</span>
                                    @elseif($item->user_accept_status == 'refuse')
                                    <span class="badge badge-danger float-right">@lang('recruit::modules.interviewSchedule.refused')</span>
                                    @else
                                    <span class="badge badge-warning float-right text-white">@lang('recruit::modules.interviewSchedule.awaiting')</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
                @endforeach

            </x-cards.data>

        </div>
    </div>
</div>
<script>
    $(document).ready(function () {

        $('body').on('click', '.delete-table-row', function () {
            var id = $(this).data('user-id');
            var parentId = "{{ $interview->parent_id }}";
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
                                'parentId' : parentId,
                                '_method': 'DELETE'
                            },
                            success: function (response) {
                                if (response.status == 'success') {
                                    if ($(MODAL_XL).hasClass('show')) {
                                        $(MODAL_XL).modal('hide');
                                        window.location.reload();
                                    } else if ($(RIGHT_MODAL).hasClass('in')) {
                                        document.getElementById('close-task-detail').click();
                                        if ($('#interview-schedule-table').length) {
                                            window.LaravelDataTables["interview-schedule-table"].draw(true);
                                        } else {
                                            window.location.href = response.redirectUrl;
                                        }
                                    } else {
                                        window.location.href = response.redirectUrl;
                                    }
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
                                '_method': 'DELETE'
                            },
                            success: function (response) {
                                if (response.status == 'success') {
                                    if ($(MODAL_XL).hasClass('show')) {
                                        $(MODAL_XL).modal('hide');
                                        window.location.reload();
                                    } else if ($(RIGHT_MODAL).hasClass('in')) {
                                        document.getElementById('close-task-detail').click();
                                        if ($('#interview-schedule-table').length) {
                                            window.LaravelDataTables["interview-schedule-table"].draw(true);
                                        } else {
                                            window.location.href = response.redirectUrl;
                                        }
                                    } else {
                                        window.location.href = response.redirectUrl;
                                    }
                                }
                            }
                        });
                    }
                });
            }
        });

        $('body').on('click', '.employeeResponse', function () {

            var action = $(this).data('response-action');
            var responseId = $(this).data('response-id');
            var url = "{{ route('interview-schedule.employee_response') }}";

            if(action == 'accept'){
                var msg = "@lang('recruit::messages.acceptanceConfirm')";
            } else{
                var msg = "@lang('recruit::messages.rejectConfirm')";
            }

            Swal.fire({
                text: msg,
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
                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        blockUI: true,
                        data: {
                            'action': action,
                            'responseId': responseId,
                            '_token': '{{ csrf_token() }}'
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

        //    change status
        $('body').on('click', '.change-interview-status', function () {
            var status = $(this).data('status');
            var id = $(this).data('id');
            var url = "{{ route('interview-schedule.change_interview_status') }}";
            var token = "{{ csrf_token() }}";
            $.easyAjax({
                url: url,
                type: "POST",
                async: false,
                data: {
                    '_token': token,
                    interviewId: id,
                    status: status,
                    sortBy: 'id'
                },
                success: function (data) {
                    window.location.reload();
                }
            })
        });

        $('body').on('click', '.delete-file', function () {
            var id = $(this).data('row-id');
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
                        data: {
                            '_token': token,
                            '_method': 'DELETE'
                        },
                        success: function (response) {
                            if (response.status == "success") {
                                $('#task-file-list').html(response.view);
                            }
                        }
                    });
                }
            });
        });


        init(RIGHT_MODAL);
    });

    $('body').off('click', ".reschedule-interview").on('click', '.reschedule-interview', function () {
        var id = $(this).data('user-id');
        const url = "{{ route('interview-schedule.reschedule') }}?id=" + id;
        $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_LG, url);
    });

</script>
<script>
    $(".ajax-tab").click(function (event) {
        event.preventDefault();

        $('.task-tabs .ajax-tab').removeClass('active');
        $(this).addClass('active');

        const requestUrl = this.href;
        console.log(requestUrl);

        $.easyAjax({
            url: requestUrl,
            blockUI: true,
            container: "#nav-tabContent",
            historyPush: ($(RIGHT_MODAL).hasClass('in') ? false : true),
            data: {
                'json': true
            },
            success: function (response) {
                if (response.status == "success") {
                    $('#nav-tabContent').html(response.html);
                }
            }
        });
    });
</script>
