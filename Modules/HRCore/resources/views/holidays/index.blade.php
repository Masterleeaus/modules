@php
  $configData = Helper::appClasses();
  $currentYear = date('Y');
  $years = range($currentYear - 2, $currentYear + 2);
@endphp

@extends('layouts/layoutMaster')

@section('title', __('Holidays'))

<!-- Vendor Styles -->
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/@form-validation/form-validation.scss',
    'resources/assets/vendor/libs/animate-css/animate.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
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
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
    'resources/assets/vendor/libs/select2/select2.js'
  ])
@endsection

@section('page-script')
    @vite(['Modules/HRCore/resources/assets/js/app/hrcore-holidays.js'])
    
    <script>
        const pageData = {
            urls: {
                index: @json(route('hrcore.holidays.index')),
                datatable: @json(route('hrcore.holidays.datatable')),
                store: @json(route('hrcore.holidays.store')),
                show: @json(route('hrcore.holidays.show', ':id')),
                update: @json(route('hrcore.holidays.update', ':id')),
                destroy: @json(route('hrcore.holidays.destroy', ':id')),
                toggleStatus: @json(route('hrcore.holidays.toggle-status', ':id')),
                create: @json(route('hrcore.holidays.create')),
                edit: @json(route('hrcore.holidays.edit', ':id')),
            },
            permissions: {
                create: @json(auth()->user()->can('hrcore.create-holidays')),
                edit: @json(auth()->user()->can('hrcore.edit-holidays')),
                delete: @json(auth()->user()->can('hrcore.delete-holidays')),
            },
            labels: {
                confirmDelete: @json(__('Are you sure you want to delete this holiday?')),
                confirmStatusChange: @json(__('Are you sure you want to change the status of this holiday?')),
                success: @json(__('Success!')),
                error: @json(__('Error!')),
            }
        };
    </script>
@endsection

@section('content')
<x-breadcrumb :title="__('Holidays')" :breadcrumbs="[
    ['name' => __('Home'), 'url' => route('dashboard')],
    ['name' => __('HR Core'), 'url' => '#'],
    ['name' => __('Holidays'), 'url' => route('hrcore.holidays.index')]
]" />

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Filters Card -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">{{ __('Filters') }}</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">{{ __('Year') }}</label>
                <select class="form-select" id="filter-year">
                    <option value="">{{ __('All Years') }}</option>
                    @foreach($years as $year)
                        <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('Type') }}</label>
                <select class="form-select" id="filter-type">
                    <option value="">{{ __('All Types') }}</option>
                    <option value="public">{{ __('Public') }}</option>
                    <option value="religious">{{ __('Religious') }}</option>
                    <option value="regional">{{ __('Regional') }}</option>
                    <option value="optional">{{ __('Optional') }}</option>
                    <option value="company">{{ __('Company') }}</option>
                    <option value="special">{{ __('Special') }}</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('Status') }}</label>
                <select class="form-select" id="filter-status">
                    <option value="">{{ __('All') }}</option>
                    <option value="1">{{ __('Active') }}</option>
                    <option value="0">{{ __('Inactive') }}</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="button" class="btn btn-secondary me-2" onclick="resetFilters()">
                    <i class="bx bx-reset"></i> {{ __('Reset') }}
                </button>
                <button type="button" class="btn btn-primary" onclick="applyFilters()">
                    <i class="bx bx-search"></i> {{ __('Search') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Data Table Card -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">{{ __('Holidays Management') }}</h5>
        @can('hrcore.create-holidays')
        <a href="{{ route('hrcore.holidays.create') }}" class="btn btn-primary">
            <i class="bx bx-plus"></i> {{ __('Add Holiday') }}
        </a>
        @endcan
    </div>

    <div class="card-datatable table-responsive">
        <table class="datatables-basic table table-bordered" id="holidays-table">
            <thead>
                <tr>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('Applicable To') }}</th>
                    <th>{{ __('Tags') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

@endsection