<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('app.add') @lang('modules.lead.followUp')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">×</span></button>
</div>

<div class="modal-body">
    <div class="portlet-body">
        <x-form id="followUpForm" method="POST" class="ajax-form">
            <div class="form-body">
                <div class="row">

                    <div class="col-md-12">
                        <x-cards.data-row :label="__('recruit::modules.interviewSchedule.candidate') . ' ' . __('app.name')" :value="$application->full_name ?? '--'" />
                    </div>

                    <div class="col-md-6">
                        <x-forms.datepicker fieldId="next_follow_up_date" fieldRequired="true"
                            :fieldLabel="__('modules.lead.leadFollowUp')" fieldName="next_follow_up_date"
                            :fieldValue="now(company()->timezone)->format(company()->date_format)"
                            :fieldPlaceholder="__('placeholders.date')" />
                    </div>
                    <div class="col-md-6">
                        <div class="bootstrap-timepicker timepicker">
                            <x-forms.text :fieldLabel="__('modules.timeLogs.startTime')" :fieldPlaceholder="__('placeholders.hours')"
                                fieldName="start_time" fieldId="start_time" fieldRequired="true"
                                :fieldValue="now(company()->timezone)->format(company()->time_format)" />
                        </div>
                    </div>
                    <div class="col-lg-12 my-3">
                        <x-forms.checkbox :fieldLabel="__('modules.tasks.reminder')" fieldName="send_reminder"
                            fieldId="send_reminder" fieldValue="yes" fieldRequired="true" />
                    </div>

                    <div class="col-lg-12 send_reminder_div d-none">
                        <div class="row">
                            <div class="col-lg-6 mt-1">
                                <x-forms.number class="mr-0 mr-lg-2 mr-md-2"
                                    :fieldLabel="__('modules.events.remindBefore')" fieldName="remind_time"
                                    fieldId="remind_time" fieldValue="" fieldRequired="true" />
                            </div>
                            <div class="col-md-6 mt-3">
                                <x-forms.select fieldId="remind_type" fieldLabel="" fieldName="remind_type"
                                    search="true">
                                    <option value="day">@lang('app.day')</option>
                                    <option value="hour">@lang('app.hour')</option>
                                    <option value="minute">@lang('app.minute')</option>
                                </x-forms.select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group my-3">
                            <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('modules.lead.remark')"
                                fieldName="remark" fieldId="remark" fieldPlaceholder="">
                            </x-forms.textarea>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="candidate_id" value="{{ $application->id }}">
            <!--  Used to check from where the create request is from. -->
            <input type="hidden" name="request_from_datatable" value="{{ $requestFromDatatable }}">
        </x-form>
    </div>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.close')</x-forms.button-cancel>
    <x-forms.button-primary id="save-followup" icon="check">@lang('app.save')</x-forms.button-primary>
</div>

<script>
    $(document).ready(function() {

        $(".select-picker").selectpicker();

        $('#start_time').timepicker({
            @if (company()->time_format == 'H:i')
                showMeridian: false,
            @endif
        });

        const dp11 = datepicker('#next_follow_up_date', {
            position: 'bl',
            ...datepickerConfig
        });
        dp11.setMin(new Date())

        $('#send_reminder').change(function() {
            $('.send_reminder_div').toggleClass('d-none');
        })

        // save channel
        $('#save-followup').click(function() {
            $.easyAjax({
                url: "{{ route('candidate-follow-up.store') }}",
                container: '#followUpForm',
                type: "POST",
                blockUI: true,
                data: $('#followUpForm').serialize(),
                success: function(response) {
                    if (response.status == "success") {
                        if (response.requestFromDatatable == "false") {
                            document.getElementById('follow-up-table').innerHTML = response.view;
                            $(MODAL_LG).modal('hide');
                            $(".select-picker").selectpicker();

                            // Delete followup
                            $('body').on('click', '.delete-table-row-followup', function() {
                                var id = $(this).data('follow-id');
                                Swal.fire({
                                    title: "@lang('messages.sweetAlertTitle')",
                                    text: "@lang('messages.recoverRecord')",
                                    icon: 'warning',
                                    showCancelButton: true,
                                    focusConfirm: false,
                                    confirmButtonText: "@lang('messages.confirmDelete')",
                                    cancelButtonText: "@lang('app.cancel')",
                                    customClass: {
                                        confirmButton: 'btn btn-primary mr-3',
                                        cancelButton: 'btn btn-secondary'
                                    },
                                    showClass: {
                                        popup: 'swal2-noanimation',
                                        backdrop: 'swal2-noanimation'
                                    },
                                    buttonsStyling: false
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        var url = "{{ route('candidate-follow-up.destroy', ':id') }}";
                                        url = url.replace(':id', id);

                                        var token = "{{ csrf_token() }}";

                                        $.easyAjax({
                                            type: 'DELETE',
                                            url: url,
                                            blockUI: true,
                                            data: {
                                                '_token': token,
                                            },
                                            success: function(response) {
                                                if (response.status == "success") {
                                                    document.getElementById('follow-up-table').innerHTML = response.view;
                                                    $(MODAL_LG).modal('hide');
                                                    $(".select-picker").selectpicker();
                                                }
                                            }
                                        });
                                    }
                                });
                            });

                            $('#add-candidate-followup').click(function() {
                                let applicationId = $(this).data('application-id');
                                let datatable = $(this).data('datatable');
                                let searchQuery = "?id=" + applicationId + "&datatable=" + datatable;
                                let url = "{{ route('candidate-follow-up.create') }}" + searchQuery;

                                $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
                                $.ajaxModal(MODAL_LG, url);
                            })

                            $('.edit-candidate-followup').click(function() {
                                var id = $(this).data('follow-id');
                                var url = "{{ route('candidate-follow-up.edit', ':id') }}";
                                url = url.replace(':id', id);
                                $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
                                $.ajaxModal(MODAL_LG, url);
                            });
                        } else {
                            window.location.reload();
                        }

                    }
                }
            })
        });
    });
</script>
