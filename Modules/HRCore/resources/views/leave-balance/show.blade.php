@extends('layouts.layoutMaster')

@section('title', __('Employee Leave Balance') . ' - ' . $employee->getFullName())

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

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  {{-- Breadcrumb Component --}}
  <x-breadcrumb 
    :title="__('Leave Balance') . ' - ' . $employee->getFullName()"
    :breadcrumbs="[
      ['name' => __('Human Resources'), 'url' => ''],
      ['name' => __('Leave Balance Management'), 'url' => route('hrcore.leave-balance.index')],
      ['name' => $employee->getFullName(), 'url' => '']
    ]"
    :home-url="url('/')"
  >
    <a href="{{ route('hrcore.leave-balance.index') }}" class="btn btn-label-secondary">
      <i class="bx bx-arrow-back me-1"></i>{{ __('Back') }}
    </a>
  </x-breadcrumb>

  {{-- Employee Info Card --}}
  <div class="card mb-4">
    <div class="card-body">
      <div class="row">
        <div class="col-md-3">
          <h6 class="text-muted mb-1">{{ __('Employee') }}</h6>
          <p class="mb-0">{{ $employee->getFullName() }}</p>
        </div>
        <div class="col-md-3">
          <h6 class="text-muted mb-1">{{ __('Code') }}</h6>
          <p class="mb-0">{{ $employee->code }}</p>
        </div>
        <div class="col-md-3">
          <h6 class="text-muted mb-1">{{ __('Designation') }}</h6>
          <p class="mb-0">{{ $employee->designation->name ?? '-' }}</p>
        </div>
        <div class="col-md-3">
          <h6 class="text-muted mb-1">{{ __('Team') }}</h6>
          <p class="mb-0">{{ $employee->team->name ?? '-' }}</p>
        </div>
      </div>
    </div>
  </div>

  {{-- Leave Balances --}}
  <div class="card mb-4">
    <div class="card-header">
      <h5 class="card-title mb-0">{{ __('Leave Balances') }} - {{ $currentYear }}</h5>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>{{ __('Leave Type') }}</th>
              <th class="text-center">{{ __('Entitled') }}</th>
              <th class="text-center">{{ __('Carried Forward') }}</th>
              <th class="text-center">{{ __('Additional') }}</th>
              <th class="text-center">{{ __('Used') }}</th>
              <th class="text-center">{{ __('Available') }}</th>
              <th>{{ __('Actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach($balances as $balance)
            <tr>
              <td>
                <strong>{{ $balance['leaveType']->name }}</strong>
                <br>
                <small class="text-muted">{{ $balance['leaveType']->code }}</small>
              </td>
              <td class="text-center">
                {{ $balance['availableLeave']->entitled_leaves ?? 0 }}
              </td>
              <td class="text-center">
                {{ $balance['availableLeave']->carried_forward_leaves ?? 0 }}
              </td>
              <td class="text-center">
                {{ $balance['availableLeave']->additional_leaves ?? 0 }}
              </td>
              <td class="text-center">
                {{ $balance['availableLeave']->used_leaves ?? 0 }}
              </td>
              <td class="text-center">
                <span class="badge bg-label-success">{{ $balance['currentBalance'] }}</span>
              </td>
              <td>
                <div class="btn-group btn-group-sm">
                  @if(!$balance['availableLeave'])
                  <button class="btn btn-primary" onclick="setInitialBalance({{ $balance['leaveType']->id }})">
                    <i class="bx bx-plus"></i> {{ __('Set Initial') }}
                  </button>
                  @else
                  <button class="btn btn-label-primary" onclick="adjustBalance({{ $balance['leaveType']->id }})">
                    <i class="bx bx-adjust"></i> {{ __('Adjust') }}
                  </button>
                  @endif
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Adjustment History --}}
  <div class="card">
    <div class="card-header">
      <h5 class="card-title mb-0">{{ __('Adjustment History') }}</h5>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>{{ __('Date') }}</th>
              <th>{{ __('Leave Type') }}</th>
              <th>{{ __('Type') }}</th>
              <th>{{ __('Days') }}</th>
              <th>{{ __('Balance Before') }}</th>
              <th>{{ __('Balance After') }}</th>
              <th>{{ __('Reason') }}</th>
              <th>{{ __('Adjusted By') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse($adjustments as $adjustment)
            <tr>
              <td>{{ $adjustment->effective_date->format('d M Y') }}</td>
              <td>{{ $adjustment->leaveType->name }}</td>
              <td>
                @if($adjustment->adjustment_type == 'add')
                  <span class="badge bg-label-success">{{ __('Added') }}</span>
                @elseif($adjustment->adjustment_type == 'deduct')
                  <span class="badge bg-label-danger">{{ __('Deducted') }}</span>
                @else
                  <span class="badge bg-label-primary">{{ __('Initial') }}</span>
                @endif
              </td>
              <td>{{ $adjustment->days }}</td>
              <td>{{ $adjustment->balance_before }}</td>
              <td>{{ $adjustment->balance_after }}</td>
              <td>{{ $adjustment->reason }}</td>
              <td>{{ $adjustment->createdBy->getFullName() ?? '-' }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="8" class="text-center">{{ __('No adjustment history found') }}</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- Set Initial Balance Modal --}}
<div class="modal fade" id="setInitialModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('Set Initial Balance') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="setInitialForm">
          <input type="hidden" name="user_id" value="{{ $employee->id }}">
          <input type="hidden" name="leave_type_id" id="initialLeaveTypeId">
          <input type="hidden" name="year" value="{{ $currentYear }}">
          <div class="mb-3">
            <label class="form-label">{{ __('Leave Type') }}</label>
            <input type="text" class="form-control" id="initialLeaveTypeName" readonly>
          </div>
          <div class="mb-3">
            <label class="form-label">{{ __('Entitled Leaves') }}</label>
            <input type="number" class="form-control" name="entitled_leaves" step="0.5" min="0" max="365" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
        <button type="button" class="btn btn-primary" onclick="submitInitialBalance()">{{ __('Set Balance') }}</button>
      </div>
    </div>
  </div>
</div>

{{-- Adjust Balance Modal --}}
<div class="modal fade" id="adjustBalanceModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('Adjust Leave Balance') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="adjustBalanceForm">
          <input type="hidden" name="user_id" value="{{ $employee->id }}">
          <input type="hidden" name="leave_type_id" id="adjustLeaveTypeId">
          <div class="mb-3">
            <label class="form-label">{{ __('Leave Type') }}</label>
            <input type="text" class="form-control" id="adjustLeaveTypeName" readonly>
          </div>
          <div class="mb-3">
            <label class="form-label">{{ __('Adjustment Type') }}</label>
            <select class="form-select" name="adjustment_type" required>
              <option value="add">{{ __('Add Days') }}</option>
              <option value="deduct">{{ __('Deduct Days') }}</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">{{ __('Days') }}</label>
            <input type="number" class="form-control" name="days" step="0.5" min="0.5" max="365" required>
          </div>
          <div class="mb-3">
            <label class="form-label">{{ __('Reason') }}</label>
            <textarea class="form-control" name="reason" rows="3" required></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
        <button type="button" class="btn btn-primary" onclick="submitAdjustment()">{{ __('Adjust Balance') }}</button>
      </div>
    </div>
  </div>
</div>

<script>
const leaveTypes = @json($balances);

function setInitialBalance(leaveTypeId) {
  const leaveType = leaveTypes.find(b => b.leaveType.id === leaveTypeId);
  $('#initialLeaveTypeId').val(leaveTypeId);
  $('#initialLeaveTypeName').val(leaveType.leaveType.name);
  $('#setInitialModal').modal('show');
}

function adjustBalance(leaveTypeId) {
  const leaveType = leaveTypes.find(b => b.leaveType.id === leaveTypeId);
  $('#adjustLeaveTypeId').val(leaveTypeId);
  $('#adjustLeaveTypeName').val(leaveType.leaveType.name);
  $('#adjustBalanceModal').modal('show');
}

function submitInitialBalance() {
  const formData = $('#setInitialForm').serialize();
  
  $.ajax({
    url: @json(route('hrcore.leave-balance.set-initial')),
    type: 'POST',
    data: formData,
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function(response) {
      if (response.status === 'success') {
        $('#setInitialModal').modal('hide');
        Swal.fire({
          icon: 'success',
          title: @json(__('Success')),
          text: response.data
        }).then(() => {
          location.reload();
        });
      }
    },
    error: function(xhr) {
      Swal.fire({
        icon: 'error',
        title: @json(__('Error')),
        text: xhr.responseJSON?.data || @json(__('An error occurred'))
      });
    }
  });
}

function submitAdjustment() {
  const formData = $('#adjustBalanceForm').serialize();
  
  $.ajax({
    url: @json(route('hrcore.leave-balance.adjust')),
    type: 'POST',
    data: formData,
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function(response) {
      if (response.status === 'success') {
        $('#adjustBalanceModal').modal('hide');
        Swal.fire({
          icon: 'success',
          title: @json(__('Success')),
          text: response.data
        }).then(() => {
          location.reload();
        });
      }
    },
    error: function(xhr) {
      Swal.fire({
        icon: 'error',
        title: @json(__('Error')),
        text: xhr.responseJSON?.data || @json(__('An error occurred'))
      });
    }
  });
}
</script>
@endsection