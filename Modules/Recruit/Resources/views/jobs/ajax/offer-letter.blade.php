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
                    <option value="pending"
                            data-content="<i class='fa fa-circle mr-2' style='color: yellow'></i> {{ __('recruit::app.job.pending') }}"></option>
                    <option value="draft"
                            data-content="<i class='fa fa-circle mr-2' style='color: brown'></i> {{ __('recruit::app.job.draft') }}"></option>
                    <option value="withdraw"
                            data-content="<i class='fa fa-circle mr-2' style='color: blue'></i> {{ __('recruit::app.job.withdraw') }}"></option>
                    <option value="accept"
                            data-content="<i class='fa fa-circle mr-2' style='color: green'></i> {{ __('recruit::modules.joboffer.accpet') }}"></option>
                    <option value="decline"
                            data-content="<i class='fa fa-circle mr-2' style='color: red'></i> {{ __('recruit::modules.joboffer.declined') }}"></option>

                </select>
            </div>
        </div>
        <!-- status end -->

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
                       for="usr">@lang('recruit::modules.job.job')</label>
                <div class="select-filter mb-4">
                    <div class="select-others">
                        <select class="form-control select-picker" name="job" data-container="body" id="job">
                            <option value="all">@lang('app.all')</option>
                            @foreach ($jobs as $job)
                                <option value="{{ $job->id }}">{{ ($job->title) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

        </x-filters.more-filter-box>
        <!-- MORE FILTERS END -->
    </x-filters.filter-box>

@endsection

@php
    $addPermission = user()->permission('add_offer_letter');
@endphp

@section('content')
    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper">
        <!-- Add Task Export Buttons Start -->
        <div class="d-flex justify-content-between action-bar">

            <div id="table-actions" class="d-block d-lg-flex align-items-center">
                @if ($addPermission == 'all' || $addPermission == 'added')
                    <x-forms.link-primary :link="route('job-offer-letter.create')" class="mr-3 openRightModal"
                                          icon="plus">
                        @lang('recruit::modules.joboffer.addjoboffer')
                    </x-forms.link-primary>
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
                            <option value="pending" data-content="{{ __('recruit::app.job.pending') }}"></option>
                            <option value="accept"
                                    data-content="{{ __('app.accept') }}">{{ __('app.accept') }}</option>
                            <option value="decline"
                                    data-content="{{ __('app.decline') }}">{{ __('app.decline') }}</option>
                            <option value="draft"
                                    data-content="{{ __('recruit::app.job.draft') }}">{{ __('recruit::app.job.draft') }}</option>
                            <option value="withdraw"
                                    data-content="{{ __('recruit::app.job.withdraw') }}">{{ __('recruit::app.job.withdraw') }}</option>
                        </select>
                    </div>
                </x-datatable.actions>
            </div>
        </div>
        <!-- Add Task Export Buttons End -->
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
        $('#offer-table').on('preXhr.dt', function (e, settings, data) {
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
            const job = $('#job').val();
            const date_filter_on = $('#date_filter_on').val();

            data['startDate'] = startDate;
            data['endDate'] = endDate;
            data['status'] = status;
            data['job'] = job;
            data['searchText'] = searchText;
            data['date_filter_on'] = date_filter_on;

        });

        const showTable = () => {
            window.LaravelDataTables["offer-table"].draw(true);
        }

        $('#search-text-field, #status, #location, #job')
            .on('change keyup', function () {
                if ($('#search-text-field').val() !== "") {
                    $('#reset-filters').removeClass('d-none');
                } else if ($('#status').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                } else if ($('#location').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                } else if ($('#job').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                } else {
                    $('#reset-filters').addClass('d-none');
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
            var rowdIds = $("#offer-table input:checkbox:checked").map(function () {
                return $(this).val();
            }).get();

            const url = "{{ route('job-offer-letter.apply_quick_action') }}?row_ids=" + rowdIds;

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
            var id = $(this).data('offer-id');
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
                    var url = "{{ route('job-offer-letter.destroy', ':id') }}";
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

        $('#offer-table').on('change', '.change-status', function () {

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
                    success: function (data) {
                        window.LaravelDataTables["offer-table"].draw(true);
                    }
                });

            }
        });

        $('body').on('click', '.send-offer-letter', function () {

            var url = "{{ route('job-offer-letter.send-offer-letter') }}";
            var token = "{{ csrf_token() }}";
            var id = $(this).data('send-id');

            $.easyAjax({
                url: url,
                type: "POST",
                container: '.content-wrapper',
                blockUI: true,
                data: {
                    '_token': token,
                    jobOfferId: id,
                },
                success: function (response) {
                    window.LaravelDataTables["offer-table"].draw(true);
                }
            });

        });

        $('body').on('click', '.create-employee', function () {
            var id = $(this).data('offer-id');
            var url = "{{ route('job-offer-letter.create_employee', ':id') }}";
            url = url.replace(':id', id);

            $(MODAL_XL + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_XL, url);
        });

        $('body').on('click', '.withdraw-offer-letter', function () {

            var url = "{{ route('job-offer-letter.withdraw-offer-letter') }}";
            var token = "{{ csrf_token() }}";
            var id = $(this).data('withdraw-id');

            $.easyAjax({
                url: url,
                type: "POST",
                container: '.content-wrapper',
                blockUI: true,
                data: {
                    '_token': token,
                    id: id,
                },
                success: function (response) {
                    window.LaravelDataTables["offer-table"].draw(true);
                }
            });

        });

        $('body').on('change', '.change-letter-status', function () {
            var id = $(this).data('letter-id');
            var url = "{{ route('job-offer-letter.change_letter_status') }}";

            var token = "{{ csrf_token() }}";
            var status = $(this).val();

            if (typeof id !== 'undefined') {
                $.easyAjax({
                    url: "{{ route('job-offer-letter.change_letter_status') }}",
                    type: "POST",
                    data: {
                        '_token': token,
                        letterId: id,
                        status: status
                    },

                    success: function (response) {
                        if (response.status == "success") {
                            showTable();
                            resetActionButtons();
                            deSelectAll();
                        }
                    }
                });
            }
        });
    </script>
@endpush
