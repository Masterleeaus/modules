/**
 * Expense Requests Management
 * Handles DataTable and CRUD operations for expense requests
 */

'use strict';

// Wait for document ready
document.addEventListener('DOMContentLoaded', function() {
  
  // Check if jQuery is loaded
  if (typeof jQuery === 'undefined') {
    return;
  }

  // Use jQuery
  jQuery(function ($) {
    
    // CSRF setup
    $.ajaxSetup({
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // Page data from backend
    const pageData = window.pageData || {};
    
    // Initialize date range picker
    if ($('#dateRangeFilter').length) {
      $('#dateRangeFilter').flatpickr({
        mode: 'range',
        dateFormat: 'Y-m-d',
        locale: {
          rangeSeparator: ' to '
        }
      });
    }

    // Initialize select2
    $('.select2').select2({
      placeholder: 'Select...',
      allowClear: true
    });

    // Initialize DataTable
    let expensesTable;
    
    if ($('.datatables-expenses').length) {
      expensesTable = $('.datatables-expenses').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: pageData.urls?.datatable,
          data: function(d) {
            d.status = $('#statusFilter').val();
            d.employee_id = $('#employeeFilter').val();
            d.expense_type_id = $('#expenseTypeFilter').val();
            
            // Handle date range
            const dateRange = $('#dateRangeFilter').val();
            if (dateRange) {
              const dates = dateRange.split(' to ');
              d.date_from = dates[0];
              d.date_to = dates[1] || dates[0];
            }
          }
        },
        columns: [
          { data: 'id', name: 'id', orderable: false, searchable: false, visible: false },
          { data: 'expense_number', name: 'expense_number' },
          { data: 'user', name: 'user.first_name', orderable: false },
          { data: 'expense_type', name: 'expenseType.name' },
          { data: 'expense_date', name: 'expense_date' },
          { data: 'amount', name: 'amount', orderable: false },
          { data: 'status', name: 'status', orderable: false },
          { data: 'attachments', name: 'attachments', orderable: false, searchable: false },
          { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[1, 'desc']],
        pageLength: 25,
        responsive: true,
        language: {
          search: pageData.labels?.search || 'Search',
          processing: pageData.labels?.processing || 'Processing...',
          lengthMenu: pageData.labels?.lengthMenu || 'Show _MENU_ entries',
          info: pageData.labels?.info || 'Showing _START_ to _END_ of _TOTAL_ entries',
          infoEmpty: pageData.labels?.infoEmpty || 'Showing 0 to 0 of 0 entries',
          emptyTable: pageData.labels?.emptyTable || 'No data available',
          paginate: {
            first: pageData.labels?.paginate?.first || 'First',
            last: pageData.labels?.paginate?.last || 'Last',
            next: '<i class="bx bx-chevron-right"></i>',
            previous: '<i class="bx bx-chevron-left"></i>'
          }
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
      });
    }

    // Filter change handlers
    $('#statusFilter, #employeeFilter, #expenseTypeFilter, #dateRangeFilter').on('change', function() {
      if (expensesTable) {
        expensesTable.ajax.reload();
      }
    });

    // Global functions for actions
    window.viewRecord = function(id) {
      window.location.href = pageData.urls.show.replace(':id', id);
    };

    window.editRecord = function(id) {
      window.location.href = pageData.urls.edit.replace(':id', id);
    };

    window.deleteRecord = function(id) {
      Swal.fire({
        title: pageData.labels?.confirmDelete || 'Are you sure?',
        text: pageData.labels?.deleteText || 'This action cannot be undone',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: pageData.labels?.yes || 'Yes',
        cancelButtonText: pageData.labels?.cancel || 'Cancel',
        customClass: {
          confirmButton: 'btn btn-primary me-2',
          cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false
      }).then(function(result) {
        if (result.isConfirmed) {
          $.ajax({
            url: pageData.urls.destroy.replace(':id', id),
            method: 'DELETE',
            success: function(response) {
              if (response.status === 'success') {
                if (expensesTable) {
                  expensesTable.ajax.reload();
                }
                Swal.fire({
                  title: pageData.labels?.success || 'Success!',
                  text: response.data?.message || response.data || 'Expense request deleted successfully',
                  icon: 'success'
                });
              }
            },
            error: function(xhr) {
              Swal.fire({
                title: pageData.labels?.error || 'Error!',
                text: xhr.responseJSON?.data || 'Failed to delete expense request',
                icon: 'error'
              });
            }
          });
        }
      });
    };

    // Approve expense request
    window.approveRecord = function(id) {
      Swal.fire({
        title: pageData.labels?.approveTitle || 'Approve Expense Request?',
        html: `
          <div class="mb-3">
            <label class="form-label">${pageData.labels?.approvedAmount || 'Approved Amount (leave empty to approve full amount)'}</label>
            <input type="number" id="approved_amount" class="form-control" step="0.01" min="0.01">
          </div>
          <div class="mb-3">
            <label class="form-label">${pageData.labels?.remarks || 'Approval Remarks (optional)'}</label>
            <textarea id="approval_remarks" class="form-control" rows="3"></textarea>
          </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: pageData.labels?.approve || 'Approve',
        cancelButtonText: pageData.labels?.cancel || 'Cancel',
        customClass: {
          confirmButton: 'btn btn-success me-2',
          cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false,
        preConfirm: () => {
          return {
            approved_amount: document.getElementById('approved_amount').value,
            approval_remarks: document.getElementById('approval_remarks').value
          }
        }
      }).then(function(result) {
        if (result.isConfirmed) {
          $.ajax({
            url: pageData.urls.approve.replace(':id', id),
            method: 'POST',
            data: result.value,
            success: function(response) {
              if (response.status === 'success') {
                if (expensesTable) {
                  expensesTable.ajax.reload();
                }
                Swal.fire({
                  title: pageData.labels?.success || 'Success!',
                  text: response.data?.message || response.data || 'Expense request approved successfully',
                  icon: 'success'
                });
              }
            },
            error: function(xhr) {
              Swal.fire({
                title: pageData.labels?.error || 'Error!',
                text: xhr.responseJSON?.data || 'Failed to approve expense request',
                icon: 'error'
              });
            }
          });
        }
      });
    };

    // Reject expense request
    window.rejectRecord = function(id) {
      Swal.fire({
        title: pageData.labels?.rejectTitle || 'Reject Expense Request?',
        html: `
          <div class="mb-3">
            <label class="form-label">${pageData.labels?.rejectionReason || 'Rejection Reason'} <span class="text-danger">*</span></label>
            <textarea id="rejection_reason" class="form-control" rows="3" required></textarea>
          </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: pageData.labels?.reject || 'Reject',
        cancelButtonText: pageData.labels?.cancel || 'Cancel',
        customClass: {
          confirmButton: 'btn btn-danger me-2',
          cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false,
        preConfirm: () => {
          const reason = document.getElementById('rejection_reason').value;
          if (!reason) {
            Swal.showValidationMessage('Rejection reason is required');
            return false;
          }
          return {
            rejection_reason: reason
          }
        }
      }).then(function(result) {
        if (result.isConfirmed) {
          $.ajax({
            url: pageData.urls.reject.replace(':id', id),
            method: 'POST',
            data: result.value,
            success: function(response) {
              if (response.status === 'success') {
                if (expensesTable) {
                  expensesTable.ajax.reload();
                }
                Swal.fire({
                  title: pageData.labels?.success || 'Success!',
                  text: response.data?.message || response.data || 'Expense request rejected successfully',
                  icon: 'success'
                });
              }
            },
            error: function(xhr) {
              Swal.fire({
                title: pageData.labels?.error || 'Error!',
                text: xhr.responseJSON?.data || 'Failed to reject expense request',
                icon: 'error'
              });
            }
          });
        }
      });
    };

    // Process expense request
    window.processRecord = function(id) {
      Swal.fire({
        title: pageData.labels?.processTitle || 'Process Expense Request?',
        html: `
          <div class="mb-3">
            <label class="form-label">${pageData.labels?.paymentReference || 'Payment Reference (optional)'}</label>
            <input type="text" id="payment_reference" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">${pageData.labels?.processingNotes || 'Processing Notes (optional)'}</label>
            <textarea id="processing_notes" class="form-control" rows="3"></textarea>
          </div>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: pageData.labels?.process || 'Process',
        cancelButtonText: pageData.labels?.cancel || 'Cancel',
        customClass: {
          confirmButton: 'btn btn-info me-2',
          cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false,
        preConfirm: () => {
          return {
            payment_reference: document.getElementById('payment_reference').value,
            processing_notes: document.getElementById('processing_notes').value
          }
        }
      }).then(function(result) {
        if (result.isConfirmed) {
          $.ajax({
            url: pageData.urls.process.replace(':id', id),
            method: 'POST',
            data: result.value,
            success: function(response) {
              if (response.status === 'success') {
                if (expensesTable) {
                  expensesTable.ajax.reload();
                }
                Swal.fire({
                  title: pageData.labels?.success || 'Success!',
                  text: response.data?.message || response.data || 'Expense request processed successfully',
                  icon: 'success'
                });
              }
            },
            error: function(xhr) {
              Swal.fire({
                title: pageData.labels?.error || 'Error!',
                text: xhr.responseJSON?.data || 'Failed to process expense request',
                icon: 'error'
              });
            }
          });
        }
      });
    };
  });
});