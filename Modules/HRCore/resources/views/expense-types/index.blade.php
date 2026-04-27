@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', __('Expense Types'))

<!-- Vendor Styles -->
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
    'resources/assets/vendor/libs/@form-validation/form-validation.scss',
    'resources/assets/vendor/libs/animate-css/animate.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
    'resources/assets/vendor/libs/select2/select2.scss'
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
    'resources/assets/vendor/libs/select2/select2.js'
  ])
@endsection

@section('page-script')
  @vite(['resources/js/main-datatable.js'])
  @vite(['resources/js/main-helper.js'])

  <script>
    // Pass data to JavaScript
    window.pageData = {
      urls: {
        datatable: '{{ route('hrcore.expense-types.datatable') }}',
        store: '{{ route('hrcore.expense-types.store') }}',
        update: '{{ route('hrcore.expense-types.update', ':id') }}',
        show: '{{ route('hrcore.expense-types.show', ':id') }}',
        destroy: '{{ route('hrcore.expense-types.destroy', ':id') }}',
        checkCode: '{{ route('hrcore.expense-types.check-code') }}',
        toggleStatus: '{{ route('hrcore.expense-types.toggle-status', ':id') }}'
      },
      labels: {
        confirmDelete: '{{ __('Are you sure?') }}',
        deleteText: '{{ __('This action cannot be undone') }}',
        confirmButton: '{{ __('Yes, delete it!') }}',
        cancelButton: '{{ __('Cancel') }}',
        success: '{{ __('Success!') }}',
        error: '{{ __('Error!') }}',
        validation: '{{ __('Validation Error') }}',
        somethingWrong: '{{ __('Something went wrong') }}',
        addExpenseType: '{{ __('Add Expense Type') }}',
        editExpenseType: '{{ __('Edit Expense Type') }}',
        create: '{{ __('Create') }}',
        update: '{{ __('Update') }}',
        deleted: '{{ __('Deleted!') }}',
        wontRevert: '{{ __("You won't be able to revert this!") }}',
        yesDeleteIt: '{{ __('Yes, delete it!') }}'
      },
      permissions: {
        create: {{ auth()->user()->can('hrcore.manage-expense-types') ? 'true' : 'false' }},
        edit: {{ auth()->user()->can('hrcore.manage-expense-types') ? 'true' : 'false' }},
        delete: {{ auth()->user()->can('hrcore.manage-expense-types') ? 'true' : 'false' }}
      },
      categories: @json(\Modules\HRCore\app\Models\ExpenseType::getCategories())
    };
  </script>
  @vite(['Modules/HRCore/resources/assets/js/expense-types.js'])
@endsection

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    {{-- Breadcrumb Component --}}
    <x-breadcrumb
      :title="__('Expense Types')"
      :breadcrumbs="[
        ['name' => __('Human Resources'), 'url' => ''],
        ['name' => __('Expense Management'), 'url' => ''],
        ['name' => __('Expense Types'), 'url' => '']
      ]"
      :home-url="url('/')"
    />

    {{-- Page Header --}}
    <div class="row mb-4">
      <div class="col">
        <p class="text-muted mb-0">{{ __('Manage expense type categories and settings') }}</p>
      </div>
      <div class="col-auto">
        @can('hrcore.manage-expense-types')
          <button type="button" class="btn btn-primary add-new" data-bs-toggle="offcanvas"
            data-bs-target="#formOffcanvas">
            <i class="bx bx-plus me-0 me-sm-2"></i>
            <span class="d-none d-sm-inline-block">{{ __('Add Expense Type') }}</span>
          </button>
        @endcan
      </div>
    </div>

    {{-- Expense Types Table --}}
    <div class="card">
      <div class="card-datatable table-responsive">
        <table class="datatables-expenseTypes table">
          <thead>
            <tr>
              <th>{{ __('') }}</th>
              <th>{{ __('ID') }}</th>
              <th>{{ __('Name') }}</th>
              <th>{{ __('Code') }}</th>
              <th>{{ __('Category') }}</th>
              <th>{{ __('Max Amount') }}</th>
              <th>{{ __('Receipt Required') }}</th>
              <th>{{ __('Status') }}</th>
              <th>{{ __('Actions') }}</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>

  @include('hrcore::expense-types.form')
@endsection
