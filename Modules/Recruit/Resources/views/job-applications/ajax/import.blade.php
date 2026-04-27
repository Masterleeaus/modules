<style>
    .import-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>

<div class="row" id="import_table">
    <div class="col-sm-12">
        <x-form id="import-job-application-data-form">
            <div class="add-project bg-white rounded">

                <div class=" import-heading border-bottom-grey">
                    <h4 class="mb-0 p-20 f-21 font-weight-normal  border-bottom-grey">
                        @lang('recruit::modules.jobApplication.importJobCandidates')</h4>

                    <x-forms.button-secondary class="mr-3" icon="download" onclick="window.location='{{ route('job-applications.import.downloadSampleCsv') }}'">
                        @lang('recruit::app.jobApplication.sampleCsv')
                    </x-forms.button-secondary>
                </div>

                <div class="row py-20">
                    <div class="col-md-12">
                        <x-forms.file :fieldLabel="__('modules.import.file')" fieldName="import_file" fieldId="job_application_import" />
                    </div>
                    <div class="col-md-12">
                        <x-forms.toggle-switch class="mr-0 mr-lg-12"
                            :fieldLabel="__('modules.import.containsHeadings')"
                            fieldName="heading"
                            fieldId="heading"/>
                    </div>
                </div>
                <x-form-actions>
                    <x-forms.button-primary id="import-job-application-form" class="mr-3" icon="arrow-right">@lang('app.uploadNext')
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('job-applications.index')" class="border-0">@lang('app.back')
                    </x-forms.button-cancel>

                </x-form-actions>
            </div>
        </x-form>

    </div>
</div>

<script>

    $(document).ready(function() {

        $("#job_application_import").dropify({
            messages: dropifyMessages
        });

        $('body').on('click', '#import-job-application-form', function() {
            const url = "{{ route('job-applications.import.store') }}";

            $.easyAjax({
                url: url,
                container: '#import-job-application-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#import-job-application-form",
                file: true,
                data: $('#import-job-application-data-form').serialize(),
                success: function(response) {
                    if (response.status == 'success') {
                        $('#import_table').html(response.view);
                    }
                }
            });
        });
    });
</script>
