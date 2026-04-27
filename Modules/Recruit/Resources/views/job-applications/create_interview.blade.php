<style>
    .display {
        display: none;
    }
</style>
<div class="modal-header">
    <h5 class="modal-title" id="modelHeading"> @lang('recruit::modules.interviewSchedule.interview') @lang('app.details')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <x-form id="save-event-data-form">
        <input type="hidden" name="applicationID" value="{{ $applicationID }}">
        <input type="hidden" name="board" value="{{ $board }}">
        <div class="row">
            <div class="col-md-4">
                <x-forms.label fieldRequired="true" class="mt-3" fieldId="applicationlabel" :fieldLabel="__('recruit::modules.interviewSchedule.candidate')">
                </x-forms.label>
                <x-forms.input-group>
                    <select @if ($applicationID) disabled @endif class="form-control select-picker"
                        name="applicationID" id="applicationID" data-live-search="true">
                        <option value="">--</option>
                        @foreach ($applications as $job)
                            <option @if ($job->id == $applicationID) selected @endif value="{{ $job->id }}">
                                {{ $job->full_name }}</option>
                        @endforeach
                    </select>
                </x-forms.input-group>
            </div>

            <div class="col-md-4">
                <x-forms.select fieldId="selectEmployee" :fieldLabel="__('recruit::modules.interviewSchedule.interviewer')" fieldName="employee_id[]"
                    fieldRequired="true" search="true" multiple="true">
                    @foreach ($employees as $emp)
                        @if (!is_null($emp))
                            <x-user-option :user="$emp" />
                        @endif
                    @endforeach
                </x-forms.select>
            </div>

            <div class="col-md-4">
                <x-forms.label fieldRequired="true" class="mt-3" fieldId="jobApplicantLabel" :fieldLabel="__('recruit::app.interviewSchedule.stages')">
                </x-forms.label>
                <x-forms.input-group>
                    <select class="form-control select-picker job-app" name="jobStage" id="jobStage"
                        data-live-search="true">
                        <option value="">--</option>
                        @foreach ($stages as $stage)
                            <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                        @endforeach
                    </select>
                </x-forms.input-group>
            </div>

            <div class="col-md-4">
                <x-forms.select fieldId="interview_type" :fieldLabel="__('recruit::modules.interviewSchedule.interviewType')" fieldName="interview_type">
                    <option value="in person">@lang('recruit::app.interviewSchedule.inPerson')</option>
                    <option value="video">@lang('recruit::app.interviewSchedule.video')</option>
                    <option value="phone">@lang('recruit::modules.jobApplication.phone')</option>
                </x-forms.select>
            </div>

            <div class="col-md-4 col-lg-4">
                <x-forms.datepicker fieldId="start_date" fieldRequired="true" :fieldLabel="__('recruit::modules.interviewSchedule.startOn')" fieldName="start_date"
                    :fieldValue="now($company->timezone)->format($company->date_format)" :fieldPlaceholder="__('placeholders.date')" />
            </div>

            <div class="col-md-4 col-lg-4">
                <div class="bootstrap-timepicker timepicker">
                    <x-forms.text :fieldLabel="__('modules.employees.startTime')" :fieldPlaceholder="__('placeholders.hours')" fieldName="start_time" fieldId="start_time"
                        fieldRequired="true" />
                </div>
            </div>

            <div class="col-md-4 d-none" id="phone">
                <x-forms.text fieldId="phone" :fieldLabel="__('recruit::modules.jobApplication.phone')" fieldName="phone" fieldRequired="true" />
            </div>

            @if (in_array('Zoom', $worksuitePlugins))
                <div class="col-md-4 col-lg-4 display" id="end_date_section">
                    <x-forms.datepicker fieldId="end_date" fieldRequired="true" :fieldLabel="__('zoom::modules.zoommeeting.endOn')" fieldName="end_date"
                        :fieldValue="now($company->timezone)
                            ->addHour()
                            ->format($company->date_format)" :fieldPlaceholder="__('placeholders.date')" />
                </div>

                <div class="col-md-4 col-lg-4 display" id="end_time_section">
                    <div class="bootstrap-timepicker timepicker">
                        <x-forms.text :fieldLabel="__('modules.employees.endTime')" :fieldPlaceholder="__('placeholders.hours')" fieldName="end_time" fieldId="end_time"
                            fieldRequired="true" />
                    </div>
                </div>

                <div class="col-md-4 d-none" id=type>
                    <div class="form-group my-3">
                        <x-forms.label fieldId="" :fieldLabel="__('recruit::modules.interviewSchedule.videoType')">
                        </x-forms.label>
                        <div class="d-flex">
                            <x-forms.radio fieldId="video_typeOnline" :fieldLabel="__('recruit::modules.interviewSchedule.zoom')" fieldName="video_type"
                                fieldValue="zoom">
                            </x-forms.radio>
                            <x-forms.radio fieldId="video_type" :fieldLabel="__('recruit::modules.interviewSchedule.other')" fieldValue="other"
                                fieldName="video_type" checked="true">
                            </x-forms.radio>
                        </div>
                    </div>
                </div>

                <div class="row py-20" id="repeat-fields" style="display: none">

                    <div class="col-md-6">
                        <x-forms.text :fieldLabel="__('recruit::modules.interviewSchedule.meetingName')" fieldName="meeting_title" fieldRequired="true"
                            fieldId="meeting_title" fieldPlaceholder="" />
                    </div>

                    <div class="col-md-4">
                        <x-forms.select fieldId="created_by" :fieldLabel="__('recruit::modules.interviewSchedule.meetingHost')" fieldName="created_by" search="true">
                            @foreach ($employees as $emp)
                                @if (!is_null($emp))
                                    <x-user-option :user="$emp" :selected="$emp->id == $user->id" />
                                @endif
                            @endforeach

                        </x-forms.select>
                    </div>


                    <div class="col-md-4">
                        <div class="form-group my-3">
                            <x-forms.label fieldId="host_video" :fieldLabel="__('recruit::modules.interviewSchedule.hostVideoStatus')">
                            </x-forms.label>
                            <div class="d-flex">
                                <x-forms.radio fieldId="host_video1" :fieldLabel="__('app.enable')" fieldName="host_video"
                                    fieldValue="1">
                                </x-forms.radio>
                                <x-forms.radio fieldId="host_video2" :fieldLabel="__('app.disable')" fieldValue="0"
                                    fieldName="host_video" checked="true">
                                </x-forms.radio>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group my-3">
                            <x-forms.label fieldId="participant_video" :fieldLabel="__('recruit::modules.interviewSchedule.participantVideoStatus')">
                            </x-forms.label>
                            <div class="d-flex">
                                <x-forms.radio fieldId="participant_video1" :fieldLabel="__('app.enable')"
                                    fieldName="participant_video" fieldValue="1">
                                </x-forms.radio>
                                <x-forms.radio fieldId="participant_video2" :fieldLabel="__('app.disable')" fieldValue="0"
                                    fieldName="participant_video" checked="true">
                                </x-forms.radio>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <x-forms.checkbox :fieldLabel="__('recruit::modules.interviewSchedule.reminder')" fieldName="send_reminder" fieldId="send_reminder"
                            fieldValue="1" />
                    </div>

                    <div class="col-lg-12 send_reminder_div d-none">
                        <div class="row">
                            <div class="col-lg-4">
                                <x-forms.number class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('recruit::modules.interviewSchedule.remindBefore')"
                                    fieldName="remind_time" fieldId="remind_time" fieldValue="1"
                                    fieldRequired="true" />
                            </div>
                            <div class="col-md-4 mt-2">
                                <x-forms.select fieldId="remind_type" fieldLabel="" fieldName="remind_type"
                                    search="true" class="mt-1">
                                    <option value="day">@lang('app.day')</option>
                                    <option value="hour">@lang('app.hour')</option>
                                    <option value="minute">@lang('app.minute')</option>
                                </x-forms.select>
                            </div>
                        </div>
                    </div>
                </div>
            @endif


            <div class="col-md-12 d-none" id="video">
                <x-forms.text fieldId="other_link" :fieldLabel="__('recruit::modules.interviewSchedule.otherLink')" fieldName="other_link" fieldRequired="true" />
            </div>

            <div class="col-md-12">
                <div class="form-group my-3">
                    <x-forms.textarea fieldId="comment" :fieldLabel="__('recruit::modules.interviewSchedule.commentForInterviewer')" fieldName="comment">
                    </x-forms.textarea>
                </div>
            </div>

            <div class="col-lg-12" id="notify-candidate">
                <x-forms.checkbox :fieldLabel="__('recruit::modules.interviewSchedule.notifyCandidate')" fieldName="notify_c" fieldId="notify_c" fieldValue="1"
                    :checked="true" />
            </div>
            <div class="col-md-12" id="candidate-comment">
                <div class="form-group my-3 mr-md-4">
                    <x-forms.textarea fieldId="candidate_comment" :fieldLabel="__('recruit::modules.interviewSchedule.commentForCandidate')" :popover="__('recruit::modules.interviewSchedule.commentSend')"
                        fieldName="candidate_comment">
                    </x-forms.textarea>
                </div>
            </div>
            <div class="col-md-12" id="reminder">
                <x-forms.checkbox :fieldLabel="__('recruit::modules.interviewSchedule.reminder')" fieldName="send_reminder_all" fieldId="send_reminder_all"
                    fieldValue="1" />

                <div class="send_reminder_all_div d-none">
                    <div class="row">
                        <div class="col-md-6">
                            <x-forms.number class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('recruit::modules.interviewSchedule.remindBefore')"
                                fieldName="remind_time_all" fieldId="remind_time_all" fieldValue="1"
                                fieldRequired="true" />
                        </div>
                        <div class="col-md-6 mt-3">
                            <x-forms.select fieldId="remind_type_all" fieldLabel="" fieldName="remind_type_all"
                                search="true">
                                <option value="day">@lang('app.day')</option>
                                <option value="hour">@lang('app.hour')</option>
                                <option value="minute">@lang('app.minute')</option>
                            </x-forms.select>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </x-form>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.close')</x-forms.button-cancel>
    <x-forms.button-primary id="save-category-form" icon="check">@lang('app.save')</x-forms.button-primary>
