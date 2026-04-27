@extends('layouts.layoutMaster')

@section('title', __('My Expenses'))

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js'
])
@endsection

@section('content')

<x-breadcrumb
  :title="__('My Expenses')"
  :breadcrumbs="[
    ['name' => __('Human Resources'), 'url' => route('hrcore.dashboard')],
    ['name' => __('Expense Management'), 'url' => ''],
    ['name' => __('My Expenses'), 'url' => '']
  ]"
  :home-url="url('/')"
/>

<div class="row mb-4">
  <!-- Statistics Cards -->
  <div class="col-md col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text text-muted">{{ __('Total Expenses') }}</p>
            <div class="d-flex align-items-end mb-2">
              <h4 class="card-title mb-0 me-2">{{ $statistics['total'] }}</h4>
            </div>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-primary rounded p-2">
              <i class="bx bx-receipt bx-sm"></i>
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
              <h4 class="card-title mb-0 me-2 text-warning">{{ $statistics['pending'] }}</h4>
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
              <h4 class="card-title mb-0 me-2 text-success">{{ $statistics['approved'] }}</h4>
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
              <h4 class="card-title mb-0 me-2 text-danger">{{ $statistics['rejected'] }}</h4>
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

  <div class="col-md col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text text-muted">{{ __('Processed') }}</p>
            <div class="d-flex align-items-end mb-2">
              <h4 class="card-title mb-0 me-2 text-info">{{ $statistics['processed'] }}</h4>
            </div>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-info rounded p-2">
              <i class="bx bx-dollar-circle bx-sm"></i>
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
      <h5 class="mb-0">{{ __('My Expense Requests') }}</h5>
      @can('hrcore.create-expense')
        <button type="button" class="btn btn-primary" onclick="createExpense()">
          <i class="bx bx-plus me-1"></i> {{ __('New Expense Request') }}
        </button>
      @endcan
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
          <option value="processed">{{ __('Processed') }}</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">{{ __('Expense Type') }}</label>
        <select id="filterExpenseType" class="form-select">
          <option value="">{{ __('All Types') }}</option>
          @foreach($expenseTypes as $type)
            <option value="{{ $type->id }}">{{ $type->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">{{ __('Date From') }}</label>
        <input type="text" id="filterDateFrom" class="form-control" placeholder="YYYY-MM-DD">
      </div>
      <div class="col-md-3">
        <label class="form-label">{{ __('Date To') }}</label>
        <input type="text" id="filterDateTo" class="form-control" placeholder="YYYY-MM-DD">
      </div>
    </div>

    <!-- DataTable -->
    <div class="table-responsive">
      <table id="expensesTable" class="table table-bordered">
        <thead>
          <tr>
            <th>{{ __('Date') }}</th>
            <th>{{ __('Type') }}</th>
            <th>{{ __('Description') }}</th>
            <th>{{ __('Amount') }}</th>
            <th>{{ __('Status') }}</th>
            <th>{{ __('Attachments') }}</th>
            <th>{{ __('Actions') }}</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>

<!-- Form Offcanvas -->
@include('hrcore::expenses._modals')

<!-- Expense Details Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="expenseDetailsOffcanvas" aria-labelledby="expenseDetailsOffcanvasLabel">
  <div class="offcanvas-header">
    <h5 id="expenseDetailsOffcanvasLabel" class="offcanvas-title">{{ __('Expense Request Details') }}</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body" id="expenseDetailsContent">
    <!-- Dynamic content will be loaded here -->
  </div>
</div>

@endsection

@section('page-script')
<script>
window.pageData = {
  urls: {
    datatable: @json(route('hrcore.my.expenses.datatable')),
    create: @json(route('hrcore.my.expenses.create')),
    store: @json(route('hrcore.my.expenses.store')),
    edit: @json(route('hrcore.my.expenses.edit', ['id' => '__ID__'])),
    update: @json(route('hrcore.my.expenses.update', ['id' => '__ID__'])),
    destroy: @json(route('hrcore.my.expenses.delete', ['id' => '__ID__'])),
    show: @json(route('hrcore.my.expenses.show', ['id' => '__ID__']))
  },
  labels: {
    confirmDelete: @json(__('Are you sure you want to delete this expense request?')),
    deleteTitle: @json(__('Delete Expense Request')),
    deleteButton: @json(__('Yes, Delete')),
    cancelButton: @json(__('Cancel')),
    success: @json(__('Success')),
    error: @json(__('Error')),
    deleted: @json(__('Expense request deleted successfully')),
    saved: @json(__('Expense request saved successfully')),
    updated: @json(__('Expense request updated successfully'))
  }
};
</script>
@vite('Modules/HRCore/resources/assets/js/my-expenses.js')
@endsection
