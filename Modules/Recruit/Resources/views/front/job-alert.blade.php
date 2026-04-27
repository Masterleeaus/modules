<div class="modal-header">
    <h5 class="modal-title"
        id="modelHeading">@lang('recruit::modules.front.createJobAlert') </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <x-form id="createJobAlert">
            <div class="row">
                <input type="hidden" name="slug" id="slug" value="{{ $company->hash }}">

                <div class="col-sm-12 col-md-6">
                    <x-forms.select fieldId="recruit_job_category_id" fieldRequired="true"
                                    :fieldLabel="__('recruit::modules.job.job') . ' ' . __('app.category')"
                                    fieldName="job_category">
                        <option value="">--</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ ($category->category_name) }}</option>
                        @endforeach
                    </x-forms.select>
                </div>
                <div class="col-sm-12 col-md-6">
                    <x-forms.select fieldId="location_id" fieldRequired="true"
                                    :fieldLabel="__('recruit::modules.jobApplication.currentLocation')"
                                    fieldName="location">
                        <option value="">--</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}">{{ ($location->location) }}</option>
                        @endforeach
                    </x-forms.select>
                </div>
                <div class="col-sm-12 col-md-6">
                    <x-forms.select fieldId="work_experience_id" fieldRequired="true"
                        :fieldLabel="__('recruit::app.job.workexperience')"
                        fieldName="work_experience">
                        <option value="">--</option>
                        @foreach ($workExperiences as $workExperience)
                            <option value="{{ $workExperience->id }}">{{ ($workExperience->work_experience) }}</option>
                        @endforeach
                    </x-forms.select>
                </div>
                <div class="col-sm-12 col-md-6">
                    <x-forms.select fieldId="recruit_job_type_id" fieldRequired="true"
                                :fieldLabel="__('recruit::app.job.jobtype')"
                                fieldName="job_type">
                            <option value="">--</option>
                            @foreach ($jobTypes as $jobType)
                                <option value="{{ $jobType->id }}">{{ ($jobType->job_type) }}</option>
                            @endforeach
                    </x-forms.select>
                </div>

                <div class="col-sm-12">
                    <x-forms.text fieldId="email" :fieldLabel="__('recruit::modules.jobApplication.email')" fieldName="email"
                                  fieldRequired="true"
                                  :fieldPlaceholder="__('placeholders.email')">
                    </x-forms.text>
                </div>
            </div>
        </x-form>
    </div>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.close')</x-forms.button-cancel>
    <x-forms.button-primary id="save-alert" icon="check">@lang('app.save')</x-forms.button-primary>
</div>
@push('scripts')
    <script>


       $('body').off('click', "#save-alert").on('click', '#save-alert', function () {

        const url = "{{ route('front.job_alert_store') }}";
            $.easyAjax({
                url: url,
                container: '#createJobAlert',
                type: "POST",
                data: $('#createJobAlert').serialize(),
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-alert",
                success: function (response) {
                    $('#addJobAlert').modal('hide');
                }
            });
        });

    </script>
@endpush
@stack('scripts')
<script>
    $(".select-picker").selectpicker();
</script>
