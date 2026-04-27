<div id="task-detail-section">
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 mb-4 mb-xl-0 mb-lg-4 mb-md-0">
            <div class="card bg-white border-0 b-shadow-4">
                <div class="card-header bg-white  border-bottom-grey  justify-content-between p-20">
                    <div class="row">
                        <div class="col-lg-8 col-10">
                            <h1 class="heading-h1">
                                {{ $application->full_name }}</h1>
                        </div>
                        <div class="col-lg-4 col-2 text-right">
                            <x-forms.button-primary data-status="completed"
                                                    class="change-task-status mr-3" id="retrive_job">
                                @lang('recruit::modules.job.retrive')
                            </x-forms.button-primary>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-8 col-lg-8 col-md-12 mb-4 mb-xl-0 mb-lg-4 mb-md-0">

                            <div class="col-12 px-0 pb-3 d-block d-lg-flex d-md-flex">
                                <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                    @lang('recruit::modules.job.jobTitle')</p>
                                <p class="mb-0 text-dark-grey f-14 w-70">
                                    {{ $application->job->title }}
                                </p>
                            </div>
                            <div class="col-12 px-0 pb-3 d-block d-lg-flex d-md-flex">
                                <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                    @lang('recruit::modules.jobApplication.applicantEmail')
                                </p>
                                <p class="mb-0 text-dark-grey f-14 w-70">
                                    {{ ($application->email ?? '--') }}
                                </p>
                            </div>
                            <div class="col-12 px-0 pb-3 d-block d-lg-flex d-md-flex">
                                <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                    @lang('recruit::modules.jobApplication.applicantPhone')
                                </p>
                                <p class="mb-0 text-dark-grey f-14 w-70">
                                    {{ $application->phone ?? '--' }}
                                </p>
                            </div>
                            @if ($application->date_of_birth)
                                <div class="col-12 px-0 pb-3 d-block d-lg-flex d-md-flex">
                                    <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                        @lang('recruit::modules.jobApplication.dateOfBirth')
                                    </p>
                                    <p class="mb-0 text-dark-grey f-14 w-70">
                                        {{ $application->date_of_birth->format($company->date_format) }}
                                    </p>
                                </div>
                            @endif

                            @if ($application->gender)
                                <div class="col-12 px-0 pb-3 d-block d-lg-flex d-md-flex">
                                    <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                        @lang('recruit::modules.jobApplication.gender')
                                    </p>
                                    <p class="mb-0 text-dark-grey f-14 w-70">
                                        {{ ($application->gender ?? '--') }}
                                    </p>
                                </div>
                            @endif

                            @if ($application->total_experience)
                                <div class="col-12 px-0 pb-3 d-block d-lg-flex d-md-flex">
                                    <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                        @lang('recruit::modules.jobApplication.experience')
                                    </p>
                                    <p class="mb-0 text-dark-grey f-14 w-70">
                                        @if ($application->total_experience == 'fresher')
                                            {{ $application->total_experience }}
                                        @else
                                            {{ $application->total_experience }} @lang('recruit::modules.jobApplication.years')
                                        @endif

                                    </p>
                                </div>
                            @endif

                            @if ($application->current_location)
                                <div class="col-12 px-0 pb-3 d-block d-lg-flex d-md-flex">
                                    <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                        @lang('recruit::modules.jobApplication.currentLocation')
                                    </p>
                                    <p class="mb-0 text-dark-grey f-14 w-70">
                                        {{ ($application->current_location ?? '--') }}
                                    </p>
                                </div>
                            @endif

                            @if ($application->current_ctc)
                                <div class="col-12 px-0 pb-3 d-block d-lg-flex d-md-flex">
                                    <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                        @lang('recruit::modules.jobApplication.currentCtc')
                                    </p>
                                    <p class="mb-0 text-dark-grey f-14 w-70">
                                        {{ currency_format($application->current_ctc, $currency ? $currency->id : company()->currency->id) }}
                                        @lang('recruit::modules.joboffer.per') {{ $application->currenct_ctc_rate }}
                                    </p>
                                </div>
                            @endif

                            @if ($application->expected_ctc)
                                <div class="col-12 px-0 pb-3 d-block d-lg-flex d-md-flex">
                                    <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                        @lang('recruit::modules.jobApplication.expectedCtc')
                                    </p>
                                    <p class="mb-0 text-dark-grey f-14 w-70">
                                        {{ currency_format($application->expected_ctc, $currency ? $currency->id : company()->currency->id) }}
                                        @lang('recruit::modules.joboffer.per') {{ $application->expected_ctc_rate }}
                                    </p>
                                </div>
                            @endif

                            @if ($application->notice_period)
                                <div class="col-12 px-0 pb-3 d-block d-lg-flex d-md-flex">
                                    <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                        @lang('recruit::modules.jobApplication.noticePeriod')
                                    </p>
                                    <p class="mb-0 text-dark-grey f-14 w-70">
                                        {{ $application->notice_period ?? '--' }} @lang('recruit::modules.jobApplication.days')
                                    </p>
                                </div>
                            @endif

                            @if ($application->source_id)
                                <div class="col-12 px-0 pb-3 d-block d-lg-flex d-md-flex">
                                    <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                        @lang('recruit::modules.front.applicationSource')
                                    </p>
                                    <p class="mb-0 text-dark-grey f-14 w-70">
                                        {{ $application->source->application_source ?? '--' }}
                                    </p>
                                </div>
                            @endif

                            <div class="col-12 px-0 pb-3 d-block d-lg-flex d-md-flex">
                                <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                    @lang('recruit::modules.jobApplication.appliedAt')
                                </p>
                                <p class="mb-0 text-dark-grey f-14 w-70">
                                    {{ $application->created_at->format($company->date_format) }}
                                </p>
                            </div>
                            @if($application->remark)
                                <div class="col-12 px-0 pb-3 d-block d-lg-flex d-md-flex">
                                    <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                        @lang('app.remark')</p>
                                    <p class="mb-0 text-dark-grey f-14 w-70">
                                        {{ ($application->remark) ?? '--' }}
                                    </p>
                                </div>
                            @endif
                            @if($application->rejection_remark)
                                <div class="col-12 px-0 pb-3 d-block d-lg-flex d-md-flex">
                                    <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                        @lang('app.remark')</p>
                                    <p class="mb-0 text-dark-grey f-14 w-70">
                                        {{ ($application->rejection_remark) ?? '--' }}
                                    </p>
                                </div>
                            @endif
                            <div class="col-12 px-0 pb-3 d-block d-lg-flex d-md-flex">
                                <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                    @lang('recruit::modules.jobApplication.coverLetter')</p>
                                <p class="mb-0 text-dark-grey f-14 w-70">
                                    {{ $application->cover_letter ?? '--' }}
                                </p>
                            </div>

                            <div class="col-12 px-0 pb-3 d-block d-lg-flex d-md-flex">
                                <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                    @lang('recruit::modules.job.location')</p>
                                <p class="mb-0 text-dark-grey f-14 w-70">
                                    {{ $address ? $address->location : '-' }}
                                </p>
                            </div>
                            <div class="col-12 px-0 pb-3 d-block d-lg-flex d-md-flex">
                                <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                    @lang('recruit::modules.jobApplication.jobapplied')</p>
                                <p class="mb-0 text-dark-grey f-14 w-70">
                                    {{ date('d-m-Y', strtotime($application->Job_applied_on)) }}
                                </p>
                            </div>
                            <div class="col-12 px-0 pb-3 d-block d-lg-flex d-md-flex">
                                <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                    @lang('recruit::modules.jobApplication.skills')</p>
                                <p class="mb-0 text-dark-grey f-14 w-70">
                                    @foreach ($skills as $item)
                                        <span>{{ $item['name'] }}</span><br>
                                    @endforeach
                                </p>
                            </div>

                            <div class="col-12 px-0 pb-3 d-lg-flex d-lg-flex d-block">
                                <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                    @lang('recruit::app.jobApplication.resume')</p>
                                <div class="row w-70">
                                    @if($application->files->count() > 0)
                                        @forelse($application->files as $file)
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
                                                            aria-labelledby="dropdownMenuLink">
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
                </div>
            </div>

            <!-- TASK TABS START -->

            <!-- TASK TABS END -->
        </div>
    </div>
