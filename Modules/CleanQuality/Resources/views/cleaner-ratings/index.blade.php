@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fa fa-star mr-1 text-warning"></i>@lang('quality_control::sidebar.cleaner_ratings')</h4>
                <a href="{{ route('quality-control.index') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left mr-1"></i>@lang('quality_control::app.dashboard')
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>@lang('app.employee')</th>
                                <th>@lang('quality_control::app.avg_score')</th>
                                <th>@lang('quality_control::app.total_records')</th>
                                <th>@lang('quality_control::app.pass_count')</th>
                                <th>@lang('quality_control::app.fail_count')</th>
                                <th>@lang('quality_control::app.last_inspected')</th>
                                <th>@lang('app.action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ratings as $row)
                                <tr>
                                    <td>
                                        @if(isset($row->cleaner) && $row->cleaner)
                                            <div class="d-flex align-items-center">
                                                @if($row->cleaner->image)
                                                    <img src="{{ asset_url('avatar/' . $row->cleaner->image) }}" class="rounded-circle mr-2" style="width:32px;height:32px;object-fit:cover;">
                                                @endif
                                                <span>{{ $row->cleaner->name }}</span>
                                            </div>
                                        @else
                                            #{{ $row->cleaner_id }}
                                        @endif
                                    </td>
                                    <td>
                                        @php $avg = (float) $row->avg_score; @endphp
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 mr-2" style="height:8px;max-width:80px;">
                                                <div class="progress-bar {{ $avg >= 70 ? 'bg-success' : 'bg-danger' }}"
                                                     style="width:{{ min($avg,100) }}%"></div>
                                            </div>
                                            <strong class="{{ $avg >= 70 ? 'text-success' : 'text-danger' }}">{{ $avg }}%</strong>
                                        </div>
                                    </td>
                                    <td>{{ $row->total_records }}</td>
                                    <td><span class="badge badge-success">{{ $row->pass_count }}</span></td>
                                    <td><span class="badge badge-danger">{{ $row->fail_count }}</span></td>
                                    <td>{{ $row->last_inspected_at ? \Carbon\Carbon::parse($row->last_inspected_at)->format('d M Y') : '—' }}</td>
                                    <td>
                                        <a href="{{ route('cleaner-ratings.show', $row->cleaner_id) }}" class="btn btn-xs btn-outline-info">
                                            <i class="fa fa-chart-line mr-1"></i>@lang('quality_control::app.trend')
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">@lang('app.noRecordFound')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
