/**
 * Expense Request Details
 * Handles actions for expense request details page
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

    // Modal form submissions
    $('#approveForm').on('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      
      $.ajax({
        url: pageData.urls?.approve,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          if (response.status === 'success') {
            const modalEl = document.getElementById('approveExpenseModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) {
              modal.hide();
            }
            Swal.fire({
              title: pageData.labels?.success || 'Success!',
              text: response.data?.message || pageData.labels?.approved || 'Expense approved successfully',
              icon: 'success'
            }).then(() => {
              location.reload();
            });
          }
        },
        error: function(xhr) {
          Swal.fire({
            title: pageData.labels?.error || 'Error!',
            text: xhr.responseJSON?.data?.message || xhr.responseJSON?.data || 'Failed to approve expense',
            icon: 'error'
          });
        }
      });
    });

    $('#rejectForm').on('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      
      $.ajax({
        url: pageData.urls?.reject,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          if (response.status === 'success') {
            const modalEl = document.getElementById('rejectExpenseModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) {
              modal.hide();
            }
            Swal.fire({
              title: pageData.labels?.success || 'Success!',
              text: response.data?.message || pageData.labels?.rejected || 'Expense rejected successfully',
              icon: 'success'
            }).then(() => {
              location.reload();
            });
          }
        },
        error: function(xhr) {
          Swal.fire({
            title: pageData.labels?.error || 'Error!',
            text: xhr.responseJSON?.data?.message || xhr.responseJSON?.data || 'Failed to reject expense',
            icon: 'error'
          });
        }
      });
    });

    $('#processForm').on('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      
      $.ajax({
        url: pageData.urls?.process,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          if (response.status === 'success') {
            const modalEl = document.getElementById('processExpenseModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) {
              modal.hide();
            }
            Swal.fire({
              title: pageData.labels?.success || 'Success!',
              text: response.data?.message || pageData.labels?.processed || 'Expense processed successfully',
              icon: 'success'
            }).then(() => {
              location.reload();
            });
          }
        },
        error: function(xhr) {
          Swal.fire({
            title: pageData.labels?.error || 'Error!',
            text: xhr.responseJSON?.data?.message || xhr.responseJSON?.data || 'Failed to process expense',
            icon: 'error'
          });
        }
      });
    });

    // Global functions for actions
    window.approveExpense = function() {
      const modal = new bootstrap.Modal(document.getElementById('approveExpenseModal'));
      modal.show();
    };

    window.rejectExpense = function() {
      const modal = new bootstrap.Modal(document.getElementById('rejectExpenseModal'));
      modal.show();
    };

    window.processExpense = function() {
      const modal = new bootstrap.Modal(document.getElementById('processExpenseModal'));
      modal.show();
    };
  });
});