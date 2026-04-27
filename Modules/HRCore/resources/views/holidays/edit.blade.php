@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', __('Edit Holiday'))

<!-- Vendor Styles -->
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/@form-validation/form-validation.scss',
    'resources/assets/vendor/libs/animate-css/animate.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
    'resources/assets/vendor/libs/select2/select2.scss'
  ])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/@form-validation/popular.js',
    'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
    'resources/assets/vendor/libs/@form-validation/auto-focus.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
    'resources/assets/vendor/libs/select2/select2.js'
  ])
@endsection

@section('page-script')
    @vite(['Modules/HRCore/resources/assets/js/app/hrcore-holidays-form.js'])
@endsection

@section('content')
<x-breadcrumb :title="__('Edit Holiday')" :breadcrumbs="[
    ['name' => __('Home'), 'url' => route('dashboard')],
    ['name' => __('HR Core'), 'url' => '#'],
    ['name' => __('Holidays'), 'url' => route('hrcore.holidays.index')],
    ['name' => __('Edit'), 'url' => route('hrcore.holidays.edit', $holiday->id)]
]" />

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('Edit Holiday') }}</h5>
            </div>
            <div class="card-body">
                <form id="holidayForm" action="{{ route('hrcore.holidays.update', $holiday->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Basic Information -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label" for="name">{{ __('Holiday Name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $holiday->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="code">{{ __('Holiday Code') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $holiday->code) }}" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="date">{{ __('Date') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control flatpickr-date @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', $holiday->date->format('Y-m-d')) }}" required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Type and Category -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label" for="type">{{ __('Type') }} <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">{{ __('Select Type') }}</option>
                                <option value="public" {{ old('type', $holiday->type) == 'public' ? 'selected' : '' }}>{{ __('Public') }}</option>
                                <option value="religious" {{ old('type', $holiday->type) == 'religious' ? 'selected' : '' }}>{{ __('Religious') }}</option>
                                <option value="regional" {{ old('type', $holiday->type) == 'regional' ? 'selected' : '' }}>{{ __('Regional') }}</option>
                                <option value="optional" {{ old('type', $holiday->type) == 'optional' ? 'selected' : '' }}>{{ __('Optional') }}</option>
                                <option value="company" {{ old('type', $holiday->type) == 'company' ? 'selected' : '' }}>{{ __('Company') }}</option>
                                <option value="special" {{ old('type', $holiday->type) == 'special' ? 'selected' : '' }}>{{ __('Special') }}</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="category">{{ __('Category') }}</label>
                            <select class="form-select @error('category') is-invalid @enderror" id="category" name="category">
                                <option value="">{{ __('Select Category') }}</option>
                                <option value="national" {{ old('category', $holiday->category) == 'national' ? 'selected' : '' }}>{{ __('National') }}</option>
                                <option value="state" {{ old('category', $holiday->category) == 'state' ? 'selected' : '' }}>{{ __('State') }}</option>
                                <option value="cultural" {{ old('category', $holiday->category) == 'cultural' ? 'selected' : '' }}>{{ __('Cultural') }}</option>
                                <option value="festival" {{ old('category', $holiday->category) == 'festival' ? 'selected' : '' }}>{{ __('Festival') }}</option>
                                <option value="company_event" {{ old('category', $holiday->category) == 'company_event' ? 'selected' : '' }}>{{ __('Company Event') }}</option>
                                <option value="other" {{ old('category', $holiday->category) == 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="color">{{ __('Calendar Color') }}</label>
                            <input type="color" class="form-control @error('color') is-invalid @enderror" id="color" name="color" value="{{ old('color', $holiday->color ?? '#4CAF50') }}" style="height: 38px;">
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Holiday Options -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">{{ __('Holiday Options') }}</label>
                            <div class="d-flex flex-wrap gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_optional" name="is_optional" value="1" {{ old('is_optional', $holiday->is_optional) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_optional">{{ __('Optional Holiday') }}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_restricted" name="is_restricted" value="1" {{ old('is_restricted', $holiday->is_restricted) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_restricted">{{ __('Restricted Holiday') }}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_recurring" name="is_recurring" value="1" {{ old('is_recurring', $holiday->is_recurring) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_recurring">{{ __('Recurring Every Year') }}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_half_day" name="is_half_day" value="1" {{ old('is_half_day', $holiday->is_half_day) ? 'checked' : '' }} onchange="toggleHalfDayFields()">
                                    <label class="form-check-label" for="is_half_day">{{ __('Half Day') }}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_compensatory" name="is_compensatory" value="1" {{ old('is_compensatory', $holiday->is_compensatory) ? 'checked' : '' }} onchange="toggleCompensatoryFields()">
                                    <label class="form-check-label" for="is_compensatory">{{ __('Compensatory Holiday') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Half Day Fields -->
                    <div class="row mb-3" id="halfDayFields" style="display: {{ old('is_half_day', $holiday->is_half_day) ? 'flex' : 'none' }};">
                        <div class="col-md-4">
                            <label class="form-label" for="half_day_type">{{ __('Half Day Type') }}</label>
                            <select class="form-select" id="half_day_type" name="half_day_type">
                                <option value="morning" {{ old('half_day_type', $holiday->half_day_type) == 'morning' ? 'selected' : '' }}>{{ __('Morning') }}</option>
                                <option value="afternoon" {{ old('half_day_type', $holiday->half_day_type) == 'afternoon' ? 'selected' : '' }}>{{ __('Afternoon') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="half_day_start_time">{{ __('Start Time') }}</label>
                            <input type="text" class="form-control flatpickr-time" id="half_day_start_time" name="half_day_start_time" value="{{ old('half_day_start_time', $holiday->half_day_start_time) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="half_day_end_time">{{ __('End Time') }}</label>
                            <input type="text" class="form-control flatpickr-time" id="half_day_end_time" name="half_day_end_time" value="{{ old('half_day_end_time', $holiday->half_day_end_time) }}">
                        </div>
                    </div>

                    <!-- Compensatory Date -->
                    <div class="row mb-3" id="compensatoryFields" style="display: {{ old('is_compensatory', $holiday->is_compensatory) ? 'flex' : 'none' }};">
                        <div class="col-md-6">
                            <label class="form-label" for="compensatory_date">{{ __('Compensatory Working Date') }}</label>
                            <input type="text" class="form-control flatpickr-date" id="compensatory_date" name="compensatory_date" value="{{ old('compensatory_date', $holiday->compensatory_date?->format('Y-m-d')) }}">
                        </div>
                    </div>

                    <!-- Applicability -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label" for="applicable_for">{{ __('Applicable For') }} <span class="text-danger">*</span></label>
                            <select class="form-select @error('applicable_for') is-invalid @enderror" id="applicable_for" name="applicable_for" required onchange="toggleApplicabilityFields()">
                                <option value="all" {{ old('applicable_for', $holiday->applicable_for) == 'all' ? 'selected' : '' }}>{{ __('All Employees') }}</option>
                                <option value="department" {{ old('applicable_for', $holiday->applicable_for) == 'department' ? 'selected' : '' }}>{{ __('Specific Departments') }}</option>
                                <option value="location" {{ old('applicable_for', $holiday->applicable_for) == 'location' ? 'selected' : '' }}>{{ __('Specific Locations') }}</option>
                                <option value="employee_type" {{ old('applicable_for', $holiday->applicable_for) == 'employee_type' ? 'selected' : '' }}>{{ __('Employee Types') }}</option>
                                <option value="custom" {{ old('applicable_for', $holiday->applicable_for) == 'custom' ? 'selected' : '' }}>{{ __('Specific Employees') }}</option>
                            </select>
                            @error('applicable_for')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Department Selection -->
                    <div class="row mb-3" id="departmentFields" style="display: {{ old('applicable_for', $holiday->applicable_for) == 'department' ? 'flex' : 'none' }};">
                        <div class="col-12">
                            <label class="form-label" for="departments">{{ __('Select Departments') }}</label>
                            <select class="form-select select2" id="departments" name="departments[]" multiple>
                                @foreach(\Modules\HRCore\app\Models\Department::where('status', \App\Enums\Status::ACTIVE)->get() as $dept)
                                    <option value="{{ $dept->id }}" {{ in_array($dept->id, old('departments', $holiday->departments ?? [])) ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Location Selection -->
                    <div class="row mb-3" id="locationFields" style="display: {{ old('applicable_for', $holiday->applicable_for) == 'location' ? 'flex' : 'none' }};">
                        <div class="col-12">
                            <label class="form-label" for="locations">{{ __('Enter Locations (comma separated)') }}</label>
                            <input type="text" class="form-control" id="locations" name="locations" placeholder="New York, Los Angeles, Chicago" value="{{ old('locations', is_array($holiday->locations) ? implode(', ', $holiday->locations) : $holiday->locations) }}">
                        </div>
                    </div>

                    <!-- Description and Notes -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="description">{{ __('Description') }}</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $holiday->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="notes">{{ __('Notes') }}</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" maxlength="500">{{ old('notes', $holiday->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Visibility and Notification -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_visible_to_employees" name="is_visible_to_employees" value="1" {{ old('is_visible_to_employees', $holiday->is_visible_to_employees) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_visible_to_employees">
                                    {{ __('Visible to Employees') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="send_notification" name="send_notification" value="1" {{ old('send_notification', $holiday->send_notification) ? 'checked' : '' }} onchange="toggleNotificationFields()">
                                <label class="form-check-label" for="send_notification">
                                    {{ __('Send Notification') }}
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Days -->
                    <div class="row mb-3" id="notificationFields" style="display: {{ old('send_notification', $holiday->send_notification) ? 'flex' : 'none' }};">
                        <div class="col-md-6">
                            <label class="form-label" for="notification_days_before">{{ __('Notify Days Before') }}</label>
                            <input type="number" class="form-control" id="notification_days_before" name="notification_days_before" value="{{ old('notification_days_before', $holiday->notification_days_before ?? 7) }}" min="0" max="30">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">{{ __('Update Holiday') }}</button>
                            <a href="{{ route('hrcore.holidays.index') }}" class="btn btn-label-secondary">{{ __('Cancel') }}</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection