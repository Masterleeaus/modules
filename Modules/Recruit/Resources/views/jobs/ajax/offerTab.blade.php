@php
    $addPermission = user()->permission('add_offer_letter');
@endphp

<!-- CONTENT WRAPPER START -->
<div class="row pb-5">
    <div class="col-lg-12 col-md-12 mb-4 mb-xl-0 mb-lg-4">
        <!-- Add Task Export Buttons Start -->
         <div class="d-block d-lg-flex d-md-flex justify-content-between action-bar dd">
            <div class="d-flex w-100 justify-content-between" id="table-actions">
                @if ($addPermission == 'all' || $addPermission == 'added')
                    <x-forms.link-primary :link="route('job-offer-letter.create',['id' => $jobId])"
                                            class="mr-3 openRightModal" icon="plus"
                                            data-redirect-url="{{ url()->full() }}">
                        @lang('recruit::modules.joboffer.addjoboffer')
                    </x-forms.link-primary>
                @endif
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
</div>
<!-- CONTENT WRAPPER END -->

@include('sections.datatable_js')

<script>
    $('#offer-table').on('preXhr.dt', function (e, settings, data) {
        data['job_id'] = '{{ $jobId }}';
    });

    const showTable = () => {
        window.LaravelDataTables["offer-table"].draw(true);
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
