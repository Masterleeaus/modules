@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', __('My Leave Balance'))

@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/apex-charts/apex-charts.scss'
  ])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/apex-charts/apexcharts.js'
  ])
@endsection

@section('page-script')
  @vite(['resources/assets/js/app/hrcore-leave-balance-display.js'])
@endsection

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    {{-- Breadcrumb --}}
    <x-breadcrumb
      :title="__('My Leave Balance')"
      :breadcrumbs="[
        ['name' => __('Leave Management'), 'url' => ''],
        ['name' => __('Leave Balance'), 'url' => '']
      ]"
    />

    {{-- Leave Balance Summary Cards --}}
    <div class="row mb-4">
      @php
        $totalEntitlement = 0;
        $totalUsed = 0;
        $totalAvailable = 0;
        $totalPending = 0;
      @endphp
      
      @foreach($leaveTypes as $type)
        @php
          $entitlement = $type->entitled_leaves ?? $type->days_allowed ?? 0;
          $used = $type->used_leaves ?? 0;
          $available = $type->available_leaves ?? 0;
          
          // Calculate pending leaves for this type
          $pending = \Modules\HRCore\app\Models\LeaveRequest::where('user_id', $user->id)
            ->where('leave_type_id', $type->id)
            ->where('status', 'pending')
            ->whereYear('from_date', date('Y'))
            ->sum('total_days') ?? 0;
          
          $totalEntitlement += $entitlement;
          $totalUsed += $used;
          $totalPending += $pending;
          $totalAvailable += $available;
        @endphp
      @endforeach

      <div class="col-lg-3 col-sm-6 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div class="card-info">
                <p class="card-text text-muted">{{ __('Total Entitlement') }}</p>
                <div class="d-flex align-items-end mb-2">
                  <h4 class="card-title mb-0 me-2">{{ $totalEntitlement }}</h4>
                  <small class="text-muted">{{ __('days') }}</small>
                </div>
              </div>
              <div class="card-icon">
                <span class="badge bg-label-primary rounded p-2">
                  <i class="bx bx-calendar bx-sm"></i>
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
                <p class="card-text text-muted">{{ __('Used Leaves') }}</p>
                <div class="d-flex align-items-end mb-2">
                  <h4 class="card-title mb-0 me-2">{{ $totalUsed }}</h4>
                  <small class="text-muted">{{ __('days') }}</small>
                </div>
              </div>
              <div class="card-icon">
                <span class="badge bg-label-danger rounded p-2">
                  <i class="bx bx-calendar-x bx-sm"></i>
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
                <p class="card-text text-muted">{{ __('Pending Approval') }}</p>
                <div class="d-flex align-items-end mb-2">
                  <h4 class="card-title mb-0 me-2">{{ $totalPending }}</h4>
                  <small class="text-muted">{{ __('days') }}</small>
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
                <p class="card-text text-muted">{{ __('Available Balance') }}</p>
                <div class="d-flex align-items-end mb-2">
                  <h4 class="card-title mb-0 me-2">{{ $totalAvailable }}</h4>
                  <small class="text-muted">{{ __('days') }}</small>
                </div>
              </div>
              <div class="card-icon">
                <span class="badge bg-label-success rounded p-2">
                  <i class="bx bx-calendar-check bx-sm"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Leave Balance Details --}}
    <div class="row">
      <div class="col-lg-8">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __('Leave Balance by Type') }}</h5>
            <a href="{{ route('hrcore.my.leaves.apply') }}" class="btn btn-primary btn-sm">
              <i class="bx bx-plus me-1"></i> {{ __('Apply Leave') }}
            </a>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>{{ __('Leave Type') }}</th>
                    <th class="text-center">{{ __('Entitlement') }}</th>
                    <th class="text-center">{{ __('Used') }}</th>
                    <th class="text-center">{{ __('Pending') }}</th>
                    <th class="text-center">{{ __('Available') }}</th>
                    <th>{{ __('Usage') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($leaveTypes as $type)
                    @php
                      $entitlement = $type->entitled_leaves ?? $type->days_allowed ?? 0;
                      $used = $type->used_leaves ?? 0;
                      $available = $type->available_leaves ?? 0;
                      
                      // Calculate pending leaves for this type
                      $pending = \Modules\HRCore\app\Models\LeaveRequest::where('user_id', $user->id)
                        ->where('leave_type_id', $type->id)
                        ->where('status', 'pending')
                        ->whereYear('from_date', date('Y'))
                        ->sum('total_days') ?? 0;
                      
                      $usagePercentage = $entitlement > 0 ? ($used / $entitlement) * 100 : 0;
                    @endphp
                    <tr>
                      <td>
                        <div class="d-flex align-items-center">
                          <span class="badge rounded-pill me-2" style="background-color: {{ $type->color }}">
                            &nbsp;
                          </span>
                          <span class="fw-medium">{{ $type->name }}</span>
                        </div>
                      </td>
                      <td class="text-center">{{ $entitlement }}</td>
                      <td class="text-center">{{ $used }}</td>
                      <td class="text-center">
                        @if($pending > 0)
                          <span class="badge bg-label-warning">{{ $pending }}</span>
                        @else
                          {{ $pending }}
                        @endif
                      </td>
                      <td class="text-center">
                        <span class="badge bg-label-{{ $available > 0 ? 'success' : 'danger' }}">
                          {{ $available }}
                        </span>
                      </td>
                      <td>
                        <div class="progress" style="height: 6px;">
                          <div class="progress-bar bg-{{ $usagePercentage > 75 ? 'danger' : ($usagePercentage > 50 ? 'warning' : 'success') }}" 
                            role="progressbar" 
                            style="width: {{ $usagePercentage }}%"
                            aria-valuenow="{{ $usagePercentage }}" 
                            aria-valuemin="0" 
                            aria-valuemax="100">
                          </div>
                        </div>
                        <small class="text-muted">{{ round($usagePercentage) }}% {{ __('used') }}</small>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      {{-- Leave Balance Chart --}}
      <div class="col-lg-4">
        <div class="card">
          <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Balance Overview') }}</h5>
          </div>
          <div class="card-body">
            <div id="leaveBalanceChart"></div>
          </div>
        </div>

        {{-- Upcoming Leaves --}}
        <div class="card mt-4">
          <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Upcoming Leaves') }}</h5>
          </div>
          <div class="card-body">
            @php
              $upcomingLeaves = \Modules\HRCore\app\Models\LeaveRequest::where('user_id', $user->id)
                ->where('status', 'approved')
                ->where('from_date', '>', now())
                ->orderBy('from_date')
                ->limit(5)
                ->get();
            @endphp
            
            @if($upcomingLeaves->count() > 0)
              <div class="list-group list-group-flush">
                @foreach($upcomingLeaves as $leave)
                  <div class="list-group-item px-0">
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <h6 class="mb-1">{{ $leave->leaveType->name }}</h6>
                        <small class="text-muted">
                          {{ $leave->from_date->format('M d') }} - {{ $leave->to_date->format('M d, Y') }}
                        </small>
                      </div>
                      <span class="badge bg-label-info">
                        {{ $leave->total_days }} {{ __('days') }}
                      </span>
                    </div>
                  </div>
                @endforeach
              </div>
            @else
              <p class="text-muted text-center mb-0">{{ __('No upcoming leaves scheduled') }}</p>
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- Leave History --}}
    <div class="card mt-4">
      <div class="card-header">
        <h5 class="card-title mb-0">{{ __('Recent Leave History') }}</h5>
      </div>
      <div class="card-body">
        @php
          $leaveHistory = \Modules\HRCore\app\Models\LeaveRequest::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        @endphp
        
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>{{ __('Leave Type') }}</th>
                <th>{{ __('From Date') }}</th>
                <th>{{ __('To Date') }}</th>
                <th>{{ __('Days') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Applied On') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($leaveHistory as $leave)
                <tr>
                  <td>{{ $leave->leaveType->name }}</td>
                  <td>{{ $leave->from_date->format('M d, Y') }}</td>
                  <td>{{ $leave->to_date->format('M d, Y') }}</td>
                  <td>{{ $leave->total_days }}</td>
                  <td>
                    @php
                      $statusColors = [
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'cancelled' => 'secondary'
                      ];
                      $statusValue = is_string($leave->status) ? $leave->status : $leave->status->value;
                    @endphp
                    <span class="badge bg-label-{{ $statusColors[$statusValue] ?? 'primary' }}">
                      {{ ucfirst($statusValue) }}
                    </span>
                  </td>
                  <td>{{ $leave->created_at->format('M d, Y') }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  {{-- Chart Data for JavaScript --}}
  <script>
    window.leaveBalanceData = {
      labels: @json($leaveTypes->pluck('name')),
      entitlement: @json($leaveTypes->map(function($type) {
        return $type->entitled_leaves ?? $type->days_allowed ?? 0;
      })),
      used: @json($leaveTypes->map(function($type) {
        return $type->used_leaves ?? 0;
      })),
      available: @json($leaveTypes->map(function($type) {
        return $type->available_leaves ?? 0;
      }))
    };
  </script>
@endsection