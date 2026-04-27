@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', __('Team Leave Calendar'))

@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/fullcalendar/fullcalendar.scss',
    'resources/assets/vendor/libs/select2/select2.scss'
  ])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/fullcalendar/fullcalendar.js',
    'resources/assets/vendor/libs/select2/select2.js'
  ])
@endsection

@section('page-script')
  @vite(['resources/assets/js/app/hrcore-team-calendar.js'])
@endsection

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    {{-- Breadcrumb --}}
    <x-breadcrumb
      :title="__('Team Leave Calendar')"
      :breadcrumbs="[
        ['name' => __('Leave Management'), 'url' => ''],
        ['name' => __('Team Calendar'), 'url' => '']
      ]"
    />

    <div class="row">
      {{-- Calendar Section --}}
      <div class="col-xl-9 col-lg-8">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __('Team Leave Schedule') }}</h5>
            <div class="d-flex gap-2">
              <button class="btn btn-sm btn-label-primary" id="calendarToday">
                <i class="bx bx-calendar-check me-1"></i> {{ __('Today') }}
              </button>
              <div class="btn-group" role="group">
                <button class="btn btn-sm btn-label-secondary" id="calendarPrev">
                  <i class="bx bx-chevron-left"></i>
                </button>
                <button class="btn btn-sm btn-label-secondary" id="calendarNext">
                  <i class="bx bx-chevron-right"></i>
                </button>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div id="teamCalendar"></div>
          </div>
        </div>
      </div>

      {{-- Team Members & Filters --}}
      <div class="col-xl-3 col-lg-4">
        {{-- Filter Card --}}
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Filters') }}</h5>
          </div>
          <div class="card-body">
            {{-- Department Filter --}}
            <div class="mb-3">
              <label class="form-label" for="departmentFilter">{{ __('Department') }}</label>
              <select id="departmentFilter" class="form-select select2">
                <option value="">{{ __('All Departments') }}</option>
                @foreach(\Modules\HRCore\app\Models\Department::where('status', 'active')->get() as $dept)
                  <option value="{{ $dept->id }}" {{ $userDepartmentId == $dept->id ? 'selected' : '' }}>
                    {{ $dept->name }}
                  </option>
                @endforeach
              </select>
            </div>

            {{-- Leave Type Filter --}}
            <div class="mb-3">
              <label class="form-label">{{ __('Leave Types') }}</label>
              <div class="leave-type-filters">
                @foreach($leaveTypes as $type)
                  <div class="form-check mb-2">
                    <input class="form-check-input leave-type-filter" type="checkbox" 
                      value="{{ $type->id }}" id="leaveType{{ $type->id }}" checked>
                    <label class="form-check-label d-flex align-items-center" for="leaveType{{ $type->id }}">
                      <span class="badge rounded-pill me-2" style="background-color: {{ $type->color }}">
                        &nbsp;
                      </span>
                      {{ $type->name }}
                    </label>
                  </div>
                @endforeach
              </div>
            </div>

            {{-- Status Filter --}}
            <div class="mb-3">
              <label class="form-label">{{ __('Status') }}</label>
              <div class="status-filters">
                <div class="form-check mb-2">
                  <input class="form-check-input status-filter" type="checkbox" 
                    value="approved" id="statusApproved" checked>
                  <label class="form-check-label" for="statusApproved">
                    <span class="badge bg-label-success">{{ __('Approved') }}</span>
                  </label>
                </div>
                <div class="form-check mb-2">
                  <input class="form-check-input status-filter" type="checkbox" 
                    value="pending" id="statusPending" checked>
                  <label class="form-check-label" for="statusPending">
                    <span class="badge bg-label-warning">{{ __('Pending') }}</span>
                  </label>
                </div>
              </div>
            </div>

            <button class="btn btn-primary w-100" id="applyFilters">
              <i class="bx bx-filter-alt me-1"></i> {{ __('Apply Filters') }}
            </button>
          </div>
        </div>

        {{-- Team Members on Leave Today --}}
        <div class="card">
          <div class="card-header">
            <h5 class="card-title mb-0">{{ __('On Leave Today') }}</h5>
            <small class="text-muted">{{ now()->format('F d, Y') }}</small>
          </div>
          <div class="card-body">
            @php
              $todayLeaves = $leaves->filter(function($leave) {
                return $leave->from_date <= now() && $leave->to_date >= now();
              });
            @endphp
            
            @if($todayLeaves->count() > 0)
              <div class="list-group list-group-flush">
                @foreach($todayLeaves as $leave)
                  <div class="list-group-item px-0">
                    <div class="d-flex align-items-center">
                      <div class="avatar avatar-sm me-2">
                        @if($leave->user->profile_photo_path)
                          <img src="{{ asset('storage/' . $leave->user->profile_photo_path) }}" 
                            alt="{{ $leave->user->name }}" class="rounded-circle">
                        @else
                          <span class="avatar-initial rounded-circle bg-label-primary">
                            {{ substr($leave->user->name, 0, 2) }}
                          </span>
                        @endif
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-0">{{ $leave->user->name }}</h6>
                        <small class="text-muted">{{ $leave->leaveType->name }}</small>
                      </div>
                      @if($leave->is_half_day)
                        <span class="badge bg-label-info">{{ __('Half Day') }}</span>
                      @endif
                    </div>
                  </div>
                @endforeach
              </div>
            @else
              <p class="text-muted text-center mb-0">{{ __('No team members on leave today') }}</p>
            @endif
          </div>
        </div>

        {{-- Leave Statistics --}}
        <div class="card mt-4">
          <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Team Statistics') }}</h5>
          </div>
          <div class="card-body">
            @php
              $currentMonth = now()->month;
              $currentYear = now()->year;
              $monthlyLeaves = $leaves->filter(function($leave) use ($currentMonth, $currentYear) {
                return $leave->from_date->month == $currentMonth && 
                       $leave->from_date->year == $currentYear;
              });
            @endphp
            
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted">{{ __('This Month') }}</span>
              <strong>{{ $monthlyLeaves->count() }} {{ __('leaves') }}</strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted">{{ __('Team Size') }}</span>
              <strong>{{ $teamMembers->count() }} {{ __('members') }}</strong>
            </div>
            <div class="d-flex justify-content-between">
              <span class="text-muted">{{ __('Avg. Leave/Person') }}</span>
              <strong>
                {{ $teamMembers->count() > 0 ? round($monthlyLeaves->count() / $teamMembers->count(), 1) : 0 }}
              </strong>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Prepare data for JavaScript --}}
  @php
    $leavesData = $leaves->map(function($leave) {
      return [
        'id' => $leave->id,
        'title' => $leave->user->name . ' - ' . $leave->leaveType->name,
        'start' => $leave->from_date->format('Y-m-d'),
        'end' => $leave->to_date->addDay()->format('Y-m-d'),
        'color' => $leave->leaveType->color,
        'user' => $leave->user->name,
        'leaveType' => $leave->leaveType->name,
        'status' => $leave->status,
        'isHalfDay' => $leave->is_half_day,
        'halfDayType' => $leave->half_day_type,
        'totalDays' => $leave->total_days,
        'reason' => $leave->user_notes
      ];
    })->toArray();
    
    $leaveTypesData = $leaveTypes->map(function($type) {
      return [
        'id' => $type->id,
        'name' => $type->name,
        'color' => $type->color
      ];
    })->toArray();
  @endphp
  
  {{-- Pass data to JavaScript --}}
  <script>
    window.teamCalendarData = {
      leaves: @json($leavesData),
      leaveTypes: @json($leaveTypesData)
    };
  </script>
@endsection