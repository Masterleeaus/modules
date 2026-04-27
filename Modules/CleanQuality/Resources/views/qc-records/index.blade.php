@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fa fa-clipboard-check mr-1"></i>@lang('quality_control::sidebar.qc_records')</h4>
                <a href="{{ route('qc-records.create') }}" class="btn btn-primary">
                    <i class="fa fa-plus mr-1"></i>@lang('quality_control::app.new_qc_record')
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
                                <th>@lang('quality_control::app.reclean')</th>
                                <th>@lang('app.date')</th>
                                <th>@lang('app.action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $record)
                                <tr>
                                    <td>{{ $record->id }}</td>
                                    <td>{{ $record->booking_id ?? '—' }}</td>
                                    <td>{{ $record->cleaner?->name ?? ($record->cleaner_id ? '#' . $record->cleaner_id : '—') }}</td>
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
                                    <td>
                                        @if($record->reclean_triggered)
                                            <span class="badge badge-warning"><i class="fa fa-redo mr-1"></i>@lang('quality_control::app.triggered')</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $record->inspected_at ? $record->inspected_at->format('d M Y') : '—' }}</td>
                                    <td>
                                        <a href="{{ route('qc-records.show', $record->id) }}" class="btn btn-xs btn-outline-info mr-1">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <form method="POST" action="{{ route('qc-records.destroy', $record->id) }}" class="d-inline"
                                              onsubmit="return confirm('@lang('messages.deleteWarning')')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-xs btn-outline-danger"><i class="fa fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
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

        <div class="mt-3">{{ $records->links() }}</div>
    </div>
@endsection
