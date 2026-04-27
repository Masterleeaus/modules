@php
    $addPermission = user()->permission('add_recommendation_status');
@endphp
<div class="row">
    <div class="col-sm-12">
        <x-form id="save-evaluation-data-form">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal  border-bottom-grey">
                    @lang('recruit::modules.skill.createnew')</h4>
                <div class="row p-20">
                    <input type="hidden" value="{{ $interview_schedule_id ? $interview_schedule_id : ''}}"
                           name="interview_schedule_id">
                    <input type="hidden" value="{{ $applicationId ? $applicationId : '' }}" name="job_application_id">
                    <input type="hidden" value="{{ $interview->recruit_interview_stage_id ? $interview->recruit_interview_stage_id : '' }}" name="stage_id">

                    <div class="col-md-4 status">
                        <x-forms.label class="mt-3" fieldId="status_id"
                                       :fieldLabel="__('recruit::modules.jobApplication.status')" fieldRequired="true">
                        </x-forms.label>
                        <x-forms.input-group>
                            <select class="form-control select-picker" name="status_id" id="recomm_status"
                                    data-live-search="true">
                                <option value="">--</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->id }}">{{ $status->status }}</option>
                                @endforeach
                            </select>
                            @if ($addPermission == 'all')
                                <x-slot name="append">
                                    <button type="button"
                                            class="btn btn-outline-secondary border-grey status-setting">@lang('app.add')</button>
                                </x-slot>
                            @endif
                        </x-forms.input-group>
                    </div>

                    <div class="col-md-4">
                        <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('recruit::app.menu.details')"
                                          fieldName="details"
                                          fieldRequired="true" fieldId="details"
                                          :fieldPlaceholder="__('recruit::app.menu.details')">
                        </x-forms.textarea>
                    </div>

                </div>
                <x-form-actions>
                    <x-forms.button-primary id="save-evaluation-form" class="mr-3" icon="check">
                        @lang('app.save')
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('interview-schedule.index')"
                                           class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
                </x-form-actions>
            </div>
        </x-form>

    </div>
</div>

<script>
    $(document).ready(function () {
        $('body').off('click', "#save-evaluation-form").on('click', '#save-evaluation-form', function () {

            const url = "{{ route('evaluation.store') }}";
            $.easyAjax({
                url: url,
                container: '#save-evaluation-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                file: true,
                buttonSelector: "#save-evaluation-form",
                data: $('#save-evaluation-data-form').serialize(),
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

        $('body').off('click', ".status-setting").on('click', '.status-setting', function () {

            const url = "{{ route('recommendation-status.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        init(RIGHT_MODAL);
    });
</script>
