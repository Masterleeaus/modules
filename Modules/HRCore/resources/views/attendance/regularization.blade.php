@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', __('My Regularization Requests'))

@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
  ])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
  ])
@endsection

@section('page-script')
  @vite(['resources/assets/js/app/hrcore-regularization.js'])
@endsection

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    {{-- Breadcrumb --}}
    <x-breadcrumb
      :title="__('My Regularization Requests')"
      :breadcrumbs="[
        ['name' => __('Self Service'), 'url' => ''],
        ['name' => __('My Regularization'), 'url' => '']
      ]"
    />

    {{-- Quick Stats Row --}}
    <div class="row mb-4">
      {{-- Monthly Limit Card --}}
      <div class="col-md-3">
        <div class="card">
          <div class="card-body">
            @php
              $monthlyRequests = $regularizationRequests->filter(function($req) {
                return $req->created_at >= now()->startOfMonth();
              })->count();
              $limit = 3;
              $remaining = max(0, $limit - $monthlyRequests);
            @endphp
            <div class="d-flex align-items-center">
              <div class="avatar avatar-md me-3">
                <div class="avatar-initial bg-label-primary rounded">
                  <i class="bx bx-calendar-check bx-sm"></i>
                </div>
              </div>
              <div>
                <h4 class="mb-0">{{ $monthlyRequests }}/{{ $limit }}</h4>
                <small class="text-muted">{{ __('Monthly Limit') }}</small>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      {{-- Pending Requests --}}
      <div class="col-md-3">
        <div class="card">
          <div class="card-body">
            @php
              $pendingCount = $regularizationRequests->where('status', 'pending')->count();
            @endphp
            <div class="d-flex align-items-center">
              <div class="avatar avatar-md me-3">
                <div class="avatar-initial bg-label-warning rounded">
                  <i class="bx bx-time-five bx-sm"></i>
                </div>
              </div>
              <div>
                <h4 class="mb-0">{{ $pendingCount }}</h4>
                <small class="text-muted">{{ __('Pending') }}</small>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      {{-- Approved Requests --}}
      <div class="col-md-3">
        <div class="card">
          <div class="card-body">
            @php
              $approvedCount = $regularizationRequests->where('status', 'approved')->count();
            @endphp
            <div class="d-flex align-items-center">
              <div class="avatar avatar-md me-3">
                <div class="avatar-initial bg-label-success rounded">
                  <i class="bx bx-check-circle bx-sm"></i>
                </div>
              </div>
              <div>
                <h4 class="mb-0">{{ $approvedCount }}</h4>
                <small class="text-muted">{{ __('Approved') }}</small>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      {{-- Rejected Requests --}}
      <div class="col-md-3">
        <div class="card">
          <div class="card-body">
            @php
              $rejectedCount = $regularizationRequests->where('status', 'rejected')->count();
            @endphp
            <div class="d-flex align-items-center">
              <div class="avatar avatar-md me-3">
                <div class="avatar-initial bg-label-danger rounded">
                  <i class="bx bx-x-circle bx-sm"></i>
                </div>
              </div>
              <div>
                <h4 class="mb-0">{{ $rejectedCount }}</h4>
                <small class="text-muted">{{ __('Rejected') }}</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- My Regularization Requests with Create Button in Header --}}
    <div class="card">
      <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">{{ __('My Requests') }}</h5>
          <div class="d-flex align-items-center gap-3">
            {{-- Search Input --}}
            <div class="input-group" style="width: 250px;">
              <span class="input-group-text"><i class="bx bx-search"></i></span>
              <input type="text" id="searchInput" class="form-control" placeholder="{{ __('Search...') }}" />
            </div>
            {{-- Create Button --}}
            <button type="button" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#regularizationOffcanvas">
              <i class="bx bx-plus me-1"></i> {{ __('Submit New Request') }}
            </button>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover" id="regularizationTable">
            <thead>
              <tr>
                <th>{{ __('Request Date') }}</th>
                <th>{{ __('Attendance Date') }}</th>
                <th>{{ __('Type') }}</th>
                <th>{{ __('Reason') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Approved By') }}</th>
                <th>{{ __('Actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($regularizationRequests as $request)
                <tr>
                  <td>{{ \Carbon\Carbon::parse($request->created_at)->format('M d, Y') }}</td>
                  <td>
                    <strong>{{ \Carbon\Carbon::parse($request->date)->format('M d, Y') }}</strong>
                  </td>
                  <td>
                    <span class="badge bg-label-primary">
                      {{ str_replace('_', ' ', ucfirst($request->type)) }}
                    </span>
                  </td>
                  <td>
                    <span class="text-truncate d-inline-block" style="max-width: 200px;" 
                      data-bs-toggle="tooltip" title="{{ $request->reason }}">
                      {{ $request->reason }}
                    </span>
                  </td>
                  <td>
                    @php
                      $statusColors = [
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger'
                      ];
                    @endphp
                    <span class="badge bg-label-{{ $statusColors[$request->status] ?? 'primary' }}">
                      {{ ucfirst($request->status) }}
                    </span>
                  </td>
                  <td>
                    @if($request->approved_by)
                      @php
                        $approver = \App\Models\User::find($request->approved_by);
                      @endphp
                      {{ $approver ? $approver->getFullName() : '--' }}
                    @else
                      --
                    @endif
                  </td>
                  <td>
                    @if($request->status == 'pending')
                      <button class="btn btn-sm btn-label-danger" onclick="cancelRequest({{ $request->id }})">
                        <i class="bx bx-x"></i> {{ __('Cancel') }}
                      </button>
                    @elseif(isset($request->attachments) && is_array($request->attachments) && count($request->attachments) > 0)
                      <a href="{{ asset('storage/' . $request->attachments[0]['path']) }}" 
                        class="btn btn-sm btn-label-info" target="_blank">
                        <i class="bx bx-file"></i> {{ __('View') }}
                      </a>
                    @else
                      <span class="text-muted">--</span>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- Guidelines Card --}}
    <div class="row mt-4">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Important Guidelines') }}</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-4">
                <div class="d-flex mb-3">
                  <div class="avatar avatar-sm me-2">
                    <span class="avatar-initial rounded bg-label-warning">
                      <i class="bx bx-time"></i>
                    </span>
                  </div>
                  <div>
                    <h6 class="mb-1">{{ __('7 Days Window') }}</h6>
                    <small class="text-muted">{{ __('Can only request for past 7 days') }}</small>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="d-flex mb-3">
                  <div class="avatar avatar-sm me-2">
                    <span class="avatar-initial rounded bg-label-info">
                      <i class="bx bx-calendar"></i>
                    </span>
                  </div>
                  <div>
                    <h6 class="mb-1">{{ __('Monthly Limit: 3') }}</h6>
                    <small class="text-muted">{{ __('Maximum 3 requests per month') }}</small>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="d-flex mb-3">
                  <div class="avatar avatar-sm me-2">
                    <span class="avatar-initial rounded bg-label-success">
                      <i class="bx bx-check-shield"></i>
                    </span>
                  </div>
                  <div>
                    <h6 class="mb-1">{{ __('Manager Approval') }}</h6>
                    <small class="text-muted">{{ __('All requests need approval') }}</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Regularization Request Offcanvas --}}
  <div class="offcanvas offcanvas-end" tabindex="-1" id="regularizationOffcanvas" aria-labelledby="regularizationOffcanvasLabel" style="width: 500px;">
    <div class="offcanvas-header">
      <h5 id="regularizationOffcanvasLabel" class="offcanvas-title">{{ __('Submit Regularization Request') }}</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      <form id="regularizationForm" method="POST" action="{{ route('hrcore.attendance-regularization.store') }}">
        @csrf
        <div class="mb-3">
          <label class="form-label" for="date">{{ __('Attendance Date') }} <span class="text-danger">*</span></label>
          <input type="text" id="date" name="date" class="form-control flatpickr-date" 
            placeholder="YYYY-MM-DD" required />
          <div class="invalid-feedback"></div>
        </div>

        <div class="mb-3">
          <label class="form-label" for="type">{{ __('Regularization Type') }} <span class="text-danger">*</span></label>
          <select id="type" name="type" class="form-select" required>
            <option value="">{{ __('Select Type') }}</option>
            <option value="missing_checkin">{{ __('Missing Check-in') }}</option>
            <option value="missing_checkout">{{ __('Missing Check-out') }}</option>
            <option value="wrong_time">{{ __('Wrong Timing Recorded') }}</option>
            <option value="forgot_punch">{{ __('Forgot to Punch') }}</option>
            <option value="other">{{ __('Other') }}</option>
          </select>
          <div class="invalid-feedback"></div>
        </div>

        <div class="mb-3">
          <label class="form-label" for="requested_check_in_time">{{ __('Requested Check-in Time') }}</label>
          <input type="time" id="requested_check_in_time" name="requested_check_in_time" class="form-control" />
        </div>

        <div class="mb-3">
          <label class="form-label" for="requested_check_out_time">{{ __('Requested Check-out Time') }}</label>
          <input type="time" id="requested_check_out_time" name="requested_check_out_time" class="form-control" />
        </div>

        <div class="mb-3">
          <label class="form-label" for="reason">{{ __('Reason') }} <span class="text-danger">*</span></label>
          <textarea id="reason" name="reason" class="form-control" rows="3" 
            placeholder="{{ __('Please provide detailed reason for regularization...') }}" required></textarea>
          <div class="invalid-feedback"></div>
        </div>

        <div class="mb-3">
          <label class="form-label" for="attachments">{{ __('Supporting Documents') }}</label>
          <input type="file" id="attachments" name="attachments[]" class="form-control" 
            accept=".pdf,.jpg,.jpeg,.png" multiple />
          <small class="text-muted">{{ __('Upload supporting documents (PDF, JPG, PNG - Max 5MB per file)') }}</small>
        </div>

        <div class="alert alert-info">
          <i class="bx bx-info-circle me-1"></i>
          {{ __('Regularization requests are subject to manager approval. You will be notified once your request is processed.') }}
        </div>

        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary flex-fill">
            <i class="bx bx-send me-1"></i> {{ __('Submit Request') }}
          </button>
          <button type="button" class="btn btn-label-secondary flex-fill" data-bs-dismiss="offcanvas">
            <i class="bx bx-x me-1"></i> {{ __('Cancel') }}
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection