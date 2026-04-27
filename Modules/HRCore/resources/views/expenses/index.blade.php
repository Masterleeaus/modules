@php
  use App\Enums\ExpenseRequestStatus;
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', __('Expense Requests'))

<!-- Vendor Styles -->
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
    'resources/assets/vendor/libs/@form-validation/form-validation.scss',
    'resources/assets/vendor/libs/animate-css/animate.scss',
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
  @vite(['resources/js/main-datatable.js'])
  @vite(['resources/js/main-helper.js'])
  @vite(['Modules/HRCore/resources/assets/js/expenses.js'])
@endsection


@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    {{-- Breadcrumb Component --}}
    <x-breadcrumb
      :title="__('Expense Requests')"
      :breadcrumbs="[
        ['name' => __('Human Resources'), 'url' => ''],
        ['name' => __('Expense Management'), 'url' => ''],
        ['name' => __('Expense Requests'), 'url' => '']
      ]"
      :home-url="url('/')"
    />

    {{-- Page Header --}}
    <div class="row mb-4">
      <div class="col">
        <p class="text-muted mb-0">{{ __('Manage employee expense requests and approvals') }}</p>
      </div>
      <div class="col-auto">
        @can('hrcore.create-expense')
          <a href="{{ route('hrcore.expenses.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-0 me-sm-2"></i>
            <span class="d-none d-sm-inline-block">{{ __('Create Expense Request') }}</span>
          </a>
        @endcan
      </div>
    </div>


    {{-- Filters Card --}}
    <div class="card mb-4">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3">
            <label for="statusFilter" class="form-label">{{ __('Status') }}</label>
            <select id="statusFilter" name="statusFilter" class="form-select">
              <option value="">{{ __('All Status') }}</option>
              <option value="pending">{{ __('Pending') }}</option>
              <option value="approved">{{ __('Approved') }}</option>
              <option value="rejected">{{ __('Rejected') }}</option>
              <option value="processed">{{ __('Processed') }}</option>
            </select>
          </div>

          <div class="col-md-3">
            <label for="employeeFilter" class="form-label">{{ __('Employee') }}</label>
            <select id="employeeFilter" name="employeeFilter" class="form-select select2">
              <option value="">{{ __('All Employees') }}</option>
              @foreach($employees as $employee)
                <option value="{{ $employee->id }}">{{ $employee->getFullName() }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-3">
            <label for="expenseTypeFilter" class="form-label">{{ __('Expense Type') }}</label>
            <select id="expenseTypeFilter" name="expenseTypeFilter" class="form-select select2">
              <option value="">{{ __('All Types') }}</option>
              @foreach($expenseTypes as $expenseType)
                <option value="{{ $expenseType->id }}">{{ $expenseType->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-3">
            <label for="dateRangeFilter" class="form-label">{{ __('Date Range') }}</label>
            <input type="text" id="dateRangeFilter" class="form-control" placeholder="{{ __('Select date range') }}">
          </div>
        </div>
      </div>
    </div>

    {{-- Expenses Table --}}
    <div class="card">
      <div class="card-datatable table-responsive">
        <table class="datatables-expenses table">
          <thead>
            <tr>
              <th>{{ __('') }}</th>
              <th>{{ __('ID') }}</th>
              <th>{{ __('Employee') }}</th>
              <th>{{ __('Type') }}</th>
              <th>{{ __('Date') }}</th>
              <th>{{ __('Amount') }}</th>
              <th>{{ __('Status') }}</th>
              <th>{{ __('Attachments') }}</th>
              <th>{{ __('Actions') }}</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>

  @include('hrcore::expenses._modals')

  {{-- Page Data for JavaScript --}}
  <script>
    window.pageData = {
      urls: {
        datatable: @json(route('hrcore.expenses.datatable')),
        show: @json(route('hrcore.expenses.show', ':id')),
        edit: @json(route('hrcore.expenses.edit', ':id')),
        destroy: @json(route('hrcore.expenses.destroy', ':id')),
        approve: @json(route('hrcore.expenses.approve', ':id')),
        reject: @json(route('hrcore.expenses.reject', ':id')),
        process: @json(route('hrcore.expenses.process', ':id'))
      },
      permissions: {
        create: @json(auth()->user()->can('hrcore.create-expense')),
        edit: @json(auth()->user()->can('hrcore.edit-expense')),
        delete: @json(auth()->user()->can('hrcore.delete-expense')),
        approve: @json(auth()->user()->can('hrcore.approve-expense')),
        reject: @json(auth()->user()->can('hrcore.reject-expense')),
        process: @json(auth()->user()->can('hrcore.process-expense'))
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
        confirmDelete: @json(__('Are you sure you want to delete this expense request?')),
        confirmApprove: @json(__('Are you sure you want to approve this expense request?')),
        confirmReject: @json(__('Are you sure you want to reject this expense request?')),
        confirmProcess: @json(__('Are you sure you want to mark this expense as processed?')),
        success: @json(__('Success!')),
        error: @json(__('Error!')),
        approve: @json(__('Approve')),
        reject: @json(__('Reject')),
        process: @json(__('Process')),
        approved: @json(__('Approved')),
        rejected: @json(__('Rejected')),
        processed: @json(__('Processed')),
        yes: @json(__('Yes')),
        cancel: @json(__('Cancel'))
      }
    };
  </script>
@endsection


