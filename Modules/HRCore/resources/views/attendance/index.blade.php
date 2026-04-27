@extends('layouts.layoutMaster')

@section('title', __('Attendances'))

<!-- Vendor Styles -->
@section('vendor-style')
    @vite([
      'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
      'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
      'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
      'resources/assets/vendor/libs/select2/select2.scss',
      'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
    ])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
    @vite([
      'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
      'resources/assets/vendor/libs/moment/moment.js',
      'resources/assets/vendor/libs/select2/select2.js',
      'resources/assets/vendor/libs/flatpickr/flatpickr.js',
    ])
@endsection

@section('page-script')
    @vite([
      'resources/assets/js/app/hrcore-attendance.js',
    ])
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- Breadcrumb --}}
        <x-breadcrumb
            :title="__('Attendance')"
            :breadcrumbs="[
                ['name' => __('Attendance'), 'url' => '']
            ]"
            :home-url="url('/')"
        />

        {{-- Statistics Cards --}}
        <div class="row mb-4">
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="avatar">
                                <div class="avatar-initial bg-label-primary rounded">
                                    <i class="bx bx-group bx-sm"></i>
                                </div>
                            </div>
                            <div class="text-end">
                                <h4 class="mb-0">{{ $users->count() }}</h4>
                                <small class="text-muted">{{ __('Total Employees') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="avatar">
                                <div class="avatar-initial bg-label-success rounded">
                                    <i class="bx bx-user-check bx-sm"></i>
                                </div>
                            </div>
                            <div class="text-end">
                                <h4 class="mb-0" id="presentCount">0</h4>
                                <small class="text-muted">{{ __('Present Today') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="avatar">
                                <div class="avatar-initial bg-label-warning rounded">
                                    <i class="bx bx-time-five bx-sm"></i>
                                </div>
                            </div>
                            <div class="text-end">
                                <h4 class="mb-0" id="lateCount">0</h4>
                                <small class="text-muted">{{ __('Late Today') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="avatar">
                                <div class="avatar-initial bg-label-danger rounded">
                                    <i class="bx bx-user-x bx-sm"></i>
                                </div>
                            </div>
                            <div class="text-end">
                                <h4 class="mb-0" id="absentCount">0</h4>
                                <small class="text-muted">{{ __('Absent Today') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('Filters') }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="date" class="form-label">{{ __('Select Date') }}</label>
                        <input type="text" id="date" name="date" class="form-control flatpickr-date"
                               value="{{ request()->get('date', now()->format('Y-m-d')) }}"
                               placeholder="YYYY-MM-DD">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="userId" class="form-label">{{ __('Select Employee') }}</label>
                        <select id="userId" name="userId" class="form-select select2">
                            <option value="">{{ __('All Employees') }}</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->code }} - {{ $user->getFullName() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="status" class="form-label">{{ __('Status') }}</label>
                        <select id="status" name="status" class="form-select">
                            <option value="">{{ __('All Status') }}</option>
                            <option value="present">{{ __('Present') }}</option>
                            <option value="late">{{ __('Late') }}</option>
                            <option value="absent">{{ __('Absent') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label d-block">&nbsp;</label>
                        <button type="button" class="btn btn-primary" id="filterBtn">
                            <i class="bx bx-filter-alt me-1"></i> {{ __('Apply Filters') }}
                        </button>
                        <button type="button" class="btn btn-secondary ms-2" id="resetBtn">
                            <i class="bx bx-refresh me-1"></i> {{ __('Reset') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Attendance Table --}}
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('Attendance Records') }}</h5>
                    <div>
                        @can('hrcore.web-check-in')
                        <button type="button" class="btn btn-primary btn-sm" id="webCheckInBtn">
                            <i class="bx bx-log-in-circle me-1"></i> {{ __('Web Check-In') }}
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="attendanceTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Employee') }}</th>
                                <th>{{ __('Shift') }}</th>
                                <th>{{ __('Check In Time') }}</th>
                                <th>{{ __('Check Out Time') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Attendance Offcanvas - Only shown to users with edit permission --}}
    @can('hrcore.edit-attendance')
    <div class="offcanvas offcanvas-end" tabindex="-1" id="editAttendanceOffcanvas" aria-labelledby="editAttendanceOffcanvasLabel">
        <div class="offcanvas-header">
            <h5 id="editAttendanceOffcanvasLabel" class="offcanvas-title">{{ __('Edit Attendance') }}</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="{{ __('Close') }}"></button>
        </div>
        <div class="offcanvas-body">
            <form id="editAttendanceForm">
                <input type="hidden" id="editAttendanceId" name="attendance_id">
                
                <div class="mb-3">
                    <label class="form-label">{{ __('Employee') }}</label>
                    <input type="text" class="form-control" id="editEmployeeName" readonly>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">{{ __('Date') }}</label>
                    <input type="text" class="form-control" id="editDate" readonly>
                </div>
                
                <div class="row g-3">
                    <div class="col-6">
                        <label for="editCheckInTime" class="form-label">{{ __('Check In Time') }} <span class="text-danger">*</span></label>
                        <input type="time" class="form-control" id="editCheckInTime" name="check_in_time" required>
                    </div>
                    <div class="col-6">
                        <label for="editCheckOutTime" class="form-label">{{ __('Check Out Time') }}</label>
                        <input type="time" class="form-control" id="editCheckOutTime" name="check_out_time">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="editStatus" class="form-label">{{ __('Status') }} <span class="text-danger">*</span></label>
                    <select class="form-select" id="editStatus" name="status" required>
                        <option value="present">{{ __('Present') }}</option>
                        <option value="late">{{ __('Late') }}</option>
                        <option value="absent">{{ __('Absent') }}</option>
                        <option value="half-day">{{ __('Half Day') }}</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="editNotes" class="form-label">{{ __('Notes') }}</label>
                    <textarea class="form-control" id="editNotes" name="notes" rows="3"></textarea>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">{{ __('Update Attendance') }}</button>
                    <button type="button" class="btn btn-label-secondary flex-fill" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                </div>
            </form>
        </div>
    </div>
    @endcan

    {{-- Page Data for JavaScript --}}
    <script>
        const pageData = {
            urls: {
                datatable: @json(route('hrcore.attendance.datatable')),
                webCheckIn: @json(route('hrcore.attendance.web-check-in')),
                webAttendance: @json(route('hrcore.attendance.web-attendance')),
                export: @json(route('hrcore.attendance.export')),
                statistics: @json(route('hrcore.attendance.statistics')),
                edit: @json(route('hrcore.attendance.edit', ':id')),
                update: @json(route('hrcore.attendance.update', ':id'))
            },
            labels: {
                search: @json(__('Search')),
                processing: @json(__('Processing...')),
                lengthMenu: @json(__('Show _MENU_ entries')),
                info: @json(__('Showing _START_ to _END_ of _TOTAL_ entries')),
                infoEmpty: @json(__('Showing 0 to 0 of 0 entries')),
                emptyTable: @json(__('No data available')),
                paginate: {
                    first: @json(__('First')),
                    last: @json(__('Last')),
                    next: @json(__('Next')),
                    previous: @json(__('Previous'))
                },
                confirmCheckIn: @json(__('Are you sure you want to check in?')),
                checkInSuccess: @json(__('Checked in successfully')),
                error: @json(__('An error occurred. Please try again.')),
                viewDetails: @json(__('View Details')),
                edit: @json(__('Edit')),
                selectEmployee: @json(__('Select Employee')),
                editAttendance: @json(__('Edit Attendance')),
                updateSuccess: @json(__('Attendance updated successfully')),
                updateError: @json(__('Failed to update attendance')),
                cancel: @json(__('Cancel')),
                update: @json(__('Update'))
            }
        };
    </script>
@endsection
