@extends('layouts/layoutMaster')

@section('title', __('Edit Compensatory Off Request'))

<!-- Vendor Styles -->
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/@form-validation/form-validation.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss'
  ])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/@form-validation/popular.js',
    'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
    'resources/assets/vendor/libs/@form-validation/auto-focus.js',
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js'
  ])
@endsection

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    {{-- Breadcrumb Component --}}
    <x-breadcrumb
      :title="__('Edit Compensatory Off Request')"
      :breadcrumbs="[
        ['name' => __('Human Resources'), 'url' => ''],
        ['name' => __('Compensatory Off'), 'url' => route('hrcore.compensatory-offs.index')],
        ['name' => __('Edit'), 'url' => '']
      ]"
      :home-url="url('/')"
    />

    <div class="row">
      {{-- Compensatory Off Form --}}
      <div class="col-md-8">
        <div class="card">
          <div class="card-header">
            <h5 class="card-title">{{ __('Edit Compensatory Off Request') }}</h5>
            <div class="card-subtitle text-muted">
              {{ __('Request ID') }}: #{{ $compOff->id }} | 
              {{ __('Status') }}: 
              @php
                $statusColors = [
                  'pending' => 'warning',
                  'approved' => 'success', 
                  'rejected' => 'danger'
                ];
                $color = $statusColors[$compOff->status] ?? 'secondary';
              @endphp
              <span class="badge bg-label-{{ $color }}">{{ ucfirst($compOff->status) }}</span>
            </div>
          </div>
          <div class="card-body">
            @if($compOff->status !== 'pending')
            <div class="alert alert-info">
              <i class="bx bx-info-circle me-2"></i>
              {{ __('This request has been') }} {{ $compOff->status }} {{ __('and cannot be modified.') }}
            </div>
            @endif

            <form action="{{ route('hrcore.compensatory-offs.update', $compOff->id) }}" method="POST" id="compOffForm">
              @csrf
              @method('PUT')
              
              <div class="row">
                {{-- Worked Date --}}
                <div class="col-md-6 mb-3">
                  <label for="worked_date" class="form-label">{{ __('Date Worked') }} <span class="text-danger">*</span></label>
                  <input type="text" id="worked_date" name="worked_date" class="form-control @error('worked_date') is-invalid @enderror" value="{{ old('worked_date', \Carbon\Carbon::parse($compOff->worked_date)->format('Y-m-d')) }}" required>
                  <small class="text-muted">{{ __('Select the date you worked extra hours') }}</small>
                  @error('worked_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                {{-- Hours Worked --}}
                <div class="col-md-6 mb-3">
                  <label for="hours_worked" class="form-label">{{ __('Extra Hours Worked') }} <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <input type="number" id="hours_worked" name="hours_worked" class="form-control @error('hours_worked') is-invalid @enderror" value="{{ old('hours_worked', $compOff->hours_worked) }}" step="0.5" min="0.5" max="24" required>
                    <span class="input-group-text">{{ __('hours') }}</span>
                  </div>
                  <small class="text-muted">{{ __('Enter the number of extra hours (minimum 0.5, maximum 24)') }}</small>
                  @error('hours_worked')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              {{-- Comp Off Days --}}
              <div class="mb-3">
                <label for="comp_off_days" class="form-label">{{ __('Compensatory Off Days Requested') }} <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input type="number" id="comp_off_days" name="comp_off_days" class="form-control @error('comp_off_days') is-invalid @enderror" value="{{ old('comp_off_days', $compOff->comp_off_days) }}" step="0.5" min="0.5" max="5" required>
                  <span class="input-group-text">{{ __('days') }}</span>
                </div>
                <small class="text-muted">{{ __('Enter the number of compensatory off days you want (minimum 0.5, maximum 5)') }}</small>
                @error('comp_off_days')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              {{-- Reason --}}
              <div class="mb-3">
                <label for="reason" class="form-label">{{ __('Reason for Extra Hours') }} <span class="text-danger">*</span></label>
                <textarea id="reason" name="reason" class="form-control @error('reason') is-invalid @enderror" rows="4" required>{{ old('reason', $compOff->reason) }}</textarea>
                <small class="text-muted">{{ __('Provide details about why you worked extra hours') }}</small>
                @error('reason')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              {{-- Calculation Helper --}}
              <div class="mb-3">
                <div class="alert alert-info" id="calculation_helper" style="display: none;">
                  <i class="bx bx-calculator me-2"></i>
                  <strong>{{ __('Calculation') }}:</strong>
                  <span id="calculation_text"></span>
                </div>
              </div>

              {{-- Current Expiry Information --}}
              <div class="mb-3">
                <div class="alert alert-warning">
                  <i class="bx bx-time me-2"></i>
                  <strong>{{ __('Current Expiry Date') }}:</strong> 
                  {{ \Carbon\Carbon::parse($compOff->expiry_date)->format('M d, Y') }}
                  <span id="new_expiry_date_display"></span>
                  <br>
                  <small class="text-muted">{{ __('Compensatory off days expire 3 months from the worked date if not used.') }}</small>
                </div>
              </div>

              {{-- Approval Information --}}
              @if($compOff->approved_by_id || $compOff->rejected_by_id)
              <div class="card bg-light mb-3">
                <div class="card-body">
                  <h6 class="card-title">{{ __('Approval Information') }}</h6>
                  @if($compOff->approved_by_id)
                    <p class="mb-1"><strong>{{ __('Approved By') }}:</strong> {{ $compOff->approvedBy->first_name }} {{ $compOff->approvedBy->last_name }}</p>
                    <p class="mb-1"><strong>{{ __('Approved At') }}:</strong> {{ \Carbon\Carbon::parse($compOff->approved_at)->format('M d, Y H:i') }}</p>
                  @endif
                  @if($compOff->rejected_by_id)
                    <p class="mb-1"><strong>{{ __('Rejected By') }}:</strong> {{ $compOff->rejectedBy->first_name }} {{ $compOff->rejectedBy->last_name }}</p>
                    <p class="mb-1"><strong>{{ __('Rejected At') }}:</strong> {{ \Carbon\Carbon::parse($compOff->rejected_at)->format('M d, Y H:i') }}</p>
                  @endif
                  @if($compOff->approval_notes)
                    <p class="mb-0"><strong>{{ __('Notes') }}:</strong> {{ $compOff->approval_notes }}</p>
                  @endif
                </div>
              </div>
              @endif

              <div class="d-flex gap-2">
                @if($compOff->status === 'pending')
                <button type="submit" class="btn btn-primary">
                  <i class="bx bx-save me-1"></i>{{ __('Update Request') }}
                </button>
                @endif
                <a href="{{ route('hrcore.compensatory-offs.show', $compOff->id) }}" class="btn btn-label-info">
                  <i class="bx bx-show me-1"></i>{{ __('View Details') }}
                </a>
                <a href="{{ route('hrcore.compensatory-offs.index') }}" class="btn btn-label-secondary">
                  <i class="bx bx-arrow-back me-1"></i>{{ __('Back to List') }}
                </a>
              </div>
            </form>
          </div>
        </div>
      </div>

      {{-- Sidebar Information --}}
      <div class="col-md-4">
        {{-- Current Status --}}
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="card-title">{{ __('Request Status') }}</h5>
          </div>
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted">{{ __('Request ID') }}</span>
              <strong>#{{ $compOff->id }}</strong>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted">{{ __('Status') }}</span>
              <span class="badge bg-{{ $color }}">{{ ucfirst($compOff->status) }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted">{{ __('Created') }}</span>
              <strong>{{ \Carbon\Carbon::parse($compOff->created_at)->format('M d, Y') }}</strong>
            </div>
            @if($compOff->is_used)
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted">{{ __('Used Date') }}</span>
              <strong>{{ \Carbon\Carbon::parse($compOff->used_date)->format('M d, Y') }}</strong>
            </div>
            @endif
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-muted">{{ __('Usage Status') }}</span>
              @if($compOff->is_used)
                <span class="badge bg-success">{{ __('Used') }}</span>
              @elseif($compOff->status === 'approved' && \Carbon\Carbon::parse($compOff->expiry_date)->isPast())
                <span class="badge bg-danger">{{ __('Expired') }}</span>
              @elseif($compOff->status === 'approved')
                <span class="badge bg-primary">{{ __('Available') }}</span>
              @else
                <span class="badge bg-secondary">-</span>
              @endif
            </div>
          </div>
        </div>

        {{-- Guidelines --}}
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="card-title">{{ __('Edit Guidelines') }}</h5>
          </div>
          <div class="card-body">
            <ul class="list-unstyled mb-0">
              <li class="d-flex align-items-start mb-3">
                <i class="bx bx-info-circle text-info me-2 mt-1"></i>
                <div>
                  <strong>{{ __('Pending Requests') }}:</strong><br>
                  <small class="text-muted">{{ __('Only pending requests can be edited') }}</small>
                </div>
              </li>
              <li class="d-flex align-items-start mb-3">
                <i class="bx bx-time text-warning me-2 mt-1"></i>
                <div>
                  <strong>{{ __('Expiry Updates') }}:</strong><br>
                  <small class="text-muted">{{ __('Changing worked date will update expiry date') }}</small>
                </div>
              </li>
              <li class="d-flex align-items-start mb-0">
                <i class="bx bx-check-circle text-success me-2 mt-1"></i>
                <div>
                  <strong>{{ __('Re-approval') }}:</strong><br>
                  <small class="text-muted">{{ __('Edited requests may require re-approval') }}</small>
                </div>
              </li>
            </ul>
          </div>
        </div>

        {{-- Request Timeline --}}
        <div class="card">
          <div class="card-header">
            <h5 class="card-title">{{ __('Request Timeline') }}</h5>
          </div>
          <div class="card-body">
            <div class="timeline">
              {{-- Created --}}
              <div class="timeline-item">
                <div class="timeline-marker bg-primary"></div>
                <div class="timeline-content">
                  <h6 class="mb-1">{{ __('Request Created') }}</h6>
                  <small class="text-muted">{{ \Carbon\Carbon::parse($compOff->created_at)->format('M d, Y H:i') }}</small>
                </div>
              </div>

              {{-- Updated --}}
              @if($compOff->updated_at != $compOff->created_at)
              <div class="timeline-item">
                <div class="timeline-marker bg-info"></div>
                <div class="timeline-content">
                  <h6 class="mb-1">{{ __('Request Updated') }}</h6>
                  <small class="text-muted">{{ \Carbon\Carbon::parse($compOff->updated_at)->format('M d, Y H:i') }}</small>
                </div>
              </div>
              @endif

              {{-- Approved --}}
              @if($compOff->approved_at)
              <div class="timeline-item">
                <div class="timeline-marker bg-success"></div>
                <div class="timeline-content">
                  <h6 class="mb-1">{{ __('Request Approved') }}</h6>
                  <small class="text-muted">{{ \Carbon\Carbon::parse($compOff->approved_at)->format('M d, Y H:i') }}</small>
                </div>
              </div>
              @endif

              {{-- Used --}}
              @if($compOff->is_used)
              <div class="timeline-item">
                <div class="timeline-marker bg-success"></div>
                <div class="timeline-content">
                  <h6 class="mb-1">{{ __('Compensatory Off Used') }}</h6>
                  <small class="text-muted">{{ \Carbon\Carbon::parse($compOff->used_date)->format('M d, Y H:i') }}</small>
                </div>
              </div>
              @endif

              {{-- Expired --}}
              @if($compOff->status === 'approved' && !$compOff->is_used && \Carbon\Carbon::parse($compOff->expiry_date)->isPast())
              <div class="timeline-item">
                <div class="timeline-marker bg-danger"></div>
                <div class="timeline-content">
                  <h6 class="mb-1">{{ __('Compensatory Off Expired') }}</h6>
                  <small class="text-muted">{{ \Carbon\Carbon::parse($compOff->expiry_date)->format('M d, Y') }}</small>
                </div>
              </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    $(document).ready(function() {
      // Initialize Flatpickr for worked date
      flatpickr('#worked_date', {
        dateFormat: 'Y-m-d',
        maxDate: 'today',
        onChange: function(selectedDates, dateStr, instance) {
          updateExpiryDate();
        }
      });

      // Auto-calculate comp off days based on hours worked
      $('#hours_worked').on('input', function() {
        const hoursWorked = parseFloat($(this).val()) || 0;
        if (hoursWorked > 0) {
          // Standard calculation: hours ÷ 8 (8-hour working day)
          const compOffDays = Math.round((hoursWorked / 8) * 2) / 2; // Round to nearest 0.5
          $('#comp_off_days').val(Math.min(compOffDays, 5)); // Max 5 days
          
          updateCalculationHelper();
        } else {
          $('#comp_off_days').val('');
          $('#calculation_helper').hide();
        }
      });

      // Update calculation helper
      $('#comp_off_days').on('input', function() {
        updateCalculationHelper();
      });

      function updateCalculationHelper() {
        const hoursWorked = parseFloat($('#hours_worked').val()) || 0;
        const compOffDays = parseFloat($('#comp_off_days').val()) || 0;
        
        if (hoursWorked > 0 && compOffDays > 0) {
          const ratio = hoursWorked / compOffDays;
          let calculationText = `${hoursWorked} {{ __('hours') }} = ${compOffDays} {{ __('days') }}`;
          
          if (ratio > 8.5) {
            calculationText += ' <span class="text-warning">({{ __("Low compensation ratio") }})</span>';
          } else if (ratio < 7.5) {
            calculationText += ' <span class="text-success">({{ __("Good compensation ratio") }})</span>';
          }
          
          $('#calculation_text').html(calculationText);
          $('#calculation_helper').show();
        } else {
          $('#calculation_helper').hide();
        }
      }

      function updateExpiryDate() {
        const workedDate = $('#worked_date').val();
        const originalWorkedDate = '{{ \Carbon\Carbon::parse($compOff->worked_date)->format("Y-m-d") }}';
        
        if (workedDate && workedDate !== originalWorkedDate) {
          const date = new Date(workedDate);
          date.setMonth(date.getMonth() + 3);
          const newExpiryDate = date.toLocaleDateString();
          $('#new_expiry_date_display').html('<br><strong>{{ __("New Expiry Date") }}:</strong> <span class="text-warning">' + newExpiryDate + '</span>');
        } else {
          $('#new_expiry_date_display').html('');
        }
      }

      // Initialize calculation helper with current values
      updateCalculationHelper();
      updateExpiryDate();

      // Form validation
      $('#compOffForm').on('submit', function(e) {
        const hoursWorked = parseFloat($('#hours_worked').val()) || 0;
        const compOffDays = parseFloat($('#comp_off_days').val()) || 0;
        const workedDate = $('#worked_date').val();
        const reason = $('#reason').val().trim();

        let isValid = true;
        let errorMessage = '';

        // Validate worked date
        if (!workedDate) {
          isValid = false;
          errorMessage = '{{ __("Please select the worked date") }}';
        }

        // Validate hours worked
        if (hoursWorked < 0.5 || hoursWorked > 24) {
          isValid = false;
          errorMessage = '{{ __("Hours worked must be between 0.5 and 24") }}';
        }

        // Validate comp off days
        if (compOffDays < 0.5 || compOffDays > 5) {
          isValid = false;
          errorMessage = '{{ __("Compensatory off days must be between 0.5 and 5") }}';
        }

        // Validate reason
        if (reason.length < 10) {
          isValid = false;
          errorMessage = '{{ __("Please provide a detailed reason (minimum 10 characters)") }}';
        }

        // Check if comp off days are reasonable compared to hours worked
        if (hoursWorked > 0 && compOffDays > 0) {
          const ratio = hoursWorked / compOffDays;
          if (ratio < 2) { // Less than 2 hours per comp off day seems unreasonable
            if (!confirm('{{ __("The compensation ratio seems high. Are you sure you want to continue?") }}')) {
              isValid = false;
            }
          }
        }

        if (!isValid && errorMessage) {
          alert(errorMessage);
          e.preventDefault();
        }
      });
    });
  </script>

  <style>
    .timeline {
      position: relative;
      padding-left: 20px;
    }
    
    .timeline::before {
      content: '';
      position: absolute;
      left: 10px;
      top: 10px;
      bottom: 10px;
      width: 2px;
      background: #e3e6f0;
    }
    
    .timeline-item {
      position: relative;
      margin-bottom: 20px;
    }
    
    .timeline-marker {
      position: absolute;
      left: -15px;
      width: 12px;
      height: 12px;
      border-radius: 50%;
      top: 5px;
      border: 2px solid #fff;
      box-shadow: 0 0 0 2px #e3e6f0;
    }
    
    .timeline-content {
      padding-left: 10px;
    }
  </style>
@endsection