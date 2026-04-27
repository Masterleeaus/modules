<div class="row">
    <div class="col-md-12">
        <h3 class="heading-h3  mb-0 py-4">
            @lang('payroll::modules.payroll.earning')</h3>
    </div>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-3">
                <x-forms.label fieldId="" :fieldLabel="__('payroll::modules.payroll.basicSalary')" />
            </div>
            <div class="col-md-3">
                <x-forms.label fieldId="" :fieldLabel="($basicType) ?? '--'" />
            </div>
            <div class="col-md-3">
                <x-forms.input-group>
                    <x-slot name="prepend" id="currency">
                        <span
                            class="input-group-text f-14 bg-white-shade">{{ ($currency ? $currency->currency_symbol : company()->currency->currency_symbol ) }}</span>
                    </x-slot>
                    <input type="text" class="form-control height-35 f-14" name="basic_type" id="basic_type"
                        value="{{ $payrollController->currencyFormatterCustom($basicSalary) }}" readonly>
                </x-forms.input-group>
            </div>

            <div class="col-md-3">
                <x-forms.input-group>
                    <x-slot name="prepend" id="currency">
                        <span
                            class="input-group-text f-14 bg-white-shade">{{ ($currency ? $currency->currency_symbol : company()->currency->currency_symbol ) }}</span>
                    </x-slot>
                    <input type="text" class="form-control height-35 f-14" name="slack_username" id="slack_username"
                        value="{{ $payrollController->currencyFormatterCustom($basicSalary * 12) }}" readonly>

                </x-forms.input-group>
            </div>
        </div>
    </div>

    @if (!is_null($salaryGroup))

        @foreach ($salaryGroup as $key => $value)
            @if ($value->component_type == 'earning')
                <div class="col-md-12 mt-3">
                    <div class="row">
                        <div class="col-md-3">
                            <x-forms.label fieldId="" :fieldLabel="$value->component_name" />
                        </div>
                        <div class="col-md-3">
                            <x-forms.label fieldId="" :fieldLabel="__('recruit::modules.joboffer.variable')" />
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span
                                    class="input-group-text f-14 bg-white-shade">{{ ($currency ? $currency->currency_symbol : company()->currency->currency_symbol ) }}</span>

                                <input type="text" class="form-control height-35 f-14 variable"
                                    name="earning_variable[{{ $value->id }}]"
                                    data-type-id="{{ $value->id }}"
                                    id="variable-{{ $value->id }}"
                                    value="{{ $payrollController->currencyFormatterCustom($value->component_value, $currency->currency_symbol) }}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="input-group">

                                <span
                                    class="input-group-text f-14 bg-white-shade">{{ ($currency ? $currency->currency_symbol : company()->currency->currency_symbol ) }}</span>

                                <input type="text" class="form-control height-35 f-14" name=""
                                    id="variableAnually{{ $value->id }}"
                                    value="{{ $payrollController->currencyFormatterCustom($value->component_value * 12) }}"
                                    readonly>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @endif
    @if (!is_null($salaryGroup))
    <div class="col-md-12">
        <div class="row">
            @if($deductionCount > 0)
                <div class="col-md-12 mt-3">
                    <h3 class="heading-h3  mb-0">
                        @lang('payroll::modules.payroll.deduction')</h2>
                </div>
            @endif
            @foreach ($salaryGroup as $key => $value)
                @if ($value->component_type == 'deduction')
                    <div class="col-md-12 mt-3">
                        <div class="row">
                            <div class="col-md-3">

                                <x-forms.label fieldId="" :fieldLabel="$value->component_name" />
                            </div>
                            <div class="col-md-3">
                                <x-forms.label fieldId="" :fieldLabel="__('recruit::modules.joboffer.variable')" />
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span
                                        class="input-group-text f-14 bg-white-shade">{{ ($currency ? $currency->currency_symbol : company()->currency->currency_symbol ) }}</span>

                                    <input type="text" class="form-control height-35 f-14 deductionVariable"
                                    name="deduction_variable[{{ $value->id }}]"
                                    data-deduction-type-id="{{ $value->id }}"
                                    id="deduction-variable-{{ $value->id }}"
                                    value="{{ $payrollController->currencyFormatterCustom($value->component_value, $currency->currency_symbol) }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="input-group">
                                    <span
                                        class="input-group-text f-14 bg-white-shade">{{ ($currency ? $currency->currency_symbol : company()->currency->currency_symbol ) }}</span>
                                    <input type="text" class="form-control height-35 f-14" name=""
                                        id="deductionVariableAnually{{ $value->id }}"
                                        value="{{ $payrollController->currencyFormatterCustom($value->component_value * 12) }}"
                                        readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif
    <div class="col-md-12">
        <div class="row my-3">
            <div class="col-md-3">
                <x-forms.label fieldId=""
                    :fieldLabel="__('payroll::modules.payroll.fixedAllowance')">
                </x-forms.label>
                <p class="f-11 text-grey">@lang('payroll::modules.payroll.extraPay')</p>
            </div>
            <div class="col-md-3">
                <x-forms.label fieldId="" :fieldLabel="__('payroll::modules.payroll.fixedAllowance')">
                </x-forms.label>
            </div>
            <div class="col-md-3">
                @if ($fixedAllowance >= 0)
                    <input type="hidden" min="0" step=".01" id="fixed_allowance_input"
                        name="fixed_allowance_input" value="{{ $fixedAllowance }}">
                        <x-forms.label fieldId="" class="monthlyFixedAllowance"
                        :fieldLabel="currency_format( $fixedAllowance, ($currency ? $currency->id : company()->currency->id ))" />
                @else
                <x-forms.label fieldId="" class="text-danger monthlyFixedAllowance"
                        :fieldLabel="currency_format($fixedAllowance, ($currency ? $currency->id : company()->currency->id ))" />
                @endif
            </div>

            <div class="col-md-3">
                @if ($fixedAllowance >= 0)
                    <x-forms.label fieldId="" class="yearFixedAllowance"
                            :fieldLabel="currency_format($fixedAllowance * 12, ($currency ? $currency->id : company()->currency->id ))" />
                    <input type="hidden" name="fixedAllowance" class="fixedAllowance" value="{{ $fixedAllowance }}"/>
                @else
                    <x-forms.label fieldId="" class="text-danger yearFixedAllowance"
                            :fieldLabel="currency_format($fixedAllowance * 12, ($currency ? $currency->id : company()->currency->id ))" />
                    <input type="hidden" name="fixedAllowance" value="{{ $fixedAllowance }}"/>
                @endif
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
                    {{ currency_format($annualSalary / 12, ($currency ? $currency->id : company()->currency->id )) }}</h4>
            </div>
            <div class="col-md-3">
                <h3 class="heading-h3 mb-0 py-4">
                    {{ currency_format($annualSalary, ($currency ? $currency->id : company()->currency->id )) }}</h4>
            </div>
        </div>

    </div>
