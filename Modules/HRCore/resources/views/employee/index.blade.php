@extends('layouts.layoutMaster')

@section('title', __('Employees'))

<!-- Vendor Styles -->
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/@form-validation/form-validation.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
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
  @vite(['resources/assets/js/app/hrcore-employees.js'])
@endsection

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    {{-- Breadcrumb Component --}}
    <x-breadcrumb
      :title="__('Employees')"
      :breadcrumbs="[
        ['name' => __('Human Resources'), 'url' => ''],
        ['name' => __('Employees'), 'url' => '']
      ]"
      :home-url="url('/')"
    />

    {{-- Statistics Cards --}}
    <div class="row g-4 mb-4">
      <div class="col-sm-6 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
              <div class="avatar">
                <div class="avatar-initial bg-label-primary rounded">
                  <i class="bx bx-user bx-sm"></i>
                </div>
              </div>
              <div class="text-end">
                <h4 class="mb-0">{{ $active + $inactive + $relieved + $terminated }}</h4>
                <small class="text-muted">{{ __('Total Employees') }}</small>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
              <div class="avatar">
                <div class="avatar-initial bg-label-success rounded">
                  <i class="bx bx-user-check bx-sm"></i>
                </div>
              </div>
              <div class="text-end">
                <h4 class="mb-0">{{ $active }}</h4>
                <small class="text-muted">{{ __('Active') }}</small>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
              <div class="avatar">
                <div class="avatar-initial bg-label-warning rounded">
                  <i class="bx bx-user-x bx-sm"></i>
                </div>
              </div>
              <div class="text-end">
                <h4 class="mb-0">{{ $inactive }}</h4>
                <small class="text-muted">{{ __('Inactive') }}</small>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
              <div class="avatar">
                <div class="avatar-initial bg-label-danger rounded">
                  <i class="bx bx-user-minus bx-sm"></i>
                </div>
              </div>
              <div class="text-end">
                <h4 class="mb-0">{{ $relieved + $terminated }}</h4>
                <small class="text-muted">{{ __('Relieved/Terminated') }}</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Filters Card --}}
    <div class="card mb-4">
      <div class="card-body">
        <h5 class="card-title">{{ __('Filters') }}</h5>
        <div class="row g-3">
          {{-- Role Filter --}}
          <div class="col-md-3">
            <label for="roleFilter" class="form-label">{{ __('Filter by Role') }}</label>
            <select id="roleFilter" name="roleFilter" class="form-select select2">
              <option value="" selected>{{ __('All Roles') }}</option>
              @foreach($roles as $role)
                <option value="{{ $role->name }}">{{ $role->name }}</option>
              @endforeach
            </select>
          </div>

          {{-- Team Filter --}}
          <div class="col-md-3">
            <label for="teamFilter" class="form-label">{{ __('Filter by Team') }}</label>
            <select id="teamFilter" name="teamFilter" class="form-select select2">
              <option value="" selected>{{ __('All Teams') }}</option>
              @foreach($teams as $team)
                <option value="{{ $team->id }}">{{ $team->name }}</option>
              @endforeach
            </select>
          </div>

          {{-- Designation Filter --}}
          <div class="col-md-3">
            <label for="designationFilter" class="form-label">{{ __('Filter by Designation') }}</label>
            <select id="designationFilter" name="designationFilter" class="form-select select2">
              <option value="" selected>{{ __('All Designations') }}</option>
              @foreach($designations as $designation)
                <option value="{{ $designation->id }}">{{ $designation->name }}</option>
              @endforeach
            </select>
          </div>

          {{-- Status Filter --}}
          <div class="col-md-3">
            <label for="statusFilter" class="form-label">{{ __('Filter by Status') }}</label>
            <select id="statusFilter" name="statusFilter" class="form-select select2">
              <option value="" selected>{{ __('All Status') }}</option>
              <option value="active">{{ __('Active') }}</option>
              <option value="inactive">{{ __('Inactive') }}</option>
              <option value="relieved">{{ __('Relieved') }}</option>
              <option value="terminated">{{ __('Terminated') }}</option>
              <option value="retired">{{ __('Retired') }}</option>
              <option value="onboarding">{{ __('Onboarding') }}</option>
              <option value="probation">{{ __('Probation') }}</option>
              <option value="resigned">{{ __('Resigned') }}</option>
              <option value="suspended">{{ __('Suspended') }}</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    {{-- Employees Table --}}
    <div class="card">
      <div class="card-datatable table-responsive">
        <table id="employeesTable" class="table">
          <thead>
            <tr>
              <th>{{ __('ID') }}</th>
              <th>{{ __('Employee') }}</th>
              <th>{{ __('Role') }}</th>
              <th>{{ __('Team') }}</th>
              <th>{{ __('Designation') }}</th>
              <th>{{ __('Attendance Type') }}</th>
              <th>{{ __('Status') }}</th>
              <th>{{ __('Actions') }}</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>


  {{-- Page Data for JavaScript --}}
  <script>
    const pageData = {
      urls: {
        datatable: @json(route('hrcore.employees.datatable')),
        create: @json(route('hrcore.employees.create')),
        edit: @json(route('hrcore.employees.edit', ':id')),
        update: @json(route('hrcore.employees.update', ':id')),
        destroy: @json(route('hrcore.employees.destroy', ':id')),
        show: @json(route('hrcore.employees.show', ':id'))
      },
      permissions: {
        create: @json(auth()->user()->can('hrcore.create-employees')),
        edit: @json(auth()->user()->can('hrcore.edit-employees')),
        delete: @json(auth()->user()->can('hrcore.delete-employees'))
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
        confirmDelete: @json(__('Are you sure you want to delete this employee?')),
        deleteSuccess: @json(__('Employee deleted successfully')),
        updateSuccess: @json(__('Employee updated successfully')),
        error: @json(__('An error occurred. Please try again.')),
        viewProfile: @json(__('View Profile')),
        edit: @json(__('Edit')),
        delete: @json(__('Delete')),
        addEmployee: @json(__('Add Employee'))
      }
    };
  </script>
@endsection
