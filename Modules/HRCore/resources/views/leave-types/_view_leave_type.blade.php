{{-- View Leave Type Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="viewLeaveTypeOffcanvas" aria-labelledby="viewLeaveTypeOffcanvasLabel" style="width: 600px;">
  <div class="offcanvas-header border-bottom">
    <h5 id="viewLeaveTypeOffcanvasLabel" class="offcanvas-title">{{ __('View Leave Type') }}</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div id="leaveTypeDetails">
      {{-- Basic Information --}}
      <div class="mb-4">
        <h6 class="text-primary mb-3">{{ __('Basic Information') }}</h6>
        <div class="row g-3">
          <div class="col-6">
            <div class="d-flex flex-column">
              <small class="text-muted mb-1">{{ __('Name') }}</small>
              <span class="fw-medium" id="viewName">-</span>
            </div>
          </div>
          <div class="col-6">
            <div class="d-flex flex-column">
              <small class="text-muted mb-1">{{ __('Code') }}</small>
              <span class="fw-medium" id="viewCode">-</span>
            </div>
          </div>
          <div class="col-12">
            <div class="d-flex flex-column">
              <small class="text-muted mb-1">{{ __('Description') }}</small>
              <span class="fw-medium" id="viewNotes">-</span>
            </div>
          </div>
          <div class="col-6">
            <div class="d-flex flex-column">
              <small class="text-muted mb-1">{{ __('Status') }}</small>
              <span id="viewStatus">-</span>
            </div>
          </div>
          <div class="col-6">
            <div class="d-flex flex-column">
              <small class="text-muted mb-1">{{ __('Proof Required') }}</small>
              <span id="viewProofRequired">-</span>
            </div>
          </div>
        </div>
      </div>

      {{-- Accrual Settings --}}
      <div class="mb-4">
        <h6 class="text-primary mb-3">{{ __('Accrual Settings') }}</h6>
        <div class="row g-3">
          <div class="col-6">
            <div class="d-flex flex-column">
              <small class="text-muted mb-1">{{ __('Accrual Enabled') }}</small>
              <span id="viewAccrualEnabled">-</span>
            </div>
          </div>
          <div class="col-6">
            <div class="d-flex flex-column">
              <small class="text-muted mb-1">{{ __('Accrual Frequency') }}</small>
              <span class="fw-medium" id="viewAccrualFrequency">-</span>
            </div>
          </div>
          <div class="col-6">
            <div class="d-flex flex-column">
              <small class="text-muted mb-1">{{ __('Accrual Rate') }}</small>
              <span class="fw-medium" id="viewAccrualRate">-</span>
            </div>
          </div>
          <div class="col-6">
            <div class="d-flex flex-column">
              <small class="text-muted mb-1">{{ __('Max Accrual Limit') }}</small>
              <span class="fw-medium" id="viewMaxAccrualLimit">-</span>
            </div>
          </div>
        </div>
      </div>

      {{-- Carry Forward Settings --}}
      <div class="mb-4">
        <h6 class="text-primary mb-3">{{ __('Carry Forward Settings') }}</h6>
        <div class="row g-3">
          <div class="col-6">
            <div class="d-flex flex-column">
              <small class="text-muted mb-1">{{ __('Allow Carry Forward') }}</small>
              <span id="viewAllowCarryForward">-</span>
            </div>
          </div>
          <div class="col-6">
            <div class="d-flex flex-column">
              <small class="text-muted mb-1">{{ __('Max Carry Forward Days') }}</small>
              <span class="fw-medium" id="viewMaxCarryForward">-</span>
            </div>
          </div>
          <div class="col-6">
            <div class="d-flex flex-column">
              <small class="text-muted mb-1">{{ __('Expiry (Months)') }}</small>
              <span class="fw-medium" id="viewCarryForwardExpiryMonths">-</span>
            </div>
          </div>
        </div>
      </div>

      {{-- Encashment Settings --}}
      <div class="mb-4">
        <h6 class="text-primary mb-3">{{ __('Encashment Settings') }}</h6>
        <div class="row g-3">
          <div class="col-6">
            <div class="d-flex flex-column">
              <small class="text-muted mb-1">{{ __('Allow Encashment') }}</small>
              <span id="viewAllowEncashment">-</span>
            </div>
          </div>
          <div class="col-6">
            <div class="d-flex flex-column">
              <small class="text-muted mb-1">{{ __('Max Encashment Days') }}</small>
              <span class="fw-medium" id="viewMaxEncashmentDays">-</span>
            </div>
          </div>
        </div>
      </div>

      {{-- Special Type --}}
      <div class="mb-4">
        <h6 class="text-primary mb-3">{{ __('Special Type') }}</h6>
        <div class="row g-3">
          <div class="col-6">
            <div class="d-flex flex-column">
              <small class="text-muted mb-1">{{ __('Compensatory Off Type') }}</small>
              <span id="viewIsCompOffType">-</span>
            </div>
          </div>
        </div>
      </div>

      {{-- Audit Information --}}
      <div class="mb-4">
        <h6 class="text-primary mb-3">{{ __('Audit Information') }}</h6>
        <div class="row g-3">
          <div class="col-6">
            <div class="d-flex flex-column">
              <small class="text-muted mb-1">{{ __('Created By') }}</small>
              <span class="fw-medium" id="viewCreatedBy">-</span>
            </div>
          </div>
          <div class="col-6">
            <div class="d-flex flex-column">
              <small class="text-muted mb-1">{{ __('Created At') }}</small>
              <span class="fw-medium" id="viewCreatedAt">-</span>
            </div>
          </div>
          <div class="col-6">
            <div class="d-flex flex-column">
              <small class="text-muted mb-1">{{ __('Last Updated By') }}</small>
              <span class="fw-medium" id="viewUpdatedBy">-</span>
            </div>
          </div>
          <div class="col-6">
            <div class="d-flex flex-column">
              <small class="text-muted mb-1">{{ __('Last Updated At') }}</small>
              <span class="fw-medium" id="viewUpdatedAt">-</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>