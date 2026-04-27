@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', __('My Attendance'))

@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss'
  ])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
    'resources/assets/vendor/libs/moment/moment.js'
  ])
@endsection

@section('page-script')
  @vite(['resources/assets/js/app/hrcore-my-attendance.js'])
@endsection

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    {{-- Breadcrumb --}}
    <x-breadcrumb
      :title="__('My Attendance')"
      :breadcrumbs="[
        ['name' => __('Attendance'), 'url' => ''],
        ['name' => __('My Attendance'), 'url' => '']
      ]"
    />

    {{-- Statistics Cards --}}
    <div class="row mb-4">
      <div class="col-lg-3 col-sm-6 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div class="card-info">
                <p class="card-text text-muted">{{ __('Present Days') }}</p>
                <div class="d-flex align-items-end mb-2">
                  <h4 class="card-title mb-0 me-2">{{ $statistics['present'] }}</h4>
                  <small class="text-success">({{ __('This Month') }})</small>
                </div>
              </div>
              <div class="card-icon">
                <span class="badge bg-label-success rounded p-2">
                  <i class="bx bx-check-circle bx-sm"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-sm-6 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div class="card-info">
                <p class="card-text text-muted">{{ __('Absent Days') }}</p>
                <div class="d-flex align-items-end mb-2">
                  <h4 class="card-title mb-0 me-2">{{ $statistics['absent'] }}</h4>
                  <small class="text-danger">({{ __('This Month') }})</small>
                </div>
              </div>
              <div class="card-icon">
                <span class="badge bg-label-danger rounded p-2">
                  <i class="bx bx-x-circle bx-sm"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-sm-6 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div class="card-info">
                <p class="card-text text-muted">{{ __('Late Arrivals') }}</p>
                <div class="d-flex align-items-end mb-2">
                  <h4 class="card-title mb-0 me-2">{{ $statistics['late'] }}</h4>
                  <small class="text-warning">({{ __('This Month') }})</small>
                </div>
              </div>
              <div class="card-icon">
                <span class="badge bg-label-warning rounded p-2">
                  <i class="bx bx-time-five bx-sm"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-sm-6 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div class="card-info">
                <p class="card-text text-muted">{{ __('Half Days') }}</p>
                <div class="d-flex align-items-end mb-2">
                  <h4 class="card-title mb-0 me-2">{{ $statistics['half_day'] }}</h4>
                  <small class="text-info">({{ __('This Month') }})</small>
                </div>
              </div>
              <div class="card-icon">
                <span class="badge bg-label-info rounded p-2">
                  <i class="bx bx-hourglass bx-sm"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Today's Status --}}
    <div class="row mb-4">
      <div class="col-12">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __("Today's Status") }}</h5>
            <span class="text-muted">{{ now()->format('l, F d, Y') }}</span>
          </div>
          <div class="card-body">
            @php
              $todayAttendance = $attendances->first(function($att) {
                return $att->created_at->isToday();
              });
            @endphp
            
            @if($todayAttendance)
              <div class="row">
                <div class="col-md-3">
                  <div class="d-flex align-items-center mb-3">
                    <div class="avatar avatar-sm me-2">
                      <span class="avatar-initial rounded-circle bg-label-success">
                        <i class="bx bx-log-in"></i>
                      </span>
                    </div>
                    <div>
                      <small class="text-muted d-block">{{ __('Check In') }}</small>
                      <strong>
                        @php
                          $checkIn = $todayAttendance->attendanceLogs->where('type', 'check_in')->first();
                        @endphp
                        {{ $checkIn ? $checkIn->created_at->format('h:i A') : '--:--' }}
                      </strong>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="d-flex align-items-center mb-3">
                    <div class="avatar avatar-sm me-2">
                      <span class="avatar-initial rounded-circle bg-label-danger">
                        <i class="bx bx-log-out"></i>
                      </span>
                    </div>
                    <div>
                      <small class="text-muted d-block">{{ __('Check Out') }}</small>
                      <strong>
                        @php
                          $checkOut = $todayAttendance->attendanceLogs->where('type', 'check_out')->last();
                        @endphp
                        {{ $checkOut ? $checkOut->created_at->format('h:i A') : '--:--' }}
                      </strong>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="d-flex align-items-center mb-3">
                    <div class="avatar avatar-sm me-2">
                      <span class="avatar-initial rounded-circle bg-label-info">
                        <i class="bx bx-time"></i>
                      </span>
                    </div>
                    <div>
                      <small class="text-muted d-block">{{ __('Total Hours') }}</small>
                      <strong>{{ $todayAttendance->total_hours ?? '0' }} {{ __('hrs') }}</strong>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="d-flex align-items-center mb-3">
                    <div class="avatar avatar-sm me-2">
                      <span class="avatar-initial rounded-circle bg-label-{{ $todayAttendance->status == 'present' ? 'success' : 'warning' }}">
                        <i class="bx bx-check"></i>
                      </span>
                    </div>
                    <div>
                      <small class="text-muted d-block">{{ __('Status') }}</small>
                      <span class="badge bg-label-{{ $todayAttendance->status == 'present' ? 'success' : 'warning' }}">
                        {{ ucfirst($todayAttendance->status) }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              
              @if(!$checkOut)
                <div class="alert alert-warning mb-0">
                  <i class="bx bx-info-circle me-1"></i>
                  {{ __("Don't forget to check out before leaving!") }}
                </div>
              @endif
            @else
              <div class="alert alert-info mb-0">
                <i class="bx bx-info-circle me-1"></i>
                {{ __('No attendance record for today. Please check in to mark your attendance.') }}
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- Attendance History --}}
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">{{ __('Attendance History') }}</h5>
        <div class="d-flex gap-2">
          <input type="text" id="dateFilter" class="form-control form-control-sm flatpickr-range" 
            placeholder="{{ __('Select Date Range') }}" style="width: 200px;">
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover" id="attendanceTable">
            <thead>
              <tr>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Day') }}</th>
                <th>{{ __('Check In') }}</th>
                <th>{{ __('Check Out') }}</th>
                <th>{{ __('Total Hours') }}</th>
                <th>{{ __('Overtime') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($attendances as $attendance)
                @php
                  $checkIn = $attendance->attendanceLogs->where('type', 'check_in')->first();
                  $checkOut = $attendance->attendanceLogs->where('type', 'check_out')->last();
                @endphp
                <tr>
                  <td>{{ $attendance->created_at->format('M d, Y') }}</td>
                  <td>{{ $attendance->created_at->format('l') }}</td>
                  <td>
                    @if($checkIn)
                      <span class="badge bg-label-success">
                        {{ $checkIn->created_at->format('h:i A') }}
                      </span>
                    @else
                      <span class="text-muted">--:--</span>
                    @endif
                  </td>
                  <td>
                    @if($checkOut)
                      <span class="badge bg-label-danger">
                        {{ $checkOut->created_at->format('h:i A') }}
                      </span>
                    @else
                      <span class="text-muted">--:--</span>
                    @endif
                  </td>
                  <td>
                    @if($attendance->total_hours)
                      <strong>{{ $attendance->total_hours }} hrs</strong>
                    @else
                      <span class="text-muted">--</span>
                    @endif
                  </td>
                  <td>
                    @if($attendance->overtime_hours > 0)
                      <span class="badge bg-label-info">
                        +{{ $attendance->overtime_hours }} hrs
                      </span>
                    @else
                      <span class="text-muted">--</span>
                    @endif
                  </td>
                  <td>
                    @php
                      $statusColors = [
                        'present' => 'success',
                        'absent' => 'danger',
                        'late' => 'warning',
                        'half_day' => 'info',
                        'holiday' => 'secondary',
                        'weekend' => 'secondary'
                      ];
                    @endphp
                    <span class="badge bg-label-{{ $statusColors[$attendance->status] ?? 'primary' }}">
                      {{ ucfirst(str_replace('_', ' ', $attendance->status)) }}
                    </span>
                  </td>
                  <td>
                    @if($attendance->status == 'absent' || $attendance->status == 'late')
                      @can('hrcore.create-attendance-regularization')
                        <a href="{{ route('hrcore.my.attendance.regularization') }}" 
                          class="btn btn-sm btn-label-primary" 
                          data-bs-toggle="tooltip" 
                          title="{{ __('Request Regularization') }}">
                          <i class="bx bx-edit"></i>
                        </a>
                      @endcan
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection