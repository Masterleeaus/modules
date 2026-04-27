@if ($messageforAdmin != null)
    <!-- Alert to admin Start -->
    <div class="row alert">
        <div class="col-md-12 mb-3">
            <div class="bg-white rounded overflow-auto border-grey">
                <div class="col-md-12 mt-3 pb-4 success-message">
                    <p class="text-dark-grey mb-0 text-justify">{{ $messageforAdmin }}</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Alert to admin End -->
@else
    @extends('recruit::layouts.front')
    <style>
        .gap-multiline-items-1{
            font-size: 14px;
            padding: 4px;
        }

        .text-dark-grey {
            margin-right: 6px;
        }

        .topDiv{
            flex-direction: column;
        }

        .front-background {
            background-color: #F2F4F7;
        }

        .site-footer {
            font-size: 0.75rem;
            border-top: 1px solid #f1f2f3;
            padding: 20px 0;
            position: absolute;
        }
    </style>
    <body>
    @section('content')

        <!-- Header Start -->
        <header class="sticky-top bg-white">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 py-2 front_header d-flex justify-content-between align-items-center">
                        <a href="{{ url('/careers', $company->hash) }}">
                            <img class="mr-2 rounded" src="{{ $company->logo_url }}">
                        </a>
                        <h3 class="mb-0 pl-1 heading-h3">{{ $companyName }}</h3>
                        @if (auth()->user() && user()->permission('view_dashboard') === 'all')
                        <x-forms.link-secondary :link="route('recruit-dashboard.index')" class="mb-2 mb-lg-0 mb-md-0">
                            @lang('recruit::app.menu.goToDashboard')
                        </x-forms.link-secondary>
                        @elseif ($setting->job_alert_status != 'no')
                            <x-forms.button-primary class="mb-2 mb-lg-0 mb-md-0" id="job-alter-create">
                                @lang('recruit::modules.front.createJobAlert')
                            </x-forms.button-primary>
                        @else
                            <div class="mb-2 mb-lg-0 mb-md-0">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </header>
        <!-- Header End -->
        <!-- Content Start -->
        <section class="front-background">
            <div class="container">
                <div class="row">

                    <div class="col-sm-12 col-lg-4">
                        <div class="bg-white rounded overflow-auto border-grey">
                            <h4 class="mb-0 p-20 f-18  border-bottom-grey">@lang('recruit::modules.joboffer.jobDetails')</h4>
                            <div class="col-md-12 mt-3 pb-4">
                                <div class="gap-multiline-items-1 mt-2">
                                    <i class="fa fa-suitcase text-dark-grey" aria-hidden="true"></i> {{ $job->workExperience->work_experience }} - {{ $job->jobType->job_type }}
                                </div>

                                <div class="gap-multiline-items-1 mt-2">
                                    <i class="fa fa-users text-dark-grey" aria-hidden="true"></i> @lang('recruit::modules.front.totalOpenings') - {{ $job->total_positions }}
                                </div>

                                <div class="gap-multiline-items-1 mt-2">
                                    <i class="fa fa-building text-dark-grey" aria-hidden="true"></i> @lang('app.department') - {{ $job->team->team_name }}
                                </div>

                                <div class="gap-multiline-items-1 mt-2">
                                    <i class="bi bi-cassette-fill text-dark-grey" aria-hidden="true"></i> @lang('app.category') - {{ $job->category->category_name }}
                                </div>

                                <div class="gap-multiline-items-1 mt-2">
                                    <i class="bi bi-person-badge-fill text-dark-grey" aria-hidden="true"></i> @lang('recruit::modules.job.subCategory') - {{ $job->subcategory->sub_category_name }}
                                </div>

                                <div class="gap-multiline-items-1 mt-2">
                                    <i class="fa fa-credit-card text-dark-grey" aria-hidden="true"></i> @lang('recruit::modules.job.payAccording') - @lang('recruit::modules.joboffer.payAcc') {{ $job->pay_according }}
                                </div>

                                <div class="gap-multiline-items-1 mt-2">
                                    <i class="bi bi-cash-stack text-dark-grey" aria-hidden="true"></i>
                                    @if($job->disclose_salary == 'yes')
                                        @if($job->pay_type == 'Starting')
                                            @lang('recruit::modules.front.salaryOffered')
                                            {{ currency_format($job->start_amount, $job->currency->id) }}
                                        @elseif ($job->pay_type == 'Maximum')
                                            @lang('recruit::modules.front.salaryOffered')
                                            {{ currency_format($job->start_amount, $job->currency->id) }}
                                        @elseif ($job->pay_type == 'Exact Amount')
                                            @lang('recruit::modules.front.salaryOffered')
                                            {{ currency_format($job->start_amount, $job->currency->id) }}
                                        @elseif ($job->pay_type == 'Range')
                                            @lang('recruit::modules.front.salaryOffered')
                                            {{ currency_format($job->start_amount, $job->currency->id) }} -
                                            {{ currency_format($job->end_amount, $job->currency->id) }}
                                        @endif
                                    @else
                                        @lang('recruit::modules.job.salaryDisclosed')
                                    @endif
                                </div>

                                <div class="gap-multiline-items-1 mt-3">
                                    @if($job->slug != null || $jobLocation->id != null)
                                        <a href="{{ route('job_apply',[$job->slug, $jobLocation->id, $company->hash]) }}" class="btn btn-primary f-14"
                                        data-toggle="tooltip"
                                        data-original-title="@lang('recruit::modules.front.apply')"><i
                                                class="fa fa-briefcase mr-1"></i>@lang('recruit::modules.front.apply')</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Overview End -->
                    <div class="col-sm-12 col-lg-8">
                        <div class="job-container">
                            <div class="job-right position-relative">
                                <div class="bg-white py-3 px-4 d-flex justify-content-between align-items-center">
                                    <div class="d-flex topDiv">
                                        <h4 class="softwareText">{{ $job->title }}</h4>
                                        <span class="f-13"><i class="ml-1 mr-1 fa fa-map-marker"></i> {{$jobLocation->location}}
                                            @if ($job->remote_job == 'yes')
                                                <span class="badge badge-pill badge-dark border ml-1">@lang('recruit::modules.front.remoteJob')</span>
                                            @endif
                                        </span>

                                    </div>
                                    <div class="mt-3 mt-lg-0 mt-md-0 text-right">
                                        <div class="dropdown">
                                            <x-forms.button-secondary class="dropdown-toggle" id="dropdownMenuButton"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-share-alt mr-1" aria-hidden="true"></i>@lang('recruit::modules.front.shareLink')
                                            </x-forms.button-secondary>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <a class="dropdown-item" target="_blank" href='https://wa.me/?text={{ route('job_apply',[$job->slug, $jobLocation->id, $company->hash]) }}'><i class="fab fa-whatsapp mr-2"></i> @lang('recruit::modules.front.shareOnWhatsapp')</a>

                                                <a class="dropdown-item btn-copy " data-clipboard-text="{{ route('job_apply',[$job->slug, $jobLocation->id, $company->hash]) }}"> <i class="fa fa-copy mr-2"></i> @lang('recruit::modules.front.copyLink')</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="px-4">
                                    <h6 class="mt-2 heading-h6"><b>@lang('recruit::modules.front.skill')</b></h6>

                                    <div class="gap-multiline-items-1 mt-2">
                                        @foreach ($job->skills as $job_skill)
                                            <span class="badge badge-pill badge-light border">{{ $job_skill->skill->name }}</span>
                                        @endforeach
                                    </div>
                                    @if($job->job_description)
                                        <h6 class="mt-3 heading-h6"><b>@lang('recruit::modules.front.description')</b></h6>

                                        <div class="col-12 px-0 pb-3 d-lg-flex d-md-flex d-block">
                                            <div
                                                class="mb-0 ql-editor">{!! nl2br($job->job_description ?? '--') !!}</div>
                                        </div>
                                    @else
                                        <p class="mb-0"></p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>


    @endsection
    <!-- Content End -->

    </body>
@endif
