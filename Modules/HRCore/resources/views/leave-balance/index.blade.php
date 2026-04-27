@extends('layouts.layoutMaster')

@section('title', __('Leave Balance Management'))

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

@section('page-script')
@vite(['resources/assets/js/app/hrcore-leave-balance.js'])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  {{-- Breadcrumb Component --}}
  <x-breadcrumb 
    :title="__('Leave Balance Management')"
    :breadcrumbs="[
      ['name' => __('Human Resources'), 'url' => ''],
      ['name' => __('Leave Management'), 'url' => route('hrcore.leaves.index')],
      ['name' => __('Balance Management'), 'url' => '']
    ]"
    :home-url="url('/')"
  >
    @can('hrcore.manage-leave-balances')
    <div class="btn-group">
      <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
        <i class="bx bx-cog me-1"></i>{{ __('Actions') }}
      </button>
      <ul class="dropdown-menu">
        <li>
          <a class="dropdown-item" href="javascript:void(0);" onclick="showBulkSetModal()">
            <i class="bx bx-upload me-1"></i>{{ __('Bulk Set Initial Balance') }}
          </a>
        </li>
        <li>
          <a class="dropdown-item" href="{{ route('hrcore.leave-balance.summary') }}">
            <i class="bx bx-bar-chart me-1"></i>{{ __('View Summary Report') }}
          </a>
        </li>
      </ul>
    </div>
    @endcan
  </x-breadcrumb>

  {{-- Filters --}}
  <div class="card mb-4">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">{{ __('Employee') }}</label>
          <select class="form-select select2" id="employeeFilter">
            <option value="">{{ __('All Employees') }}</option>
            @foreach(\App\Models\User::whereDoesntHave('roles', function($q) { $q->where('name', 'client'); })->get() as $employee)
              <option value="{{ $employee->id }}">{{ $employee->getFullName() }} ({{ $employee->code }})</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">{{ __('Team') }}</label>
          <select class="form-select select2" id="teamFilter">
            <option value="">{{ __('All Teams') }}</option>
            @foreach(\Modules\HRCore\app\Models\Team::where('status', 'active')->get() as $team)
              <option value="{{ $team->id }}">{{ $team->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4 d-flex align-items-end">
          <button class="btn btn-secondary" onclick="resetFilters()">
            <i class="bx bx-refresh me-1"></i>{{ __('Reset') }}
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- Leave Balance Table --}}
  <div class="card">
    <div class="card-header">
      <h5 class="card-title mb-0">{{ __('Employee Leave Balances') }}</h5>
    </div>
    <div class="card-datatable table-responsive">
      <table class="table" id="leaveBalanceTable">
        <thead>
          <tr>
            <th>{{ __('Employee') }}</th>
            <th>{{ __('Code') }}</th>
            <th>{{ __('Designation') }}</th>
            <th>{{ __('Team') }}</th>
            @foreach(\Modules\HRCore\app\Models\LeaveType::where('status', 'active')->get() as $leaveType)
              <th class="text-center">{{ $leaveType->code }}</th>
            @endforeach
            <th>{{ __('Actions') }}</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>

{{-- Bulk Set Initial Balance Modal --}}
<div class="modal fade" id="bulkSetModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('Bulk Set Initial Balance') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="bulkSetForm">
          <div class="mb-3">
            <label class="form-label">{{ __('Year') }}</label>
            <select class="form-select" name="year" id="bulkYear">
              @for($i = date('Y'); $i >= date('Y') - 2; $i--)
                <option value="{{ $i }}">{{ $i }}</option>
              @endfor
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">{{ __('Leave Type') }}</label>
            <select class="form-select" name="leave_type_id" id="bulkLeaveType">
              @foreach(\Modules\HRCore\app\Models\LeaveType::where('status', 'active')->get() as $leaveType)
                <option value="{{ $leaveType->id }}">{{ $leaveType->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">{{ __('Default Days') }}</label>
            <input type="number" class="form-control" name="default_days" id="defaultDays" step="0.5" min="0" max="365">
            <small class="form-text text-muted">{{ __('This will be applied to all selected employees') }}</small>
          </div>
          <div class="mb-3">
            <label class="form-label">{{ __('Select Employees') }}</label>
            <select class="form-select select2" name="employees[]" id="bulkEmployees" multiple>
              @foreach(\App\Models\User::whereDoesntHave('roles', function($q) { $q->where('name', 'client'); })->get() as $employee)
                <option value="{{ $employee->id }}">{{ $employee->getFullName() }} ({{ $employee->code }})</option>
              @endforeach
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
        <button type="button" class="btn btn-primary" onclick="submitBulkSet()">{{ __('Set Balance') }}</button>
      </div>
    </div>
  </div>
</div>

{{-- Page Data --}}
<script>
const pageData = {
  urls: {
    datatable: @json(route('hrcore.leave-balance.datatable')),
    show: @json(route('hrcore.leave-balance.show', ':id')),
    setInitial: @json(route('hrcore.leave-balance.set-initial')),
    adjust: @json(route('hrcore.leave-balance.adjust')),
    bulkSet: @json(route('hrcore.leave-balance.bulk-set'))
  },
  labels: {
    viewDetails: @json(__('View Details')),
    setBalance: @json(__('Set Balance')),
    adjustBalance: @json(__('Adjust Balance')),
    success: @json(__('Success')),
    error: @json(__('Error')),
    confirmAction: @json(__('Are you sure?'))
  },
  leaveTypes: @json(\Modules\HRCore\app\Models\LeaveType::where('status', 'active')->pluck('code', 'id'))
};
</script>
@endsection