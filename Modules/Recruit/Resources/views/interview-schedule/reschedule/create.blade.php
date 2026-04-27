<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('recruit::modules.interviewSchedule.reSchedule')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">×</span></button>
</div>
<x-form id="save-reschedule-data-form">
    <div class="modal-body">
        <input type="hidden" value="{{ $interview->id }}" name="interview_schedule_id">

        <div class="row">
            @foreach ($selected_employees as $select)
                <input type="hidden" value="{{ $select }}" name="employee_id[]">
            @endforeach

            <div class="col-md-6">
                <x-forms.datepicker fieldId="scheduleDate" fieldRequired="true"
                                    :fieldLabel="__('recruit::modules.interviewSchedule.startOn')"
                                    fieldName="scheduleDate"
                                    :fieldValue="\Carbon\Carbon::parse($interview->schedule_date)->format($company->date_format)"
                                    :fieldPlaceholder="__('placeholders.date')"/>          
            </div>

            <div class="col-md-6">
                <div class="bootstrap-timepicker timepicker">
                    <x-forms.text :fieldLabel="__('modules.employees.startTime')"
                                  :fieldPlaceholder="__('placeholders.hours')" fieldName="scheduleTime"
                                  fieldId="scheduleTime"
                                  fieldRequired="true"
                                  :fieldValue="\Carbon\Carbon::parse($interview->schedule_date)->setTimeZone(company()->timezone)->format($company->time_format)"/>

                </div>
            </div>
        </div>
        @if (in_array('Zoom', $worksuitePlugins))
            <div class="row">
                <div class="col-md-6" id="end_date_section">
                    <x-forms.datepicker fieldId="end_date" fieldRequired="true"
                                        :fieldLabel="__('recruit::modules.interviewSchedule.endOn')"
                                        fieldName="end_date"
                                        :fieldValue="($interview->meeting)?\Carbon\Carbon::parse($interview->meeting->end_date_time)->format($company->date_format) : ''"
                                        :fieldPlaceholder="__('placeholders.date')"/>
                </div>

                <div class="col-md-6" id="end_time_section">
                    <div class="bootstrap-timepicker timepicker">
                        <x-forms.text :fieldLabel="__('modules.employees.endTime')"
                                      :fieldPlaceholder="__('placeholders.hours')" fieldName="end_time"
                                      fieldId="end_time" fieldRequired="true"
                                      :fieldValue="($interview->meeting)?\Carbon\Carbon::parse($interview->meeting->end_date_time)->format($company->time_format) : ''"/>
                    </div>
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-md-12" id="notify-candidate">
                <x-forms.checkbox :fieldLabel="__('recruit::modules.interviewSchedule.notifyCandidate')"
                                fieldName="notify_c"
                                fieldId="notify_c" fieldValue="1" :checked="($interview->notify_c == 1)"/>
            </div>

            <div class="col-md-12" id="candidate-comment">
                <div class="form-group">
                    <x-forms.textarea fieldId="candidate_comment" :fieldLabel="__('recruit::modules.interviewSchedule.commentForCandidate')" fieldName="candidate_comment" :fieldValue="$comment->candidate_comment ?? ''">
                    </x-forms.textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.close')</x-forms.button-cancel>
        <x-forms.button-primary id="save-reschedule-form" icon="check">@lang('app.save')</x-forms.button-primary>
    </div>
</x-form>

<script>
    $(document).ready(function () {

        $('#scheduleTime, #end_time').timepicker({
            showMeridian: (company.time_format == 'H:i' ? false : true)
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
            minDate: new Date(),
            position: 'bl',
            onSelect: (instance, date) => {
                dp1.setMax(date);
            },
            ...datepickerConfig
        });
        @endif

        $('#notify-candidate').change(function () {
            $('#candidate-comment').toggleClass('d-none');
        });

        @if($interview->notify_c != 1)
            $('#candidate-comment').addClass('d-none');
        @endif

        @if($interview->video_type == 'zoom')
        $('#end_date_section').show();
        $('#end_time_section').show();
        @else
        $('#end_date_section').hide();
        $('#end_time_section').hide();
        @endif

        $('body').off('click', "#save-reschedule-form").on('click', '#save-reschedule-form', function () {

            const url = "{{ route('interview-schedule.reschedule.store') }}";

            $.easyAjax({
                url: url,
                container: '#save-reschedule-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-reschedule-form",
                data: $('#save-reschedule-data-form').serialize(),
                success: function (response) {
                    if (response.status == 'success') {
                        if ($(MODAL_XL).hasClass('show')) {
                            $(MODAL_XL).modal('hide');
                            window.location.reload();
                        } else {
                            window.location.href = response.redirectUrl;
                        }
                    }
                }
            });
        });

    });
</script>
