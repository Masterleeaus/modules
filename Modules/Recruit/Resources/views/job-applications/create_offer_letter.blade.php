<link rel="stylesheet" href="{{ asset('vendor/css/dropzone.min.css') }}">
<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('app.create') @lang('recruit::modules.job.offerletter')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <x-form id="save-job-data-form">
        <div class="add-client bg-white rounded">
            <div class="row py-20">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="hidden" name="jobApplicant" value="{{ $applicationId }}">
                            <input type="hidden" name="jobId" value="{{ $jobId }}">
                            <input type="hidden" name="board" value="{{ $board }}">
                            <x-forms.label fieldRequired="true" class="mt-3" fieldId="joblabel"
                                           :fieldLabel="__('recruit::modules.joboffer.job')">
                            </x-forms.label>
                            <x-forms.input-group>
                                <select @if($jobId) disabled @endif class="form-control select-picker" name="jobId"
                                        id="jobName" data-live-search="true">
                                    <option value="">--</option>
                                    @foreach ($jobs as $job)
                                        <option @if($jobId && $job->id == $jobId) selected
                                                @endif value="{{ $job->id }}">{{ ($job->title) }}</option>
                                    @endforeach
                                </select>
                            </x-forms.input-group>
                        </div>

                        <div class="col-md-4">
                            <x-forms.label fieldRequired="true" class="mt-3" fieldId="jobApplicantLabel"
                                           :fieldLabel="__('recruit::app.jobOffer.jobApplicant')">
                            </x-forms.label>
                            <x-forms.input-group>
                                <select @if($jobApplications->id) disabled @endif class="form-control select-picker"
                                        name="jobApplicant"
                                        id="jobApplicant" data-live-search="true">
                                    <option value="">--</option>
                                    @foreach ($applications as $application)
                                        <option @if($application->id == $jobApplications->id) selected
                                                @endif value="{{ $application->id }}">{{ ($application->full_name) }}</option>
                                    @endforeach
                                </select>
                            </x-forms.input-group>
                        </div>

                        <div class="col-md-4">
                            <x-forms.datepicker fieldId="start_date" fieldRequired="true"
                                                :fieldLabel="__('recruit::modules.joboffer.OfferExp')"
                                                fieldName="jobExpireDate"
                                                :fieldValue="now($company->timezone)->addDays(7)->format($company->date_format)"
                                                :fieldPlaceholder="__('placeholders.date')"/>
                        </div>

                        <div class="col-md-4">
                            <x-forms.datepicker fieldId="end_date" fieldRequired="true"
                                                :fieldLabel="__('recruit::app.jobOffer.expJoinDate')"
                                                fieldName="expJoinDate"
                                                :fieldValue="now($company->timezone)->format($company->date_format)"
                                                :fieldPlaceholder="__('placeholders.date')"/>
                        </div>

                        <div class="col-md-4" id="comp_amount">

                            <x-forms.label class="my-3" fieldId="startamtlabel"
                                           :fieldLabel="__('recruit::app.job.salary') . ' ' . $company->currency->currency_symbol"
                                           fieldRequired="true"></x-forms.label>
                            <x-forms.input-group>
                                <input type="number" min="0" class="form-control height-35 f-14"
                                       name="comp_amount" id="start_amount">
                            </x-forms.input-group>

                        </div>

                        <div class="col-md-4 pay_according" id="payaccording">
                            <x-forms.label fieldRequired="true" class="mt-3" fieldId="pay_according"
                                           :fieldLabel="__('recruit::app.job.payaccording')"
                            >
                            </x-forms.label>
                            @if($jobOffer != null && $jobId)
                                <x-forms.input-group>
                                    <input type="hidden" name="pay_according" value="{{$jobOffer->pay_according}}">
                                    <select class="form-control select-picker"
                                            id="pay_according" data-live-search="true" disabled>
                                        <option value="">--</option>
                                        <option @if($jobId && $jobOffer->pay_according == "hour") selected
                                                @endif  value="hour">{{ __('recruit::app.job.hour') }}</option>
                                        <option @if($jobId && $jobOffer->pay_according == "day") selected
                                                @endif value="day">{{ __('recruit::app.job.day') }}</option>
                                        <option @if($jobId && $jobOffer->pay_according == "week") selected
                                                @endif value="week">{{ __('recruit::app.job.week') }}</option>
                                        <option @if($jobId && $jobOffer->pay_according == "month") selected
                                                @endif value="month">{{ __('recruit::app.job.month') }}</option>
                                        <option @if($jobId && $jobOffer->pay_according == "year") selected
                                                @endif value="year">{{ __('recruit::app.job.year') }}</option>
                                    </select>
                                </x-forms.input-group>
                            @else
                                <x-forms.input-group>
                                    <input type="hidden" name="pay_according" value="">
                                    <select class="form-control select-picker"
                                            id="pay_according" data-live-search="true" disabled>
                                        <option value="">--</option>
                                        <option value="hour">{{ __('recruit::app.job.hour') }}</option>
                                        <option value="day">{{ __('recruit::app.job.day') }}</option>
                                        <option value="week">{{ __('recruit::app.job.week') }}</option>
                                        <option value="month">{{ __('recruit::app.job.month') }}</option>
                                        <option value="year">{{ __('recruit::app.job.year') }}</option>
                                    </select>
                                </x-forms.input-group>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="d-flex mt-2">
                                            <input type="hidden" name="signature" value="off"/>
                                            <x-forms.checkbox fieldId="is_public"
                                                              :fieldLabel="__('recruit::app.jobOffer.SignatureReq')"
                                                              fieldName="signature" value="on"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="d-flex mt-2">
                                            <input type="hidden" name="sendEmail" value="off"/>
                                            <x-forms.checkbox fieldId="sendEmail"
                                                              :fieldLabel="__('recruit::modules.joboffer.sendEmail')"
                                                              fieldName="sendEmail" value="on"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group my-3">
                                <x-forms.file-multiple class="mr-0 mr-lg-2 mr-md-2"
                                                       :fieldLabel="__('recruit::app.menu.add') . ' ' .__('recruit::app.jobOffer.files')"
                                                       fieldName="resume"
                                                       fieldId="file-upload-dropzone"/>
                                <input type="hidden" name="applicationID" id="applicationID">
                                <input type="hidden" name="type" id="resume">
                            </div>
                        </div>
                        @if (in_array('Payroll', $worksuitePlugins))
                            <div class="col-md-12 mb-3">
                                <p class="position-absolute bottom-0 end-0 text-darkest-grey">@lang('recruit::modules.joboffer.modelActionMsg')<a class="text-darkest-grey" href="{{ route('job-offer-letter.create') }}">&nbsp;@lang('app.clickHere')</a></p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </x-form>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.close')</x-forms.button-cancel>
    <x-forms.button-primary class="save-job-form" id="save-job-form"
                            icon="check">@lang('app.save')</x-forms.button-primary>
