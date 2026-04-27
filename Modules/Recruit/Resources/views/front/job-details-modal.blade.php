<div class="modal-header">
    <h5 class="modal-title"
        id="modelHeading">{{ $job->title }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <section class="py-3">
            <div class="container">
                <div class="row">
                    <div class="col-12 px-0 pb-3 d-lg-flex d-md-flex d-block">
                        <div class="row">
                            <p class="col-6 mb-0 text-lightest f-14 w-30 ">@lang('app.category')</p>
                            <p class="col-6 mb-0 text-dark-grey f-14 w-70 text-wrap">{{ $job->category ?$job->category->category_name : '--' }}</p>
                        </div>
                    </div>
                    <div class="col-12 px-0 pb-3 d-lg-flex d-md-flex d-block">
                        <div class="row">
                            <p class="col-6 mb-0 text-lightest f-14 w-30 ">@lang('app.category')</p>
                            <p class="col-6 mb-0 text-dark-grey f-14 w-70 text-wrap">{{ $job->subcategory ?$job->subcategory->sub_category_name : '--'}}</p>
                        </div>
                    </div>
                    <div class="col-12 px-0 pb-3 d-lg-flex d-md-flex d-block">
                        <div class="row">
                            <p class="col-6 mb-0 text-lightest f-14 w-30 ">@lang('recruit::modules.job.subCategory')</p>
                            <p class="col-6 mb-0 text-dark-grey f-14 w-70 text-wrap">{{ $job->subcategory ?$job->subcategory->sub_category_name : '--'}}</p>
                        </div>
                    </div>
                    <div class="col-12 px-0 pb-3 d-lg-flex d-md-flex d-block">
                        <div class="row">
                            <p class="col-6 mb-0 text-lightest f-14 w-30 ">@lang('app.department')</p>
                            <p class="col-6 mb-0 text-dark-grey f-14 w-70 text-wrap">{{ $job->team ? $job->team->team_name : '--'}}</p>
                        </div>
                    </div>
                    <div class="col-12 px-0 pb-3 d-lg-flex d-md-flex d-block">
                        <div class="row">
                            <p class="col-6 mb-0 text-lightest f-14 w-30 ">@lang('recruit::modules.job.totalOpening')</p>
                            <p class="col-6 mb-0 text-dark-grey f-14 w-70 text-wrap">{{ $job->total_positions ?? '--'}}</p>
                        </div>
                    </div>
                    <div class="col-12 px-0 pb-3 d-lg-flex d-md-flex d-block">
                        <div class="row">
                            <p class="col-6 mb-0 text-lightest f-14 w-30 ">@lang('recruit::modules.job.endDate')</p>
                            <p class="col-6 mb-0 text-dark-grey f-14 w-70 text-wrap">{{ $job->end_date ? $job->end_date->format($company->date_format) : __('recruit::modules.job.noEndDate')}}</p>
                        </div>
                    </div>
                    <div class="col-12 px-0 pb-3 d-lg-flex d-md-flex d-block">
                        <div class="row">
                            <p class="col-6 mb-0 text-lightest f-14 w-30 ">@lang('recruit::modules.joboffer.workExperience')</p>
                            <p class="col-6 mb-0 text-dark-grey f-14 w-70 text-wrap">{{ $job->workExperience->work_experience }}</p>
                        </div>
                    </div>
                    <div class="col-12 px-0 pb-3 d-lg-flex d-md-flex d-block">
                        <div class="row">
                            <p class="col-6 mb-0 text-lightest f-14 w-30 ">@lang('recruit::modules.jobApplication.jobType')</p>
                            <p class="col-6 mb-0 text-dark-grey f-14 w-70 text-wrap">{{ ($job->job_type) }}</p>
                        </div>
                    </div>
                    @if ($job->remote_job == 'yes')
                        <div class="col-12 px-0 pb-3 d-lg-flex d-md-flex d-block">
                            <div class="row">
                                <p class="col-6 mb-0 text-lightest f-14 w-30 ">@lang('recruit::modules.job.payAccording')</p>
                                <p class="col-6 mb-0 text-dark-grey f-14 w-70 text-wrap">@lang('recruit::modules.joboffer.payAcc') {{ ($job->remote_job) }}</p>
                            </div>
                        </div>
                    @endif

                    @if($job->disclose_salary == 'yes')
                        <div class="col-12 px-0 pb-3 d-lg-flex d-md-flex d-block">
                            <div class="row">
                                <p class="col-6 mb-0 text-lightest f-14 w-30 ">@lang('recruit::modules.front.salaryOffered')</p>
                                <p class="col-6 mb-0 text-dark-grey f-14 w-70 text-wrap">
                                @if($job->pay_type == 'Starting')
                                    {{ currency_format($job->start_amount, $job->currency->id) }}
                                @elseif ($job->pay_type == 'Maximum')
                                    {{ currency_format($job->start_amount, $job->currency->id) }}
                                @elseif ($job->pay_type == 'Exact Amount')
                                    {{ currency_format($job->start_amount, $job->currency->id) }}
                                @elseif ($job->pay_type == 'Range')
                                    {{ currency_format($job->start_amount, $job->currency->id) }} -
                                    {{ currency_format($job->end_amount, $job->currency->id) }}
                                @endif
                                </p>
                            </div>
                        </div>
                    @else
                        @lang('recruit::modules.job.salaryDisclosed')
                    @endif

                    <div class="col-12 px-0 pb-3 d-lg-flex d-md-flex d-block">
                        <div class="row">
                            <p class="col-6 mb-0 text-lightest f-14 w-30 ">@lang('recruit::modules.front.remoteJob')</p>
                            <p class="col-6 mb-0 text-dark-grey f-14 w-70 text-wrap">{{ ($job->pay_according) }}</p>
                        </div>
                    </div>

                    @if($job->skills)
                        <div class="col-12 px-0 pb-3 d-lg-flex d-md-flex d-block">
                            <div class="row">
                                <p class="col-6 mb-0 text-lightest f-14 w-30 ">@lang('recruit::modules.front.skill')</p>
                                <p class="col-6 mb-0 text-dark-grey f-14 w-70 text-wrap">
                                    @foreach ($job->skills as $job_skill)
                                        <span class="badge badge-pill badge-light border">{{ $job_skill->skill->name }}</span>
                                    @endforeach</p>
                            </div>
                        </div>
                    @endif

                    @if($job->job_description)
                        <div class="col-12 px-0 pb-3 d-lg-flex d-md-flex d-block">
                            <p class="mb-0 text-lightest f-14 w-30 ">@lang('recruit::modules.front.description')</p>
                            <div class="mb-0 f-13 text-wrap ql-editor">{!! nl2br($job->job_description ?? '--') !!}</div>
                        </div>
                    @endif

                    <div class="col-md-12">
                        <div class="gap-multiline-items-1">
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
        </section>
    </div>
</div>
