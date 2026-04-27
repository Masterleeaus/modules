@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', __('Departments'))

<!-- Vendor Styles -->
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
    'resources/assets/vendor/libs/select2/select2.scss'
  ])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
    'resources/assets/vendor/libs/select2/select2.js'
  ])
@endsection

@section('page-script')
  <script>
    const pageData = {
      urls: {
        datatable: @json(route('hrcore.departments.datatable')),
        store: @json(route('hrcore.departments.addOrUpdateDepartmentAjax')),
        edit: @json(route('hrcore.departments.getDepartmentAjax', ':id')),
        delete: @json(route('hrcore.departments.deleteAjax', ':id')),
        changeStatus: @json(route('hrcore.departments.changeStatus', ':id')),
        parentList: @json(route('hrcore.departments.parent-list'))
      },
      labels: {
        addDepartment: @json(__('Add Department')),
        editDepartment: @json(__('Edit Department')),
        selectParent: @json(__('Select parent department')),
        nameRequired: @json(__('Department name is required')),
        codeRequired: @json(__('Department code is required')),
        codeLength: @json(__('Code must be between 3 and 10 characters')),
        codeUnique: @json(__('Department code must be unique')),
        confirmDelete: @json(__('Are you sure?')),
        deleteWarning: @json(__('You won\'t be able to revert this!')),
        yesDelete: @json(__('Yes, delete it!')),
        cancel: @json(__('Cancel')),
        success: @json(__('Success')),
        error: @json(__('Error')),
        createSuccess: @json(__('Department created successfully!')),
        updateSuccess: @json(__('Department updated successfully!')),
        deleteSuccess: @json(__('Department deleted successfully!')),
        statusUpdated: @json(__('Department status updated successfully!'))
      },
      permissions: {
        create: @json(auth()->user()->can('hrcore.create-departments')),
        edit: @json(auth()->user()->can('hrcore.edit-departments')),
        delete: @json(auth()->user()->can('hrcore.delete-departments'))
      }
    };
  </script>
  @vite(['resources/js/main-datatable.js'])
  @vite(['resources/js/main-helper.js'])
  @vite(['resources/assets/js/app/department-index.js'])
@endsection

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    {{-- Breadcrumb Component --}}
    <x-breadcrumb
      :title="__('Departments')"
      :breadcrumbs="[
        ['name' => __('Human Resources'), 'url' => ''],
        ['name' => __('Departments'), 'url' => '']
      ]"
      :home-url="url('/')"
    />

    <!-- Departments List -->
    <div class="card">
      <div class="card-datatable table-responsive">
        <table class="datatables-departments table border-top">
          <thead>
            <tr>
              <th>{{ __('') }}</th>
              <th>{{ __('Id') }}</th>
              <th>{{ __('Name') }}</th>
              <th>{{ __('Code') }}</th>
              <th>{{ __('Parent Department') }}</th>
              <th>{{ __('Description') }}</th>
              <th>{{ __('Status') }}</th>
              <th>{{ __('Actions') }}</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>

    {{-- Department Form --}}
    @include('hrcore::departments._department_form')
  </div>
@endsection