</div>

<script src="{{ asset('vendor/jquery/dropzone.min.js') }}"></script>

<script>

    $(document).ready(function () {
        $(".select-picker").selectpicker();

        datepicker('#start_date', {
            minDate: new Date(),
            position: 'bl',
            ...datepickerConfig
        });
        datepicker('#end_date', {
            minDate: new Date(),
            position: 'bl',
            ...datepickerConfig
        });

        Dropzone.autoDiscover = false;
        //Dropzone class
        myDropzone = new Dropzone("div#file-upload-dropzone", {
            dictDefaultMessage: "{{ __('app.dragDrop') }}",
            url: "{{ route('job-offer-file.store') }}",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            paramName: "file",
            maxFilesize: 10,
            maxFiles: 10,
            autoProcessQueue: false,
            uploadMultiple: true,
            addRemoveLinks: true,
            parallelUploads: 10,
            init: function () {
                myDropzone = this;
            }
        });
        myDropzone.on('sending', function (file, xhr, formData) {

            var ids = $('#applicationID').val();
            formData.append('applicationID', ids);
        });
        myDropzone.on('uploadprogress', function () {
            $.easyBlockUI();
        });
        myDropzone.on('completemultiple', function () {
            var msgs = "@lang('messages.updateSuccess')";

            window.location.href = "{{ route('job-appboard.index') }}"
        });

        $('#save-job-form').click(function () {
            const url = "{{ route('job-appboard.offer_letter_store') }}";

            $.easyAjax({
                url: url,
                container: '#save-job-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                file: true,
                buttonSelector: "#save-job-form",
                data: $('#save-job-data-form').serialize(),
                success: function (response) {
                    if (response.status == 'success') {
                        if ((myDropzone.getQueuedFiles().length > 0)) {
                            $('#applicationID').val(response.application_id);
                            myDropzone.processQueue();
                        }
                        $(MODAL_LG).modal('hide');
                        if(response.board == 0){
                            showTable();
                        } else{
                            loadData();
                        }
                    }
                }
            });
        });

    });

</script>
