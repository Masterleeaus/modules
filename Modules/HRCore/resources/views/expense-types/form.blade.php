<div class="offcanvas offcanvas-end" tabindex="-1" id="formOffcanvas"
    aria-labelledby="formOffcanvasLabel" style="width: 600px;">
    <div class="offcanvas-header border-bottom">
        <h5 id="formTitle" class="offcanvas-title">{{ __('Add Expense Type') }}</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100 overflow-auto">
        <form class="pt-0" id="expenseTypeForm">
            @csrf
            <input type="hidden" name="id" id="id">

            {{-- Basic Information --}}
            <h6 class="mb-4">{{ __('Basic Information') }}</h6>
            <div class="mb-4">
                <label class="form-label" for="name">{{ __('Name') }}<span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" 
                    placeholder="{{ __('Enter expense type name') }}" required />
            </div>
            
            <div class="mb-4">
                <label class="form-label" for="code">{{ __('Code') }}<span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="code" name="code" 
                    placeholder="{{ __('Enter unique code') }}" required />
                <small class="text-muted">{{ __('Unique identifier for this expense type') }}</small>
            </div>
            
            <div class="mb-4">
                <label class="form-label" for="description">{{ __('Description') }}</label>
                <textarea class="form-control" id="description" name="description" rows="3"
                    placeholder="{{ __('Enter description') }}"></textarea>
            </div>
            
            <div class="mb-4">
                <label class="form-label" for="category">{{ __('Category') }}</label>
                <select class="form-select" id="category" name="category">
                    <option value="">{{ __('Select Category') }}</option>
                    @foreach(\Modules\HRCore\app\Models\ExpenseType::getCategories() as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>

            <hr class="my-5">

            {{-- Amount Settings --}}
            <h6 class="mb-4">{{ __('Amount Settings') }}</h6>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="form-label" for="defaultAmount">{{ __('Default Amount') }}</label>
                        <input type="number" class="form-control" id="defaultAmount" name="default_amount" 
                            step="0.01" min="0" placeholder="0.00" />
                        <small class="text-muted">{{ __('Default amount for this expense type') }}</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="form-label" for="maxAmount">{{ __('Maximum Amount') }}</label>
                        <input type="number" class="form-control" id="maxAmount" name="max_amount" 
                            step="0.01" min="0" placeholder="0.00" />
                        <small class="text-muted">{{ __('Maximum allowable amount (leave blank for no limit)') }}</small>
                    </div>
                </div>
            </div>

            <hr class="my-5">

            {{-- Requirements --}}
            <h6 class="mb-4">{{ __('Requirements') }}</h6>
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <label class="form-label mb-0" for="requiresReceiptToggle">{{ __('Requires Receipt') }}</label>
                    <small class="text-muted d-block">{{ __('Require receipt/attachment for this expense type') }}</small>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="requiresReceiptToggle">
                    <input type="hidden" name="requires_receipt" id="requiresReceipt" value="0">
                </div>
            </div>
            
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <label class="form-label mb-0" for="requiresApprovalToggle">{{ __('Requires Approval') }}</label>
                    <small class="text-muted d-block">{{ __('Require approval before processing') }}</small>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="requiresApprovalToggle">
                    <input type="hidden" name="requires_approval" id="requiresApproval" value="0">
                </div>
            </div>

            <hr class="my-5">

            {{-- Status --}}
            <h6 class="mb-4">{{ __('Status') }}</h6>
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <label class="form-label mb-0" for="statusToggle">{{ __('Active') }}</label>
                    <small class="text-muted d-block">{{ __('Enable or disable this expense type') }}</small>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="statusToggle" checked>
                    <input type="hidden" name="status" id="status" value="active">
                </div>
            </div>

            <hr class="my-5">

            {{-- Accounting --}}
            <h6 class="mb-4">{{ __('Accounting') }}</h6>
            <div class="mb-4">
                <label class="form-label" for="glAccountCode">{{ __('GL Account Code') }}</label>
                <input type="text" class="form-control" id="glAccountCode" name="gl_account_code" 
                    placeholder="{{ __('Enter GL account code') }}" />
                <small class="text-muted">{{ __('General Ledger account code for accounting integration') }}</small>
            </div>

            <hr class="my-5">

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill data-submit">{{ __('Create') }}</button>
                <button type="button" class="btn btn-label-secondary flex-fill" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
            </div>
        </form>
    </div>
</div>