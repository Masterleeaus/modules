@extends('layouts.layoutMaster')

@section('title', __('View Attendance'))

@section('vendor-style')
    @vite([
        'resources/assets/vendor/libs/leaflet/leaflet.scss',
    ])
@endsection

@section('vendor-script')
    @vite([
        'resources/assets/vendor/libs/leaflet/leaflet.js',
    ])
@endsection

@section('page-script')
    @vite([
        'resources/assets/js/app/hrcore-attendance-view.js',
    ])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- Breadcrumb --}}
    <x-breadcrumb 
        :title="__('View Attendance')"
        :breadcrumbs="[
            ['name' => __('Attendance'), 'url' => route('hrcore.attendance.index')],
            ['name' => __('View'), 'url' => '']
        ]"
        :home-url="url('/')"
    />

    <div class="row">
        {{-- Employee Information Card --}}
        <div class="col-xl-4 col-lg-5 col-md-5">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="user-avatar-section">
                        <div class="d-flex align-items-center flex-column">
                            @if($attendance->user->profile_picture)
                                <img class="img-fluid rounded mb-3 mt-4" 
                                     src="{{ $attendance->user->getProfilePicture() }}" 
                                     height="120" width="120" alt="User avatar">
                            @else
                                <div class="avatar avatar-xl mb-3 mt-4">
                                    <span class="avatar-initial rounded bg-label-primary">
                                        {{ $attendance->user->getInitials() }}
                                    </span>
                                </div>
                            @endif
                            <div class="user-info text-center">
                                <h4>{{ $attendance->user->getFullName() }}</h4>
                                <span class="badge bg-label-secondary">{{ $attendance->user->code }}</span>
                            </div>
                        </div>
                    </div>
                    <h5 class="pb-3 border-bottom mb-3 mt-4">{{ __('Details') }}</h5>
                    <div class="info-container">
                        <ul class="list-unstyled mb-4">
                            <li class="mb-3">
                                <span class="fw-medium me-1">{{ __('Employee ID') }}:</span>
                                <span>{{ $attendance->user->code }}</span>
                            </li>
                            <li class="mb-3">
                                <span class="fw-medium me-1">{{ __('Email') }}:</span>
                                <span>{{ $attendance->user->email }}</span>
                            </li>
                            <li class="mb-3">
                                <span class="fw-medium me-1">{{ __('Designation') }}:</span>
                                <span>{{ $attendance->user->designation->name ?? __('N/A') }}</span>
                            </li>
                            <li class="mb-3">
                                <span class="fw-medium me-1">{{ __('Department') }}:</span>
                                <span>{{ $attendance->user->designation->department->name ?? __('N/A') }}</span>
                            </li>
                            <li class="mb-3">
                                <span class="fw-medium me-1">{{ __('Shift') }}:</span>
                                <span>{{ $attendance->shift->name ?? __('Default Shift') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Attendance Details Card --}}
        <div class="col-xl-8 col-lg-7 col-md-7">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Attendance Details') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">{{ __('Date') }}</label>
                            <p class="mb-0">
                                <i class="bx bx-calendar me-1"></i>
                                {{ $attendance->created_at->format('l, F j, Y') }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">{{ __('Status') }}</label>
                            <p class="mb-0">
                                @php
                                    $statusClass = match($attendance->status) {
                                        'present' => 'success',
                                        'late' => 'warning',
                                        'absent' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-label-{{ $statusClass }}">
                                    {{ ucfirst($attendance->status ?? 'present') }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">{{ __('Check In Time') }}</label>
                            <p class="mb-0">
                                <i class="bx bx-log-in-circle me-1"></i>
                                @if($checkInLog = $attendance->attendanceLogs->where('type', 'check_in')->first())
                                    {{ $checkInLog->created_at->format('h:i A') }}
                                @else
                                    <span class="text-muted">{{ __('Not checked in') }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">{{ __('Check Out Time') }}</label>
                            <p class="mb-0">
                                <i class="bx bx-log-out-circle me-1"></i>
                                @if($checkOutLog = $attendance->attendanceLogs->where('type', 'check_out')->last())
                                    {{ $checkOutLog->created_at->format('h:i A') }}
                                @else
                                    <span class="text-muted">{{ __('Not checked out') }}</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">{{ __('Total Hours') }}</label>
                            <p class="mb-0">
                                <i class="bx bx-time me-1"></i>
                                @if($checkInLog && $checkOutLog)
                                    @php
                                        $totalHours = $checkInLog->created_at->diffInHours($checkOutLog->created_at);
                                        $totalMinutes = $checkInLog->created_at->diffInMinutes($checkOutLog->created_at) % 60;
                                    @endphp
                                    {{ $totalHours }}h {{ $totalMinutes }}m
                                @else
                                    <span class="text-muted">{{ __('N/A') }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">{{ __('Late Reason') }}</label>
                            <p class="mb-0">
                                {{ $attendance->late_reason ?? __('N/A') }}
                            </p>
                        </div>
                    </div>

                    @if($attendance->early_checkout_reason)
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label text-muted">{{ __('Early Checkout Reason') }}</label>
                            <p class="mb-0">{{ $attendance->early_checkout_reason }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Attendance Logs Timeline --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Attendance Timeline') }}</h5>
                </div>
                <div class="card-body">
                    <ul class="timeline mb-0">
                        @forelse($attendance->attendanceLogs->sortByDesc('created_at') as $log)
                        <li class="timeline-item pb-4 timeline-item-{{ $log->type === 'check_in' ? 'success' : 'danger' }} border-left-dashed">
                            <span class="timeline-indicator-advanced timeline-indicator-{{ $log->type === 'check_in' ? 'success' : 'danger' }}">
                                <i class="bx bx-{{ $log->type === 'check_in' ? 'log-in-circle' : 'log-out-circle' }}"></i>
                            </span>
                            <div class="timeline-event">
                                <div class="timeline-header border-bottom mb-3">
                                    <h6 class="mb-0">{{ $log->type === 'check_in' ? __('Checked In') : __('Checked Out') }}</h6>
                                    <span class="text-muted">{{ $log->created_at->format('h:i A') }}</span>
                                </div>
                                @if($log->address)
                                <p class="mb-2">
                                    <i class="bx bx-map me-1"></i>
                                    {{ $log->address }}
                                </p>
                                @endif
                                @if($log->notes)
                                <p class="mb-0">
                                    <i class="bx bx-note me-1"></i>
                                    {{ $log->notes }}
                                </p>
                                @endif
                            </div>
                        </li>
                        @empty
                        <li class="text-center text-muted">
                            {{ __('No attendance logs found') }}
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>

            {{-- Location Map (if coordinates available) --}}
            @if($attendance->attendanceLogs->where('latitude', '!=', null)->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Check-in/out Locations') }}</h5>
                </div>
                <div class="card-body">
                    <div id="attendanceMap" style="height: 400px;"></div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Page Data for JavaScript --}}
@php
    $attendanceLogs = $attendance->attendanceLogs->map(function($log) {
        return [
            'type' => $log->type,
            'latitude' => $log->latitude,
            'longitude' => $log->longitude,
            'address' => $log->address,
            'time' => $log->created_at->format('h:i A')
        ];
    })->filter(function($log) {
        return $log['latitude'] && $log['longitude'];
    })->values();
@endphp

<script>
    const pageData = {
        attendance: {
            logs: @json($attendanceLogs)
        },
        labels: {
            checkIn: @json(__('Check In')),
            checkOut: @json(__('Check Out'))
        }
    };
</script>
@endsection