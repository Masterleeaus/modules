@extends('layouts.layoutMaster')

@section('title', __('Employee Profile'))

<!-- Vendor Styles -->
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss'
  ])
@endsection

<!-- Page Styles -->
@section('page-style')
  <style>
    .employee-profile-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 2rem;
      border-radius: 0.5rem;
      margin-bottom: 2rem;
    }
    .employee-avatar {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      border: 4px solid white;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .info-card {
      border-left: 3px solid #667eea;
    }
    /* Modern Tab Styling */
    .nav-tabs-shadow {
      border-bottom: 2px solid #e8e8e8;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .nav-tabs-shadow .nav-link {
      border: none;
      border-bottom: 3px solid transparent;
      color: #566a7f;
      font-weight: 500;
      padding: 0.75rem 1.25rem;
      transition: all 0.3s ease;
    }
    .nav-tabs-shadow .nav-link:hover {
      border-bottom-color: #e8e8e8;
      background-color: #f8f9fa;
    }
    .nav-tabs-shadow .nav-link.active {
      color: #667eea;
      border-bottom-color: #667eea;
      background-color: transparent;
    }
    .nav-tabs-shadow .nav-link i {
      font-size: 1.125rem;
    }
  </style>
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
    'resources/assets/vendor/libs/moment/moment.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js'
  ])
@endsection

@section('page-script')
  @vite(['resources/assets/js/app/hrcore-employees-show.js'])
