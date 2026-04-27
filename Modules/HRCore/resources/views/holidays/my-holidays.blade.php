@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', __('My Holidays'))

@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/fullcalendar/fullcalendar.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
  ])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/moment/moment.js',
    'resources/assets/vendor/libs/fullcalendar/fullcalendar.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
  ])
@endsection

@section('page-script')
  @vite(['resources/assets/js/app/hrcore-holidays-calendar.js'])
@endsection

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    {{-- Breadcrumb --}}
    <x-breadcrumb
      :title="__('My Holidays')"
      :breadcrumbs="[
        ['name' => __('Holidays'), 'url' => ''],
        ['name' => __('My Holidays'), 'url' => '']
      ]"
    />

    {{-- Holiday Statistics --}}
    <div class="row mb-4">
      <div class="col-lg-4 col-sm-6 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div class="card-info">
                <p class="card-text text-muted">{{ __('Total Holidays') }}</p>
                <div class="d-flex align-items-end mb-2">
                  <h4 class="card-title mb-0 me-2">{{ $totalHolidays }}</h4>
                  <small class="text-muted">{{ __('in') }} {{ $currentYear }}</small>
                </div>
              </div>
              <div class="card-icon">
                <span class="badge bg-label-primary rounded p-2">
                  <i class="bx bx-calendar-event bx-sm"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-sm-6 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div class="card-info">
                <p class="card-text text-muted">{{ __('Past Holidays') }}</p>
                <div class="d-flex align-items-end mb-2">
                  <h4 class="card-title mb-0 me-2">{{ $pastHolidays }}</h4>
                  <small class="text-muted">{{ __('enjoyed') }}</small>
                </div>
              </div>
              <div class="card-icon">
                <span class="badge bg-label-secondary rounded p-2">
                  <i class="bx bx-calendar-check bx-sm"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-sm-6 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div class="card-info">
                <p class="card-text text-muted">{{ __('Upcoming Holidays') }}</p>
                <div class="d-flex align-items-end mb-2">
                  <h4 class="card-title mb-0 me-2">{{ $futureHolidays }}</h4>
                  <small class="text-muted">{{ __('remaining') }}</small>
                </div>
              </div>
              <div class="card-icon">
                <span class="badge bg-label-success rounded p-2">
                  <i class="bx bx-calendar-star bx-sm"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      {{-- Holiday Calendar --}}
      <div class="col-lg-8">
        <div class="card">
          <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Holiday Calendar') }} {{ $currentYear }}</h5>
          </div>
          <div class="card-body">
            <div id="holidayCalendar"></div>
            {{-- Fallback if calendar doesn't load --}}
            <div id="calendarFallback" style="display: none;">
              <div class="alert alert-info">
                <i class="bx bx-info-circle me-2"></i>
                Calendar view is loading. If it doesn't appear, please check your browser console.
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Upcoming Holidays List --}}
      <div class="col-lg-4">
        <div class="card">
          <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Upcoming Holidays') }}</h5>
          </div>
          <div class="card-body">
            @if($upcomingHolidays->count() > 0)
              <div class="list-group list-group-flush">
                @foreach($upcomingHolidays as $holiday)
                  @php
                    $daysUntil = ceil(now()->diffInDays($holiday->date, false));
                    $isToday = $holiday->date->isToday();
                    $isTomorrow = $holiday->date->isTomorrow();
                  @endphp
                  <div class="list-group-item px-0">
                    <div class="d-flex justify-content-between align-items-start">
                      <div class="me-2">
                        <h6 class="mb-1">{{ $holiday->name }}</h6>
                        <div class="d-flex align-items-center text-muted small">
                          <i class="bx bx-calendar me-1"></i>
                          <span>{{ $holiday->date->format('M d, Y') }}</span>
                          <span class="mx-2">•</span>
                          <span>{{ $holiday->date->format('l') }}</span>
                        </div>
                        @if($holiday->notes)
                          <small class="text-muted">{{ $holiday->notes }}</small>
                        @endif
                      </div>
                      <div class="text-end">
                        @if($isToday)
                          <span class="badge bg-label-success">{{ __('Today') }}</span>
                        @elseif($isTomorrow)
                          <span class="badge bg-label-info">{{ __('Tomorrow') }}</span>
                        @else
                          <span class="badge bg-label-primary">
                            {{ $daysUntil }} {{ $daysUntil == 1 ? __('day') : __('days') }}
                          </span>
                        @endif
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            @else
              <p class="text-muted text-center mb-0">{{ __('No upcoming holidays') }}</p>
            @endif
          </div>
        </div>

        {{-- Holidays by Month --}}
        <div class="card mt-4">
          <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Holidays by Month') }}</h5>
          </div>
          <div class="card-body">
            <div class="accordion accordion-flush" id="holidaysByMonthAccordion">
              @foreach($holidaysByMonth as $month => $monthHolidays)
                <div class="accordion-item">
                  <h2 class="accordion-header" id="heading{{ $month }}">
                    <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" 
                      data-bs-toggle="collapse" data-bs-target="#collapse{{ $month }}" 
                      aria-expanded="{{ $loop->first ? 'true' : 'false' }}">
                      {{ $month }}
                      <span class="badge bg-label-primary ms-auto">{{ $monthHolidays->count() }}</span>
                    </button>
                  </h2>
                  <div id="collapse{{ $month }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" 
                    data-bs-parent="#holidaysByMonthAccordion">
                    <div class="accordion-body pt-0">
                      @foreach($monthHolidays as $holiday)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                          <div>
                            <span class="fw-medium">{{ $holiday->name }}</span>
                            <small class="text-muted d-block">{{ $holiday->date->format('D, M d') }}</small>
                          </div>
                          @if($holiday->date->isPast())
                            <i class="bx bx-check-circle text-success"></i>
                          @else
                            <small class="text-muted">{{ $holiday->date->diffForHumans() }}</small>
                          @endif
                        </div>
                      @endforeach
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- All Holidays Table --}}
    <div class="card mt-4">
      <div class="card-header">
        <h5 class="card-title mb-0">{{ __('All Holidays') }} - {{ $currentYear }}</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>{{ __('Holiday') }}</th>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Day') }}</th>
                <th>{{ __('Notes') }}</th>
                <th>{{ __('Status') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($holidays as $holiday)
                <tr>
                  <td>
                    <span class="fw-medium">{{ $holiday->name }}</span>
                  </td>
                  <td>{{ $holiday->date->format('M d, Y') }}</td>
                  <td>{{ $holiday->date->format('l') }}</td>
                  <td>
                    @if($holiday->notes)
                      <small>{{ $holiday->notes }}</small>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td>
                    @if($holiday->date->isPast())
                      <span class="badge bg-label-secondary">{{ __('Past') }}</span>
                    @elseif($holiday->date->isToday())
                      <span class="badge bg-label-success">{{ __('Today') }}</span>
                    @else
                      <span class="badge bg-label-info">{{ __('Upcoming') }}</span>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  {{-- Holiday Data for Calendar --}}
  @php
    $holidayData = $holidays->map(function($holiday) {
      return [
        'id' => $holiday->id,
        'title' => $holiday->name,
        'start' => $holiday->date->format('Y-m-d'),
        'allDay' => true,
        'className' => $holiday->date->isPast() ? 'bg-secondary' : ($holiday->date->isToday() ? 'bg-success' : 'bg-primary'),
        'extendedProps' => [
          'notes' => $holiday->notes,
          'code' => $holiday->code
        ]
      ];
    })->toArray();
  @endphp
  <script>
    window.holidayData = @json($holidayData);
  </script>
@endsection