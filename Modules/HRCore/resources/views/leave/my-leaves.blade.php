@extends('layouts.layoutMaster')

@section('title', __('My Leave Requests'))

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

@section('content')

<x-breadcrumb
  :title="__('My Leave Requests')"
  :breadcrumbs="[
    ['name' => __('Self Service'), 'url' => ''],
    ['name' => __('My Leaves'), 'url' => '']
  ]"
/>

<div class="row mb-4">
  <!-- Statistics Cards -->
  <div class="col-md col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text text-muted">{{ __('Total Requests') }}</p>
            <div class="d-flex align-items-end mb-2">
              <h4 class="card-title mb-0 me-2">{{ $statistics['total'] ?? 0 }}</h4>
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

  <div class="col-md col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text text-muted">{{ __('Pending') }}</p>
            <div class="d-flex align-items-end mb-2">
              <h4 class="card-title mb-0 me-2 text-warning">{{ $statistics['pending'] ?? 0 }}</h4>
            </div>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-warning rounded p-2">
              <i class="bx bx-time bx-sm"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text text-muted">{{ __('Approved') }}</p>
            <div class="d-flex align-items-end mb-2">
              <h4 class="card-title mb-0 me-2 text-success">{{ $statistics['approved'] ?? 0 }}</h4>
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

  <div class="col-md col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text text-muted">{{ __('Rejected') }}</p>
            <div class="d-flex align-items-end mb-2">
              <h4 class="card-title mb-0 me-2 text-danger">{{ $statistics['rejected'] ?? 0 }}</h4>
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
</div>

<div class="card">
  <div class="card-header">
    <div class="d-flex justify-content-between align-items-center">
      <h5 class="mb-0">{{ __('My Leave Requests') }}</h5>
      <div class="d-flex gap-2">
        <a href="{{ route('hrcore.my.leaves.apply') }}" class="btn btn-primary">
          <i class="bx bx-plus me-1"></i> {{ __('Apply for Leave') }}
        </a>
        <a href="{{ route('hrcore.my.leaves.balance') }}" class="btn btn-label-info">
          <i class="bx bx-bar-chart-alt me-1"></i> {{ __('Leave Balance') }}
        </a>
      </div>
    </div>
  </div>

  <div class="card-body">
    <!-- Filters Row -->
    <div class="row mb-3">
      <div class="col-md-3">
        <label class="form-label">{{ __('Status') }}</label>
        <select id="filterStatus" class="form-select">
          <option value="">{{ __('All Status') }}</option>
          <option value="pending">{{ __('Pending') }}</option>
          <option value="approved">{{ __('Approved') }}</option>
          <option value="rejected">{{ __('Rejected') }}</option>
          <option value="cancelled">{{ __('Cancelled') }}</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">{{ __('Leave Type') }}</label>
        <select id="filterLeaveType" class="form-select">
          <option value="">{{ __('All Types') }}</option>
          @foreach($leaveTypes as $type)
            <option value="{{ $type->id }}">{{ $type->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">{{ __('Date From') }}</label>
        <input type="date" id="filterDateFrom" class="form-control">
      </div>
      <div class="col-md-3">
        <label class="form-label">{{ __('Date To') }}</label>
        <input type="date" id="filterDateTo" class="form-control">
      </div>
    </div>

    <!-- DataTable -->
    <div class="table-responsive">
      <table id="leavesTable" class="table table-bordered">
        <thead>
          <tr>
            <th>{{ __('Request Date') }}</th>
            <th>{{ __('Leave Type') }}</th>
            <th>{{ __('From Date') }}</th>
            <th>{{ __('To Date') }}</th>
            <th>{{ __('Days') }}</th>
            <th>{{ __('Status') }}</th>
            <th>{{ __('Actions') }}</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>

<!-- Offcanvas for Leave Details -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="leaveDetailsOffcanvas" aria-labelledby="leaveDetailsOffcanvasLabel">
  <div class="offcanvas-header">
    <h5 id="leaveDetailsOffcanvasLabel" class="offcanvas-title">{{ __('Leave Request Details') }}</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body" id="leaveDetailsContent">
    <div class="text-center">
      <div class="spinner-border spinner-border-sm" role="status">
        <span class="visually-hidden">{{ __('Loading...') }}</span>
      </div>
    </div>
  </div>
</div>

@endsection

@section('page-script')
<script>
window.pageData = {
  urls: {
    datatable: @json(route('hrcore.my.leaves.datatable')),
    show: @json(route('hrcore.my.leaves.show', ['id' => '__ID__'])),
    cancel: @json(route('hrcore.my.leaves.cancel', ['id' => '__ID__']))
  },
  labels: {
    confirmCancel: @json(__('Are you sure you want to cancel this leave request?')),
    cancelTitle: @json(__('Cancel Leave Request')),
    cancelButton: @json(__('Yes, Cancel')),
    cancelButtonText: @json(__('Cancel')),
    success: @json(__('Success')),
    error: @json(__('Error')),
    cancelled: @json(__('Leave request cancelled successfully'))
  }
};
</script>
@vite('Modules/HRCore/resources/assets/js/my-leaves.js')
@endsection