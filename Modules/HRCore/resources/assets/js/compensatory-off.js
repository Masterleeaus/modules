/**
 * HRCore Compensatory Off Management
 */

document.addEventListener('DOMContentLoaded', function() {
  'use strict';
  
  // Ensure jQuery is loaded
  if (typeof jQuery === 'undefined') {
    console.error('jQuery is required for compensatory-off.js');
    return;
  }
  
  // Use jQuery's document ready
  jQuery(function($) {
    // CSRF token setup
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    // Initialize DataTable
    const dt = $('#compOffTable').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: pageData.urls.datatable,
        data: function (d) {
          d.status = $('#statusFilter').val();
          d.user_id = $('#employeeFilter').val();
          d.is_used = $('#usedFilter').val();
          d.expired = $('#expiredFilter').val();
        }
      },
      columns: [
        { data: 'id', visible: false },
        { data: 'employee', orderable: false },
        { data: 'worked_date_display', orderable: true },
        { data: 'hours_worked_display', orderable: false },
        { data: 'comp_off_days_display', orderable: false },
        { data: 'expiry_date_display', orderable: true },
        { data: 'status_display', orderable: false },
        { data: 'usage_status', orderable: false },
        { data: 'actions', orderable: false, searchable: false }
      ],
      order: [[0, 'desc']],
      language: {
        search: pageData.labels.search,
        processing: pageData.labels.processing,
        lengthMenu: pageData.labels.lengthMenu,
        info: pageData.labels.info,
        infoEmpty: pageData.labels.infoEmpty,
        emptyTable: pageData.labels.emptyTable,
        paginate: pageData.labels.paginate
      },
      dom:
        '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
        't' +
        '<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
    });

    // Function to initialize Select2
    function initializeSelect2() {
      // Destroy existing Select2 instances if they exist
      if ($('#employeeFilter').data('select2')) {
        $('#employeeFilter').select2('destroy');
      }
      if ($('#statusFilter').data('select2')) {
        $('#statusFilter').select2('destroy');
      }
      if ($('#usedFilter').data('select2')) {
        $('#usedFilter').select2('destroy');
      }
      if ($('#expiredFilter').data('select2')) {
        $('#expiredFilter').select2('destroy');
      }
      
      // Re-initialize Select2
      $('#employeeFilter').select2({
        theme: 'bootstrap-5',
        allowClear: true,
        width: '100%',
        placeholder: 'All Employees',
        dropdownParent: $('body')
      });
      
      $('#statusFilter').select2({
        theme: 'bootstrap-5',
        allowClear: true,
        width: '100%',
        placeholder: 'All Statuses',
        dropdownParent: $('body')
      });
      
      $('#usedFilter').select2({
        theme: 'bootstrap-5',
        allowClear: true,
        width: '100%',
        placeholder: 'All',
        dropdownParent: $('body')
      });
      
      $('#expiredFilter').select2({
        theme: 'bootstrap-5',
        allowClear: true,
        width: '100%',
        placeholder: 'All',
        dropdownParent: $('body')
      });
    }

    // Initialize filters after DataTable is ready
    dt.on('init.dt', function() {
      // Initialize Select2
      if ($.fn.select2) {
        initializeSelect2();
      }

      // Initialize Flatpickr for date filter
      if (typeof flatpickr !== 'undefined') {
        flatpickr('#workedDateFilter', {
          dateFormat: 'Y-m-d',
          allowClear: true
        });
      }
    });

    // Filter handlers
    $('#statusFilter, #employeeFilter, #usedFilter, #expiredFilter, #workedDateFilter').on('change', function () {
      dt.ajax.reload();
    });
    
    // Re-initialize Select2 after each DataTable draw to maintain styles
    dt.on('draw.dt', function() {
      // Small delay to ensure DOM is ready
      setTimeout(function() {
        if ($.fn.select2 && !$('#employeeFilter').data('select2')) {
          initializeSelect2();
        }
      }, 50);
    });

    // Calculate comp off days based on hours worked
    $('#hours_worked').on('input', function() {
      calculateCompOffDays();
    });

    function calculateCompOffDays() {
      const hoursWorked = parseFloat($('#hours_worked').val()) || 0;
      let compOffDays = 0;
      
      // Standard calculation: 8 hours = 1 day
      if (hoursWorked >= 8) {
        compOffDays = Math.floor(hoursWorked / 8);
      } else if (hoursWorked >= 4) {
        compOffDays = 0.5;
      }
      
      $('#comp_off_days').val(compOffDays);
      $('#comp_off_days_display').text(compOffDays + ' ' + (compOffDays === 1 ? 'day' : 'days'));
    }

    // Calculate expiry date based on worked date
    $('#worked_date').on('change', function() {
      calculateExpiryDate();
    });

    function calculateExpiryDate() {
      const workedDate = $('#worked_date').val();
      if (workedDate) {
        const date = new Date(workedDate);
        date.setMonth(date.getMonth() + 3); // 3 months expiry by default
        const expiryDate = date.toISOString().split('T')[0];
        $('#expiry_date').val(expiryDate);
      }
    }

    // View compensatory off details
    window.viewCompensatoryOff = function(id) {
      window.location.href = pageData.urls.show.replace(':id', id);
    };

    // Approve compensatory off
    window.approveCompensatoryOff = function(id) {
      Swal.fire({
        title: pageData.labels.confirmApprove,
        input: 'textarea',
        inputLabel: pageData.labels.approvalNotes,
        inputPlaceholder: pageData.labels.enterNotes,
        showCancelButton: true,
        confirmButtonText: pageData.labels.approve,
        cancelButtonText: pageData.labels.cancel,
        confirmButtonClass: 'btn btn-success me-3',
        cancelButtonClass: 'btn btn-label-secondary',
        buttonsStyling: false
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: pageData.urls.approve.replace(':id', id),
            type: 'POST',
            data: { notes: result.value },
            success: function(response) {
              if (response.success) {
                Swal.fire({
                  icon: 'success',
                  title: pageData.labels.success,
                  text: response.message,
                  confirmButtonClass: 'btn btn-success',
                  buttonsStyling: false
                });
                $('#compOffTable').DataTable().ajax.reload();
              } else {
                Swal.fire({
                  icon: 'error',
                  title: pageData.labels.error,
                  text: response.message,
                  confirmButtonClass: 'btn btn-danger',
                  buttonsStyling: false
                });
              }
            },
            error: function(xhr) {
              Swal.fire({
                icon: 'error',
                title: pageData.labels.error,
                text: xhr.responseJSON?.message || pageData.labels.somethingWentWrong,
                confirmButtonClass: 'btn btn-danger',
                buttonsStyling: false
              });
            }
          });
        }
      });
    };

    // Reject compensatory off
    window.rejectCompensatoryOff = function(id) {
      Swal.fire({
        title: pageData.labels.confirmReject,
        input: 'textarea',
        inputLabel: pageData.labels.rejectionReason,
        inputPlaceholder: pageData.labels.enterReason,
        inputValidator: (value) => {
          if (!value) {
            return pageData.labels.reasonRequired;
          }
        },
        showCancelButton: true,
        confirmButtonText: pageData.labels.reject,
        cancelButtonText: pageData.labels.cancel,
        confirmButtonClass: 'btn btn-danger me-3',
        cancelButtonClass: 'btn btn-label-secondary',
        buttonsStyling: false
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: pageData.urls.reject.replace(':id', id),
            type: 'POST',
            data: { reason: result.value },
            success: function(response) {
              if (response.success) {
                Swal.fire({
                  icon: 'success',
                  title: pageData.labels.success,
                  text: response.message,
                  confirmButtonClass: 'btn btn-success',
                  buttonsStyling: false
                });
                $('#compOffTable').DataTable().ajax.reload();
              } else {
                Swal.fire({
                  icon: 'error',
                  title: pageData.labels.error,
                  text: response.message,
                  confirmButtonClass: 'btn btn-danger',
                  buttonsStyling: false
                });
              }
            },
            error: function(xhr) {
              Swal.fire({
                icon: 'error',
                title: pageData.labels.error,
                text: xhr.responseJSON?.message || pageData.labels.somethingWentWrong,
                confirmButtonClass: 'btn btn-danger',
                buttonsStyling: false
              });
            }
          });
        }
      });
    };

    // Delete compensatory off
    window.deleteCompensatoryOff = function(id) {
      Swal.fire({
        title: pageData.labels.confirmDelete,
        text: pageData.labels.deleteWarning,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: pageData.labels.delete,
        cancelButtonText: pageData.labels.cancel,
        confirmButtonClass: 'btn btn-danger me-3',
        cancelButtonClass: 'btn btn-label-secondary',
        buttonsStyling: false
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: pageData.urls.destroy.replace(':id', id),
            type: 'DELETE',
            success: function(response) {
              if (response.success) {
                Swal.fire({
                  icon: 'success',
                  title: pageData.labels.success,
                  text: response.message,
                  confirmButtonClass: 'btn btn-success',
                  buttonsStyling: false
                });
                $('#compOffTable').DataTable().ajax.reload();
              } else {
                Swal.fire({
                  icon: 'error',
                  title: pageData.labels.error,
                  text: response.message,
                  confirmButtonClass: 'btn btn-danger',
                  buttonsStyling: false
                });
              }
            },
            error: function(xhr) {
              Swal.fire({
                icon: 'error',
                title: pageData.labels.error,
                text: xhr.responseJSON?.message || pageData.labels.somethingWentWrong,
                confirmButtonClass: 'btn btn-danger',
                buttonsStyling: false
              });
            }
          });
        }
      });
    };

    // Mark as used
    window.markAsUsed = function(id) {
      Swal.fire({
        title: pageData.labels.confirmMarkUsed,
        text: pageData.labels.markUsedWarning,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: pageData.labels.confirm,
        cancelButtonText: pageData.labels.cancel,
        confirmButtonClass: 'btn btn-info me-3',
        cancelButtonClass: 'btn btn-label-secondary',
        buttonsStyling: false
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: pageData.urls.markUsed.replace(':id', id),
            type: 'POST',
            success: function(response) {
              if (response.success) {
                Swal.fire({
                  icon: 'success',
                  title: pageData.labels.success,
                  text: response.message,
                  confirmButtonClass: 'btn btn-success',
                  buttonsStyling: false
                });
                $('#compOffTable').DataTable().ajax.reload();
              } else {
                Swal.fire({
                  icon: 'error',
                  title: pageData.labels.error,
                  text: response.message,
                  confirmButtonClass: 'btn btn-danger',
                  buttonsStyling: false
                });
              }
            },
            error: function(xhr) {
              Swal.fire({
                icon: 'error',
                title: pageData.labels.error,
                text: xhr.responseJSON?.message || pageData.labels.somethingWentWrong,
                confirmButtonClass: 'btn btn-danger',
                buttonsStyling: false
              });
            }
          });
        }
      });
    };

    // Check for expiring comp offs and notify
    function checkExpiringCompOffs() {
      $.ajax({
        url: pageData.urls.checkExpiring,
        type: 'GET',
        success: function(response) {
          if (response.data && response.data.length > 0) {
            const expiringCount = response.data.length;
            const message = `You have ${expiringCount} compensatory off(s) expiring soon!`;
            
            // Show notification
            showNotification(message, 'warning');
          }
        }
      });
    }

    // Show notification
    function showNotification(message, type = 'info') {
      const toast = `
        <div class="bs-toast toast fade show bg-${type}" role="alert" aria-live="assertive" aria-atomic="true">
          <div class="toast-header">
            <i class="bx bx-bell me-2"></i>
            <div class="me-auto fw-semibold">Notification</div>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
          <div class="toast-body">${message}</div>
        </div>
      `;
      
      const toastContainer = document.querySelector('.toast-container') || 
        (() => {
          const container = document.createElement('div');
          container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
          document.body.appendChild(container);
          return container;
        })();
      
      toastContainer.insertAdjacentHTML('beforeend', toast);
      
      setTimeout(() => {
        toastContainer.lastElementChild.remove();
      }, 5000);
    }

    // Check expiring comp offs on page load
    $(document).ready(function() {
      if ($('#compensatoryOffTable').length) {
        checkExpiringCompOffs();
      }
    });
  }); // End jQuery ready
}); // End DOMContentLoaded