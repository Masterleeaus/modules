@extends('layouts.app')

<script src="{{ asset('vendor/jquery/Chart.min.js') }}"></script>


@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

@push('styles')
    <style>
        .h-200 {
            height: 340px;
            overflow-y: auto;
        }

        .dashboard-settings {
            width: 600px;
        }

        @media (max-width: 768px) {
            .dashboard-settings {
                width: 300px;
            }
        }

        .border-1 {
            background: #ECEFF34D;
            border-radius: 2px;
            padding: 16px;
        }

        .badge-light {
            font-size: 11.5px;
        }

        .table thead th{
            text-align: left !important;
        }

        .column-width-title {
            width: 140px;
        }

        .column-width{
            width:150px;
        }

    </style>
@endpush
@php
    $viewJobPermission = user()->permission('view_job');
    $viewInterviewPermission = user()->permission('view_interview_schedule');
@endphp
@section('content')
    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper">
        <div class="row row-cols-lg-4 my-3">

            <div class="col mb-4">
                <a href="{{ route('jobs.index') }}" data-status="closed" class="widget-filter-status">
                    <x-cards.widget :title="__('recruit::app.dashboard.totalOpenings')"
                                    value="{{ $totalOpenings }}" icon="ticket-alt" widgetId="closedTickets" :info="__('recruit::app.dashboard.totalActiveJobs')"/>
                </a>
            </div>

            <div class="col mb-4">
                <a href="{{ route('job-appboard.index') }}" data-status="open" class="widget-filter-status">
                    <x-cards.widget :title="__('recruit::app.dashboard.totalApplications')"
                                    value="{{ $totalApplications }}" icon="ticket-alt" widgetId="openTickets"/>
                </a>
            </div>

            <div class="col mb-4">
                <a href="{{ route('job-appboard.index') }}" data-status="pending" class="widget-filter-status">
                    <x-cards.widget :title="__('recruit::app.dashboard.totalHired')" value="{{ $totalHired }}"
                                    icon="ticket-alt"
                                    widgetId="pendingTickets"/>
                </a>
            </div>

            <div class="col mb-4">
                <a href="{{ route('job-appboard.index') }}" data-status="resolved" class="widget-filter-status">
                    <x-cards.widget :title="__('recruit::app.dashboard.totalRejected')" value="{{ $totalRejected }}"
                                    icon="ticket-alt"
                                    widgetId="resolvedTickets"/>
                </a>
            </div>
            <div class="col mb-4">
                <a href="{{ route('job-appboard.index') }}" data-status="resolved" class="widget-filter-status">
                    <x-cards.widget :title="__('recruit::app.dashboard.newApplications')" value="{{ $newApplications }}"
                                    icon="ticket-alt"
                                    :info="__('recruit::app.dashboard.totalNewApp')"
                                    widgetId="resolvedTickets"/>
                </a>
            </div>
            <div class="col mb-4">
                <a href="{{ route('job-appboard.index') }}" data-status="resolved" class="widget-filter-status">
                    <x-cards.widget :title="__('recruit::app.dashboard.shortlistedCandidate')"
                                    value="{{ $shortlisted }}" icon="ticket-alt"
                                    widgetId="resolvedTickets"/>
                </a>
            </div>
            <div class="col mb-4">
                <a href="{{ route('interview-schedule.index') }}" data-status="resolved" class="widget-filter-status">
                    <x-cards.widget :title="__('recruit::app.dashboard.todayInterview')"
                                    value="{{ $totalTodayInterview }}" icon="ticket-alt"
                                    widgetId="resolvedTickets"/>
                </a>
            </div>

        </div>

        <div class="row">

            <div class="col-sm-12 col-lg-12">
                <x-cards.data :title="__('recruit::modules.jobApplication.totalApplications')">
                    <div class="d-flex flex-column w-tables rounded bg-white table-responsive">

                        {!! $dataTable->table(['class' => 'table table-hover border-0 w-100 mt-3 mb-3']) !!}

                    </div>
                </x-cards.data>
            </div>

            <div class="col-sm-12 col-lg-6 mt-3">
                <x-cards.data :title="__('recruit::app.dashboard.todayInterview')" padding="false" otherClasses="h-200">
                    <x-table>
                        @forelse ($todaysInterview as $item)
                            @if($viewInterviewPermission != 'none')
                                <tr>
                                    @php
                                        $secEmp = [];
                                            foreach($item->employees as $usrdt){
                                                $secEmp[] = $usrdt->id;
                                            }

                                            $employeeStatus = $item->employeesData->filter(function ($value, $key) use ($loggedEmployee)  {
                                                return $value->user_id == $loggedEmployee->id;
                                            })->first();
                                    @endphp
                                    @if ($viewInterviewPermission == 'all'
                                    || ($viewInterviewPermission == 'added' && $item->added_by == user()->id)
                                    || ($viewInterviewPermission == 'owned' && in_array($loggedEmployee->id, $secEmp))
                                    || ($viewInterviewPermission == 'both' && (in_array($loggedEmployee->id, $secEmp)) || $item->added_by == user()->id))
                                        <td class="pl-20">
                                            <h5 class="f-13 text-darkest-grey">
                                                @if ($viewInterviewPermission == 'all'
                                                || ($viewInterviewPermission == 'added' && $item->added_by == user()->id)
                                                || ($viewInterviewPermission == 'owned' && in_array($loggedEmployee->id, $secEmp)
                                                || ($viewInterviewPermission == 'both' && (in_array($loggedEmployee->id, $secEmp)) || $item->added_by == user()->id)))
                                                    <a href="{{ route('interview-schedule.show', [$item->id]) }}"
                                                       class="openRightModal">{{ $item->jobApplication ? ($item->jobApplication->full_name) : ''}}</a>
                                                @else
                                                    <a href="javascript:;">{{ $item->jobApplication ? ($item->jobApplication->full_name) : '' }}</a>
                                                @endif
                                            </h5>
                                            <div
                                                class="text-muted">{{ $item->jobApplication ? ($item->jobApplication->job->title) : '' }}</div>
                                        </td>
                                        <td class="text-darkest-grey">{{ $item->schedule_date->format(company()->date_format) }}</td>
                                        <td>
                                            @php
                                                if ($item->status == 'pending') {
                                                    $priority = 'yellow';
                                                } elseif ($item->status == 'hired') {
                                                    $priority = 'light-green';
                                                } elseif ($item->status == 'canceled') {
                                                    $priority = 'red';
                                                } elseif ($item->status == 'rejected') {
                                                    $priority = 'brown';
                                                } elseif ($item->status == 'completed') {
                                                    $priority = 'blue';
                                                } else {
                                                    $priority = 'black';
                                                }
                                            @endphp
                                            <x-status :color="$priority"
                                                      :value="__('recruit::app.interviewSchedule.' . $item->status)"/>
                                        </td>

                                        <td align="right" class="pr-20">
                                            @if ($viewInterviewPermission == 'all'
                                                || ($viewInterviewPermission == 'added' && $item->added_by == user()->id)
                                                || ($viewInterviewPermission == 'owned' && in_array($loggedEmployee->id, $secEmp)
                                                || ($viewInterviewPermission == 'both' && (in_array($loggedEmployee->id, $secEmp)) || $item->added_by == user()->id)))
                                                <div class="task_view">
                                                    <a href="{{ route('interview-schedule.show', [$item->id]) }}"
                                                       class="taskView openRightModal">@lang('app.view')</a>
                                                </div>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="4" class="shadow-none">
                                    <x-cards.no-record icon="calendar" :message="__('messages.noRecordFound')"/>
                                </td>
                            </tr>
                        @endforelse
                    </x-table>
                </x-cards.data>
            </div>


            <div class="col-sm-12 col-lg-6 mt-3">
                <x-cards.data :title="__('recruit::app.dashboard.openJobs')" padding="false" otherClasses="h-200">
                    <x-table>
                        @forelse ($activeJobs as $item)
                            <tr>
                                <td class="pl-20">
                                    <h5 class="f-13 text-darkest-grey">
                                        <a href="{{ route('jobs.show', [$item->id]) }}"> {{ ($item->title) }} </a>
                                    </h5>
                                </td>
                                <td class="text-darkest-grey">{{ $item->end_date ? $item->end_date->format(company()->date_format) : __('recruit::modules.job.noEndDate') }}</td>
                                <td>
                                    <x-employee :user="$item->recruiter"/>
                                </td>
                                <td align="right" class="pr-20">
                                    @if($viewJobPermission == 'all'
                                        || ($viewJobPermission == 'added' && $item->added_by == user()->id)
                                        || ($viewJobPermission == 'owned' && user()->id == $item->recruiter_id)
                                        || ($viewJobPermission == 'both' && user()->id == $item->recruiter_id)
                                        || $item->added_by == user()->id)
                                        <div class="task_view">
                                            <a href="{{ route('jobs.show', [$item->id]) }}"
                                               class="taskView">@lang('app.view')</a>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="shadow-none">
                                    <x-cards.no-record icon="tasks" :message="__('messages.noRecordFound')"/>
                                </td>
                            </tr>
                        @endforelse
                    </x-table>
                </x-cards.data>
            </div>


            <div class="col-sm-12 col-lg-6 mt-3">
                <x-cards.data :title="__('recruit::app.dashboard.source')">
                    <x-pie-chart id="task-chart" :labels="$applicationSourceWise['labels']"
                                 :values="$applicationSourceWise['values']" :colors="$applicationSourceWise['colors']"
                                 width="300"/>
                </x-cards.data>
            </div>
            <div class="col-sm-12 col-lg-6 mt-3">
                <x-cards.data :title="__('recruit::app.dashboard.applicationStatus')">
                    <x-pie-chart id="task-chart-1" :labels="$candidateStatusWise['labels']"
                                 :values="$candidateStatusWise['values']" :colors="$candidateStatusWise['colors']"
                                 width="300"/>
                </x-cards.data>
            </div>

        </div>

    </div>
@endsection
@push('scripts')
    @include('sections.datatable_js')

    <script>
        const showTable = () => {
            window.LaravelDataTables["pipeline-widget-table"].draw(true);
        }

    </script>
@endpush