</div>
<script>
    $(document).ready(function() {
        $(".select-picker").selectpicker();

        const dp1 = datepicker('#start_date', {
            position: 'bl',
            minDate: new Date(),
            onSelect: (instance, date) => {

                if (typeof dp2.dateSelected !== 'undefined' && dp2.dateSelected.getTime() < date
                    .getTime()) {
                    dp2.setDate(date, true)
                }
                if (typeof dp2.dateSelected === 'undefined') {
                    dp2.setDate(date, true)
                }
                dp2.setMin(date);
            },
            ...datepickerConfig
        });
        @if (in_array('Zoom', $worksuitePlugins))
            const dp2 = datepicker('#end_date', {
                position: 'bl',
                minDate: new Date(),
                onSelect: (instance, date) => {
                    dp1.setMax(date);
                },
                ...datepickerConfig
            });
        @endif
        $('#repeat-event').change(function() {
            $('.repeat-event-div').toggleClass('d-none');
        });
        $('#repeat_type').change(function() {
            var type = $(this).val();
            console.log(type);
            switch (type) {
                case 'day':
                    $('#daily-fields').removeClass('d-none');
                    $('#weekly-fields').addClass('d-none');
                    $('#monthly-fields').addClass('d-none');
                    break;
                case 'week':
                    $('#daily-fields').addClass('d-none');
                    $('#weekly-fields').removeClass('d-none');
                    $('#monthly-fields').addClass('d-none');
                    break;
                case 'month':
                    $('#daily-fields').addClass('d-none');
                    $('#weekly-fields').addClass('d-none');
                    $('#monthly-fields').removeClass('d-none');
                    break;

                default:
                    break;
            }
        });

        $('#send_reminder').change(function() {
            $('.send_reminder_div').toggleClass('d-none');
        });

        $('#send_reminder_all').change(function() {
            $('.send_reminder_all_div').toggleClass('d-none');
        });

        $('#start_time, #end_time').timepicker({
            showMeridian: (company.time_format == 'H:i' ? false : true)
        });

        $('#notify-candidate').change(function() {
            $('#candidate-comment').toggleClass('d-none');
        });

        $("#selectEmployee, #selectClient").selectpicker({
            actionsBox: true,
            selectAllText: "{{ __('modules.permission.selectAll') }}",
            deselectAllText: "{{ __('modules.permission.deselectAll') }}",
            multipleSeparator: " ",
            selectedTextFormat: "count > 8"
        });

        $('#interview_type').change(function() {
            var type = $(this).val();
            console.log(type);
            switch (type) {
                case 'in person':
                    $('#phone').addClass('d-none');
                    $('#video').addClass('d-none');
                    $('#type').addClass('d-none');

                    $('#repeat-fields').hide();
                    $('#end_date_section').hide();
                    $('#end_time_section').hide();
                    $('#reminder').show();

                    break;

                case 'phone':
                    $('#phone').removeClass('d-none');
                    $('#video').addClass('d-none');
                    $('#type').addClass('d-none');

                    $('#repeat-fields').hide();
                    $('#end_date_section').hide();
                    $('#end_time_section').hide();
                    $('#reminder').show();

                    break;

                case 'video':
                    $('#phone').addClass('d-none');
                    $('#video').removeClass('d-none');
                    $('#type').removeClass('d-none');
                    break;

                default:
                    break;
            }
        });

        $('input[type=radio][name=video_type]').change(function() {
            if (this.value == 'zoom') {
                $('#repeat-fields').show();
                $('#end_time_section').show();
                $('#end_date_section').show();
                $('#video').hide();
                $('#reminder').hide();
            } else {
                $('#video').show();
                $('#repeat-fields').hide();
                $('#end_date_section').hide();
                $('#end_time_section').hide();
                $('#reminder').show();
            }
        })

        $('body').off('click').on('click', '#save-category-form', function() {
            var url = "{{ route('job-appboard.interview_store') }}";
            $.easyAjax({
                url: url,
                container: '#save-event-data-form',
                type: "POST",
                data: $('#save-event-data-form').serialize(),
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-category-form",
                success: function(response) {
                    if (response.status == 'success') {
                        $(MODAL_LG).modal('hide');
                        if (response.board == 0) {
                            showTable();
                        } else {
                            loadData();
                        }
                    }
                }
            })
        });

    });
</script>
