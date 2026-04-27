/**
 * Expense Types Management
 * Handles DataTable and CRUD operations for expense types
 */

'use strict';

// Wait for document ready and ensure jQuery is loaded
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
    const pageData = window.pageData || {
      urls: {
        datatable: '/hrcore/expense-types/datatable',
        store: '/hrcore/expense-types',
        update: '/hrcore/expense-types/:id',
        show: '/hrcore/expense-types/:id',
        destroy: '/hrcore/expense-types/:id',
        checkCode: '/hrcore/expense-types/check-code'
      },
      labels: {
        confirmDelete: 'Are you sure?',
        deleteText: 'This action cannot be undone',
        confirmButton: 'Yes, delete it!',
        cancelButton: 'Cancel',
        success: 'Success!',
        error: 'Error!',
        validation: 'Validation Error',
        somethingWrong: 'Something went wrong'
      }
    };

    // Initialize DataTable
    let expenseTypesTable;
    
    if ($('.datatables-expenseTypes').length) {
      try {
        expenseTypesTable = $('.datatables-expenseTypes').DataTable({
          processing: true,
          serverSide: true,
          ajax: {
            url: pageData.urls.datatable,
            type: 'GET',
            data: function (d) {
              // Add filter values
              d.status = $('#status-filter').val();
              d.category = $('#category-filter').val();
              return d;
            },
            dataSrc: function(json) {
              return json.data || [];
            }
          },
          columns: [
            { data: 'id', name: 'id', orderable: false, searchable: false, visible: false },
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'code', name: 'code' },
            { data: 'category', name: 'category' },
            { data: 'max_amount', name: 'max_amount', render: function(data) {
                return data ? '$' + parseFloat(data).toFixed(2) : '-';
              }
            },
            { data: 'requires_receipt', name: 'requires_receipt', orderable: false },
            { data: 'status', name: 'status', orderable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
          ],
          order: [[1, 'desc']],
          pageLength: 25,
          responsive: true,
          language: {
            paginate: {
              next: '<i class="bx bx-chevron-right"></i>',
              previous: '<i class="bx bx-chevron-left"></i>'
            }
          },
          dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
        });
      } catch (error) {
        // DataTable initialization failed
      }
    }

    // Filter handlers
    $('#status-filter, #category-filter').on('change', function () {
      if (expenseTypesTable) {
        expenseTypesTable.ajax.reload();
      }
    });

    // Clear filters
    $('#clear-filters').on('click', function () {
      $('#status-filter').val('').trigger('change');
      $('#category-filter').val('').trigger('change');
      if (expenseTypesTable) {
        expenseTypesTable.ajax.reload();
      }
    });

    // Form handling
    const expenseTypeForm = $('#expenseTypeForm');
    
    let expenseTypeId = null;
    let formValidator = null;
    
    // Handle add new button click
    $('.add-new').on('click', function() {
      expenseTypeForm[0].reset();
      expenseTypeId = null;
      $('#formTitle').text(pageData.labels.addExpenseType || 'Add Expense Type');
      $('.data-submit').text(pageData.labels.create || 'Create');
      
      // Reset checkboxes
      $('#requiresReceiptToggle').prop('checked', false);
      $('#requiresApprovalToggle').prop('checked', true);
      $('#requiresReceipt').val('0');
      $('#requiresApproval').val('1');
      
      // Reset status to active
      $('#statusToggle').prop('checked', true);
      $('#status').val('active');
    });

    // Initialize form validation if available
    if (typeof FormValidation !== 'undefined' && expenseTypeForm.length) {
      // Form validation code here
    }

    // Global functions for actions
    window.editRecord = window.editExpenseType = function(id) {
      expenseTypeId = id;
      $('#formTitle').text(pageData.labels.editExpenseType || 'Edit Expense Type');
      $('.data-submit').text(pageData.labels.update || 'Update');
      
      // Load expense type data
      $.ajax({
        url: pageData.urls.show.replace(':id', id),
        method: 'GET',
        success: function(response) {
          if (response.status === 'success') {
            const data = response.data;
            
            $('#name').val(data.name);
            $('#code').val(data.code);
            $('#description').val(data.description);
            $('#category').val(data.category);
            $('#defaultAmount').val(data.default_amount);
            $('#maxAmount').val(data.max_amount);
            $('#glAccountCode').val(data.gl_account_code);
            
            $('#requiresReceiptToggle').prop('checked', data.requires_receipt);
            $('#requiresReceipt').val(data.requires_receipt ? '1' : '0');
            
            $('#requiresApprovalToggle').prop('checked', data.requires_approval);
            $('#requiresApproval').val(data.requires_approval ? '1' : '0');
            
            // Set status toggle
            const isActive = data.status === 'active';
            $('#statusToggle').prop('checked', isActive);
            $('#status').val(data.status);
            
            if (formValidator) {
              formValidator.resetForm();
            }
            
            const offcanvasEl = document.getElementById('formOffcanvas');
            const offcanvas = new bootstrap.Offcanvas(offcanvasEl);
            offcanvas.show();
          }
        },
        error: function(xhr) {
          Swal.fire({
            title: pageData.labels.error || 'Error!',
            text: 'Failed to load expense type data',
            icon: 'error'
          });
        }
      });
    };

    window.deleteRecord = window.deleteExpenseType = function(id) {
      Swal.fire({
        title: pageData.labels.confirmDelete || 'Are you sure?',
        text: pageData.labels.wontRevert || "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: pageData.labels.yesDeleteIt || 'Yes, delete it!',
        cancelButtonText: pageData.labels.cancelButton || 'Cancel',
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
                if (expenseTypesTable) {
                  expenseTypesTable.ajax.reload();
                }
                Swal.fire({
                  title: pageData.labels.deleted || 'Deleted!',
                  text: response.data || 'Expense type deleted successfully',
                  icon: 'success'
                });
              }
            },
            error: function(xhr) {
              const errorMsg = xhr.responseJSON?.data || 'Failed to delete expense type';
              Swal.fire({
                title: pageData.labels.error || 'Error!',
                text: errorMsg,
                icon: 'error'
              });
            }
          });
        }
      });
    };

    window.toggleStatus = function(id) {
      $.post(pageData.urls.toggleStatus.replace(':id', id))
        .done(function(response) {
          if (response.status === 'success') {
            if (expenseTypesTable) {
              expenseTypesTable.ajax.reload();
            }
            Swal.fire({
              title: 'Success!',
              text: response.data.message || 'Status updated successfully',
              icon: 'success',
              timer: 1500,
              showConfirmButton: false
            });
          }
        })
        .fail(function(xhr) {
          Swal.fire({
            title: pageData.labels.error || 'Error!',
            text: 'Failed to update status',
            icon: 'error'
          });
        });
    };

    // Toggle switches
    $('#requiresReceiptToggle').on('change', function() {
      const checked = $(this).is(':checked');
      $('#requiresReceipt').val(checked ? '1' : '0');
    });

    $('#requiresApprovalToggle').on('change', function() {
      const checked = $(this).is(':checked');
      $('#requiresApproval').val(checked ? '1' : '0');
    });

    $('#statusToggle').on('change', function() {
      const checked = $(this).is(':checked');
      $('#status').val(checked ? 'active' : 'inactive');
    });
    
    // Form submission
    expenseTypeForm.on('submit', function(e) {
      e.preventDefault();
      
      if (formValidator) {
        formValidator.validate().then(function(status) {
          if (status === 'Valid') {
            submitExpenseTypeForm();
          }
        });
      } else {
        submitExpenseTypeForm();
      }
    });

    function submitExpenseTypeForm() {
      const formData = new FormData(expenseTypeForm[0]);
      
      // Fix checkbox values
      const requiresReceipt = $('#requiresReceiptToggle').is(':checked');
      const requiresApproval = $('#requiresApprovalToggle').is(':checked');
      
      formData.delete('requires_receipt');
      formData.delete('requires_approval');
      formData.append('requires_receipt', requiresReceipt ? '1' : '0');
      formData.append('requires_approval', requiresApproval ? '1' : '0');

      const isEdit = expenseTypeId && expenseTypeId !== '';
      const url = isEdit ? pageData.urls.update.replace(':id', expenseTypeId) : pageData.urls.store;
      
      if (isEdit) {
        formData.append('_method', 'PUT');
      }

      // Show loading state
      const submitBtn = expenseTypeForm.find('button[type="submit"]');
      const originalText = submitBtn.html();
      submitBtn.html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');
      submitBtn.prop('disabled', true);

      $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
          if (response.status === 'success') {
            // Close offcanvas
            const offcanvasEl = document.getElementById('formOffcanvas');
            const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
            if (offcanvas) {
              offcanvas.hide();
            }
            
            Swal.fire({
              title: pageData.labels.success || 'Success!',
              text: response.data.message || 'Operation completed successfully',
              icon: 'success',
              confirmButtonText: 'OK'
            })
            
            // Reset form
            expenseTypeForm[0].reset();
            
            // Reload table
            if (expenseTypesTable) {
              expenseTypesTable.ajax.reload();
            }
          }
        },
        error: function (xhr) {
          const errors = xhr.responseJSON?.data?.errors;
          if (errors) {
            // Display validation errors
            Object.keys(errors).forEach(function(key) {
              const field = expenseTypeForm.find(`[name="${key}"]`);
              const error = errors[key][0];
              
              if (field.length) {
                field.addClass('is-invalid');
                field.closest('.mb-3').find('.invalid-feedback').remove();
                field.closest('.mb-3').append(`<div class="invalid-feedback">${error}</div>`);
              } else {
                Swal.fire({
                  title: pageData.labels.validation || 'Validation Error',
                  text: error,
                  icon: 'error'
                });
              }
            });
          } else {
            Swal.fire({
              title: pageData.labels.error || 'Error!',
              text: xhr.responseJSON?.data || pageData.labels.somethingWrong || 'Something went wrong',
              icon: 'error'
            });
          }
        },
        complete: function() {
          // Restore button state
          submitBtn.html(originalText);
          submitBtn.prop('disabled', false);
        }
      });
    }
  });
});