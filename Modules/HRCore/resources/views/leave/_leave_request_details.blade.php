<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasLeaveDetails" aria-labelledby="offcanvasLeaveDetailsLabel">
  <div class="offcanvas-header border-bottom">
    <h5 id="offcanvasLeaveDetailsLabel" class="offcanvas-title">@lang('Leave Request Details')</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
    <div id="leaveDetailsContent">
      <div class="text-center py-4">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">@lang('Loading...')</span>
        </div>
        <div class="mt-2">@lang('Loading leave details...')</div>
      </div>
    </div>

    {{-- Action buttons --}}
    <div class="mt-6 d-flex flex-wrap gap-2" id="leaveActionButtons">
      @can('hrcore.approve-leave')
        <button type="button" class="btn btn-success leave-action-btn" id="approveBtn" style="display: none;" data-action="approve">
          <i class="bx bx-check me-1"></i>@lang('Approve')
        </button>
      @endcan

      @can('hrcore.approve-leave')
        <button type="button" class="btn btn-danger leave-action-btn" id="rejectBtn" style="display: none;" data-action="reject">
          <i class="bx bx-x me-1"></i>@lang('Reject')
        </button>
      @endcan

      @can('hrcore.cancel-leave')
        <button type="button" class="btn btn-warning leave-action-btn" id="cancelBtn" style="display: none;" data-action="cancel">
          <i class="bx bx-block me-1"></i>@lang('Cancel')
        </button>
      @endcan

      @can('hrcore.edit-leave')
        <button type="button" class="btn btn-primary leave-action-btn" id="editBtn" style="display: none;" data-action="edit">
          <i class="bx bx-edit me-1"></i>@lang('Edit')
        </button>
      @endcan

      @can('hrcore.delete-leave')
        <button type="button" class="btn btn-outline-danger leave-action-btn" id="deleteBtn" style="display: none;" data-action="delete">
          <i class="bx bx-trash me-1"></i>@lang('Delete')
        </button>
      @endcan
    </div>
  </div>
</div>

<script>
// Enhanced leave details loader
document.addEventListener('DOMContentLoaded', function() {
  // Add missing labels to pageData
  if (typeof pageData !== 'undefined') {
    pageData.labels = pageData.labels || {};
    Object.assign(pageData.labels, {
      employee: @json(__('Employee')),
      leaveType: @json(__('Leave Type')),
      dates: @json(__('Dates')),
      status: @json(__('Status')),
      reason: @json(__('Reason')),
      approvalNotes: @json(__('Approval Notes')),
      document: @json(__('Document')),
      requestedOn: @json(__('Requested On')),
      approvedBy: @json(__('Approved By')),
      rejectedBy: @json(__('Rejected By')),
      cancelledBy: @json(__('Cancelled By')),
      totalDays: @json(__('Total Days')),
      duration: @json(__('Duration')),
      emergencyContact: @json(__('Emergency Contact')),
      emergencyPhone: @json(__('Emergency Phone')),
      contactDuringLeave: @json(__('Contact During Leave')),
      travelingAbroad: @json(__('Traveling Abroad')),
      location: @json(__('Location')),
      leaveBalance: @json(__('Leave Balance')),
      entitled: @json(__('Entitled')),
      used: @json(__('Used')),
      available: @json(__('Available')),
      pending: @json(__('Pending')),
      recentHistory: @json(__('Recent Leave History')),
      noHistory: @json(__('No recent leave history')),
      viewDocument: @json(__('View Document')),
      noDocument: @json(__('No document attached')),
      confirmApprove: @json(__('Are you sure you want to approve this leave request?')),
      confirmReject: @json(__('Are you sure you want to reject this leave request?')),
      confirmCancel: @json(__('Are you sure you want to cancel this leave request?')),
      confirmDelete: @json(__('Are you sure you want to delete this leave request? This action cannot be undone!')),
      enterNotes: @json(__('Enter approval notes (optional)')),
      enterReason: @json(__('Enter reason for rejection')),
      enterCancelReason: @json(__('Enter cancellation reason (optional)')),
      reasonRequired: @json(__('Reason is required for rejection')),
      approve: @json(__('Approve')),
      reject: @json(__('Reject')),
      cancel: @json(__('Cancel')),
      delete: @json(__('Delete')),
      edit: @json(__('Edit')),
      yes: @json(__('Yes')),
      no: @json(__('No')),
      success: @json(__('Success')),
      error: @json(__('Error')),
      processing: @json(__('Processing...')),
      somethingWentWrong: @json(__('Something went wrong. Please try again.'))
    });
  }
});

