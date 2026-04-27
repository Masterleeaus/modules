@extends('layouts.layoutMaster')

@section('title', __('Attendance Dashboard'))

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/apex-charts/apex-charts.scss'
])
@endsection

@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
    'resources/assets/vendor/libs/moment/moment.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/apex-charts/apexcharts.js'
])
@endsection

@section('content')
<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="d-block mb-1 text-muted">{{ __('Present Today') }}</span>
                        <h3 class="card-title mb-0" id="stat-present">0</h3>
                    </div>
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-success">
                            <i class="bx bx-user-check"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="d-block mb-1 text-muted">{{ __('Absent Today') }}</span>
                        <h3 class="card-title mb-0" id="stat-absent">0</h3>
                    </div>
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-danger">
                            <i class="bx bx-user-x"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="d-block mb-1 text-muted">{{ __('Late Arrivals') }}</span>
                        <h3 class="card-title mb-0" id="stat-late">0</h3>
                    </div>
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-warning">
                            <i class="bx bx-time"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="d-block mb-1 text-muted">{{ __('Pending Requests') }}</span>
                        <h3 class="card-title mb-0" id="stat-pending">0</h3>
                    </div>
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-info">
                            <i class="bx bx-clipboard"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <div class="col-lg-8 col-12 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">{{ __('Attendance Trends (Last 30 Days)') }}</h5>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        {{ __('Options') }}
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="javascript:void(0);" onclick="refreshChart()">
                            <i class="bx bx-refresh me-2"></i>{{ __('Refresh') }}
                        </a>
                        <a class="dropdown-item" href="javascript:void(0);" onclick="exportChart()">
                            <i class="bx bx-download me-2"></i>{{ __('Export') }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="attendanceTrendsChart"></div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('Weekly Summary') }}</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">{{ __('Average Attendance') }}</span>
                    <span class="fw-semibold" id="weekly-avg">0</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">{{ __('Total Hours') }}</span>
                    <span class="fw-semibold" id="weekly-hours">0 hrs</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">{{ __('Team Size') }}</span>
                    <span class="fw-semibold" id="team-size">0</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">{{ __('This Week vs Last Week') }}</span>
                    <span class="badge bg-label-success" id="weekly-comparison">+0%</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Team Attendance Table -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">{{ __('Today\'s Team Attendance') }}</h5>
                <div class="d-flex gap-2">
                    <input type="date" class="form-control form-control-sm" id="attendance-date" value="{{ date('Y-m-d') }}" style="width: auto;">
                    <button type="button" class="btn btn-sm btn-primary" onclick="refreshTeamAttendance()">
                        <i class="bx bx-refresh"></i>
                    </button>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables-team-attendance table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('Employee') }}</th>
                            <th>{{ __('Department') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Check In') }}</th>
                            <th>{{ __('Check Out') }}</th>
                            <th>{{ __('Total Hours') }}</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Pending Regularizations -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">{{ __('Pending Regularization Requests') }}</h5>
                <a href="{{ route('hrcore.attendance-regularization.index') }}" class="btn btn-sm btn-outline-primary">
                    {{ __('View All') }}
                </a>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables-pending-regularizations table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('Employee') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Requested Times') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions Modal -->
<div class="modal fade" id="quickActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quickActionTitle">{{ __('Quick Action') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="quickActionContent">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-primary" id="quickActionSubmit">{{ __('Submit') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
const pageData = {
    routes: {
        stats: "{{ route('hrcore.attendance-dashboard.stats') }}",
        teamAttendance: "{{ route('hrcore.attendance-dashboard.team-attendance') }}",
        pendingRegularizations: "{{ route('hrcore.attendance-dashboard.pending-regularizations') }}",
        regularizationView: "{{ route('hrcore.attendance-regularization.show', ':id') }}",
        regularizationApprove: "{{ route('hrcore.attendance-regularization.approve', ':id') }}",
        regularizationReject: "{{ route('hrcore.attendance-regularization.reject', ':id') }}"
    },
    labels: {
        present: @json(__('Present')),
        absent: @json(__('Absent')),
        late: @json(__('Late')),
        completed: @json(__('Completed')),
        attendancePercentage: @json(__('Attendance %')),
        approve: @json(__('Approve')),
        reject: @json(__('Reject')),
        view: @json(__('View')),
        error: @json(__('An error occurred. Please try again.')),
        confirmApprove: @json(__('Are you sure you want to approve this request?')),
        confirmReject: @json(__('Are you sure you want to reject this request?')),
        approveSuccess: @json(__('Request approved successfully!')),
        rejectSuccess: @json(__('Request rejected successfully!'))
    },
    permissions: {
        canApprove: @json(auth()->user()->can('approve-attendance-regularization'))
    }
};
</script>
@vite(['resources/assets/js/app/hrcore-attendance-dashboard.js'])
@endsection