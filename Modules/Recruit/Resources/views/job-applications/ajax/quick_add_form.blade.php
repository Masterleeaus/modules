
<style>
    #save-application-data-form .input-width {
        width: 75px !important;
    }

    #save-application-data-form .table td {
        padding: 6px 2px;
    }

    #save-application-data-form  .input-group .bootstrap-select.form-control .dropdown-toggle,
    #save-application-data-form .bootstrap-select > .dropdown-toggle {
        font-size: 12px;
    }
</style>
<!-- CONTENT WRAPPER START -->
    <div class="d-flex flex-column w-tables rounded mt-3 bg-white">
        <h4 class="mb-0 p-20 f-21 font-weight-normal  border-bottom-grey d-flex justify-content-between">
            @lang('recruit::modules.jobApplication.quickAdd')

            <button class="btn quick-add btn-sm f-15 p-0" type="button"><i class="fa fa-times"></i></button>
        </h4>
        <x-form id="save-application-data-form" method="POST" class="ajax-form">
            <div class="table-responsive">
                <x-table>
                    <x-slot name="thead">
                        <th class="pl-20">@lang('recruit::modules.jobApplication.jobs')</th>
                        <th>@lang('recruit::modules.job.location')</th>
                        <th>@lang('recruit::modules.jobApplication.name')</th>

                        @if (in_array('email', $formFields))
                            <th>@lang('recruit::modules.jobApplication.email')</th>
                        @endif

                        @if (in_array('phone', $formFields))
                            <th>@lang('app.phone')</th>
                        @endif

                        @if (in_array('gender', $formFields))
                            <th>@lang('recruit::modules.jobApplication.gender')</th>
                        @endif

                        @if (in_array('total_experience', $formFields))
                            <th>@lang('recruit::modules.jobApplication.experience')</th>
                        @endif

                        @if (in_array('current_location', $formFields))
                            <th>@lang('recruit::modules.jobApplication.currentLocation')</th>
                        @endif

                        @if (in_array('current_ctc', $formFields))
                            <th>@lang('recruit::modules.jobApplication.currentCtc')</th>
                        @endif

                        @if (in_array('expected_ctc', $formFields))
                            <th>@lang('recruit::modules.jobApplication.expectedCtc')</th>
                        @endif

                        @if (in_array('notice_period', $formFields))
                            <th>@lang('recruit::modules.jobApplication.noticePeriod')</th>
                        @endif

                        @if (in_array('status', $formFields))
                            <th>@lang('recruit::modules.jobApplication.status')</th>
                        @endif

                        @if (in_array('application_source', $formFields))
                            <th>@lang('recruit::modules.front.applicationSource')</th>
                        @endif

                        @if (in_array('cover_letter', $formFields))
                            <th>@lang('recruit::modules.jobApplication.coverLetter')</th>
                        @endif

                        <th class="text-right pr-20">@lang('app.action')</th>
                    </x-slot>
                        <tr>
                            <td scope="row" class="pl-20">
                                <div class="select-others">
                                    <select class="form-control select-picker" name="job_id" id="job-id" data-live-search="true" data-container="body">
                                        <option value="">--</option>
                                        @foreach ($jobs as $job)
                                            <option value="{{ $job->id }}">{{ ($job->title) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>

                            <td>
                                <div class="select-others">
                                    <select class="form-control select-picker" name="location_id" id="locationid" data-live-search="false" data-container="body">
                                            <option value="">--</option>
                                            @foreach ($jobLocations as $locationData)
                                                <option value="{{ $locationData->location->id }}">{{ ucwords($locationData->location->location) }}</option>
                                            @endforeach
                                    </select>
                                </div>
                            </td>

                            <td>
                                <x-forms.input-group>
                                    <input type="text" min="0" class="form-control height-35 f-12 input-width"
                                        name="full_name" id="name"
                                        placeholder="@lang('placeholders.name')">
                                </x-forms.input-group>
                            </td>

                            @if (in_array('email', $formFields))
                                <td>
                                    <x-forms.input-group>
                                        <input type="text" min="0" class="form-control height-35 f-12 input-width"
                                            name="email"
                                            placeholder="@lang('placeholders.email')">
                                    </x-forms.input-group>
                                </td>
                            @endif

                            @if (in_array('phone', $formFields))
                                <td>
                                    <x-forms.input-group>
                                    <input type="text" min="0" class="form-control height-35 f-12 input-width"
                                        name="phone"
                                        placeholder="@lang('placeholders.mobile')">
                                    </x-forms.input-group>
                                </td>
                            @endif

                            @if (in_array('gender', $formFields))
                                <td>
                                    <div class="select-others">
                                        <select class="form-control select-picker" name="gender" id="gender-1" data-live-search="true" data-container="body">
                                            <option value="">--</option>
                                            <option value="male">@lang('app.male')</option>
                                            <option value="female">@lang('app.female')</option>
                                            <option value="others">@lang('app.others')</option>
                                        </select>
                                    </div>
                                </td>
                            @endif

                            @if (in_array('total_experience', $formFields))
                                <td>
                                    <div class="select-others">
                                        <select class="form-control select-picker" name="total_experience" data-live-search="false" data-container="body">
                                            <option value="fresher">@lang('recruit::modules.jobApplication.fresher')</option>
                                            <option value="1-2">1-2 @lang('recruit::modules.jobApplication.years')</option>
                                            <option value="3-4">3-4 @lang('recruit::modules.jobApplication.years')</option>
                                            <option value="5-6">5-6 @lang('recruit::modules.jobApplication.years')</option>
                                            <option value="7-8">7-8 @lang('recruit::modules.jobApplication.years')</option>
                                            <option value="9-10">9-10 @lang('recruit::modules.jobApplication.years')</option>
                                            <option value="11-12">11-12 @lang('recruit::modules.jobApplication.years')</option>
                                            <option value="13-14">13-14 @lang('recruit::modules.jobApplication.years')</option>
                                            <option value="over-15">@lang('recruit::modules.jobApplication.over15')</option>
                                        </select>
                                    </div>
                                </td>
                            @endif

                            @if (in_array('current_location', $formFields))
                                <td>
                                <x-forms.input-group>
                                        <input type="text" min="0" class="form-control height-35 f-12 input-width"
                                            name="current_location" placeholder="@lang('recruit::modules.jobApplication.currentLocationPlaceholder')">
                                    </x-forms.input-group>
                                </td>
                            @endif

                            @if (in_array('current_ctc', $formFields))
                                <td>
                                    <x-forms.input-group>
                                        <input type="number" min="0" class="form-control height-35 f-12 input-width"
                                            name="current_ctc" placeholder="@lang('recruit::modules.jobApplication.currentCtcPlaceHolder')">
                                    </x-forms.input-group>
                                </td>
                            @endif

                            @if (in_array('expected_ctc', $formFields))
                                <td>
                                    <x-forms.input-group>
                                        <input type="number" min="0" class="form-control height-35 f-12 input-width"
                                            name="expected_ctc" placeholder="@lang('recruit::modules.jobApplication.expectedCtcPlaceHolder')">
                                    </x-forms.input-group>
                                </td>
                            @endif

                            @if (in_array('notice_period', $formFields))
                                <td>
                                    <div class="select-others">
                                        <select class="form-control select-picker" name="notice_period" data-live-search="false" data-container="body">
                                            <option value="">--</option>
                                            <option value="15">15 @lang('recruit::modules.jobApplication.days')</option>
                                            <option value="30">30 @lang('recruit::modules.jobApplication.days')</option>
                                            <option value="45">45 @lang('recruit::modules.jobApplication.days')</option>
                                            <option value="60">60 @lang('recruit::modules.jobApplication.days')</option>
                                            <option value="75">75 @lang('recruit::modules.jobApplication.days')</option>
                                            <option value="90">90 @lang('recruit::modules.jobApplication.days')</option>
                                            <option value="over-90">@lang('recruit::modules.jobApplication.over90')</option>
                                        </select>
                                    </div>
                                </td>
                            @endif

                            @if (in_array('status', $formFields))
                                <td>
                                    <div class="select-others">
                                        <select class="form-control select-picker" id="status_id" name="status_id" data-live-search="false" data-container="body">
                                            @foreach ($applicationStatus as $status)
                                                <option value="{{$status->id}}">{{ ($status->status) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                            @endif

                            @if (in_array('application_source', $formFields))
                                <td>
                                    <div class="select-others">
                                        <select class="form-control select-picker" name="source" data-live-search="false" data-container="body">
                                        <option value="">--</option>
                                            @foreach ($applicationSources as $source)
                                                <option value="{{$source->id}}"> {{ ($source->application_source) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                            @endif

                            @if (in_array('cover_letter', $formFields))
                                <td>
                                    <textarea class="form-control f-14 pt-2 input-width" rows="2" placeholder="{{__('recruit::modules.jobApplication.coverLetter') }}" name="cover_letter"></textarea>
                                </td>
                            @endif

                            <td  class="text-right pr-20"><button type="submit" id="save-application" class="btn-primary rounded f-14 p-2">@lang('app.save')</button></td>
                        </tr>
                </x-table>
            </div>
        </x-form>
    </div>
<!-- CONTENT WRAPPER END -->

<script>

    $('#job-id').change(function() {
        const jobId = $(this).val();
        const url = "{{ route('job-applications.get_location') }}";

        $.easyAjax({
            url: url,
            type: "GET",
            disableButton: true,
            blockUI: true,
            data: {
                job_id:jobId
            },
            success: function(response) {
                $('#locationid').html(response.locations);
                $('#locationid').selectpicker('refresh');
            }

        });
    });

</script>
