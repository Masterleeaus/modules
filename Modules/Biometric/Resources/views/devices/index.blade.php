@extends('layouts.app')
@push('styles')
    <style>
        .badge {
            display: inline;
        }
    </style>
@endpush
@section('filter-section')
    <x-filters.filter-box>
        <!-- SEARCH BY TASK START -->
        <div class="task-search d-flex py-1 pr-lg-3 px-0 border-right-grey align-items-center">
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
        <!-- SEARCH BY TASK END -->

        <!-- RESET START -->
        <div class="select-box d-flex py-1 px-lg-2 px-md-2 px-0">
            <x-forms.button-secondary class="btn-xs d-none" id="reset-filters" icon="times-circle">
                @lang('app.clearFilters')
            </x-forms.button-secondary>
        </div>
        <!-- RESET END -->
    </x-filters.filter-box>
@endsection

@section('content')

    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper">



        <!-- Add Task Export Buttons Start -->
        <div class="d-flex justify-content-between">

            <div id="table-actions" class="flex-grow-1 align-items-center">
                <x-forms.link-primary :link="route('biometric-devices.create')" class="mr-3 float-left openRightModal"
                    icon="plus">
                    @lang('biometric::app.addBiometricDevice')
                </x-forms.link-primary>
                <x-forms.button-secondary id="sync-all-employees" icon="sync" class="mr-3 float-left">
                    @lang('biometric::app.pushEmployeesToDevices')
                </x-forms.button-secondary>
            </div>
            <div>
                <x-forms.button-secondary id="refresh-table" icon="sync" class="mr-3 float-left">
                    @lang('biometric::app.refresh')
                </x-forms.button-secondary>
            </div>
        </div>

        <!-- Device Configuration Guide Start -->

        <!-- Alternative Design: Card with Timeline -->
        <div class="row mt-2 mb-3">
            <div class="col-md-12">
                <div class="card border-bottom border-grey shadow-sm">
                    <div class="card-header bg-white border-0  d-flex justify-content-between py-2 border-bottom border-grey" data-toggle="collapse" data-target="#deviceConfigGuide" style="cursor: pointer;">
                        <h6 class="mb-0">
                            <i class="fa fa-cogs mr-2"></i>@lang('biometric::app.deviceConfigurationGuide')
                            <span class="badge badge-light ml-2">@lang('biometric::app.clickToExpand')</span>
                        </h6>
                        <i class="fa fa-chevron-down transition-transform"></i>
                    </div>
                    <div class="card-body p-3 collapse" id="deviceConfigGuide">
                        <div class="alert alert-warning mb-3">
                            <i class="fa fa-exclamation-triangle mr-2"></i>
                            <strong>Important Note:</strong> This configuration guide is currently being tested with <strong>ZKTeco</strong> devices that support ADMS mode. We have specifically tested this with the <strong>ZKTeco K40 Pro</strong> model. This setup may not work with other biometric devices.
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="d-flex mb-3">
                                    <div class="mr-2">
                                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                            <i class="fa fa-check fa-sm"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="font-weight-bold">@lang('biometric::app.checkDeviceAdmsSupport')</h6>
                                        <p class="mb-0">@lang('biometric::app.zktecoAdmsInfo')</p>
                                    </div>
                                </div>

                                <div class="d-flex">
                                    <div class="mr-2">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                            <i class="fa fa-cog fa-sm"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="font-weight-bold">@lang('biometric::app.configureAdmsSettings')</h6>
                                        <p><strong>@lang('biometric::app.steps'):</strong> @lang('biometric::app.goToMenuCommunication')</p>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm ">
                                                <tbody>
                                                    <tr>
                                                        <td class="py-1">@lang('biometric::app.serverMode')</td>
                                                        <td class="py-1"><strong>ADMS</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="py-1">@lang('biometric::app.enableDomainName')</td>
                                                        <td class="py-1"><strong>@lang('biometric::app.on')</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="py-1">@lang('biometric::app.serverAddress')</td>
                                                        <td class="py-1"><strong>{{ preg_replace('/^https?:\/\//', '', config('app.url')) }}</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="py-1">@lang('biometric::app.enableProxyServer')</td>
                                                        <td class="py-1"><strong>@lang('biometric::app.off')</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="py-1">HTTPS</td>
                                                        <td class="py-1"><strong>@lang('biometric::app.on')</strong> <span class="text-muted">(@lang('biometric::app.dependsOnServer'))</span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="alert alert-info mt-2 py-2 ">
                                            <strong>@lang('biometric::app.note'):</strong> @lang('biometric::app.afterAddingDevice')
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-center justify-content-center">
                                @include('biometric::devices.create-url')
                                {{-- <a href="javascript:;" class="open-image-modal" data-image-url="https://public.froid.works/adms-zkteco.png">
                                    <img src="https://public.froid.works/adms-setting.png" alt="ADMS Settings" class="img-fluid border rounded shadow-sm" style="max-height: 300px;">
                                </a> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .transition-transform {
                transition: transform 0.3s ease;
            }
            [aria-expanded="true"] .fa-chevron-down {
                transform: rotate(180deg);
            }
        </style>

        <script>
            $(document).ready(function() {
                $('#deviceConfigGuide').on('show.bs.collapse', function () {
                    $(this).prev().find('.fa-chevron-down').css('transform', 'rotate(180deg)');
                }).on('hide.bs.collapse', function () {
                    $(this).prev().find('.fa-chevron-down').css('transform', 'rotate(0deg)');
                });
            });
        </script>
        <!-- Device Configuration Guide End -->

        <!-- Pending Device Alert Start -->
        <div class="row mt-3" id="pending-device-alert">
            <div class="col-md-12">
                <div class="alert alert-warning d-flex align-items-center pending-alert">
                    <div class="mr-3">
                        <div class="spinner-border text-warning" role="status">
                            <span class="sr-only">Waiting...</span>
                        </div>
                    </div>
                    <div>
                        <h6 class="mb-1"><i class="fa fa-clock mr-2"></i>@lang('biometric::app.waitingForDeviceConnection')</h6>
                        <p class="mb-0">@lang('biometric::app.performClockInOut')</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pending Device Alert End -->

        <!-- Connection Established Alert Start -->
        <div class="row mt-3" id="connection-established-alert" style="display: none;">
            <div class="col-md-12">
                <div class="alert alert-success d-flex align-items-center blink-alert" style="border-radius: 8px; background: linear-gradient(145deg, #dcffe4, #ffffff);">
                    <div class="mr-3">
                        <i class="fa fa-check-circle fa-2x text-success"></i>
                    </div>
                    <div>
                        <h6 class="mb-1"><i class="fa fa-plug mr-2"></i>@lang('biometric::app.connectionEstablished')</h6>
                        <p class="mb-0">@lang('biometric::app.deviceSuccessfullyConnected')</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Connection Established Alert End -->


        <!-- Task Box Start -->
        <div class="d-flex flex-column w-tables rounded mt-3 bg-white">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive" id="device-table-container">
                                <table class="table table-bordered table-hover" id="biometric-device-table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>@lang('biometric::app.deviceName')</th>
                                            <th>@lang('biometric::app.serialNumber')</th>
                                            <th>@lang('biometric::app.deviceIp')</th>
                                            <th class="text-center">@lang('biometric::app.lastOnline')</th>
                                            <th>@lang('biometric::app.status')</th>
                                            <th>@lang('biometric::app.actions')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($biometricDevice as $device)
                                            <tr data-device-status="{{ $device->status }}">
                                                <td>{{ $device->device_name }}</td>
                                                <td>{{ $device->serial_number }}</td>
                                                <td>{{ $device->device_ip }}</td>
                                                <td class="text-center">{!! $device->last_online ? $device->last_online->diffForHumans() . '<br>(' . $device->last_online->timezone(company()->timezone)->format('d F Y h:i A') . ')' : '--' !!}</td>
                                                <td>
                                                    @if($device->status == 'online')
                                                        <span class="badge badge-success">@lang('biometric::app.online')</span>
                                                    @elseif($device->status == 'offline')
                                                        <span class="badge badge-danger">@lang('biometric::app.offline')</span>
                                                    @else
                                                        <span class="badge badge-secondary">{{ $device->status }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <x-forms.button-secondary data-devices-id="{{ $device->id }}" icon="trash"
                                                        class="delete-table-row">
                                                        @lang('app.delete')
                                                    </x-forms.button-secondary>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">@lang('messages.noRecordFound')</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Task Box End -->
    </div>
    <!-- CONTENT WRAPPER END -->

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">@lang('biometric::app.admsSettings')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img src="" id="modalImage" class="img-fluid" alt="@lang('biometric::app.admsSettings')">
                </div>
            </div>
        </div>
    </div>

    <!-- Sync Employees Modal -->
    <div class="modal fade" id="syncEmployeesModal" tabindex="-1" role="dialog" aria-labelledby="syncEmployeesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="syncEmployeesModalLabel">
                        <i class="fa fa-users mr-2"></i>@lang('biometric::app.syncAllEmployees')
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="alert alert-info m-3" id="sync-info-alert">
                        <i class="fa fa-info-circle mr-1"></i>
                        @lang('biometric::app.followingEmployeesWillBeSynced')
                    </div>

                    <div class="alert alert-warning m-3" id="sync-warning-alert">
                        <i class="fa fa-exclamation-triangle mr-1"></i>
                        <strong>Warning:</strong> If any employee ID already exists on the device, it will be overwritten with this data.
                    </div>

                    <div class="table-responsive sync-employees-table-container" id="sync-employees-table-container">
                        <table class="table table-bordered mb-0" id="employees-to-sync-table">
                            <thead class="thead-light sticky-top">
                                <tr>
                                    <th style="width: 40px;">
                                        <input type="checkbox" id="select-all-employees">
                                    </th>
                                    <th>@lang('app.employee')</th>
                                    <th>@lang('biometric::app.employeeId')</th>
                                    <th>@lang('biometric::app.biometricUserId')</th>
                                    <th class="text-center">@lang('biometric::app.status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Will be populated via AJAX -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Success message, hidden by default -->
                    <div class="row mt-3" id="response-message-modal" style="display: none; opacity: 0; transition: opacity 0.5s;">
                        <div class="col-md-12">
                            <div class="alert alert-success d-flex align-items-center pending-alert m-3">
                                <div class="mr-3">
                                    <div class="text-success" role="status">
                                        <span class="sr-only">Processing...</span>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-1"><i class="fa fa-sync mr-2"></i> @lang('biometric::app.employeesPushedToDevices')</h6>
                                    <p class="mb-0">@lang('biometric::app.employeesSyncInitiated', ['pendingCommandsUrl' => route('biometric-devices.commands')])</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <x-forms.button-secondary data-dismiss="modal">
                        <i class="fa fa-times mr-1"></i>@lang('app.cancel')
                    </x-forms.button-secondary>
                    <x-forms.button-primary id="confirm-sync-employees">
                        <i class="fa fa-check mr-1"></i>@lang('biometric::app.confirmPushEmployeesToDevices')
                    </x-forms.button-primary>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        "use strict";  // Enforces strict mode for the entire script

        $(document).ready(function() {
            // Check if auto refresh is needed
            checkAutoRefresh();

            // Check if we have any pending devices and show/hide the alert
            updatePendingDeviceAlert();

            // Refresh button click handler
            $('#refresh-table').click(function() {
                refreshTable();
            });

            // Handle image modal
            $('.open-image-modal').click(function() {
                var imageUrl = $(this).data('image-url');
                $('#modalImage').attr('src', imageUrl);
                $('#imageModal').modal('show');
            });

            // Handle sync all employees
            $('#sync-all-employees').click(function() {
                // Reset modal state
                $('#sync-info-alert, #sync-warning-alert, #sync-employees-table-container').show();
                $('#confirm-sync-employees').prop('disabled', false);
                $('#syncEmployeesModal .modal-footer .btn-secondary').text('Cancel');

                // Show loading in modal
                $('#employees-to-sync-table tbody').html('<tr><td colspan="4" class="text-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></td></tr>');

                // Show the modal
                $('#syncEmployeesModal').modal('show');

                // Fetch employees list
                $.easyAjax({
                    url: "{{ route('biometric-employees.get-employees-to-sync') }}",
                    type: "GET",
                    success: function(response) {
                        let html = '';
                        if (response.data.length === 0) {
                            html = `<tr><td colspan="5" class="text-center">@lang('biometric::app.noEmployeesToSync')</td></tr>`;
                        } else {
                            response.data.forEach(function(employee) {
                                html += `
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="select-employee" value="${employee.id}">
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="${employee.image_url}" class="mr-2 rounded-circle" width="30" height="30">
                                                <div>
                                                    <h5 class="mb-0 f-13 font-weight-normal">${employee.name}</h5>
                                                    <p class="mb-0 f-11 text-muted">${employee.email}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>${employee.employee_id}</td>
                                        <td>${employee.biometric_id}</td>
                                        <td class="text-center">
                                            ${employee.is_configured ?
                                                '<span class="badge badge-success"><i class="fa fa-check-circle mr-1"></i>@lang("biometric::app.configured")</span>' :
                                                '<span class="badge badge-warning"><i class="fa fa-exclamation-circle mr-1"></i>@lang("biometric::app.notConfigured")</span>'
                                            }
                                        </td>
                                    </tr>
                                `;
                            });
                        }
                        $('#employees-to-sync-table tbody').html(html);
                        // Handle select all
                        $('#select-all-employees').prop('checked', false);
                        $('#select-all-employees').off('change').on('change', function() {
                            var checked = $(this).is(':checked');
                            $('.select-employee').prop('checked', checked);
                        });
                        // Uncheck select all if any checkbox is unchecked
                        $('#employees-to-sync-table').off('change', '.select-employee').on('change', '.select-employee', function() {
                            if (!$(this).is(':checked')) {
                                $('#select-all-employees').prop('checked', false);
                            } else if ($('.select-employee:checked').length === $('.select-employee').length) {
                                $('#select-all-employees').prop('checked', true);
                            }
                        });
                    }
                });
            });

            // Handle confirm sync
            $('#confirm-sync-employees').click(function() {
                var selectedIds = $('.select-employee:checked').map(function() {
                    return $(this).val();
                }).get();
                if (selectedIds.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No employees selected',
                        text: 'Please select at least one employee to push to the device.'
                    });
                    return;
                }
                Swal.fire({
                    title: "@lang('messages.sweetAlertTitle')",
                    text: "@lang('biometric::app.syncAllEmployeesConfirmation')",
                    icon: 'warning',
                    showCancelButton: true,
                    focusConfirm: false,
                    confirmButtonText: "@lang('app.yes')",
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
                            url: "{{ route('biometric-devices.sync-employees') }}",
                            type: "POST",
                            data: {
                                '_token': "{{ csrf_token() }}",
                                'employee_ids': selectedIds
                            },
                            blockUI: true,
                            messagePosition: 'inline',
                            success: function(response) {
                                if (response.status == "success") {
                                    // Hide table/alerts immediately, show success message after AJAX
                                    $('#sync-info-alert, #sync-warning-alert, #sync-employees-table-container').hide();
                                    $('#response-message-modal').hide().css('opacity', 0);
                                    $('#confirm-sync-employees').prop('disabled', true);
                                    $('#syncEmployeesModal .modal-footer .btn-secondary').text('Close');
                                    // Show success message with fade in
                                    $('#response-message-modal').css('opacity', 0).show().animate({opacity: 1}, 100);
                                }
                                else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: response.message
                                    });
                                }
                            },
                        });
                    }
                });
            });
        });

        // Function to check for pending devices and show/hide the alert
        function updatePendingDeviceAlert() {
            var hasPendingDevice = false;
            var hasOnlineDevice = false;
            var previousDeviceStatuses = {};

            // Store previous device statuses if they exist
            if (typeof window.previousDeviceStatuses === 'undefined') {
                window.previousDeviceStatuses = {};
                $("#biometric-device-table tbody tr").each(function() {
                    var deviceId = $(this).find('.delete-table-row').data('devices-id');
                    var status = $(this).data('device-status');
                    if (deviceId) {
                        window.previousDeviceStatuses[deviceId] = status;
                    }
                });
            }

            // Check current statuses
            $("#biometric-device-table tbody tr").each(function() {
                var deviceId = $(this).find('.delete-table-row').data('devices-id');
                var currentStatus = $(this).data('device-status');

                if (currentStatus === 'pending') {
                    hasPendingDevice = true;
                }

                if (currentStatus === 'online') {
                    hasOnlineDevice = true;

                    // Check if status changed from pending/offline to online
                    if (deviceId && window.previousDeviceStatuses[deviceId] &&
                        window.previousDeviceStatuses[deviceId] !== 'online') {
                        // Show connection established message
                        $('#connection-established-alert').fadeIn();

                        // Hide it after 2 min
                        setTimeout(function() {
                            $('#connection-established-alert').fadeOut();
                        }, 120000);
                    }
                }

                // Update the stored status
                if (deviceId) {
                    window.previousDeviceStatuses[deviceId] = currentStatus;
                }
            });

            if (hasPendingDevice) {
                $('#pending-device-alert').show();
            } else {
                $('#pending-device-alert').hide();
            }
        }

        // Function to check if we should auto-refresh based on device status
        function checkAutoRefresh() {
            var hasPendingDevice = false;

            $("#biometric-device-table tbody tr").each(function() {
                if ($(this).data('device-status') === 'pending') {
                    hasPendingDevice = true;
                    return false; // Break the loop
                }
            });

            if (hasPendingDevice) {
                // If pending, set a 5-second auto-refresh
                setTimeout(function() {
                    refreshTable();
                    updatePendingDeviceAlert();
                }, 5000);
            }
        }

        // Function to refresh the table via AJAX
        function refreshTable() {
            $.ajax({
                url: "{{ route('biometric-devices.index') }}",
                type: 'GET',
                dataType: 'html',
                beforeSend: function() {
                    // Add loading indicator
                    $('#device-table-container').addClass('blur');
                    $('#refresh-table i').addClass('fa-spin');
                },
                success: function(response) {
                    // Extract the table HTML from the response
                    var newTable = $(response).find('#device-table-container').html();
                    $('#device-table-container').html(newTable);

                    // Apply search filtering if search is active
                    if ($('#search-text-field').val() !== '') {
                        showTable();
                    }

                    // Check if we need to auto-refresh again
                    checkAutoRefresh();

                    // Update the pending device alert
                    updatePendingDeviceAlert();
                },
                complete: function() {
                    // Remove loading indicator
                    $('#device-table-container').removeClass('blur');
                    $('#refresh-table i').removeClass('fa-spin');
                }
            });
        }

        $('#biometric-device-table').on('preXhr.dt', function(e, settings, data) {
            var searchText = $('#search-text-field').val();
            data['searchText'] = searchText;
        });

        $('#search-text-field')
            .on('change keyup',
                function() {
                    if ($('#search-text-field').val() != "") {
                        $('#reset-filters').removeClass('d-none');
                        showTable();
                    } else {
                        $('#reset-filters').addClass('d-none');
                        showTable();
                    }
                });

        $('body').on('click', '#reset-filters', function () {
            $('#filter-form')[0].reset();

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

        function showTable() {
            var searchText = $('#search-text-field').val();

            // Use client-side filtering for simplicity
            $("#biometric-device-table tbody tr").each(function() {
                var rowText = $(this).text().toLowerCase();
                if (searchText === "" || rowText.indexOf(searchText.toLowerCase()) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });

            // Show "no results" message if all rows are hidden
            if ($("#biometric-device-table tbody tr:visible").length === 0 &&
                $("#no-results-row").length === 0 &&
                searchText !== "") {
                $("#biometric-device-table tbody").append('<tr id="no-results-row"><td colspan="6" class="text-center">@lang('biometric::app.noMatchingRecordsFound')</td></tr>');
            } else if (searchText === "" || $("#biometric-device-table tbody tr:visible").length > 0) {
                $("#no-results-row").remove();
            }
        }

        $('body').on('click', '.delete-table-row', function() {
            var id = $(this).data('devices-id');
            var row = $(this).closest('tr');

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
                    var url = "{{ route('biometric-devices.destroy', ':id') }}";
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
                        success: function(response) {
                            if (response.status == "success") {
                                row.fadeOut(300, function() {
                                    $(this).remove();
                                    // If no rows left, show "no records" message
                                    if ($('.table tbody tr').length === 0) {
                                        $('.table tbody').append('<tr><td colspan="6" class="text-center">@lang("messages.noRecordFound")</td></tr>');
                                    }
                                    // Update pending device alert
                                    updatePendingDeviceAlert();
                                });
                            }
                        }
                    });
                }
            });
        });
    </script>

    <style>
        .blur {
            opacity: 0.6;
            pointer-events: none;
        }

        @keyframes enhanced-alert {
            0% {
                opacity: 1;
                transform: scale(1);
                box-shadow: 0 0 0 rgba(40, 167, 69, 0);
            }
            50% {
                /* opacity: 0.85; */
                /* transform: scale(1.005); */
                box-shadow: 0 0 20px rgba(40, 167, 69, 0.3);
            }
            100% {
                opacity: 1;
                transform: scale(1);
                box-shadow: 0 0 0 rgba(40, 167, 69, 0);
            }
        }

        @keyframes pending-alert {
            0% {
                opacity: 1;
                transform: scale(1);
                box-shadow: 0 0 0 rgba(255, 193, 7, 0);
            }
            50% {
                /* opacity: 0.95; */
                /* transform: scale(1.001); */
                box-shadow: 0 0 20px rgba(255, 193, 7, 0.3);
            }
            100% {
                opacity: 1;
                transform: scale(1);
                box-shadow: 0 0 0 rgba(255, 193, 7, 0);
            }
        }

        .blink-alert {
            animation: enhanced-alert 1.5s ease-in-out infinite;
            transition: all 0.3s ease;
            border-left: 5px solid #28a745;
        }

        .pending-alert {
            animation: pending-alert 1.5s ease-in-out infinite;
            transition: all 0.3s ease;
            border-left: 5px solid #ffc107;
            border-radius: 8px;
            background: linear-gradient(145deg, #fff9e6, #ffffff);
        }

        .blink-alert i.fa-check-circle {
            animation: rotate-icon 1.5s ease-in-out infinite;
        }

        .pending-alert .spinner-border {
            animation: spin 1.5s linear infinite;
        }

        @keyframes rotate-icon {
            0% {
                transform: scale(1) rotate(0deg);
            }
            50% {
                transform: scale(1.05) rotate(3deg);
            }
            100% {
                transform: scale(1) rotate(0deg);
            }
        }

        @keyframes spin {
            0% {
                transform: scale(1) rotate(0deg);
            }
            50% {
                transform: scale(1.05) rotate(180deg);
            }
            100% {
                transform: scale(1) rotate(360deg);
            }
        }

        /* Sync Employees Modal Styles */
        .sync-employees-table-container {
            max-height: 60vh;
            overflow-y: auto;
        }

        .sync-employees-table-container thead.sticky-top {
            position: sticky;
            top: 0;
            z-index: 1;
            background: #f8f9fa;
        }

        .sync-employees-table-container thead.sticky-top th {
            border-top: none;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        #syncEmployeesModal .modal-body {
            display: flex;
            flex-direction: column;
        }

        #syncEmployeesModal .alert {
            margin-bottom: 0;
        }
    </style>
@endpush
