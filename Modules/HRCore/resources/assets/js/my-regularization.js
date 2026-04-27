/**
 * My Attendance Regularization
 */

'use strict';

$(function() {
  // Get CSRF token
  const csrfToken = $('meta[name="csrf-token"]').attr('content');

  // Initialize DataTable
  const table = $('#myRegularizationTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: pageData.urls.datatable,
    columns: [
      { data: 'date', name: 'date' },
      { data: 'type', name: 'type' },
      { data: 'requested_times', name: 'requested_times' },
      { data: 'reason', name: 'reason' },
      { data: 'status', name: 'status' },
      { data: 'approved_by', name: 'approved_by' },
      { data: 'actions', name: 'actions', orderable: false, searchable: false }
    ],
    order: [[0, 'desc']],
    dom: '<"card-header d-flex flex-wrap"<"me-5 ms-n2"f><"d-flex justify-content-start justify-content-md-end align-items-baseline gap-2"<"dt-action-buttons"B>l>>t<"row mx-1"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
    displayLength: 10,
    lengthMenu: [10, 25, 50, 100],
    buttons: [],
    language: {
      searchPlaceholder: pageData.labels.search || 'Search...'
    }
  });

  // Handle form submission (create or update)
  $('#regularizationForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = $(this).find('button[type="submit"]');
    const updateId = $(this).attr('data-update-id');
    
    // Disable submit button
    submitBtn.prop('disabled', true);
    
    let url = pageData.urls.store;
    
    // If updating, change URL and add PUT method
    if (updateId) {
      url = pageData.urls.update.replace(':id', updateId);
      formData.append('_method', 'PUT');
    }
    
    $.ajax({
      url: url,
      method: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      headers: {
        'X-CSRF-TOKEN': csrfToken
      },
      success: function(response) {
        if (response.status === 'success') {
          Swal.fire({
            icon: 'success',
            title: pageData.labels.success,
            text: response.data.message
          });
          
          // Reset form and remove update ID
          $('#regularizationForm')[0].reset();
          $('#regularizationForm').removeAttr('data-update-id');
          
          // Update offcanvas title back to "New"
          $('#addRegularizationOffcanvas .offcanvas-title').text('New Regularization Request');
          
          const offcanvasEl = document.getElementById('addRegularizationOffcanvas');
          const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
          if (offcanvas) {
            offcanvas.hide();
          }
          
          // Reload table
          table.ajax.reload();
        }
      },
      error: function(xhr) {
        let message = pageData.labels.errorOccurred;
        if (xhr.responseJSON) {
          if (xhr.responseJSON.data) {
            message = xhr.responseJSON.data;
          } else if (xhr.responseJSON.message) {
            message = xhr.responseJSON.message;
          } else if (xhr.responseJSON.errors) {
            // Handle validation errors
            const errors = xhr.responseJSON.errors;
            message = Object.values(errors).flat().join('\n');
          }
        }
        
        Swal.fire({
          icon: 'error',
          title: pageData.labels.error,
          text: message
        });
      },
      complete: function() {
        // Re-enable submit button
        submitBtn.prop('disabled', false);
      }
    });
  });

  // View regularization details
  window.viewMyRegularization = function(id) {
    $.ajax({
      url: pageData.urls.show.replace(':id', id),
      method: 'GET',
      headers: {
        'X-CSRF-TOKEN': csrfToken
      },
      success: function(response) {
        if (response.status === 'success' && response.data) {
          // You can show this in a modal or redirect to detail page
          showRegularizationDetails(response.data.regularization);
        }
      },
      error: function(xhr) {
        Swal.fire({
          icon: 'error',
          title: pageData.labels.error,
          text: pageData.labels.failedToLoadDetails
        });
      }
    });
  };

  // Edit regularization (only for pending)
  window.editMyRegularization = function(id) {
    $.ajax({
      url: pageData.urls.edit.replace(':id', id),
      method: 'GET',
      headers: {
        'X-CSRF-TOKEN': csrfToken
      },
      success: function(response) {
        if (response.status === 'success' && response.data) {
          console.log('Edit data received:', response.data); // Debug log
          // Populate form with data
          populateEditForm(response.data);
          
          // Open offcanvas
          const offcanvasEl = document.getElementById('addRegularizationOffcanvas');
          const offcanvas = new bootstrap.Offcanvas(offcanvasEl);
          offcanvas.show();
        }
      },
      error: function(xhr) {
        Swal.fire({
          icon: 'error',
          title: pageData.labels.error,
          text: pageData.labels.failedToLoadData
        });
      }
    });
  };

  // Delete regularization (only for pending)
  window.deleteMyRegularization = function(id) {
    Swal.fire({
      title: pageData.labels.areYouSure,
      text: pageData.labels.deleteWarning,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: pageData.labels.yesDelete,
      cancelButtonText: pageData.labels.cancel,
      customClass: {
        confirmButton: 'btn btn-danger me-3',
        cancelButton: 'btn btn-label-secondary'
      },
      buttonsStyling: false
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: pageData.urls.delete.replace(':id', id),
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': csrfToken
          },
          success: function(response) {
            if (response.status === 'success') {
              Swal.fire({
                icon: 'success',
                title: pageData.labels.deleted,
                text: response.data.message,
                customClass: {
                  confirmButton: 'btn btn-success'
                },
                buttonsStyling: false
              });
              table.ajax.reload();
            }
          },
          error: function(xhr) {
            Swal.fire({
              icon: 'error',
              title: pageData.labels.error,
              text: pageData.labels.failedToDelete,
              customClass: {
                confirmButton: 'btn btn-success'
              },
              buttonsStyling: false
            });
          }
        });
      }
    });
  };

  // Helper function to show regularization details
  function showRegularizationDetails(regularization) {
    // Format the details
    let statusBadge = '';
    switch(regularization.status) {
      case 'pending':
        statusBadge = '<span class="badge bg-label-warning">Pending</span>';
        break;
      case 'approved':
        statusBadge = '<span class="badge bg-label-success">Approved</span>';
        break;
      case 'rejected':
        statusBadge = '<span class="badge bg-label-danger">Rejected</span>';
        break;
      default:
        statusBadge = '<span class="badge bg-label-secondary">' + regularization.status + '</span>';
    }
    
    // Format type display
    let typeDisplay = regularization.type || '-';
    if (typeDisplay === 'missing_checkin') typeDisplay = 'Missing Check-in';
    else if (typeDisplay === 'missing_checkout') typeDisplay = 'Missing Check-out';
    else if (typeDisplay === 'wrong_time') typeDisplay = 'Wrong Time';
    else if (typeDisplay === 'forgot_punch') typeDisplay = 'Forgot to Punch';
    else if (typeDisplay === 'other') typeDisplay = 'Other';
    
    let detailsHtml = `
      <div class="mb-3">
        <label class="text-muted small">Date</label>
        <p class="mb-2">${regularization.date || '-'}</p>
      </div>
      
      <div class="mb-3">
        <label class="text-muted small">Type</label>
        <p class="mb-2">${typeDisplay}</p>
      </div>
      
      <div class="mb-3">
        <label class="text-muted small">Requested Check-in Time</label>
        <p class="mb-2">${regularization.requested_check_in_time || '-'}</p>
      </div>
      
      <div class="mb-3">
        <label class="text-muted small">Requested Check-out Time</label>
        <p class="mb-2">${regularization.requested_check_out_time || '-'}</p>
      </div>
      
      <div class="mb-3">
        <label class="text-muted small">Reason</label>
        <p class="mb-2">${regularization.reason || '-'}</p>
      </div>
      
      <div class="mb-3">
        <label class="text-muted small">Status</label>
        <p class="mb-2">${statusBadge}</p>
      </div>
      
      <div class="mb-3">
        <label class="text-muted small">Approved By</label>
        <p class="mb-2">${regularization.approved_by_name || '-'}</p>
      </div>
      
      <div class="mb-3">
        <label class="text-muted small">Admin Notes</label>
        <p class="mb-2">${regularization.admin_notes || '-'}</p>
      </div>
    `;
    
    // Populate the offcanvas content
    $('#regularizationDetailsContent').html(detailsHtml);
    
    // Show the offcanvas
    const offcanvasEl = document.getElementById('viewRegularizationOffcanvas');
    const offcanvas = new bootstrap.Offcanvas(offcanvasEl);
    offcanvas.show();
  }

  // Helper function to populate edit form
  function populateEditForm(data) {
    // Format date to YYYY-MM-DD from ISO 8601 format
    let formattedDate = data.date;
    if (data.date) {
      // Handle ISO 8601 format (e.g., "2025-09-01T18:30:00.000000Z")
      if (data.date.includes('T')) {
        formattedDate = data.date.split('T')[0];
      }
      // Handle datetime format (e.g., "2025-09-01 18:30:00")
      else if (data.date.includes(' ')) {
        formattedDate = data.date.split(' ')[0];
      }
    }
    
    // Format time to HH:MM if it includes seconds
    let formattedCheckIn = data.requested_check_in_time;
    let formattedCheckOut = data.requested_check_out_time;
    
    if (formattedCheckIn && formattedCheckIn.length > 5) {
      // Remove seconds if present (HH:MM:SS -> HH:MM)
      formattedCheckIn = formattedCheckIn.substring(0, 5);
    }
    
    if (formattedCheckOut && formattedCheckOut.length > 5) {
      // Remove seconds if present (HH:MM:SS -> HH:MM)
      formattedCheckOut = formattedCheckOut.substring(0, 5);
    }
    
    // Populate form fields
    $('#regularizationForm').find('[name="date"]').val(formattedDate);
    $('#regularizationForm').find('[name="type"]').val(data.type);
    $('#regularizationForm').find('[name="requested_check_in_time"]').val(formattedCheckIn);
    $('#regularizationForm').find('[name="requested_check_out_time"]').val(formattedCheckOut);
    $('#regularizationForm').find('[name="reason"]').val(data.reason);
    
    // Update form for editing mode
    $('#regularizationForm').attr('data-update-id', data.id);
    
    // Update offcanvas title
    $('#regularizationOffcanvasTitle').text('Edit Regularization Request');
    
    console.log('Form populated with:', {
      date: formattedDate,
      type: data.type,
      checkIn: formattedCheckIn,
      checkOut: formattedCheckOut
    });
  }

  // Reset form when offcanvas is closed
  document.getElementById('addRegularizationOffcanvas').addEventListener('hidden.bs.offcanvas', function () {
    // Reset form
    $('#regularizationForm')[0].reset();
    $('#regularizationForm').removeAttr('data-update-id');
    
    // Reset title
    $('#regularizationOffcanvasTitle').text('New Regularization Request');
  });
});