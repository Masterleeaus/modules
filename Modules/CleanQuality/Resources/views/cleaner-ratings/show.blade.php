@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fa fa-chart-line mr-1 text-info"></i>
                    @lang('quality_control::app.cleaner_trend')
                    @if($cleaner)— {{ $cleaner->name }}@endif
                </h4>
                <a href="{{ route('cleaner-ratings.index') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left mr-1"></i>@lang('app.back')
                </a>
            </div>
        </div>

        @if($records->isNotEmpty())
            {{-- Trend chart data --}}
            @php
                $labels = $records->map(fn($r) => optional($r->inspected_at)->format('d M Y') ?? '')->values()->toJson();
                $scores = $records->pluck('overall_score')->values()->toJson();
            @endphp

            <div class="card mb-4">
                <div class="card-body">
                    <canvas id="trendChart" height="80"></canvas>
                </div>
            </div>

            <div class="card">
                <div class="card-header py-2">@lang('quality_control::app.qc_history')</div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>@lang('quality_control::app.booking')</th>
                                <th>@lang('quality_control::app.template')</th>
                                <th>@lang('quality_control::app.score')</th>
                                <th>@lang('app.status')</th>
                                <th>@lang('quality_control::app.inspected_at')</th>
                                <th>@lang('app.action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $record)
                                <tr>
                                    <td>{{ $record->id }}</td>
                                    <td>{{ $record->booking_id ?? '—' }}</td>
                                    <td>{{ $record->template?->name ?? '—' }}</td>
                                    <td>
                                        <span class="badge {{ $record->overall_score >= 70 ? 'badge-success' : 'badge-danger' }}">{{ $record->overall_score }}%</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ in_array($record->status, ['pass','reclean_done']) ? 'success' : (in_array($record->status, ['fail','reclean_required']) ? 'danger' : 'secondary') }}">
                                            {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                                        </span>
                                    </td>
                                    <td>{{ $record->inspected_at ? $record->inspected_at->format('d M Y') : '—' }}</td>
                                    <td>
                                        <a href="{{ route('qc-records.show', $record->id) }}" class="btn btn-xs btn-outline-info">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body text-center text-muted py-5">@lang('app.noRecordFound')</div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
@if($records->isNotEmpty())
<script src="https://cdn.jsdelivr.net/npm/chart.js@3/dist/chart.min.js"></script>
<script>
const scoreData = {!! $scores !!};
new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: {!! $labels !!},
        datasets: [{
            label: '{{ __('quality_control::app.score') }} %',
            data: scoreData,
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78,115,223,0.1)',
            tension: 0.3,
            fill: true,
            pointBackgroundColor: scoreData.map(s => s >= 70 ? '#1cc88a' : '#e74a3b'),
        }]
    },
    options: {
        scales: { y: { min: 0, max: 100, ticks: { callback: v => v + '%' } } },
        plugins: {
            annotation: {
                annotations: { threshold: {
                    type: 'line',
                    yMin: 70, yMax: 70,
                    borderColor: 'orange',
                    borderDash: [6, 4],
                    label: { content: '70% threshold', enabled: true }
                }}
            }
        }
    }
});
</script>
@endif
@endpush
