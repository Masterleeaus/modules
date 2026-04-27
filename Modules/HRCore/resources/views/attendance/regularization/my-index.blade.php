@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', __('My Attendance Regularization'))

@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/dropzone/dropzone.scss'
  ])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/dropzone/dropzone.js'
  ])
@endsection

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    {{-- Breadcrumb --}}
    <x-breadcrumb
      :title="__('My Attendance Regularization')"
      :breadcrumbs="[
        ['name' => __('Self Service'), 'url' => ''],
        ['name' => __('My Regularization'), 'url' => '']
      ]"
    />

    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">{{ __('My Regularization Requests') }}</h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#addRegularizationOffcanvas">
          <i class="bx bx-plus me-1"></i> {{ __('New Request') }}
        </button>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover" id="myRegularizationTable">
            <thead>
              <tr>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Type') }}</th>
                <th>{{ __('Requested Times') }}</th>
                <th>{{ __('Reason') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Approved By') }}</th>
                <th>{{ __('Actions') }}</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>

  {{-- Add Regularization Offcanvas --}}
  <div class="offcanvas offcanvas-end" tabindex="-1" id="addRegularizationOffcanvas">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="regularizationOffcanvasTitle">{{ __('New Regularization Request') }}</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
      <form id="regularizationForm" enctype="multipart/form-data">
        @csrf
        
        <div class="mb-3">
          <label class="form-label">{{ __('Date') }} <span class="text-danger">*</span></label>
          <input type="date" class="form-control" name="date" max="{{ date('Y-m-d') }}" required>
        </div>

        <div class="mb-3">
          <label class="form-label">{{ __('Type') }} <span class="text-danger">*</span></label>
          <select class="form-select" name="type" required>
            <option value="">{{ __('Select Type') }}</option>
            <option value="missing_checkin">{{ __('Missing Check-in') }}</option>
            <option value="missing_checkout">{{ __('Missing Check-out') }}</option>
            <option value="wrong_time">{{ __('Wrong Time') }}</option>
            <option value="forgot_punch">{{ __('Forgot to Punch') }}</option>
            <option value="other">{{ __('Other') }}</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">{{ __('Requested Check-in Time') }}</label>
          <input type="time" class="form-control" name="requested_check_in_time">
        </div>

        <div class="mb-3">
          <label class="form-label">{{ __('Requested Check-out Time') }}</label>
          <input type="time" class="form-control" name="requested_check_out_time">
        </div>

        <div class="mb-3">
          <label class="form-label">{{ __('Reason') }} <span class="text-danger">*</span></label>
          <textarea class="form-control" name="reason" rows="3" required></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">{{ __('Attachments') }}</label>
          <input type="file" class="form-control" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png">
          <small class="text-muted">{{ __('Accepted formats: PDF, JPG, PNG. Max 5MB per file.') }}</small>
        </div>

        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary flex-fill">{{ __('Submit Request') }}</button>
          <button type="button" class="btn btn-label-secondary flex-fill" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
        </div>
      </form>
    </div>
  </div>

  {{-- View Details Offcanvas --}}
  <div class="offcanvas offcanvas-end" tabindex="-1" id="viewRegularizationOffcanvas">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title">{{ __('Regularization Details') }}</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body" id="regularizationDetailsContent">
      <!-- Details will be loaded here dynamically -->
    </div>
  </div>
@endsection

@section('page-script')
<script>
window.pageData = {
  urls: {
    datatable: @json(route('hrcore.my.attendance.regularization.datatable')),
    store: @json(route('hrcore.my.attendance.regularization.store')),
    show: @json(route('hrcore.my.attendance.regularization.show', ':id')),
    edit: @json(route('hrcore.my.attendance.regularization.edit', ':id')),
    update: @json(route('hrcore.my.attendance.regularization.update', ':id')),
    delete: @json(route('hrcore.my.attendance.regularization.delete', ':id'))
  },
  labels: {
    search: @json(__('Search...')),
    success: @json(__('Success')),
    error: @json(__('Error')),
    errorOccurred: @json(__('An error occurred')),
    areYouSure: @json(__('Are you sure?')),
    deleteWarning: @json(__("You won't be able to revert this!")),
    yesDelete: @json(__('Yes, delete it!')),
    cancel: @json(__('Cancel')),
    deleted: @json(__('Deleted!')),
    failedToDelete: @json(__('Failed to delete the request')),
    failedToLoadDetails: @json(__('Failed to load regularization details')),
    failedToLoadData: @json(__('Failed to load data'))
  }
};
</script>
@vite('Modules/HRCore/resources/assets/js/my-regularization.js')
@endsection