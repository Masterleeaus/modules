@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

@section('filter-section')

    <x-filters.filter-box>
        <!-- DATE START -->
        <div class="select-box d-flex pr-2 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.duration')</p>
            <div class="select-status d-flex">
                <input type="text" class="position-relative text-dark form-control border-0 p-2 text-left f-14 f-w-500 border-additional-grey"
                       id="datatableRange" placeholder="@lang('placeholders.dateRange')">
            </div>
        </div>
        <!-- DATE END -->

        <!-- status start -->
        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.status')</p>
            <div class="select-status">
                <select class="form-control select-picker" name="status" id="status" data-live-search="true"
                        data-size="8">
                    <option {{ request('status') == 'all' ? 'selected' : '' }} value="all">@lang('app.all')</option>
                    @foreach ($applicationStatus as $status)
                        <option value="{{$status->id}}"
                                data-content="<i class='fa fa-circle mr-2' style='color: {{$status->color}}'></i> {{ $status->status }}"></option>
                    @endforeach
                </select>
            </div>
        </div>
        <!-- status end -->

        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('recruit::modules.job.job')</p>
            <div class="select-status">
                <select class="form-control select-picker" name="job" id="job" data-live-search="true"
                    data-size="8">
                    <option value="all">@lang('app.all')</option>
                    @foreach ($jobs as $job)
                        <option
                            data-content=""
                            value="{{ $job->id }}">{{ $job->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- SEARCH BY APPLICATION START -->
        <div class="task-search d-flex  py-1 px-lg-3 px-0 border-right-grey align-items-center">
            <form class="w-100 mr-1 mr-lg-0 mr-md-1 ml-md-1 ml-0 ml-lg-0">
                <div class="input-group bg-grey rounded">
                    <div class="input-group-prepend">
                        <span class="input-group-text border-0 bg-additional-grey">
                            <i class="fa fa-search f-13 text-dark-grey"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control f-14 p-1 border-additional-grey" id="search-text-field"
                           placeholder="@lang('app.startTyping')">
                </div>
            </form>
        </div>
        <!-- SEARCH BY APPLICATION END -->

        <!-- RESET START -->
        <div class="select-box d-flex py-1 px-lg-2 px-md-2 px-0">
            <x-forms.button-secondary class="btn-xs d-none" id="reset-filters" icon="times-circle">
                @lang('app.clearFilters')
            </x-forms.button-secondary>
        </div>
        <!-- RESET END -->

        <!-- MORE FILTERS START -->
        <x-filters.more-filter-box>

            <div class="more-filter-items">
                <label class="f-14 text-dark-grey mb-12 "
                       for="usr">@lang('recruit::modules.jobApplication.location')</label>
                <div class="select-filter mb-4">
                    <div class="select-others">
                        <select class="form-control select-picker" name="location" data-live-search="true"
                                data-container="body" id="location">
                            <option value="all">@lang('app.all')</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}">{{ ($location->location) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="more-filter-items">
                <label class="f-14 text-dark-grey mb-12 "
                       for="usr">@lang('recruit::modules.jobApplication.gender')</label>
                <div class="select-filter mb-4">
                    <div class="select-others">
                        <select class="form-control select-picker" name="gender" data-container="body" id="gender">
                            <option value="all">@lang('app.all')</option>
                            <option value="male">@lang('app.male')</option>
                            <option value="female">@lang('app.female')</option>
                            <option value="others">@lang('app.others')</option>

                        </select>
                    </div>
                </div>
            </div>

            <div class="more-filter-items">
                <label class="f-14 text-dark-grey mb-12 "
                       for="usr">@lang('recruit::modules.jobApplication.experience')</label>
                <div class="select-filter mb-4">
                    <div class="select-others">
                        <select class="form-control select-picker" id="total_experience" data-live-search="true"
                                data-container="body" data-size="8">
                            <option value="all">@lang('app.all')</option>
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
                </div>
            </div>
            <div class="more-filter-items">
                <label class="f-14 text-dark-grey mb-12 "
                       for="usr">@lang('recruit::modules.jobApplication.currentLocation')</label>
                <div class="select-filter">
                    <div class="select-others">
                        <select class="form-control select-picker" id="current_location" data-live-search="true"
                                data-container="body" data-size="8">
                            <option value="all">@lang('app.all')</option>
                            @if (count($currentLocations) > 0)
                                @foreach ($currentLocations as $currentLocation)
                                    <option
                                        value="{{ $currentLocation->current_location }}">{{ ($currentLocation->current_location) }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            </div>

            <div class="more-filter-items">
                <x-forms.label class="my-3" fieldId="current_ctc_min"
                               :fieldLabel="__('recruit::modules.jobApplication.currentCtc')"></x-forms.label>
                <div class="row">
                    <div class="col-md-5 ml-4">
                        <x-forms.input-group>
                            <input type="number" min="0" class="form-control height-35 f-14"
                                   name="current_ctc_min" id="current_ctc_min"
                                   placeholder="@lang('recruit::modules.jobApplication.minimum')">
                        </x-forms.input-group>
                    </div>

                    <div class="col-md-5">
                        <x-forms.input-group>
                            <input type="number" min="0" class="form-control height-35 f-14"
                                   name="current_ctc_max" id="current_ctc_max"
                                   placeholder="@lang('recruit::modules.jobApplication.maximum')">
                        </x-forms.input-group>
                    </div>
                </div>
            </div>

            <div class="more-filter-items">
                <x-forms.label class="my-3" fieldId="expected_ctc_min"
                               :fieldLabel="__('recruit::modules.jobApplication.expectedCtc')"></x-forms.label>
                <div class="row">
                    <div class="col-md-5 ml-4">
                        <x-forms.input-group>
                            <input type="number" min="0" class="form-control height-35 f-14"
                                   name="expected_ctc_min" id="expected_ctc_min"
                                   placeholder="@lang('recruit::modules.jobApplication.minimum')">
                        </x-forms.input-group>
                    </div>

                    <div class="col-md-5">
                        <x-forms.input-group>
                            <input type="number" min="0" class="form-control height-35 f-14"
                                   name="expected_ctc_max" id="expected_ctc_max"
                                   placeholder="@lang('recruit::modules.jobApplication.maximum')">
                        </x-forms.input-group>
                    </div>
                </div>
            </div>
        </x-filters.more-filter-box>
        <!-- MORE FILTERS END -->
    </x-filters.filter-box>

@endsection

@php
    $addJobApplicationPermission = user()->permission('add_job_application');
@endphp

@section('content')
    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper">
        <!-- Add Task Export Buttons Start -->
        <div class="d-block d-lg-flex d-md-flex justify-content-between action-bar dd">

            <div id="table-actions" class="flex-grow-1 align-items-center">
                @if ($addJobApplicationPermission == 'all' || $addJobApplicationPermission == 'added')
                    <x-forms.link-primary :link="route('job-applications.create')" class="mr-3 openRightModal float-left mb-2 mb-lg-0 mb-md-0"
                                          icon="plus">
                        @lang('recruit::modules.jobApplication.addJobApplications')
                    </x-forms.link-primary>
                @endif

                <x-forms.button-secondary class="btn-secondary rounded f-14 p-2 mr-3 float-left mb-2 mb-lg-0 mb-md-0 quick-add" icon="plus">
                    @lang('recruit::modules.jobApplication.quickAdd')
                </x-forms.button-secondary>

                @if ($addJobApplicationPermission == 'all' || $addJobApplicationPermission == 'added')
                <x-forms.link-secondary :link="route('job-applications.import')" class="mr-3 float-left mb-2 mb-lg-0 mb-md-0 d-none d-lg-block" icon="file-upload">
                    @lang('app.importExcel')
                </x-forms.link-secondary>
                @endif
            </div>

            <div class="d-flex">
                <x-datatable.actions>
                    <div class="select-status mr-3 pl-3">
                        <select name="action_type" class="form-control select-picker" id="quick-action-type" disabled>
                            <option value="">@lang('app.selectAction')</option>
                            <option value="change-status">@lang('modules.tasks.changeStatus')</option>
                            <option value="delete">@lang('app.delete')</option>
                        </select>
                    </div>
                    <div class="select-status mr-3 d-none quick-action-field" id="change-status-action">
                        <select name="status" class="form-control select-picker">
                            @foreach ($applicationStatus as $status)
                                <option
                                    value="{{ $status->id }}">{{ $status->slug == ('app.' . 'applied') || $status->slug == ('app.' . 'hired') ? __('app.' . $status->slug) : $status->status }}</option>
                            @endforeach
                        </select>
                    </div>
                </x-datatable.actions>

                <div class="btn-group mt-3 mt-lg-0 mt-md-0 ml-lg-3" role="group">
                    <a href="{{ route('job-applications.index') }}" class="btn btn-secondary f-14 btn-active"
                       data-toggle="tooltip"
                       data-original-title="@lang('recruit::app.menu.tableView')"><i
                            class="side-icon bi bi-list-ul"></i></a>

                    <a href="{{ route('job-appboard.index') }}" class="btn btn-secondary f-14" data-toggle="tooltip"
                        data-original-title="@lang('recruit::app.menu.boardView')"><i class="side-icon bi bi-kanban"></i></a>

                </div>
            </div>
        </div>
        <div class="mt-3" id="quick-add-form">
           @include('recruit::job-applications.ajax.quick_add_form')
        </div>

        <!-- Task Box Start -->
        <div class="d-flex flex-column w-tables rounded mt-3 bg-white">

            {!! $dataTable->table(['class' => 'table table-hover border-0 w-100']) !!}

        </div>
        <!-- Task Box End -->
    </div>
    <!-- CONTENT WRAPPER END -->

@endsection

@push('scripts')
    @include('sections.datatable_js')

    <script>

        $('body').on('click', '#save-application', function() {
            const url = "{{ route('job-applications.quick_add_form_store') }}";

            $.easyAjax({
                url: url,
                container: '#save-application-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                file: true,
                buttonSelector: "#save-application",
                data: $('#save-application-data-form').serialize(),
                success: function(response) {
                    $('#save-application-data-form')[0].reset();
                    $('#save-application-data-form .select-picker').selectpicker("refresh");
                    showTable();
                }
            });
        });

        $('#quick-add-form').hide();

        $('body').on('click', '.quick-add', function() {
            $('#quick-add-form').toggle();
            // $('body').addClass('sidebar-toggled');
        });

        $('#job-applications-table').on('preXhr.dt', function(e, settings, data) {
            const dateRangePicker = $('#datatableRange').data('daterangepicker');
            let startDate = $('#datatableRange').val();

            let endDate;

            if (startDate == '') {
                startDate = null;
                endDate = null;
            } else {
                startDate = dateRangePicker.startDate.format('{{ $company->moment_format }}');
                endDate = dateRangePicker.endDate.format('{{ $company->moment_format }}');
            }

            const searchText = $('#search-text-field').val();
            const status = $('#status').val();
            const location = $('#location').val();
            const job = $('#job').val();
            const gender = $('#gender').val();
            const total_experience = $('#total_experience').val();
            const current_location = $('#current_location').val();
            const current_ctc_min = $('#current_ctc_min').val();
            const current_ctc_max = $('#current_ctc_max').val();
            const expected_ctc_min = $('#expected_ctc_min').val();
            const expected_ctc_max = $('#expected_ctc_max').val();
            const date_filter_on = $('#date_filter_on').val();

            data['startDate'] = startDate;
            data['endDate'] = endDate;
            data['status'] = status;
            data['location'] = location;
            data['job'] = job;
            data['gender'] = gender;
            data['total_experience'] = total_experience;
            data['current_location'] = current_location;
            data['current_ctc_min'] = current_ctc_min;
            data['current_ctc_max'] = current_ctc_max;
            data['expected_ctc_min'] = expected_ctc_min;
            data['expected_ctc_max'] = expected_ctc_max;
            data['searchText'] = searchText;
            data['date_filter_on'] = date_filter_on;

        });

        const showTable = () => {
            window.LaravelDataTables["job-applications-table"].draw(true);
        }

        $('#search-text-field, #status, #location, #job, #gender, #total_experience, #current_location, #current_ctc_min, #current_ctc_max, #expected_ctc_min, #expected_ctc_max')
            .on('change keyup', function () {
                if ($('#search-text-field').val() !== "") {
                    $('#reset-filters').removeClass('d-none');
                } else if ($('#status').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                } else if ($('#location').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                } else if ($('#job').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                } else if ($('#gender').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                } else if ($('#total_experience').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                } else if ($('#current_location').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                } else if ($('#current_ctc_min').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                } else if ($('#current_ctc_max').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                } else if ($('#expected_ctc_min').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                } else if ($('#expected_ctc_max').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                }
                showTable();
            });

        $('body').on('click', '#reset-filters', function () {
            $('#filter-form')[0].reset();
            $('.filter-box #status').val('not finished');
            $('.filter-box .select-picker').selectpicker("refresh");
            $('#reset-filters').addClass('d-none');
            showTable();
        });

        $('body').on('click', '#reset-filters-2', function () {
            $('#filter-form')[0].reset();
            $('.filter-box .select-picker').selectpicker("refresh");
            $('#reset-filters').addClass('d-none');
            showTable();
        });

        $('#quick-action-type').change(function () {
            const actionValue = $(this).val();
            if (actionValue != '') {
                $('#quick-action-apply').removeAttr('disabled');

                if (actionValue == 'change-status') {
                    $('.quick-action-field').addClass('d-none');
                    $('#change-status-action').removeClass('d-none');
                } else {
                    $('.quick-action-field').addClass('d-none');
                }
            } else {
                $('#quick-action-apply').attr('disabled', true);
                $('.quick-action-field').addClass('d-none');
            }
        });

        $('body').on('click', '#quick-action-apply', function () {

            const actionValue = $('#quick-action-type').val();
            if (actionValue == 'delete') {
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
                        applyQuickAction();
                    }
                });

            } else {
                applyQuickAction();
            }
        });
        const applyQuickAction = () => {
            var rowdIds = $("#job-applications-table input:checkbox:checked").map(function () {
                return $(this).val();
            }).get();

            const url = "{{ route('job-applications.apply_quick_action') }}?row_ids=" + rowdIds;

            $.easyAjax({
                url: url,
                container: '#quick-action-form',
                type: "POST",
                disableButton: true,
                buttonSelector: "#quick-action-apply",
                data: $('#quick-action-form').serialize(),
                success: function (response) {
                    if (response.status == 'success') {
                        showTable();
                        resetActionButtons();
                        deSelectAll();
                        $('#quick-action-form').hide();
                    }
                }
            })
        };
        $('body').on('click', '.delete-table-row', function () {
            var id = $(this).data('application-id');
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
                    var url = "{{ route('job-applications.destroy', ':id') }}";
                    url = url.replace(':id', id);
                    var token = "{{ csrf_token() }}";
                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        blockUI: true,
                        data: {
                            '_token': token,
                            '_method': 'DELETE'
                        },
                        success: function (response) {
                            if (response.status == "success") {
                                showTable();
                            }
                        }
                    });
                }
            });
        });

        $('body').on('change', '.change-status', function () {
            var url = "{{ route('job-applications.change_status') }}";
            var token = "{{ csrf_token() }}";
            var id = $(this).data('status-id');
            var status = $(this).val();

            if (id != "" && status != "") {
                $.easyAjax({
                    url: url,
                    type: "POST",
                    container: '.content-wrapper',
                    blockUI: true,
                    data: {
                        '_token': token,
                        row_ids: id,
                        status: status,
                        sortBy: 'id'
                    },
                    success: function (response) {
                        let app_id = id;
                        let board = 0;
                        if (app_id && response.status.action == 'yes') {
                            if (response.status.category.name == 'shortlist') {
                                var url = "{{ route('job-appboard.application_remark',[':id', ':board']) }}";
                                url = url.replace(':id', app_id);
                                url = url.replace(':board', board);

                                $(MODAL_DEFAULT + ' ' + MODAL_HEADING).html('...');
                                $.ajaxModal(MODAL_DEFAULT, url);
                            }
                            if (response.status.category.name == 'interview' && response.interviewPermission == 'all') {
                                var url = "{{ route('job-appboard.interview', [':id', ':board']) }}";
                                url = url.replace(':id', app_id);
                                url = url.replace(':board', board);
                                $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
                                $.ajaxModal(MODAL_LG, url);
                            }
                            if (response.status.category.name == 'hired' && response.offerLetterPermission == 'all') {
                                var url = "{{ route('job-appboard.offer_letter', [':id', ':board']) }}";
                                url = url.replace(':id', app_id);
                                url = url.replace(':board', board);
                                $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
                                $.ajaxModal(MODAL_LG, url);
                            }
                            if (response.status.category.name == 'rejected') {
                                var url = "{{ route('job-appboard.rejected_remark', [':id', ':board']) }}";
                                url = url.replace(':id', app_id);
                                url = url.replace(':board', board);
                                $(MODAL_DEFAULT + ' ' + MODAL_HEADING).html('...');
                                $.ajaxModal(MODAL_DEFAULT, url);
                            }
                        }
                    }
                });

            }
        });

        $('body').on('click', '.archive-job', function () {
            Swal.fire({
                title: "@lang('messages.sweetAlertTitle')",
                text: "@lang('recruit::messages.archiveMessage')",
                icon: 'warning',
                showCancelButton: true,
                focusConfirm: false,
                confirmButtonText: "@lang('recruit::messages.confirmArchive')",
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
                    var url = "{{ route('candidate-database.store') }}";
                    var token = "{{ csrf_token() }}";
                    var rowId = $(this).data('application-id');

                    $.easyAjax({
                        url: url,
                        type: "POST",
                        data: {
                            '_token': token,
                            row_id: rowId
                        },
                        success: function (response) {
                            if (response.status == 'success') {
                                window.location.reload();
                            }
                        }
                    });
                }
            });
        });

        $('body').off('click', ".follow-up").on('click', '.follow-up', function () {
            let applicationId = $(this).data('application-id');
            let datatable = $(this).data('datatable');
            let searchQuery = "?id=" + applicationId + "&datatable=" + datatable;
            let url = "{{ route('candidate-follow-up.create') }}" + searchQuery;

            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
                $.ajaxModal(MODAL_LG, url);
        });
    </script>

    <script>
        function openApplicationFilter() {
            var omf = document.getElementById("application_filter");
            omf.classList.add("in");
        }

        function closeApplicationFilter() {
            var cls = document.getElementById("application_filter");
            cls.classList.remove("in");
        }

        if ($('#application_filter').length > 0) {
            $(document).on('mouseup', function (e) {
                var container = $("#application_filter");
                var searchField = $(".bs-searchbox");

                // if the target of the click isn't the container nor a descendant of the container
                if (container.is(e.target) && container.has(e.target).length === 0 && !searchField.is(e.target) && searchField.has(e.target).length === 0) {
                    closeApplicationFilter()
                }
            });
        }
    </script>
@endpush
