
@php
    $addJobApplicationPermission = user()->permission('add_job_application');
@endphp

<!-- ROW START -->
<div class="row pb-5">
    <div class="col-lg-12 col-md-12 mb-4 mb-xl-0 mb-lg-4">
        <!-- Add Task Export Buttons Start -->
         <div class="d-block d-lg-flex d-md-flex justify-content-between action-bar dd">
            <div class="d-flex justify-content-between" id="table-actions">
                @if ($addJobApplicationPermission == 'all' || $addJobApplicationPermission == 'added')
                    <x-forms.link-primary :link="route('job-applications.create',['id' => $jobId])"
                                            class="mr-3 openRightModal" icon="plus"
                                            data-redirect-url="{{ url()->full() }}">
                        @lang('recruit::modules.jobApplication.addJobApplications')
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
                        @foreach ($applicationStatus as $status)
                        <option value="{{ $status->id }}">{{ $status->slug == ('app.' . 'applied') || $status->slug == ('app.' . 'hired') ? __('app.' . $status->slug) : $status->status }}</option>
                        @endforeach
                    </select>
                </div>
            </x-datatable.actions>
         </div>
        <!-- Task Box Start -->
        <div class="d-flex flex-column w-tables rounded mt-3 bg-white">

            {!! $dataTable->table(['class' => 'table table-hover border-0 w-100']) !!}

        </div>
        <!-- Task Box End -->
    </div>
</div>
<!-- ROW END -->

@include('sections.datatable_js')

<script>
    const showTable = () => {
        window.LaravelDataTables["job-applications-table"].draw(true);
    }

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

    $('#job-applications-table').on('change', '.change-status', function () {
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
                        if (app_id && response.status.action == 'yes') {
                            if (response.status.category.name == 'shortlist') {
                                var url = "{{ route('job-appboard.application_remark', ':id') }}";
                                url = url.replace(':id', app_id);

                                $(MODAL_DEFAULT + ' ' + MODAL_HEADING).html('...');
                                $.ajaxModal(MODAL_DEFAULT, url);
                            }
                            if (response.status.category.name == 'interview' && response.interviewPermission == 'all') {
                                var url = "{{ route('job-appboard.interview',':id') }}";
                                url = url.replace(':id', app_id);

                                $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
                                $.ajaxModal(MODAL_LG, url);
                            }
                            if (response.status.category.name == 'hired' && response.offerLetterPermission == 'all') {
                                var url = "{{ route('job-appboard.offer_letter', ':id') }}";
                                url = url.replace(':id', app_id);

                                $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
                                $.ajaxModal(MODAL_LG, url);
                            }
                            if (response.status.category.name == 'rejected') {
                                var url = "{{ route('job-appboard.rejected_remark', ':id') }}";
                                url = url.replace(':id', app_id);

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
