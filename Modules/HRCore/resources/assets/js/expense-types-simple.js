/**
 * Expense Types Management - Simplified Version
 */

'use strict';

$(function() {
  // CSRF setup
  $.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
  });

  // Check if pageData exists
  if (typeof window.pageData === 'undefined') {
    console.error('pageData is not defined');
    return;
  }

  // Initialize DataTable
  const dataTable = $('.datatables-expenseTypes').DataTable({
    processing: true,
    serverSide: true,
    ajax: pageData.urls.datatable,
    columns: [
      { data: 'id', visible: false },
      { data: 'id' },
      { data: 'name' },
      { data: 'code' },
      { data: 'category' },
      { data: 'max_amount', render: function(data) {
          return data ? '$' + parseFloat(data).toFixed(2) : '-';
        }
      },
      { data: 'requires_receipt', orderable: false },
      { data: 'status', orderable: false },
      { data: 'actions', orderable: false, searchable: false }
    ],
    order: [[1, 'desc']],
    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
    language: {
      processing: 'Processing...',
      search: 'Search:',
      lengthMenu: 'Show _MENU_ entries',
      info: 'Showing _START_ to _END_ of _TOTAL_ entries',
      infoEmpty: 'Showing 0 to 0 of 0 entries',
      emptyTable: 'No data available',
      paginate: {
        first: 'First',
        last: 'Last',
        next: 'Next',
        previous: 'Previous'
      }
    }
  });

  // Form handling
  const form = $('#expenseTypeForm');
  const offcanvas = document.getElementById('formOffcanvas');
  let currentId = null;

  // Add new record button
  $('.add-new').on('click', function() {
    form[0].reset();
    currentId = null;
    $('#formTitle').text(pageData.labels.addExpenseType || 'Add Expense Type');
    $('.data-submit').text(pageData.labels.create || 'Create');
  });

  // Form submission
  form.on('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Convert checkbox values
    const requiresReceipt = $('#requiresReceiptToggle').is(':checked');
    const requiresApproval = $('#requiresApprovalToggle').is(':checked');
    
    formData.delete('requires_receipt');
    formData.delete('requires_approval');
    formData.append('requires_receipt', requiresReceipt ? '1' : '0');
    formData.append('requires_approval', requiresApproval ? '1' : '0');

    const url = currentId ? 
      pageData.urls.update.replace(':id', currentId) : 
      pageData.urls.store;
    
    if (currentId) {
      formData.append('_method', 'PUT');
    }

    $.ajax({
      url: url,
      method: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        if (response.status === 'success') {
          const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvas);
          if (bsOffcanvas) {
            bsOffcanvas.hide();
          }
          dataTable.ajax.reload();
          
          Swal.fire({
            title: pageData.labels.success || 'Success!',
            text: response.data.message || response.data,
            icon: 'success',
            confirmButtonText: 'OK'
          });
        }
      },
      error: function(xhr) {
        let errorMessage = pageData.labels.error || 'Error!';
        if (xhr.responseJSON && xhr.responseJSON.errors) {
          const errors = Object.values(xhr.responseJSON.errors).flat();
          errorMessage = errors.join('<br>');
        } else if (xhr.responseJSON && xhr.responseJSON.message) {
          errorMessage = xhr.responseJSON.message;
        }
        
        Swal.fire({
          title: pageData.labels.error || 'Error!',
          html: errorMessage,
          icon: 'error'
        });
      }
    });
  });

  // Global functions for actions
  window.editRecord = function(id) {
    $.get(pageData.urls.show.replace(':id', id))
      .done(function(response) {
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
          
          currentId = data.id;
          $('#formTitle').text(pageData.labels.editExpenseType || 'Edit Expense Type');
          $('.data-submit').text(pageData.labels.update || 'Update');
          
          const bsOffcanvas = new bootstrap.Offcanvas(offcanvas);
          bsOffcanvas.show();
        }
      })
      .fail(function() {
        Swal.fire({
          title: 'Error!',
          text: 'Failed to load expense type data',
          icon: 'error'
        });
      });
  };

  window.deleteRecord = function(id) {
    Swal.fire({
      title: pageData.labels.confirmDelete || 'Are you sure?',
      text: pageData.labels.wontRevert || "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: pageData.labels.yesDeleteIt || 'Yes, delete it!',
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
              dataTable.ajax.reload();
              Swal.fire({
                title: pageData.labels.deleted || 'Deleted!',
                text: response.data || 'Expense type deleted successfully',
                icon: 'success'
              });
            }
          },
          error: function() {
            Swal.fire({
              title: 'Error!',
              text: 'Failed to delete expense type',
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
          dataTable.ajax.reload();
          Swal.fire({
            title: 'Success!',
            text: response.data.message || 'Status updated successfully',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
          });
        }
      })
      .fail(function() {
        Swal.fire({
          title: 'Error!',
          text: 'Failed to update status',
          icon: 'error'
        });
      });
  };

  // Toggle switches
  $('#requiresReceiptToggle').on('change', function() {
    $('#requiresReceipt').val($(this).is(':checked') ? '1' : '0');
  });

  $('#requiresApprovalToggle').on('change', function() {
    $('#requiresApproval').val($(this).is(':checked') ? '1' : '0');
  });
});