</div>
<script>

    $(document).ready(function () {
        $("body").on("click", ".ajax-tab", function (event) {
            event.preventDefault();

            $('.task-tabs .ajax-tab').removeClass('active');
            $(this).addClass('active');

            const requestUrl = this.href;

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


        $("#selectSkill").selectpicker({
            actionsBox: true,
            selectAllText: "{{ __('modules.permission.selectAll') }}",
            deselectAllText: "{{ __('modules.permission.deselectAll') }}",
            multipleSeparator: " ",
            selectedTextFormat: "count > 8",
            countSelectedText: function (selected, total) {
                return selected + " {{ __('app.membersSelected') }} ";
            }
        });
    });

    $('#retrive_job').on('click', function () {

        var url = "{{ route('candidate-database.update',$database->id) }}";
        var token = "{{ csrf_token() }}";

        $.easyAjax({
            url: url,
            container: '#task-detail-section',
            type: "PUT",
            disableButton: true,
            blockUI: true,
            data: {
                '_token': token,
                'job_app_id': "{{ $database->job_application_id }}"
            },
            success: function (response) {
                if (response.status == 'success') {
                    setTimeout(() => {
                        window.location.href = "{{ route('job-applications.index') }}"
                    }, 500);
                }
            }
        });
    });
</script>

