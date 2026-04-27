@extends('layouts.app')

@push('styles')
<style>
    .container {
        max-width: 100%;
        overflow-x: hidden;
        padding: 0 15px;
    }



    .card {
        max-width: 100%;
    }

    .card-body {
        padding: 1.25rem;
        overflow-x: hidden;
    }



    .badge {
        display: inline;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .refresh-spin {
        animation: spin 1s linear infinite;
        display: inline-block;
    }

    .table-loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    .employee-filter-tabs {
        display: flex;
        gap: 1px;
        background: #f0f2f5;
        padding: 4px;
        border-radius: 8px;
        margin-bottom: 5px;
    }

    .filter-tab {
        flex: 1;
        padding: 10px 20px;
        border: none;
        background: transparent;
        color: #6c757d;
        font-weight: 500;
        transition: all 0.3s ease;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .filter-tab.active {
        background: white;
        color: #0d6efd;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .search-wrapper {
        position: relative;
    }

    .search-wrapper .input-group {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .biometric-instructions {
        background: var(--bs-light, #f8f9fa);
        border-radius: 8px;
        padding: 1.5rem;
        border: 1px solid var(--bs-border-color, #e9ecef);
        margin-bottom: 20px;
    }

    .auth-method-cell .btn {
        width: 100%;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-size: 12px;
    }

    .btn-disabled {
        background-color: var(--bs-secondary-bg, #e9ecef) !important;
        color: var(--bs-secondary-color, #6c757d) !important;
        cursor: not-allowed;
        opacity: 0.8;
    }

    /* Dark mode specific styles */
    [data-bs-theme="dark"] .biometric-instructions {
        background: var(--bs-dark);
        border-color: var(--bs-border-color);
    }

    [data-bs-theme="dark"] .card.bg-light {
        background-color: var(--bs-dark) !important;
        border-color: var(--bs-border-color);
    }

    [data-bs-theme="dark"] .card.bg-light .card-title {
        color: var(--bs-body-color);
    }

    [data-bs-theme="dark"] .card.bg-light .card-text {
        color: var(--bs-secondary-color);
    }

    [data-bs-theme="dark"] .alert-info {
        background-color: rgba(13, 202, 240, 0.1);
        border-color: rgba(13, 202, 240, 0.2);
        color: var(--bs-body-color);
    }

    [data-bs-theme="dark"] .alert-info .alert-heading {
        color: var(--bs-body-color);
    }

    [data-bs-theme="dark"] .alert-info .text-muted {
        color: var(--bs-secondary-color) !important;
    }

    [data-bs-theme="dark"] .employee-filter-tabs {
        background: var(--bs-dark);
        border: 1px solid var(--bs-border-color);
    }

    [data-bs-theme="dark"] .filter-tab {
        color: var(--bs-secondary-color);
    }

    [data-bs-theme="dark"] .filter-tab.active {
        background: var(--bs-primary);
        color: white;
    }

    [data-bs-theme="dark"] .search-wrapper .input-group {
        background: var(--bs-dark);
        border: 1px solid var(--bs-border-color);
    }

    [data-bs-theme="dark"] .search-wrapper .input-group-text {
        background-color: var(--bs-dark);
        border-color: var(--bs-border-color);
        color: var(--bs-secondary-color);
    }

    [data-bs-theme="dark"] .search-wrapper .form-control {
        background-color: var(--bs-dark);
        border-color: var(--bs-border-color);
        color: var(--bs-body-color);
    }

    [data-bs-theme="dark"] .search-wrapper .form-control::placeholder {
        color: var(--bs-secondary-color);
    }

    [data-bs-theme="dark"] .table {
        background-color: var(--bs-dark);
        color: var(--bs-body-color);
    }

    [data-bs-theme="dark"] .table thead th {
        background-color: var(--bs-dark);
        border-color: var(--bs-border-color);
        color: var(--bs-body-color);
    }

    [data-bs-theme="dark"] .table tbody td {
        border-color: var(--bs-border-color);
    }

    [data-bs-theme="dark"] .table-hover tbody tr:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.1);
    }

    [data-bs-theme="dark"] .table-loading-overlay {
        background: rgba(0, 0, 0, 0.8);
    }

    [data-bs-theme="dark"] .form-control {
        background-color: var(--bs-dark);
        border-color: var(--bs-border-color);
        color: var(--bs-body-color);
    }

    [data-bs-theme="dark"] .form-control:focus {
        background-color: var(--bs-dark);
        border-color: var(--bs-primary);
        color: var(--bs-body-color);
    }
</style>
@endpush

@section('content')
<div class="content-wrapper">

     <!-- Add Task Export Buttons Start -->




    <div class="d-flex justify-content-between align-items-center mb-4">

        @if(isset($devices) && count($devices) > 0)
        <div>
            <x-forms.button-primary type="button" class="btn btn-primary mr-3" id="refresh-employees" onclick="refreshEmployees()">
                <i class="fa fa-sync"></i> @lang('biometric::app.refreshEmployeeList')
            </x-forms.button-primary>

            <x-forms.button-secondary type="button" class="btn btn-info" id="fetch-all-data" onclick="fetchAllBiometricData()">
                <i class="fa fa-download"></i> @lang('biometric::app.fetchAllBiometricData')
            </x-forms.button-secondary>
        </div>
        @endif
    </div>

    @if(isset($devices) && count($devices) > 0)
    <div class="alert alert-info mb-3">
        <h5 class="alert-heading"><i class="fa fa-info-circle mr-1"></i> @lang('biometric::app.biometricConfigurationInstructions')</h5>
        <p>@lang('biometric::app.configureEmployeeBiometricIdsInfo')</p>

        <div class="row">
            <div class="col-md-4">
                <div class="card bg-light mb-2">
                    <div class="card-body text-dark py-2">
                        <h6 class="card-title mb-1"><i class="fa fa-id-card mr-1"></i> @lang('biometric::app.employeeId')</h6>
                        <p class="card-text small mb-0">@lang('biometric::app.employeeIdInternalInfo')</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light mb-2">
                    <div class="card-body text-dark py-2">
                        <h6 class="card-title mb-1"><i class="fa fa-fingerprint mr-1"></i> @lang('biometric::app.biometricUserId')</h6>
                        <p class="card-text small mb-0">@lang('biometric::app.enterBiometricUserIdInfo')</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light mb-2">
                    <div class="card-body text-dark py-2">
                        <h6 class="card-title mb-1"><i class="fa fa-check-circle mr-1"></i> @lang('biometric::app.afterSaving')</h6>
                        <p class="card-text small mb-0">@lang('biometric::app.afterSavingAttendanceLogsInfo')</p>
                    </div>
                </div>
            </div>
            </div>
            </div>




        <div class="card border-0 bg-white rounded">
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="employee-filter-tabs">
                            <button type="button" class="filter-tab active" data-filter="active" onclick="filterEmployees('active')">
                                <i class="fa fa-check-circle"></i> @lang('biometric::app.activeEmployees')
                            </button>
                            <button type="button" class="filter-tab" data-filter="inactive" onclick="filterEmployees('inactive')">
                                <i class="fa fa-times-circle"></i> @lang('biometric::app.inactiveEmployees')
                            </button>
                            <button type="button" class="filter-tab" data-filter="all" onclick="filterEmployees('all')">
                                <i class="fa fa-users"></i> @lang('biometric::app.allEmployees')
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="search-wrapper">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-search"></i></span>
                                <input type="text"  class="form-control height-35 f-14" id="search-input" placeholder="@lang('biometric::app.searchEmployees')">
                                <span class="input-group-text clear-search d-none" onclick="clearSearch()">
                                    <i class="fa fa-times"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <form id="save-biometric-form" class="ajax-form">
                    @csrf
                    <div class="table-responsive position-relative">
                        <div id="table-loading" class="table-loading-overlay d-none">
                            <div class="spinner-wrapper">
                                <i class="fa fa-refresh refresh-spin"></i>
                                <span>@lang('biometric::app.refreshing')</span>
                            </div>
                        </div>

                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    {{-- <th width="15%" class="text-center">@lang('biometric::app.clockInMethod')</th> --}}
                                    <th width="30%" class="text-center">@lang('biometric::app.employeeDetails')</th>
                                    <th width="15%" class="text-center">@lang('biometric::app.employeeId')</th>
                                    <th width="15%" class="text-center">@lang('biometric::app.biometricUserId')</th>
                                    <th width="15%" class="text-center">@lang('biometric::app.status')</th>
                                    <th width="15%" class="text-center">@lang('app.action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employees as $employee)
                                <tr class="employee-row"
                                    data-status="{{ $employee->status }}"
                                    data-name="{{ $employee->name }}"
                                    data-employee-id="{{ $employee->employee_id }}"
                                    data-force-biometric="{{ $employee->force_biometric_clockin }}">
                                    {{-- <td class="text-center auth-method-cell">
                                        <button type="button"
                                            class="btn btn-sm {{ $employee->biometric_employee_id ? ($employee->force_biometric_clockin === 1 ? 'btn-success' : 'btn-danger') : 'btn-disabled' }}"
                                            onclick="toggleBiometricMethod({{ $employee->id }})"
                                            {{ !$employee->biometric_employee_id ? 'disabled' : '' }}
                                            data-toggle="tooltip"
                                            title="{{ !$employee->biometric_employee_id ? __('biometric::app.addBiometricIdFirstTooltip') : '' }}">
                                            @if($employee->force_biometric_clockin === 1)
                                                <i class="fa fa-fingerprint"></i>
                                                {{ __('biometric::app.biometricOnly') }}
                                            @else
                                                <i class="fa fa-clock"></i>
                                                {{ __('biometric::app.allMethods') }}
                                            @endif
                                        </button>
                                    </td> --}}
                                    <td>
                                        <x-employee :user="$employee" />
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-between">
                                            <span id="employee-id-{{ $employee->id }}">{{ $employee->employee_id ?: '-' }}</span>

                                            <a href="javascript:;" class="ml-2 text-dark pull-right" onclick="copyToBiometricId({{ $employee->id }})" data-toggle="tooltip" title="@lang('biometric::app.copyToBiometricId')">
                                                <i class="fa fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    </td>
                                    <td>

                                        <input type="text"
                                        class="form-control height-35 f-14"

                                        value="{{ $employee->biometric_employee_id }}"
                                        name="biometric_employee_id[{{ $employee->id }}]"
                                         id="biometric_employee_id_{{ $employee->id }}"
                                        >

                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            @if($employee->biometric_employee_id)
                                                <span class="text-success">
                                                    <i class="fa fa-check-circle"></i> @lang('biometric::app.configured')
                                                    @if($employee->has_fingerprint)
                                                        <i class="fa fa-fingerprint ml-1" data-toggle="tooltip" title="@lang('biometric::app.fingerprintRegistered')"></i>
                                                    @endif
                                                    @if($employee->has_card)
                                                        <i class="fa fa-id-card ml-1" data-toggle="tooltip" title="@lang('biometric::app.cardRegistered')"></i>
                                                    @endif
                                                </span>
                                            @else
                                                <span class="text-warning">
                                                    <i class="fa fa-exclamation-circle"></i> @lang('biometric::app.notConfigured')
                                                </span>
                                            @endif
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        @if($employee->biometric_employee_id)
                                            {{-- <button type="button" class="btn btn-sm btn-info mr-2"
                                                onclick="fetchBiometricData({{ $employee->id }})"
                                                data-toggle="tooltip"
                                                title="@lang('biometric::app.fetchBiometricData')">
                                                <i class="fa fa-download"></i>
                                            </button> --}}
                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="removeFromDevice({{ $employee->id }})"
                                                data-toggle="tooltip"
                                                title="@lang('biometric::app.removeFromDevice')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="form-group mt-4 text-center">
                        <button type="submit" class="btn btn-primary mr-3">@lang('app.save')</button>
                        <a href="{{ route('biometric-devices.index') }}" class="btn btn-secondary">@lang('app.cancel')</a>
                    </div>
                </form>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-md-12">
                <div class="card border-0 bg-white rounded">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fa fa-fingerprint fa-3x text-muted"></i>
                        </div>
                        <h4>@lang('biometric::app.noBiometricDeviceTitle')</h4>
                        <p class="mb-4">@lang('biometric::app.noBiometricDeviceText')</p>
                        <a href="{{ route('biometric-devices.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> @lang('biometric::app.addBiometricDevice')
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Add input event handler for biometric ID fields
        $('input[name^="biometric_employee_id"]').on('input', function() {
            const employeeId = this.name.match(/\[(\d+)\]/)[1];
            const $button = $(`.employee-row button[onclick="toggleBiometricMethod(${employeeId})"]`);
            const hasValue = this.value.trim() !== '';
            const $row = $button.closest('tr');
            const forceBiometric = $row.data('force-biometric') === 1;

            if (hasValue) {
                $button.removeClass('btn-disabled').prop('disabled', false);
                if (forceBiometric) {
                    $button.removeClass('btn-danger').addClass('btn-success')
                        .html('<i class="fa fa-fingerprint"></i> @lang("biometric::app.biometricOnly")');
                    $('#save-biometric-form').append(`<input type="hidden" name="force_biometric_clockin[${employeeId}]" value="1">`);
                } else {
                    $button.removeClass('btn-success').addClass('btn-danger')
                        .html('<i class="fa fa-clock"></i> @lang("biometric::app.allMethods")');
                    $('#save-biometric-form').append(`<input type="hidden" name="force_biometric_clockin[${employeeId}]" value="0">`);
                }

            } else {
                $button.removeClass('btn-success btn-danger').addClass('btn-disabled').prop('disabled', true);
                $button.html('<i class="fa fa-lock"></i> @lang("biometric::app.addBiometricIdFirst")');
                // Remove hidden input if exists
                $(`input[name="force_biometric_clockin[${employeeId}]"]`).remove();
            }


        });

        // Trigger input event for all biometric ID fields on page load
        $('input[name^="biometric_employee_id"]').each(function() {
            $(this).trigger('input');

        });

        // Handle form submission
        $('#save-biometric-form').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const url = "{{ route('biometric-employees.store') }}";

            $.easyAjax({
                url: url,
                container: '#save-biometric-form',
                type: "POST",
                disableButton: true,
                buttonSelector: "button[type=submit]",
                data: form.serialize(),
                success: function(response) {
                    if (response.status === 'success') {
                        window.location.reload();
                    }
                }
            });
        });
    });

    function refreshEmployees() {
        $('#table-loading').removeClass('d-none');
        window.location.reload();
    }

    function filterEmployees(filter) {
        $('.filter-tab').removeClass('active');
        $(`.filter-tab[data-filter="${filter}"]`).addClass('active');

        $('.employee-row').hide();
        if (filter === 'all') {
            $('.employee-row').show();
        } else {
            $(`.employee-row[data-status="${filter}"]`).show();
        }

        applySearch();
    }

    function applySearch() {
        const searchTerm = $('#search-input').val().toLowerCase();
        const activeFilter = $('.filter-tab.active').data('filter');

        $('.employee-row').each(function() {
            const $row = $(this);
            const matchesFilter = activeFilter === 'all' || $row.data('status') === activeFilter;
            const name = $row.data('name').toLowerCase();
            const empId = $row.data('employee-id').toString().toLowerCase();
            const matchesSearch = name.includes(searchTerm) || empId.includes(searchTerm);

            if (matchesFilter && matchesSearch) {
                $row.show();
            } else {
                $row.hide();
            }
        });
    }

    $('#search-input').on('input', function() {
        const hasValue = $(this).val().length > 0;
        $('.clear-search').toggleClass('d-none', !hasValue);
        applySearch();
    });

    function clearSearch() {
        $('#search-input').val('');
        $('.clear-search').addClass('d-none');
        applySearch();
    }

    function copyEmployeeId(employeeId) {
        const text = $(`#employee-id-${employeeId}`).text();
        navigator.clipboard.writeText(text);

        const $icon = $(`.copy-id[onclick="copyEmployeeId(${employeeId})"] i`);
        $icon.removeClass('fa-copy').addClass('fa-check text-success');
        setTimeout(() => {
            $icon.removeClass('fa-check text-success').addClass('fa-copy');
        }, 1500);
    }

    function copyToBiometricId(employeeId) {
        const text = $(`#employee-id-${employeeId}`).text();
        const $input = $(`input[name="biometric_employee_id[${employeeId}]"]`);
        $input.val(text).focus();
        // Trigger input event to update clock method button
        $input.trigger('input');
    }

    function toggleBiometricMethod(employeeId) {
        const $button = $(`.employee-row button[onclick="toggleBiometricMethod(${employeeId})"]`);
        const currentMethod = $button.hasClass('btn-success');

        // Toggle button classes
        $button.toggleClass('btn-success btn-danger');

        // Toggle icon and text
        if (currentMethod) {
            // Switching to All Methods
            $button.html('<i class="fa fa-clock"></i> @lang("biometric::app.allMethods")');
            // Set force_biometric_clockin to 0
            $(`input[name="force_biometric_clockin[${employeeId}]"]`).remove();
            $('#save-biometric-form').append(`<input type="hidden" name="force_biometric_clockin[${employeeId}]" value="0">`);
        } else {
            // Switching to Biometric Only
            $button.html('<i class="fa fa-fingerprint"></i> @lang("biometric::app.biometricOnly")');
            // Set force_biometric_clockin to 1
            $(`input[name="force_biometric_clockin[${employeeId}]"]`).remove();
            $('#save-biometric-form').append(`<input type="hidden" name="force_biometric_clockin[${employeeId}]" value="1">`);
        }
    }

    function fetchBiometricData(employeeId) {
        const $button = $(`.employee-row button[onclick="fetchBiometricData(${employeeId})"]`);
        $button.prop('disabled', true);
        $button.find('i').removeClass('fa-download').addClass('fa-refresh refresh-spin');

        $.easyAjax({
            url: `{{ route('biometric-employees.fetch-biometric-data', '') }}/${employeeId}`,
            type: 'GET',
            success: function(response) {
                if (response.status === 'success') {
                    window.location.reload();
                }
            },
            complete: function() {
                $button.prop('disabled', false);
                $button.find('i').removeClass('fa-refresh refresh-spin').addClass('fa-download');
            }
        });
    }

    function removeFromDevice(employeeId) {
        Swal.fire({
            title: '@lang("messages.sweetAlertTitle")',
            text: '@lang("biometric::app.removeEmployeeFromDeviceWarning")',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '@lang("messages.confirmDelete")',
            cancelButtonText: '@lang("messages.confirmNoArchive")',
            customClass: {
                confirmButton: 'btn btn-primary mr-3',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                $.easyAjax({
                    url: `{{ route('biometric-employees.remove-from-device',['id' => ':id']) }}`.replace(':id', employeeId),
                    type: 'DELETE',
                    data: {
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            window.location.reload();
                        }
                    }
                });
            }
        });
    }

    function fetchAllBiometricData() {
        Swal.fire({
            title: '@lang("messages.sweetAlertTitle")',
            text: '@lang("biometric::app.fetchAllBiometricDataWarning")',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '@lang("app.yes")',
            cancelButtonText: '@lang("app.no")',
            customClass: {
                confirmButton: 'btn btn-primary mr-3',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                const $button = $('#fetch-all-data');
                $button.prop('disabled', true);
                $button.find('i').removeClass('fa-download').addClass('fa-refresh refresh-spin');

                $.easyAjax({
                    url: "{{ route('biometric-employees.fetch-biometric-data') }}",
                    type: 'GET',
                    success: function(response) {
                        if (response.status === 'success') {
                            window.location.reload();
                        }
                    },
                    complete: function() {
                        $button.prop('disabled', false);
                        $button.find('i').removeClass('fa-refresh refresh-spin').addClass('fa-download');
                    }
                });
            }
        });
    }
</script>
@endpush

@endsection