// Function to load leave details
window.loadLeaveDetails = function(leaveId) {
  const contentDiv = document.getElementById('leaveDetailsContent');
  const actionButtons = document.getElementById('leaveActionButtons');
  
  // Hide all action buttons initially
  actionButtons.querySelectorAll('.leave-action-btn').forEach(btn => {
    btn.style.display = 'none';
  });
  
  // Show loading state
  contentDiv.innerHTML = `
    <div class="text-center py-4">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">${pageData.labels?.processing || 'Loading...'}</span>
      </div>
      <div class="mt-2">${pageData.labels?.processing || 'Loading leave details...'}</div>
    </div>
  `;
  
  // Fetch leave details
  fetch(pageData.urls.show.replace(':id', leaveId), {
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
      setupActionButtons(data.data);
    } else {
      showError(data.message || pageData.labels?.error || 'Failed to load leave details');
    }
  })
  .catch(error => {
    console.error('Error loading leave details:', error);
    showError(pageData.labels?.somethingWentWrong || 'Something went wrong. Please try again.');
  });
};

// Function to render comprehensive leave details
function renderLeaveDetails(leave) {
  const contentDiv = document.getElementById('leaveDetailsContent');
  
  contentDiv.innerHTML = `
    <!-- Leave Request Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
      <div>
        <h6 class="mb-1">${pageData.labels?.employee || 'Employee'}</h6>
        <div class="d-flex align-items-center">
          ${leave.user.avatar 
            ? `<img src="${leave.user.avatar}" alt="Avatar" class="rounded-circle me-2" width="32" height="32">`
            : `<div class="avatar avatar-sm me-2">
                 <span class="avatar-initial rounded-circle bg-label-primary">
                   ${leave.user.first_name.charAt(0)}${leave.user.last_name.charAt(0)}
                 </span>
               </div>`
          }
          <div>
            <div class="fw-semibold">${leave.user.name}</div>
            <small class="text-muted">${leave.user.code}${leave.user.department ? ' • ' + leave.user.department : ''}</small>
          </div>
        </div>
      </div>
      <span class="badge bg-${leave.status_color} badge-lg">${leave.status_display}</span>
    </div>

    <!-- Leave Information -->
    <div class="mb-4">
      <h6 class="mb-3">${pageData.labels?.leaveType || 'Leave Information'}</h6>
      <div class="row g-3">
        <div class="col-6">
          <label class="form-label text-muted small">${pageData.labels?.leaveType || 'Leave Type'}</label>
          <div>
            <span class="badge bg-label-primary" style="background-color: ${leave.leave_type.color}15 !important; color: ${leave.leave_type.color} !important;">
              ${leave.leave_type.name}
            </span>
          </div>
        </div>
        <div class="col-6">
          <label class="form-label text-muted small">${pageData.labels?.totalDays || 'Total Days'}</label>
          <div class="fw-semibold">
            ${leave.is_half_day 
              ? `<span class="badge bg-label-info">Half Day (${leave.half_day_display})</span>`
              : `${leave.total_days} ${leave.total_days == 1 ? 'Day' : 'Days'}`
            }
          </div>
        </div>
        <div class="col-12">
          <label class="form-label text-muted small">${pageData.labels?.dates || 'Dates'}</label>
          <div class="fw-semibold">
            ${leave.from_date_formatted === leave.to_date_formatted 
              ? leave.from_date_formatted
              : `${leave.from_date_formatted} - ${leave.to_date_formatted}`
            }
          </div>
        </div>
      </div>
    </div>

    <!-- Leave Reason -->
    <div class="mb-4">
      <h6 class="mb-2">${pageData.labels?.reason || 'Reason for Leave'}</h6>
      <div class="border rounded p-3 bg-light">
        <p class="mb-0">${leave.user_notes || 'No reason provided'}</p>
      </div>
    </div>

    ${leave.emergency_contact || leave.emergency_phone || leave.contact_during_leave ? `
    <!-- Contact Information -->
    <div class="mb-4">
      <h6 class="mb-3">${pageData.labels?.emergencyContact || 'Contact Information'}</h6>
      <div class="row g-2">
        ${leave.emergency_contact ? `
        <div class="col-12">
          <small class="text-muted">${pageData.labels?.emergencyContact || 'Emergency Contact'}</small>
          <div class="fw-semibold">${leave.emergency_contact}</div>
        </div>
        ` : ''}
        ${leave.emergency_phone ? `
        <div class="col-12">
          <small class="text-muted">${pageData.labels?.emergencyPhone || 'Emergency Phone'}</small>
          <div class="fw-semibold">${leave.emergency_phone}</div>
        </div>
        ` : ''}
        ${leave.contact_during_leave ? `
        <div class="col-12">
          <small class="text-muted">${pageData.labels?.contactDuringLeave || 'Contact During Leave'}</small>
          <div class="fw-semibold">${leave.contact_during_leave}</div>
        </div>
        ` : ''}
      </div>
    </div>
    ` : ''}

    ${leave.is_abroad ? `
    <!-- Travel Information -->
    <div class="mb-4">
      <h6 class="mb-2">${pageData.labels?.travelingAbroad || 'Travel Information'}</h6>
      <div class="alert alert-info">
        <i class="bx bx-plane me-2"></i>
        <strong>${pageData.labels?.travelingAbroad || 'Traveling Abroad'}:</strong> ${leave.abroad_location}
      </div>
    </div>
    ` : ''}

    ${leave.has_document ? `
    <!-- Document -->
    <div class="mb-4">
      <h6 class="mb-2">${pageData.labels?.document || 'Supporting Document'}</h6>
      ${leave.document_url ? `
      <div class="border rounded p-3">
        <div class="d-flex align-items-center">
          <i class="bx bx-file-blank me-3 text-primary" style="font-size: 2rem;"></i>
          <div>
            <a href="${leave.document_url}" target="_blank" class="text-primary fw-bold">
              ${pageData.labels?.viewDocument || 'View Document'}
            </a>
            <br>
            <small class="text-muted">Click to view in new window</small>
          </div>
        </div>
      </div>
      ` : `
      <div class="alert alert-warning">
        <i class="bx bx-info-circle me-2"></i>
        Document exists but cannot be displayed
      </div>
      `}
    </div>
    ` : ''}

    ${leave.approved_by || leave.rejected_by || leave.cancelled_by ? `
    <!-- Approval Information -->
    <div class="mb-4">
      <h6 class="mb-3">Approval Information</h6>
      ${leave.approved_by ? `
      <div class="d-flex align-items-center mb-3">
        <div class="avatar avatar-sm me-3">
          <span class="avatar-initial rounded-circle bg-success">
            <i class="bx bx-check"></i>
          </span>
        </div>
        <div>
          <div class="fw-semibold">${pageData.labels?.approvedBy || 'Approved by'} ${leave.approved_by.name}</div>
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
          <div class="fw-semibold">${pageData.labels?.rejectedBy || 'Rejected by'} ${leave.rejected_by.name}</div>
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
          <div class="fw-semibold">${pageData.labels?.cancelledBy || 'Cancelled by'} ${leave.cancelled_by.name}</div>
          <small class="text-muted">${leave.cancelled_at}</small>
        </div>
      </div>
      ` : ''}
      ${leave.approval_notes ? `
      <div class="mt-3">
        <small class="text-muted">${pageData.labels?.approvalNotes || 'Notes'}</small>
        <div class="border rounded p-2 bg-light mt-1">
          <small>${leave.approval_notes}</small>
        </div>
      </div>
      ` : ''}
      ${leave.cancellation_reason ? `
      <div class="mt-3">
        <small class="text-muted">Cancellation Reason</small>
        <div class="border rounded p-2 bg-light mt-1">
          <small>${leave.cancellation_reason}</small>
        </div>
      </div>
      ` : ''}
    </div>
    ` : ''}

    ${leave.balance ? `
    <!-- Leave Balance -->
    <div class="mb-4">
      <h6 class="mb-3">${pageData.labels?.leaveBalance || 'Leave Balance'} (${leave.leave_type.name})</h6>
      <div class="row text-center">
        <div class="col-3">
          <div class="border rounded p-2">
            <div class="fw-bold text-primary">${leave.balance.entitled_leaves}</div>
            <small class="text-muted">${pageData.labels?.entitled || 'Entitled'}</small>
          </div>
        </div>
        <div class="col-3">
          <div class="border rounded p-2">
            <div class="fw-bold text-success">${leave.balance.used_leaves}</div>
            <small class="text-muted">${pageData.labels?.used || 'Used'}</small>
          </div>
        </div>
        <div class="col-3">
          <div class="border rounded p-2">
            <div class="fw-bold text-warning">${leave.balance.pending_leaves}</div>
            <small class="text-muted">${pageData.labels?.pending || 'Pending'}</small>
          </div>
        </div>
        <div class="col-3">
          <div class="border rounded p-2">
            <div class="fw-bold text-info">${leave.balance.available_leaves}</div>
            <small class="text-muted">${pageData.labels?.available || 'Available'}</small>
          </div>
        </div>
      </div>
    </div>
    ` : ''}

    ${leave.leave_history && leave.leave_history.length > 0 ? `
    <!-- Recent Leave History -->
    <div class="mb-4">
      <h6 class="mb-3">${pageData.labels?.recentHistory || 'Recent Leave History'}</h6>
      <div style="max-height: 200px; overflow-y: auto;">
        ${leave.leave_history.map(history => `
        <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
          <div>
            <div class="fw-semibold small">${history.leave_type}</div>
            <small class="text-muted">${history.from_date} - ${history.to_date}</small>
          </div>
          <div class="text-end">
            <span class="badge bg-label-${history.status_color}">${history.status}</span>
            <br>
            <small class="text-muted">${history.total_days} days</small>
          </div>
        </div>
        `).join('')}
      </div>
    </div>
    ` : ''}

    <!-- Request Information -->
    <div class="mb-0">
      <h6 class="mb-3">Request Information</h6>
      <div class="row g-2">
        <div class="col-6">
          <small class="text-muted">Request ID</small>
          <div class="fw-semibold">#${leave.id}</div>
        </div>
        <div class="col-6">
          <small class="text-muted">Submitted</small>
          <div class="fw-semibold">${leave.created_at_human}</div>
        </div>
        <div class="col-12">
          <small class="text-muted">Submitted On</small>
          <div class="fw-semibold">${leave.created_at}</div>
        </div>
      </div>
    </div>
  `;
}

