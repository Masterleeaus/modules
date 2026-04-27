@php
    $addPermission = user()->permission('add_job');
    $skillPermission = user()->permission('manage_skill');
    $addRecruiterPermission = user()->permission('add_recruiter');
    $addJobCategoryPermission = user()->permission('manage_job_category');
    $addJobSubCategoryPermission = user()->permission('manage_job_sub_category');
@endphp
<div class="row">
    <div class="col-sm-12">
        <x-form id="save-job-data-form">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal  border-bottom-grey">
                    @lang('recruit::modules.job.job') @lang('app.details')</h4>
                <div class="row p-20">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-md-3">
                                <x-forms.text fieldId="heading" :fieldLabel="__('recruit::modules.job.jobTitle')"
                                              fieldName="title" fieldRequired="true"
                                              :fieldValue="$job ? $job->title : ''"
                                              :fieldPlaceholder="__('recruit::modules.job.jobTitle')">
                                </x-forms.text>
                            </div>

                            <div class="col-md-3">
                                <x-forms.label fieldRequired="true" class="mt-3" fieldId="job-category"
                                               :fieldLabel="__('recruit::modules.job.job') . ' ' . __('app.category')">
                                </x-forms.label>
                                <x-forms.input-group>
                                    <select class="form-control select-picker" name="category_id"
                                            id="category_id" data-live-search="false">
                                        <option value="">--</option>
                                        @foreach ($categories as $category)
                                            <option
                                                @if(!is_null($job) && ($category->id == $job->recruit_job_category_id))selected
                                                @endif
                                                value="{{ $category->id }}">{{ ($category->category_name) }}</option>
                                        @endforeach
                                    </select>

                                    @if ($addJobCategoryPermission == 'all')
                                        <x-slot name="append">
                                            <button type="button"
                                                    class="btn btn-outline-secondary border-grey job-category-add">@lang('app.add')</button>
                                        </x-slot>
                                    @endif
                                </x-forms.input-group>
                            </div>

                            <div class="col-md-3">
                                <x-forms.label fieldRequired="true" class="mt-3" fieldId="sub_category_id"
                                               :fieldLabel="__('recruit::modules.job.job') . ' ' . __('recruit::modules.job.subCategory')"></x-forms.label>
                                <x-forms.input-group>
                                    <select class="form-control select-picker" name="sub_category_id"
                                            id="sub_category_id"
                                            data-live-search="true">
                                        <option value="">--</option>
                                        @foreach ($Subcategories as $category)
                                            <option
                                                @if(!is_null($job) && ($category->id == $job->recruit_job_sub_category_id))selected
                                                @endif
                                                value="{{ $category->id }}">{{ ($category->sub_category_name) }}</option>
                                        @endforeach
                                    </select>

                                    @if ($addJobSubCategoryPermission == 'all')
                                        <x-slot name="append">
                                            <button type="button"
                                                    class="btn btn-outline-secondary border-grey job-sub-category-add">@lang('app.add')</button>
                                        </x-slot>
                                    @endif
                                </x-forms.input-group>
                            </div>
                            <div class="col-md-3 department">
                                <x-forms.label class="mt-3" fieldId="department_id" :fieldLabel="__('app.department')"
                                               fieldRequired="true">
                                </x-forms.label>
                                <x-forms.input-group>
                                    <select class="form-control select-picker" name="department_id"
                                            id="employee_department" data-live-search="true">
                                        <option value="">--</option>
                                        @foreach ($departments as $team)
                                            <option @if(!is_null($job) && $job->department_id ==  $team->id) selected
                                                    @endif value="{{ $team->id }}">{{ $team->team_name }}</option>
                                        @endforeach
                                    </select>
                                </x-forms.input-group>
                            </div>

                            <div class="col-md-3">
                                <x-forms.label class="mt-3" fieldRequired="true" fieldId="selectEmployeeData"
                                               :fieldLabel="__('recruit::modules.jobApplication.skills')"
                                >
                                </x-forms.label>
                                <x-forms.input-group>
                                    <select class="form-control multiple-users" multiple name="skill_id[]"
                                            id="selectEmployeeData" data-live-search="true" data-size="8">
                                        @foreach ($skills as $skill)
                                            <option
                                                data-content="<span class='badge badge-pill badge-light border'><div class='d-inline-block mr-1'></div> {{ ($skill->name) }}</span>"
                                                @if(!is_null($job) && isset($jobSkills) && in_array($skill->id, $jobSkills)) selected
                                                @endif
                                                value="{{ $skill->id }}">{{ $skill->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($skillPermission == 'all')
                                        <x-slot name="append">
                                            <button type="button"
                                                    class="btn btn-outline-secondary border-grey skill-setting">@lang('app.add')</button>
                                        </x-slot>
                                    @endif
                                </x-forms.input-group>
                            </div>

                            <div class="col-md-3">
                                <x-forms.label fieldRequired="true" class="mt-3" fieldId="selectEmployee"
                                               :fieldLabel="__('app.location')"
                                >
                                </x-forms.label>
                                <x-forms.input-group>
                                    <select class="form-control select-picker" multiple name="location_id[]"
                                            id="selectEmployee" data-live-search="true" data-size="8">
                                        @foreach ($locations as $location)
                                            <option
                                                data-content="<span class='badge badge-pill badge-light border'><div class='d-inline-block mr-1'></div> {{ ($location->location) }}</span>"
                                                @if(!is_null($job) && isset($jobLocation) && in_array($location->id, $jobLocation)) selected
                                                @endif
                                                value="{{ $location->id }}">{{ ($location->location) }}</option>
                                        @endforeach
                                    </select>
                                </x-forms.input-group>
                            </div>

                            <div class="col-md-3">
                                <x-forms.label class="mt-3" fieldRequired="true" fieldId="selectStages"
                                               :fieldLabel="__('recruit::modules.jobApplication.stages')"
                                >
                                </x-forms.label>
                                <x-forms.input-group>
                                    <select class="form-control multiple-users" multiple name="stage_id[]"
                                            id="selectStages" data-live-search="true" data-size="8">
                                        @foreach ($stages as $stage)
                                            <option
                                                data-content="<span class='badge badge-pill badge-light border'><div class='d-inline-block mr-1'></div> {{ ($stage->name) }}</span>"
                                                @if(!is_null($job) && isset($jobInterviews) && in_array($stage->id, $jobInterviews)) selected
                                                @endif
                                                value="{{ $stage->id }}">{{ $stage->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($addPermission == 'all' || $addPermission == 'added')
                                        <x-slot name="append">
                                            <button type="button"
                                                    class="btn btn-outline-secondary border-grey interview-stage">@lang('app.add')</button>
                                        </x-slot>
                                    @endif
                                </x-forms.input-group>
                            </div>

                            <div class="col-md-3">
                                <x-forms.datepicker fieldId="start_date" fieldRequired="true"
                                                    :fieldLabel="__('modules.projects.startDate')"
                                                    fieldName="start_date"
                                                    :fieldValue="now($company->timezone)->format($company->date_format)"
                                                    :fieldPlaceholder="__('placeholders.date')"/>
                            </div>

                            <div class="col-md-3" id="endDateBox">
                                <x-forms.datepicker fieldId="end_date" fieldRequired="true"
                                                    :fieldLabel="__('recruit::modules.job.endDate')"
                                                    fieldName="end_date"
                                                    :fieldValue="now($company->timezone)->format($company->date_format)"
                                                    :fieldPlaceholder="__('placeholders.date')"/>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <div class="d-flex mt-5">
                                        @if (!is_null($job))
                                        <x-forms.checkbox fieldId="without_end_date"
                                                            :checked="($job->end_date == null) ? true : false"
                                                            :fieldLabel="__('recruit::modules.job.noEndDate')"
                                                            fieldName="without_end_date"/>
                                        @else
                                        <x-forms.checkbox fieldId="without_end_date"
                                                            :fieldLabel="__('recruit::modules.job.noEndDate')"
                                                            fieldName="without_end_date"/>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 position">
                                <x-forms.number fieldId="total_positions"
                                                :fieldLabel="__('recruit::modules.job.totalOpening')"
                                                :fieldValue="$job ? $job->total_positions : ''"
                                                fieldName="total_positions" fieldRequired="true">
                                    </x-forms.text>
                            </div>
                            <div class="col-md-3">
                                <x-forms.select fieldId="status" :fieldLabel="__('app.status')" fieldName="status"
                                                search="true">
                                    <option @if(!is_null($job) && $job->status == 'open') selected @endif value="open"
                                            data-content="<i class='fa fa-circle mr-2 text-light-green'></i> @lang('app.open')"></option>
                                    <option @if(!is_null($job) && $job->status == 'closed') selected
                                            @endif value="closed"
                                            data-content="<i class='fa fa-circle mr-2 text-red'></i> @lang('app.closed')"></option>
                                </x-forms.select>
                            </div>

                            <div class="col-md-3 recruiter">
                                <x-forms.label fieldRequired="true" class="mt-3" fieldId="recruiter"
                                               :fieldLabel="__('recruit::app.job.recruiter')"
                                >
                                </x-forms.label>
                                <x-forms.input-group>
                                    <select class="form-control select-picker" name="recruiter"
                                            id="fetch-recruiter" data-live-search="true">
                                        <option value="">--</option>
                                        @foreach ($employees as $employee)
                                            @if(!is_null($employee->user))
                                                <x-user-option :user="$employee->user" :selected="!is_null($job) && $job->recruiter_id == $employee->user_id"/>
                                             @endif
                                        @endforeach
                                    </select>

                                    @if ($addRecruiterPermission == 'all')
                                        <x-slot name="append">
                                            <button type="button"
                                                    class="btn btn-outline-secondary border-grey recruiter-add">@lang('app.add')</button>
                                        </x-slot>
                                    @endif
                                </x-forms.input-group>
                            </div>

                            <div class="col-md-3 jobtype">
                                <x-forms.label fieldRequired="true" class="mt-3" fieldId="jobType"
                                               :fieldLabel="__('recruit::app.job.jobtype')"
                                >
                                </x-forms.label>
                                <x-forms.input-group>
                                    <select class="form-control select-picker" name="job_type_id"
                                            id="job-type" data-live-search="true">
                                        <option value="">--</option>
                                        @foreach ($jobTypes as $jobType)
                                            <option @if(!is_null($job) && $job->recruit_job_type_id == $jobType->id ) selected
                                                    @endif value="{{ $jobType->id }}">{{ $jobType->job_type }}</option>
                                        @endforeach
                                    </select>
                                    <x-slot name="append">
                                        <button type="button"
                                                class="btn btn-outline-secondary border-grey job-type">@lang('app.add')</button>
                                    </x-slot>
                                </x-forms.input-group>
                            </div>

                            <div class="col-md-3 work_experience_1">
                                <x-forms.label fieldRequired="true" class="mt-3" fieldId=""
                                               :fieldLabel="__('recruit::app.job.workexperience')"
                                >
                                </x-forms.label>
                                <x-forms.input-group>
                                    <select class="form-control select-picker" name="work_experience"
                                            id="work_experience_1" data-live-search="true">
                                        <option value="">--</option>
                                        @foreach ($workExperience as $key => $experience)
                                            <option
                                                @if(!is_null($job) && $job->recruit_work_experience_id == $experience->id ) selected
                                                @endif value="{{ $experience->id }}">{{ ($experience->work_experience) }}</option>
                                        @endforeach
                                    </select>
                                    <x-slot name="append">
                                        <button type="button"
                                                class="btn btn-outline-secondary border-grey work-experience">@lang('app.add')</button>
                                    </x-slot>
                                </x-forms.input-group>
                            </div>

                             <!-- CURRENCY START -->
                             <div class="col-md-6 col-lg-3">
                                <div class="form-group c-inv-select mb-lg-0 mb-md-0 mb-4">
                                    <x-forms.label class="mt-3" fieldId="currency_id" :fieldLabel="__('modules.invoices.currency')">
                                    </x-forms.label>

                                    <div class="select-others height-35 rounded">
                                        <select class="form-control select-picker" name="currency_id" id="currency_id">
                                            @foreach ($currencies as $currency)
                                            <option @if($company->currency->id == $currency->id) selected @endif
                                            value="{{ $currency->id }}">
                                                {{ $currency->currency_code . ' (' . $currency->currency_symbol . ')' }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- CURRENCY END -->

                            <div class="col-md-3 paytype">
                                <x-forms.label fieldRequired="true" class="mt-3" fieldId="paytype"
                                               :fieldLabel="__('recruit::app.job.paytype')"
                                >
                                </x-forms.label>
                                <x-forms.input-group>
                                    <select class="form-control select-picker" name="paytype"
                                            id="paytype" data-live-search="true">
                                        <option value="">--</option>
                                        <option @if(!is_null($job) && $job->pay_type == 'Range') selected
                                                @endif value="Range">{{ __('recruit::app.job.range') }}</option>
                                        <option @if(!is_null($job) && $job->pay_type == 'Starting') selected
                                                @endif value="Starting">{{ __('recruit::app.job.Startingamt') }}</option>
                                        <option @if(!is_null($job) && $job->pay_type == 'Maximum') selected
                                                @endif value="Maximum">{{ __('recruit::app.job.Maximumamt') }}</option>
                                        <option @if(!is_null($job) && $job->pay_type == 'Exact Amount') selected
                                                @endif value="Exact Amount">{{ __('recruit::app.job.exactamt') }}</option>

                                    </select>
                                </x-forms.input-group>
                            </div>


                            <div class="col-md-6" id="amount_field">
                                <div class="row">
                                    <div class="col-md-6" id="start_amt">

                                        <x-forms.label fieldRequired="true" class="my-3" fieldId="Startingamt"
                                                       :fieldLabel="__('recruit::app.job.Startingamt')"></x-forms.label>


                                        <x-forms.input-group>
                                            <input type="number" value="{{$job ? $job->start_amount : '' }}" min="0"
                                                   class="form-control height-35 f-14"
                                                   name="start_amount" id="start_amount"
                                                   :fieldValue="$job ? $job->start_amount : '' " fieldRequired>
                                        </x-forms.input-group>

                                    </div>
                                    <div class="col-md-6" id="end_amt">
                                        <x-forms.label fieldRequired="true" class="my-3" fieldId="endamtlabel"
                                                       :fieldLabel="__('recruit::app.job.maxsal')"></x-forms.label>

                                        <x-forms.input-group>
                                            <input type="number" value="{{$job ? $job->end_amount : '' }}" min="0"
                                                   class="form-control height-35 f-14"
                                                   name="end_amount" id="end_amount" fieldRequired>
                                        </x-forms.input-group>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 pay_according" id="payaccording">
                                <x-forms.label fieldRequired="true" class="mt-3" fieldId="pay_according"
                                               :fieldLabel="__('recruit::app.job.payaccording')"
                                >
                                </x-forms.label>
                                <x-forms.input-group>
                                    <select class="form-control select-picker" name="pay_according"
                                            id="pay_according" data-live-search="true">
                                        <option value="">--</option>
                                        <option @if(!is_null($job) && $job->pay_according == 'hour') selected
                                                @endif  value="Hour">{{ __('recruit::app.job.hour') }}</option>
                                        <option @if(!is_null($job) && $job->pay_according == 'day') selected
                                                @endif value="Day">{{ __('recruit::app.job.day') }}</option>
                                        <option @if(!is_null($job) && $job->pay_according == 'week') selected
                                                @endif value="Week">{{ __('recruit::app.job.week') }}</option>
                                        <option @if(!is_null($job) && $job->pay_according == 'month') selected
                                                @endif value="Month">{{ __('recruit::app.job.month') }}</option>
                                        <option @if(!is_null($job) && $job->pay_according == 'year') selected
                                                @endif value="Year">{{ __('recruit::app.job.year') }}</option>
                                    </select>
                                </x-forms.input-group>
                            </div>

                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <div class="d-flex mt-5">
                                        @if (!is_null($job))
                                        <x-forms.checkbox fieldId="remote_job"
                                                        :checked="($job->remote_job == 'yes') ? true : false"
                                                          :fieldLabel="__('recruit::modules.job.remoteJob')"
                                                          fieldValue="yes" fieldName="remote_job"/>
                                        @else
                                        <x-forms.checkbox fieldId="remote_job"
                                                        :fieldLabel="__('recruit::modules.job.remoteJob')"
                                                        fieldValue="yes" fieldName="remote_job"/>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <div class="d-flex mt-5">
                                        <x-forms.checkbox fieldId="disclose_salary"
                                                        :fieldLabel="__('recruit::modules.job.discloseSalary')"
                                                        fieldValue="yes" fieldName="disclose_salary"/>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group my-3">
                                    <x-forms.label class="my-3" fieldId="job_description"
                                                   :fieldLabel="__('recruit::modules.job.jobDescription')">
                                    </x-forms.label>
                                    <div id="job_description">  {!! $job ? $job->job_description : '' !!}</div>
                                    <textarea name="job_description" id="description-text" class="d-none">
                                    </textarea>
                                </div>
                            </div>
                            @if ($job)
                                <div class="col-md-6">
                                    <x-forms.text fieldId="meta-title" :fieldLabel="__('recruit::modules.job.metaTitle')"
                                                fieldName="meta_title"
                                                fieldValue="{{$job->meta_details ? $job->meta_details['title'] : ''}}"
                                                :fieldPlaceholder="__('recruit::modules.job.metaTitle')">
                                    </x-forms.text>
                                </div>

                                <div class="col-md-6">
                                    <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2"
                                                    :fieldLabel="__('recruit::modules.job.metaDescription')"
                                                    fieldName="meta_description"
                                                    fieldValue="{{$job->meta_details ? $job->meta_details['description'] : ''}}"
                                                    fieldId="meta_description"
                                                    :fieldPlaceholder="__('recruit::modules.job.metaDescription')">
                                    </x-forms.textarea>
                                </div>
                            @endif

                            <div class="col-md-12">
                                <div class="form-group">
                                    <x-forms.label class="" fieldId=""
                                                   :fieldLabel="__('recruit::modules.job.requiredColumn')"
                                                   :popover="__('recruit::modules.job.requiredFiled')">
                                    </x-forms.label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-2">
                                <div class="form-group">
                                    <div class="d-flex">
                                        <x-forms.checkbox :fieldLabel="__('recruit::modules.job.photoRequired')"
                                                          fieldName="is_photo_require"
                                                          fieldId="is_photo_require" fieldValue="1"
                                                          :checked="$job ? $job->is_photo_require : ''"/>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-2">
                                <div class="form-group">
                                    <div class="d-flex ">
                                        <x-forms.checkbox :fieldLabel="__('recruit::modules.job.resumeRequired')"
                                                          fieldName="is_resume_require"
                                                          fieldId="is_resume_require" fieldValue="1"
                                                          :checked="$job ? $job->is_resume_require : ''"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-2">
                                <div class="form-group">
                                    <div class="d-flex ">
                                        <x-forms.checkbox
                                            :fieldLabel="__('recruit::modules.jobApplication.dateOfBirth')"
                                            fieldName="is_dob_require"
                                            fieldId="is_dob_require" fieldValue="1"
                                            :checked="$job ? $job->is_dob_require : ''"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-2">
                                <div class="form-group">
                                    <div class="d-flex ">
                                        <x-forms.checkbox :fieldLabel="__('recruit::modules.jobApplication.gender')"
                                                          fieldName="is_gender_require"
                                                          fieldId="is_gender_require" fieldValue="1"
                                                          :checked="$job ? $job->is_gender_require : ''"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-2">
                                <div class="form-group">
                                    <div class="d-flex ">
                                        <x-forms.checkbox
                                            :fieldLabel="__('recruit::modules.jobApplication.currentCtc')"
                                            fieldName="is_currentctc_require"
                                            fieldId="is_currentctc_require" fieldValue="1"
                                            :checked="$job ? $job->is_dob_require : ''"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-2">
                                <div class="form-group">
                                    <div class="d-flex ">
                                        <x-forms.checkbox
                                            :fieldLabel="__('recruit::modules.jobApplication.expectedCtc')"
                                            fieldName="is_expectedctc_require"
                                            fieldId="is_expectedctc_require" fieldValue="1"
                                            :checked="$job ? $job->is_expectedctc_require : ''"/>
                                    </div>
                                </div>
                            </div>

                            @if (count($questions) > 0)
                                <div class="col-md-12 pt-lg-2">
                                    <div class="form-group">
                                        <x-forms.label class="" fieldId=""
                                                    :fieldLabel="__('recruit::modules.jobApplication.additionalQuestions')">
                                        </x-forms.label>
                                    </div>
                                </div>

                                @forelse($questions as $question)
                                    <div class="col-md-6 col-lg-2">
                                        <div class="form-group">
                                            <x-forms.checkbox :fieldLabel="$question->question" fieldName="checkQuestionColumn[]" class="module_checkbox" :fieldId="'column-name-'.$question->id" :fieldValue="$question->id"/>
                                        </div>
                                    </div>
                                @empty
                                @endforelse
                            @endif
                        </div>
                    </div>
                </div>

                <x-form-actions>
                    <x-forms.button-primary id="save-job" class="mr-3" icon="check">@lang('app.save')
                    </x-forms.button-primary>
                    <x-forms.button-secondary class="mr-3" id="save-more-job-form"
                                              icon="check-double">@lang('app.saveAddMore')
                    </x-forms.button-secondary>
                    <x-forms.button-cancel :link="route('jobs.index')" class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
                </x-form-actions>
            </div>
        </x-form>
    </div>
</div>

<script>
    $(document).ready(function () {
        datepicker('#start_date', {
            position: 'bl',
            ...datepickerConfig
        });
        datepicker('#end_date', {
            position: 'bl',
            ...datepickerConfig
        });

        quillImageLoad('#job_description');

        $("#selectEmployeeData").selectpicker({
            actionsBox: true,
            selectAllText: "{{ __('modules.permission.selectAll') }}",
            deselectAllText: "{{ __('modules.permission.deselectAll') }}",
            multipleSeparator: " ",
            selectedTextFormat: "count > 8",
            countSelectedText: function (selected, total) {
                return selected + " {{ __('recruit::messages.skillsSelected') }} ";
            }
        });

        $("#selectStages").selectpicker({
            actionsBox: true,
            selectAllText: "{{ __('modules.permission.selectAll') }}",
            deselectAllText: "{{ __('modules.permission.deselectAll') }}",
            multipleSeparator: " ",
            selectedTextFormat: "count > 8",
            countSelectedText: function (selected, total) {
                return selected + " {{ __('recruit::messages.skillsSelected') }} ";
            }
        });

        $("#selectEmployee").selectpicker({
            actionsBox: true,
            selectAllText: "{{ __('modules.permission.selectAll') }}",
            deselectAllText: "{{ __('modules.permission.deselectAll') }}",
            multipleSeparator: " ",
            selectedTextFormat: "count > 8",
            countSelectedText: function (selected, total) {
                return selected + " {{ __('recruit::messages.skillsSelected') }} ";
            }
        });

        $('#amount_field, #payaccording').hide();

        $('#without_end_date').click(function () {
            var check = $('#without_end_date').is(":checked") ? true : false;
            if (check == true) {
                $('#endDateBox').hide();
            } else {
                $('#endDateBox').show();
            }
        });

        @if(!is_null($job) && $job->pay_type != 'range')
        $('#amount_field, #payaccording').show();
        $('#end_amt').hide();
        $('#start_amt').removeClass('col-md-6');
        $('#start_amt').addClass('col-md-12');

        switch ($('#paytype').val()) {
            case 'Starting' :
                $('#start_amt > label').html("{{ __('recruit::app.job.Startingamt') }}<sup class='f-14 mr-1'>*</sup>");
                break;
            case 'Maximum' :
                $('#start_amt > label').html("{{ __('recruit::app.job.maxsal') }} <sup class='f-14 mr-1'>*</sup>");
                break;
            case 'Exact Amount' :
                $('#start_amt > label').html("{{ __('recruit::app.job.exactamt') }} <sup class='f-14 mr-1'>*</sup>");
                break;
        }
        @elseif(!is_null($job) && $job->pay_type == 'range')
        $('#start_amt > label').html("{{ __('recruit::app.job.minsal') }} <sup class='f-14 mr-1'>*</sup>");
        $('#amount_field, #end_amt, #payaccording').show();
        $('#start_amt').removeClass('col-md-12');
        $('#start_amt').addClass('col-md-6');
        @endif

        $('#without_end_date').click(function () {
            var check = $('#without_end_date').is(":checked") ? true : false;
            if (check == true) {
                $('#endDateBox').hide();
            } else {
                $('#endDateBox').show();
            }
        });

        @if (!is_null($job) && $job->end_date == null)
            $('#endDateBox').hide();
        @endif

        $('#paytype').change(function () {
            if ($('#paytype').val() != 'Range') {
                $('#amount_field, #payaccording').show();
                $('#end_amt').hide();
                $('#start_amt').removeClass('col-md-6');
                $('#start_amt').addClass('col-md-12');

                switch ($('#paytype').val()) {
                    case 'Starting' :
                        $('#start_amt > label').html("{{ __('recruit::app.job.Startingamt') }} <sup class='f-14 mr-1'>*</sup>");
                        break;
                    case 'Maximum' :
                        $('#start_amt > label').html("{{ __('recruit::app.job.maxsal') }} <sup class='f-14 mr-1'>*</sup>");
                        break;
                    case 'Exact Amount' :
                        $('#start_amt > label').html("{{ __('recruit::app.job.exactamt') }} <sup class='f-14 mr-1'>*</sup>");
                        break;
                }
            } else {
                $('#start_amt > label').html("{{ __('recruit::app.job.minsal') }} <sup class='f-14 mr-1'>*</sup>");
                $('#amount_field, #end_amt, #payaccording').show();
                $('#start_amt').removeClass('col-md-12');
                $('#start_amt').addClass('col-md-6');
            }

        });

        $('#category_id').change(function (e) {

            let categoryId = $(this).val();

            var url = "{{ route('get_job_sub_categories', ':id') }}";
            url = url.replace(':id', categoryId);

            $.easyAjax({
                url: url,
                type: "GET",
                success: function (response) {
                    if (response.status == 'success') {
                        var options = [];
                        var rData = [];
                        rData = response.data;
                        $.each(rData, function (index, value) {
                            var selectData = '';
                            selectData = '<option value="' + value.id + '">' + value
                                .sub_category_name + '</option>';
                            options.push(selectData);
                        });

                        $('#sub_category_id').html('<option value="">--</option>' +
                            options);
                        $('#sub_category_id').selectpicker('refresh');
                    }
                }
            })

        });

        $('#save-job').click(function () {
            let jobDescription = document.getElementById('job_description').children[0].innerHTML;
            document.getElementById('description-text').value = jobDescription;

            const url = "{{ route('jobs.store') }}";
            var data = $('#save-job-data-form').serialize();

            saveJob(data, url, "#save-job");

        });

        $('#save-more-job-form').click(function () {
            let jobDescription = document.getElementById('job_description').children[0].innerHTML;
            document.getElementById('description-text').value = jobDescription;

            const url = "{{ route('jobs.store') }}";
            var data = $('#save-job-data-form').serialize() + '&add_more=true';

            saveJob(data, url, "#save-more-job-form");

        });

        function saveJob(data, url, buttonSelector) {
            $.easyAjax({
                url: url,
                container: '#save-job-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: buttonSelector,
                data: data,
                success: function (response) {
                    if (response.status === 'success') {
                        if (response.add_more == true) {
                            $(RIGHT_MODAL_CONTENT).html(response.html.html);
                        } else if ($(MODAL_XL).hasClass('show')) {
                            $(MODAL_XL).modal('hide');
                            window.location.reload();
                        } else {
                            window.location.href = response.redirectUrl;
                        }
                    }
                }
            });
        };

        $('body').off('click', ".job-type").on('click', '.job-type', function () {
            const url = "{{ route('job-type.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);

        });

        $('body').off('click', ".work-experience").on('click', '.work-experience', function () {
            const url = "{{ route('work-experience.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        $('body').off('click', ".skill-setting").on('click', '.skill-setting', function () {
            var selectedValue = $('#selectEmployeeData').val();
            var newVal = selectedValue.join(',');
            const url = "{{ route('job-skills.addSkill') }}?skill=" + newVal;
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        $('body').off('click', ".recruiter-add").on('click', '.recruiter-add', function () {
            const url = "{{ route('jobs.addRecruiter') }}";
            $(MODAL_DEFAULT + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_DEFAULT, url);
        });

        $('body').off('click', ".job-category-add").on('click', '.job-category-add', function () {
            const url = "{{ route('job-category.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        $('body').off('click', ".job-sub-category-add").on('click', '.job-sub-category-add', function () {
            const url = "{{ route('job-sub-category.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        $('body').off('click', ".interview-stage").on('click', '.interview-stage', function () {
            var selectedValue = $('#selectStages').val();
            var newVal = selectedValue.join(',');
            const url = "{{ route('interview-stages.create') }}?stage=" + newVal;
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        init(RIGHT_MODAL);
    });
</script>
