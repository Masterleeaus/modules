@extends('layouts.layoutMaster')

@section('title', __('Attendance Regularization Management'))

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/dropzone/dropzone.scss'
])
@endsection

@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
    'resources/assets/vendor/libs/moment/moment.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/dropzone/dropzone.js'
])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- Breadcrumb --}}
    <x-breadcrumb
      :title="__('Regularization Management')"
      :breadcrumbs="[
        ['name' => __('HR'), 'url' => ''],
        ['name' => __('Attendance'), 'url' => ''],
        ['name' => __('Regularization Management'), 'url' => '']
      ]"
    />

    {{-- HR Statistics Cards --}}
    <div class="row mb-4">
        {{-- Total Requests Card --}}
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md me-3">
                            <div class="avatar-initial bg-label-primary rounded">
                                <i class="bx bx-file bx-sm"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="mb-0" id="totalCount">0</h4>
                            <small class="text-muted">{{ __('Total Requests') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pending Approval Card --}}
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md me-3">
                            <div class="avatar-initial bg-label-warning rounded">
                                <i class="bx bx-time-five bx-sm"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="mb-0" id="pendingCount">0</h4>
                            <small class="text-muted">{{ __('Pending Approval') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Approved Today Card --}}
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md me-3">
                            <div class="avatar-initial bg-label-success rounded">
                                <i class="bx bx-check-circle bx-sm"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="mb-0" id="approvedToday">0</h4>
                            <small class="text-muted">{{ __('Approved Today') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rejected Today Card --}}
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md me-3">
                            <div class="avatar-initial bg-label-danger rounded">
                                <i class="bx bx-x-circle bx-sm"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="mb-0" id="rejectedToday">0</h4>
                            <small class="text-muted">{{ __('Rejected Today') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters Card --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Filters') }}</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label" for="date-from">{{ __('From Date') }}</label>
                    <input type="text" class="form-control" id="date-from" placeholder="YYYY-MM-DD">
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="date-to">{{ __('To Date') }}</label>
                    <input type="text" class="form-control" id="date-to" placeholder="YYYY-MM-DD">
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="status-filter">{{ __('Status') }}</label>
                    <select class="form-select" id="status-filter">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="pending">{{ __('Pending') }}</option>
                        <option value="approved">{{ __('Approved') }}</option>
                        <option value="rejected">{{ __('Rejected') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="type-filter">{{ __('Type') }}</label>
                    <select class="form-select" id="type-filter">
                        <option value="">{{ __('All Types') }}</option>
                        <option value="missing_checkin">{{ __('Missing Check-in') }}</option>
                        <option value="missing_checkout">{{ __('Missing Check-out') }}</option>
                        <option value="wrong_time">{{ __('Wrong Time') }}</option>
                        <option value="forgot_punch">{{ __('Forgot to Punch') }}</option>
                        <option value="other">{{ __('Other') }}</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="user-filter">{{ __('Employee') }}</label>
                    <select class="form-select select2" id="user-filter">
                        <option value="">{{ __('All Employees') }}</option>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <button type="button" class="btn btn-primary" onclick="applyFilters()">
                        <i class='bx bx-search me-1'></i> {{ __('Apply Filters') }}
                    </button>
                    <button type="button" class="btn btn-label-secondary" onclick="clearFilters()">
                        <i class='bx bx-reset me-1'></i> {{ __('Reset Filters') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- DataTable Card with Create Button in Header --}}
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">{{ __('All Regularization Requests') }}</h5>
                <div class="d-flex align-items-center gap-3">
                    {{-- Search Input --}}
                    <div class="input-group" style="width: 250px;">
                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                        <input type="text" id="searchInput" class="form-control" placeholder="{{ __('Search...') }}" />
                    </div>
                    {{-- View Mode Indicator --}}
                    <span class="badge bg-label-info me-2">
                        <i class='bx bx-shield me-1'></i>{{ __('HR View') }}
                    </span>
                    {{-- Create Button - for HR to submit on behalf of employees --}}
                    @can('hrcore.create-attendance-regularization')
                    <button type="button" class="btn btn-primary" onclick="openCreateOffcanvas()">
                        <i class='bx bx-plus me-1'></i>{{ __('Create Request') }}
                    </button>
                    @endcan
                </div>
            </div>
        </div>
        <div class="card-datatable table-responsive">
            <table class="datatables-regularization table table-hover">
                <thead>
                    <tr>
                        <th>{{ __('ID') }}</th>
                        <th>{{ __('Employee') }}</th>
                        <th>{{ __('Department') }}</th>
                        <th>{{ __('Request Date') }}</th>
                        <th>{{ __('Attendance Date') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Manager') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Create/Edit Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="regularizationOffcanvas" aria-labelledby="regularizationOffcanvasLabel" style="width: 500px;">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="regularizationOffcanvasLabel">{{ __('Attendance Regularization Request') }}</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="regularizationForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="regularizationId" name="id">
            <div class="mb-3">
                <label class="form-label">{{ __('Date') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control flatpickr-date" name="date" id="date" required>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('Type') }} <span class="text-danger">*</span></label>
                <select class="form-select" name="type" id="type" required>
                    <option value="">{{ __('Select Type') }}</option>
                    <option value="missing_checkin">{{ __('Missing Check-in') }}</option>
                    <option value="missing_checkout">{{ __('Missing Check-out') }}</option>
                    <option value="wrong_time">{{ __('Wrong Time') }}</option>
                    <option value="forgot_punch">{{ __('Forgot to Punch') }}</option>
                    <option value="other">{{ __('Other') }}</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('Requested Check-in Time') }}</label>
                <input type="time" class="form-control" name="requested_check_in_time" id="requested_check_in_time">
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('Requested Check-out Time') }}</label>
                <input type="time" class="form-control" name="requested_check_out_time" id="requested_check_out_time">
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('Reason') }} <span class="text-danger">*</span></label>
                <textarea class="form-control" name="reason" id="reason" rows="4" placeholder="{{ __('Explain the reason for regularization...') }}" maxlength="1000" required></textarea>
                <div class="form-text">{{ __('Maximum 1000 characters') }}</div>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('Attachments') }}</label>
                <input type="file" class="form-control" name="attachments[]" id="attachments" multiple accept=".pdf,.jpg,.jpeg,.png">
                <div class="form-text">{{ __('Supported formats: PDF, JPG, PNG. Max file size: 5MB each.') }}</div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill" id="submitBtn">
                    <i class='bx bx-send me-1'></i>{{ __('Submit Request') }}
                </button>
                <button type="button" class="btn btn-label-secondary flex-fill" data-bs-dismiss="offcanvas">
                    <i class='bx bx-x me-1'></i>{{ __('Cancel') }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- View Details Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="viewOffcanvas" aria-labelledby="viewOffcanvasLabel" style="width: 500px;">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="viewOffcanvasLabel">{{ __('Regularization Details') }}</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body" id="viewContent">
        <!-- Content will be loaded dynamically -->
    </div>
</div>

<!-- Approval Modal -->
<div class="modal fade" id="approvalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approvalModalTitle">{{ __('Process Request') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="approvalForm">
                @csrf
                <input type="hidden" id="approvalId" name="id">
                <input type="hidden" id="approvalAction" name="action">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Manager Comments') }}</label>
                        <textarea class="form-control" name="manager_comments" id="manager_comments" rows="3" placeholder="{{ __('Optional comments...') }}" maxlength="500"></textarea>
                        <div class="form-text">{{ __('Maximum 500 characters') }}</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn" id="approvalBtn">{{ __('Submit') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
const pageData = {
    routes: {
        datatable: "{{ route('hrcore.attendance-regularization.datatable') }}",
        store: "{{ route('hrcore.attendance-regularization.store') }}",
        show: "{{ route('hrcore.attendance-regularization.show', ':id') }}",
        edit: "{{ route('hrcore.attendance-regularization.edit', ':id') }}",
        update: "{{ route('hrcore.attendance-regularization.update', ':id') }}",
        approve: "{{ route('hrcore.attendance-regularization.approve', ':id') }}",
        reject: "{{ route('hrcore.attendance-regularization.reject', ':id') }}",
        destroy: "{{ route('hrcore.attendance-regularization.destroy', ':id') }}",
        userSearch: "{{ route('hrcore.employees.search') }}"
    },
    labels: {
        confirmDelete: @json(__('Are you sure you want to delete this regularization request?')),
        confirmApprove: @json(__('Are you sure you want to approve this request?')),
        confirmReject: @json(__('Are you sure you want to reject this request?')),
        deleteSuccess: @json(__('Regularization request deleted successfully!')),
        approveSuccess: @json(__('Request approved successfully!')),
        rejectSuccess: @json(__('Request rejected successfully!')),
        submitSuccess: @json(__('Request submitted successfully!')),
        updateSuccess: @json(__('Request updated successfully!')),
        error: @json(__('An error occurred. Please try again.')),
        required: @json(__('This field is required')),
        invalidEmail: @json(__('Please enter a valid email address')),
        pending: @json(__('Pending')),
        approved: @json(__('Approved')),
        rejected: @json(__('Rejected')),
        allEmployees: @json(__('All Employees'))
    },
    permissions: {
        canView: @json(auth()->user()->can('hrcore.view-attendance-regularization')),
        canCreate: @json(auth()->user()->can('hrcore.create-attendance-regularization')),
        canEdit: @json(auth()->user()->can('hrcore.edit-attendance-regularization')),
        canDelete: @json(auth()->user()->can('hrcore.delete-attendance-regularization')),
        canApprove: @json(auth()->user()->can('hrcore.approve-attendance-regularization'))
    }
};
</script>
@vite(['resources/assets/js/app/hrcore-attendance-regularization.js'])
@endsection