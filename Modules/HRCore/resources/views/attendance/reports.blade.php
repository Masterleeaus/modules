@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', __('Attendance Reports'))

@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/apex-charts/apex-charts.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss'
  ])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/apex-charts/apexcharts.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js'
  ])
@endsection

@section('page-script')
  @vite(['resources/assets/js/app/hrcore-attendance-reports.js'])
@endsection

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    {{-- Breadcrumb --}}
    <x-breadcrumb
      :title="__('Attendance Reports')"
      :breadcrumbs="[
        ['name' => __('Attendance'), 'url' => ''],
        ['name' => __('Reports'), 'url' => '']
      ]"
    />

    {{-- Report Filters --}}
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="card-title mb-0">{{ __('Report Filters') }}</h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-4">
            <label class="form-label" for="reportPeriod">{{ __('Report Period') }}</label>
            <select id="reportPeriod" class="form-select">
              <option value="current_month" selected>{{ __('Current Month') }}</option>
              <option value="last_month">{{ __('Last Month') }}</option>
              <option value="last_3_months">{{ __('Last 3 Months') }}</option>
              <option value="last_6_months">{{ __('Last 6 Months') }}</option>
              <option value="current_year">{{ __('Current Year') }}</option>
              <option value="custom">{{ __('Custom Range') }}</option>
            </select>
          </div>
          <div class="col-md-4" id="customDateRange" style="display: none;">
            <label class="form-label" for="dateRange">{{ __('Date Range') }}</label>
            <input type="text" id="dateRange" class="form-control flatpickr-range" 
              placeholder="{{ __('Select Date Range') }}" />
          </div>
          <div class="col-md-4 d-flex align-items-end">
            <button class="btn btn-primary" id="generateReport">
              <i class="bx bx-refresh me-1"></i> {{ __('Generate Report') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row mb-4">
      @php
        $totalWorkingDays = 22; // This should be calculated based on the period
        $totalPresent = array_sum(array_column($monthlyStats, 'present'));
        $totalAbsent = array_sum(array_column($monthlyStats, 'absent'));
        $totalLate = array_sum(array_column($monthlyStats, 'late'));
        $totalHours = array_sum(array_column($monthlyStats, 'total_hours'));
        $avgHours = count($monthlyStats) > 0 ? round($totalHours / count($monthlyStats), 1) : 0;
        $attendanceRate = $totalWorkingDays > 0 ? round(($totalPresent / $totalWorkingDays) * 100, 1) : 0;
      @endphp

      <div class="col-lg-3 col-sm-6 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div class="card-info">
                <p class="card-text text-muted">{{ __('Attendance Rate') }}</p>
                <div class="d-flex align-items-end mb-2">
                  <h4 class="card-title mb-0 me-2">{{ $attendanceRate }}%</h4>
                </div>
                <small class="text-success">
                  <i class="bx bx-up-arrow-alt"></i> {{ __('Good') }}
                </small>
              </div>
              <div class="card-icon">
                <span class="badge bg-label-success rounded p-2">
                  <i class="bx bx-check-shield bx-sm"></i>
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
                <p class="card-text text-muted">{{ __('Total Present') }}</p>
                <div class="d-flex align-items-end mb-2">
                  <h4 class="card-title mb-0 me-2">{{ $totalPresent }}</h4>
                  <small class="text-muted">{{ __('days') }}</small>
                </div>
                <small>{{ __('Out of :days days', ['days' => $totalWorkingDays]) }}</small>
              </div>
              <div class="card-icon">
                <span class="badge bg-label-primary rounded p-2">
                  <i class="bx bx-calendar-check bx-sm"></i>
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
                  <h4 class="card-title mb-0 me-2">{{ $totalLate }}</h4>
                  <small class="text-muted">{{ __('times') }}</small>
                </div>
                <small class="text-warning">{{ __('Needs improvement') }}</small>
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
                <p class="card-text text-muted">{{ __('Avg. Work Hours') }}</p>
                <div class="d-flex align-items-end mb-2">
                  <h4 class="card-title mb-0 me-2">{{ $avgHours }}</h4>
                  <small class="text-muted">{{ __('hrs/month') }}</small>
                </div>
                <small>{{ __('Total: :hours hrs', ['hours' => $totalHours]) }}</small>
              </div>
              <div class="card-icon">
                <span class="badge bg-label-info rounded p-2">
                  <i class="bx bx-time bx-sm"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Charts Row --}}
    <div class="row">
      {{-- Monthly Attendance Trend --}}
      <div class="col-lg-8">
        <div class="card">
          <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Monthly Attendance Trend') }}</h5>
          </div>
          <div class="card-body">
            <div id="attendanceTrendChart"></div>
          </div>
        </div>
      </div>

      {{-- Attendance Distribution --}}
      <div class="col-lg-4">
        <div class="card">
          <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Attendance Distribution') }}</h5>
          </div>
          <div class="card-body">
            <div id="attendanceDistributionChart"></div>
            <div class="mt-3">
              <div class="d-flex justify-content-between mb-2">
                <span><i class="bx bx-circle text-success me-1"></i> {{ __('Present') }}</span>
                <strong>{{ $totalPresent }} {{ __('days') }}</strong>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span><i class="bx bx-circle text-danger me-1"></i> {{ __('Absent') }}</span>
                <strong>{{ $totalAbsent }} {{ __('days') }}</strong>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span><i class="bx bx-circle text-warning me-1"></i> {{ __('Late') }}</span>
                <strong>{{ $totalLate }} {{ __('days') }}</strong>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Detailed Monthly Report --}}
    <div class="card mt-4">
      <div class="card-header">
        <h5 class="card-title mb-0">{{ __('Monthly Breakdown') }}</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>{{ __('Month') }}</th>
                <th class="text-center">{{ __('Working Days') }}</th>
                <th class="text-center">{{ __('Present') }}</th>
                <th class="text-center">{{ __('Absent') }}</th>
                <th class="text-center">{{ __('Late') }}</th>
                <th class="text-center">{{ __('Half Day') }}</th>
                <th class="text-center">{{ __('Total Hours') }}</th>
                <th class="text-center">{{ __('Attendance %') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($monthlyStats as $stat)
                @php
                  $workingDays = 22; // This should be calculated
                  $attendancePercent = $workingDays > 0 ? round(($stat['present'] / $workingDays) * 100, 1) : 0;
                @endphp
                <tr>
                  <td><strong>{{ $stat['month'] }}</strong></td>
                  <td class="text-center">{{ $workingDays }}</td>
                  <td class="text-center">
                    <span class="badge bg-label-success">{{ $stat['present'] }}</span>
                  </td>
                  <td class="text-center">
                    <span class="badge bg-label-danger">{{ $stat['absent'] }}</span>
                  </td>
                  <td class="text-center">
                    <span class="badge bg-label-warning">{{ $stat['late'] }}</span>
                  </td>
                  <td class="text-center">
                    <span class="badge bg-label-info">{{ $stat['half_day'] }}</span>
                  </td>
                  <td class="text-center">{{ $stat['total_hours'] }} hrs</td>
                  <td class="text-center">
                    <div class="d-flex align-items-center justify-content-center">
                      <span class="me-2">{{ $attendancePercent }}%</span>
                      <div class="progress" style="width: 60px; height: 6px;">
                        <div class="progress-bar bg-{{ $attendancePercent >= 90 ? 'success' : ($attendancePercent >= 75 ? 'warning' : 'danger') }}" 
                          role="progressbar" 
                          style="width: {{ $attendancePercent }}%">
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr class="table-active">
                <th>{{ __('Total') }}</th>
                <th class="text-center">--</th>
                <th class="text-center">{{ $totalPresent }}</th>
                <th class="text-center">{{ $totalAbsent }}</th>
                <th class="text-center">{{ $totalLate }}</th>
                <th class="text-center">--</th>
                <th class="text-center">{{ $totalHours }} hrs</th>
                <th class="text-center">{{ $attendanceRate }}%</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>

  {{-- Pass data to JavaScript --}}
  <script>
    window.attendanceReportData = {
      monthlyStats: @json($monthlyStats),
      totalPresent: {{ $totalPresent }},
      totalAbsent: {{ $totalAbsent }},
      totalLate: {{ $totalLate }}
    };
  </script>
@endsection