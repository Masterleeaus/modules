@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">
                        <i class="fa fa-check-circle mr-1 text-success"></i>
                        @lang('quality_control::sidebar.qc_dashboard')
                    </h4>
                    <div class="d-flex">
                        @if($canViewRatings)
                            <a href="{{ route('cleaner-ratings.index') }}" class="btn btn-outline-secondary mr-2">
                                <i class="fa fa-star mr-1"></i>@lang('quality_control::sidebar.cleaner_ratings')
                            </a>
                        @endif
                        <a href="{{ route('qc-records.create') }}" class="btn btn-primary mr-2">
                            <i class="fa fa-plus mr-1"></i>@lang('quality_control::app.new_qc_record')
                        </a>
                        <a href="{{ route('reclean.index') }}" class="btn btn-warning">
                            <i class="fa fa-redo mr-1"></i>@lang('quality_control::sidebar.reclean_management')
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Summary Stats --}}
        @if(!empty($stats))
            <div class="row mb-4">
                <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                    <div class="card border-left-primary h-100">
                        <div class="card-body py-3">
                            <p class="text-xs font-weight-bold text-primary text-uppercase mb-1">@lang('quality_control::app.total_records')</p>
                            <h5 class="mb-0 font-weight-bold text-gray-800">{{ $stats['total'] ?? 0 }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                    <div class="card border-left-success h-100">
                        <div class="card-body py-3">
                            <p class="text-xs font-weight-bold text-success text-uppercase mb-1">@lang('quality_control::app.passed')</p>
                            <h5 class="mb-0 font-weight-bold text-gray-800">{{ $stats['pass'] ?? 0 }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                    <div class="card border-left-danger h-100">
                        <div class="card-body py-3">
                            <p class="text-xs font-weight-bold text-danger text-uppercase mb-1">@lang('quality_control::app.failed')</p>
                            <h5 class="mb-0 font-weight-bold text-gray-800">{{ $stats['fail'] ?? 0 }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                    <div class="card border-left-warning h-100">
                        <div class="card-body py-3">
                            <p class="text-xs font-weight-bold text-warning text-uppercase mb-1">@lang('quality_control::app.recleans')</p>
                            <h5 class="mb-0 font-weight-bold text-gray-800">{{ $stats['reclean'] ?? 0 }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                    <div class="card border-left-info h-100">
                        <div class="card-body py-3">
                            <p class="text-xs font-weight-bold text-info text-uppercase mb-1">@lang('quality_control::app.avg_score')</p>
                            <h5 class="mb-0 font-weight-bold text-gray-800">{{ $stats['avg_score'] ?? 0 }}%</h5>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                    <div class="card border-left-secondary h-100">
                        <div class="card-body py-3">
                            <p class="text-xs font-weight-bold text-secondary text-uppercase mb-1">@lang('quality_control::app.fail_rate')</p>
                            <h5 class="mb-0 font-weight-bold text-gray-800">{{ $stats['fail_rate'] ?? 0 }}%</h5>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Performers --}}
            @if($canViewRatings && (!empty($stats['top_performers']) || !empty($stats['bottom_performers'])))
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header bg-success text-white py-2">
                                <i class="fa fa-trophy mr-1"></i>@lang('quality_control::app.top_performers')
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-sm mb-0">
                                    <thead><tr><th>@lang('app.employee')</th><th>@lang('quality_control::app.avg_score')</th><th>@lang('quality_control::app.total_records')</th></tr></thead>
                                    <tbody>
                                        @forelse($stats['top_performers'] as $row)
                                            <tr>
                                                <td>
                                                    @if($row->cleaner_id)
                                                        <a href="{{ route('cleaner-ratings.show', $row->cleaner_id) }}">#{{ $row->cleaner_id }}</a>
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td><span class="badge badge-success">{{ $row->avg_score }}%</span></td>
                                                <td>{{ $row->total }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3" class="text-center text-muted">@lang('app.noRecordFound')</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header bg-danger text-white py-2">
                                <i class="fa fa-exclamation-triangle mr-1"></i>@lang('quality_control::app.bottom_performers')
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-sm mb-0">
                                    <thead><tr><th>@lang('app.employee')</th><th>@lang('quality_control::app.avg_score')</th><th>@lang('quality_control::app.total_records')</th></tr></thead>
                                    <tbody>
                                        @forelse($stats['bottom_performers'] as $row)
                                            <tr>
                                                <td>
                                                    @if($row->cleaner_id)
                                                        <a href="{{ route('cleaner-ratings.show', $row->cleaner_id) }}">#{{ $row->cleaner_id }}</a>
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td><span class="badge badge-danger">{{ $row->avg_score }}%</span></td>
                                                <td>{{ $row->total }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3" class="text-center text-muted">@lang('app.noRecordFound')</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Recent Records --}}
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header py-2 d-flex justify-content-between align-items-center">
                            <span><i class="fa fa-list mr-1"></i>@lang('quality_control::app.recent_records')</span>
                            <a href="{{ route('qc-records.index') }}" class="btn btn-sm btn-outline-primary">@lang('app.viewAll')</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th>@lang('quality_control::app.booking')</th>
                                            <th>@lang('quality_control::app.cleaner')</th>
                                            <th>@lang('quality_control::app.template')</th>
                                            <th>@lang('quality_control::app.score')</th>
                                            <th>@lang('app.status')</th>
                                            <th>@lang('app.date')</th>
                                            <th>@lang('app.action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($stats['recent'] ?? [] as $record)
                                            <tr>
                                                <td>{{ $record->id }}</td>
                                                <td>{{ $record->booking_id ?? '—' }}</td>
                                                <td>{{ $record->cleaner?->name ?? '#' . $record->cleaner_id }}</td>
                                                <td>{{ $record->template?->name ?? '—' }}</td>
                                                <td>
                                                    @php $score = $record->overall_score; @endphp
                                                    <span class="badge {{ $score >= 70 ? 'badge-success' : 'badge-danger' }}">{{ $score }}%</span>
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
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4">
                                                    @lang('app.noRecordFound')
                                                    <br>
                                                    <a href="{{ route('qc-records.create') }}" class="btn btn-sm btn-primary mt-2">
                                                        @lang('quality_control::app.new_qc_record')
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fa fa-check-circle fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">@lang('quality_control::app.no_data_yet')</h5>
                            <a href="{{ route('qc-records.create') }}" class="btn btn-primary mt-2">
                                @lang('quality_control::app.new_qc_record')
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