// Function to setup action buttons based on permissions
function setupActionButtons(leave) {
  const approveBtn = document.getElementById('approveBtn');
  const rejectBtn = document.getElementById('rejectBtn');
  const cancelBtn = document.getElementById('cancelBtn');
  const editBtn = document.getElementById('editBtn');
  const deleteBtn = document.getElementById('deleteBtn');
  
  // Show/hide buttons based on permissions and status
  if (approveBtn && leave.can_approve && leave.status === 'pending') {
    approveBtn.style.display = 'inline-block';
    approveBtn.onclick = () => approveLeave(leave.id);
  }
  
  if (rejectBtn && leave.can_reject && leave.status === 'pending') {
    rejectBtn.style.display = 'inline-block';
    rejectBtn.onclick = () => rejectLeave(leave.id);
  }
  
  if (cancelBtn && leave.can_cancel) {
    cancelBtn.style.display = 'inline-block';
    cancelBtn.onclick = () => cancelLeave(leave.id);
  }
  
  if (editBtn && leave.can_edit) {
    editBtn.style.display = 'inline-block';
    editBtn.onclick = () => editLeave(leave.id);
  }
  
  if (deleteBtn && leave.can_delete) {
    deleteBtn.style.display = 'inline-block';
    deleteBtn.onclick = () => deleteLeave(leave.id);
  }
}

