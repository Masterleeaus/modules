<link rel="stylesheet" href="{{ asset('vendor/css/dropzone.min.css') }}">
<div class="row">
    <div class="col-sm-12">
        <x-form id="save-job-data-form">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal  border-bottom-grey">
                    @lang('recruit::app.menu.jobofferdetails')</h4>
                <div class="row p-20">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-md-3">
                                <x-forms.label fieldRequired="true" class="mt-3" fieldId="joblabel"
                                               :fieldLabel="__('recruit::modules.joboffer.job')"
                                >
                                </x-forms.label>
                                <x-forms.input-group>
                                    @if($jobId) <input type="hidden" name="jobId" value="{{$jobId}}"> @endif
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

                            <div class="col-md-3">
                                <x-forms.label fieldRequired="true" class="mt-3" fieldId="jobApplicantLabel"
                                               :fieldLabel="__('recruit::app.jobOffer.jobApplicant')"
                                >
                                </x-forms.label>
                                <x-forms.input-group>
                                    <select class="form-control select-picker job-app" name="jobApplicant"
                                            id="jobApplicant" data-live-search="true">
                                        <option value="">--</option>
                                        @if($jobId)
                                            @foreach ($jobApplications as $application)
                                                <option
                                                    value="{{ $application->id }}">{{ ($application->full_name) }}</option>
                                            @endforeach
                                        @else
                                            @foreach ($applications as $application)
                                                <option
                                                    value="{{ $application->id }}">{{ ($application->full_name) }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </x-forms.input-group>
                            </div>


                            <div class="col-md-3">
                                <x-forms.datepicker fieldId="start_date" fieldRequired="true"
                                                    :fieldLabel="__('recruit::modules.joboffer.OfferExp')"
                                                    fieldName="jobExpireDate"
                                                    :fieldValue="now($company->timezone)->addDays(7)->format($company->date_format)"
                                                    :fieldPlaceholder="__('placeholders.date')"/>
                            </div>

                            <div class="col-md-3">
                                <x-forms.datepicker fieldId="end_date" fieldRequired="true"
                                                    :fieldLabel="__('recruit::app.jobOffer.expJoinDate')"
                                                    fieldName="expJoinDate"
                                                    :fieldValue="now($company->timezone)->format($company->date_format)"
                                                    :fieldPlaceholder="__('placeholders.date')"/>
                            </div>

                            <div class="col-md-3" id="comp_amount">
                                @if($jobOffer != null && $jobId)
                                    <x-forms.label class="my-3" fieldId="startamtlabel"
                                                    :fieldLabel="__('recruit::app.job.salary')"
                                                     ></x-forms.label>
                                        <span class="f-14 text-dark-grey">{{ $currency->currency_symbol ?? ''}}</span>
                                @else
                                    <x-forms.label class="my-3" fieldId="startamtlabel"
                                                    :fieldLabel="__('recruit::app.job.salary')"
                                                     ></x-forms.label>
                                    <span class="f-14 text-dark-grey" id="currency-symbol"></span>
                                @endif
                                <x-forms.input-group>
                                    <input type="number" min="0" class="form-control height-35 f-14"
                                        name="comp_amount" id="start_amount">
                                </x-forms.input-group>
                            </div>

                            <div class="col-md-3 pay_according" id="payaccording">
                                <x-forms.label fieldRequired="true" class="my-3" fieldId="pay_according"
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
                                        <span class="f-14 text-dark-grey" id="pay_according"> -- </span>
                                        {{-- <input type="number" min="0" class="form-control height-35 f-14" name="pay_according_data" value="" id="pay_according"> --}}
                                        {{-- <select class="form-control select-picker"
                                                id="pay_according" data-live-search="true" disabled>
                                            <option value="">--</option>
                                            <option value="hour">{{ __('recruit::app.job.hour') }}</option>
                                            <option value="day">{{ __('recruit::app.job.day') }}</option>
                                            <option value="week">{{ __('recruit::app.job.week') }}</option>
                                            <option value="month">{{ __('recruit::app.job.month') }}</option>
                                            <option value="year">{{ __('recruit::app.job.year') }}</option>
                                        </select> --}}
                                    </x-forms.input-group>
                                @endif
                            </div>

                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group d-flex mt-3">
                                            <div class="">
                                                <input type="hidden" name="signature" value="off"/>
                                                <x-forms.checkbox fieldId="is_public"
                                                                :fieldLabel="__('recruit::app.jobOffer.SignatureReq')"
                                                                fieldName="signature" value="on"/>

                                            </div>
                                        </div>
                                    </div>
                                    @if (in_array('Payroll', $worksuitePlugins))
                                        <div class="col-md-3 mt-3">
                                            <input type="hidden" name="add_structure" value="0"/>
                                            <x-forms.checkbox :fieldLabel="__('app.add') . ' ' . __('recruit::modules.joboffer.salaryStructure')"
                                            fieldName="add_structure" fieldId="add_structure" fieldValue="1"/>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @if (in_array('Payroll', $worksuitePlugins))
                                <div id="salary-structure" class="d-none p-2">
                                    @include('recruit::jobs.salary-components.default-structure')
                                </div>
                            @endif

                            <div class="col-md-12">
                                <div class="form-group my-3">
                                    <x-forms.label fieldId="description" :fieldLabel="__('recruit::app.menu.description')">
                                    </x-forms.label>
                                    <div id="description"></div>
                                    <textarea name="description" id="description-text" class="d-none"></textarea>
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

                            @if (count($questions) > 0)
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <x-forms.label class="my-3" fieldId=""
                                                    :fieldLabel="__('recruit::modules.jobApplication.additionalQuestions')">
                                        </x-forms.label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="d-flex ">
                                            @forelse($questions as $question)
                                                <x-forms.checkbox :fieldLabel="ucwords($question->question)" fieldName="checkQuestionColumn[]" class="module_checkbox" :fieldId="'column-name-'.$question->id" :fieldValue="$question->id"/>
                                            @empty
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>

                <input type="hidden" name="save_type" value="" id="save_type"/>
                <x-form-actions class="c-inv-btns">
                    <div class="d-flex mb-3">

                        <div class="inv-action dropup mr-3">
                            <button class="btn-primary dropdown-toggle" type="button" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                @lang('app.save')
                                <span><i class="fa fa-chevron-down f-15 text-white"></i></span>
                            </button>
                            <!-- DROPDOWN - INFORMATION -->
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuBtn" tabindex="0">
                                <li>
                                    <a class="dropdown-item f-14 text-dark save-form" href="javascript:;"
                                       data-type="save">
                                        <i class="fa fa-save f-w-500 mr-2 f-11"></i> @lang('app.save')
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item f-14 text-dark save-form" href="javascript:void(0);"
                                       data-type="send">
                                        <i class="fa fa-paper-plane f-w-500  mr-2 f-12"></i> @lang('app.saveSend')
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <x-forms.button-cancel :link="route('job-offer-letter.index')"
                                               class="border-0">@lang('app.cancel')
                        </x-forms.button-cancel>

                    </div>

                </x-form-actions>
            </div>
        </x-form>

    </div>
</div>
<script src="{{ asset('vendor/jquery/dropzone.min.js') }}"></script>

<script>

    $("#annual_salary").on("keyup change", function (e) {
        var annualSalary = $(this).val();
        var monthlySalary = annualSalary / 12;
        let netMonthlySalary = number_format(monthlySalary.toFixed(2));
        $('#monthly_salary').html(netMonthlySalary);
    });

    function changeClc() {
        var basicSalary = $('#basic_value').val();
        if(basicSalary > 0){
            getBasicCalculations();
        }
    }

    $("#basic-type").on("change", function (e) {
        getBasicCalculations();
    });

    function getBasicCalculations() {
        var basicType = $('#basic-type').val();
        var basicValue = $('#basic_value').val();
        var annualSalary = $('#annual_salary').val();

        const url = "{{ route('job-offer-letter.get-salary') }}";
        $.easyAjax({
            url: url,
            type: "GET",
            disableButton: true,
            blockUI: true,
            data: {
                basicType: basicType,
                basicValue: basicValue,
                annualSalary: annualSalary,
            },
            success: function (response) {
                $('#components').html(response.component)
            }
        })
    }

    function number_format(number) {
        let decimals = '{{ currency_format_setting()->no_of_decimal }}';
        let thousands_sep = '{{ currency_format_setting()->thousand_separator }}';
        let currency_position = '{{ currency_format_setting()->currency_position }}';
        let dec_point = '{{ currency_format_setting()->decimal_separator }}';
        // Strip all characters but numerical ones.
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');

        var currency_symbol = '{{ ($currency ? $currency->currency_symbol : company()->currency->currency_symbol ) }}';

        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }

        // number = dec_point == '' ? s[0] : s.join(dec);

        number = s.join(dec);

        switch (currency_position) {
            case 'left':
                number = currency_symbol + number;
                break;
            case 'right':
                number = number + currency_symbol;
                break;
            case 'left_with_space':
                number = currency_symbol + ' ' + number;
                break;
            case 'right_with_space':
                number = number + ' ' + currency_symbol;
                break;
            default:
                number = currency_symbol + number;
                break;
        }
        return number;
    }

    function fetchedCurrency(currencyId){
        var currencyId = currencyId;

        const url = "{{ route('job-offer-letter.fetched-currency') }}";
        $.easyAjax({
            url: url,
            type: "GET",
            disableButton: true,
            blockUI: true,
            data: {
                currencyId: currencyId,
            },
            success: function (response) {
                $('#default-structure').html(response.html)
            }
        })
    }

    $(document).ready(function () {

        quillMention(null, '#description');
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
            var redirect_url = $('#redirect_url').val();
            if (redirect_url != '') {
                window.location.href = decodeURIComponent(redirect_url);
            }
            window.location.href = "{{ route('job-offer-letter.index') }}"
        });

        $('#add_structure').click(function () {
            var check = $('#add_structure').is(":checked") ? true : false;
            if (check == true) {
                $('#salary-structure').removeClass('d-none');
                $('#comp_amount').addClass('d-none');
                $('#payaccording').addClass('d-none');
            } else {
                $('#salary-structure').addClass('d-none');
                $('#comp_amount').removeClass('d-none');
                $('#payaccording').removeClass('d-none');
            }
        });

        $('#selectComponentData').change(function () {

            const componentId = $(this).val();
            var url = "{{ route('job-offer-letter.fetch_component') }}";
            url = url.replace(':id', componentId);

            $.easyAjax({
                url: url,
                type: "GET",
                disableButton: true,
                blockUI: true,
                data: {
                    component_id: componentId
                },
                success: function (response) {
                    if (response.status == 'success') {
                        $('#components').html(response.html);
                    }
                }
            });
        });

        $('#save-job-data-form .save-form').click(function () {

            var type = $(this).data('type');
            $('#save_type').val(type);
            var desc = document.getElementById('description').children[0].innerHTML;
            document.getElementById('description-text').value = desc;

            const url = "{{ route('job-offer-letter.store') }}?type=" + type;

            $.easyAjax({
                url: url,
                container: '#save-job-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                file: true,
                buttonSelector: "#save-job",
                data: $('#save-job-data-form').serialize(),
                success: function (response) {
                    if (response.status == 'success') {
                        if ((myDropzone.getQueuedFiles().length > 0)) {

                            $('#applicationID').val(response.application_id);
                            myDropzone.processQueue();
                        } else if (typeof response.redirectUrl !== 'undefined') {
                            window.location.href = response.redirectUrl;
                        }
                    }
                }
            });
        });

        $('#jobName').change(function () {

            const jobId = $(this).val();
            const url = "{{ route('job-offer-letter.fetch-job-application') }}";

            $.easyAjax({
                url: url,
                type: "GET",
                disableButton: true,
                blockUI: true,
                data: {
                    job_id: jobId
                },
                success: function (response) {
                    if (response.status == 'success') {
                        if (response.currencySymbol != null) {
                            document.getElementById("currency-symbol").innerHTML = response.currencySymbol.currency_symbol;
                            fetchedCurrency(response.currencySymbol);
                        }
                        var options = [];
                        var rData = [];

                        rData = response.applications;

                        $.each(rData, function (index, value) {
                            var selectData = '';
                            selectData = '<option value="' + value.id + '">' + value
                                .full_name + '</option>';
                            options.push(selectData);

                        });

                        $('#pay_according').html(response.payAccording);
                        $("input[name='pay_according']").val(response.job.pay_according);
                        $('#jobApplicant').html('<option value="">--</option>' +
                            options);
                        $('#jobApplicant').selectpicker('refresh');
                        // $('#pay_according').selectpicker('refresh');
                    }
                }
            });
        });

        init(RIGHT_MODAL);
    });
</script>