</div>
<script>

    lastValue = 0;
    $('.deductionVariable').on('keyup', function (e){
        var deductionVariable = $(this).val();
        var id = $(this).data('deduction-type-id');
        var yearly = (deductionVariable.replace(/[,]/g, '') * 12);

        $('#deductionVariableAnually' + id).val(yearly);
        salaryClaculationDeduction(deductionVariable.replace(/[,]/g, ''));
    })

    $('.deductionVariable').on('keydown', e => {
        lastValue = $(e.target).val();
        lastValue = lastValue.replace(/[,]/g, '');
    });

    function salaryClaculationDeduction(deductionVariable){
        var fixed = $('.fixedAllowance').val();

        if (fixed == '' || fixed == 'NaN' || fixed == undefined) {
            fixed = 0;
        }

        if (lastValue == '' || lastValue == 'NaN' || lastValue == undefined) {
            lastValue = 0;
        }

        if (deductionVariable == '' || deductionVariable == 'NaN' || deductionVariable == undefined) {
            deductionVariable = 0;
        }

        var newFixed = 0;

        if (lastValue > deductionVariable) {
            newFixed = parseInt(fixed) - (lastValue - deductionVariable);
        }
        if (lastValue < deductionVariable) {
            newFixed = (parseInt(fixed) + (deductionVariable - lastValue));
        }

        if (lastValue == deductionVariable) {
            newFixed = parseInt(fixed);
        }

        if ((deductionVariable == '' || deductionVariable == 'NaN' || deductionVariable == undefined) && (lastValue == '' || lastValue == 'NaN' ||
            lastValue == undefined)) {
            newFixed = fixed;

        }

        $('.fixedAllowance').val(newFixed);

        var yearlyvariableFix = newFixed * 12;

        $('.monthlyFixedAllowance').html(newFixed);

        $('.yearFixedAllowance').html(yearlyvariableFix);

        console.log(lastValue, deductionVariable, fixed, newFixed);

    }

    lastValue = 0;
    yearlySalary = {{ $annualSalary }}
    $('.variable').on('keyup', function (e) {
        var variable = $(this).val();
        var id = $(this).data('type-id');

        var yearly = (variable.replace(/[,]/g, '') * 12);

        $('#variableAnually' + id).val(yearly);
        salaryClaculation(variable.replace(/[,]/g, ''));
    })

    $('.variable').on('keydown', e => {
        lastValue = $(e.target).val();
        lastValue = lastValue.replace(/[,]/g, '');
    });

    function salaryClaculation(variable) {

        var fixed = $('.fixedAllowance').val();

        if (fixed == '' || fixed == 'NaN' || fixed == undefined) {
            fixed = 0;
        }

        if (lastValue == '' || lastValue == 'NaN' || lastValue == undefined) {
            lastValue = 0;
        }

        if (variable == '' || variable == 'NaN' || variable == undefined) {
            variable = 0;
        }

        var newFixed = 0;

        if (lastValue > variable) {
            newFixed = (lastValue - variable) + parseInt(fixed);
        }

        if (lastValue < variable) {
            newFixed = (parseInt(fixed) - (variable - lastValue));
        }

        if (lastValue == variable) {
            newFixed = parseInt(fixed);
        }

        if ((variable == '' || variable == 'NaN' || variable == undefined) && (lastValue == '' || lastValue == 'NaN' ||
            lastValue == undefined)) {
            newFixed = fixed;

        }

        $('.fixedAllowance').val(newFixed);

        var yearlyvariableFix = newFixed * 12;

        $('.monthlyFixedAllowance').html(newFixed);

        $('.yearFixedAllowance').html(yearlyvariableFix);
    }
</script>
