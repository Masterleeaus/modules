@extends('layouts/layoutMaster')

@section('title', __('Create Compensatory Off Request'))

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
      :title="__('Create Compensatory Off Request')"
      :breadcrumbs="[
        ['name' => __('Human Resources'), 'url' => ''],
        ['name' => __('Compensatory Off'), 'url' => route('hrcore.compensatory-offs.index')],
        ['name' => __('Create'), 'url' => '']
      ]"
      :home-url="url('/')"
    />

    <div class="row">
      {{-- Compensatory Off Form --}}
      <div class="col-md-8">
        <div class="card">
          <div class="card-header">
            <h5 class="card-title">{{ __('Compensatory Off Request Details') }}</h5>
            <p class="card-subtitle text-muted mb-0">
              {{ __('Request compensatory time off for extra hours worked') }}
            </p>
          </div>
          <div class="card-body">
            <form action="{{ route('hrcore.compensatory-offs.store') }}" method="POST" id="compOffForm">
              @csrf
              
              <div class="row">
                {{-- Worked Date --}}
                <div class="col-md-6 mb-3">
                  <label for="worked_date" class="form-label">{{ __('Date Worked') }} <span class="text-danger">*</span></label>
                  <input type="text" id="worked_date" name="worked_date" class="form-control @error('worked_date') is-invalid @enderror" value="{{ old('worked_date') }}" required>
                  <small class="text-muted">{{ __('Select the date you worked extra hours') }}</small>
                  @error('worked_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                {{-- Hours Worked --}}
                <div class="col-md-6 mb-3">
                  <label for="hours_worked" class="form-label">{{ __('Extra Hours Worked') }} <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <input type="number" id="hours_worked" name="hours_worked" class="form-control @error('hours_worked') is-invalid @enderror" value="{{ old('hours_worked') }}" step="0.5" min="0.5" max="24" required>
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
                  <input type="number" id="comp_off_days" name="comp_off_days" class="form-control @error('comp_off_days') is-invalid @enderror" value="{{ old('comp_off_days') }}" step="0.5" min="0.5" max="5" required>
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
                <textarea id="reason" name="reason" class="form-control @error('reason') is-invalid @enderror" rows="4" required>{{ old('reason') }}</textarea>
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

              {{-- Expiry Information --}}
              <div class="mb-3">
                <div class="alert alert-warning">
                  <i class="bx bx-time me-2"></i>
                  <strong>{{ __('Important') }}:</strong> 
                  {{ __('Compensatory off days will expire 3 months from the worked date if not used.') }}
                  <span id="expiry_date_display"></span>
                </div>
              </div>

              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                  <i class="bx bx-save me-1"></i>{{ __('Submit Request') }}
                </button>
                <a href="{{ route('hrcore.compensatory-offs.index') }}" class="btn btn-label-secondary">
                  <i class="bx bx-arrow-back me-1"></i>{{ __('Cancel') }}
                </a>
              </div>
            </form>
          </div>
        </div>
      </div>

      {{-- Sidebar Information --}}
      <div class="col-md-4">
        {{-- Guidelines --}}
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="card-title">{{ __('Guidelines') }}</h5>
          </div>
          <div class="card-body">
            <ul class="list-unstyled mb-0">
              <li class="d-flex align-items-start mb-3">
                <i class="bx bx-check-circle text-success me-2 mt-1"></i>
                <div>
                  <strong>{{ __('Minimum Hours') }}:</strong><br>
                  <small class="text-muted">{{ __('You must have worked at least 0.5 extra hours to claim compensatory off') }}</small>
                </div>
              </li>
              <li class="d-flex align-items-start mb-3">
                <i class="bx bx-time text-warning me-2 mt-1"></i>
                <div>
                  <strong>{{ __('Expiry Period') }}:</strong><br>
                  <small class="text-muted">{{ __('Compensatory off expires after 3 months from worked date') }}</small>
                </div>
              </li>
              <li class="d-flex align-items-start mb-3">
                <i class="bx bx-calendar text-primary me-2 mt-1"></i>
                <div>
                  <strong>{{ __('Usage') }}:</strong><br>
                  <small class="text-muted">{{ __('Can be used as regular leave once approved') }}</small>
                </div>
              </li>
              <li class="d-flex align-items-start mb-0">
                <i class="bx bx-user-check text-info me-2 mt-1"></i>
                <div>
                  <strong>{{ __('Approval') }}:</strong><br>
                  <small class="text-muted">{{ __('Requires manager approval before it can be used') }}</small>
                </div>
              </li>
            </ul>
          </div>
        </div>

        {{-- Calculation Formula --}}
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="card-title">{{ __('Calculation Formula') }}</h5>
          </div>
          <div class="card-body">
            <div class="text-center mb-3">
              <div class="bg-light rounded p-3">
                <h6 class="mb-2">{{ __('Standard Formula') }}</h6>
                <div class="border rounded p-2 bg-white">
                  <strong>{{ __('Comp Off Days') }} = {{ __('Extra Hours') }} ÷ 8</strong>
                </div>
                <small class="text-muted d-block mt-2">{{ __('Based on 8-hour working day') }}</small>
              </div>
            </div>
            
            <div class="table-responsive">
              <table class="table table-sm">
                <thead>
                  <tr>
                    <th>{{ __('Hours') }}</th>
                    <th>{{ __('Comp Off') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>4 {{ __('hours') }}</td>
                    <td>0.5 {{ __('days') }}</td>
                  </tr>
                  <tr>
                    <td>8 {{ __('hours') }}</td>
                    <td>1.0 {{ __('day') }}</td>
                  </tr>
                  <tr>
                    <td>12 {{ __('hours') }}</td>
                    <td>1.5 {{ __('days') }}</td>
                  </tr>
                  <tr>
                    <td>16 {{ __('hours') }}</td>
                    <td>2.0 {{ __('days') }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        {{-- Recent Requests --}}
        <div class="card">
          <div class="card-header">
            <h5 class="card-title">{{ __('Your Recent Requests') }}</h5>
          </div>
          <div class="card-body">
            <div id="recent_requests">
              <p class="text-muted mb-0">{{ __('Loading recent requests...') }}</p>
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
        if (workedDate) {
          const date = new Date(workedDate);
          date.setMonth(date.getMonth() + 3);
          const expiryDate = date.toLocaleDateString();
          $('#expiry_date_display').html('<br><strong>{{ __("Expiry Date") }}:</strong> ' + expiryDate);
        } else {
          $('#expiry_date_display').html('');
        }
      }

      // Load recent requests
      loadRecentRequests();

      function loadRecentRequests() {
        // This would typically be an AJAX call to get recent requests
        // For now, showing placeholder
        setTimeout(function() {
          $('#recent_requests').html(`
            <div class="text-center">
              <i class="bx bx-info-circle text-muted" style="font-size: 2rem;"></i>
              <p class="text-muted mb-0 mt-2">{{ __('No recent requests found') }}</p>
            </div>
          `);
        }, 1000);
      }

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

      // Initialize if values are already present (form reload with errors)
      if ($('#hours_worked').val()) {
        updateCalculationHelper();
      }
      if ($('#worked_date').val()) {
        updateExpiryDate();
      }
    });
  </script>
@endsection