@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', __('My Profile'))

@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss'
  ])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js'
  ])
@endsection

@section('page-style')
<style>
  .user-profile-header-banner {
    position: relative;
    height: 250px;
    overflow: hidden;
    border-radius: 0.375rem 0.375rem 0 0;
  }
  
  .user-profile-header-banner img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
  }
  
  .user-profile-img {
    width: 120px;
    height: 120px;
    border: 4px solid #fff;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    object-fit: cover;
    position: relative;
    z-index: 1;
  }
  
  .user-profile-header {
    position: relative;
  }
  
  .user-profile-initials {
    width: 120px;
    height: 120px;
    border: 4px solid #fff;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    font-weight: 600;
    color: #fff;
    position: relative;
    z-index: 1;
    text-align: center;
    line-height: 1;
  }
</style>
@endsection

@section('page-script')
  @vite(['resources/assets/js/app/hrcore-self-service-profile.js'])
@endsection

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    {{-- Header --}}
    <div class="row">
      <div class="col-12">
        <div class="card mb-4">
          <div class="user-profile-header-banner">
            <img src="{{ asset('assets/img/pages/profile-banner.png') }}" alt="Banner image">
          </div>
          <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
            <div class="flex-shrink-0 mt-n5 mx-sm-0 mx-auto">
              @if($user->profile_photo_path)
                <img src="{{ asset('storage/' . $user->profile_photo_path) }}" 
                  alt="user image" 
                  class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img"
                  id="profileImage">
              @else
                @php
                  $names = explode(' ', $user->name);
                  $initials = '';
                  foreach($names as $name) {
                    if(!empty($name)) {
                      $initials .= strtoupper(substr($name, 0, 1));
                    }
                  }
                  // Limit to 2 characters
                  $initials = substr($initials, 0, 2);
                @endphp
                <div class="d-flex ms-0 ms-sm-4 rounded-circle user-profile-initials" id="profileImage">
                  <span>{{ $initials }}</span>
                </div>
              @endif
            </div>
            <div class="flex-grow-1 mt-3 mt-sm-5">
              <div class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4">
                <div class="user-profile-info">
                  <h4>{{ $user->name }}</h4>
                  <ul class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-2">
                    <li class="list-inline-item fw-medium">
                      <i class="bx bx-id-card"></i> {{ $user->employee_id ?? 'N/A' }}
                    </li>
                    <li class="list-inline-item fw-medium">
                      <i class="bx bx-briefcase"></i> {{ $user->designation->name ?? 'N/A' }}
                    </li>
                    <li class="list-inline-item fw-medium">
                      <i class="bx bx-building"></i> {{ $user->designation->department->name ?? 'N/A' }}
                    </li>
                    <li class="list-inline-item fw-medium">
                      <i class="bx bx-calendar"></i> {{ __('Joined') }} {{ $user->joining_date ? \Carbon\Carbon::parse($user->joining_date)->format('M d, Y') : 'N/A' }}
                    </li>
                  </ul>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadPhotoModal">
                  <i class="bx bx-camera me-1"></i>{{ __('Change Photo') }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Nav tabs --}}
    <div class="nav-align-top mb-4">
      <ul class="nav nav-pills mb-3" role="tablist">
        <li class="nav-item">
          <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-profile" aria-controls="navs-pills-profile" aria-selected="true">
            <i class="bx bx-user me-1"></i> {{ __('Profile') }}
          </button>
        </li>
        <li class="nav-item">
          <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-personal" aria-controls="navs-pills-personal" aria-selected="false">
            <i class="bx bx-info-circle me-1"></i> {{ __('Personal Info') }}
          </button>
        </li>
        <li class="nav-item">
          <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-emergency" aria-controls="navs-pills-emergency" aria-selected="false">
            <i class="bx bx-phone-call me-1"></i> {{ __('Emergency Contact') }}
          </button>
        </li>
        <li class="nav-item">
          <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-security" aria-controls="navs-pills-security" aria-selected="false">
            <i class="bx bx-lock-alt me-1"></i> {{ __('Security') }}
          </button>
        </li>
      </ul>
      
      <div class="tab-content">
        {{-- Profile Tab --}}
        <div class="tab-pane fade show active" id="navs-pills-profile" role="tabpanel">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title mb-4">{{ __('Professional Information') }}</h5>
              <div class="row">
                <div class="col-md-6">
                  <dl class="row mb-0">
                    <dt class="col-sm-4 fw-medium">{{ __('Employee ID') }}</dt>
                    <dd class="col-sm-8">{{ $user->employee_id ?? 'N/A' }}</dd>
                    
                    <dt class="col-sm-4 fw-medium">{{ __('Email') }}</dt>
                    <dd class="col-sm-8">{{ $user->email }}</dd>
                    
                    <dt class="col-sm-4 fw-medium">{{ __('Department') }}</dt>
                    <dd class="col-sm-8">{{ $user->designation->department->name ?? 'N/A' }}</dd>
                    
                    <dt class="col-sm-4 fw-medium">{{ __('Designation') }}</dt>
                    <dd class="col-sm-8">{{ $user->designation->name ?? 'N/A' }}</dd>
                    
                    <dt class="col-sm-4 fw-medium">{{ __('Team') }}</dt>
                    <dd class="col-sm-8">{{ $user->team->name ?? 'N/A' }}</dd>
                  </dl>
                </div>
                <div class="col-md-6">
                  <dl class="row mb-0">
                    <dt class="col-sm-4 fw-medium">{{ __('Manager') }}</dt>
                    <dd class="col-sm-8">{{ $user->manager->name ?? 'N/A' }}</dd>
                    
                    <dt class="col-sm-4 fw-medium">{{ __('Joining Date') }}</dt>
                    <dd class="col-sm-8">{{ $user->joining_date ? \Carbon\Carbon::parse($user->joining_date)->format('M d, Y') : 'N/A' }}</dd>
                    
                    <dt class="col-sm-4 fw-medium">{{ __('Shift') }}</dt>
                    <dd class="col-sm-8">{{ $user->shift->name ?? 'Default' }}</dd>
                    
                    <dt class="col-sm-4 fw-medium">{{ __('Employment Type') }}</dt>
                    <dd class="col-sm-8">{{ ucfirst($user->employment_type ?? 'Full Time') }}</dd>
                    
                    <dt class="col-sm-4 fw-medium">{{ __('Status') }}</dt>
                    <dd class="col-sm-8">
                      <span class="badge bg-label-{{ $user->status->value == 'active' ? 'success' : 'secondary' }}">
                        {{ ucfirst($user->status->value) }}
                      </span>
                    </dd>
                  </dl>
                </div>
              </div>
              
              {{-- Quick Stats --}}
              <hr class="my-4">
              <h6 class="mb-3">{{ __('This Month Statistics') }}</h6>
              <div class="row g-3">
                <div class="col-6 col-md-3">
                  <div class="d-flex align-items-center">
                    <div class="avatar">
                      <div class="avatar-initial bg-label-success rounded">
                        <i class="bx bx-check"></i>
                      </div>
                    </div>
                    <div class="ms-3">
                      <div class="small mb-1">{{ __('Present') }}</div>
                      <h5 class="mb-0">{{ $attendanceStats['present'] ?? 0 }}</h5>
                    </div>
                  </div>
                </div>
                <div class="col-6 col-md-3">
                  <div class="d-flex align-items-center">
                    <div class="avatar">
                      <div class="avatar-initial bg-label-danger rounded">
                        <i class="bx bx-x"></i>
                      </div>
                    </div>
                    <div class="ms-3">
                      <div class="small mb-1">{{ __('Absent') }}</div>
                      <h5 class="mb-0">{{ $attendanceStats['absent'] ?? 0 }}</h5>
                    </div>
                  </div>
                </div>
                <div class="col-6 col-md-3">
                  <div class="d-flex align-items-center">
                    <div class="avatar">
                      <div class="avatar-initial bg-label-warning rounded">
                        <i class="bx bx-time-five"></i>
                      </div>
                    </div>
                    <div class="ms-3">
                      <div class="small mb-1">{{ __('Late') }}</div>
                      <h5 class="mb-0">{{ $attendanceStats['late'] ?? 0 }}</h5>
                    </div>
                  </div>
                </div>
                <div class="col-6 col-md-3">
                  <div class="d-flex align-items-center">
                    <div class="avatar">
                      <div class="avatar-initial bg-label-info rounded">
                        <i class="bx bx-calendar"></i>
                      </div>
                    </div>
                    <div class="ms-3">
                      <div class="small mb-1">{{ __('Leaves') }}</div>
                      <h5 class="mb-0">{{ $attendanceStats['leave'] ?? 0 }}</h5>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- Personal Info Tab --}}
        <div class="tab-pane fade" id="navs-pills-personal" role="tabpanel">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title mb-4">{{ __('Personal Information') }}</h5>
              <form method="POST" action="{{ route('hrcore.my.profile.update') }}">
                @csrf
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label" for="personal_email">{{ __('Personal Email') }}</label>
                    <input type="email" id="personal_email" name="personal_email" class="form-control" 
                      value="{{ $user->personal_email }}" placeholder="personal@example.com">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="phone">{{ __('Phone') }}</label>
                    <input type="text" id="phone" name="phone" class="form-control" 
                      value="{{ $user->phone }}" placeholder="+1234567890">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="mobile">{{ __('Mobile') }}</label>
                    <input type="text" id="mobile" name="mobile" class="form-control" 
                      value="{{ $user->mobile }}" placeholder="+1234567890">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="date_of_birth">{{ __('Date of Birth') }}</label>
                    <input type="text" id="date_of_birth" name="date_of_birth" class="form-control flatpickr-date" 
                      value="{{ $user->date_of_birth }}" placeholder="YYYY-MM-DD">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="blood_group">{{ __('Blood Group') }}</label>
                    <select id="blood_group" name="blood_group" class="form-select">
                      <option value="">{{ __('Select Blood Group') }}</option>
                      @foreach(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $group)
                        <option value="{{ $group }}" {{ $user->blood_group == $group ? 'selected' : '' }}>
                          {{ $group }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="marital_status">{{ __('Marital Status') }}</label>
                    <select id="marital_status" name="marital_status" class="form-select">
                      <option value="">{{ __('Select Status') }}</option>
                      @foreach(['single' => 'Single', 'married' => 'Married', 'divorced' => 'Divorced', 'widowed' => 'Widowed'] as $value => $label)
                        <option value="{{ $value }}" {{ $user->marital_status == $value ? 'selected' : '' }}>
                          {{ $label }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-12">
                    <label class="form-label" for="address">{{ __('Address') }}</label>
                    <textarea id="address" name="address" class="form-control" rows="2" 
                      placeholder="Enter your address">{{ $user->address }}</textarea>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="city">{{ __('City') }}</label>
                    <input type="text" id="city" name="city" class="form-control" 
                      value="{{ $user->city }}" placeholder="City">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="state">{{ __('State') }}</label>
                    <input type="text" id="state" name="state" class="form-control" 
                      value="{{ $user->state }}" placeholder="State">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="country">{{ __('Country') }}</label>
                    <input type="text" id="country" name="country" class="form-control" 
                      value="{{ $user->country }}" placeholder="Country">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="postal_code">{{ __('Postal Code') }}</label>
                    <input type="text" id="postal_code" name="postal_code" class="form-control" 
                      value="{{ $user->postal_code }}" placeholder="12345">
                  </div>
                </div>
                <div class="mt-4">
                  <button type="submit" class="btn btn-primary me-2">{{ __('Save Changes') }}</button>
                  <button type="reset" class="btn btn-label-secondary">{{ __('Cancel') }}</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        {{-- Emergency Contact Tab --}}
        <div class="tab-pane fade" id="navs-pills-emergency" role="tabpanel">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title mb-4">{{ __('Emergency Contact Information') }}</h5>
              <form method="POST" action="{{ route('hrcore.my.profile.update') }}">
                @csrf
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label" for="emergency_contact_name">{{ __('Contact Name') }}</label>
                    <input type="text" id="emergency_contact_name" name="emergency_contact_name" class="form-control" 
                      value="{{ $user->emergency_contact_name }}" placeholder="John Doe">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="emergency_contact_relation">{{ __('Relationship') }}</label>
                    <input type="text" id="emergency_contact_relation" name="emergency_contact_relation" class="form-control" 
                      value="{{ $user->emergency_contact_relation }}" placeholder="Father/Mother/Spouse">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="emergency_contact_phone">{{ __('Contact Phone') }}</label>
                    <input type="text" id="emergency_contact_phone" name="emergency_contact_phone" class="form-control" 
                      value="{{ $user->emergency_contact_phone }}" placeholder="+1234567890">
                  </div>
                </div>
                <div class="mt-4">
                  <button type="submit" class="btn btn-primary me-2">{{ __('Update Emergency Contact') }}</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        {{-- Security Tab --}}
        <div class="tab-pane fade" id="navs-pills-security" role="tabpanel">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title mb-4">{{ __('Change Password') }}</h5>
              <form method="POST" action="{{ route('hrcore.my.profile.password') }}">
                @csrf
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label" for="current_password">{{ __('Current Password') }}</label>
                    <input type="password" id="current_password" name="current_password" class="form-control" required>
                  </div>
                  <div class="col-md-6"></div>
                  <div class="col-md-6">
                    <label class="form-label" for="new_password">{{ __('New Password') }}</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" required>
                    <small class="text-muted">{{ __('Minimum 8 characters') }}</small>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="new_password_confirmation">{{ __('Confirm New Password') }}</label>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control" required>
                  </div>
                </div>
                <div class="mt-4">
                  <button type="submit" class="btn btn-primary me-2">{{ __('Change Password') }}</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Upload Photo Modal --}}
  <div class="modal fade" id="uploadPhotoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ __('Upload Profile Photo') }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="uploadPhotoForm" action="{{ route('hrcore.my.profile.photo') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-body">
            <div class="mb-3">
              <label for="photo" class="form-label">{{ __('Choose Photo') }}</label>
              <input class="form-control" type="file" id="photo" name="photo" accept="image/*" required>
              <small class="text-muted">{{ __('Allowed formats: JPG, PNG. Max size: 2MB') }}</small>
            </div>
            <div id="photoPreview" class="text-center" style="display: none;">
              <img id="previewImage" src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
            <button type="submit" class="btn btn-primary">{{ __('Upload Photo') }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection