@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', __('Create Expense Request'))

<!-- Vendor Styles -->
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/@form-validation/form-validation.scss',
    'resources/assets/vendor/libs/animate-css/animate.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
    'resources/assets/vendor/libs/dropzone/dropzone.scss'
  ])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/@form-validation/popular.js',
    'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
    'resources/assets/vendor/libs/@form-validation/auto-focus.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
    'resources/assets/vendor/libs/dropzone/dropzone.js'
  ])
@endsection

@section('page-script')
  @vite(['resources/js/main-helper.js'])
  @vite(['Modules/HRCore/resources/assets/js/expense-create.js'])
@endsection

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    {{-- Breadcrumb Component --}}
    <x-breadcrumb
      :title="__('Create Expense Request')"
      :breadcrumbs="[
        ['name' => __('Human Resources'), 'url' => ''],
        ['name' => __('Expense Management'), 'url' => ''],
        ['name' => __('Expense Requests'), 'url' => route('hrcore.expenses.index')],
        ['name' => __('Create'), 'url' => '']
      ]"
      :home-url="url('/')"
    />

    <div class="row">
      <div class="col-xl-12">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __('Expense Request Details') }}</h5>
            <div class="card-action">
              <a href="{{ route('hrcore.expenses.index') }}" class="btn btn-label-secondary">
                <i class="bx bx-arrow-back me-1"></i> {{ __('Back to List') }}
              </a>
            </div>
          </div>
          
          <div class="card-body">
            <form id="expenseForm" enctype="multipart/form-data">
              @csrf
              
              <div class="row">
                <div class="col-md-6">
                  {{-- Basic Information --}}
                  <h6 class="mb-4">{{ __('Basic Information') }}</h6>
                  
                  <div class="mb-4">
                    <label class="form-label" for="expense_type_id">{{ __('Expense Type') }} <span class="text-danger">*</span></label>
                    <select class="form-select select2" id="expense_type_id" name="expense_type_id" required>
                      <option value="">{{ __('Select Expense Type') }}</option>
                      @foreach($expenseTypes as $expenseType)
                        <option value="{{ $expenseType->id }}" 
                          data-max-amount="{{ $expenseType->max_amount }}"
                          data-default-amount="{{ $expenseType->default_amount }}"
                          data-requires-receipt="{{ $expenseType->requires_receipt }}">
                          {{ $expenseType->name }} 
                          @if($expenseType->max_amount)
                            (Max: ${{ number_format($expenseType->max_amount, 2) }})
                          @endif
                        </option>
                      @endforeach
                    </select>
                    <small class="text-muted" id="expenseTypeHelp"></small>
                  </div>

                  <div class="mb-4">
                    <label class="form-label" for="title">{{ __('Title') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="title" name="title" 
                      placeholder="{{ __('Enter expense title') }}" required>
                  </div>

                  <div class="mb-4">
                    <label class="form-label" for="expense_date">{{ __('Expense Date') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control flatpickr-date" id="expense_date" name="expense_date" 
                      placeholder="{{ __('Select date') }}" required>
                  </div>

                  <div class="mb-4">
                    <label class="form-label" for="amount">{{ __('Amount') }} <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <span class="input-group-text">$</span>
                      <input type="number" class="form-control" id="amount" name="amount" 
                        step="0.01" min="0.01" placeholder="0.00" required>
                    </div>
                    <small class="text-muted" id="amountHelp"></small>
                  </div>

                  <div class="mb-4">
                    <label class="form-label" for="description">{{ __('Description') }}</label>
                    <textarea class="form-control" id="description" name="description" rows="4"
                      placeholder="{{ __('Enter detailed description of the expense') }}"></textarea>
                  </div>
                </div>

                <div class="col-md-6">
                  {{-- Additional Information --}}
                  <h6 class="mb-4">{{ __('Additional Information') }}</h6>

                  <div class="mb-4">
                    <label class="form-label" for="department_id">{{ __('Department') }}</label>
                    <select class="form-select select2" id="department_id" name="department_id">
                      <option value="">{{ __('Select Department') }}</option>
                      @foreach($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="mb-4">
                    <label class="form-label" for="project_code">{{ __('Project Code') }}</label>
                    <input type="text" class="form-control" id="project_code" name="project_code" 
                      placeholder="{{ __('Enter project code (if applicable)') }}">
                  </div>

                  <div class="mb-4">
                    <label class="form-label" for="cost_center">{{ __('Cost Center') }}</label>
                    <input type="text" class="form-control" id="cost_center" name="cost_center" 
                      placeholder="{{ __('Enter cost center (if applicable)') }}">
                  </div>

                  {{-- Attachments --}}
                  <h6 class="mb-4">{{ __('Attachments') }} <span class="text-danger" id="required-indicator" style="display: none;">*</span></h6>
                  <div class="mb-4">
                    <div class="dropzone needsclick" id="dropzone-attachments">
                      <div class="dz-message needsclick">
                        <i class="bx bx-upload display-4"></i>
                        <h5>{{ __('Drop files here or click to upload') }}</h5>
                        <small class="text-muted">
                          {{ __('Supported formats: JPG, PNG, PDF, DOC, DOCX. Max file size: 10MB') }}
                        </small>
                      </div>
                    </div>
                    <small class="text-muted" id="attachmentHelp">
                      {{ __('Upload receipts, invoices, or other supporting documents') }}
                    </small>
                  </div>
                </div>
              </div>

              {{-- Form Actions --}}
              <div class="row">
                <div class="col-12">
                  <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('hrcore.expenses.index') }}" class="btn btn-label-secondary">
                      {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="btn btn-primary">
                      <i class="bx bx-check me-1"></i>
                      {{ __('Submit Request') }}
                    </button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Page Data for JavaScript --}}
  <script>
    window.pageData = {
      urls: {
        store: @json(route('hrcore.expenses.store')),
        index: @json(route('hrcore.expenses.index'))
      },
      labels: {
        success: @json(__('Success!')),
        error: @json(__('Error!')),
        created: @json(__('Expense request created successfully')),
        maxAmountExceeded: @json(__('Amount exceeds maximum limit')),
        receiptRequired: @json(__('Receipt is required for this expense type')),
        uploading: @json(__('Uploading...')),
        submitting: @json(__('Submitting...')),
        selectPlaceholder: @json(__('Select...')),
        removeFile: @json(__('Remove')),
        maxAmountPrefix: @json(__('Maximum amount: $')),
        receiptRequiredText: @json(__('Receipt/attachment is required for this expense type')),
        uploadDocumentsText: @json(__('Upload receipts, invoices, or other supporting documents'))
      }
    };
  </script>
@endsection