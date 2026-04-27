@php
  use App\Enums\ExpenseRequestStatus;
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', __('Expense Request Details'))

<!-- Vendor Styles -->
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/animate-css/animate.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
  ])
@endsection

<!-- Page Styles -->
@section('page-style')
  @vite(['Modules/HRCore/resources/assets/css/expense-details.css'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
  ])
@endsection

<!-- Page Scripts -->
@section('page-script')
  @vite(['Modules/HRCore/resources/assets/js/expense-details.js'])
@endsection

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    {{-- Breadcrumb Component --}}
    <x-breadcrumb
      :title="__('Expense Request Details')"
      :breadcrumbs="[
        ['name' => __('Human Resources'), 'url' => ''],
        ['name' => __('Expense Management'), 'url' => ''],
        ['name' => __('Expense Requests'), 'url' => route('hrcore.expenses.index')],
        ['name' => __('Details'), 'url' => '']
      ]"
      :home-url="url('/')"
    />

    <div class="row">
      {{-- Main Details --}}
      <div class="col-xl-8 col-lg-7 col-md-7 order-1 order-md-0">
        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ $expense->title }}</h5>
            <div class="card-action">
              <a href="{{ route('hrcore.expenses.index') }}" class="btn btn-label-secondary">
                <i class="bx bx-arrow-back me-1"></i> {{ __('Back to List') }}
              </a>
              @if($expense->can_edit)
                @can('hrcore.edit-expense')
                  <a href="{{ route('hrcore.expenses.edit', $expense->id) }}" class="btn btn-primary">
                    <i class="bx bx-edit me-1"></i> {{ __('Edit') }}
                  </a>
                @endcan
              @endif
            </div>
          </div>
          
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <h6>{{ __('Basic Information') }}</h6>
                <table class="table table-borderless">
                  <tr>
                    <td class="px-0"><strong>{{ __('Expense Number') }}:</strong></td>
                    <td class="px-0">{{ $expense->expense_number }}</td>
                  </tr>
                  <tr>
                    <td class="px-0"><strong>{{ __('Employee') }}:</strong></td>
                    <td class="px-0">{{ $expense->user->getFullName() }} ({{ $expense->user->code }})</td>
                  </tr>
                  <tr>
                    <td class="px-0"><strong>{{ __('Expense Type') }}:</strong></td>
                    <td class="px-0">{{ $expense->expenseType->name ?? '-' }}</td>
                  </tr>
                  <tr>
                    <td class="px-0"><strong>{{ __('Date') }}:</strong></td>
                    <td class="px-0">{{ $expense->expense_date->format('M d, Y') }}</td>
                  </tr>
                  <tr>
                    <td class="px-0"><strong>{{ __('Amount') }}:</strong></td>
                    <td class="px-0"><span class="fw-bold text-primary">{{ $expense->formatted_amount }}</span></td>
                  </tr>
                  @if($expense->approved_amount && $expense->approved_amount != $expense->amount)
                    <tr>
                      <td class="px-0"><strong>{{ __('Approved Amount') }}:</strong></td>
                      <td class="px-0"><span class="fw-bold text-success">{{ $expense->formatted_approved_amount }}</span></td>
                    </tr>
                  @endif
                </table>
              </div>
              
              <div class="col-md-6">
                <h6>{{ __('Additional Details') }}</h6>
                <table class="table table-borderless">
                  @if($expense->department)
                    <tr>
                      <td class="px-0"><strong>{{ __('Department') }}:</strong></td>
                      <td class="px-0">{{ $expense->department->name }}</td>
                    </tr>
                  @endif
                  @if($expense->project_code)
                    <tr>
                      <td class="px-0"><strong>{{ __('Project Code') }}:</strong></td>
                      <td class="px-0">{{ $expense->project_code }}</td>
                    </tr>
                  @endif
                  @if($expense->cost_center)
                    <tr>
                      <td class="px-0"><strong>{{ __('Cost Center') }}:</strong></td>
                      <td class="px-0">{{ $expense->cost_center }}</td>
                    </tr>
                  @endif
                  <tr>
                    <td class="px-0"><strong>{{ __('Submitted') }}:</strong></td>
                    <td class="px-0">{{ $expense->created_at->format('M d, Y H:i') }}</td>
                  </tr>
                </table>
              </div>
            </div>
            
            @if($expense->description)
              <div class="mt-4">
                <h6>{{ __('Description') }}</h6>
                <div class="card">
                  <div class="card-body">
                    {{ $expense->description }}
                  </div>
                </div>
              </div>
            @endif

            {{-- Attachments --}}
            @if($expense->hasAttachments())
              <div class="mt-4">
                <h6>{{ __('Attachments') }}</h6>
                <div class="row">
                  @foreach($expense->getExpenseDocuments() as $file)
                    @php
                      $fileName = $file->original_name;
                      $fileSize = $file->size;
                      $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                      $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']);
                      
                      // Use proper routes for file access
                      $downloadUrl = route('filemanagercore.file.download', $file->uuid);
                      $viewUrl = $isImage ? route('filemanagercore.file.view', $file->uuid) : null;
                    @endphp
                    
                    <div class="col-md-6 col-lg-4 mb-3">
                      <div class="card">
                        <div class="card-body text-center">
                          @if($isImage && $viewUrl)
                            <img src="{{ $viewUrl }}" alt="{{ $fileName }}" 
                              class="img-fluid mb-2" style="max-height: 150px;">
                          @else
                            <i class="bx bx-file display-4 mb-2"></i>
                          @endif
                          
                          <h6 class="mb-1">{{ $fileName }}</h6>
                          @if($fileSize > 0)
                            <small class="text-muted">{{ $file->formatted_size }}</small>
                          @endif
                          
                          <div class="mt-2">
                            <a href="{{ $downloadUrl }}" class="btn btn-sm btn-primary">
                              <i class="bx bx-download me-1"></i> {{ __('Download') }}
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
              </div>
            @endif
          </div>
        </div>
      </div>

      {{-- Status & Actions --}}
      <div class="col-xl-4 col-lg-5 col-md-5 order-0 order-md-1">
        {{-- Status Card --}}
        <div class="card mb-4">
          <div class="card-body text-center">
            <h6 class="card-title">{{ __('Status') }}</h6>
            @php
              $statusConfig = match($expense->status) {
                ExpenseRequestStatus::PENDING => ['class' => 'bg-warning', 'icon' => 'bx-time', 'text' => __('Pending Approval')],
                ExpenseRequestStatus::APPROVED => ['class' => 'bg-success', 'icon' => 'bx-check-circle', 'text' => __('Approved')],
                ExpenseRequestStatus::REJECTED => ['class' => 'bg-danger', 'icon' => 'bx-x-circle', 'text' => __('Rejected')],
                ExpenseRequestStatus::PROCESSED => ['class' => 'bg-info', 'icon' => 'bx-money', 'text' => __('Processed')],
                default => ['class' => 'bg-secondary', 'icon' => 'bx-question-mark', 'text' => __('Unknown')]
              }
            @endphp
            <div class="avatar avatar-lg mx-auto mb-3">
              <div class="avatar-initial rounded-circle {{ $statusConfig['class'] }}">
                <i class="bx {{ $statusConfig['icon'] }} display-4"></i>
              </div>
            </div>
            <h5>{{ $statusConfig['text'] }}</h5>
          </div>
        </div>

        {{-- Actions Card --}}
        @if($expense->can_approve && $expense->status === ExpenseRequestStatus::PENDING)
          @canany(['hrcore.approve-expense', 'hrcore.reject-expense'])
            <div class="card mb-4">
              <div class="card-body">
                <h6 class="card-title">{{ __('Actions') }}</h6>
                <div class="d-grid gap-2">
                  @can('hrcore.approve-expense')
                    <button type="button" class="btn btn-success" onclick="approveExpense()">
                      <i class="bx bx-check me-1"></i> {{ __('Approve') }}
                    </button>
                  @endcan
                  @can('hrcore.reject-expense')
                    <button type="button" class="btn btn-danger" onclick="rejectExpense()">
                      <i class="bx bx-x me-1"></i> {{ __('Reject') }}
                    </button>
                  @endcan
                </div>
              </div>
            </div>
          @endcanany
        @endif

        @if($expense->can_process && $expense->status === ExpenseRequestStatus::APPROVED)
          @can('hrcore.process-expense')
            <div class="card mb-4">
              <div class="card-body">
                <h6 class="card-title">{{ __('Payment Processing') }}</h6>
                <button type="button" class="btn btn-primary w-100" onclick="processExpense()">
                  <i class="bx bx-money me-1"></i> {{ __('Mark as Processed') }}
                </button>
              </div>
            </div>
          @endcan
        @endif

        {{-- Approval History --}}
        <div class="card">
          <div class="card-body">
            <h6 class="card-title">{{ __('Timeline') }}</h6>
            <div class="timeline">
              <div class="timeline-item">
                <div class="timeline-point">
                  <i class="bx bx-plus"></i>
                </div>
                <div class="timeline-content">
                  <h6 class="mb-1">{{ __('Request Submitted') }}</h6>
                  <small class="text-muted">{{ $expense->created_at->format('M d, Y H:i') }}</small>
                  <p class="mb-0 text-muted">{{ __('By') }} {{ $expense->user->getFullName() }}</p>
                </div>
              </div>

              @if($expense->approved_at)
                <div class="timeline-item">
                  <div class="timeline-point">
                    <i class="bx bx-check"></i>
                  </div>
                  <div class="timeline-content">
                    <h6 class="mb-1">{{ __('Approved') }}</h6>
                    <small class="text-muted">{{ $expense->approved_at->format('M d, Y H:i') }}</small>
                    <p class="mb-0 text-muted">{{ __('By') }} {{ $expense->approvedBy?->getFullName() ?? __('System') }}</p>
                    @if($expense->approval_remarks)
                      <p class="mt-2 text-sm"><em>"{{ $expense->approval_remarks }}"</em></p>
                    @endif
                  </div>
                </div>
              @endif

              @if($expense->rejected_at)
                <div class="timeline-item">
                  <div class="timeline-point">
                    <i class="bx bx-x"></i>
                  </div>
                  <div class="timeline-content">
                    <h6 class="mb-1">{{ __('Rejected') }}</h6>
                    <small class="text-muted">{{ $expense->rejected_at->format('M d, Y H:i') }}</small>
                    <p class="mb-0 text-muted">{{ __('By') }} {{ $expense->rejectedBy?->getFullName() ?? __('System') }}</p>
                    @if($expense->rejection_reason)
                      <p class="mt-2 text-sm"><em>"{{ $expense->rejection_reason }}"</em></p>
                    @endif
                  </div>
                </div>
              @endif

              @if($expense->processed_at)
                <div class="timeline-item">
                  <div class="timeline-point">
                    <i class="bx bx-money"></i>
                  </div>
                  <div class="timeline-content">
                    <h6 class="mb-1">{{ __('Payment Processed') }}</h6>
                    <small class="text-muted">{{ $expense->processed_at->format('M d, Y H:i') }}</small>
                    <p class="mb-0 text-muted">{{ __('By') }} {{ $expense->processedBy?->getFullName() ?? __('System') }}</p>
                    @if($expense->payment_reference)
                      <p class="mt-2 text-sm">{{ __('Reference') }}: {{ $expense->payment_reference }}</p>
                    @endif
                    @if($expense->processing_notes)
                      <p class="mt-2 text-sm"><em>"{{ $expense->processing_notes }}"</em></p>
                    @endif
                  </div>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Include modals --}}
  @include('hrcore::expenses._modals')

  {{-- Page Data for JavaScript --}}
  <script>
    window.pageData = {
      urls: {
        approve: @json(route('hrcore.expenses.approve', $expense->id)),
        reject: @json(route('hrcore.expenses.reject', $expense->id)),
        process: @json(route('hrcore.expenses.process', $expense->id))
      },
      labels: {
        success: @json(__('Success!')),
        error: @json(__('Error!')),
        approved: @json(__('Expense approved successfully')),
        rejected: @json(__('Expense rejected successfully')),
        processed: @json(__('Expense processed successfully'))
      }
    };
  </script>
@endsection