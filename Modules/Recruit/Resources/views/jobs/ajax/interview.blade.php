@php
    $addInterviewPermission = user()->permission('add_interview_schedule');
@endphp

<!-- CONTENT WRAPPER START -->
<div class="row pb-5">
    <div class="col-lg-12 col-md-12 mb-4 mb-xl-0 mb-lg-4">
        <!-- Add Task Export Buttons Start -->
        <div class="d-block d-lg-flex d-md-flex justify-content-between action-bar dd">
            <div class="d-flex" id="table-actions">
                @if ($addInterviewPermission == 'all' || $addInterviewPermission == 'added')
                    <x-forms.link-primary :link="route('interview-schedule.create',['id' => $jobId])"
                                            class="mr-3 openRightModal"
                                            icon="plus" data-redirect-url="{{ url()->full() }}">
                        @lang('add') @lang('recruit::app.menu.interviewSchedule')
                    </x-forms.link-primary>
                @endif
            </div>
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
                        <option value="all">@lang('app.all')</option>
                        <option value="rejected">@lang('app.rejected')</option>
                        <option value="hired">@lang('recruit::app.menu.hired')</option>
                        <option value="pending">@lang('app.pending')</option>
                        <option value="canceled">@lang('app.canceled')</option>
                    </select>
                </div>
            </x-datatable.actions>
        </div>

        <!-- Add Task Export Buttons End -->
        <!-- Task Box Start -->
        <div class="d-flex flex-column w-tables rounded mt-3 bg-white">

            {!! $dataTable->table(['class' => 'table table-hover border-0 w-100']) !!}

        </div>
        <!-- Task Box End -->
    </div>
</div>
<!-- CONTENT WRAPPER END -->

@include('sections.datatable_js')

<script>
    $('#interview-schedule-table').on('preXhr.dt', function (e, settings, data) {
        data['job_id'] = '{{ $job->id }}';
    });

    const showTable = () => {
        window.LaravelDataTables["interview-schedule-table"].draw(true);
    }

    $('#search-text-field, #status, #location, #job')
        .on('change keyup', function () {
            if ($('#search-text-field').val() !== "") {
                $('#reset-filters').removeClass('d-none');
            } else if ($('#status').val() != "all") {
                $('#reset-filters').removeClass('d-none');
                showTable();
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
        if (actionValue !== '') {
            $('#quick-action-apply').removeAttr('disabled');

            if (actionValue === 'change-status') {
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
        var rowdIds = $("#interview-schedule-table input:checkbox:checked").map(function () {
            return $(this).val();
        }).get();

        const url = "{{ route('interview-schedule.apply_quick_action') }}?row_ids=" + rowdIds;

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
                }
            }
        })
    };
    $('body').on('click', '.delete-table-row', function () {
        var id = $(this).data('user-id');
        console.log(id);
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
                var url = "{{ route('interview-schedule.destroy', ':id') }}";
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

    $('body').on('click', '.reschedule-interview', function () {
        var id = $(this).data('user-id');
        const url = "{{ route('interview-schedule.reschedule') }}?id=" + id;
        $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_LG, url);
    });

    $('body').on('change', '.change-interview-status', function () {
        var id = $(this).data('interview-id');
        var url = "{{ route('interview-schedule.change_interview_status') }}";

        var token = "{{ csrf_token() }}";
        var status = $(this).val();

        if (typeof id !== 'undefined') {
            $.easyAjax({
                url: "{{ route('interview-schedule.change_interview_status') }}",
                type: "POST",
                data: {
                    '_token': token,
                    interviewId: id,
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

    $('body').on('click', '.employeeResponse', function () {
        var action = $(this).data('response-action');
        var responseId = $(this).data('response-id');
        var url = "{{ route('interview-schedule.employee_response') }}";
        var msg;

        Swal.fire({
            title: "@lang('messages.sweetAlertTitle')",
            icon: 'warning',
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: "@lang('messages.confirm')",
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
                $.easyAjax({
                    type: 'POST',
                    url: url,
                    blockUI: true,
                    data: {
                        'action': action,
                        'responseId': responseId,
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.status == 'success') {
                            showTable();
                            resetActionButtons();
                            deSelectAll();
                        }
                    }
                });
            }
        });

    });

</script>
