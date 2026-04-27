@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', __('Shifts'))

<!-- Vendor Styles -->
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/@form-validation/form-validation.scss',
    'resources/assets/vendor/libs/animate-css/animate.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
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
    'resources/assets/vendor/libs/flatpickr/flatpickr.js'
  ])
@endsection

@section('page-script')
    @vite(['resources/js/main-datatable.js'])
    @vite(['resources/js/main-helper.js'])
    @vite(['resources/assets/js/app/hrcore-shifts.js'])

    <script>
        const pageData = {
            urls: {
                index: @json(route('hrcore.shifts.index')),
                datatable: @json(route('hrcore.shifts.datatable')),
                store: @json(route('hrcore.shifts.store')),
                show: @json(route('hrcore.shifts.show', ':id')),
                update: @json(route('hrcore.shifts.update', ':id')),
                destroy: @json(route('hrcore.shifts.destroy', ':id')),
                toggleStatus: @json(route('hrcore.shifts.toggle-status', ':id')),
                list: @json(route('hrcore.shifts.active-list')),
            },
            permissions: {
                create: @json(auth()->user()->can('hrcore.create-shifts')),
                edit: @json(auth()->user()->can('hrcore.edit-shifts')),
                delete: @json(auth()->user()->can('hrcore.delete-shifts')),
            },
            labels: {
                shift: @json(__('Shift')),
                shifts: @json(__('Shifts')),
                addShift: @json(__('Add Shift')),
                editShift: @json(__('Edit Shift')),
                createShift: @json(__('Create Shift')),
                updateShift: @json(__('Update Shift')),
                deleteShift: @json(__('Delete Shift')),
                confirmDelete: @json(__('Are you sure you want to delete this shift?')),
                deleteSuccess: @json(__('Shift deleted successfully!')),
                createSuccess: @json(__('Shift created successfully!')),
                updateSuccess: @json(__('Shift updated successfully!')),
                statusUpdated: @json(__('Shift status updated successfully!')),
                activate: @json(__('Activate')),
                deactivate: @json(__('Deactivate')),
                submit: @json(__('Submit')),
                cancel: @json(__('Cancel')),
                error: @json(__('An error occurred. Please try again.')),
                cannotDeleteAssigned: @json(__('Cannot delete shift that is assigned to users.')),
            }
        };
    </script>
@endsection

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    {{-- Breadcrumb Component --}}
    <x-breadcrumb
      :title="__('Shifts')"
      :breadcrumbs="[
        ['name' => __('Human Resources'), 'url' => ''],
        ['name' => __('Shifts'), 'url' => '']
      ]"
      :home-url="url('/')"
    />

    {{-- Shifts Table --}}
    <div class="card">
        <div class="card-datatable table-responsive">
            <table class="datatables-shifts table border-top">
                <thead>
                <tr>
                    <th></th>
                    <th>@lang('ID')</th>
                    <th>@lang('Name')</th>
                    <th>@lang('Code')</th>
                    <th>@lang('Shift Time')</th>
                    <th>@lang('Working Days')</th>
                    <th>@lang('Status')</th>
                    <th>@lang('Actions')</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
    @include('hrcore::shift._add_or_update_shift')
  </div>
@endsection