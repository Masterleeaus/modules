@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', __('Apply for Leave'))

@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
  ])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
  ])
@endsection

@section('page-script')
  @vite(['resources/assets/js/app/hrcore-leave-apply.js'])
@endsection

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    {{-- Breadcrumb --}}
    <x-breadcrumb
      :title="__('Apply for Leave')"
      :breadcrumbs="[
        ['name' => __('Self Service'), 'url' => ''],
        ['name' => __('My Leaves'), 'url' => route('hrcore.my.leaves')],
        ['name' => __('Apply Leave'), 'url' => '']
      ]"
    />

    <div class="row">
      <div class="col-xl-8 col-lg-10 col-md-12">
        <div class="card">
          <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Leave Application Form') }}</h5>
          </div>
          <form id="leaveApplicationForm" method="POST" action="{{ route('hrcore.my.leaves.store') }}" enctype="multipart/form-data">
            @csrf
            {{-- Hidden user_id field for self application --}}
            @unless(auth()->user()->can('hrcore.create-leave-for-others'))
              <input type="hidden" name="user_id" value="{{ auth()->id() }}">
            @endunless
            <div class="card-body">
              {{-- Leave Type --}}
              <div class="row mb-3">
                <div class="col-12">
                  <label class="form-label" for="leave_type_id">{{ __('Leave Type') }} <span class="text-danger">*</span></label>
                  <select id="leave_type_id" name="leave_type_id" class="form-select select2" required>
                    <option value="">{{ __('Select Leave Type') }}</option>
                    @foreach($leaveTypes as $type)
                      <option value="{{ $type->id }}"
                        data-days="{{ $type->user_available ?? $type->days_allowed ?? 0 }}"
                        data-color="{{ $type->color }}">
                        {{ $type->name }} ({{ $type->user_available ?? $type->days_allowed ?? 0 }} {{ __('days available') }})
                      </option>
                    @endforeach
                  </select>
                  <div class="invalid-feedback"></div>
                </div>
              </div>

              {{-- Date Range --}}
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label" for="from_date">{{ __('From Date') }} <span class="text-danger">*</span></label>
                  <input type="text" id="from_date" name="from_date" class="form-control flatpickr-date" placeholder="YYYY-MM-DD" required />
                  <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="to_date">{{ __('To Date') }} <span class="text-danger">*</span></label>
                  <input type="text" id="to_date" name="to_date" class="form-control flatpickr-date" placeholder="YYYY-MM-DD" required />
                  <div class="invalid-feedback"></div>
                </div>
              </div>

              {{-- Half Day Options --}}
              <div class="row mb-3">
                <div class="col-md-6">
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="is_half_day" name="is_half_day" value="1">
                    <label class="form-check-label" for="is_half_day">
                      {{ __('Half Day Leave') }}
                    </label>
                  </div>
                </div>
                <div class="col-md-6" id="half_day_type_container" style="display: none;">
                  <label class="form-label" for="half_day_type">{{ __('Half Day Type') }}</label>
                  <select id="half_day_type" name="half_day_type" class="form-select">
                    <option value="">{{ __('Select') }}</option>
                    <option value="first_half">{{ __('First Half') }}</option>
                    <option value="second_half">{{ __('Second Half') }}</option>
                  </select>
                </div>
              </div>

              {{-- Total Days --}}
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label" for="total_days">{{ __('Total Days') }}</label>
                  <input type="number" id="total_days" name="total_days" class="form-control" readonly step="0.5" />
                </div>
              </div>

              {{-- Reason --}}
              <div class="row mb-3">
                <div class="col-12">
                  <label class="form-label" for="user_notes">{{ __('Reason for Leave') }} <span class="text-danger">*</span></label>
                  <textarea id="user_notes" name="user_notes" class="form-control" rows="3" placeholder="{{ __('Please provide the reason for your leave request...') }}" required></textarea>
                  <div class="invalid-feedback"></div>
                </div>
              </div>

              {{-- Emergency Contact --}}
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label" for="emergency_contact">{{ __('Emergency Contact Person') }}</label>
                  <input type="text" id="emergency_contact" name="emergency_contact" class="form-control" placeholder="{{ __('Contact Name') }}" />
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="emergency_phone">{{ __('Emergency Phone') }}</label>
                  <input type="text" id="emergency_phone" name="emergency_phone" class="form-control" placeholder="{{ __('Phone Number') }}" />
                </div>
              </div>

              {{-- Abroad Leave --}}
              <div class="row mb-3">
                <div class="col-md-6">
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="is_abroad" name="is_abroad" value="1">
                    <label class="form-check-label" for="is_abroad">
                      {{ __('Traveling Abroad') }}
                    </label>
                  </div>
                </div>
                <div class="col-md-6" id="abroad_location_container" style="display: none;">
                  <label class="form-label" for="abroad_location">{{ __('Location') }}</label>
                  <input type="text" id="abroad_location" name="abroad_location" class="form-control" placeholder="{{ __('Country/City') }}" />
                </div>
              </div>

              {{-- Document Upload --}}
              <div class="row mb-3">
                <div class="col-12">
                  <label class="form-label" for="document">{{ __('Supporting Document') }}</label>
                  <input type="file" id="document" name="document" class="form-control" accept=".pdf,.jpg,.jpeg,.png" />
                  <small class="text-muted">{{ __('Upload medical certificate or other supporting documents (PDF, JPG, PNG - Max 2MB)') }}</small>
                </div>
              </div>

              {{-- Current Leave Balance Summary --}}
              <div class="alert alert-info">
                <h6 class="alert-heading mb-2">{{ __('Leave Balance Summary') }}</h6>
                <div id="leave-balance-summary">
                  <p class="mb-0">{{ __('Select a leave type to view balance details') }}</p>
                </div>
              </div>
            </div>

            <div class="card-footer">
              <button type="submit" class="btn btn-primary me-2">
                <i class="bx bx-send me-1"></i> {{ __('Submit Application') }}
              </button>
              <a href="{{ route('hrcore.my.leaves') }}" class="btn btn-label-secondary">
                <i class="bx bx-x me-1"></i> {{ __('Cancel') }}
              </a>
            </div>
          </form>
        </div>
      </div>

      {{-- Leave Policy Card --}}
      <div class="col-xl-4 col-lg-2 col-md-12">
        <div class="card">
          <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Leave Policies') }}</h5>
          </div>
          <div class="card-body">
            <div class="accordion" id="leavePoliciesAccordion">
              @foreach($leaveTypes as $index => $type)
                <div class="accordion-item">
                  <h2 class="accordion-header" id="heading{{ $index }}">
                    <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button"
                      data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}"
                      aria-expanded="{{ $index === 0 ? 'true' : 'false' }}">
                      {{ $type->name }}
                      <span class="badge rounded-pill ms-auto" style="background-color: {{ $type->color }}">
                        {{ $type->user_available ?? $type->days_allowed ?? 0 }} {{ __('days') }}
                      </span>
                    </button>
                  </h2>
                  <div id="collapse{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                    data-bs-parent="#leavePoliciesAccordion">
                    <div class="accordion-body">
                      <small class="text-muted">{{ $type->description ?? __('No description available') }}</small>
                      <hr>
                      <div class="d-flex justify-content-between mb-1">
                        <span>{{ __('Annual Entitlement:') }}</span>
                        <strong>{{ $type->user_entitled ?? $type->days_allowed ?? 0 }} {{ __('days') }}</strong>
                      </div>
                      @if($type->carry_forward)
                        <div class="d-flex justify-content-between mb-1">
                          <span>{{ __('Carry Forward:') }}</span>
                          <strong>{{ $type->user_carried ?? 0 }} {{ __('days') }}</strong>
                        </div>
                      @endif
                      @if(isset($type->user_additional) && $type->user_additional > 0)
                        <div class="d-flex justify-content-between mb-1">
                          <span>{{ __('Additional:') }}</span>
                          <strong>{{ $type->user_additional ?? 0 }} {{ __('days') }}</strong>
                        </div>
                      @endif
                      <div class="d-flex justify-content-between mb-1">
                        <span>{{ __('Used:') }}</span>
                        <strong class="text-warning">{{ $type->user_used ?? 0 }} {{ __('days') }}</strong>
                      </div>
                      <hr>
                      <div class="d-flex justify-content-between">
                        <span><strong>{{ __('Available:') }}</strong></span>
                        <strong class="text-success">{{ $type->user_available ?? $type->days_allowed ?? 0 }} {{ __('days') }}</strong>
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
