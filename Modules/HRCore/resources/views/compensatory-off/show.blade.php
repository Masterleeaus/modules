@extends('layouts/layoutMaster')

@section('title', __('Compensatory Off Details'))

<!-- Vendor Styles -->
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
  ])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
  ])
@endsection

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    {{-- Breadcrumb Component --}}
    <x-breadcrumb
      :title="__('Compensatory Off Details')"
      :breadcrumbs="[
        ['name' => __('Human Resources'), 'url' => ''],
        ['name' => __('Compensatory Off'), 'url' => route('hrcore.compensatory-offs.index')],
        ['name' => __('Details'), 'url' => '']
      ]"
      :home-url="url('/')"
    >
      @if($compOff->status === 'pending' && ($compOff->user_id === auth()->id() || auth()->user()->can('hrcore.edit-comp-off')))
        <a href="{{ route('hrcore.compensatory-offs.edit', $compOff->id) }}" class="btn btn-primary">
          <i class="bx bx-edit me-1"></i>{{ __('Edit Request') }}
        </a>
      @endif
    </x-breadcrumb>

    <div class="row">
      {{-- Main Content --}}
      <div class="col-md-8">
        {{-- Compensatory Off Details Card --}}
        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <div>
              <h5 class="card-title mb-1">{{ __('Compensatory Off Request #:id', ['id' => $compOff->id]) }}</h5>
              <small class="text-muted">{{ __('Submitted on') }} {{ \Carbon\Carbon::parse($compOff->created_at)->format('M d, Y H:i') }}</small>
            </div>
            <div>
              @php
                $statusColors = [
                  'pending' => 'warning',
                  'approved' => 'success', 
                  'rejected' => 'danger'
                ];
                $color = $statusColors[$compOff->status] ?? 'secondary';
              @endphp
              <span class="badge bg-{{ $color }} badge-lg">{{ ucfirst($compOff->status) }}</span>
            </div>
          </div>
          <div class="card-body">
            <div class="row">
              {{-- Employee Information --}}
              <div class="col-md-6 mb-4">
                <h6 class="text-primary mb-3">{{ __('Employee Information') }}</h6>
                <div class="d-flex align-items-center mb-3">
                  @if($compOff->user->avatar)
                    <img src="{{ $compOff->user->avatar }}" alt="Avatar" class="rounded-circle me-3" width="50" height="50">
                  @else
                    <div class="avatar avatar-md me-3">
                      <span class="avatar-initial rounded-circle bg-label-primary">
                        {{ substr($compOff->user->first_name, 0, 1) }}{{ substr($compOff->user->last_name, 0, 1) }}
                      </span>
                    </div>
                  @endif
                  <div>
                    <h6 class="mb-0">{{ $compOff->user->first_name }} {{ $compOff->user->last_name }}</h6>
                    <small class="text-muted">{{ $compOff->user->code }} | {{ $compOff->user->designation->name ?? __('N/A') }}</small>
                    <br>
                    <small class="text-muted">{{ $compOff->user->department->name ?? __('N/A') }}</small>
                  </div>
                </div>
              </div>

              {{-- Work Information --}}
              <div class="col-md-6 mb-4">
                <h6 class="text-primary mb-3">{{ __('Work Information') }}</h6>
                <table class="table table-borderless table-sm">
                  <tr>
                    <td class="text-muted">{{ __('Worked Date') }}:</td>
                    <td><strong>{{ \Carbon\Carbon::parse($compOff->worked_date)->format('M d, Y') }}</strong></td>
                  </tr>
                  <tr>
                    <td class="text-muted">{{ __('Hours Worked') }}:</td>
                    <td><span class="badge bg-label-info">{{ $compOff->hours_worked }} {{ __('hours') }}</span></td>
                  </tr>
                  <tr>
                    <td class="text-muted">{{ __('Comp Off Days') }}:</td>
                    <td><span class="badge bg-label-primary">{{ $compOff->comp_off_days }} {{ __('days') }}</span></td>
                  </tr>
                  <tr>
                    <td class="text-muted">{{ __('Expiry Date') }}:</td>
                    <td>
                      @php
                        $expiryDate = \Carbon\Carbon::parse($compOff->expiry_date);
                        $isExpired = $expiryDate->isPast() && !$compOff->is_used;
                        $isExpiringSoon = $expiryDate->diffInDays(now()) <= 7 && !$compOff->is_used;
                        $badgeColor = $isExpired ? 'danger' : ($isExpiringSoon ? 'warning' : 'secondary');
                      @endphp
                      <span class="badge bg-label-{{ $badgeColor }}">
                        {{ $expiryDate->format('M d, Y') }}
                        @if($isExpired)
                          ({{ __('Expired') }})
                        @elseif($isExpiringSoon)
                          ({{ __('Expiring Soon') }})
                        @endif
                      </span>
                    </td>
                  </tr>
                </table>
              </div>
            </div>

            {{-- Reason --}}
            <div class="mb-4">
              <h6 class="text-primary mb-2">{{ __('Reason for Extra Hours') }}</h6>
              <div class="border rounded p-3 bg-light">
                <p class="mb-0">{{ $compOff->reason }}</p>
              </div>
            </div>

            {{-- Usage Information --}}
            <div class="mb-4">
              <h6 class="text-primary mb-3">{{ __('Usage Information') }}</h6>
              <div class="row">
                <div class="col-md-4 mb-2">
                  <small class="text-muted d-block">{{ __('Current Status') }}</small>
                  @if($compOff->is_used)
                    <span class="badge bg-success">{{ __('Used') }}</span>
                  @elseif($compOff->status === 'approved' && $expiryDate->isPast())
                    <span class="badge bg-danger">{{ __('Expired') }}</span>
                  @elseif($compOff->status === 'approved')
                    <span class="badge bg-primary">{{ __('Available') }}</span>
                  @else
                    <span class="badge bg-secondary">{{ __('Not Available') }}</span>
                  @endif
                </div>
                @if($compOff->is_used)
                <div class="col-md-4 mb-2">
                  <small class="text-muted d-block">{{ __('Used Date') }}</small>
                  <strong>{{ \Carbon\Carbon::parse($compOff->used_date)->format('M d, Y') }}</strong>
                </div>
                @endif
                @if($compOff->leaveRequest)
                <div class="col-md-4 mb-2">
                  <small class="text-muted d-block">{{ __('Used For Leave') }}</small>
                  <a href="{{ route('hrcore.leave-requests.show', $compOff->leaveRequest->id) }}" class="text-primary">
                    {{ __('Leave Request #:id', ['id' => $compOff->leaveRequest->id]) }}
                  </a>
                </div>
                @endif
              </div>
            </div>

            {{-- Calculation Info --}}
            <div class="mb-4">
              <h6 class="text-primary mb-2">{{ __('Calculation Details') }}</h6>
              <div class="row">
                <div class="col-md-6">
                  <div class="border rounded p-3 text-center bg-light">
                    <h6 class="mb-1">{{ __('Hours to Days Ratio') }}</h6>
                    <div class="text-muted">
                      {{ $compOff->hours_worked }} {{ __('hours') }} ÷ {{ $compOff->comp_off_days }} {{ __('days') }} = 
                      <strong>{{ round($compOff->hours_worked / $compOff->comp_off_days, 1) }} {{ __('hours/day') }}</strong>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="border rounded p-3 text-center bg-light">
                    <h6 class="mb-1">{{ __('Efficiency Rating') }}</h6>
                    <div class="text-muted">
                      @php
                        $ratio = $compOff->hours_worked / $compOff->comp_off_days;
                        if ($ratio >= 8) {
                          $rating = 'Standard';
                          $ratingColor = 'success';
                        } elseif ($ratio >= 6) {
                          $rating = 'Good';
                          $ratingColor = 'primary';
                        } else {
                          $rating = 'High';
                          $ratingColor = 'warning';
                        }
                      @endphp
                      <span class="badge bg-{{ $ratingColor }}">{{ __($rating) }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- Approval Information --}}
        @if($compOff->approved_by_id || $compOff->rejected_by_id)
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="card-title">{{ __('Approval Information') }}</h5>
          </div>
          <div class="card-body">
            @if($compOff->approved_by_id)
              <div class="d-flex align-items-center mb-3">
                <div class="avatar avatar-sm me-3">
                  <span class="avatar-initial rounded-circle bg-success">
                    <i class="bx bx-check"></i>
                  </span>
                </div>
                <div>
                  <h6 class="mb-0">{{ __('Approved by') }} {{ $compOff->approvedBy->first_name }} {{ $compOff->approvedBy->last_name }}</h6>
                  <small class="text-muted">{{ \Carbon\Carbon::parse($compOff->approved_at)->format('M d, Y H:i') }}</small>
                </div>
              </div>
            @endif

            @if($compOff->rejected_by_id)
              <div class="d-flex align-items-center mb-3">
                <div class="avatar avatar-sm me-3">
                  <span class="avatar-initial rounded-circle bg-danger">
                    <i class="bx bx-x"></i>
                  </span>
                </div>
                <div>
                  <h6 class="mb-0">{{ __('Rejected by') }} {{ $compOff->rejectedBy->first_name }} {{ $compOff->rejectedBy->last_name }}</h6>
                  <small class="text-muted">{{ \Carbon\Carbon::parse($compOff->rejected_at)->format('M d, Y H:i') }}</small>
                </div>
              </div>
            @endif

            @if($compOff->approval_notes)
              <div class="mt-3">
                <h6 class="mb-2">{{ __('Notes') }}</h6>
                <div class="border rounded p-3 bg-light">
                  <p class="mb-0">{{ $compOff->approval_notes }}</p>
                </div>
              </div>
            @endif
          </div>
        </div>
        @endif

        {{-- Action Buttons --}}
        @if($compOff->status === 'pending')
        <div class="card">
          <div class="card-header">
            <h5 class="card-title">{{ __('Actions') }}</h5>
          </div>
          <div class="card-body">
            <div class="d-flex gap-2 flex-wrap">
              {{-- Approve Button --}}
              @if(auth()->user()->can('hrcore.approve-comp-off') || ($compOff->user->reporting_to_id === auth()->id()))
                <button type="button" class="btn btn-success" onclick="approveCompOff({{ $compOff->id }})">
                  <i class="bx bx-check me-1"></i>{{ __('Approve') }}
                </button>
              @endif

              {{-- Reject Button --}}
              @if(auth()->user()->can('hrcore.approve-comp-off') || ($compOff->user->reporting_to_id === auth()->id()))
                <button type="button" class="btn btn-danger" onclick="rejectCompOff({{ $compOff->id }})">
                  <i class="bx bx-x me-1"></i>{{ __('Reject') }}
                </button>
              @endif

              {{-- Edit Button --}}
              @if($compOff->user_id === auth()->id() || auth()->user()->can('hrcore.edit-comp-off'))
                <a href="{{ route('hrcore.compensatory-offs.edit', $compOff->id) }}" class="btn btn-primary">
                  <i class="bx bx-edit me-1"></i>{{ __('Edit') }}
                </a>
              @endif

              {{-- Delete Button --}}
              @if(auth()->user()->can('hrcore.delete-comp-off'))
                <button type="button" class="btn btn-outline-danger" onclick="deleteCompOff({{ $compOff->id }})">
                  <i class="bx bx-trash me-1"></i>{{ __('Delete') }}
                </button>
              @endif
            </div>
          </div>
        </div>
        @endif
      </div>

      {{-- Sidebar --}}
      <div class="col-md-4">
        {{-- Quick Stats --}}
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="card-title">{{ __('Quick Stats') }}</h5>
          </div>
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted">{{ __('Request ID') }}</span>
              <strong>#{{ $compOff->id }}</strong>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted">{{ __('Hours Worked') }}</span>
              <strong>{{ $compOff->hours_worked }}</strong>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted">{{ __('Days Earned') }}</span>
              <strong>{{ $compOff->comp_off_days }}</strong>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted">{{ __('Submitted') }}</span>
              <strong>{{ \Carbon\Carbon::parse($compOff->created_at)->diffForHumans() }}</strong>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-muted">{{ __('Days Until Expiry') }}</span>
              <strong>
                @if($compOff->is_used)
                  {{ __('Used') }}
                @elseif($expiryDate->isPast())
                  <span class="text-danger">{{ __('Expired') }}</span>
                @else
                  {{ $expiryDate->diffForHumans() }}
                @endif
              </strong>
            </div>
          </div>
        </div>

        {{-- Timeline --}}
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="card-title">{{ __('Timeline') }}</h5>
          </div>
          <div class="card-body">
            <div class="timeline">
              {{-- Created --}}
              <div class="timeline-item">
                <div class="timeline-marker bg-primary"></div>
                <div class="timeline-content">
                  <h6 class="mb-1">{{ __('Request Created') }}</h6>
                  <small class="text-muted">{{ \Carbon\Carbon::parse($compOff->created_at)->format('M d, Y H:i') }}</small>
                </div>
              </div>

              {{-- Updated --}}
              @if($compOff->updated_at != $compOff->created_at)
              <div class="timeline-item">
                <div class="timeline-marker bg-info"></div>
                <div class="timeline-content">
                  <h6 class="mb-1">{{ __('Request Updated') }}</h6>
                  <small class="text-muted">{{ \Carbon\Carbon::parse($compOff->updated_at)->format('M d, Y H:i') }}</small>
                </div>
              </div>
              @endif

              {{-- Approved --}}
              @if($compOff->approved_at)
              <div class="timeline-item">
                <div class="timeline-marker bg-success"></div>
                <div class="timeline-content">
                  <h6 class="mb-1">{{ __('Request Approved') }}</h6>
                  <small class="text-muted">{{ \Carbon\Carbon::parse($compOff->approved_at)->format('M d, Y H:i') }}</small>
                </div>
              </div>
              @endif

              {{-- Used --}}
              @if($compOff->is_used)
              <div class="timeline-item">
                <div class="timeline-marker bg-success"></div>
                <div class="timeline-content">
                  <h6 class="mb-1">{{ __('Compensatory Off Used') }}</h6>
                  <small class="text-muted">{{ \Carbon\Carbon::parse($compOff->used_date)->format('M d, Y') }}</small>
                </div>
              </div>
              @endif

              {{-- Expired --}}
              @if($compOff->status === 'approved' && !$compOff->is_used && $expiryDate->isPast())
              <div class="timeline-item">
                <div class="timeline-marker bg-danger"></div>
                <div class="timeline-content">
                  <h6 class="mb-1">{{ __('Compensatory Off Expired') }}</h6>
                  <small class="text-muted">{{ $expiryDate->format('M d, Y') }}</small>
                </div>
              </div>
              @endif
            </div>
          </div>
        </div>

        {{-- Related Information --}}
        @if($compOff->leaveRequest)
        <div class="card">
          <div class="card-header">
            <h5 class="card-title">{{ __('Related Leave Request') }}</h5>
          </div>
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="avatar avatar-sm me-3">
                <span class="avatar-initial rounded bg-label-success">
                  <i class="bx bx-calendar-check"></i>
                </span>
              </div>
              <div>
                <h6 class="mb-0">
                  <a href="{{ route('hrcore.leave-requests.show', $compOff->leaveRequest->id) }}" class="text-primary">
                    {{ __('Leave Request #:id', ['id' => $compOff->leaveRequest->id]) }}
                  </a>
                </h6>
                <small class="text-muted">
                  {{ \Carbon\Carbon::parse($compOff->leaveRequest->from_date)->format('M d') }} - 
                  {{ \Carbon\Carbon::parse($compOff->leaveRequest->to_date)->format('M d, Y') }}
                </small>
              </div>
            </div>
          </div>
        </div>
        @endif
      </div>
    </div>
  </div>

  <script>
    // Page data for JavaScript functions
    const pageData = {
      urls: {
        approve: @json(route('hrcore.compensatory-offs.approve', ':id')),
        reject: @json(route('hrcore.compensatory-offs.reject', ':id')),
        destroy: @json(route('hrcore.compensatory-offs.destroy', ':id')),
        index: @json(route('hrcore.compensatory-offs.index'))
      },
      labels: {
        confirmAction: @json(__('Are you sure?')),
        success: @json(__('Success')),
        error: @json(__('Error')),
        notes: @json(__('Notes (optional)')),
        enterNotes: @json(__('Enter any notes...')),
        reason: @json(__('Reason')),
        enterReason: @json(__('Enter reason for rejection')),
        cancel: @json(__('Cancel')),
        confirm: @json(__('Confirm'))
      }
    };

    // Approve compensatory off request
    function approveCompOff(id) {
      Swal.fire({
        title: pageData.labels.confirmAction,
        text: 'Are you sure you want to approve this compensatory off request?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Approve',
        cancelButtonText: pageData.labels.cancel,
        input: 'textarea',
        inputLabel: pageData.labels.notes,
        inputPlaceholder: pageData.labels.enterNotes
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: pageData.urls.approve.replace(':id', id),
            method: 'POST',
            data: {
              notes: result.value || '',
              _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
              if (response.status === 'success') {
                Swal.fire({
                  icon: 'success',
                  title: pageData.labels.success,
                  text: response.data.message || 'Compensatory off request approved successfully'
                }).then(() => {
                  window.location.reload();
                });
              }
            },
            error: function () {
              Swal.fire({
                icon: 'error',
                title: pageData.labels.error,
                text: 'Failed to approve compensatory off request'
              });
            }
          });
        }
      });
    }

    // Reject compensatory off request
    function rejectCompOff(id) {
      Swal.fire({
        title: pageData.labels.confirmAction,
        text: 'Are you sure you want to reject this compensatory off request?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Reject',
        cancelButtonText: pageData.labels.cancel,
        input: 'textarea',
        inputLabel: pageData.labels.reason,
        inputPlaceholder: pageData.labels.enterReason,
        inputValidator: (value) => {
          if (!value) {
            return 'Reason is required for rejection'
          }
        }
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: pageData.urls.reject.replace(':id', id),
            method: 'POST',
            data: {
              reason: result.value,
              _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
              if (response.status === 'success') {
                Swal.fire({
                  icon: 'success',
                  title: pageData.labels.success,
                  text: response.data.message || 'Compensatory off request rejected'
                }).then(() => {
                  window.location.reload();
                });
              }
            },
            error: function () {
              Swal.fire({
                icon: 'error',
                title: pageData.labels.error,
                text: 'Failed to reject compensatory off request'
              });
            }
          });
        }
      });
    }

    // Delete compensatory off request
    function deleteCompOff(id) {
      Swal.fire({
        title: pageData.labels.confirmAction,
        text: 'This action cannot be undone!',
        icon: 'danger',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: pageData.labels.cancel
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: pageData.urls.destroy.replace(':id', id),
            method: 'DELETE',
            data: {
              _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
              if (response.status === 'success') {
                Swal.fire({
                  icon: 'success',
                  title: pageData.labels.success,
                  text: response.data.message || 'Compensatory off request deleted'
                }).then(() => {
                  window.location.href = pageData.urls.index;
                });
              }
            },
            error: function () {
              Swal.fire({
                icon: 'error',
                title: pageData.labels.error,
                text: 'Failed to delete compensatory off request'
              });
            }
          });
        }
      });
    }
  </script>

  <style>
    .timeline {
      position: relative;
      padding-left: 20px;
    }
    
    .timeline::before {
      content: '';
      position: absolute;
      left: 10px;
      top: 10px;
      bottom: 10px;
      width: 2px;
      background: #e3e6f0;
    }
    
    .timeline-item {
      position: relative;
      margin-bottom: 20px;
    }
    
    .timeline-marker {
      position: absolute;
      left: -15px;
      width: 12px;
      height: 12px;
      border-radius: 50%;
      top: 5px;
      border: 2px solid #fff;
      box-shadow: 0 0 0 2px #e3e6f0;
    }
    
    .timeline-content {
      padding-left: 10px;
    }
  </style>
@endsection