// Function to show error message
function showError(message) {
  const contentDiv = document.getElementById('leaveDetailsContent');
  contentDiv.innerHTML = `
    <div class="alert alert-danger">
      <i class="bx bx-error me-2"></i>
      ${message}
    </div>
  `;
}

// Action functions
function approveLeave(id) {
  Swal.fire({
    title: pageData.labels?.confirmApprove || 'Are you sure you want to approve this leave request?',
    input: 'textarea',
    inputLabel: pageData.labels?.approvalNotes || 'Approval Notes',
    inputPlaceholder: pageData.labels?.enterNotes || 'Enter approval notes (optional)',
    showCancelButton: true,
    confirmButtonText: pageData.labels?.approve || 'Approve',
    cancelButtonText: pageData.labels?.no || 'Cancel',
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

function rejectLeave(id) {
  Swal.fire({
    title: pageData.labels?.confirmReject || 'Are you sure you want to reject this leave request?',
    input: 'textarea',
    inputLabel: pageData.labels?.reason || 'Reason',
    inputPlaceholder: pageData.labels?.enterReason || 'Enter reason for rejection',
    inputValidator: (value) => {
      if (!value) {
        return pageData.labels?.reasonRequired || 'Reason is required for rejection';
      }
    },
    showCancelButton: true,
    confirmButtonText: pageData.labels?.reject || 'Reject',
    cancelButtonText: pageData.labels?.no || 'Cancel',
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

function cancelLeave(id) {
  Swal.fire({
    title: pageData.labels?.confirmCancel || 'Are you sure you want to cancel this leave request?',
    input: 'textarea',
    inputLabel: 'Cancellation Reason',
    inputPlaceholder: pageData.labels?.enterCancelReason || 'Enter cancellation reason (optional)',
    showCancelButton: true,
    confirmButtonText: pageData.labels?.yes || 'Yes, Cancel',
    cancelButtonText: pageData.labels?.no || 'No',
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

function editLeave(id) {
  // Redirect to edit page
  if (pageData.urls?.edit) {
    window.location.href = pageData.urls.edit.replace(':id', id);
  }
}

function deleteLeave(id) {
  Swal.fire({
    title: pageData.labels?.confirmDelete || 'Are you sure you want to delete this leave request?',
    text: 'This action cannot be undone!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: pageData.labels?.delete || 'Delete',
    cancelButtonText: pageData.labels?.no || 'Cancel',
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

// Generic function to perform actions
function performAction(action, id, data = {}, method = 'POST') {
  const url = pageData.urls?.[action]?.replace(':id', id);
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
        title: pageData.labels?.success || 'Success',
        text: data.message || data.data?.message || 'Action completed successfully',
        customClass: {
          confirmButton: 'btn btn-success'
        },
        buttonsStyling: false
      }).then(() => {
        // Reload the details
        loadLeaveDetails(id);
        
        // Reload the DataTable if it exists
        if (typeof window.reloadLeaveTable === 'function') {
          window.reloadLeaveTable();
        }
        
        // If deleting, close the offcanvas
        if (action === 'destroy') {
          bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasLeaveDetails')).hide();
        }
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: pageData.labels?.error || 'Error',
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
      title: pageData.labels?.error || 'Error',
      text: pageData.labels?.somethingWentWrong || 'Something went wrong. Please try again.',
      customClass: {
        confirmButton: 'btn btn-danger'
      },
      buttonsStyling: false
    });
  });
}
</script>