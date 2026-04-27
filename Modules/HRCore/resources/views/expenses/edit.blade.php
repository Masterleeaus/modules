@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', __('Edit Expense Request'))

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
  @vite(['Modules/HRCore/resources/assets/js/expense-edit.js'])
@endsection

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    {{-- Breadcrumb Component --}}
    <x-breadcrumb
      :title="__('Edit Expense Request')"
      :breadcrumbs="[
        ['name' => __('Human Resources'), 'url' => ''],
        ['name' => __('Expense Management'), 'url' => ''],
        ['name' => __('Expense Requests'), 'url' => route('hrcore.expenses.index')],
        ['name' => __('Edit'), 'url' => '']
      ]"
      :home-url="url('/')"
    />

    <div class="row">
      <div class="col-xl-12">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __('Edit Expense Request') }} #{{ $expense->id }}</h5>
            <div class="card-action">
              <a href="{{ route('hrcore.expenses.index') }}" class="btn btn-label-secondary">
                <i class="bx bx-arrow-back me-1"></i> {{ __('Back to List') }}
              </a>
            </div>
          </div>
          
          <div class="card-body">
            <form id="expenseForm" enctype="multipart/form-data">
              @csrf
              @method('PUT')
              
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
                          data-requires-receipt="{{ $expenseType->requires_receipt }}"
                          {{ $expense->expense_type_id == $expenseType->id ? 'selected' : '' }}>
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
                      value="{{ $expense->title }}"
                      placeholder="{{ __('Enter expense title') }}" required>
                  </div>

                  <div class="mb-4">
                    <label class="form-label" for="expense_date">{{ __('Expense Date') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control flatpickr-date" id="expense_date" name="expense_date" 
                      value="{{ $expense->expense_date->format('Y-m-d') }}"
                      placeholder="{{ __('Select date') }}" required>
                  </div>

                  <div class="mb-4">
                    <label class="form-label" for="amount">{{ __('Amount') }} <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <span class="input-group-text">$</span>
                      <input type="number" class="form-control" id="amount" name="amount" 
                        value="{{ $expense->amount }}"
                        step="0.01" min="0.01" placeholder="0.00" required>
                    </div>
                    <small class="text-muted" id="amountHelp"></small>
                  </div>

                  <div class="mb-4">
                    <label class="form-label" for="description">{{ __('Description') }}</label>
                    <textarea class="form-control" id="description" name="description" rows="4"
                      placeholder="{{ __('Enter detailed description of the expense') }}">{{ $expense->description }}</textarea>
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
                        <option value="{{ $department->id }}" {{ $expense->department_id == $department->id ? 'selected' : '' }}>
                          {{ $department->name }}
                        </option>
                      @endforeach
                    </select>
                  </div>

                  <div class="mb-4">
                    <label class="form-label" for="project_code">{{ __('Project Code') }}</label>
                    <input type="text" class="form-control" id="project_code" name="project_code" 
                      value="{{ $expense->project_code }}"
                      placeholder="{{ __('Enter project code (if applicable)') }}">
                  </div>

                  <div class="mb-4">
                    <label class="form-label" for="cost_center">{{ __('Cost Center') }}</label>
                    <input type="text" class="form-control" id="cost_center" name="cost_center" 
                      value="{{ $expense->cost_center }}"
                      placeholder="{{ __('Enter cost center (if applicable)') }}">
                  </div>

                  {{-- Existing Attachments --}}
                  @php
                    $attachments = $expense->attachments;
                    if (is_string($attachments)) {
                        $attachments = json_decode($attachments, true) ?: [];
                    }
                    $attachments = is_array($attachments) ? $attachments : [];
                  @endphp
                  
                  @if(!empty($attachments))
                    <h6 class="mb-3">{{ __('Existing Attachments') }}</h6>
                    <div class="existing-attachments mb-4">
                      @foreach($attachments as $index => $attachment)
                        @php
                          if (is_string($attachment)) {
                              $fileName = basename($attachment);
                          } else {
                              $fileName = $attachment['name'] ?? basename($attachment['path'] ?? 'file');
                          }
                        @endphp
                        <div class="d-flex align-items-center mb-2 attachment-item" data-index="{{ $index }}">
                          <i class="bx bx-file me-2"></i>
                          <span class="me-auto">{{ $fileName }}</span>
                          <button type="button" class="btn btn-sm btn-label-danger remove-attachment" data-index="{{ $index }}">
                            <i class="bx bx-x"></i>
                          </button>
                        </div>
                      @endforeach
                    </div>
                  @endif

                  {{-- New Attachments --}}
                  <h6 class="mb-4">{{ __('Add New Attachments') }} <span class="text-danger" id="required-indicator" style="display: none;">*</span></h6>
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
                      {{ __('Upload additional receipts, invoices, or other supporting documents') }}
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
                      {{ __('Update Request') }}
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
      expenseId: @json($expense->id),
      existingAttachments: @json($attachments),
      urls: {
        update: @json(route('hrcore.expenses.update', $expense->id)),
        index: @json(route('hrcore.expenses.index'))
      },
      labels: {
        success: @json(__('Success!')),
        error: @json(__('Error!')),
        updated: @json(__('Expense request updated successfully')),
        maxAmountExceeded: @json(__('Amount exceeds maximum limit')),
        receiptRequired: @json(__('Receipt is required for this expense type')),
        uploading: @json(__('Uploading...')),
        submitting: @json(__('Updating...')),
        selectPlaceholder: @json(__('Select...')),
        removeFile: @json(__('Remove')),
        maxAmountPrefix: @json(__('Maximum amount: $')),
        receiptRequiredText: @json(__('Receipt/attachment is required for this expense type')),
        uploadDocumentsText: @json(__('Upload additional receipts, invoices, or other supporting documents')),
        confirmRemove: @json(__('Are you sure you want to remove this attachment?')),
        removeTitle: @json(__('Remove Attachment'))
      }
    };
  </script>
@endsection