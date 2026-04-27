<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddOrUpdateLeaveType"
    aria-labelledby="offcanvasCreateLeaveTypeLabel" style="width: 600px;">
    <div class="offcanvas-header border-bottom">
        <h5 id="offcanvasLeaveTypeLabel" class="offcanvas-title">@lang('Create Leave Type')</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100 overflow-auto">
        <form class="pt-0" id="leaveTypeForm">
            <input type="hidden" name="id" id="id">
            <input type="hidden" name="status" id="status">
            
            {{-- Basic Information --}}
            <h6 class="mb-4">@lang('Basic Information')</h6>
            <div class="mb-4">
                <label class="form-label" for="name">@lang('Name')<span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" placeholder="@lang('Enter name')"
                    name="name" />
            </div>
            <div class="mb-4">
                <label class="form-label" for="code">@lang('Code')<span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="code" placeholder="@lang('Enter code')"
                    name="code" />
            </div>
            <div class="mb-4">
                <label class="form-label" for="notes">@lang('Description')</label>
                <textarea class="form-control" id="notes" placeholder="@lang('Enter description')" name="notes" rows="3"></textarea>
            </div>
            <div class="mb-4 d-flex justify-content-between">
                <label class="form-label" for="isProofRequired">@lang('Is Proof Required')</label>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="isProofRequiredToggle">
                    <input type="hidden" name="is_proof_required" id="isProofRequired" value="0">
                </div>
            </div>
            <div class="mb-4 d-flex justify-content-between">
                <label class="form-label" for="isCompOffType">@lang('Is Compensatory Off Type')</label>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="isCompOffTypeToggle">
                    <input type="hidden" name="is_comp_off_type" id="isCompOffType" value="0">
                </div>
            </div>
            
            <hr class="my-5">
            
            {{-- Accrual Settings --}}
            <h6 class="mb-4">@lang('Accrual Settings')</h6>
            <div class="mb-4 d-flex justify-content-between">
                <label class="form-label" for="isAccrualEnabled">@lang('Enable Automatic Accrual')</label>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="isAccrualEnabledToggle">
                    <input type="hidden" name="is_accrual_enabled" id="isAccrualEnabled" value="0">
                </div>
            </div>
            <div id="accrualSettingsSection" style="display: none;">
                <div class="mb-4">
                    <label class="form-label" for="accrualFrequency">@lang('Accrual Frequency')</label>
                    <select class="form-select" id="accrualFrequency" name="accrual_frequency">
                        <option value="monthly">@lang('Monthly')</option>
                        <option value="quarterly">@lang('Quarterly')</option>
                        <option value="yearly">@lang('Yearly')</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="accrualRate">@lang('Accrual Rate')</label>
                    <input type="number" class="form-control" id="accrualRate" name="accrual_rate" 
                        placeholder="@lang('Days per frequency')" step="0.5" min="0">
                    <small class="form-text text-muted">@lang('Number of days to accrue per frequency period')</small>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="maxAccrualLimit">@lang('Maximum Accrual Limit')</label>
                    <input type="number" class="form-control" id="maxAccrualLimit" name="max_accrual_limit" 
                        placeholder="@lang('Maximum days')" step="0.5" min="0">
                    <small class="form-text text-muted">@lang('Maximum days that can be accrued (leave blank for no limit)')</small>
                </div>
            </div>
            
            <hr class="my-5">
            
            {{-- Carry Forward Settings --}}
            <h6 class="mb-4">@lang('Carry Forward Settings')</h6>
            <div class="mb-4 d-flex justify-content-between">
                <label class="form-label" for="allowCarryForward">@lang('Allow Carry Forward')</label>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="allowCarryForwardToggle">
                    <input type="hidden" name="allow_carry_forward" id="allowCarryForward" value="0">
                </div>
            </div>
            <div id="carryForwardSettingsSection" style="display: none;">
                <div class="mb-4">
                    <label class="form-label" for="maxCarryForward">@lang('Maximum Carry Forward Days')</label>
                    <input type="number" class="form-control" id="maxCarryForward" name="max_carry_forward" 
                        placeholder="@lang('Maximum days')" step="0.5" min="0">
                    <small class="form-text text-muted">@lang('Maximum days that can be carried forward to next year')</small>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="carryForwardExpiryMonths">@lang('Carry Forward Expiry')</label>
                    <input type="number" class="form-control" id="carryForwardExpiryMonths" name="carry_forward_expiry_months" 
                        placeholder="@lang('Months')" min="0">
                    <small class="form-text text-muted">@lang('Number of months after which carried forward leaves expire')</small>
                </div>
            </div>
            
            <hr class="my-5">
            
            {{-- Encashment Settings --}}
            <h6 class="mb-4">@lang('Encashment Settings')</h6>
            <div class="mb-4 d-flex justify-content-between">
                <label class="form-label" for="allowEncashment">@lang('Allow Leave Encashment')</label>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="allowEncashmentToggle">
                    <input type="hidden" name="allow_encashment" id="allowEncashment" value="0">
                </div>
            </div>
            <div id="encashmentSettingsSection" style="display: none;">
                <div class="mb-4">
                    <label class="form-label" for="maxEncashmentDays">@lang('Maximum Encashment Days')</label>
                    <input type="number" class="form-control" id="maxEncashmentDays" name="max_encashment_days" 
                        placeholder="@lang('Maximum days')" step="0.5" min="0">
                    <small class="form-text text-muted">@lang('Maximum days that can be encashed per year')</small>
                </div>
            </div>
            
            <hr class="my-5">
            
            <button type="submit" class="btn btn-primary me-3 data-submit">@lang('Create')</button>
            <button type="reset" class="btn btn-label-danger" data-bs-dismiss="offcanvas">@lang('Cancel')</button>
        </form>
    </div>
</div>
