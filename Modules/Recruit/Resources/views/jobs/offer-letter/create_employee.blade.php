@php
    $addDesignationPermission = user()->permission('add_designation');
    $addDepartmentPermission = user()->permission('add_department');
@endphp
<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('app.add') @lang('app.employee')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body">
    <x-form id="createEmployeeForm">
        <div class="row">
            <input type="hidden" name="offer_letter_id" value="{{ $offerLetter->id }}">
            <div class="col-md-4">
                <x-forms.text fieldId="employee_id" :fieldLabel="__('modules.employees.employeeId')" fieldName="employee_id" :fieldValue="$lastEmployeeID + 1"
                    fieldRequired="true" :fieldPlaceholder="__('modules.employees.employeeIdInfo')">
                </x-forms.text>
            </div>
            <div class="col-md-4">
                <x-forms.text fieldId="name" :fieldLabel="__('modules.employees.employeeName')" fieldName="name" :fieldValue="$offerLetter->jobApplication->full_name ? $offerLetter->jobApplication->full_name : ''" fieldRequired="true"
                    :fieldPlaceholder="__('placeholders.name')">
                </x-forms.text>
            </div>
            <div class="col-md-4">
                <x-forms.text fieldId="email" :fieldLabel="__('modules.employees.employeeEmail')" fieldName="email" fieldRequired="true" :fieldValue="$offerLetter->jobApplication->email ? $offerLetter->jobApplication->email : ''"
                    :fieldPlaceholder="__('placeholders.email')">
                </x-forms.text>
            </div>
            <div class="col-md-4">
                <x-forms.label class="mt-3" fieldId="password" :fieldLabel="__('app.password')" fieldRequired="true">
                </x-forms.label>
                <x-forms.input-group>

                    <input type="password" name="password" id="password" class="form-control height-35 f-14">
                    <x-slot name="preappend">
                        <button type="button" data-toggle="tooltip" data-original-title="@lang('app.viewPassword')"
                            class="btn btn-outline-secondary border-grey height-35 toggle-password"><i
                                class="fa fa-eye"></i></button>
                    </x-slot>
                    <x-slot name="append">
                        <button id="random_password" type="button" data-toggle="tooltip"
                            data-original-title="@lang('modules.client.generateRandomPassword')"
                            class="btn btn-outline-secondary border-grey height-35"><i
                                class="fa fa-random"></i></button>
                    </x-slot>
                </x-forms.input-group>
                <small class="form-text text-muted">@lang('placeholders.password')</small>
            </div>
            <div class="col-md-4">
                <x-forms.label class="my-3" fieldId="category_id" :fieldLabel="__('app.designation')" fieldRequired="true">
                </x-forms.label>
                <x-forms.input-group>
                    <select class="form-control select-picker" name="designation" id="employee_designation"
                        data-live-search="true">
                        <option value="">--</option>
                        @foreach ($designations as $designation)
                            <option value="{{ $designation->id }}">{{ $designation->name }}</option>
                        @endforeach
                    </select>

                    @if ($addDesignationPermission == 'all' || $addDesignationPermission == 'added')
                        <x-slot name="append">
                            <button id="designation-setting-add" type="button"
                                class="btn btn-outline-secondary border-grey">@lang('app.add')</button>
                        </x-slot>
                    @endif
                </x-forms.input-group>
            </div>
            <div class="col-md-4">
                <x-forms.label class="my-3" fieldId="department" :fieldLabel="__('app.department')" fieldRequired="true">
                </x-forms.label>
                <x-forms.input-group>
                    <select class="form-control select-picker" name="department" id="employee_department"
                        data-live-search="true">
                        <option value="">--</option>
                        @foreach ($teams as $team)
                            <option @if ($offerLetter->jobApplication->job->department_id == $team->id) selected @endif value="{{ $team->id }}">
                                {{ $team->team_name }}</option>
                        @endforeach
                    </select>
                </x-forms.input-group>
            </div>
            <div class="col-md-4">
                <x-forms.select fieldId="country" :fieldLabel="__('app.country')" fieldName="country" search="true">
                    <option value="">--</option>
                    @foreach ($countries as $item)
                        <option data-tokens="{{ $item->iso3 }}"
                            data-content="<span class='flag-icon flag-icon-{{ strtolower($item->iso) }} flag-icon-squared'></span> {{ $item->nicename }}"
                            value="{{ $item->id }}">{{ $item->nicename }}</option>
                    @endforeach
                </x-forms.select>
            </div>
            <div class="col-md-4">
                <x-forms.tel fieldId="mobile" :fieldLabel="__('app.mobile')" fieldName="mobile" :fieldValue="$offerLetter->jobApplication->phone ? $offerLetter->jobApplication->phone : ''"
                    fieldPlaceholder="e.g. 987654321"></x-forms.tel>
            </div>

            <div class="col-md-4">
                <x-forms.datepicker fieldId="joining_date" :fieldLabel="__('modules.employees.joiningDate')" fieldName="joining_date"
                    :fieldPlaceholder="__('placeholders.date')" fieldRequired="true" :fieldValue="now(company()->timezone)->format(company()->date_format)" />
            </div>
            <div class="col-md-4">
                <x-forms.select fieldId="reporting_to" :fieldLabel="__('modules.employees.reportingTo')" fieldName="reporting_to" :fieldPlaceholder="__('placeholders.date')"
                    search="true">
                    <option value="">--</option>
                    @foreach ($employees as $item)
                        @if (!is_null($item->user))
                            <x-user-option :user="$item" />
                        @endif
                    @endforeach
                </x-forms.select>
            </div>
        </div>
    </x-form>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
    <x-forms.button-primary id="save-employee-form" icon="check">@lang('app.save')</x-forms.button-primary>
</div>

<script>


    $('body').on('click', '#save-employee-form', function() {
        var url = "{{ route('job-offer-letter.employee-store') }}";
        $.easyAjax({
            url: url,
            container: '#createEmployeeForm',
            disableButton: true,
            blockUI: true,
            buttonSelector: "#save-employee-form",
            type: "POST",
            data: $('#createEmployeeForm').serialize(),
            success: function(response) {
                if (response.status == 'success') {
                    window.location.reload();
                }
            }
        })
    });
    $(".select-picker").selectpicker();


    $('body').on('click', '#designation-setting-add', function() {

        var url = "{{ route('job-offer-letter.create_designation') }}";
        $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_LG, url);
    });

</script>
