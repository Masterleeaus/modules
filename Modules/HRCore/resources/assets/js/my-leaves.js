$(function() {
  // Initialize DataTable
  const table = $('#leavesTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: pageData.urls.datatable,
      data: function(d) {
        d.status = $('#filterStatus').val();
        d.leave_type_id = $('#filterLeaveType').val();
        d.date_from = $('#filterDateFrom').val();
        d.date_to = $('#filterDateTo').val();
      }
    },
    columns: [
      { data: 'created_at', name: 'created_at' },
      { data: 'leave_type', name: 'leave_type' },
      { data: 'from_date', name: 'from_date' },
      { data: 'to_date', name: 'to_date' },
      { data: 'total_days', name: 'total_days' },
      { data: 'status', name: 'status' },
      { data: 'actions', name: 'actions', orderable: false, searchable: false }
    ],
    order: [[0, 'desc']],
    dom: '<"card-header d-flex flex-wrap"<"me-5 ms-n2"f><"d-flex justify-content-start justify-content-md-end align-items-baseline gap-2"<"dt-action-buttons"B>l>>t<"row mx-1"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
    displayLength: 10,
    lengthMenu: [10, 25, 50, 100],
    buttons: []
  });

  // Apply filters
  $('#filterStatus, #filterLeaveType, #filterDateFrom, #filterDateTo').on('change', function() {
    table.ajax.reload();
  });
});

// View leave details
function viewMyLeave(id) {
  // Show offcanvas
  const offcanvasElement = document.getElementById('leaveDetailsOffcanvas');
  const offcanvas = new bootstrap.Offcanvas(offcanvasElement);
  offcanvas.show();
  
  // Reset content to loading
  document.getElementById('leaveDetailsContent').innerHTML = `
    <div class="text-center">
      <div class="spinner-border spinner-border-sm" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
  `;
  
  // Fetch leave details
  fetch(pageData.urls.show.replace('__ID__', id), {
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.status === 'success') {
      const leave = data.data.leave_request;
      const leaveType = data.data.leave_type;
      
      // Format status badge
      const statusBadge = {
        'pending': 'bg-label-warning',
        'approved': 'bg-label-success',
        'rejected': 'bg-label-danger',
        'cancelled': 'bg-label-secondary'
      };
      
      // Build the content HTML
      const content = `
        <div class="leave-details">
          <!-- Status -->
          <div class="mb-4 text-center">
            <span class="badge ${statusBadge[leave.status] || 'bg-label-primary'} fs-6">
              ${leave.status ? leave.status.toUpperCase() : ''}
            </span>
          </div>
          
          <!-- Leave Type -->
          <div class="mb-3">
            <label class="form-label text-muted small">Leave Type</label>
            <p class="mb-0 fw-semibold">${leaveType.name}</p>
          </div>
          
          <!-- Date Range -->
          <div class="row mb-3">
            <div class="col-6">
              <label class="form-label text-muted small">From Date</label>
              <p class="mb-0">${new Date(leave.from_date).toLocaleDateString()}</p>
            </div>
            <div class="col-6">
              <label class="form-label text-muted small">To Date</label>
              <p class="mb-0">${new Date(leave.to_date).toLocaleDateString()}</p>
            </div>
          </div>
          
          <!-- Total Days -->
          <div class="mb-3">
            <label class="form-label text-muted small">Total Days</label>
            <p class="mb-0">
              <span class="badge bg-label-primary">${leave.total_days} days</span>
              ${leave.is_half_day ? '<span class="badge bg-label-info ms-1">Half Day</span>' : ''}
            </p>
          </div>
          
          <!-- Reason -->
          <div class="mb-3">
            <label class="form-label text-muted small">Reason for Leave</label>
            <p class="mb-0">${leave.user_notes || 'N/A'}</p>
          </div>
          
          <!-- Emergency Contact -->
          ${leave.emergency_contact ? `
          <div class="mb-3">
            <label class="form-label text-muted small">Emergency Contact</label>
            <p class="mb-0">${leave.emergency_contact} ${leave.emergency_phone ? '(' + leave.emergency_phone + ')' : ''}</p>
          </div>
          ` : ''}
          
          <!-- Traveling Abroad -->
          ${leave.is_abroad ? `
          <div class="mb-3">
            <label class="form-label text-muted small">Traveling Location</label>
            <p class="mb-0">${leave.abroad_location || 'N/A'}</p>
          </div>
          ` : ''}
          
          <!-- Applied On -->
          <div class="mb-3">
            <label class="form-label text-muted small">Applied On</label>
            <p class="mb-0">${new Date(leave.created_at).toLocaleString()}</p>
          </div>
          
          <!-- Approved/Rejected By -->
          ${leave.approved_by_id ? `
          <div class="mb-3">
            <label class="form-label text-muted small">
              ${leave.status === 'approved' ? 'Approved By' : 'Rejected By'}
            </label>
            <p class="mb-0">${data.data.approved_by ? data.data.approved_by.name : 'N/A'}</p>
          </div>
          ` : ''}
          
          <!-- Admin Notes -->
          ${leave.admin_notes ? `
          <div class="mb-3">
            <label class="form-label text-muted small">Admin Notes</label>
            <p class="mb-0">${leave.admin_notes}</p>
          </div>
          ` : ''}
          
          <!-- Actions -->
          ${leave.status === 'pending' ? `
          <div class="d-grid gap-2">
            <button type="button" class="btn btn-danger" onclick="cancelMyLeave(${leave.id}); bootstrap.Offcanvas.getInstance(document.getElementById('leaveDetailsOffcanvas')).hide();">
              <i class="bx bx-x me-1"></i> Cancel Request
            </button>
          </div>
          ` : ''}
        </div>
      `;
      
      document.getElementById('leaveDetailsContent').innerHTML = content;
    }
  })
  .catch(error => {
    console.error('Error fetching leave details:', error);
    document.getElementById('leaveDetailsContent').innerHTML = `
      <div class="alert alert-danger">
        <i class="bx bx-error-circle me-1"></i>
        Failed to load leave details. Please try again.
      </div>
    `;
  });
}

// Cancel leave request
function cancelMyLeave(id) {
  Swal.fire({
    title: pageData.labels.cancelTitle,
    text: pageData.labels.confirmCancel,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: pageData.labels.cancelButton,
    cancelButtonText: pageData.labels.cancelButtonText,
    customClass: {
      confirmButton: 'btn btn-danger me-3',
      cancelButton: 'btn btn-label-secondary'
    },
    buttonsStyling: false
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: pageData.urls.cancel.replace('__ID__', id),
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          if (response.status === 'success') {
            Swal.fire({
              icon: 'success',
              title: pageData.labels.success,
              text: pageData.labels.cancelled,
              customClass: {
                confirmButton: 'btn btn-success'
              },
              buttonsStyling: false
            });
            $('#leavesTable').DataTable().ajax.reload();
          }
        },
        error: function(xhr) {
          Swal.fire({
            icon: 'error',
            title: pageData.labels.error,
            text: xhr.responseJSON?.message || 'Failed to cancel leave request',
            customClass: {
              confirmButton: 'btn btn-danger'
            },
            buttonsStyling: false
          });
        }
      });
    }
  });
}

// Make functions available globally
window.viewMyLeave = viewMyLeave;
window.cancelMyLeave = cancelMyLeave;