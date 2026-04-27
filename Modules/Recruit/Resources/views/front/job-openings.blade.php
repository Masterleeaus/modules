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
        .active {
            background: #E3E9F0 !important;

        }

        .front-background {
            background-color: #F2F4F7;
        }

        a.text-dark:hover {
            text-decoration: underline;
        }
    </style>
    <!-- Header Start -->
    @section('content')

        <header class="sticky-top bg-white">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 py-2 front_header d-flex justify-content-between align-items-center">
                        <a href="{{ url('/careers', $company->hash) }}">
                            <img class="mr-2 rounded" src="{{ $company->logo_url }}">
                        </a>
                        <h3 class="mb-0 pl-1 heading-h3">{{ $company->company_name }}</h3>
                        @if (auth()->user() && user()->permission('view_dashboard') === 'all')
                        <x-forms.link-secondary :link="route('recruit-dashboard.index')"
                                                    class="mb-2 mb-lg-0 mb-md-0">
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
        <section class="front-background py-3">
            <div class="container">
                <div class="job-container">
                    <div class="p-2 col-md-12">
                        <x-form id="fetch-job-filter-form">
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="f-14 text-dark-grey " for="usr">@lang('app.department')</label>
                                    <select class="form-control select-picker" name="department_id" data-container="body"
                                            id="department_id">
                                        <option value="all">@lang('app.all')</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}">{{ ($department->team_name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="f-14 text-dark-grey " for="usr">@lang('recruit::app.job.jobtype')</label>
                                    <select class="form-control select-picker" name="job_type_id" data-container="body"
                                            id="job_type_id">
                                        <option value="all">@lang('app.all')</option>
                                        @foreach ($jobTypes as $jobType)
                                            <option value="{{ $jobType->id }}">{{ ($jobType->job_type) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="f-14 text-dark-grey " for="usr">@lang('recruit::app.job.workexperience')</label>
                                    <select class="form-control select-picker" name="work_experience_id" data-container="body"
                                            id="work_experience_id">
                                        <option value="all">@lang('app.all')</option>
                                        @foreach ($workExperiences as $workExperience)
                                            <option value="{{ $workExperience->id }}">{{ ($workExperience->work_experience) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="f-14 text-dark-grey " for="usr">@lang('recruit::modules.job.job') @lang('app.category')</label>
                                    <select class="form-control select-picker" name="job_category_id" data-container="body"
                                            id="job_category_id">
                                        <option value="all">@lang('app.all')</option>
                                        @foreach ($jobCategories as $jobCategory)
                                            <option value="{{ $jobCategory->id }}">{{ ($jobCategory->category_name) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="f-14 text-dark-grey " for="usr">@lang('recruit::modules.front.remoteJob')</label>
                                    <select class="form-control select-picker" name="remote_job" data-container="body"
                                            id="remote_job">
                                        <option value="all">@lang('app.all')</option>
                                        <option value="yes">@lang('recruit::modules.front.remoteJob')</option>
                                    </select>
                                </div>

                                <div class="col-md-2 mt-4">
                                    <x-forms.button-primary id="fetch-job" class="border-0 mr-3">@lang('app.apply')</x-forms.button-primary>
                                    <x-forms.button-secondary id="filter-box" class="border-0 mr-3">@lang('app.clearFilters')</x-forms.button-secondary>

                                </div>
                            </div>
                        </x-form>
                    </div>
                </div>
            </div>
        </section>
        <!-- Content Start -->
        <section class="front-background">
            <div class="container">
                <div class="job-container">
                    <div class="job-left">
                        <ul class="list-style-none">
                            <div id="location">
                                @forelse($locations as $locationData)
                                @if(!is_null($locationData->job))
                                    <li class="border-bottom-grey cursor-pointer job-opening-card">
                                        <div class="card border-0 p-4">
                                            <div class="card-block" onclick="openJobDetail({{$locationData->job->id}},
                                            {{ $locationData->company_address_id }})">
                                                <input type="hidden" name="job_id{{$locationData->job->id}}" id="job_id"
                                                    value="{{ $locationData->job->id }}">
                                                <h5 class="card-title mb-0 heading-h5">{{ $locationData->job->title }}</h5>
                                                <div class="d-flex flex-wrap justify-content-between card-location mt-1">
                                                    <small class="text-dark-grey">{{ $locationData->job->jobType->job_type }}</small>
                                                    <span class="f-13 text-dark-grey">{{ isset($locationData->job->team->team_name) ? $locationData->job->team->team_name : null; }}<i class="ml-2 fa fa-graduation-cap"></i></span>
                                                </div>

                                                <div class="d-flex flex-wrap justify-content-between card-location mt-3">
                                                    <span class="fw-400 f-13"><i class="mr-2 fa fa-map-marker"></i>{{ ucwords($locationData->location->location) }}</span>
                                                    <div class="row">
                                                        <div class="d-block d-sm-none">
                                                            <a class="mr-3 btn btn-secondary rounded f-14" data-job-id="{{ $locationData->job->id }}" data-location-id="{{ $locationData->company_address_id }}" id="job-details-show">@lang('recruit::modules.joboffer.jobDetails')
                                                            </a>
                                                        </div>

                                                        <a href="{{ route('job_apply',[$locationData->job->slug, $locationData->location->id, $locationData->job->company->hash]) }}" class="btn btn-primary f-14 d-block d-sm-none">
                                                            <i class="fa fa-briefcase mr-1"></i>@lang('recruit::modules.front.apply')
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endif
                                @empty
                                    <x-cards.no-record icon="list" :message="__('recruit::messages.noOpenings')"/>
                                @endforelse
                            </div>
                        </ul>
                    </div>
                    <div class="job-right position-relative d-none d-lg-block">
                        <div class="firstjob">
                            @if ($firstJob == '')
                                <x-cards.no-record icon="list" :message="__('recruit::messages.noOpenings')"/>
                            @else

                                <div class="jobDetail">
                                    <div
                                        class="bg-white sticky-top py-3 px-4 d-flex justify-content-between align-items-center">
                                        <h4 class="mb-0 heading-h4">{{ $firstJob->title }}</h4>
                                        <div class="mt-3 mt-lg-0 mt-md-0">
                                            <a href="{{ route('job_apply',[$firstJob->slug, $firstJob->address[0]->id, $firstJob->company->hash]) }}" class="btn btn-primary f-14"
                                            data-toggle="tooltip"
                                            data-original-title="@lang('recruit::modules.front.apply')"><i
                                                    class="fa fa-briefcase mr-1"></i>@lang('recruit::modules.front.apply')</a>
                                        </div>
                                    </div>
                                    <div class="px-4">
                                        <h6 class="heading-h6">@lang('recruit::modules.front.skill')</h6>
                                        <div class="gap-multiline-items-1">
                                            @foreach ($firstJob->skills as $job_skill)
                                                <span>{{ $job_skill->skill->name }} @if(!$loop->last) &bull; @endif</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endsection
    <!-- Content End -->
    @push('scripts')

        <script>

            $(document).ready(function () {

                $("ul li:first").find('.card').addClass('active');
                $('body').on('click', '.job-opening-card', function () {
                    $('.job-opening-card').find('.card').removeClass("active");
                    $(this).find('.card').addClass('active');
                });
            });

            $('body').on('click', '#filter-box', function () {
                $('#fetch-job-filter-form')[0].reset();
                $('#department_id').val('all').trigger('change');
                $('#job_type_id').val('all').trigger('change');
                $('#work_experience_id').val('all').trigger('change');
                $('#job_category_id').val('all').trigger('change');
                $('#remote_job').val('all').trigger('change');
                var departmentId = 'all';
                var jobTypeId = 'all';
                var jobCategoryId = 'all';
                var remoteJob = 'all';
                var workExperienceId = 'all';
                var company = '{{ $company->hash }}'

                var url = "{{ route('job-opening.fetch_job', ':company') }}";
                url = url.replace(':company', company);

                $.easyAjax({
                    url: url,
                    type: "GET",
                    disableButton: true,
                    blockUI: true,
                    data: {
                        department_id: departmentId,
                        job_type_id: jobTypeId,
                        job_category_id: jobCategoryId,
                        work_experience_id: workExperienceId,
                        remote_job: remoteJob
                    },
                    success: function (response) {
                        if (response.status == 'success') {
                            $('#location').html(response.html);
                            $('.firstjob').html(response.firstjob);

                            $(document).ready(function () {
                                $("ul li:first").find('.card').addClass('active');
                                $('body').on('click', '.job-opening-card', function () {
                                    $('.job-opening-card').find('.card').removeClass("active");
                                    $(this).find('.card').addClass('active');
                                });
                            });

                        }
                    }
                });
            });

            @if ($locations->count() > 0 &&  $locations[0]->job != null){
                var jobid = '{{ $locations[0]->job->id }}'
                var locationid = '{{ $locations[0]->company_address_id }}'
                var company = '{{ $company->hash }}'

                openJobDetail(jobid, locationid, company);
            }
            @else {
                var jobid = ''
                var locationid = ''
                var company = ''
            }
            @endif

            function openJobDetail(job_id, location_id) {
                var id = job_id;
                var locationId = location_id;
                var company = '{{ $company->hash }}'

                var url = "{{route('job-detail', [':id', ':locationId', ':company'])}}";
                url = url.replace(':id', id);
                url = url.replace(':locationId', locationId);
                url = url.replace(':company', company);

                $.easyAjax({
                    url: url,
                    type: "GET",
                    blockUI: true,
                    success: function (response) {
                        if (response.status == "success") {
                            $('.jobDetail').html(response.html);
                        }
                    }
                })
            }

            $('body').on('click', '#fetch-job', function () {
                var departmentId = $('#department_id').val();
                var jobTypeId = $('#job_type_id').val();
                var jobCategoryId = $('#job_category_id').val();
                var remoteJob = $('#remote_job').val();
                var workExperienceId = $('#work_experience_id').val();
                var company = '{{ $company->hash }}'

                var url = "{{ route('job-opening.fetch_job', ':company') }}";
                url = url.replace(':company', company);

                $.easyAjax({
                    url: url,
                    type: "GET",
                    disableButton: true,
                    blockUI: true,
                    data: {
                        department_id: departmentId,
                        job_type_id: jobTypeId,
                        job_category_id: jobCategoryId,
                        work_experience_id: workExperienceId,
                        remote_job: remoteJob
                    },
                    success: function (response) {
                        if (response.status == 'success') {
                            $('#location').html(response.html);
                            $('.firstjob').html(response.firstjob);

                            $(document).ready(function () {
                                $("ul li:first").find('.card').addClass('active');
                                $('body').on('click', '.job-opening-card', function () {
                                    $('.job-opening-card').find('.card').removeClass("active");
                                    $(this).find('.card').addClass('active');
                                });
                            });
                        }
                    }
                });
            });



            $('body').on('click', '#job-details-show', function () {
                const id = $(this).data('job-id');
                const locationId = $(this).data('location-id');
                var url = "{{ route('front.job_details_modal') }}?slug={{ $company->hash }}&id="+id+"&locationId="+locationId;

                $('.modal-title').html("@lang('recruit::modules.joboffer.jobDetails')");
                $.ajaxModal('#addJobAlert', url);
            });

        </script>
    @endpush
@endif


