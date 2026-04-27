$(function () {
    'use strict';

    // Initialize components
    initializeDataTable();
    initializeFilters();
    setupEventHandlers();
});

let table;

function initializeDataTable() {
    table = $('#expensesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: pageData.urls.datatable,
            data: function (d) {
                d.status = $('#filterStatus').val();
                d.expense_type_id = $('#filterExpenseType').val();
                d.date_from = $('#filterDateFrom').val();
                d.date_to = $('#filterDateTo').val();
            }
        },
        columns: [
            { data: 'expense_date', name: 'expense_date' },
            { data: 'expense_type', name: 'expense_type_id' },
            { data: 'description', name: 'description' },
            { data: 'amount', name: 'amount' },
            { data: 'status', name: 'status' },
            { data: 'attachments', name: 'attachments', orderable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        dom: '<"card-header d-flex flex-wrap pb-2"<"me-5 ms-n2"f><"d-flex justify-content-start justify-content-md-end align-items-baseline"<"dt-action-buttons d-flex justify-content-center flex-md-row mb-3 mb-md-0 ps-1 ms-1 align-items-baseline"lB>>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        displayLength: 10,
        lengthMenu: [10, 20, 50, 100],
        buttons: [],
        language: {
            search: '',
            searchPlaceholder: 'Search...'
        }
    });
}

function initializeFilters() {
    // Initialize Select2
    $('#filterStatus, #filterExpenseType').select2({
        minimumResultsForSearch: -1
    });

    // Initialize Flatpickr for date inputs
    $('#filterDateFrom, #filterDateTo').flatpickr({
        dateFormat: 'Y-m-d',
        allowInput: true
    });

    // Apply filters on change
    $('#filterStatus, #filterExpenseType, #filterDateFrom, #filterDateTo').on('change', function () {
        table.ajax.reload();
    });
}

function setupEventHandlers() {
    // CSRF token setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
}

// Create new expense
window.createExpense = function() {
    window.location.href = pageData.urls.create;
};

// Edit expense
window.editExpense = function(id) {
    window.location.href = pageData.urls.edit.replace('__ID__', id);
};

// View expense details
window.viewExpense = function(id) {
    // Show offcanvas first
    const offcanvasElement = document.getElementById('expenseDetailsOffcanvas');
    const offcanvas = new bootstrap.Offcanvas(offcanvasElement);
    offcanvas.show();
    
    // Show loading state
    $('#expenseDetailsContent').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `);
    
    // Fetch expense details
    $.ajax({
        url: pageData.urls.show.replace('__ID__', id),
        type: 'GET',
        success: function(response) {
            if (response.status === 'success') {
                const expense = response.data.expense;
                
                // Build HTML content
                let html = `
                    <div class="expense-details">
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">${pageData.labels.expenseNumber || 'Expense Number'}</h6>
                            <p class="fw-semibold">${expense.expense_number || '-'}</p>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">${pageData.labels.date || 'Date'}</h6>
                            <p>${expense.expense_date}</p>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">${pageData.labels.type || 'Type'}</h6>
                            <p>${expense.expense_type?.name || '-'}</p>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">${pageData.labels.title || 'Title'}</h6>
                            <p>${expense.title || '-'}</p>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">${pageData.labels.description || 'Description'}</h6>
                            <p>${expense.description || '-'}</p>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">${pageData.labels.amount || 'Amount'}</h6>
                            <p class="fs-5 fw-semibold">${expense.formatted_amount || expense.amount}</p>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">${pageData.labels.status || 'Status'}</h6>
                            <p>${expense.status_badge || expense.status}</p>
                        </div>
                `;
                
                // Add approval info if approved
                if (expense.status === 'approved' && expense.approved_by) {
                    html += `
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">${pageData.labels.approvedBy || 'Approved By'}</h6>
                            <p>${expense.approved_by.name} on ${expense.approved_at}</p>
                            ${expense.approval_remarks ? `<p class="text-muted small">${expense.approval_remarks}</p>` : ''}
                        </div>
                    `;
                }
                
                // Add rejection info if rejected
                if (expense.status === 'rejected' && expense.rejected_by) {
                    html += `
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">${pageData.labels.rejectedBy || 'Rejected By'}</h6>
                            <p>${expense.rejected_by.name} on ${expense.rejected_at}</p>
                            ${expense.rejection_reason ? `<p class="text-danger small">${expense.rejection_reason}</p>` : ''}
                        </div>
                    `;
                }
                
                // Add processing info if processed
                if (expense.status === 'processed' && expense.processed_by) {
                    html += `
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">${pageData.labels.processedBy || 'Processed By'}</h6>
                            <p>${expense.processed_by.name} on ${expense.processed_at}</p>
                            ${expense.payment_reference ? `<p class="small">Reference: ${expense.payment_reference}</p>` : ''}
                            ${expense.processing_notes ? `<p class="text-muted small">${expense.processing_notes}</p>` : ''}
                        </div>
                    `;
                }
                
                // Add attachments if available
                if (expense.attachments && expense.attachments.length > 0) {
                    html += `
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">${pageData.labels.attachments || 'Attachments'}</h6>
                            <div class="list-group">
                    `;
                    expense.attachments.forEach(function(attachment) {
                        html += `
                            <a href="${attachment.url}" target="_blank" class="list-group-item list-group-item-action">
                                <i class="bx bx-file me-2"></i> ${attachment.name}
                            </a>
                        `;
                    });
                    html += `</div></div>`;
                }
                
                html += `</div>`;
                
                // Update offcanvas content
                $('#expenseDetailsContent').html(html);
            } else {
                $('#expenseDetailsContent').html(`
                    <div class="alert alert-danger">
                        ${response.data || 'Failed to load expense details'}
                    </div>
                `);
            }
        },
        error: function(xhr) {
            let errorMessage = 'Failed to load expense details';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            $('#expenseDetailsContent').html(`
                <div class="alert alert-danger">
                    ${errorMessage}
                </div>
            `);
        }
    });
};

// Delete expense
window.deleteExpense = function(id) {
    Swal.fire({
        title: pageData.labels.deleteTitle,
        text: pageData.labels.confirmDelete,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: pageData.labels.deleteButton,
        cancelButtonText: pageData.labels.cancelButton,
        customClass: {
            confirmButton: 'btn btn-danger me-3',
            cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false
    }).then(function (result) {
        if (result.isConfirmed) {
            $.ajax({
                url: pageData.urls.destroy.replace('__ID__', id),
                type: 'DELETE',
                success: function (response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: pageData.labels.success,
                            text: response.data.message || pageData.labels.deleted,
                            customClass: {
                                confirmButton: 'btn btn-success'
                            }
                        });
                        table.ajax.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: pageData.labels.error,
                            text: response.data || pageData.labels.error,
                            customClass: {
                                confirmButton: 'btn btn-success'
                            }
                        });
                    }
                },
                error: function (xhr) {
                    let errorMessage = pageData.labels.error;
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: pageData.labels.error,
                        text: errorMessage,
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    });
                }
            });
        }
    });
};