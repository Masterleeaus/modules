@extends('layouts.layoutMaster')

@section('title', __('Web Attendance'))

@section('vendor-style')
    @vite([
        'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
    ])
@endsection

@section('vendor-script')
    @vite([
        'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
    ])
@endsection

@section('page-style')
<style>
    .attendance-clock {
        font-family: 'Courier New', monospace;
        font-size: 3rem;
        font-weight: bold;
        color: #566a7f;
    }

    .attendance-date {
        font-size: 1.2rem;
        color: #697a8d;
    }

    .attendance-card {
        min-height: 200px;
        transition: all 0.3s ease;
    }

    .attendance-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 25px 0 rgba(67, 89, 113, 0.15);
    }

    .check-btn {
        min-width: 200px;
        height: 60px;
        font-size: 1.2rem;
        font-weight: 600;
    }


    .attendance-log-item {
        border-left: 3px solid #e7e7ff;
        padding-left: 1rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .attendance-log-item:hover {
        border-left-color: #696cff;
    }

    .attendance-log-item.check-in {
        border-left-color: #71dd37;
    }

    .attendance-log-item.check-out {
        border-left-color: #ffab00;
    }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- Breadcrumb --}}
    <x-breadcrumb
        :title="__('Web Attendance')"
        :breadcrumbs="[
            ['name' => __('Attendance'), 'url' => route('hrcore.attendance.index')],
            ['name' => __('Web Check-in'), 'url' => '']
        ]"
        :home-url="url('/')"
    />

    {{-- Clock and Date Display --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card text-center attendance-card">
                <div class="card-body">
                    <div class="attendance-date mb-2" id="currentDate">
                        {{ now()->format('l, F d, Y') }}
                    </div>
                    <div class="attendance-clock" id="currentTime">
                        {{ now()->format('h:i:s A') }}
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-label-primary">{{ auth()->user()->shift?->name ?? __('Default Shift') }}</span>
                        <span class="badge bg-label-info ms-2">{{ auth()->user()->code }} - {{ auth()->user()->getFullName() }}</span>
                        @can('hrcore.multiple-check-in')
                            <span class="badge bg-label-success ms-2">
                                <i class="bx bx-refresh me-1"></i>{{ __('Multiple Check-ins Enabled') }}
                            </span>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Status Cards --}}
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card attendance-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="bx bx-log-in bx-sm"></i>
                            </span>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ __('Check In') }}</h5>
                            <small class="text-muted" id="checkInTime">{{ __('Not checked in') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card attendance-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="bx bx-log-out bx-sm"></i>
                            </span>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ __('Check Out') }}</h5>
                            <small class="text-muted" id="checkOutTime">{{ __('Not checked out') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card attendance-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="bx bx-time-five bx-sm"></i>
                            </span>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ __('Working Hours') }}</h5>
                            <small class="text-muted" id="workingHours">0h 0m</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Action Card --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bx bx-fingerprint" style="font-size: 4rem; color: #696cff;"></i>
                    </div>

                    <h3 class="mb-4" id="statusMessage">{{ __('Ready to check in') }}</h3>

                    <button type="button" class="btn btn-primary check-btn" id="checkInOutBtn">
                        <i class="bx bx-log-in me-2"></i>
                        <span id="checkBtnText">{{ __('Check In') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Today's Activity --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __("Today's Activity Log") }}</h5>
                </div>
                <div class="card-body">
                    <div id="todayLogs">
                        <div class="text-center text-muted py-4">
                            <i class="bx bx-calendar-x" style="font-size: 3rem;"></i>
                            <p class="mt-2">{{ __('No activity recorded today') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Page Data --}}
<script>
    const pageData = {
        urls: {
            checkInOut: @json(route('hrcore.attendance.web-check-in')),
            getTodayStatus: @json(route('hrcore.attendance.today-status'))
        },
        labels: {
            checkIn: @json(__('Check In')),
            checkOut: @json(__('Check Out')),
            checking: @json(__('Processing...')),
            readyToCheckIn: @json(__('Ready to check in')),
            readyToCheckOut: @json(__('Ready to check out')),
            checkedIn: @json(__('Checked in successfully')),
            checkedOut: @json(__('Checked out successfully')),
            error: @json(__('An error occurred. Please try again.')),
            confirmCheckIn: @json(__('Confirm check in?')),
            confirmCheckOut: @json(__('Confirm check out?')),
            notCheckedIn: @json(__('Not checked in')),
            notCheckedOut: @json(__('Not checked out')),
            alreadyCheckedIn: @json(__('You have already checked in today')),
            alreadyCheckedOut: @json(__('You have already checked out today')),
            multipleCheckInAllowed: @json(__('(Multiple check-ins allowed)'))
        }
    };
</script>
@endsection

@section('page-script')
    @vite(['resources/assets/js/app/hrcore-web-attendance.js'])
@endsection
