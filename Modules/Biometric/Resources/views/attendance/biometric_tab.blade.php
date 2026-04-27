{{--
    Biometric Data Tab
    Pushed into @stack('biometric-data-tab') on the core attendance show view.
    Shows GPS coordinates, method badge and geofence pass/fail for each clock-in.
    $attendanceActivity is available from the parent AttendanceController::show context.
--}}
@if(in_array('biometric', user_modules()))
@push('biometric-data-tab')
    <div class="row mt-3">
        <div class="col-md-12">
            <x-cards.data :title="__('biometric::app.biometricData')">

                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>@lang('modules.attendance.clock_in')</th>
                                <th>@lang('biometric::app.method')</th>
                                <th>@lang('biometric::app.geofence')</th>
                                <th>@lang('biometric::app.gpsIn')</th>
                                <th>@lang('modules.attendance.clock_out')</th>
                                <th>@lang('biometric::app.methodOut')</th>
                                <th>@lang('biometric::app.gpsOut')</th>
                                <th>@lang('biometric::app.device')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $biometricRows = isset($attendanceActivity)
                                    ? $attendanceActivity->filter(fn($r) => isset($r->clock_in_method) && $r->clock_in_method !== 'manual')
                                    : collect();
                            @endphp
                            @forelse($biometricRows as $row)
                                <tr>
                                    <td>{{ $row->clock_in_time?->translatedFormat(company()->time_format) }}</td>
                                    <td>
                                        <span class="badge badge-info">
                                            <i class="fa fa-fingerprint mr-1"></i>
                                            {{ ucfirst($row->clock_in_method) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($row->geofence_passed)
                                            <span class="badge badge-success">
                                                <i class="fa fa-check mr-1"></i>@lang('biometric::app.passed')
                                            </span>
                                        @else
                                            <span class="badge badge-danger">
                                                <i class="fa fa-times mr-1"></i>@lang('biometric::app.failed')
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($row->clock_in_lat && $row->clock_in_lng)
                                            <a href="https://maps.google.com/?q={{ $row->clock_in_lat }},{{ $row->clock_in_lng }}"
                                               target="_blank" rel="noopener noreferrer" class="f-12">
                                                {{ number_format($row->clock_in_lat, 5) }},
                                                {{ number_format($row->clock_in_lng, 5) }}
                                            </a>
                                        @else
                                            <span class="text-muted f-12">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $row->clock_out_time?->translatedFormat(company()->time_format) ?? '—' }}</td>
                                    <td>
                                        @if(isset($row->clock_out_method) && $row->clock_out_method !== 'manual')
                                            <span class="badge badge-secondary">
                                                {{ ucfirst($row->clock_out_method) }}
                                            </span>
                                        @else
                                            <span class="text-muted f-12">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($row->clock_out_lat && $row->clock_out_lng)
                                            <a href="https://maps.google.com/?q={{ $row->clock_out_lat }},{{ $row->clock_out_lng }}"
                                               target="_blank" rel="noopener noreferrer" class="f-12">
                                                {{ number_format($row->clock_out_lat, 5) }},
                                                {{ number_format($row->clock_out_lng, 5) }}
                                            </a>
                                        @else
                                            <span class="text-muted f-12">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="f-12 text-muted">
                                            {{ $row->biometric_device_id ?? '—' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">
                                        @lang('biometric::app.noBiometricData')
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </x-cards.data>
        </div>
    </div>
@endpush
@endif
