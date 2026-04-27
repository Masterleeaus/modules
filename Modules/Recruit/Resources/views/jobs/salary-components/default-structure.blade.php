@if (in_array('Payroll', $worksuitePlugins))
    <div id="default-structure">
        <div class="row">
            <div class="col-md-4 mt-4">
                <h5 class="card-title mb-2 ">@lang('recruit::modules.joboffer.salaryStructure')</h5>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <x-forms.label class="mt-3" fieldId="selectComponentData"
                            :fieldLabel="__('app.select') .' '. __('recruit::modules.joboffer.salaryComponent')">
                </x-forms.label>

            </div>
            <div class="col-md-4">
                <x-forms.input-group>
                    <select class="form-control multiple-users" multiple name="component_id[]"
                            id="selectComponentData" data-live-search="true" data-size="8">
                        <optgroup label="@lang('recruit::modules.joboffer.earning')">
                            @foreach ($earningComponents as $component)
                                <option
                                data-content="<span class='badge badge-pill badge-light border'><div class='d-inline-block mr-1'></div> {{ ($component->component_name) }}</span>" value="{{ $component->id }}">{{ $component->component_name }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="@lang('recruit::modules.joboffer.deduction')">
                            @foreach ($deductionComponents as $component)
                                <option
                                data-content="<span class='badge badge-pill badge-light border'><div class='d-inline-block mr-1'></div> {{ ($component->component_name) }}</span>" value="{{ $component->id }}">{{ $component->component_name }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                </x-forms.input-group>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <x-forms.label class="my-3" fieldId=""
                            :fieldLabel="__('payroll::modules.payroll.salary')" fieldRequired="true">
                </x-forms.label>

            </div>
            <input type="hidden" id="currency_id" name="currency_id" value="{{ $currency ? $currency->id : company()->currency->id }}">
            <div class="col-md-4">
                <x-forms.input-group class="mt-2">

                    <x-slot name="prepend" id="currency">
                        <span
                            class="input-group-text f-14 bg-white-shade">{{ ($currency ? $currency->currency_symbol : company()->currency->currency_symbol) }}</span>
                    </x-slot>

                    <input type="number" class="form-control height-35 f-14" name="annual_salary"
                        onmouseout="changeClc()"
                        id="annual_salary" value="">
                </x-forms.input-group>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <x-forms.label class="mt-3" fieldId="" :fieldLabel="__('payroll::modules.payroll.basicSalary')"
                            fieldRequired="true">
                </x-forms.label>
                <x-forms.input-group>
                    <input type="number" value="" onmouseout="changeClc()" name="basic_salary" id="basic_value"
                        class="form-control height-35 f-15">
                </x-forms.input-group>


            </div>
            <div class="col-lg-4">
                <x-forms.select fieldId="basic-type" :fieldLabel="__('payroll::modules.payroll.basicValueType')"
                                fieldName="basic_value" fieldRequired="true">
                    <option value="fixed">@lang('payroll::modules.payroll.fixed')</option>
                    <option value="ctc_percent">@lang('payroll::modules.payroll.ctcPercent')</option>
                </x-forms.select>
            </div>
        </div>

        <div class="row border-bottom-grey mt-4">
            <div class="col-md-3">

                <h5 class="heading-h5  mb-0 py-4">
                    @lang('payroll::modules.payroll.salaryComponent')</h5>
            </div>
            <div class="col-md-3">
                <h5 class="heading-h5  mb-0 py-4">
                    @lang('payroll::modules.payroll.calculationType')</h5>
            </div>
            <div class="col-md-3">
                <h5 class="heading-h5  mb-0 py-4">
                    @lang('payroll::modules.payroll.monthlyAmount')</h5>
            </div>
            <div class="col-md-3">
                <h5 class="heading-h5  mb-0 py-4">
                    @lang('payroll::modules.payroll.annualAmount')</h5>
            </div>
        </div>

        <div id="components">
            <div class="row">
                <div class="col-md-12">
                    <h3 class="heading-h3  mb-0 py-4">
                        @lang('payroll::modules.payroll.earning')</h3>
                </div>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3">
                            <x-forms.label fieldId=""
                                        :fieldLabel="__('payroll::modules.payroll.basicPay')">
                            </x-forms.label>
                        </div>
                        <div class="col-md-3">

                            --

                        </div>
                        <div class="col-md-3">
                            <x-forms.input-group>

                                <x-slot name="prepend" id="currency">
                                    <span
                                        class="input-group-text f-14 bg-white-shade">{{ ($currency ? $currency->currency_symbol : company()->currency->currency_symbol) }}</span>
                                </x-slot>

                                <input type="text" class="form-control height-35 f-14" name="slack_username"
                                    value="{{ $payrollController->currencyFormatterCustom(0) }}"
                                    readonly>

                            </x-forms.input-group>
                        </div>
                        <div class="col-md-3">
                            <x-forms.input-group>
                                <x-slot name="prepend" id="currency">
                                    <span
                                        class="input-group-text f-14 bg-white-shade">{{ ($currency ? $currency->currency_symbol : company()->currency->currency_symbol) }}</span>
                                </x-slot>

                                <input type="text" class="form-control height-35 f-14" name="slack_username"
                                    value="{{ $payrollController->currencyFormatterCustom(0) }}"
                                    readonly>
                            </x-forms.input-group>

                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="row my-3">
                        <div class="col-md-3">
                            <x-forms.label fieldId=""
                                        :fieldLabel="__('payroll::modules.payroll.fixedAllowance')"
                                        fieldRequired="">
                            </x-forms.label>
                            <p class="f-11 text-grey">@lang('payroll::modules.payroll.extraPay')</p>
                        </div>
                        <div class="col-md-3">

                            <x-forms.label fieldId="" :fieldLabel="__('payroll::modules.payroll.fixedAllowance')" />

                        </div>
                        <div class="col-md-3">
                            <x-forms.label fieldId="" :fieldLabel="currency_format(0, ($currency ? $currency->id : company()->currency->id ))" />
                        </div>

                        <div class="col-md-3">

                            <x-forms.label fieldId="" :fieldLabel="currency_format(0, ($currency ? $currency->id : company()->currency->id ))" />
                        </div>
                    </div>
                </div>
                <div class="col-md-12 salary-total mt-2 rounded bg-light">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="heading-h3 mb-0 py-4">
                                @lang('payroll::modules.payroll.totalSalary')</h3>
                        </div>
                        <div class="col-md-3">
                            <h3 class="heading-h3 mb-0 py-4">
                                {{ currency_format(0, ($currency ? $currency->id : company()->currency->id)) }}</h3>
                        </div>
                        <div class="col-md-3">
                            <h3 class="heading-h3 mb-0 py-4">
                                {{ currency_format(0, ($currency ? $currency->id : company()->currency->id)) }}</h3>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
@endif
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
        // var userId = $('#user_id').val();
        var componentIDs = $('#selectComponentData').val();
        var currency_id = $('#currency_id').val();

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
                currency_id:currency_id,
                componentIds: componentIDs,
                // userId: userId
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

    $(document).ready(function () {

        $(".select-picker").selectpicker();

        $("#selectComponentData").selectpicker({
            actionsBox: true,
            selectAllText: "{{ __('modules.permission.selectAll') }}",
            deselectAllText: "{{ __('modules.permission.deselectAll') }}",
            multipleSeparator: " ",
            selectedTextFormat: "count > 8",
            countSelectedText: function (selected, total) {
                return selected + " {{ __('recruit::messages.skillsSelected') }} ";
            }
        });

        $('#selectComponentData').change(function () {

            const componentId = $(this).val();
            var currencyId = {{ $currency ? $currency->id : company()->currency->currency_symbol}};
            var url = "{{ route('job-offer-letter.fetch_component') }}";
            url = url.replace(':id', componentId);

            $.easyAjax({
                url: url,
                type: "GET",
                disableButton: true,
                blockUI: true,
                data: {
                    component_id: componentId,
                    currencyId:currencyId,
                },
                success: function (response) {
                    if (response.status == 'success') {
                        $('#components').html(response.html);
                    }
                }
            });
        });
    });
</script>
