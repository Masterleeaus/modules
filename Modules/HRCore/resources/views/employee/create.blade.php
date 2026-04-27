@extends('layouts.layoutMaster')

@section('title', __('Add Employee'))

<!-- Vendor Styles -->
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/@form-validation/form-validation.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
  ])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/@form-validation/popular.js',
    'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
    'resources/assets/vendor/libs/@form-validation/auto-focus.js',
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
  ])
@endsection

@section('page-script')
  @vite(['resources/assets/js/app/hrcore-employees-create.js'])
@endsection

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    {{-- Breadcrumb Component --}}
    <x-breadcrumb
      :title="__('Add Employee')"
      :breadcrumbs="[
        ['name' => __('Human Resources'), 'url' => ''],
        ['name' => __('Employees'), 'url' => route('hrcore.employees.index')],
        ['name' => __('Add Employee'), 'url' => '']
      ]"
      :home-url="url('/')"
    />

    {{-- Default Password Notice --}}
    <div class="alert alert-info alert-dismissible mb-4" role="alert">
      <h6 class="alert-heading mb-1"><i class="bx bx-lock me-1"></i>{{ __('Default Password Information') }}</h6>
      <p class="mb-0">{{ __('New employees will be assigned the default password:') }} <strong>{{ $defaultPassword }}</strong></p>
      <small>{{ __('The employee should change this password upon first login.') }}</small>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <form id="createEmployeeForm" method="POST" action="{{ route('hrcore.employees.store') }}" enctype="multipart/form-data">
      @csrf
      
      {{-- Personal Information Card --}}
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title mb-0">{{ __('Personal Information') }}</h5>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="first_name" class="form-label">{{ __('First Name') }} <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') }}" required>
              @error('first_name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label for="last_name" class="form-label">{{ __('Last Name') }} <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') }}" required>
              @error('last_name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label for="email" class="form-label">{{ __('Email') }} <span class="text-danger">*</span></label>
              <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label for="phone" class="form-label">{{ __('Phone') }} <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" required>
              @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label for="code" class="form-label">{{ __('Employee Code') }} <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" required>
              @error('code')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-3">
              <label for="date_of_birth" class="form-label">{{ __('Date of Birth') }} <span class="text-danger">*</span></label>
              <input type="text" class="form-control flatpickr-date @error('date_of_birth') is-invalid @enderror" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
              @error('date_of_birth')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-3">
              <label for="gender" class="form-label">{{ __('Gender') }} <span class="text-danger">*</span></label>
              <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                <option value="">{{ __('Select Gender') }}</option>
                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
              </select>
              @error('gender')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-12">
              <label for="address" class="form-label">{{ __('Address') }}</label>
              <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2">{{ old('address') }}</textarea>
              @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-12">
              <label for="profile_picture" class="form-label">{{ __('Profile Picture') }}</label>
              <input type="file" class="form-control @error('profile_picture') is-invalid @enderror" id="profile_picture" name="profile_picture" accept="image/*">
              @error('profile_picture')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>
      </div>

      {{-- Work Information Card --}}
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title mb-0">{{ __('Work Information') }}</h5>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="date_of_joining" class="form-label">{{ __('Date of Joining') }} <span class="text-danger">*</span></label>
              <input type="text" class="form-control flatpickr-date @error('date_of_joining') is-invalid @enderror" id="date_of_joining" name="date_of_joining" value="{{ old('date_of_joining') }}" required>
              @error('date_of_joining')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label for="designation_id" class="form-label">{{ __('Designation') }} <span class="text-danger">*</span></label>
              <select class="form-select select2 @error('designation_id') is-invalid @enderror" id="designation_id" name="designation_id" required>
                <option value="">{{ __('Select Designation') }}</option>
                @foreach($designations as $designation)
                  <option value="{{ $designation->id }}" {{ old('designation_id') == $designation->id ? 'selected' : '' }}>
                    {{ $designation->name }}
                  </option>
                @endforeach
              </select>
              @error('designation_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label for="team_id" class="form-label">{{ __('Team') }} <span class="text-danger">*</span></label>
              <select class="form-select select2 @error('team_id') is-invalid @enderror" id="team_id" name="team_id" required>
                <option value="">{{ __('Select Team') }}</option>
                @foreach($teams as $team)
                  <option value="{{ $team->id }}" {{ old('team_id') == $team->id ? 'selected' : '' }}>
                    {{ $team->name }}
                  </option>
                @endforeach
              </select>
              @error('team_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label for="shift_id" class="form-label">{{ __('Shift') }} <span class="text-danger">*</span></label>
              <select class="form-select select2 @error('shift_id') is-invalid @enderror" id="shift_id" name="shift_id" required>
                <option value="">{{ __('Select Shift') }}</option>
                @foreach($shifts as $shift)
                  <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                    {{ $shift->name }}
                  </option>
                @endforeach
              </select>
              @error('shift_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label for="reporting_to_id" class="form-label">{{ __('Reporting To') }}</label>
              <select class="form-select select2 @error('reporting_to_id') is-invalid @enderror" id="reporting_to_id" name="reporting_to_id">
                <option value="">{{ __('Select Manager') }}</option>
                @foreach($reportingManagers as $manager)
                  <option value="{{ $manager->id }}" {{ old('reporting_to_id') == $manager->id ? 'selected' : '' }}>
                    {{ $manager->first_name }} {{ $manager->last_name }} ({{ $manager->code }})
                  </option>
                @endforeach
              </select>
              @error('reporting_to_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label for="role" class="form-label">{{ __('Role') }} <span class="text-danger">*</span></label>
              <select class="form-select select2 @error('role') is-invalid @enderror" id="role" name="role" required>
                <option value="">{{ __('Select Role') }}</option>
                @foreach($roles as $role)
                  <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                    {{ $role->name }}
                  </option>
                @endforeach
              </select>
              @error('role')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>
      </div>

      {{-- Attendance Configuration Card --}}
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title mb-0">{{ __('Attendance Configuration') }}</h5>
        </div>
        <div class="card-body">
          <div class="row g-3">
            {{-- Open Attendance Type (Always Available) --}}
            <div class="col-md-6">
              <label for="attendance_type" class="form-label">{{ __('Attendance Type') }} <span class="text-danger">*</span></label>
              <select class="form-select @error('attendance_type') is-invalid @enderror" id="attendance_type" name="attendance_type" required>
                <option value="">{{ __('Select Attendance Type') }}</option>
                
                {{-- Always Available --}}
                <optgroup label="{{ __('Standard') }}">
                  <option value="open" {{ old('attendance_type', 'open') == 'open' ? 'selected' : '' }}>{{ __('Open') }}</option>
                </optgroup>
                
                {{-- Module-Dependent Attendance Types --}}
                @if(count(array_filter([
                  Module::has('GeofenceSystem'),
                  Module::has('IpAddressAttendance'),
                  Module::has('QRAttendance'),
                  Module::has('SiteAttendance'),
                  Module::has('DynamicQrAttendance'),
                  Module::has('FaceAttendance')
                ])) > 0)
                  <optgroup label="{{ __('Requires Module Installation') }}">
                    @if(Module::has('GeofenceSystem') && Module::isEnabled('GeofenceSystem'))
                      <option value="geofence" {{ old('attendance_type') == 'geofence' ? 'selected' : '' }}>{{ __('Geofence') }}</option>
                    @endif
                    
                    @if(Module::has('IpAddressAttendance') && Module::isEnabled('IpAddressAttendance'))
                      <option value="ip_address" {{ old('attendance_type') == 'ip_address' ? 'selected' : '' }}>{{ __('IP Address') }}</option>
                    @endif
                    
                    @if(Module::has('QRAttendance') && Module::isEnabled('QRAttendance'))
                      <option value="qr_code" {{ old('attendance_type') == 'qr_code' ? 'selected' : '' }}>{{ __('QR Code') }}</option>
                    @endif
                    
                    @if(Module::has('SiteAttendance') && Module::isEnabled('SiteAttendance'))
                      <option value="site" {{ old('attendance_type') == 'site' ? 'selected' : '' }}>{{ __('Site') }}</option>
                    @endif
                    
                    @if(Module::has('DynamicQrAttendance') && Module::isEnabled('DynamicQrAttendance'))
                      <option value="dynamic_qr" {{ old('attendance_type') == 'dynamic_qr' ? 'selected' : '' }}>{{ __('Dynamic QR') }}</option>
                    @endif
                    
                    @if(Module::has('FaceAttendance') && Module::isEnabled('FaceAttendance'))
                      <option value="face_recognition" {{ old('attendance_type') == 'face_recognition' ? 'selected' : '' }}>{{ __('Face Recognition') }}</option>
                    @endif
                  </optgroup>
                @endif
              </select>
              @error('attendance_type')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              
              {{-- Module Not Installed Notice --}}
              <div class="form-text">
                <small class="text-muted">
                  {{ __('Note: Some attendance types require specific modules to be installed and enabled.') }}
                </small>
              </div>
            </div>
            
            <div class="col-md-6" id="attendanceConfigContainer">
              {{-- Dynamic content based on attendance type will be loaded here --}}
            </div>
          </div>
          
          {{-- Module Status Information --}}
          <div class="row mt-3">
            <div class="col-12">
              <div class="alert alert-info" role="alert">
                <h6 class="alert-heading mb-1">{{ __('Module Requirements') }}</h6>
                <small>
                  <ul class="mb-0 ps-3">
                    <li><strong>{{ __('Open') }}</strong>: {{ __('No additional module required (Default)') }}</li>
                    <li><strong>{{ __('Geofence') }}</strong>: {{ __('Requires GeofenceSystem module') }} @if(Module::has('GeofenceSystem')) <span class="badge bg-success">{{ __('Installed') }}</span> @else <span class="badge bg-danger">{{ __('Not Installed') }}</span> @endif</li>
                    <li><strong>{{ __('IP Address') }}</strong>: {{ __('Requires IpAddressAttendance module') }} @if(Module::has('IpAddressAttendance')) <span class="badge bg-success">{{ __('Installed') }}</span> @else <span class="badge bg-danger">{{ __('Not Installed') }}</span> @endif</li>
                    <li><strong>{{ __('QR Code') }}</strong>: {{ __('Requires QRAttendance module') }} @if(Module::has('QRAttendance')) <span class="badge bg-success">{{ __('Installed') }}</span> @else <span class="badge bg-danger">{{ __('Not Installed') }}</span> @endif</li>
                    <li><strong>{{ __('Site') }}</strong>: {{ __('Requires SiteAttendance module') }} @if(Module::has('SiteAttendance')) <span class="badge bg-success">{{ __('Installed') }}</span> @else <span class="badge bg-danger">{{ __('Not Installed') }}</span> @endif</li>
                    <li><strong>{{ __('Dynamic QR') }}</strong>: {{ __('Requires DynamicQrAttendance module') }} @if(Module::has('DynamicQrAttendance')) <span class="badge bg-success">{{ __('Installed') }}</span> @else <span class="badge bg-danger">{{ __('Not Installed') }}</span> @endif</li>
                    <li><strong>{{ __('Face Recognition') }}</strong>: {{ __('Requires FaceAttendance module') }} @if(Module::has('FaceAttendance')) <span class="badge bg-success">{{ __('Installed') }}</span> @else <span class="badge bg-danger">{{ __('Not Installed') }}</span> @endif</li>
                  </ul>
                </small>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Form Actions --}}
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('hrcore.employees.index') }}" class="btn btn-label-secondary">
              <i class="bx bx-x me-1"></i>{{ __('Cancel') }}
            </a>
            <button type="submit" class="btn btn-primary">
              <i class="bx bx-save me-1"></i>{{ __('Save Employee') }}
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
@endsection