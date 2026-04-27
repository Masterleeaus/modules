@extends('layouts.layoutMaster')

@section('title', __('Edit Employee'))

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
  @vite(['resources/assets/js/app/hrcore-employees-edit.js'])
@endsection

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    {{-- Breadcrumb Component --}}
    <x-breadcrumb
      :title="__('Edit Employee')"
      :breadcrumbs="[
        ['name' => __('Human Resources'), 'url' => ''],
        ['name' => __('Employees'), 'url' => route('hrcore.employees.index')],
        ['name' => $employee->first_name . ' ' . $employee->last_name, 'url' => route('hrcore.employees.show', $employee->id)],
        ['name' => __('Edit'), 'url' => '']
      ]"
      :home-url="url('/')"
    />

    <form id="editEmployeeForm" method="POST" action="{{ route('hrcore.employees.update', $employee->id) }}" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      
      {{-- Personal Information Card --}}
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title mb-0">{{ __('Personal Information') }}</h5>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="first_name" class="form-label">{{ __('First Name') }} <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                     id="first_name" name="first_name" value="{{ old('first_name', $employee->first_name) }}" 
                     @cannot('hrcore.edit-employee-personal-info') readonly @endcannot required>
              @error('first_name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            
            <div class="col-md-6">
              <label for="last_name" class="form-label">{{ __('Last Name') }} <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                     id="last_name" name="last_name" value="{{ old('last_name', $employee->last_name) }}" 
                     @cannot('hrcore.edit-employee-personal-info') readonly @endcannot required>
              @error('last_name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            
            <div class="col-md-6">
              <label for="email" class="form-label">{{ __('Email') }} <span class="text-danger">*</span></label>
              <input type="email" class="form-control @error('email') is-invalid @enderror" 
                     id="email" name="email" value="{{ old('email', $employee->email) }}" 
                     @cannot('hrcore.edit-employee-personal-info') readonly @endcannot required>
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            
            <div class="col-md-6">
              <label for="phone" class="form-label">{{ __('Phone') }} <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                     id="phone" name="phone" value="{{ old('phone', $employee->phone) }}" 
                     @cannot('hrcore.edit-employee-personal-info') readonly @endcannot required>
              @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            
            <div class="col-md-6">
              <label for="code" class="form-label">{{ __('Employee Code') }} <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('code') is-invalid @enderror" 
                     id="code" name="code" value="{{ old('code', $employee->code) }}" 
                     @cannot('hrcore.edit-employees') readonly @endcannot required>
              @error('code')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            
            <div class="col-md-3">
              <label for="date_of_birth" class="form-label">{{ __('Date of Birth') }} <span class="text-danger">*</span></label>
              <input type="text" class="form-control flatpickr-date @error('date_of_birth') is-invalid @enderror" 
                     id="date_of_birth" name="date_of_birth" 
                     value="{{ old('date_of_birth', $employee->dob) }}" 
                     @cannot('hrcore.edit-employee-personal-info') readonly @endcannot required>
              @error('date_of_birth')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            
            <div class="col-md-3">
              <label for="gender" class="form-label">{{ __('Gender') }} <span class="text-danger">*</span></label>
              <select class="form-select @error('gender') is-invalid @enderror" 
                      id="gender" name="gender" 
                      @cannot('hrcore.edit-employee-personal-info') disabled @endcannot required>
                <option value="">{{ __('Select Gender') }}</option>
                @foreach(\App\Enums\Gender::cases() as $gender)
                  <option value="{{ $gender->value }}" 
                    {{ old('gender', $employee->gender) == $gender->value ? 'selected' : '' }}>
                    {{ ucfirst($gender->value) }}
                  </option>
                @endforeach
              </select>
              @error('gender')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            
            <div class="col-12">
              <label for="address" class="form-label">{{ __('Address') }}</label>
              <textarea class="form-control @error('address') is-invalid @enderror" 
                        id="address" name="address" rows="2"
                        @cannot('hrcore.edit-employee-personal-info') readonly @endcannot>{{ old('address', $employee->address) }}</textarea>
              @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            
            <div class="col-md-6">
              <label for="profile_picture" class="form-label">{{ __('Profile Picture') }}</label>
              @if($employee->hasProfilePicture() || $employee->profile_picture)
                <div class="mb-2">
                  <img src="{{ $employee->getProfilePicture() }}" 
                       alt="{{ $employee->first_name }}" 
                       class="rounded" 
                       style="max-height: 100px;"
                       onerror="this.style.display='none'">
                </div>
              @endif
              <input type="file" class="form-control @error('profile_picture') is-invalid @enderror" 
                     id="profile_picture" name="profile_picture" accept="image/*"
                     @cannot('hrcore.edit-employee-personal-info') disabled @endcannot>
              <div class="form-text">{{ __('Max size: 5MB. Formats: JPG, PNG') }}</div>
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
              <input type="text" class="form-control flatpickr-date @error('date_of_joining') is-invalid @enderror" 
                     id="date_of_joining" name="date_of_joining" 
                     value="{{ old('date_of_joining', $employee->date_of_joining) }}" 
                     @cannot('hrcore.edit-employee-work-info') readonly @endcannot required>
              @error('date_of_joining')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            
            <div class="col-md-6">
              <label for="designation_id" class="form-label">{{ __('Designation') }} <span class="text-danger">*</span></label>
              <select class="form-select select2 @error('designation_id') is-invalid @enderror" 
                      id="designation_id" name="designation_id" 
                      @cannot('hrcore.edit-employee-work-info') disabled @endcannot required>
                <option value="">{{ __('Select Designation') }}</option>
                @foreach($designations as $designation)
                  <option value="{{ $designation->id }}" 
                    {{ old('designation_id', $employee->designation_id) == $designation->id ? 'selected' : '' }}>
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
              <select class="form-select select2 @error('team_id') is-invalid @enderror" 
                      id="team_id" name="team_id" 
                      @cannot('hrcore.edit-employee-work-info') disabled @endcannot required>
                <option value="">{{ __('Select Team') }}</option>
                @foreach($teams as $team)
                  <option value="{{ $team->id }}" 
                    {{ old('team_id', $employee->team_id) == $team->id ? 'selected' : '' }}>
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
              <select class="form-select select2 @error('shift_id') is-invalid @enderror" 
                      id="shift_id" name="shift_id" 
                      @cannot('hrcore.edit-employee-work-info') disabled @endcannot required>
                <option value="">{{ __('Select Shift') }}</option>
                @foreach($shifts as $shift)
                  <option value="{{ $shift->id }}" 
                    {{ old('shift_id', $employee->shift_id) == $shift->id ? 'selected' : '' }}>
                    {{ $shift->name }}
                  </option>
                @endforeach
              </select>
              @error('shift_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            
            <div class="col-md-6">
              <label for="reporting_to_id" class="form-label">{{ __('Reporting Manager') }}</label>
              <select class="form-select select2 @error('reporting_to_id') is-invalid @enderror" 
                      id="reporting_to_id" name="reporting_to_id"
                      @cannot('hrcore.edit-employee-work-info') disabled @endcannot>
                <option value="">{{ __('Select Manager') }}</option>
                @foreach($reportingManagers as $manager)
                  <option value="{{ $manager->id }}" 
                    {{ old('reporting_to_id', $employee->reporting_to_id) == $manager->id ? 'selected' : '' }}>
                    {{ $manager->first_name }} {{ $manager->last_name }} ({{ $manager->code }})
                  </option>
                @endforeach
              </select>
              @error('reporting_to_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            
            @can('hrcore.manage-employee-status')
            <div class="col-md-6">
              <label for="status" class="form-label">{{ __('Status') }} <span class="text-danger">*</span></label>
              <select class="form-select @error('status') is-invalid @enderror" 
                      id="status" name="status" required>
                @foreach(\App\Enums\UserAccountStatus::cases() as $status)
                  <option value="{{ $status->value }}" 
                    {{ old('status', $employee->status?->value ?? '') == $status->value ? 'selected' : '' }}>
                    {{ $status->label() }}
                  </option>
                @endforeach
              </select>
              @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            @endcan
            
            @can('manage-user-roles')
            <div class="col-md-6">
              <label for="role" class="form-label">{{ __('Role') }} <span class="text-danger">*</span></label>
              <select class="form-select select2 @error('role') is-invalid @enderror" 
                      id="role" name="role" required>
                <option value="">{{ __('Select Role') }}</option>
                @foreach($roles as $role)
                  <option value="{{ $role->name }}" 
                    {{ old('role', $currentRole ?? '') == $role->name ? 'selected' : '' }}>
                    {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                  </option>
                @endforeach
              </select>
              @error('role')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            @endcan
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
            {{-- Attendance Type Selection --}}
            <div class="col-md-6">
              <label for="attendance_type" class="form-label">{{ __('Attendance Type') }} <span class="text-danger">*</span></label>
              <select class="form-select @error('attendance_type') is-invalid @enderror" 
                      id="attendance_type" name="attendance_type" 
                      @cannot('hrcore.edit-employee-work-info') disabled @endcannot required>
                <option value="">{{ __('Select Attendance Type') }}</option>
                
                {{-- Always Available --}}
                <optgroup label="{{ __('Standard') }}">
                  <option value="open" {{ old('attendance_type', $employee->attendance_type ?? 'open') == 'open' ? 'selected' : '' }}>{{ __('Open') }}</option>
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
                      <option value="geofence" {{ old('attendance_type', $employee->attendance_type ?? '') == 'geofence' ? 'selected' : '' }}>{{ __('Geofence') }}</option>
                    @endif
                    
                    @if(Module::has('IpAddressAttendance') && Module::isEnabled('IpAddressAttendance'))
                      <option value="ip_address" {{ old('attendance_type', $employee->attendance_type ?? '') == 'ip_address' ? 'selected' : '' }}>{{ __('IP Address') }}</option>
                    @endif
                    
                    @if(Module::has('QRAttendance') && Module::isEnabled('QRAttendance'))
                      <option value="qr_code" {{ old('attendance_type', $employee->attendance_type ?? '') == 'qr_code' ? 'selected' : '' }}>{{ __('QR Code') }}</option>
                    @endif
                    
                    @if(Module::has('SiteAttendance') && Module::isEnabled('SiteAttendance'))
                      <option value="site" {{ old('attendance_type', $employee->attendance_type ?? '') == 'site' ? 'selected' : '' }}>{{ __('Site') }}</option>
                    @endif
                    
                    @if(Module::has('DynamicQrAttendance') && Module::isEnabled('DynamicQrAttendance'))
                      <option value="dynamic_qr" {{ old('attendance_type', $employee->attendance_type ?? '') == 'dynamic_qr' ? 'selected' : '' }}>{{ __('Dynamic QR') }}</option>
                    @endif
                    
                    @if(Module::has('FaceAttendance') && Module::isEnabled('FaceAttendance'))
                      <option value="face_recognition" {{ old('attendance_type', $employee->attendance_type ?? '') == 'face_recognition' ? 'selected' : '' }}>{{ __('Face Recognition') }}</option>
                    @endif
                  </optgroup>
                @endif
                
                {{-- Show current type if module is not installed but employee has it --}}
                @php
                  $moduleNotInstalled = false;
                  $currentType = old('attendance_type', $employee->attendance_type ?? 'open');
                  
                  if ($currentType && $currentType !== 'open') {
                    $moduleMap = [
                      'geofence' => 'GeofenceSystem',
                      'ip_address' => 'IpAddressAttendance',
                      'qr_code' => 'QRAttendance',
                      'site' => 'SiteAttendance',
                      'dynamic_qr' => 'DynamicQrAttendance',
                      'face_recognition' => 'FaceAttendance'
                    ];
                    
                    if (isset($moduleMap[$currentType]) && (!Module::has($moduleMap[$currentType]) || !Module::isEnabled($moduleMap[$currentType]))) {
                      $moduleNotInstalled = true;
                    }
                  }
                @endphp
                
                @if($moduleNotInstalled)
                  <optgroup label="{{ __('Current (Module Not Available)') }}">
                    <option value="{{ $currentType }}" selected>
                      {{ ucwords(str_replace('_', ' ', $currentType)) }} ({{ __('Module not installed') }})
                    </option>
                  </optgroup>
                @endif
              </select>
              @error('attendance_type')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              
              {{-- Module Status Alert --}}
              @if($moduleNotInstalled)
                <div class="alert alert-warning mt-2" role="alert">
                  <strong>{{ __('Warning') }}:</strong> {{ __('The current attendance type requires a module that is not installed. Please install the required module or change to an available type.') }}
                </div>
              @endif
            </div>
            
            {{-- Dynamic Attendance Configuration --}}
            <div class="col-md-6" id="attendanceConfigContainer">
              {{-- Dynamic content based on attendance type will be loaded here --}}
            </div>
          </div>
          
          {{-- Module Requirements Info --}}
          <div class="row mt-3">
            <div class="col-12">
              <div class="alert alert-info" role="alert">
                <h6 class="alert-heading mb-1">{{ __('Attendance Module Requirements') }}</h6>
                <div class="row">
                  <div class="col-md-6">
                    <small>
                      <ul class="mb-0 ps-3">
                        <li><strong>{{ __('Open') }}</strong>: {{ __('No module required (Default)') }}</li>
                        <li><strong>{{ __('Geofence') }}</strong>: @if(Module::has('GeofenceSystem')) <span class="badge bg-success">{{ __('Installed') }}</span> @else <span class="badge bg-danger">{{ __('Not Installed') }}</span> @endif</li>
                        <li><strong>{{ __('IP Address') }}</strong>: @if(Module::has('IpAddressAttendance')) <span class="badge bg-success">{{ __('Installed') }}</span> @else <span class="badge bg-danger">{{ __('Not Installed') }}</span> @endif</li>
                        <li><strong>{{ __('QR Code') }}</strong>: @if(Module::has('QRAttendance')) <span class="badge bg-success">{{ __('Installed') }}</span> @else <span class="badge bg-danger">{{ __('Not Installed') }}</span> @endif</li>
                      </ul>
                    </small>
                  </div>
                  <div class="col-md-6">
                    <small>
                      <ul class="mb-0 ps-3">
                        <li><strong>{{ __('Site') }}</strong>: @if(Module::has('SiteAttendance')) <span class="badge bg-success">{{ __('Installed') }}</span> @else <span class="badge bg-danger">{{ __('Not Installed') }}</span> @endif</li>
                        <li><strong>{{ __('Dynamic QR') }}</strong>: @if(Module::has('DynamicQrAttendance')) <span class="badge bg-success">{{ __('Installed') }}</span> @else <span class="badge bg-danger">{{ __('Not Installed') }}</span> @endif</li>
                        <li><strong>{{ __('Face Recognition') }}</strong>: @if(Module::has('FaceAttendance')) <span class="badge bg-success">{{ __('Installed') }}</span> @else <span class="badge bg-danger">{{ __('Not Installed') }}</span> @endif</li>
                      </ul>
                    </small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      {{-- Form Actions --}}
      <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('hrcore.employees.show', $employee->id) }}" class="btn btn-label-secondary">
          {{ __('Cancel') }}
        </a>
        <button type="submit" class="btn btn-primary">
          {{ __('Update Employee') }}
        </button>
      </div>
    </form>
  </div>
@endsection