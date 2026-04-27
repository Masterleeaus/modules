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
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss'
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
    'resources/assets/vendor/libs/flatpickr/flatpickr.js'
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
        ['name' => __('Self Service'), 'url' => ''],
        ['name' => __('My Expenses'), 'url' => route('hrcore.my.expenses')],
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
              <a href="{{ route('hrcore.my.expenses') }}" class="btn btn-label-secondary">
                <i class="bx bx-arrow-back me-1"></i> {{ __('Back to My Expenses') }}
              </a>
            </div>
          </div>
          
          <div class="card-body">
            <form id="expenseForm" enctype="multipart/form-data">
              @csrf
              <!-- Hidden user_id for self-service -->
              <input type="hidden" name="user_id" value="{{ auth()->id() }}">
              
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
                          data-requires-receipt="{{ $expenseType->requires_receipt ? '1' : '0' }}">
                          {{ $expenseType->name }}
                          @if($expenseType->max_amount)
                            ({{ __('Max') }}: {{ \App\Helpers\FormattingHelper::formatCurrency($expenseType->max_amount) }})
                          @endif
                        </option>
                      @endforeach
                    </select>
                  </div>
                  
                  <div class="mb-4">
                    <label class="form-label" for="expense_date">{{ __('Expense Date') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control flatpickr" id="expense_date" name="expense_date" placeholder="{{ __('Select Date') }}" required>
                  </div>
                  
                  <div class="mb-4">
                    <label class="form-label" for="amount">{{ __('Amount') }} <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <span class="input-group-text">{{ config('app.currency_symbol', '$') }}</span>
                      <input type="number" class="form-control" id="amount" name="amount" placeholder="0.00" step="0.01" min="0" required>
                    </div>
                    <div class="form-text" id="max_amount_hint" style="display: none;">
                      {{ __('Maximum allowed amount') }}: <span id="max_amount_value"></span>
                    </div>
                  </div>
                  
                  <div class="mb-4">
                    <label class="form-label" for="title">{{ __('Title') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="title" name="title" placeholder="{{ __('Enter expense title...') }}" required>
                  </div>
                  
                  <div class="mb-4">
                    <label class="form-label" for="description">{{ __('Description') }}</label>
                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="{{ __('Enter expense description...') }}"></textarea>
                  </div>
                </div>
                
                <div class="col-md-6">
                  {{-- Additional Details --}}
                  <h6 class="mb-4">{{ __('Additional Details') }}</h6>
                  
                  <div class="mb-4">
                    <label class="form-label" for="department_id">{{ __('Department') }}</label>
                    <select class="form-select select2" id="department_id" name="department_id">
                      <option value="">{{ __('Select Department (Optional)') }}</option>
                      @foreach($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  
                  <div class="mb-4">
                    <label class="form-label" for="project_code">{{ __('Project Code') }}</label>
                    <input type="text" class="form-control" id="project_code" name="project_code" placeholder="{{ __('Enter project code') }}">
                  </div>
                  
                  <div class="mb-4">
                    <label class="form-label" for="cost_center">{{ __('Cost Center') }}</label>
                    <input type="text" class="form-control" id="cost_center" name="cost_center" placeholder="{{ __('Enter cost center') }}">
                  </div>
                  
                  {{-- Receipt Upload --}}
                  <div class="mb-4">
                    <label class="form-label" for="attachments">{{ __('Receipt/Invoice') }} <span id="receipt_required" class="text-danger" style="display: none;">*</span></label>
                    <input type="file" class="form-control" id="attachments" name="attachments[]" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" multiple>
                    <div class="form-text">{{ __('Upload receipts or invoices (PDF, JPG, PNG, DOC - Max 10MB per file)') }}</div>
                  </div>
                </div>
              </div>
              
              <div class="row mt-4">
                <div class="col-12">
                  <button type="submit" class="btn btn-primary">
                    <i class="bx bx-save me-1"></i> {{ __('Submit Expense Request') }}
                  </button>
                  <a href="{{ route('hrcore.my.expenses') }}" class="btn btn-label-secondary ms-2">
                    <i class="bx bx-x me-1"></i> {{ __('Cancel') }}
                  </a>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Page Data for JavaScript -->
  <script>
    window.pageData = {
      urls: {
        store: @json(route('hrcore.my.expenses.store')),
        list: @json(route('hrcore.my.expenses'))
      },
      labels: {
        success: @json(__('Success')),
        error: @json(__('Error')),
        confirmSubmit: @json(__('Are you sure you want to submit this expense request?')),
        submitTitle: @json(__('Submit Expense Request')),
        submitButton: @json(__('Yes, Submit')),
        cancelButton: @json(__('Cancel')),
        created: @json(__('Expense request created successfully')),
        maxAmountExceeded: @json(__('Amount exceeds the maximum limit for this expense type')),
        receiptRequired: @json(__('Receipt is required for this expense type'))
      },
      currency: @json(config('app.currency_symbol', '$'))
    };
  </script>
@endsection