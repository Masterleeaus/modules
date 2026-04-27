<div class="job-right position-relative">
    <div class="bg-white sticky-top py-3 px-4 d-flex justify-content-between align-items-center">
        <h4 class="mb-0">
            <a href="{{ route('job_detail_page',[$job->slug, $jobLocation->id, $company->hash]) }}" class="text-dark">
                {{ $job->title }}
            </a>
        </h4>
        <div class="mt-3 mt-lg-0 mt-md-0">
            <div class="row">
                <div class="col-md-6">
                    <div class="dropdown">
                        <x-forms.button-secondary class="dropdown-toggle" id="dropdownMenuButton"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">@lang('recruit::modules.front.shareLink')
                        </x-forms.button-secondary>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" target="_blank" href='https://wa.me/?text={{ route('job_apply',[$job->slug, $jobLocation->id, $company->hash]) }}'><i class="fab fa-whatsapp mr-2"></i> @lang('recruit::modules.front.shareOnWhatsapp')</a>
                            
                            <a class="dropdown-item btn-copy " data-clipboard-text="{{ route('job_apply',[$job->slug, $jobLocation->id, $company->hash]) }}"> <i class="fa fa-copy mr-2"></i> @lang('recruit::modules.front.copyLink')</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    @if($job->slug != null || $jobLocation->id != null)
                        <a href="{{ route('job_apply',[$job->slug, $jobLocation->id, $company->hash]) }}" class="btn btn-primary f-13"
                        data-toggle="tooltip"
                        data-original-title="@lang('recruit::modules.front.apply')"><i
                                class="fa fa-briefcase mr-1"></i>@lang('recruit::modules.front.apply')</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="location_id{{$jobLocation->id}}" id="location_id" value="{{ $jobLocation->id }}">
    <div class="px-4">
        <span class="text-darkest-grey f-13">
            <i class="fa fa-suitcase" aria-hidden="true"></i> {{ $job->workExperience->work_experience }}
        </span>
        <span class="text-darkest-grey f-13">
            <i class="ml-1 mr-1 fa fa-map-marker"></i>{{ $jobLocation->location }}
        </span>
        @if ($job->remote_job == 'yes')
            <span class="badge badge-pill badge-dark border ml-1">@lang('recruit::modules.front.remoteJob')</span>
        @endif

        <div class="gap-multiline-items-1 mt-2 f-13">
            @if($job->disclose_salary == 'yes')
                @if($job->pay_type == 'Starting')
                    {{ currency_format($job->start_amount, $job->currency->id) }}
                @elseif ($job->pay_type == 'Maximum')
                    {{ currency_format($job->start_amount, $job->currency->id) }}
                @elseif ($job->pay_type == 'Exact Amount')
                    {{ currency_format($job->start_amount, $job->currency->id) }}
                @elseif ($job->pay_type == 'Range')
                {{ currency_format($job->start_amount, $job->currency->id) }} - {{ currency_format($job->end_amount, $job->currency->id) }}
                @endif
            @else
                @lang('recruit::modules.job.salaryDisclosed')
            @endif
        </div>
        <h6 class="mt-4 heading-h6">@lang('recruit::modules.front.skill')</h6>

        <div class="gap-multiline-items-1 mt-2">
            @foreach ($job->skills as $job_skill)
                <span>{{ $job_skill->skill->name }} @if(!$loop->last) &bull; @endif</span>
            @endforeach
        </div>

        @if($job->job_description)
            <h6 class="mt-4 heading-h6 f-14">@lang('recruit::modules.front.description')</h6>
            <div class="col-12 px-0 pb-3 d-lg-flex d-md-flex d-block">
                <div
                    class="mb-0 f-13 text-wrap ql-editor">{!! nl2br($job->job_description ?? '--') !!}</div>
            </div>
        @else
            <p class="mb-0"></p>
        @endif
    </div>

</div>
