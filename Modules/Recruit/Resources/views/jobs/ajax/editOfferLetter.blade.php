<link rel="stylesheet" href="{{ asset('vendor/css/dropzone.min.css') }}">
<div class="row">
    <div class="col-sm-12">
        <x-form id="save-job-data-form" method="PUT">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal  border-bottom-grey">
                    @lang('recruit::app.menu.offerletter') @lang('app.edit')</h4>
                <div class="row p-20">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-md-3">
                                <x-forms.label fieldRequired="true" class="mt-3" fieldId="joblabel"
                                               :fieldLabel="__('recruit::modules.joboffer.job')"
                                >
                                </x-forms.label>
                                <x-forms.input-group>
                                    <input type="hidden" name="jobId" value="{{$jobOffer->recruit_job_id}}">
                                    <select class="form-control select-picker" name="jobId"
                                            @if($jobOffer->recruit_job_id) disabled @endif
                                            id="jobName" data-live-search="true">
                                        <option value="">--</option>
                                        @foreach ($jobs as $job)
                                            <option @if($jobOffer->recruit_job_id == $job->id) selected
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
                                    <select class="form-control select-picker" name="jobApplicant"
                                            id="jobApplicant" data-live-search="true">
                                        <option value="">--</option>
                                        @foreach ($applications as $application)
                                            <option @if($jobOffer->recruit_job_application_id == $application->id) selected
                                                    @endif value="{{ $application->id }}">{{ ($application->full_name) }}</option>
                                        @endforeach
                                    </select>
                                </x-forms.input-group>
                            </div>

                            <div class="col-md-3">
                                <x-forms.datepicker fieldId="start_date" fieldRequired="true"
                                                    :fieldLabel="__('recruit::modules.joboffer.OfferExp')"
                                                    fieldName="jobExpireDate" :fieldValue="(($jobOffer->job_expire) ? $jobOffer->job_expire->format(company()->date_format) : '')"
                                                    :fieldPlaceholder="__('placeholders.date')"/>
                            </div>

                            <div class="col-md-3">
                                <x-forms.datepicker fieldId="end_date" fieldRequired="true"
                                                    :fieldLabel="__('recruit::app.jobOffer.expJoinDate')"
                                                    fieldName="expJoinDate"
                                                    :fieldValue="(($jobOffer->expected_joining_date) ? $jobOffer->expected_joining_date->format(company()->date_format) : '')"
                                                    :fieldPlaceholder="__('placeholders.date')"/>
                            </div>

                            <div class="col-md-3" id="comp_amount">

                                <x-forms.label class="my-3" fieldId="startamtlabel"
                                                :fieldLabel="__('recruit::app.job.salary')"
                                                fieldRequired="true"></x-forms.label>
                                    <span class="f-14 text-dark-grey">{{ $currency->currency_symbol }}</span>

                                <x-forms.input-group>
                                    <input type="number" min="0" class="form-control height-35 f-14"
                                           name="comp_amount" id="start_amount" value="{{ $jobOffer->comp_amount}}">
                                </x-forms.input-group>

                            </div>

                            <div class="col-md-3 pay_according" id="payaccording">
                                <x-forms.label fieldRequired="true" class="mt-3" fieldId="pay_according"
                                               :fieldLabel="__('recruit::app.job.payaccording')"
                                >
                                </x-forms.label>
                                <x-forms.input-group>
                                    <input type="hidden" name="pay_according" value="{{$jobOffer->pay_according}}">
                                    <select class="form-control select-picker" name="pay_according"
                                            id="pay_according" data-live-search="true"
                                            @if($jobOffer->pay_according) disabled @endif>
                                        <option value="">--</option>
                                        <option @if($jobOffer->pay_according == 'hour') selected
                                                @endif value="hour">{{ __('recruit::app.job.hour') }}</option>
                                        <option @if($jobOffer->pay_according == 'day') selected
                                                @endif value="day">{{ __('recruit::app.job.day') }}</option>
                                        <option @if($jobOffer->pay_according == 'week') selected
                                                @endif value="week">{{ __('recruit::app.job.week') }}</option>
                                        <option @if($jobOffer->pay_according == 'month') selected
                                                @endif value="month">{{ __('recruit::app.job.month') }}</option>
                                        <option @if($jobOffer->pay_according == 'year') selected
                                                @endif value="year">{{ __('recruit::app.job.year') }}</option>
                                    </select>
                                </x-forms.input-group>
                            </div>

                            <div class="col-md-3">
                                <x-forms.select fieldId="status_id" fieldName="status"
                                                :fieldLabel="__('recruit::modules.jobApplication.status')">

                                    <option @if($jobOffer->status == 'pending') selected @endif value="pending"
                                            data-content="<i class='fa fa-circle mr-2 text-yellow'></i> {{ __('recruit::app.job.pending') }}"></option>
                                    <option @if($jobOffer->status == 'draft') selected @endif value="draft"
                                            data-content="<i class='fa fa-circle mr-2 text-brown'></i> {{ __('recruit::app.job.draft') }}">{{ __('recruit::app.job.draft') }}</option>
                                    <option @if($jobOffer->status == 'withdraw') selected @endif value="withdraw"
                                            data-content="<i class='fa fa-circle mr-2 text-blue'></i> {{ __('recruit::app.job.withdraw') }}">{{ __('recruit::app.job.withdraw')
                                    }}</option>
                                    <option @if($jobOffer->status == 'accept') selected @endif value="accept"
                                            data-content="<i class='fa fa-circle mr-2 text-light-green'></i> {{ __('app.accept') }}">{{ __('app.accept')
                                    }}</option>
                                    <option @if($jobOffer->status == 'decline') selected @endif value="decline"
                                            data-content="<i class='fa fa-circle mr-2 text-red'></i> {{ __('app.decline') }}">{{ __('app.decline')
                                    }}</option>
                                    <option @if($jobOffer->status == 'expired') selected @endif value="expired"
                                            data-content="<i class='fa fa-circle mr-2 text-black'></i> {{ __('recruit::app.job.expired') }}">{{ __('recruit::app.job.expired')
                                    }}</option>

                                </x-forms.select>
                            </div>

                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="d-flex mt-3">
                                                <x-forms.checkbox fieldId="is_public"
                                                                :fieldLabel="__('recruit::app.jobOffer.SignatureReq')"
                                                                fieldName="signature"
                                                                :checked="($jobOffer)?$jobOffer->sign_require == 'on' : ''"
                                                />
                                            </div>
                                        </div>
                                    </div>

                                    @if (in_array('Payroll', $worksuitePlugins))
                                        <div class="col-md-3 mt-3">
                                            <input type="hidden" name="add_structure" value="0"/>
                                            <x-forms.checkbox :fieldLabel="__('app.add') . ' ' . __('recruit::modules.joboffer.salaryStructure')"
                                            fieldName="add_structure" fieldId="add_structure" fieldValue="1"
                                            :checked="($jobOffer)?$jobOffer->add_structure == '1' : '0'"/>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if (in_array('Payroll', $worksuitePlugins))
                                <div id="salary-structure" class="d-none p-2">

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
                                                            @if ($earningComponents)
                                                                <optgroup label="@lang('recruit::modules.joboffer.earning')">
                                                                    @foreach ($earningComponents as $component)
                                                                        <option @if(in_array($component->id, $earningsArray)) selected @endif
                                                                        data-content="<span class='badge badge-pill badge-light border'><div class='d-inline-block mr-1'></div> {{ ($component->component_name) }}</span>" value="{{ $component->id }}">{{ $component->component_name }}</option>
                                                                    @endforeach
                                                                </optgroup>
                                                            @endif
                                                            @if ($deductionComponents)
                                                                <optgroup label="@lang('recruit::modules.joboffer.deduction')">
                                                                    @foreach ($deductionComponents as $component)
                                                                        <option @if(in_array($component->id, $deductionsArray)) selected @endif
                                                                        data-content="<span class='badge badge-pill badge-light border'><div class='d-inline-block mr-1'></div> {{ ($component->component_name) }}</span>" value="{{ $component->id }}">{{ $component->component_name }}</option>
                                                                    @endforeach
                                                                </optgroup>

                                                            @endif
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
                                                        id="annual_salary" value="{{ $salaryStructure ? (int)$salaryStructure->annual_salary : '' }}">
                                                </x-forms.input-group>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-4">
                                                <x-forms.label class="mt-3" fieldId="" :fieldLabel="__('payroll::modules.payroll.basicSalary')"
                                                            fieldRequired="true">
                                                </x-forms.label>
                                                <x-forms.input-group>
                                                    <input type="number" value="{{ $salaryStructure ? (int)$salaryStructure->basic_salary : '' }}" onmouseout="changeClc()" name="basic_salary" id="basic_value"
                                                        class="form-control height-35 f-15">
                                                </x-forms.input-group>


                                            </div>
                                            <div class="col-lg-4">
                                                @if($salaryStructure)
                                                    <x-forms.select fieldId="basic-type" :fieldLabel="__('payroll::modules.payroll.basicValueType')"
                                                                    fieldName="basic_value" fieldRequired="true">
                                                        <option value="fixed"@if ($salaryStructure->basic_value_type == 'fixed')selected @endif>@lang('payroll::modules.payroll.fixed')</option>
                                                        <option value="ctc_percent" @if($salaryStructure->basic_value_type == 'ctc_percent') selected @endif>@lang('payroll::modules.payroll.ctcPercent')</option>
                                                    </x-forms.select>
                                                @else
                                                    <x-forms.select fieldId="basic-type" :fieldLabel="__('payroll::modules.payroll.basicValueType')"
                                                                    fieldName="basic_value" fieldRequired="true">
                                                        <option value="fixed">@lang('payroll::modules.payroll.fixed')</option>
                                                        <option value="ctc_percent">@lang('payroll::modules.payroll.ctcPercent')</option>
                                                    </x-forms.select>
                                                @endif
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
                                                            <x-forms.label fieldId="" :fieldLabel="$salaryStructure ? ($salaryStructure->basic_value_type) : '--'" />
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
                                                                    value="{{ $payrollController->currencyFormatterCustom($salaryStructure ? (int)$salaryStructure->basic_salary : 0) }}"
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
                                                                    value="{{ $payrollController->currencyFormatterCustom($salaryStructure ? (int)$salaryStructure->basic_salary * 12 : 0) }}"
                                                                    readonly>
                                                            </x-forms.input-group>

                                                        </div>
                                                    </div>
                                                </div>

                                                @if (!is_null($formSettingsEarn))
                                                    @foreach ($formSettingsEarn as $componentDetail)
                                                        <div class="col-md-12 mt-3">
                                                            <div class="row">
                                                                @if ($componentDetail->component_type == 'earning')
                                                                    <div class="col-md-3">
                                                                        <x-forms.label fieldId=""
                                                                        :fieldLabel="$componentDetail->component_name" />
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
                                                                                value="{{ $payrollController->currencyFormatterCustom($componentDetail->component_value) }}" readonly>
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
                                                                                value="{{ (int)$payrollController->currencyFormatterCustom($componentDetail->component_value * 12) }}"
                                                                                readonly>
                                                                        </x-forms.input-group>

                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                                @if (!is_null($formSettingsDeduction))
                                                    @if (count($formSettingsDeduction) > 0)
                                                        <div class="col-md-12 mt-3">
                                                            <h3 class="heading-h3  mb-0">
                                                                @lang('payroll::modules.payroll.deduction')</h2>
                                                        </div>
                                                    @endif
                                                    @foreach ($formSettingsDeduction as $componentDetail)
                                                        <div class="col-md-12 mt-3">
                                                            <div class="row">
                                                                @if ($componentDetail->component_type == 'deduction')
                                                                    <div class="col-md-3">
                                                                        <x-forms.label fieldId="" :fieldLabel="$componentDetail->component_name" />
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
                                                                                value="{{ $payrollController->currencyFormatterCustom($componentDetail->component_value) }}"
                                                                                readonly>
                                                                        </x-forms.input-group>
                                                                        <input type="hidden" name="component_variable_id"
                                                                            value="{{ $componentDetail->id }}">
                                                                    </div>

                                                                    <div class="col-md-3">
                                                                        <x-forms.input-group>
                                                                            <x-slot name="prepend" id="currency">
                                                                            <span
                                                                                class="input-group-text f-14 bg-white-shade">{{ ($currency ? $currency->currency_symbol : company()->currency->currency_symbol ) }}</span>
                                                                            </x-slot>
                                                                            <input type="text" class="form-control height-35 f-14"
                                                                                name="component_variable_yearly"
                                                                                value="{{ $payrollController->currencyFormatterCustom($componentDetail->component_value * 12) }}"
                                                                                readonly>
                                                                        </x-forms.input-group>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
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
                                                            <x-forms.label fieldId="" :fieldLabel="currency_format(($salaryStructure ? $salaryStructure->fixed_allowance : 0), ($currency ? $currency->id : company()->currency->id ))" />
                                                        </div>

                                                        <div class="col-md-3">

                                                            <x-forms.label fieldId="" :fieldLabel="currency_format(($salaryStructure ? $salaryStructure->fixed_allowance*12 : 0), ($currency ? $currency->id : company()->currency->id ))" />
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
                                                                {{ currency_format(($salaryStructure ? (int)$salaryStructure->annual_salary/12 : 0), ($currency ? $currency->id : company()->currency->id )) }}</h3>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <h3 class="heading-h3 mb-0 py-4">
                                                                {{ currency_format($salaryStructure ? (int)$salaryStructure->annual_salary : 0, ($currency ? $currency->id : company()->currency->id )) }}</h3>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endif

                            <div class="col-md-12">
                                <div class="form-group my-3">
                                    <x-forms.label fieldId="description" :fieldLabel="__('recruit::app.menu.description')">
                                    </x-forms.label>
                                    <div id="description"> {!! $jobOffer->description !!} </div>
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

                            <div class="d-flex flex-wrap p-20" id="aplication-file-list">
                                @foreach($jobOffer->files as $file)
                                    <x-file-card :fileName="$file->filename"
                                                 :dateAdded="$file->created_at->diffForHumans()">
                                        @if ($file->icon == 'images')
                                            <img src="{{ $file->file_url }}">
                                        @else
                                            <i class="fa fa-file-pdf text-lightest"></i>
                                        @endif
                                        <x-slot name="action">
                                            <div class="dropdown ml-auto file-action">
                                                <button
                                                    class="btn btn-lg f-14 p-0 text-lightest  rounded  dropdown-toggle"
                                                    type="button" data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                    <i class="fa fa-ellipsis-h"></i>
                                                </button>

                                                <div
                                                    class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                                                    aria-labelledby="dropdownMenuLink" tabindex="0">
                                                    @if ($file->icon != 'images')
                                                        <a class="cursor-pointer d-block text-dark-grey f-13 pt-3 px-3 "
                                                           target="_blank"
                                                           href="{{ $file->file_url }}">@lang('app.view')</a>
                                                    @endif
                                                    <a class="cursor-pointer d-block text-dark-grey f-13 py-3 px-3 "
                                                       href="{{ route('job-offer-file.download', md5($file->id)) }}">@lang('app.download')</a>

                                                    <a class="cursor-pointer d-block text-dark-grey f-13 pb-3 px-3 delete-file"
                                                       data-row-id="{{ $file->id }}"
                                                       href="javascript:;">@lang('app.delete')</a>
                                                </div>
                                            </div>
                                        </x-slot>
                                    </x-file-card>
                                @endforeach
                            </div>

                            @if (count($questions) > 0)
                                <div class="col-md-12">
                                    <div class="form-group my-3">
                                        <x-forms.label class="my-3" fieldId=""
                                                    :fieldLabel="__('recruit::modules.setting.question')">
                                        </x-forms.label>
                                    </div>
                                </div>
                            @endif

                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="d-flex ">
                                        @forelse($questions as $question)
                                            <x-forms.checkbox :checked="in_array($question->id, $selectedQuestions)"  :fieldLabel="ucwords($question->question)" fieldName="checkQuestionColumn[]" class="module_checkbox" :fieldId="'column-name-'.$question->id" :fieldValue="$question->id"/>
                                        @empty
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <x-form-actions>
                    <x-forms.button-primary id="save-job" class="mr-3" icon="check">@lang('app.save')
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('job-offer-letter.index')" class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
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
                componentIds: componentIDs
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

        $(".select-picker").selectpicker();

        $("#selectComponentData").selectpicker({
            actionsBox: true,
            selectAllText: "{{ __('modules.permission.selectAll') }}",
            deselectAllText: "{{ __('modules.permission.deselectAll') }}",
            multipleSeparator: " ",
            selectedTextFormat: "count > 8",
            countSelectedText: function (selected, total) {
                return selected + " {{ __('recruit::messages.componentSelected') }} ";
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

        $('body').off('click', "#save-job").on('click', '#save-job', function () {

            var desc = document.getElementById('description').children[0].innerHTML;
            document.getElementById('description-text').value = desc;

            const url = "{{ route('job-offer-letter.update', $jobOffer->id) }}";

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

                    if ((myDropzone.getQueuedFiles().length > 0)) {
                        $('#applicationID').val(response.application_id);
                        myDropzone.processQueue();
                    } else if ($(RIGHT_MODAL).hasClass('in')) {
                        document.getElementById('close-task-detail').click();
                        if ($('#offer-table').length) {
                            window.LaravelDataTables["offer-table"].draw(true);
                        } else {
                            window.location.href = response.redirectUrl;
                        }
                    } else {
                        window.location.href = response.redirectUrl;
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
                        var options = [];
                        var rData = [];

                        rData = response.applications;

                        $.each(rData, function (index, value) {
                            var selectData = '';
                            selectData = '<option  value="' + value.id + '">' + value
                                .full_name + '</option>';
                            options.push(selectData);
                        });

                        $('#jobApplicant').html('<option value="">--</option>' +
                            options);
                        $('#jobApplicant').selectpicker('refresh');
                    }
                }
            });
        });

        $('body').on('click', '.delete-file', function () {
            var id = $(this).data('row-id');
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
                    var url = "{{ route('job-offer-file.destroy', ':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {
                            '_token': token,
                            '_method': 'DELETE'
                        },
                        success: function (response) {
                            if (response.status == "success") {
                                window.location.reload();
                            }
                        }
                    });
                }
            });
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

        @if($jobOffer->add_structure == '1')
            $('#salary-structure').removeClass('d-none');
            $('#comp_amount').addClass('d-none');
            $('#payaccording').addClass('d-none');
        @else
            $('#salary-structure').addClass('d-none');
            $('#comp_amount').removeClass('d-none');
            $('#payaccording').removeClass('d-none');
        @endif

        init(RIGHT_MODAL);
    });
</script>
