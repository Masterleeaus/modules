{{-- Approve Expense Modal --}}
<div class="modal fade" id="approveExpenseModal" tabindex="-1" aria-labelledby="approveExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveExpenseModalLabel">{{ __('Approve Expense Request') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="approveForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="approvedAmount" class="form-label">{{ __('Approved Amount') }}</label>
                        <input type="number" class="form-control" id="approvedAmount" name="approved_amount" 
                            step="0.01" min="0" placeholder="{{ __('Enter approved amount') }}">
                        <small class="text-muted">{{ __('Leave blank to approve full amount') }}</small>
                    </div>
                    <div class="mb-3">
                        <label for="approvalRemarks" class="form-label">{{ __('Approval Remarks') }}</label>
                        <textarea class="form-control" id="approvalRemarks" name="approval_remarks" rows="3" 
                            placeholder="{{ __('Enter any remarks...') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-success">{{ __('Approve') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Reject Expense Modal --}}
<div class="modal fade" id="rejectExpenseModal" tabindex="-1" aria-labelledby="rejectExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectExpenseModalLabel">{{ __('Reject Expense Request') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejectionReason" class="form-label">{{ __('Rejection Reason') }} <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejectionReason" name="rejection_reason" rows="4" 
                            placeholder="{{ __('Please provide a reason for rejection...') }}" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Reject') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Process Expense Modal --}}
<div class="modal fade" id="processExpenseModal" tabindex="-1" aria-labelledby="processExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="processExpenseModalLabel">{{ __('Process Expense Payment') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="processForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="paymentReference" class="form-label">{{ __('Payment Reference') }}</label>
                        <input type="text" class="form-control" id="paymentReference" name="payment_reference" 
                            placeholder="{{ __('Enter payment reference number') }}">
                    </div>
                    <div class="mb-3">
                        <label for="processingNotes" class="form-label">{{ __('Processing Notes') }}</label>
                        <textarea class="form-control" id="processingNotes" name="processing_notes" rows="3" 
                            placeholder="{{ __('Enter any processing notes...') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Mark as Processed') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>