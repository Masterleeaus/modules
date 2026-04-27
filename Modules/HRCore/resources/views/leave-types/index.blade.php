@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', __('Leave Types'))

<!-- Vendor Styles -->
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
    'resources/assets/vendor/libs/@form-validation/form-validation.scss',
    'resources/assets/vendor/libs/animate-css/animate.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
  ])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
    'resources/assets/vendor/libs/@form-validation/popular.js',
    'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
    'resources/assets/vendor/libs/@form-validation/auto-focus.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
  ])
@endsection

@section('page-script')
  @vite(['resources/js/main-datatable.js'])
  @vite(['resources/js/main-helper.js'])
  @vite(['resources/assets/js/app/leave-type-index.js'])
@endsection

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    {{-- Breadcrumb Component --}}
    <x-breadcrumb
      :title="__('Leave Types')"
      :breadcrumbs="[
        ['name' => __('Human Resources'), 'url' => ''],
        ['name' => __('Leave Types'), 'url' => '']
      ]"
      :home-url="url('/')"
    >
      @can('hrcore.create-leave-types')
        <button type="button" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddOrUpdateLeaveType">
          <i class="bx bx-plus me-1"></i>{{ __('Add Leave Type') }}
        </button>
      @endcan
    </x-breadcrumb>

    {{-- Leave Types Table --}}
    <div class="card">
      <div class="card-datatable table-responsive">
        <table class="datatables-leaveTypes table">
          <thead>
            <tr>
              <th>{{ __('') }}</th>
              <th>{{ __('ID') }}</th>
              <th>{{ __('Name') }}</th>
              <th>{{ __('Code') }}</th>
              <th>{{ __('Description') }}</th>
              <th>{{ __('Proof Required') }}</th>
              <th>{{ __('Status') }}</th>
              <th>{{ __('Actions') }}</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>

  @include('hrcore::leave-types._add_or_update_leave_type')
  @include('hrcore::leave-types._view_leave_type')

  {{-- Page Data for JavaScript --}}
  <script>
    const pageData = {
      urls: {
        datatable: @json(route('hrcore.leave-types.datatable')),
        store: @json(route('hrcore.leave-types.store')),
        show: @json(route('hrcore.leave-types.show', ':id')),
        edit: @json(route('hrcore.leave-types.edit', ':id')),
        update: @json(route('hrcore.leave-types.update', ':id')),
        destroy: @json(route('hrcore.leave-types.destroy', ':id')),
        toggleStatus: @json(route('hrcore.leave-types.toggle-status', ':id')),
        checkCode: @json(route('hrcore.leave-types.check-code'))
      },
      permissions: {
        create: @json(auth()->user()->can('hrcore.create-leave-types')),
        edit: @json(auth()->user()->can('hrcore.edit-leave-types')),
        delete: @json(auth()->user()->can('hrcore.delete-leave-types'))
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
        confirmDelete: @json(__('Are you sure you want to delete this leave type?')),
        deleteSuccess: @json(__('Leave type deleted successfully')),
        createSuccess: @json(__('Leave type created successfully')),
        updateSuccess: @json(__('Leave type updated successfully')),
        success: @json(__('Success!')),
        error: @json(__('An error occurred. Please try again.')),
        edit: @json(__('Edit')),
        delete: @json(__('Delete')),
        validationRequired: @json(__('This field is required')),
        codeRequired: @json(__('The code is required')),
        nameRequired: @json(__('The name is required')),
        codeTaken: @json(__('The code is already taken')),
        addLeaveType: @json(__('Add Leave Type')),
        editLeaveType: @json(__('Edit Leave Type')),
        create: @json(__('Create')),
        update: @json(__('Update')),
        deleted: @json(__('Deleted!')),
        wontRevert: @json(__("You won't be able to revert this!")),
        yesDeleteIt: @json(__('Yes, delete it!'))
      }
    };
  </script>
@endsection