@endsection

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    {{-- Breadcrumb Component --}}
    <x-breadcrumb
      :title="__('Employee Profile')"
      :breadcrumbs="[
        ['name' => __('Human Resources'), 'url' => ''],
        ['name' => __('Employees'), 'url' => route('hrcore.employees.index')],
        ['name' => $employee->getFullName(), 'url' => '']
      ]"
      :home-url="url('/')"
    />

    {{-- Profile Header --}}
    <div class="card mb-4">
      <div class="card-body">
        <div class="employee-profile-header">
          <div class="row align-items-center">
            <div class="col-auto">
              @if($employee->hasProfilePicture() || $employee->profile_picture)
                <img src="{{ $employee->getProfilePicture() }}" 
                     alt="{{ $employee->getFullName() }}" 
                     class="employee-avatar">
              @else
                <div class="employee-avatar d-flex align-items-center justify-content-center bg-white text-primary">
                  <span class="display-4">{{ $employee->getInitials() }}</span>
                </div>
              @endif
            </div>
            <div class="col">
              <h2 class="mb-1">{{ $employee->getFullName() }}</h2>
              <p class="mb-2">
                <i class="bx bx-briefcase me-1"></i> {{ $employee->designation->name ?? '-' }}
                @if($employee->department)
                  <span class="mx-2">|</span>
                  <i class="bx bx-building me-1"></i> {{ $employee->designation->department->name ?? '-' }}
                @endif
              </p>
              <div class="d-flex flex-wrap gap-2">
                <span class="badge bg-white text-primary">
                  <i class="bx bx-id-card me-1"></i> {{ $employee->code }}
                </span>
                <span class="badge bg-white text-primary">
                  <i class="bx bx-envelope me-1"></i> {{ $employee->email }}
                </span>
                <span class="badge bg-white text-primary">
                  <i class="bx bx-phone me-1"></i> {{ $employee->phone }}
                </span>
                @if($employee->isUnderProbation())
                  <span class="badge bg-white text-warning">
                    <i class="bx bx-time me-1"></i> {{ __('Under Probation') }}
                  </span>
                @endif
              </div>
            </div>
            <div class="col-auto">
              @can('hrcore.edit-employees')
                <a href="{{ route('hrcore.employees.edit', $employee->id) }}" class="btn btn-white text-primary">
                  <i class="bx bx-edit me-1"></i> {{ __('Edit') }}
                </a>
              @endcan
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Nav Tabs --}}
    <div class="nav-align-top mb-4">
      <ul class="nav nav-tabs nav-tabs-shadow" role="tablist">
        <li class="nav-item">
          <button type="button" class="nav-link active d-flex align-items-center" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-profile" aria-controls="navs-top-profile" aria-selected="true">
            <i class="bx bx-user bx-sm me-1"></i> {{ __('Profile') }}
          </button>
        </li>
        <li class="nav-item">
          <button type="button" class="nav-link d-flex align-items-center" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-lifecycle" aria-controls="navs-top-lifecycle" aria-selected="false">
            <i class="bx bx-history bx-sm me-1"></i> {{ __('Lifecycle') }}
          </button>
        </li>
        <li class="nav-item">
          <button type="button" class="nav-link d-flex align-items-center" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-attendance" aria-controls="navs-top-attendance" aria-selected="false">
            <i class="bx bx-calendar-check bx-sm me-1"></i> {{ __('Attendance') }}
          </button>
        </li>
        <li class="nav-item">
          <button type="button" class="nav-link d-flex align-items-center" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-leaves" aria-controls="navs-top-leaves" aria-selected="false">
            <i class="bx bx-calendar-x bx-sm me-1"></i> {{ __('Leaves') }}
          </button>
        </li>
      </ul>
      
      <div class="tab-content mt-3">
        {{-- Profile Tab --}}
        <div class="tab-pane fade show active" id="navs-top-profile" role="tabpanel">
          <div class="row">
            {{-- Personal Information --}}
            <div class="col-md-6 mb-4">
              <div class="card info-card">
                <div class="card-header">
                  <h5 class="card-title mb-0">{{ __('Personal Information') }}</h5>
                </div>
                <div class="card-body">
                  <div class="row mb-3">
                    <div class="col-sm-4 text-muted">{{ __('Full Name') }}</div>
                    <div class="col-sm-8">{{ $employee->getFullName() }}</div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-sm-4 text-muted">{{ __('Date of Birth') }}</div>
                    <div class="col-sm-8">{{ $employee->dob ? \Carbon\Carbon::parse($employee->dob)->format('d M Y') : '-' }}</div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-sm-4 text-muted">{{ __('Gender') }}</div>
                    <div class="col-sm-8">{{ ucfirst($employee->gender) }}</div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-sm-4 text-muted">{{ __('Email') }}</div>
                    <div class="col-sm-8">{{ $employee->email }}</div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-sm-4 text-muted">{{ __('Phone') }}</div>
                    <div class="col-sm-8">{{ $employee->phone }}</div>
                  </div>
                </div>
              </div>
            </div>

            {{-- Work Information --}}
            <div class="col-md-6 mb-4">
              <div class="card info-card">
                <div class="card-header">
                  <h5 class="card-title mb-0">{{ __('Work Information') }}</h5>
                </div>
                <div class="card-body">
                  <div class="row mb-3">
                    <div class="col-sm-4 text-muted">{{ __('Employee Code') }}</div>
                    <div class="col-sm-8">{{ $employee->code }}</div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-sm-4 text-muted">{{ __('Department') }}</div>
                    <div class="col-sm-8">{{ $employee->designation->department->name ?? '-' }}</div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-sm-4 text-muted">{{ __('Designation') }}</div>
                    <div class="col-sm-8">{{ $employee->designation->name ?? '-' }}</div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-sm-4 text-muted">{{ __('Team') }}</div>
                    <div class="col-sm-8">{{ $employee->team->name ?? '-' }}</div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-sm-4 text-muted">{{ __('Shift') }}</div>
                    <div class="col-sm-8">{{ $employee->shift->name ?? '-' }}</div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-sm-4 text-muted">{{ __('Reporting To') }}</div>
                    <div class="col-sm-8">
                      @if($employee->reportingTo)
                        <a href="{{ route('hrcore.employees.show', $employee->reportingTo->id) }}">
                          {{ $employee->reportingTo->getFullName() }}
                        </a>
                      @else
                        -
                      @endif
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-sm-4 text-muted">{{ __('Role') }}</div>
                    <div class="col-sm-8">
                      @foreach($employee->roles as $role)
                        <span class="badge bg-label-primary">{{ $role->name }}</span>
                      @endforeach
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {{-- Bank Accounts --}}
            @if($employee->bankAccounts->isNotEmpty())
            <div class="col-md-12 mb-4">
              <div class="card">
                <div class="card-header">
                  <h5 class="card-title mb-0">{{ __('Bank Accounts') }}</h5>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table">
                      <thead>
                        <tr>
                          <th>{{ __('Bank Name') }}</th>
                          <th>{{ __('Account Name') }}</th>
                          <th>{{ __('Account Number') }}</th>
                          <th>{{ __('Primary') }}</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($employee->bankAccounts as $account)
                          <tr>
                            <td>{{ $account->bank_name }}</td>
                            <td>{{ $account->account_name }}</td>
                            <td>{{ $account->account_number }}</td>
                            <td>
                              @if($account->is_primary)
                                <span class="badge bg-label-success">{{ __('Primary') }}</span>
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
            @endif
          </div>
        </div>
        {{-- End Profile Tab --}}
        
        {{-- Lifecycle Tab --}}
        <div class="tab-pane fade" id="navs-top-lifecycle" role="tabpanel">
          <div class="row">
            {{-- Current Lifecycle State --}}
            <div class="col-12 mb-4">
              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h5 class="card-title mb-0">{{ __('Current Lifecycle State') }}</h5>
                  @can('hrcore.manage-employee-lifecycle')
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="offcanvas" data-bs-target="#changeStateOffcanvas" aria-controls="changeStateOffcanvas">
                      <i class="bx bx-refresh me-1"></i>{{ __('Change State') }}
                    </button>
                  @endcan
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-4">
                      <h6 class="text-muted mb-1">{{ __('Current State') }}</h6>
                      @php
                        $currentState = $employee->lifecycleStates()->latest()->first();
                        $stateColors = [
                          'onboarding' => 'info',
                          'active' => 'success',
                          'inactive' => 'secondary',
                          'probation' => 'warning',
                          'relieved' => 'danger',
                          'terminated' => 'danger',
                          'retired' => 'dark',
                          'resigned' => 'danger',
                          'suspended' => 'warning'
                        ];
                        $stateName = $currentState ? $currentState->state : 'active';
                        $stateColor = $stateColors[$stateName] ?? 'secondary';
                      @endphp
                      <h4><span class="badge bg-label-{{ $stateColor }}">{{ ucfirst($stateName) }}</span></h4>
                    </div>
                    <div class="col-md-4">
                      <h6 class="text-muted mb-1">{{ __('Effective Date') }}</h6>
                      <p class="mb-0">{{ $currentState ? \Carbon\Carbon::parse($currentState->effective_date)->format('d M Y') : \Carbon\Carbon::parse($employee->date_of_joining)->format('d M Y') }}</p>
                    </div>
                    <div class="col-md-4">
                      <h6 class="text-muted mb-1">{{ __('Changed By') }}</h6>
                      <p class="mb-0">{{ $currentState && $currentState->createdBy ? $currentState->createdBy->getFullName() : '-' }}</p>
                    </div>
                  </div>
                  @if($currentState && $currentState->reason)
                    <div class="row mt-3">
                      <div class="col-12">
                        <h6 class="text-muted mb-1">{{ __('Reason') }}</h6>
                        <p class="mb-0">{{ $currentState->reason }}</p>
                      </div>
                    </div>
                  @endif
                </div>
              </div>
            </div>
            
            {{-- Lifecycle History --}}
            <div class="col-12 mb-4">
              <div class="card">
                <div class="card-header">
                  <h5 class="card-title mb-0">{{ __('Lifecycle History') }}</h5>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-hover" id="lifecycleHistoryTable">
                      <thead>
                        <tr>
                          <th>{{ __('Date') }}</th>
                          <th>{{ __('Previous State') }}</th>
                          <th>{{ __('New State') }}</th>
                          <th>{{ __('Reason') }}</th>
                          <th>{{ __('Changed By') }}</th>
                          <th>{{ __('Approved By') }}</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($employee->lifecycleStates()->orderBy('created_at', 'desc')->get() as $state)
                          <tr>
                            <td>{{ \Carbon\Carbon::parse($state->effective_date)->format('d M Y') }}</td>
                            <td>
                              @if($state->previous_state)
                                <span class="badge bg-label-{{ $stateColors[$state->previous_state] ?? 'secondary' }}">
                                  {{ ucfirst($state->previous_state) }}
                                </span>
                              @else
                                -
                              @endif
                            </td>
                            <td>
                              <span class="badge bg-label-{{ $stateColors[$state->state] ?? 'secondary' }}">
                                {{ ucfirst($state->state) }}
                              </span>
                            </td>
                            <td>{{ $state->reason ?: '-' }}</td>
                            <td>{{ $state->createdBy ? $state->createdBy->getFullName() : '-' }}</td>
                            <td>
                              @if($state->approvedBy)
                                {{ $state->approvedBy->getFullName() }}
                                <br>
                                <small class="text-muted">{{ $state->approved_at->format('d M Y h:i A') }}</small>
                              @else
                                -
                              @endif
                            </td>
                          </tr>
                        @empty
                          <tr>
                            <td colspan="6" class="text-center">{{ __('No lifecycle history found') }}</td>
                          </tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            
            {{-- Employee History (All Changes) --}}
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h5 class="card-title mb-0">{{ __('Change History') }}</h5>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-hover" id="employeeHistoryTable">
                      <thead>
                        <tr>
                          <th>{{ __('Date') }}</th>
                          <th>{{ __('Event Type') }}</th>
                          <th>{{ __('Changes') }}</th>
                          <th>{{ __('Changed By') }}</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($employee->employeeHistories()->orderBy('created_at', 'desc')->limit(20)->get() as $history)
                          <tr>
                            <td>{{ $history->created_at->format('d M Y h:i A') }}</td>
                            <td>
                              @php
                                $eventLabels = [
                                  'personal_info_update' => 'Personal Info Update',
                                  'work_info_update' => 'Work Info Update',
                                  'designation_change' => 'Designation Change',
                                  'team_transfer' => 'Team Transfer',
                                  'reporting_change' => 'Reporting Manager Change',
                                  'shift_change' => 'Shift Change',
                                  'status_change' => 'Status Change',
                                  'role_change' => 'Role Change',
                                  'lifecycle_change' => 'Lifecycle State Change'
                                ];
                              @endphp
                              <span class="badge bg-label-primary">
                                {{ $eventLabels[$history->event_type] ?? ucwords(str_replace('_', ' ', $history->event_type)) }}
                              </span>
                            </td>
                            <td>{{ $history->getChangeDescription() }}</td>
                            <td>{{ $history->changedBy ? $history->changedBy->getFullName() : '-' }}</td>
                          </tr>
                        @empty
                          <tr>
                            <td colspan="4" class="text-center">{{ __('No change history found') }}</td>
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
        {{-- End Lifecycle Tab --}}
        
        {{-- Attendance Tab --}}
        <div class="tab-pane fade" id="navs-top-attendance" role="tabpanel">
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h5 class="card-title mb-0">{{ __('Attendance Records') }}</h5>
                  <a href="{{ route('hrcore.attendance.index') }}?userId={{ $employee->id }}" class="btn btn-sm btn-label-primary">
                    <i class="bx bx-list-ul me-1"></i>{{ __('View All') }}
                  </a>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th>{{ __('Date') }}</th>
                          <th>{{ __('Check In') }}</th>
                          <th>{{ __('Check Out') }}</th>
                          <th>{{ __('Total Hours') }}</th>
                          <th>{{ __('Status') }}</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($employee->attendances()->latest()->limit(10)->get() as $attendance)
                          <tr>
                            <td>{{ $attendance->created_at->format('d M Y') }}</td>
                            <td>
                              @php
                                $checkIn = $attendance->attendanceLogs->where('type', 'check_in')->first();
                              @endphp
                              {{ $checkIn ? $checkIn->created_at->format('h:i A') : '-' }}
                            </td>
                            <td>
                              @php
                                $checkOut = $attendance->attendanceLogs->where('type', 'check_out')->last();
                              @endphp
                              {{ $checkOut ? $checkOut->created_at->format('h:i A') : '-' }}
                            </td>
                            <td>
                              @if($checkIn && $checkOut)
                                {{ $checkIn->created_at->diff($checkOut->created_at)->format('%H:%I') }}
                              @else
                                -
                              @endif
                            </td>
                            <td>
                              @php
                                $statusBadges = [
                                  'present' => 'success',
                                  'late' => 'warning',
                                  'absent' => 'danger',
                                  'half-day' => 'info'
                                ];
                                $badgeClass = $statusBadges[$attendance->status] ?? 'secondary';
                              @endphp
                              <span class="badge bg-label-{{ $badgeClass }}">{{ ucfirst($attendance->status) }}</span>
                            </td>
                          </tr>
                        @empty
                          <tr>
                            <td colspan="5" class="text-center">{{ __('No attendance records found') }}</td>
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
        {{-- End Attendance Tab --}}
        
        {{-- Leaves Tab --}}
        <div class="tab-pane fade" id="navs-top-leaves" role="tabpanel">
          <div class="row">
            {{-- Leave Balance --}}
            <div class="col-md-6 mb-4">
              <div class="card">
                <div class="card-header">
                  <h5 class="card-title mb-0">{{ __('Leave Balance') }}</h5>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table">
                      <thead>
                        <tr>
                          <th>{{ __('Leave Type') }}</th>
                          <th class="text-center">{{ __('Entitled') }}</th>
                          <th class="text-center">{{ __('Used') }}</th>
                          <th class="text-center">{{ __('Available') }}</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($leaveBalances as $balance)
                          <tr>
                            <td>
                              <span class="fw-medium">{{ $balance['leaveType']->name }}</span>
                              @if($balance['leaveType']->is_accrual_enabled)
                                <br>
                                <small class="text-muted">{{ __('Accrual') }}: {{ $balance['leaveType']->accrual_rate }} {{ __('per') }} {{ $balance['leaveType']->accrual_frequency }}</small>
                              @endif
                            </td>
                            <td class="text-center">
                              <span class="badge bg-label-primary">{{ $balance['totalBalance'] }}</span>
                            </td>
                            <td class="text-center">
                              <span class="badge bg-label-warning">{{ $balance['totalBalance'] - $balance['availableBalance'] }}</span>
                            </td>
                            <td class="text-center">
                              <span class="badge bg-label-success">{{ $balance['availableBalance'] }}</span>
                            </td>
                          </tr>
                        @empty
                          <tr>
                            <td colspan="4" class="text-center">{{ __('No leave types configured') }}</td>
                          </tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

            {{-- Recent Leave Requests --}}
            <div class="col-md-6 mb-4">
              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h5 class="card-title mb-0">{{ __('Recent Leave Requests') }}</h5>
                  <a href="{{ route('hrcore.leaves.index') }}?employeeFilter={{ $employee->id }}" class="btn btn-sm btn-label-primary">
                    <i class="bx bx-list-ul me-1"></i>{{ __('View All') }}
                  </a>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th>{{ __('Type') }}</th>
                          <th>{{ __('From') }}</th>
                          <th>{{ __('To') }}</th>
                          <th>{{ __('Status') }}</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($employee->leaveRequests()->latest()->limit(5)->get() as $leave)
                          <tr>
                            <td>{{ $leave->leaveType->name ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($leave->from_date)->format('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($leave->to_date)->format('d M Y') }}</td>
                            <td>
                              @if($leave->status instanceof \App\Enums\LeaveRequestStatus)
                                {!! $leave->status->badge() !!}
                              @else
                                @php
                                  $statusBadges = [
                                    'pending' => 'warning',
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                    'cancelled' => 'secondary'
                                  ];
                                  $badgeClass = $statusBadges[$leave->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-label-{{ $badgeClass }}">{{ ucfirst($leave->status) }}</span>
                              @endif
                            </td>
                          </tr>
                        @empty
                          <tr>
                            <td colspan="4" class="text-center">{{ __('No leave requests found') }}</td>
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
        {{-- End Leaves Tab --}}
      </div>
    </div>
    {{-- End Nav Tabs --}}
  </div>

  {{-- Change State Offcanvas --}}
  @can('hrcore.manage-employee-lifecycle')
  <div class="offcanvas offcanvas-end" tabindex="-1" id="changeStateOffcanvas" aria-labelledby="changeStateOffcanvasLabel" style="width: 500px;">
    <div class="offcanvas-header">
      <h5 id="changeStateOffcanvasLabel" class="offcanvas-title">{{ __('Change Employee Lifecycle State') }}</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      <form id="changeStateForm" method="POST" action="{{ route('hrcore.employees.lifecycle.change', $employee->id) }}">
        @csrf
        <div class="mb-4">
          <label for="new_state" class="form-label">{{ __('New State') }} <span class="text-danger">*</span></label>
          <select class="form-select" id="new_state" name="state" required>
            <option value="">{{ __('Select State') }}</option>
            <option value="onboarding">{{ __('Onboarding') }}</option>
            <option value="active">{{ __('Active') }}</option>
            <option value="inactive">{{ __('Inactive') }}</option>
            <option value="probation">{{ __('Probation') }}</option>
            <option value="relieved">{{ __('Relieved') }}</option>
            <option value="terminated">{{ __('Terminated') }}</option>
            <option value="retired">{{ __('Retired') }}</option>
            <option value="resigned">{{ __('Resigned') }}</option>
            <option value="suspended">{{ __('Suspended') }}</option>
          </select>
        </div>
        
        <div class="mb-4">
          <label for="effective_date" class="form-label">{{ __('Effective Date') }} <span class="text-danger">*</span></label>
          <input type="text" class="form-control" id="effective_date" name="effective_date" placeholder="YYYY-MM-DD" required>
        </div>
        
        <div class="mb-4">
          <label for="change_reason" class="form-label">{{ __('Reason') }}</label>
          <textarea class="form-control" id="change_reason" name="reason" rows="3" placeholder="{{ __('Enter reason for state change...') }}"></textarea>
        </div>
        
        <div class="mb-4">
          <label for="change_remarks" class="form-label">{{ __('Remarks') }}</label>
          <textarea class="form-control" id="change_remarks" name="remarks" rows="3" placeholder="{{ __('Additional notes or comments...') }}"></textarea>
        </div>
        
        <div class="d-flex justify-content-end gap-2">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
          <button type="submit" class="btn btn-primary">
            <i class="bx bx-save me-1"></i>{{ __('Change State') }}
          </button>
        </div>
      </form>
    </div>
  </div>
  @endcan

  {{-- Page Data for JavaScript --}}
  <script>
    const pageData = {
      urls: {
        edit: @json(route('hrcore.employees.edit', ':id')),
        update: @json(route('hrcore.employees.update', ':id'))
      },
      labels: {
        editEmployee: @json(__('Edit Employee')),
        updateSuccess: @json(__('Employee updated successfully')),
        updateError: @json(__('Failed to update employee'))
      }
    };
  </script>
@endsection