@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', __('Designations'))

<!-- Vendor Styles -->
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/@form-validation/form-validation.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
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
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
  ])
@endsection

@section('page-script')
  @vite(['resources/js/main-datatable.js'])
  @vite(['resources/js/main-helper.js'])
  @vite(['resources/assets/js/app/designation-index.js'])
@endsection

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    {{-- Breadcrumb Component --}}
    <x-breadcrumb
      :title="__('Designations')"
      :breadcrumbs="[
        ['name' => __('Human Resources'), 'url' => ''],
        ['name' => __('Designations'), 'url' => '']
      ]"
      :home-url="url('/')"
    />

    {{-- Designations Table --}}
    <div class="card">
      <div class="card-datatable table-responsive">
        <table class="datatables-designations table">
          <thead>
            <tr>
              <th>{{ __('') }}</th>
              <th>{{ __('ID') }}</th>
              <th>{{ __('Name') }}</th>
              <th>{{ __('Code') }}</th>
              <th>{{ __('Department') }}</th>
              <th>{{ __('Notes') }}</th>
              <th>{{ __('Status') }}</th>
              <th>{{ __('Actions') }}</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>

  @canany(['hrcore.create-designations', 'hrcore.edit-designations'])
    @include('hrcore::designation._designation_form')
  @endcanany

  {{-- Page Data for JavaScript --}}
  <script>
    const pageData = {
      urls: {
        datatable: @json(route('hrcore.designations.datatable')),
        store: @json(route('hrcore.designations.store')),
        show: @json(route('hrcore.designations.show', ':id')),
        update: @json(route('hrcore.designations.update', ':id')),
        destroy: @json(route('hrcore.designations.destroy', ':id')),
        toggleStatus: @json(route('hrcore.designations.toggle-status', ':id')),
        checkCode: @json(route('hrcore.designations.check-code')),
        departmentList: @json(route('hrcore.departments.list'))
      },
      permissions: {
        create: @json(auth()->user()->can('hrcore.create-designations')),
        edit: @json(auth()->user()->can('hrcore.edit-designations')),
        delete: @json(auth()->user()->can('hrcore.delete-designations'))
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
        confirmDelete: @json(__('Are you sure you want to delete this designation?')),
        deleteSuccess: @json(__('Designation deleted successfully')),
        createSuccess: @json(__('Designation created successfully')),
        updateSuccess: @json(__('Designation updated successfully')),
        statusChanged: @json(__('Designation status changed successfully')),
        error: @json(__('An error occurred. Please try again.')),
        addDesignation: @json(__('Add Designation')),
        editDesignation: @json(__('Edit Designation')),
        create: @json(__('Create')),
        update: @json(__('Update')),
        cancel: @json(__('Cancel')),
        nameRequired: @json(__('Name is required')),
        codeRequired: @json(__('Code is required')),
        codeTaken: @json(__('This code is already taken')),
        selectDepartment: @json(__('Select Department'))
      }
    };
  </script>
@endsection
