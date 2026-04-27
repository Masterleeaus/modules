@php use App\Enums\LeaveRequestStatus; @endphp
@extends('layouts/layoutMaster')

@section('title', __('Leave Requests'))

<!-- Vendor Styles -->
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/@form-validation/form-validation.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss'
  ])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
    'resources/assets/vendor/libs/@form-validation/popular.js',
    'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
    'resources/assets/vendor/libs/@form-validation/auto-focus.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js'
  ])
@endsection

@section('page-script')
  @vite(['resources/assets/js/app/hrcore-leaves.js'])
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css"/>
  <script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>
@endsection


@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    {{-- Breadcrumb Component --}}
    <x-breadcrumb
      :title="__('Leave Requests')"
      :breadcrumbs="[
        ['name' => __('Human Resources'), 'url' => ''],
        ['name' => __('Leave Requests'), 'url' => '']
      ]"
      :home-url="url('/')"
    />

  {{-- Filters Card --}}
  <div class="card mb-4">
    <div class="card-body">
      <h5 class="card-title">{{ __('Filters') }}</h5>
      <div class="row g-3">
        {{-- Employee Filter --}}
        <div class="col-md-3">
          <label for="employeeFilter" class="form-label">{{ __('Filter by Employee') }}</label>
          <select id="employeeFilter" name="employeeFilter" class="form-select" style="width: 100%;">
            <option value="" selected>{{ __('All Employees') }}</option>
          </select>
        </div>

        {{-- Date Filter --}}
        <div class="col-md-3">
          <label for="dateFilter" class="form-label">{{ __('Filter by Date') }}</label>
          <input type="text" id="dateFilter" name="dateFilter" class="form-control" placeholder="{{ __('Select date') }}">
        </div>

        {{-- Leave Type Filter --}}
        <div class="col-md-3">
          <label for="leaveTypeFilter" class="form-label">{{ __('Filter by Leave Type') }}</label>
          <select id="leaveTypeFilter" name="leaveTypeFilter" class="form-select">
            <option value="" selected>{{ __('All Leave Types') }}</option>
            @foreach($leaveTypes as $leaveType)
              <option value="{{ $leaveType->id }}">{{ $leaveType->name }}</option>
            @endforeach
          </select>
        </div>

        {{-- Status Filter --}}
        <div class="col-md-3">
          <label for="statusFilter" class="form-label">{{ __('Filter by Status') }}</label>
          <select id="statusFilter" name="statusFilter" class="form-select">
            <option value="" selected>{{ __('All Statuses') }}</option>
            @foreach(LeaveRequestStatus::cases() as $status)
              <option value="{{ $status->value }}">{{ __($status->name) }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
  </div>

  {{-- Leave Requests Table --}}
  <div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
      <h5 class="card-title mb-0">{{ __('Leave Requests List') }}</h5>
      @can('hrcore.create-leave')
        <button type="button" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddLeave">
          <i class="bx bx-plus me-1"></i>{{ __('Add Leave Request') }}
        </button>
      @endcan
    </div>
    <div class="card-datatable table-responsive">
      <table id="leaveRequestsTable" class="table">
        <thead>
          <tr>
            <th>{{ __('ID') }}</th>
            <th>{{ __('Employee') }}</th>
            <th>{{ __('Leave Type') }}</th>
            <th>{{ __('Leave Dates') }}</th>
            <th>{{ __('Status') }}</th>
            <th>{{ __('Document') }}</th>
            <th>{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>

  {{-- Include offcanvas for leave details --}}
  @include('hrcore::leave._leave_request_details')
  
  {{-- Include offcanvas for adding leave --}}
  @include('hrcore::leave._add_leave_request')

  {{-- Page Data for JavaScript --}}
  <script>
    const pageData = {
      urls: {
        datatable: @json(route('hrcore.leaves.datatable')),
        show: @json(route('hrcore.leaves.show', ':id')),
        action: @json(route('hrcore.leaves.action', ':id')),
        edit: @json(route('hrcore.leaves.edit', ':id'))
      },
      labels: {
        search: @json(__('Search')),
        processing: @json(__('Processing...')),
        lengthMenu: @json(__('Show _MENU_ entries')),
        info: @json(__('Showing _START_ to _END_ of _TOTAL_ entries')),
        infoEmpty: @json(__('Showing 0 to 0 of 0 entries')),
        emptyTable: @json(__('No data available')),
        paginate: {
          first: @json(__('First')),
          last: @json(__('Last')),
          next: @json(__('Next')),
          previous: @json(__('Previous'))
        },
        selectEmployee: @json(__('Select Employee')),
        viewDetails: @json(__('View Details')),
        error: @json(__('An error occurred. Please try again.')),
        success: @json(__('Success')),
        confirmAction: @json(__('Are you sure?')),
        approved: @json(__('Approved')),
        rejected: @json(__('Rejected')),
        cancelled: @json(__('Cancelled')),
        pending: @json(__('Pending')),
        addLeaveRequest: @json(__('Add Leave Request')),
        editLeaveRequest: @json(__('Edit Leave Request')),
        submitRequest: @json(__('Submit Request')),
        updateRequest: @json(__('Update Request'))
      }
    };
  </script>
@endsection

