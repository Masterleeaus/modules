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
                <input type="hidden" id="currency_id" name="currency_id" value="{{ $currency ? $currency->id : company()->currency->id }}">
                <div class="col-md-3">
                    <x-forms.input-group>

                        <x-slot name="prepend" id="currency">
                            <span
                                class="input-group-text f-14 bg-white-shade">{{ ($currency ? $currency->currency_symbol : company()->currency->currency_symbol ) }}</span>
                        </x-slot>

                        <input type="text" class="form-control height-35 f-14" name="slack_username"
                            id=""
                            value="{{ $payrollController->currencyFormatterCustom(0) }}"
                            readonly>

                    </x-forms.input-group>
                </div>
                <div class="col-md-3">
                    <x-forms.input-group>
                        <x-slot name="prepend" id="currency">
                            <span
                                class="input-group-text f-14 bg-white-shade">{{ ($currency ? $currency->currency_symbol : company()->currency->currency_symbol ) }}</span>
                        </x-slot>

                        <input type="text" class="form-control height-35 f-14" name="slack_username"
                            id=""
                            value="{{ $payrollController->currencyFormatterCustom(0) }}"
                            readonly>
                    </x-forms.input-group>

                </div>
            </div>
        </div>

        @if (!is_null($salaryGroup))
            @foreach ($salaryGroup as $component)
                @if ($component->component_type == 'earning')
                    <div class="col-md-12 mt-3">
                        <div class="row">
                            <div class="col-md-3">
                                <x-forms.label fieldId=""
                                :fieldLabel="$component->component_name" />
                            </div>
                            <div class="col-md-3">
                                <x-forms.label fieldId=""
                                    :fieldLabel="__('recruit::modules.joboffer.variable')" />
                            </div>
                            <div class="col-md-3">
                                <x-forms.input-group>
                                    <x-slot name="prepend" id="currency">
                                    <span
                                        class="input-group-text f-14 bg-white-shade">{{ ($currency ? $currency->currency_symbol : company()->currency->currency_symbol ) }}</span>
                                    </x-slot>
                                    <input type="text" class="form-control height-35 f-14"
                                        name="slack_username" id=""
                                        value="{{ $payrollController->currencyFormatterCustom(0) }}">
                                </x-forms.input-group>
                            </div>

                            <div class="col-md-3">
                                <x-forms.input-group>
                                    <x-slot name="prepend" id="currency">
                                        <span
                                            class="input-group-text f-14 bg-white-shade">{{ ($currency ? $currency->currency_symbol : company()->currency->currency_symbol ) }}</span>
                                    </x-slot>
                                    <input type="text" class="form-control height-35 f-14"
                                        name="slack_username" id=""
                                        value="{{ $payrollController->currencyFormatterCustom(0) }}"
                                        readonly>
                                </x-forms.input-group>

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
                    @foreach ($salaryGroup as $component)
                        @if ($component->component_type == 'deduction')
                            <div class="col-md-12 mt-3">
                                <div class="row">
                                    <div class="col-md-3">
                                        <x-forms.label fieldId="" :fieldLabel="$component->component_name" />
                                    </div>
                                    <div class="col-md-3">
                                        <x-forms.label fieldId=""
                                        :fieldLabel="__('recruit::modules.joboffer.variable')" />
                                    </div>
                                    <div class="col-md-3">
                                        <x-forms.input-group>
                                            <x-slot name="prepend" id="currency">
                                            <span
                                                class="input-group-text f-14 bg-white-shade">{{ ($currency ? $currency->currency_symbol : company()->currency->currency_symbol ) }}</span>
                                            </x-slot>
                                            <input type="text" class="form-control height-35 f-14"
                                                name="comoponent_variable_monthly" id=""
                                                value="{{ $payrollController->currencyFormatterCustom(0) }}"
                                                >
                                        </x-forms.input-group>
                                        <input type="hidden" name="component_variable_id"
                                            value="{{ $component->id }}">
                                    </div>

                                    <div class="col-md-3">
                                        <x-forms.input-group>
                                            <x-slot name="prepend" id="currency">
                                            <span
                                                class="input-group-text f-14 bg-white-shade">{{ ($currency ? $currency->currency_symbol : company()->currency->currency_symbol ) }}</span>
                                            </x-slot>
                                            <input type="text" class="form-control height-35 f-14"
                                                name="component_variable_yearly"
                                                value="{{ $payrollController->currencyFormatterCustom(0) }}"
                                                readonly>
                                        </x-forms.input-group>
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
                        {{ currency_format(0, ($currency ? $currency->id : company()->currency->id )) }}</h3>
                </div>
                <div class="col-md-3">
                    <h3 class="heading-h3 mb-0 py-4">
                        {{ currency_format(0, ($currency ? $currency->id : company()->currency->id )) }}</h3>
                </div>
            </div>

        </div>
    </div>
</div>
