@php
    $moveClass = '';
@endphp
@if ($draggable == 'false')
    @php
        $moveClass = 'move-disable';
    @endphp
@endif

<div class="card rounded bg-white border-grey b-shadow-4 m-1 mb-3 {{ $moveClass }} task-card"
     data-app-id="{{ $application->id }}" id="drag-task-{{ $application->id }}">
    <div class="card-body p-2">
        <div class="d-flex justify-content-between mb-2">
            <div class="d-flex flex-wrap align-items-center">
                <div class="avatar-img mr-2 rounded-circle">
                    <a href="{{ route('job-applications.show', [$application->id]) }}" class="openRightModal" alt="{{ $application->full_name }}"
                       data-toggle="tooltip" data-original-title="{{ $application->full_name }}"
                       data-placement="right"><img src="{{ $application->image_url }}"></a>
                </div>
                <a href="{{ route('job-applications.show', [$application->id]) }}"
                   class="f-12 f-w-500 text-dark mb-0 text-wrap openRightModal">{{ ($application->full_name) }}</a>
            </div>

            @if ($draggable != 'false')
                <a href="javascript:;" id="drag-app-{{ $application->id }}" data-app-id="{{ $application->id }}" data-toggle="tooltip" data-original-title="@lang('recruit::modules.jobApplication.rejectApplication')" class="text-red close-application"><i class="fa fa-times"></i></a>
            @endif

        </div>

        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex flex-wrap align-items-center">
                <h6 class="text-dark-grey f-12"><i class="bi bi-envelope"></i> {{ $application->email ? $application->email : '--' }}</h6>
            </div>
            <div class="d-flex flex-wrap align-items-center">
                <h6 class="text-dark-grey f-12"><i class="bi bi-telephone"></i> {{ $application->phone ? $application->phone : '--' }}</h6>
            </div>
        </div>

        @if ($application->applicationStatus->recruit_application_status_category_id == 2 && $application->remark != '')
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex flex-wrap align-items-center">
                    <h6 class="text-lightest f-12">{{ $application->remark }}</h6>
                </div>
            </div>
        @endif

        @if ($application->applicationStatus->recruit_application_status_category_id == 5 && $application->rejection_remark != '')
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex flex-wrap align-items-center">
                    <h6 class="text-lightest f-12">{{ $application->rejection_remark }}</h6>
                </div>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center">
            @if ($application->job_id)
                <div class="d-flex align-items-center">
                    <i class="fa fa-layer-group f-11 text-lightest"></i>
                    <span
                        class="ml-2 f-12 text-lightest">{{ ($application->job->title) }}</span>
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center">
                @if (!is_null($application->created_at))
                    @if ($application->created_at->endOfDay()->isPast())
                        <div class="d-flex text-red">
                            <i class="f-11 mt-1 bi bi-calendar align-items-center"></i><span
                                class="f-11 ml-1">{{ $application->created_at->format(global_setting()->date_format) }}</span>
                        </div>
                    @elseif($application->created_at->setTimezone(company()->timezone)->isToday())
                        <div class="d-flex text-dark-green align-items-center">
                            <i class="fa fa-calendar-alt f-11"></i><span class="f-12 ml-1">@lang('app.today')</span>
                        </div>
                    @else
                        <div class="d-flex text-lightest align-items-center">
                            <i class="fa fa-calendar-alt f-11"></i><span
                                class="f-11 ml-1">{{ $application->created_at->format(company()->date_format) }}</span>
                        </div>
                    @endif
                @endif
            </div>

        </div>

    </div>
</div><!-- div end -->
