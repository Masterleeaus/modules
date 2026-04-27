<div class="row">
    <div class="col-sm-12">
        <x-form id="save-job-application-data-form">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal  border-bottom-grey">
                    @lang('recruit::modules.skill.createnew')</h4>
                <div class="row p-20">
                    <div id="firstDiv" class="col-lg-9 col-xl-10">
                        <div class="row">
                            <div class="col-md-3">
                                <x-forms.label fieldId="job_id" fieldRequired="true"
                                               :fieldLabel="__('recruit::modules.jobApplication.jobs')"
                                               class="mt-3"></x-forms.label>
                                <div class="form-group mb-0">
                                    @if($jobId) <input type="hidden" name="job_id" value="{{$jobId}}"> @endif
                                    <select @if($jobId) disabled @endif name="job_id" id="job_id"
                                            class="form-control select-picker" data-size="8">
                                        <option value="">--</option>
                                        @foreach ($jobs as $job)
                                            <option @if($jobId && $job->id == $jobId) selected @endif
                                                value="{{ $job->id }}">{{ ($job->title) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <x-forms.text fieldId="name" :fieldLabel="__('recruit::modules.jobApplication.name')"
                                              fieldName="full_name" fieldRequired="true"
                                              :fieldPlaceholder="__('placeholders.name')">
                                </x-forms.text>
                            </div>
                            <div class="col-md-3">
                                <x-forms.text fieldId="email" :fieldLabel="__('recruit::modules.jobApplication.email')"
                                              fieldName="email"
                                              :fieldPlaceholder="__('placeholders.email')">
                                </x-forms.text>
                            </div>
                            <div class="col-md-3">
                                <x-forms.tel fieldId="phone" :fieldLabel="__('app.phone')" fieldName="phone"
                                    fieldPlaceholder="e.g. 987654321" fieldRequired="true"></x-forms.tel>
                            </div>
                            <div class="col-md-3" id="gender-div">
                                <x-forms.select fieldId="gender" fieldRequired="true"
                                                :fieldLabel="__('recruit::modules.jobApplication.gender')"
                                                fieldName="gender">
                                    <option value="">--</option>
                                    <option value="male">@lang('app.male')</option>
                                    <option value="female">@lang('app.female')</option>
                                    <option value="others">@lang('app.others')</option>
                                </x-forms.select>
                            </div>
                            <div class="col-md-3">
                                <x-forms.select fieldId="location_id" fieldRequired="true" fieldName="location_id"
                                                :fieldLabel="__('recruit::modules.jobApplication.location')">
                                    <option value="">--</option>
                                    @if($jobId)
                                        @foreach ($jobLocations as $locationData)
                                            <option
                                                value="{{ $locationData->location->id }}">{{ $locationData->location->location }}</option>
                                        @endforeach
                                    @endif
                                </x-forms.select>
                            </div>
                            <div class="col-md-3">
                                <label class="f-14 text-dark-grey mb-12 mt-3 "
                                    for="usr">@lang('recruit::modules.jobApplication.experience')</label>

                                <div class="mb-4">
                                    <select name="total_experience" class="form-control select-picker" id="total_experience" data-live-search="true"
                                            data-container="body" data-size="8">
                                        <option value="">--</option>
                                        <option value="fresher">@lang('recruit::modules.jobApplication.fresher')</option>
                                        <option value="0-1">0-1 @lang('recruit::modules.jobApplication.years')</option>
                                        <option value="1-2">1-2 @lang('recruit::modules.jobApplication.years')</option>
                                        <option value="2-3">2-3 @lang('recruit::modules.jobApplication.years')</option>
                                        <option value="3-4">3-4 @lang('recruit::modules.jobApplication.years')</option>
                                        <option value="4-5">4-5 @lang('recruit::modules.jobApplication.years')</option>
                                        <option value="5-6">5-6 @lang('recruit::modules.jobApplication.years')</option>
                                        <option value="6-7">6-7 @lang('recruit::modules.jobApplication.years')</option>
                                        <option value="7-8">7-8 @lang('recruit::modules.jobApplication.years')</option>
                                        <option value="8-9">8-9 @lang('recruit::modules.jobApplication.years')</option>
                                        <option value="9-10">9-10 @lang('recruit::modules.jobApplication.years')</option>
                                        <option value="10-11">10-11 @lang('recruit::modules.jobApplication.years')</option>
                                        <option value="11-12">11-12 @lang('recruit::modules.jobApplication.years')</option>
                                        <option value="12-13">12-13 @lang('recruit::modules.jobApplication.years')</option>
                                        <option value="13-14">13-14 @lang('recruit::modules.jobApplication.years')</option>
                                        <option value="over-15">@lang('recruit::modules.jobApplication.over15')</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3 current">
                                <x-forms.label class="my-3" fieldId="current"
                                               :fieldLabel="__('recruit::modules.jobApplication.currentCtc')"></x-forms.label>
                                            <span class="f-14 text-dark-grey" id="currency-symbol"></span>
                                <x-forms.input-group>
                                    <input type="number" min="0" class="form-control height-35 f-14"
                                           name="current_ctc"
                                           placeholder="@lang('recruit::modules.jobApplication.currentCtcPlaceHolder')">
                                </x-forms.input-group>
                            </div>

                            <div class="col-md-3 current">
                                <x-forms.label fieldRequired="false" class="mt-3" fieldId="currenct_ctc_rate"
                                               :fieldLabel="__('recruit::modules.jobApplication.currentCtc') . ' ' . __('recruit::app.job.payaccording')"
                                >
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

                            <div class="col-md-3 expected">
                                <x-forms.label class="my-3" fieldId="expected"
                                               :fieldLabel="__('recruit::modules.jobApplication.expectedCtc')"></x-forms.label>
                                            <span class="f-14 text-dark-grey" id="currency-symbol-1"></span>
                                <x-forms.input-group>
                                    <input type="number" min="0" class="form-control height-35 f-14"
                                           name="expected_ctc"
                                           placeholder="@lang('recruit::modules.jobApplication.expectedCtcPlaceHolder')">
                                </x-forms.input-group>
                            </div>

                            <div class="col-md-3 expected">
                                <x-forms.label fieldRequired="false" class="mt-3" fieldId="expected_ctc_rate"
                                               :fieldLabel="__('recruit::modules.jobApplication.expectedCtc') . ' ' . __('recruit::app.job.payaccording')"
                                >
                                </x-forms.label>
                                <x-forms.input-group>
                                    <select class="form-control select-picker" name="expected_ctc_rate"
                                            id="expected_ctc_rate" data-live-search="true">
                                        <option value="">--</option>
                                        <option value="Hour">{{ __('recruit::app.job.hour') }}</option>
                                        <option value="Day">{{ __('recruit::app.job.day') }}</option>
                                        <option value="Week">{{ __('recruit::app.job.week') }}</option>
                                        <option value="Month">{{ __('recruit::app.job.month') }}</option>
                                        <option value="Year">{{ __('recruit::app.job.year') }}</option>
                                    </select>
                                </x-forms.input-group>
                            </div>

                            <div class="col-md-3">
                                <x-forms.text fieldId="current_location"
                                              :fieldLabel="__('recruit::modules.jobApplication.currentLocation')"
                                              fieldName="current_location"
                                              :fieldPlaceholder="__('recruit::modules.jobApplication.currentLocationPlaceholder')">
                                </x-forms.text>
                            </div>

                            <div class="col-md-3">
                                <x-forms.select fieldId="notice_period"
                                                :fieldLabel="__('recruit::modules.jobApplication.noticePeriod')"
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

                            <div class="col-md-3">
                                <x-forms.select fieldId="status_id" fieldName="status_id"
                                                :fieldLabel="__('recruit::modules.jobApplication.status')">
                                    @foreach ($applicationStatus as $status)
                                        <option @if ($status->id == $statusId) selected @endif value="{{$status->id}}"
                                                data-content="<i class='fa fa-circle mr-2' style='color: {{$status->color}}'></i> {{ ($status->status) }}"></option>
                                    @endforeach
                                </x-forms.select>
                            </div>

                            <div class="col-md-3" id="dob">
                                <x-forms.text class="date-picker" :fieldRequired="true"
                                              :fieldLabel="__('recruit::modules.jobApplication.dateOfBirth')"
                                              fieldName="date_of_birth"
                                              fieldId="date_of_birth-1" :fieldPlaceholder="__('placeholders.date')"
                                              fieldValue=""/>
                            </div>

                            <div class="col-md-3">
                                <x-forms.select fieldId="source" fieldName="source"
                                                :fieldLabel="__('recruit::modules.front.applicationSource')">
                                    <option value="">--</option>
                                    @foreach ($applicationSources as $source)
                                        <option
                                            value="{{$source->id}}"> {{ ($source->application_source) }}</option>
                                    @endforeach
                                </x-forms.select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-xl-2" id="photo">
                        <x-forms.file allowedFileExtensions="png jpg jpeg svg" class="mr-0 mr-lg-2 mr-md-2"
                                      :fieldLabel="__('modules.profile.profilePicture')"
                                      fieldName="photo" :fieldRequired="true"
                                      fieldId="photo" fieldHeight="119"/>
                    </div>

                    <div class="col-md-12">
                        <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2"
                                          :fieldLabel="__('recruit::modules.jobApplication.coverLetter')"
                                          fieldName="cover_letter"
                                          fieldId="cover_letter"
                                          :fieldPlaceholder="__('recruit::modules.jobApplication.coverLetter')">
                        </x-forms.textarea>
                    </div>
                    <div class="col-md-12" id="resume1">
                        <div class="form-group my-3">
                            <x-forms.label fieldId="resume" fieldRequired="true"
                                           :fieldLabel="__('recruit::app.menu.add') . ' ' .__('recruit::app.jobApplication.resume')"
                                           class="mt-3"></x-forms.label>
                            <input type="file" class="dropify" name="resume"
                                   data-allowed-file-extensions="txt pdf doc xls xlsx docx rtf png jpg jpeg svg" data-messages-default="test"
                                   data-height="150"/>
                            <input type="hidden" name="resume">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="f-14 text-dark-grey mb-12 w-100" for="send_email"></label>
                            <div class="d-flex">
                                <x-forms.checkbox fieldId="send_email" :fieldLabel="__('recruit::modules.jobApplication.sendMailToJobApplicant')" fieldValue="1" fieldName="send_email"></x-forms.checkbox>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row p-20">
                    <div class="col-lg-12" id="custom-fields"></div>
                </div>
                <x-form-actions>
                    <x-forms.button-primary id="save-job-application" class="mr-3" icon="check">@lang('app.save')
                    </x-forms.button-primary>
                    <x-forms.button-secondary class="mr-3" id="save-more-job-application"
                                              icon="check-double">@lang('app.saveAddMore')
                    </x-forms.button-secondary>
                    <x-forms.button-cancel :link="route('job-applications.index')" class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
                </x-form-actions>
            </div>
        </x-form>
    </div>
</div>

<script>
    photoShow = false;
    $(document).ready(function () {

        datepicker('#date_of_birth-1', {
            position: 'bl',
            maxDate: new Date(),

            ...datepickerConfig
        });

        $(document).find('.dropify').dropify({
            messages: dropifyMessages
        });

        $('#save-job-application').click(function () {
            const url = "{{ route('job-applications.store') }}";
            var data = $('#save-job-application-data-form').serialize();

            saveApplication(data, url, "#save-job-application");

        });

        $('#save-more-job-application').click(function () {
            const url = "{{ route('job-applications.store') }}?add_more=true";
            var data = $('#save-job-application-data-form').serialize();

            saveApplication(data, url, "#save-more-job-application");

        });

        function saveApplication(data, url, buttonSelector) {
            $.easyAjax({
                url: url,
                container: '#save-job-application-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                file: true,
                buttonSelector: buttonSelector,
                data: data,
                success: function (response) {
                    if (response.add_more == true) {
                        $(RIGHT_MODAL_CONTENT).html(response.html.html);
                    } else {
                        window.location.href = response.redirectUrl;
                    }
                }
            });
        };

        init(RIGHT_MODAL);
    });

    @if($jobApp != null)
    @if($jobApp->is_dob_require)
    $('#dob').show();
    @else
    $('#dob').hide();
    @endif

    @if($jobApp != null)
    @if($jobApp->is_currentctc_require)
    $('.current').show();
    @else
    $('.current').hide();
    @endif
    @endif


    @if($jobApp != null)
    @if($jobApp->is_expectedctc_require)
    $('.expected').show();
    @else
    $('.expected').hide();
    @endif
    @endif

    @if($jobApp->is_photo_require)
    $('#photo').show();
    photoShow = true;
    checkPhoto(photoShow)
    @else
    $('#photo').hide();
    photoShow = false;
    checkPhoto(photoShow)
    @endif

    @if($jobApp->is_gender_require)
    $('#gender-div').show();
    @else
    $('#gender-div').hide();
    @endif

    @if($jobApp->is_resume_require)
    $('#resume1').show();
    @else
    $('#resume1').hide();
    @endif
    @else
    $('#dob').hide();
    $('#photo').hide();
    $('.current').hide();
    $('.expected').hide();
    photoShow = false;
    checkPhoto(photoShow)
    $('#gender-div').hide();
    $('#resume1').hide();
    @endif

    $('#custom-fields').hide();

    $('#job_id').change(function () {
        const jobId = $(this).val();
        if (jobId != "" && jobId != undefined) {
            const url = "{{ route('job-applications.get_location') }}";
            $.easyAjax({
                url: url,
                type: "GET",
                disableButton: true,
                blockUI: true,
                data: {
                    job_id: jobId
                },
                success: function (response) {
                    if (response.currencySymbol != null) {
                        document.getElementById("currency-symbol").innerHTML = response.currencySymbol.currency_symbol;
                        document.getElementById("currency-symbol-1").innerHTML = response.currencySymbol.currency_symbol;
                    }
                    $('#location_id').html(response.locations);
                    $('#location_id').selectpicker('refresh');

                    if (response.job) {
                        if (response.job.is_dob_require == 1) {
                            $('#dob').show();
                        } else {
                            $('#dob').hide();
                        }

                        if (response.job.is_gender_require == 1) {
                            $('#gender-div').show();
                        } else {
                            $('#gender-div').hide();
                        }

                        if (response.job.is_currentctc_require == 1) {
                            $('.current').show();
                        } else {
                            $('.current').hide();
                        }

                        if (response.job.is_expectedctc_require == 1) {
                            $('.expected').show();
                        } else {
                            $('.expected').hide();
                        }

                        if (response.job.is_photo_require == 1) {
                            $('#photo').show();
                            photoShow = true;
                            checkPhoto(photoShow)
                        } else {
                            $('#photo').hide();
                            photoShow = false;
                            checkPhoto(photoShow)
                        }

                        if (response.job.is_resume_require == 1) {
                            $('#resume1').show();
                        } else {
                            $('#resume1').hide();
                        }

                        if (response.job.question && response.job.question.length > 0) {
                            $('#custom-fields').show();
                        } else {
                            $('#custom-fields').hide();
                        }
                    }
                }
            });
        } else {
            $('#dob').hide();
            $('.current').hide();
            $('.expected').hide();
            $('#gender-div').hide();
            $('#custom-fields').hide();
            $('#photo').hide();
            photoShow = false;
            checkPhoto(photoShow);
            $('#resume1').hide();
        }
    });

    $('#job_id').on('change', function () {
        const jobId = $(this).val();
        const $customFields = $('#custom-fields');
        if (jobId) {
            $.easyAjax({
                url: "{{ route('job-applications.get_custom_fields') }}",
                type: "GET",
                data: { job_id: jobId },
                disableButton: true,
                blockUI: true,
                success: function (response) {
                    if (Array.isArray(response.fields) && response.fields.length) {
                        $customFields.show().empty();
                        const tempDiv = $('<div>').html(response.html);
                        const rowDiv = $('<div class="row"></div>');
                        tempDiv.children().each(function () {
                            rowDiv.append($('<div class="col-md-3"></div>').append($(this)));
                        });
                        $customFields.html(rowDiv);

                        $customFields.find('input[type="text"][id^="answer["]').each(function () {
                            const $input = $(this);
                            if ($input.hasClass('custom-date-picker') || $input.attr('placeholder') === "{{ __('recruit::modules.setting.selectDateHere') }}") {
                                datepicker('#' + $input.attr('id'), {
                                    position: 'bl',
                                    ...datepickerConfig
                                });
                            }
                        });
                        $(".select-picker").selectpicker();
                    } else {
                        $customFields.hide().empty();
                    }
                }
            });
        } else {
            $customFields.hide().empty();
        }
    });

    function checkPhoto(photoShow) {
        if (photoShow === true) {
            $('#firstDiv').removeClass('col-lg-12 col-xl-12');
            $('#firstDiv').addClass('col-lg-9 col-xl-10');
        } else {
            $('#firstDiv').removeClass('col-lg-9 col-xl-10');
            $('#firstDiv').addClass('col-lg-12 col-xl-12');
        }
    }

</script>
