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
    <!-- Header Start -->
    <style>
        .required:after {
            content: " *";
            color: red;
        }

        .front-background {
            background-color: #F2F4F7;
        }
    </style>
    <style>
        .banner-header {
            background-repeat: no-repeat;
            background-position: center;
            height: 200px;
        }

        .banner-color {
            background-color: {{ $recruitSetting->background_color }};
            background-repeat: no-repeat;
            background-position: center;
            height: 200px;
        }

        .header-banner-logo {
            position: absolute !important;
            background-color: white !important;
            width: 130px !important;
            height: 130px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            bottom: -49px !important;
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
    @section('content')
        <header class="sticky-top bg-white">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 py-2 front_header d-flex justify-content-between align-items-center">
                        <a href="{{ url('/job-opening', $company->hash) }}">
                            <img class="mr-2 rounded" src="{{ $company->logo_url }}">
                        </a>
                        <h3 class="mb-0 pl-1 heading-h3">{{ $companyName }}</h3>
                    </div>
                </div>
            </div>
        </header>
        <!-- Header End -->

        <!-- Content Start -->
        <form id="applyForm" method="POST">
            @csrf
            <input type="hidden" name="job_id" value="{{ $job->id }}">
            <input type="hidden" name="location_id" value="{{ $job->address[0]->id }}">
            <input type="hidden" name="companyHash" value="{{ $company->hash }}">
            <section class="front-background py-3 main-content">
                <div class="container">
                    <!-- Banner Start -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="bg-white rounded overflow-auto border-grey">
                                <div class="col-md-12

                        @if ($recruitSetting->type == 'bg-image') banner-header
                        @else
                                banner-color @endif
                                "
                                    @if ($setting->type == 'bg-image') style="background-image: url({{ $recruitSetting->getBgImageUrlAttribute() }})" @endif
                                    id="bannerImg">
                                    <div class="header-banner-logo rounded">
                                        <img src={{ $recruitSetting->getLogoUrlAttribute() }} />
                                    </div>
                                </div>
                                <div
                                    class="col-md-12 mt-5 pb-4 d-block d-lg-flex d-md-flex  justify-content-between align-items-end">
                                    <div class="mt-4">
                                        <h3 class="heading-h3">{{ $job->title }}</h3>
                                        <h5 class="heading-h5 text-darkest-grey d-block d-sm-none">
                                            {{ $location->location }} &bull; {{ $location->address }}</h5>
                                        <p class="text-lightest f-12">{{ $job->start_date->diffForHumans() }}</p>
                                    </div>

                                    <div class="mt-3 mt-lg-0 mt-md-0">
                                        <a href="{{ route('job_opening', $company->hash) }}" class="btn btn-primary f-14"
                                            data-toggle="tooltip" data-original-title="@lang('recruit::app.menu.job')"><i
                                                class="fa fa-briefcase mr-1"></i>@lang('recruit::app.menu.job')</a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- Banner End -->
                    <!-- Overview Start -->
                    <div class="row">
                        <div class="col-md-12 mt-3">
                            <div class="add-client bg-white rounded">
                                <h4 class="mb-0 p-20 f-21 font-weight-normal  border-bottom-grey">
                                    @lang('recruit::modules.front.personalInformation')</h4>
                                <div class="row p-20">
                                    <div class="col-md-4">
                                        <div class="form-group my-3">
                                            <x-forms.text fieldId="full_name" :fieldLabel="__('recruit::modules.front.fullName')" fieldName="full_name"
                                                fieldRequired="true" :fieldPlaceholder="__('placeholders.name')">
                                            </x-forms.text>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group my-3">
                                            <x-forms.text fieldId="email" :fieldLabel="__('recruit::modules.front.email')" fieldName="email"
                                                fieldRequired="true" :fieldPlaceholder="__('placeholders.email')">
                                            </x-forms.text>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group my-3">
                                            <x-forms.tel fieldId="phone" fieldRequired="true" :fieldLabel="__('app.mobile')"
                                                fieldName="phone" fieldPlaceholder="e.g. 987654321"></x-forms.tel>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <x-forms.label class="my-3" fieldId="selectEmployeeData" :fieldLabel="__('recruit::modules.front.yourSkills')">
                                        </x-forms.label>
                                        <x-forms.input-group>
                                            <select class="form-control multiple-users" multiple name="skill_id[]"
                                                id="selectEmployeeData" data-live-search="true" data-size="8">
                                                @forelse ($skills as $skill)
                                                    <option
                                                        data-content="<span class='badge badge-pill badge-light border'><div class='d-inline-block mr-1'></div> {{ $skill->skill->name }}</span>"
                                                        value="{{ $skill->skill->id }}">{{ $skill->skill->name }}</option>
                                                @empty
                                                    <option value=""> @lang('recruit::messages.noSkillAdded')</option>
                                                @endforelse
                                            </select>
                                        </x-forms.input-group>
                                    </div>

                                    @if ($job->is_dob_require)
                                        <div class="col-md-4">
                                            <x-forms.text class="date-picker" :fieldRequired="true" :fieldLabel="__('recruit::modules.jobApplication.dateOfBirth')"
                                                fieldName="date_of_birth" fieldId="date_of_birth" :fieldPlaceholder="__('placeholders.date')"
                                                fieldValue="" />
                                        </div>
                                    @endif
                                    @if ($job->is_gender_require)
                                        <div class="col-md-4">
                                            <x-forms.select fieldId="gender" fieldRequired="true" :fieldLabel="__('recruit::modules.jobApplication.gender')"
                                                fieldName="gender">
                                                <option value="">--</option>
                                                <option value="male">@lang('app.male')</option>
                                                <option value="female">@lang('app.female')</option>
                                                <option value="others">@lang('app.others')</option>
                                            </x-forms.select>
                                        </div>
                                    @endif

                                    <div class="col-md-4">
                                        <x-forms.select fieldId="total_experience" :fieldLabel="__('recruit::modules.jobApplication.experience')"
                                            fieldName="total_experience">
                                            <option value="">--</option>
                                            <option value="fresher">@lang('recruit::modules.jobApplication.fresher')</option>
                                            <option value="1-2">1-2 @lang('recruit::modules.jobApplication.years')</option>
                                            <option value="3-4">3-4 @lang('recruit::modules.jobApplication.years')</option>
                                            <option value="5-6">5-6 @lang('recruit::modules.jobApplication.years')</option>
                                            <option value="7-8">7-8 @lang('recruit::modules.jobApplication.years')</option>
                                            <option value="9-10">
                                                9-10 @lang('recruit::modules.jobApplication.years')</option>
                                            <option value="11-12">
                                                11-12 @lang('recruit::modules.jobApplication.years')</option>
                                            <option value="13-14">
                                                13-14 @lang('recruit::modules.jobApplication.years')</option>
                                            <option value="over-15">@lang('recruit::modules.jobApplication.over15')</option>
                                        </x-forms.select>
                                    </div>

                                    <div class="col-md-4">
                                        <x-forms.text fieldId="current_location" :fieldLabel="__('recruit::modules.jobApplication.currentLocation')"
                                            fieldName="current_location" :fieldPlaceholder="__('recruit::modules.jobApplication.currentLocationPlaceholder')">
                                        </x-forms.text>
                                    </div>
                                    <div class="col-md-4">
                                        <x-forms.label class="my-3" fieldId="current_ctc"
                                            :fieldLabel="__('recruit::modules.jobApplication.currentCtc') .
                                                ' ' .
                                                $job->currency->currency_symbol"></x-forms.label>
                                        <x-forms.input-group>
                                            <input type="number" min="0" class="form-control height-35 f-14"
                                                name="current_ctc" placeholder="@lang('recruit::modules.jobApplication.currentCtcPlaceHolder')">
                                        </x-forms.input-group>
                                    </div>

                                    <div class="col-md-4">
                                        <x-forms.label fieldRequired="false" class="mt-3" fieldId="currenct_ctc_rate"
                                            :fieldLabel="__('recruit::modules.jobApplication.currentCtc') .
                                                ' ' .
                                                __('recruit::app.job.payaccording')">
                                        </x-forms.label>
                                        <x-forms.input-group>
                                            <select class="form-control select-picker" name="currenct_ctc_rate"
                                                id="currenct_ctc_rate" data-live-search="true">
                                                <option value="">--</option>
                                                <option value="Hour">{{ __('recruit::app.job.hour') }}</option>
                                                <option value="Day">{{ __('recruit::app.job.day') }}</option>
                                                <option value="Week">{{ __('recruit::app.job.week') }}</option>
                                                <option value="Month">{{ __('recruit::app.job.month') }}</option>
                                                <option value="Year">{{ __('recruit::app.job.year') }}</option>
                                            </select>
                                        </x-forms.input-group>
                                    </div>

                                    <div class="col-md-4">
                                        <x-forms.label class="my-3" fieldId="expected_ctc"
                                            :fieldLabel="__('recruit::modules.jobApplication.expectedCtc') .
                                                ' ' .
                                                $job->currency->currency_symbol"></x-forms.label>
                                        <x-forms.input-group>
                                            <input type="number" min="0" class="form-control height-35 f-14"
                                                name="expected_ctc" placeholder="@lang('recruit::modules.jobApplication.expectedCtcPlaceHolder')">
                                        </x-forms.input-group>
                                    </div>

                                    <div class="col-md-4">
                                        <x-forms.label fieldRequired="false" class="mt-3" fieldId="expected_ctc_rate"
                                            :fieldLabel="__('recruit::modules.jobApplication.expectedCtc') .
                                                ' ' .
                                                __('recruit::app.job.payaccording')">
                                        </x-forms.label>
                                        <x-forms.input-group>
                                            <select class="form-control select-picker" name="expected_ctc_rate"
                                                id="expected_ctc_rate" data-live-search="true">
                                                <option value="">--</option>
                                                <option value="hour">{{ __('recruit::app.job.hour') }}</option>
                                                <option value="day">{{ __('recruit::app.job.day') }}</option>
                                                <option value="week">{{ __('recruit::app.job.week') }}</option>
                                                <option value="month">{{ __('recruit::app.job.month') }}</option>
                                                <option value="year">{{ __('recruit::app.job.year') }}</option>
                                            </select>
                                        </x-forms.input-group>
                                    </div>

                                    <div class="col-md-4">
                                        <x-forms.select fieldId="notice_period" :fieldLabel="__('recruit::modules.jobApplication.noticePeriod')"
                                            fieldName="notice_period">
                                            <option value="">--</option>
                                            <option value="15">15 @lang('recruit::modules.jobApplication.days')</option>
                                            <option value="30">30 @lang('recruit::modules.jobApplication.days')</option>
                                            <option value="45">45 @lang('recruit::modules.jobApplication.days')</option>
                                            <option value="60">60 @lang('recruit::modules.jobApplication.days')</option>
                                            <option value="75">75 @lang('recruit::modules.jobApplication.days')</option>
                                            <option value="90">90 @lang('recruit::modules.jobApplication.days')</option>
                                            <option value="over-90">@lang('recruit::modules.jobApplication.over90')</option>
                                        </x-forms.select>
                                    </div>

                                    <div class="col-md-4">
                                        <x-forms.select fieldId="source" fieldName="source" :fieldLabel="__('recruit::modules.front.source')">
                                            <option value="">--</option>
                                            @foreach ($applicationSources as $source)
                                                <option value="{{ $source->id }}"> {{ $source->application_source }}
                                                </option>
                                            @endforeach
                                        </x-forms.select>
                                    </div>
                                </div>
                                <div class="row">
                                    @if ($job->is_resume_require)
                                        <div class="col-md-6">
                                            <div class="col-md-10">
                                                <x-forms.label class="" fieldRequired="true" fieldId="resume"
                                                    :fieldLabel="__('recruit::modules.front.resume')"></x-forms.label>
                                                <div class="form-group custom-file">
                                                    <input type="file" class="custom-file-input" name="resume">
                                                    <x-forms.label fieldId="resume" fieldRequired="false"
                                                        :fieldLabel="__('recruit::app.menu.add') .
                                                            ' ' .
                                                            __('recruit::app.jobApplication.resume')" class="custom-file-label"></x-forms.label>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($job->is_photo_require)
                                        <div class="col-md-6">
                                            <div class="col-md-10">
                                                <x-forms.label class="" fieldRequired="true" fieldId="photo"
                                                    :fieldLabel="__('recruit::modules.front.photo')"></x-forms.label>
                                                <div class="form-group custom-file">
                                                    <input type="file" class="custom-file-input" name="photo"
                                                        accept="image/jpeg, image/jpg, image/png">
                                                    <x-forms.label fieldId="photo" fieldRequired="false"
                                                        :fieldLabel="__('recruit::app.menu.add') .
                                                            ' ' .
                                                            __('recruit::modules.front.photo')" class="custom-file-label"></x-forms.label>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-12 mt-4">
                                    <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('recruit::modules.jobApplication.coverLetter')"
                                        fieldName="cover_letter" fieldId="cover_letter" :fieldPlaceholder="__('recruit::modules.jobApplication.coverLetter')">
                                    </x-forms.textarea>
                                </div>

                                @if (count($fields) > 0)
                                    <h4 class="mb-0 p-20 f-21 font-weight-normal  border-bottom-grey">
                                        @lang('recruit::modules.front.additionalDetails')</h4>

                                    <div class="p-20">
                                        <x-recruit::cards.custom-question-field :fields="$fields" :values="$values" />
                                    </div>
                                @endif

                                @if (!is_null($recruitSetting->legal_term))
                                    <h4 class="mb-0 p-20 f-21 font-weight-normal  border-top-grey">
                                        @lang('recruit::modules.front.termsCondition') </h4>
                                    <div class="col-md-12 p-20 mb-0">
                                        <p class="text-dark-grey mb-0 text-justify">{!! $recruitSetting->legal_term !!}</p>
                                    </div>
                                    <div class="col-md-12">
                                        <x-forms.checkbox :fieldLabel="__('recruit::modules.front.agreeCondition')" fieldName="term_agreement"
                                            fieldId="term_agreement" />
                                    </div>
                                @else
                                    <input type="hidden" name="term_agreement" id="term_agreement" value="off" />
                                @endif

                                @if ($recruitSetting->google_recaptcha_status == 'active')
                                    @if ($globalSetting->google_recaptcha_status == 'active' && $globalSetting->google_recaptcha_v2_status == 'active')
                                        <div class="col-md-12 col-lg-12 mt-2 mb-2" id="captcha_container"></div>
                                    @endif

                                    {{-- This is used for google captcha v3 --}}
                                    <input type="hidden" id="g_recaptcha" name="g_recaptcha">

                                    @if ($errors->has('g-recaptcha-response'))
                                        <div class="help-block with-errors">{{ $errors->first('g-recaptcha-response') }}
                                        </div>
                                    @endif
                                @endif

                                <div class="col-md-12 mt-3">
                                    <div
                                        class='w-100 border-top-grey d-block d-lg-flex d-md-flex justify-content-start py-3'>
                                        <x-forms.button-primary id="save-form" class="mr-3" icon="check">
                                            @lang('recruit::modules.front.apply')
                                        </x-forms.button-primary>
                                        <x-forms.button-cancel :link="route('job_opening', $company->hash)" class="border-0">
                                            @lang('app.cancel')
                                        </x-forms.button-cancel>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Overview End -->
            </section>
        </form>
    @endsection
    <!-- Content End -->
    @push('scripts')

        <script>
            if ($('.custom-date-picker').length > 0) {
                datepicker('.custom-date-picker', {
                    position: 'bl',
                    ...datepickerConfig
                });
            }

            function checkboxChange(parentClass, id) {
                var checkedData = '';
                $('.' + parentClass).find("input[type= 'checkbox']:checked").each(function() {
                    checkedData = (checkedData !== '') ? checkedData + ', ' + $(this).val() : $(this).val();
                });
                $('#' + id).val(checkedData);
            }

            $("#selectEmployeeData").selectpicker({
                actionsBox: true,
                selectAllText: "{{ __('modules.permission.selectAll') }}",
                deselectAllText: "{{ __('modules.permission.deselectAll') }}",
                multipleSeparator: " ",
                selectedTextFormat: "count > 8",
                countSelectedText: function(selected, total) {
                    return selected + " {{ __('app.membersSelected') }} ";
                }
            });

            $(".custom-file-input").on("change", function() {
                var fileName = $(this).val().split("\").pop();
                $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
            });

            $('body').on('click', '#save-form', function() {

                $.easyAjax({
                    url: "{{ route('save_application') }}",
                    container: '#applyForm',
                    type: "POST",
                    disableButton: true,
                    blockUI: true,
                    file: true,
                    redirect: true,
                    data: $('#applyForm').serialize(),
                    success: function(response) {
                        if (response.status == "success") {
                            window.location.href = response.redirectUrl;
                        }
                    }
                });
            });

            @if ($job->is_dob_require)
                datepicker('#date_of_birth', {
                    position: 'bl',
                    maxDate: new Date(),

                    ...datepickerConfig
                });
            @endif

            @if (count($fields) > 0)
                datepicker('#answer', {
                    position: 'bl',

                    ...datepickerConfig
                });
            @endif
        </script>

        @if ($recruitSetting->google_recaptcha_status == 'active')
            @if ($globalSetting->google_recaptcha_status == 'active' && $globalSetting->google_recaptcha_v2_status == 'active')
                <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
                <script>
                    var gcv3;
                    var onloadCallback = function() {
                        // Renders the HTML element with id 'captcha_container' as a reCAPTCHA widget.
                        // The id of the reCAPTCHA widget is assigned to 'gcv3'.
                        gcv3 = grecaptcha.render('captcha_container', {
                            'sitekey': '{{ $globalSetting->google_recaptcha_v2_site_key }}',
                            'theme': 'light',
                            'callback': function(response) {
                                if (response) {
                                    $('#g_recaptcha').val(response);
                                }
                            },
                        });
                    };
                </script>
            @endif

            @if ($globalSetting->google_recaptcha_status == 'active' && $globalSetting->google_recaptcha_v3_status == 'active')
                <script src="https://www.google.com/recaptcha/api.js?render={{ $globalSetting->google_recaptcha_v3_site_key }}">
                </script>
                <script>
                    grecaptcha.ready(function() {
                        grecaptcha.execute('{{ $globalSetting->google_recaptcha_v3_site_key }}').then(function(token) {
                            // Add your logic to submit to your backend server here.
                            $('#g_recaptcha').val(token);
                        });
                    });
                </script>
            @endif
        @endif
    @endpush
@endif
