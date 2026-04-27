<div class="row">
    <div class="col-sm-12">
        <div class="add-client bg-white rounded">
            <div id="stage-select-update">
                <x-form id="save-event-data-form">
                    <h4 class="mb-0 p-20 f-21 font-weight-normal  border-bottom-grey">
                        @lang('recruit::modules.interviewSchedule.interview') @lang('app.details')</h4>
                    <div class="row py-20">
                        <input type="hidden" name="stage_id"
                            value="{{ $interview->recruit_interview_stage_id ? $interview->recruit_interview_stage_id : '' }}">

                        <div class="col-md-3">
                            <x-forms.label fieldId="candidate_id" :fieldLabel="__('recruit::modules.interviewSchedule.candidate')" class="mt-3"></x-forms.label>
                            <input type="hidden" name="candidate_id"
                                value="{{ $interview->recruit_job_application_id }}">
                            <select disabled name="candidate_id" id="candidate_id" class="form-control select-picker"
                                data-size="8">
                                <option value="">--</option>
                                @foreach ($candidates as $candidate)
                                    <option @if ($interview->recruit_job_application_id == $candidate->id) selected @endif
                                        value="{{ $candidate->id }}">{{ $candidate->full_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <x-forms.select fieldId="selectEmployee" :fieldLabel="__('recruit::modules.interviewSchedule.interviewer')" fieldName="employee_id[]"
                                fieldRequired="true" search="true" multiple="true">
                                @foreach ($employees as $emp)
                                    @if (!is_null($emp))
                                        <x-user-option :user="$emp" :selected="in_array($emp->id, $selected_employees)" />
                                    @endif
                                @endforeach
                            </x-forms.select>
                        </div>

                        <div class="col-md-3">
                            <x-forms.select fieldId="interview_type" :fieldLabel="__('recruit::modules.interviewSchedule.interviewType')" fieldName="interview_type">
                                <option value="in person" @if ($interview->interview_type == 'in person') selected @endif>
                                    @lang('recruit::app.interviewSchedule.inPerson')</option>
                                <option value="video" @if ($interview->interview_type == 'video') selected @endif>
                                    @lang('recruit::app.interviewSchedule.video')</option>
                                <option value="phone" @if ($interview->interview_type == 'phone') selected @endif>
                                    @lang('recruit::modules.jobApplication.phone')</option>
                            </x-forms.select>
                        </div>

                        <div class="col-md-3 py-20">
                            <x-forms.select fieldId="status" :fieldLabel="__('recruit::modules.jobApplication.status')" fieldName="status" search="true">
                                <option value=""> --</option>
                                <option @if ($interview->status == 'rejected') selected @endif value="rejected"
                                    data-content="<i class='fa fa-circle mr-2 text-brown'></i> @lang('recruit::app.interviewSchedule.rejected')">
                                </option>
                                <option @if ($interview->status == 'hired') selected @endif value="hired"
                                    data-content="<i class='fa fa-circle mr-2 text-light-green'></i> @lang('recruit::app.interviewSchedule.hired')">
                                </option>
                                <option @if ($interview->status == 'pending') selected @endif value="pending"
                                    data-content="<i class='fa fa-circle mr-2 text-yellow'></i> @lang('recruit::app.interviewSchedule.pending')">
                                </option>
                                <option @if ($interview->status == 'completed') selected @endif value="completed"
                                    data-content="<i class='fa fa-circle mr-2 text-blue'></i> @lang('app.completed')">
                                </option>
                                <option @if ($interview->status == 'canceled') selected @endif value="canceled"
                                    data-content="<i class='fa fa-circle mr-2 text-red'></i> @lang('recruit::app.interviewSchedule.canceled')">
                                </option>
                            </x-forms.select>
                        </div>

                        <div class="col-md-3 col-lg-3">
                            <x-forms.datepicker fieldId="scheduleDate" fieldRequired="true" :fieldLabel="__('recruit::modules.interviewSchedule.startOn')"
                                fieldName="scheduleDate" :fieldValue="\Carbon\Carbon::parse($interview->schedule_date)->format(
                                    $company->date_format,
                                )" :fieldPlaceholder="__('placeholders.date')" />
                        </div>

                        <div class="col-md-3 col-lg-3">
                            <div class="bootstrap-timepicker timepicker">
                                <x-forms.text :fieldLabel="__('modules.employees.startTime')" :fieldPlaceholder="__('placeholders.hours')" fieldName="scheduleTime"
                                    fieldId="scheduleTime" fieldRequired="true" :fieldValue="\Carbon\Carbon::parse($interview->schedule_date)->setTimeZone(company()->timezone)->format(
                                        $company->time_format,
                                    )" />

                            </div>
                        </div>

                        <div class="col-md-3 d-none" id="phone">
                            <x-forms.text fieldId="phone" :fieldLabel="__('recruit::modules.jobApplication.phone')" fieldName="phone" :fieldValue="$interview ? $interview->phone : ''"
                                fieldRequired="true" />
                        </div>

                        @if (in_array('Zoom', $worksuitePlugins))

                            <div class="col-md-3 col-lg-3" id="end_date_section">
                                <x-forms.datepicker fieldId="end_date" fieldRequired="true" :fieldLabel="__('recruit::modules.interviewSchedule.endOn')"
                                    fieldName="end_date" :fieldValue="$interview->meeting
                                        ? \Carbon\Carbon::parse($interview->meeting->end_date_time)->format(
                                            $company->date_format,
                                        )
                                        : ''" :fieldPlaceholder="__('placeholders.date')" />
                            </div>

                            <div class="col-md-3 col-lg-3" id="end_time_section">
                                <div class="bootstrap-timepicker timepicker">
                                    <x-forms.text :fieldLabel="__('modules.employees.endTime')" :fieldPlaceholder="__('placeholders.hours')" fieldName="end_time"
                                        fieldId="end_time" fieldRequired="true" :fieldValue="$interview->meeting
                                            ? \Carbon\Carbon::parse($interview->meeting->end_date_time)->format(
                                                $company->time_format,
                                            )
                                            : ''" />

                                </div>
                            </div>

                            <div class="col-md-4 d-none" id=type>
                                <div class="form-group my-3">
                                    <x-forms.label fieldId="" :fieldLabel="__('recruit::modules.interviewSchedule.interviewType')"></x-forms.label>
                                    <div class="d-flex">
                                        <x-forms.radio fieldId="video_typeOnline" :fieldLabel="__('recruit::modules.interviewSchedule.zoom')"
                                            fieldName="video_type" fieldValue="zoom" :checked="$interview->video_type == 'zoom'">
                                        </x-forms.radio>
                                        <x-forms.radio fieldId="video_type" :fieldLabel="__('recruit::modules.interviewSchedule.other')" fieldName="video_type"
                                            fieldValue="other" :checked="$interview->video_type == 'other'">
                                        </x-forms.radio>

                                    </div>
                                </div>
                            </div>

                            <div class="row py-20 display" id="repeat-fields">
                                <div class="col-md-6">
                                    <x-forms.text :fieldLabel="__('recruit::modules.interviewSchedule.meetingName')" fieldName="meeting_title" fieldRequired="true"
                                        fieldId="meeting_title" fieldPlaceholder="" :fieldValue="$interview->meeting ? $interview->meeting->meeting_name : ''" />
                                </div>

                                <div class="col-md-4">
                                    <x-forms.select fieldId="created_by" :fieldLabel="__('recruit::modules.interviewSchedule.meetingHost')" fieldName="created_by"
                                        search="true">
                                        @if ($interview->meeting)
                                            @foreach ($employees as $emp)
                                            @if(!is_null($emp))
                                                <x-user-option :user="$emp" :selected="$emp->id == $interview->meeting->created_by" />
                                            @endif
                                            @endforeach
                                        @else
                                            @foreach ($employees as $emp)
                                            @if(!is_null($emp))
                                                <x-user-option :user="$emp" :selected="$emp->id == $user->id" />
                                            @endif
                                            @endforeach
                                        @endif

                                    </x-forms.select>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group my-3">
                                        <x-forms.label fieldId="host_video" :fieldLabel="__('recruit::modules.interviewSchedule.hostVideoStatus')"></x-forms.label>
                                        <div class="d-flex">
                                            <x-forms.radio fieldId="host_video1" :fieldLabel="__('app.enable')"
                                                fieldName="host_video" fieldValue="1" :checked="$interview->meeting
                                                    ? $interview->meeting->host_video == 1
                                                    : ''">
                                            </x-forms.radio>

                                            <x-forms.radio fieldId="host_video2" :fieldLabel="__('app.disable')"
                                                fieldName="host_video" fieldValue="0" :checked="$interview->meeting
                                                    ? $interview->meeting->host_video == 0
                                                    : ''" checked>
                                            </x-forms.radio>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group my-3">
                                        <x-forms.label fieldId="participant_video" :fieldLabel="__('recruit::modules.interviewSchedule.participantVideoStatus')"></x-forms.label>
                                        <div class="d-flex">
                                            <x-forms.radio fieldId="participant_video1" :fieldLabel="__('app.enable')"
                                                fieldName="participant_video" fieldValue="1" :checked="$interview->meeting
                                                    ? $interview->meeting->participant_video == 1
                                                    : ''">
                                            </x-forms.radio>
                                            <x-forms.radio fieldId="participant_video2" :fieldLabel="__('app.disable')"
                                                fieldValue="0" fieldName="participant_video" :checked="$interview->meeting
                                                    ? $interview->meeting->participant_video == 0
                                                    : ''"
                                                checked>
                                            </x-forms.radio>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12 my-3">
                                    <x-forms.checkbox :fieldLabel="__('recruit::modules.interviewSchedule.reminder')" fieldName="send_reminder"
                                        fieldId="send_reminder" fieldValue="1" />
                                </div>
                                <div class="col-lg-12 send_reminder_div d-none">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <x-forms.number class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('recruit::modules.interviewSchedule.remindBefore')"
                                                fieldName="remind_time" fieldId="remind_time" fieldValue="1"
                                                fieldRequired="true" />
                                        </div>
                                        <div class="col-md-4 mt-2">
                                            <x-forms.select fieldId="remind_type" fieldLabel=""
                                                fieldName="remind_type" search="true" class="mt-1">
                                                <option value="day">@lang('app.day')</option>
                                                <option value="hour">@lang('app.hour')</option>
                                                <option value="minute">@lang('app.minute')</option>
                                            </x-forms.select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-6 py-20 d-none" id="video">
                        <x-forms.text fieldId="other_link" :fieldLabel="__('recruit::modules.interviewSchedule.otherLink')" fieldName="other_link"
                            :fieldValue="$interview ? $interview->other_link : ''" fieldRequired="true" />
                    </div>

                    <div class="col-md-12 ml-2">
                        <div class="form-group my-3">
                            <x-forms.textarea fieldId="comment" :fieldValue="$comment->comment ?? ''" :fieldLabel="__('app.comment')"
                                fieldName="comment">
                            </x-forms.textarea>
                        </div>
                    </div>

                    <div class="col-lg-12 my-2 ml-1 py-20" id="notify-candidate">
                        <x-forms.checkbox :fieldLabel="__('recruit::modules.interviewSchedule.notifyCandidate')" fieldName="notify_c" fieldId="notify_c" fieldValue="1"
                            :checked="$interview->notify_c == 1" />
                    </div>

                    <div class="col-md-12 ml-2" id="candidate-comment">
                        <div class="form-group my-3 mr-md-4">
                            <x-forms.textarea fieldId="candidate_comment" :fieldLabel="__('recruit::modules.interviewSchedule.commentForCandidate')" :popover="__('recruit::modules.interviewSchedule.commentSend')"
                                fieldName="candidate_comment" :fieldValue="$comment->candidate_comment ?? ''">
                            </x-forms.textarea>
                        </div>
                    </div>

                    <div class="ml-2" id="reminder">
                        <div class="col-lg-12 my-2">
                            <x-forms.checkbox :fieldLabel="__('recruit::modules.interviewSchedule.reminder')" fieldName="send_reminder_all"
                                fieldId="send_reminder_all" fieldValue="1" />
                        </div>

                        <div class="col-lg-12 send_reminder_all_div d-none">
                            <div class="row">
                                <div class="col-lg-4">
                                    <x-forms.number class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('recruit::modules.interviewSchedule.remindBefore')"
                                        fieldName="remind_time_all" fieldId="remind_time_all" fieldValue="1"
                                        fieldRequired="true" />
                                </div>
                                <div class="col-md-4 mt-2">
                                    <x-forms.select fieldId="remind_type_all" fieldLabel=""
                                        fieldName="remind_type_all" search="true" class="mt-1">
                                        <option value="day">@lang('app.day')</option>
                                        <option value="hour">@lang('app.hour')</option>
                                        <option value="minute">@lang('app.minute')</option>
                                    </x-forms.select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <x-form-actions>
                        <x-forms.button-primary id="save-event-form" class="mr-3" icon="check">@lang('app.save')
                        </x-forms.button-primary>
                        <x-forms.button-cancel :link="route('interview-schedule.index')" class="border-0">
                            @lang('app.cancel')
                        </x-forms.button-cancel>
                    </x-form-actions>
                </x-form>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {

        $('#repeat-event').change(function() {
            $('.repeat-event-div').toggleClass('d-none');
        });

        $('#repeat_type').change(function() {
            $('.repeat-event-div').toggleClass('d-none');
        });

        $('#send_reminder').change(function() {
            $('.send_reminder_div').toggleClass('d-none');
        });

        $('#send_reminder_all').change(function() {
            $('.send_reminder_all_div').toggleClass('d-none');
        });

        $('#scheduleTime, #end_time').timepicker({
            showMeridian: (company.time_format == 'H:i' ? false : true)
        });

        $('#notify-candidate').change(function() {
            $('#candidate-comment').toggleClass('d-none');
        });

        @if ($interview->notify_c != 1)
            $('#candidate-comment').addClass('d-none');
        @endif

        $("#selectEmployee, #selectClient").selectpicker({
            actionsBox: true,
            selectAllText: "{{ __('modules.permission.selectAll') }}",
            deselectAllText: "{{ __('modules.permission.deselectAll') }}",
            multipleSeparator: " ",
            selectedTextFormat: "count > 8"
        });

        const dp1 = datepicker('#scheduleDate', {
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
            var value = $('input[name="video_type"]:checked').val();
            const dp2 = datepicker('#end_date', {
                position: 'bl',
                minDate: new Date(),
                onSelect: (instance, date) => {
                    dp1.setMax(date);
                },
                ...datepickerConfig
            });
        @endif

        $('#save-event-form').click(function() {

            const url = "{{ route('interview-schedule.update', $interview->id) }}";

            $.easyAjax({
                url: url,
                container: '#save-event-data-form',
                type: "PUT",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-event-form",
                data: $('#save-event-data-form').serialize(),
                success: function(response) {
                    if (response.status == 'success') {
                        if ($(MODAL_XL).hasClass('show')) {
                            $(MODAL_XL).modal('hide');
                        } else if ($(RIGHT_MODAL).hasClass('in')) {
                            document.getElementById('close-task-detail').click();
                            if ($('#interview-schedule-table').length) {
                                window.LaravelDataTables["interview-schedule-table"].draw(
                                    false);
                            } else {
                                window.location.href = response.redirectUrl;
                            }
                        } else {
                            window.location.href = response.redirectUrl;
                        }
                    }
                }
            });
        });

        $('body').on('click', '#save-event-form-stage', function() {
            var id = $('input[name="interviewId"]').val();

            var url = "{{ route('interview-schedule.update', ':id') }}";
            url = url.replace(':id', id);
            $.easyAjax({
                url: url,
                container: '#save-event-data-form-stage',
                type: "PUT",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-event-form-stage",
                data: $('#save-event-data-form-stage').serialize(),
                success: function(response) {
                    if (response.status == 'success') {
                        if ($(MODAL_XL).hasClass('show')) {
                            $(MODAL_XL).modal('hide');
                            window.location.reload();
                        } else if ($(RIGHT_MODAL).hasClass('in')) {
                            document.getElementById('close-task-detail').click();
                            if ($('#interview-schedule-table').length) {
                                window.LaravelDataTables["interview-schedule-table"].draw(
                                    false);
                            } else {
                                window.location.href = response.redirectUrl;
                            }
                        } else {
                            window.location.href = response.redirectUrl;
                        }
                    }
                }
            });
        });

        @if ($interview->video_type == 'zoom')
            $('#end_date_section').show();
            $('#end_time_section').show();
        @else
            $('#end_date_section').hide();
            $('#end_time_section').hide();
        @endif

        @if ($interview->interview_type == 'video')
            $('#type').removeClass('d-none');
        @else
            $('#type').addClass('d-none');
        @endif

        var value = $('input[name="interview_type"]').val();
        @if ($interview->interview_type == 'in person')
            $('#phone').addClass('d-none');
            $('#video').addClass('d-none');
            $('#type').addClass('d-none');
            $('#reminder').show();
        @elseif ($interview->interview_type == 'video')
            @if ($interview->video_type == 'other')
                $('#phone').addClass('d-none');
                $('#video').removeClass('d-none');
                $('#type').removeClass('d-none');
                $('#reminder').show();
            @else
                $('#phone').addClass('d-none');
                $('#video').addClass('d-none');
                $('#type').removeClass('d-none');
            @endif
        @elseif($interview->interview_type == 'phone')
            $('#phone').removeClass('d-none');
            $('#video').addClass('d-none');
            $('#type').addClass('d-none');
            $('#reminder').show();
        @endif

        var value = $('input[name="video_type"]:checked').val();
        if (value == 'other') {
            $('#repeat-fields').hide();
            $('#end_date_section').hide();
            $('#end_time_section').hide();
            $('#reminder').show();
        } else {
            $('#repeat-fields').show();
            $('#end_date_section').show();
            $('#end_time_section').show();
            $('#reminder').hide();
        }

        $('input[type=radio][name=video_type]').change(function() {
            if (this.value == 'zoom') {
                $('#repeat-fields').show();
                $('#end_time_section').show();
                $('#end_date_section').show();
                $('#video').addClass('d-none');
                $('#reminder').hide();
            } else {
                $('#video').removeClass('d-none');
                $('#repeat-fields').hide();
                $('#end_date_section').hide();
                $('#end_time_section').hide();
                $('#reminder').show();
            }
        })

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

        init(RIGHT_MODAL);

    });
</script>
