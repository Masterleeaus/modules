@php
    $configData = Helper::appClasses();
@endphp

@extends('layouts.layoutMaster')

@section('title', __('Teams'))

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
    @vite(['resources/assets/js/app/hrcore-teams.js'])

    <script>
        const pageData = {
            urls: {
                index: @json(route('hrcore.teams.index')),
                datatable: @json(route('hrcore.teams.datatable')),
                store: @json(route('hrcore.teams.store')),
                show: @json(route('hrcore.teams.show', ':id')),
                update: @json(route('hrcore.teams.update', ':id')),
                destroy: @json(route('hrcore.teams.destroy', ':id')),
                toggleStatus: @json(route('hrcore.teams.toggle-status', ':id')),
                checkCode: @json(route('hrcore.teams.check-code')),
                list: @json(route('hrcore.teams.list')),
            },
            permissions: {
                create: @json(auth()->user()->can('hrcore.create-teams')),
                edit: @json(auth()->user()->can('hrcore.edit-teams')),
                delete: @json(auth()->user()->can('hrcore.delete-teams')),
            },
            labels: {
                team: @json(__('Team')),
                teams: @json(__('Teams')),
                addTeam: @json(__('Add Team')),
                editTeam: @json(__('Edit Team')),
                createTeam: @json(__('Create Team')),
                updateTeam: @json(__('Update Team')),
                deleteTeam: @json(__('Delete Team')),
                confirmDelete: @json(__('Are you sure you want to delete this team?')),
                deleteSuccess: @json(__('Team deleted successfully!')),
                createSuccess: @json(__('Team created successfully!')),
                updateSuccess: @json(__('Team updated successfully!')),
                statusUpdated: @json(__('Team status updated successfully!')),
                activate: @json(__('Activate')),
                deactivate: @json(__('Deactivate')),
                submit: @json(__('Submit')),
                cancel: @json(__('Cancel')),
                error: @json(__('An error occurred. Please try again.')),
            }
        };
    </script>
@endsection


@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    {{-- Breadcrumb Component --}}
    <x-breadcrumb
      :title="__('Teams')"
      :breadcrumbs="[
        ['name' => __('Organization'), 'url' => ''],
        ['name' => __('Teams'), 'url' => '']
      ]"
      :home-url="url('/')"
    />

    {{-- Teams Table --}}
    <div class="card">
        <div class="card-datatable table-responsive">
            <table class="datatables-teams table border-top">
                <thead>
                <tr>
                    <th></th>
                    <th>@lang('ID')</th>
                    <th>@lang('Name')</th>
                    <th>@lang('Code')</th>
                    <th>@lang('Team Head')</th>
                    <th>@lang('Status')</th>
                    <th>@lang('Actions')</th>
                </tr>
                </thead>
            </table>
        </div>

    </div>
    @include('hrcore::teams._add_or_update_team')
  </div>
@endsection
