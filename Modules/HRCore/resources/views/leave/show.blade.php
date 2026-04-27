@extends('layouts/layoutMaster')

@section('title', __('Leave Request Details'))

<!-- Vendor Styles -->
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
    'resources/assets/vendor/libs/apex-charts/apex-charts.scss'
  ])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
    'resources/assets/vendor/libs/apex-charts/apexcharts.js'
  ])
@endsection

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    {{-- Breadcrumb Component --}}
    <x-breadcrumb
      :title="__('Leave Request Details')"
      :breadcrumbs="[
        ['name' => __('Human Resources'), 'url' => ''],
        ['name' => __('Leave Requests'), 'url' => route('hrcore.leaves.index')],
        ['name' => __('Details'), 'url' => '']
      ]"
      :home-url="url('/')"
    >
      <div class="d-flex gap-2">
        <button type="button" class="btn btn-label-secondary" onclick="window.history.back()">
          <i class="bx bx-arrow-back me-1"></i>{{ __('Back') }}
        </button>
        <a href="{{ route('hrcore.leaves.index') }}" class="btn btn-label-primary">
          <i class="bx bx-list-ul me-1"></i>{{ __('All Requests') }}
        </a>
      </div>
    </x-breadcrumb>

    {{-- Action Buttons Row --}}
    <div class="row mb-4" id="actionButtonsContainer" style="display: none;">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0">{{ __('Quick Actions') }}</h5>
              <div class="d-flex gap-2 flex-wrap" id="actionButtonsWrapper">
                {{-- Dynamic action buttons will be inserted here --}}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row" id="leaveDetailsContainer">
      {{-- Loading State --}}
      <div class="col-12">
        <div class="card">
          <div class="card-body text-center py-5">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">{{ __('Loading...') }}</span>
            </div>
            <div class="mt-3">{{ __('Loading leave request details...') }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Page Data for JavaScript --}}
  <script>
    const pageData = {
      urls: {
        show: @json(route('hrcore.leaves.show', ':id')),
        approve: @json(route('hrcore.leaves.approve', ':id')),
        reject: @json(route('hrcore.leaves.reject', ':id')),
        cancel: @json(route('hrcore.leaves.cancel', ':id')),
        destroy: @json(route('hrcore.leaves.destroy', ':id')),
        edit: @json(route('hrcore.leaves.edit', ':id')),
        index: @json(route('hrcore.leaves.index'))
      },
      labels: {
        confirmApprove: @json(__('Are you sure you want to approve this leave request?')),
        confirmReject: @json(__('Are you sure you want to reject this leave request?')),
        confirmCancel: @json(__('Are you sure you want to cancel this leave request?')),
        confirmDelete: @json(__('Are you sure you want to delete this leave request? This action cannot be undone!')),
        enterNotes: @json(__('Enter approval notes (optional)')),
        enterReason: @json(__('Enter reason')),
        reasonRequired: @json(__('Reason is required')),
        success: @json(__('Success')),
        error: @json(__('Error')),
        yes: @json(__('Yes')),
        no: @json(__('No')),
        approve: @json(__('Approve')),
        reject: @json(__('Reject')),
        cancel: @json(__('Cancel')),
        delete: @json(__('Delete'))
      }
    };

    // Get leave ID from URL
    const leaveId = {{ request()->route('id') ?? 'null' }};

    // Load leave details on page load
    document.addEventListener('DOMContentLoaded', function() {
      if (leaveId) {
        loadLeaveDetails(leaveId);
      }
    });

    function loadLeaveDetails(id) {
      fetch(pageData.urls.show.replace(':id', id), {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          renderLeaveDetails(data.data);
        } else {
          showError(data.message || 'Failed to load leave details');
        }
      })
      .catch(error => {
        console.error('Error loading leave details:', error);
        showError('Something went wrong. Please try again.');
      });
    }

    function renderActionButtons(leave) {
      const actionContainer = document.getElementById('actionButtonsContainer');
      const actionWrapper = document.getElementById('actionButtonsWrapper');
      
      // Clear any existing buttons
      actionWrapper.innerHTML = '';
      
      // Build action buttons based on permissions and status
      let hasActions = false;
      
      // Approve button (only for pending)
      if (leave.can_approve && leave.status === 'pending') {
        actionWrapper.innerHTML += `
          <button type="button" class="btn btn-success" onclick="approveLeaveRequest(${leave.id})">
            <i class="bx bx-check me-1"></i>{{ __('Approve') }}
          </button>`;
        hasActions = true;
      }
      
      // Reject button (only for pending)
      if (leave.can_reject && leave.status === 'pending') {
        actionWrapper.innerHTML += `
          <button type="button" class="btn btn-danger" onclick="rejectLeaveRequest(${leave.id})">
            <i class="bx bx-x me-1"></i>{{ __('Reject') }}
          </button>`;
        hasActions = true;
      }
      
      // Cancel button (for pending or approved)
      if (leave.can_cancel) {
        actionWrapper.innerHTML += `
          <button type="button" class="btn btn-warning" onclick="cancelLeaveRequest(${leave.id})">
            <i class="bx bx-block me-1"></i>{{ __('Cancel Request') }}
          </button>`;
        hasActions = true;
      }
      
      // Edit button (only for pending)
      if (leave.can_edit) {
        actionWrapper.innerHTML += `
          <a href="${pageData.urls.edit.replace(':id', leave.id)}" class="btn btn-primary">
            <i class="bx bx-edit me-1"></i>{{ __('Edit') }}
          </a>`;
        hasActions = true;
      }
      
      // Delete button (show at the end)
      if (leave.can_delete) {
        actionWrapper.innerHTML += `
          <button type="button" class="btn btn-outline-danger" onclick="deleteLeaveRequest(${leave.id})">
            <i class="bx bx-trash me-1"></i>{{ __('Delete') }}
          </button>`;
        hasActions = true;
      }
      
      // Show/hide the action container based on whether there are actions
      if (hasActions) {
        actionContainer.style.display = 'block';
      } else {
        actionContainer.style.display = 'none';
      }
    }

    function renderLeaveDetails(leave) {
      // First, set up the action buttons at the top
      renderActionButtons(leave);
      
      const container = document.getElementById('leaveDetailsContainer');

      container.innerHTML = `
        <div class="row">
          {{-- Main Content --}}
          <div class="col-md-8">
            {{-- Leave Request Details Card --}}
            <div class="card mb-4">
              <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                  <h5 class="card-title mb-1">{{ __('Leave Request') }} #${leave.id}</h5>
                  <small class="text-muted">{{ __('Submitted') }} ${leave.created_at_human}</small>
                </div>
                <div>
                  <span class="badge bg-${leave.status_color} badge-lg">${leave.status_display}</span>
                </div>
              </div>
              <div class="card-body">
                <div class="row">
                  {{-- Employee Information --}}
                  <div class="col-md-6 mb-4">
                    <h6 class="text-primary mb-3">{{ __('Employee Information') }}</h6>
                    <div class="d-flex align-items-center mb-3">
                      ${leave.user.avatar
                        ? `<img src="${leave.user.avatar}" alt="Avatar" class="rounded-circle me-3" width="50" height="50">`
                        : `<div class="avatar avatar-md me-3">
                             <span class="avatar-initial rounded-circle bg-label-primary">
                               ${leave.user.first_name.charAt(0)}${leave.user.last_name.charAt(0)}
                             </span>
                           </div>`
                      }
                      <div>
                        <h6 class="mb-0">${leave.user.name}</h6>
                        <small class="text-muted">${leave.user.code}${leave.user.designation ? ' | ' + leave.user.designation : ''}</small>
                        ${leave.user.department ? `<br><small class="text-muted">${leave.user.department}</small>` : ''}
                      </div>
                    </div>
                  </div>

                  {{-- Leave Information --}}
                  <div class="col-md-6 mb-4">
                    <h6 class="text-primary mb-3">{{ __('Leave Information') }}</h6>
                    <table class="table table-borderless table-sm">
                      <tr>
                        <td class="text-muted">{{ __('Leave Type') }}:</td>
                        <td><span class="badge bg-label-primary">${leave.leave_type.name}</span></td>
                      </tr>
                      <tr>
                        <td class="text-muted">{{ __('From Date') }}:</td>
                        <td><strong>${leave.from_date_formatted}</strong></td>
                      </tr>
                      <tr>
                        <td class="text-muted">{{ __('To Date') }}:</td>
                        <td><strong>${leave.to_date_formatted}</strong></td>
                      </tr>
                      <tr>
                        <td class="text-muted">{{ __('Duration') }}:</td>
                        <td>
                          ${leave.is_half_day
                            ? `<span class="badge bg-label-info">{{ __('Half Day') }} (${leave.half_day_display})</span>`
                            : `<span class="badge bg-label-info">${leave.total_days} ${leave.total_days == 1 ? 'Day' : 'Days'}</span>`
                          }
                        </td>
                      </tr>
                    </table>
                  </div>
                </div>

                {{-- Reason for Leave --}}
                <div class="mb-4">
                  <h6 class="text-primary mb-2">{{ __('Reason for Leave') }}</h6>
                  <div class="border rounded p-3 bg-light">
                    <p class="mb-0">${leave.user_notes || 'No reason provided'}</p>
                  </div>
                </div>

                ${leave.emergency_contact || leave.emergency_phone ? `
                {{-- Contact Information --}}
                <div class="mb-4">
                  <h6 class="text-primary mb-3">{{ __('Contact Information') }}</h6>
                  <div class="row">
                    ${leave.emergency_contact ? `
                    <div class="col-md-4 mb-2">
                      <small class="text-muted d-block">{{ __('Emergency Contact') }}</small>
                      <strong>${leave.emergency_contact}</strong>
                    </div>
                    ` : ''}
                    ${leave.emergency_phone ? `
                    <div class="col-md-4 mb-2">
                      <small class="text-muted d-block">{{ __('Emergency Phone') }}</small>
                      <strong>${leave.emergency_phone}</strong>
                    </div>
                    ` : ''}
                  </div>
                </div>
                ` : ''}

                ${leave.is_abroad ? `
                {{-- Travel Information --}}
                <div class="mb-4">
                  <h6 class="text-primary mb-2">{{ __('Travel Information') }}</h6>
                  <div class="alert alert-info">
                    <i class="bx bx-plane me-2"></i>
                    <strong>{{ __('Traveling Abroad') }}:</strong> ${leave.abroad_location}
                  </div>
                </div>
                ` : ''}

                ${leave.has_document ? `
                {{-- Supporting Document --}}
                <div class="mb-4">
                  <h6 class="text-primary mb-2">{{ __('Supporting Document') }}</h6>
                  ${leave.document_url ? `
                  <div class="border rounded p-3">
                    <div class="d-flex align-items-center">
                      <i class="bx bx-file-blank me-3 text-primary" style="font-size: 2rem;"></i>
                      <div>
                        <a href="${leave.document_url}" target="_blank" class="text-primary fw-bold">
                          {{ __('View Document') }}
                        </a>
                        <br>
                        <small class="text-muted">{{ __('Click to view in new window') }}</small>
                      </div>
                    </div>
                  </div>
                  ` : `
                  <div class="alert alert-warning">
                    <i class="bx bx-info-circle me-2"></i>
                    {{ __('Document exists but cannot be displayed') }}
                  </div>
                  `}
                </div>
                ` : ''}
              </div>
            </div>

            ${leave.approved_by || leave.rejected_by || leave.cancelled_by ? `
            {{-- Approval Information --}}
            <div class="card mb-4">
              <div class="card-header">
                <h5 class="card-title">{{ __('Approval Information') }}</h5>
              </div>
              <div class="card-body">
                ${leave.approved_by ? `
                  <div class="d-flex align-items-center mb-3">
                    <div class="avatar avatar-sm me-3">
                      <span class="avatar-initial rounded-circle bg-success">
                        <i class="bx bx-check"></i>
                      </span>
                    </div>
                    <div>
                      <h6 class="mb-0">{{ __('Approved by') }} ${leave.approved_by.name}</h6>
                      <small class="text-muted">${leave.approved_at}</small>
                    </div>
                  </div>
                ` : ''}

                ${leave.rejected_by ? `
                  <div class="d-flex align-items-center mb-3">
                    <div class="avatar avatar-sm me-3">
                      <span class="avatar-initial rounded-circle bg-danger">
                        <i class="bx bx-x"></i>
                      </span>
                    </div>
                    <div>
                      <h6 class="mb-0">{{ __('Rejected by') }} ${leave.rejected_by.name}</h6>
                      <small class="text-muted">${leave.rejected_at}</small>
                    </div>
                  </div>
                ` : ''}

                ${leave.cancelled_by ? `
                  <div class="d-flex align-items-center mb-3">
                    <div class="avatar avatar-sm me-3">
                      <span class="avatar-initial rounded-circle bg-secondary">
                        <i class="bx bx-block"></i>
                      </span>
                    </div>
                    <div>
                      <h6 class="mb-0">{{ __('Cancelled by') }} ${leave.cancelled_by.name}</h6>
                      <small class="text-muted">${leave.cancelled_at}</small>
                    </div>
                  </div>
                ` : ''}

                ${leave.approval_notes ? `
                  <div class="mt-3">
                    <h6 class="mb-2">{{ __('Notes') }}</h6>
                    <div class="border rounded p-3 bg-light">
                      <p class="mb-0">${leave.approval_notes}</p>
                    </div>
                  </div>
                ` : ''}

                ${leave.cancellation_reason ? `
                  <div class="mt-3">
                    <h6 class="mb-2">{{ __('Cancellation Reason') }}</h6>
                    <div class="border rounded p-3 bg-light">
                      <p class="mb-0">${leave.cancellation_reason}</p>
                    </div>
                  </div>
                ` : ''}
              </div>
            </div>
            ` : ''}

          </div>

          {{-- Sidebar --}}
          <div class="col-md-4">
            ${leave.balance ? `
            {{-- Leave Balance --}}
            <div class="card mb-4">
              <div class="card-header">
                <h5 class="card-title">{{ __('Leave Balance') }}</h5>
                <small class="text-muted">${leave.leave_type.name}</small>
              </div>
              <div class="card-body">
                <div class="row text-center">
                  <div class="col-4">
                    <div class="border rounded p-2">
                      <h6 class="mb-1 text-primary">${leave.balance.entitled_leaves}</h6>
                      <small class="text-muted">{{ __('Entitled') }}</small>
                    </div>
                  </div>
                  <div class="col-4">
                    <div class="border rounded p-2">
                      <h6 class="mb-1 text-success">${leave.balance.used_leaves}</h6>
                      <small class="text-muted">{{ __('Used') }}</small>
                    </div>
                  </div>
                  <div class="col-4">
                    <div class="border rounded p-2">
                      <h6 class="mb-1 text-info">${leave.balance.available_leaves}</h6>
                      <small class="text-muted">{{ __('Available') }}</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            ` : ''}

            ${leave.leave_history && leave.leave_history.length > 0 ? `
            {{-- Recent Leave History --}}
            <div class="card mb-4">
              <div class="card-header">
                <h5 class="card-title">{{ __('Recent Leave History') }}</h5>
              </div>
              <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                ${leave.leave_history.map(history => `
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <div>
                    <h6 class="mb-0">${history.leave_type}</h6>
                    <small class="text-muted">${history.from_date} - ${history.to_date}</small>
                  </div>
                  <div class="text-end">
                    <span class="badge bg-label-${history.status_color}">${history.status}</span>
                    <br>
                    <small class="text-muted">${history.total_days} days</small>
                  </div>
                </div>
                ${leave.leave_history.indexOf(history) < leave.leave_history.length - 1 ? '<hr>' : ''}
                `).join('')}
              </div>
            </div>
            ` : ''}

            {{-- Quick Stats --}}
            <div class="card">
              <div class="card-header">
                <h5 class="card-title">{{ __('Quick Stats') }}</h5>
              </div>
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="text-muted">{{ __('Request ID') }}</span>
                  <strong>#${leave.id}</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="text-muted">{{ __('Total Days') }}</span>
                  <strong>${leave.total_days}</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="text-muted">{{ __('Submitted') }}</span>
                  <strong>${leave.created_at_human}</strong>
                </div>
                ${leave.approved_at ? `
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="text-muted">{{ __('Approved') }}</span>
                  <strong>${leave.approved_at}</strong>
                </div>
                ` : ''}
              </div>
            </div>
          </div>
        </div>
      `;
    }

    function showError(message) {
      const container = document.getElementById('leaveDetailsContainer');
      container.innerHTML = `
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <div class="alert alert-danger mb-0">
                <i class="bx bx-error me-2"></i>
                ${message}
              </div>
            </div>
          </div>
        </div>
      `;
    }

    // Action functions
    function approveLeaveRequest(id) {
      Swal.fire({
        title: pageData.labels.confirmApprove,
        input: 'textarea',
        inputLabel: '{{ __("Approval Notes") }}',
        inputPlaceholder: pageData.labels.enterNotes,
        showCancelButton: true,
        confirmButtonText: pageData.labels.approve,
        cancelButtonText: pageData.labels.no,
        customClass: {
          confirmButton: 'btn btn-success me-3',
          cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false
      }).then((result) => {
        if (result.isConfirmed) {
          performAction('approve', id, { notes: result.value });
        }
      });
    }

    function rejectLeaveRequest(id) {
      Swal.fire({
        title: pageData.labels.confirmReject,
        input: 'textarea',
        inputLabel: '{{ __("Reason") }}',
        inputPlaceholder: pageData.labels.enterReason,
        inputValidator: (value) => {
          if (!value) {
            return pageData.labels.reasonRequired;
          }
        },
        showCancelButton: true,
        confirmButtonText: pageData.labels.reject,
        cancelButtonText: pageData.labels.no,
        customClass: {
          confirmButton: 'btn btn-danger me-3',
          cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false
      }).then((result) => {
        if (result.isConfirmed) {
          performAction('reject', id, { reason: result.value });
        }
      });
    }

    function cancelLeaveRequest(id) {
      Swal.fire({
        title: pageData.labels.confirmCancel,
        input: 'textarea',
        inputLabel: '{{ __("Cancellation Reason") }}',
        inputPlaceholder: pageData.labels.enterReason,
        showCancelButton: true,
        confirmButtonText: pageData.labels.yes,
        cancelButtonText: pageData.labels.no,
        customClass: {
          confirmButton: 'btn btn-warning me-3',
          cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false
      }).then((result) => {
        if (result.isConfirmed) {
          performAction('cancel', id, { reason: result.value });
        }
      });
    }

    function deleteLeaveRequest(id) {
      Swal.fire({
        title: pageData.labels.confirmDelete,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: pageData.labels.delete,
        cancelButtonText: pageData.labels.no,
        customClass: {
          confirmButton: 'btn btn-danger me-3',
          cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false
      }).then((result) => {
        if (result.isConfirmed) {
          performAction('destroy', id, {}, 'DELETE');
        }
      });
    }

    function performAction(action, id, data = {}, method = 'POST') {
      const url = pageData.urls[action]?.replace(':id', id);
      if (!url) return;

      fetch(url, {
        method: method,
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          Swal.fire({
            icon: 'success',
            title: pageData.labels.success,
            text: data.message || data.data?.message || 'Action completed successfully',
            customClass: {
              confirmButton: 'btn btn-success'
            },
            buttonsStyling: false
          }).then(() => {
            if (action === 'destroy') {
              window.location.href = pageData.urls.index;
            } else {
              loadLeaveDetails(id);
            }
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: pageData.labels.error,
            text: data.message || data.data || 'Action failed',
            customClass: {
              confirmButton: 'btn btn-danger'
            },
            buttonsStyling: false
          });
        }
      })
      .catch(error => {
        console.error('Action error:', error);
        Swal.fire({
          icon: 'error',
          title: pageData.labels.error,
          text: 'Something went wrong. Please try again.',
          customClass: {
            confirmButton: 'btn btn-danger'
          },
          buttonsStyling: false
        });
      });
    }

  </script>
@endsection
