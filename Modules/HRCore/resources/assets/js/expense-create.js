$(function() {
    'use strict';
    
    // CSRF setup
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // Initialize date picker
    $('.flatpickr').flatpickr({
        dateFormat: 'Y-m-d',
        maxDate: 'today'
    });

    // Initialize select2
    $('.select2').select2({
        placeholder: 'Select...',
        allowClear: true
    });

    // Expense type change handler
    $('#expense_type_id').on('change', function() {
        const option = $(this).find('option:selected');
        const maxAmount = parseFloat(option.data('max-amount'));
        const requiresReceipt = option.data('requires-receipt') == '1';
        
        // Show/hide max amount hint
        if (maxAmount > 0) {
            $('#max_amount_hint').show();
            $('#max_amount_value').text(pageData.currency + maxAmount.toFixed(2));
        } else {
            $('#max_amount_hint').hide();
        }
        
        // Show/hide receipt required indicator
        if (requiresReceipt) {
            $('#receipt_required').show();
        } else {
            $('#receipt_required').hide();
        }
    });

    // Amount validation
    $('#amount').on('input', function() {
        const amount = parseFloat($(this).val());
        const option = $('#expense_type_id').find('option:selected');
        const maxAmount = parseFloat(option.data('max-amount'));
        
        if (maxAmount > 0 && amount > maxAmount) {
            $(this).addClass('is-invalid');
            if (!$(this).next('.invalid-feedback').length) {
                $(this).after('<div class="invalid-feedback">' + pageData.labels.maxAmountExceeded + '</div>');
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        }
    });

    // Form submission
    $('#expenseForm').on('submit', function(e) {
        e.preventDefault();
        
        // Check required receipt
        const option = $('#expense_type_id').find('option:selected');
        const requiresReceipt = option.data('requires-receipt') == '1';
        const attachmentInput = $('#attachments')[0];
        
        if (requiresReceipt && (!attachmentInput.files || attachmentInput.files.length === 0)) {
            Swal.fire({
                title: pageData.labels.error,
                text: pageData.labels.receiptRequired,
                icon: 'error',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
            return;
        }
        
        const formData = new FormData(this);
        const submitButton = $(this).find('button[type="submit"]');
        const originalText = submitButton.html();
        
        submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Submitting...');

        $.ajax({
            url: pageData.urls.store,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        title: pageData.labels.success,
                        text: response.data.message || pageData.labels.created,
                        icon: 'success',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        },
                        buttonsStyling: false
                    }).then(() => {
                        window.location.href = pageData.urls.list;
                    });
                } else {
                    Swal.fire({
                        title: pageData.labels.error,
                        text: response.data || 'Unknown error',
                        icon: 'error',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = pageData.labels.error;
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        errorMessage = errors.join('<br>');
                    } else if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                }
                
                Swal.fire({
                    title: pageData.labels.error,
                    html: errorMessage,
                    icon: 'error',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
            },
            complete: function() {
                submitButton.prop('disabled', false).html(originalText);
            }
        });
    });
});