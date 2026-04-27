@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fa fa-redo mr-1 text-warning"></i>@lang('quality_control::sidebar.reclean_management')</h4>
                <a href="{{ route('quality-control.index') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left mr-1"></i>@lang('quality_control::app.dashboard')
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        <div class="card">
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
                                <th>@lang('quality_control::app.triggered_at')</th>
                                <th>@lang('app.action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(is_a($records, \Illuminate\Pagination\AbstractPaginator::class) ? $records->items() : $records as $record)
                                <tr>
                                    <td>{{ $record->id }}</td>
                                    <td>{{ $record->booking_id ?? '—' }}</td>
                                    <td>{{ $record->cleaner?->name ?? ($record->cleaner_id ? '#' . $record->cleaner_id : '—') }}</td>
                                    <td>{{ $record->template?->name ?? '—' }}</td>
                                    <td><span class="badge badge-danger">{{ $record->overall_score }}%</span></td>
                                    <td>
                                        <span class="badge badge-{{ $record->status === 'reclean_done' ? 'success' : 'warning' }}">
                                            {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                                        </span>
                                    </td>
                                    <td>{{ $record->reclean_triggered_at ? $record->reclean_triggered_at->format('d M Y H:i') : '—' }}</td>
                                    <td>
                                        @if($record->status === 'reclean_required')
                                            <form method="POST" action="{{ route('reclean.done', $record->id) }}" class="d-inline">
                                                @csrf
                                                <button class="btn btn-xs btn-success">
                                                    <i class="fa fa-check mr-1"></i>@lang('quality_control::app.mark_done')
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge badge-success"><i class="fa fa-check-circle mr-1"></i>@lang('quality_control::app.done')</span>
                                        @endif
                                        <a href="{{ route('qc-records.show', $record->id) }}" class="btn btn-xs btn-outline-info ml-1">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">@lang('app.noRecordFound')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if(is_a($records, \Illuminate\Pagination\AbstractPaginator::class) && $records->hasPages())
            <div class="mt-3">{{ $records->links() }}</div>
        @endif
    </div>
@endsection
