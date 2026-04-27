@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fa fa-clipboard-check mr-1"></i>@lang('quality_control::app.qc_record') #{{ $record->id }}</h4>
                <div>
                    <a href="{{ route('qc-records.index') }}" class="btn btn-outline-secondary mr-2">
                        <i class="fa fa-arrow-left mr-1"></i>@lang('app.back')
                    </a>
                    @if($canTriggerReclean)
                        <form method="POST" action="{{ route('qc-records.trigger_reclean', $record->id) }}" class="d-inline">
                            @csrf
                            <button class="btn btn-warning" onclick="return confirm('@lang('quality_control::app.trigger_reclean_confirm')')">
                                <i class="fa fa-redo mr-1"></i>@lang('quality_control::app.trigger_reclean')
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        @foreach(['success','warning','error'] as $msg)
            @if(session($msg))
                <div class="alert alert-{{ $msg === 'error' ? 'danger' : $msg }} alert-dismissible fade show">
                    {{ session($msg) }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif
        @endforeach

        <div class="row">
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header py-2"><i class="fa fa-info-circle mr-1"></i>@lang('quality_control::app.record_details')</div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <th class="text-muted w-40">@lang('quality_control::app.status')</th>
                                <td>
                                    <span class="badge badge-{{ in_array($record->status, ['pass','reclean_done']) ? 'success' : (in_array($record->status, ['fail','reclean_required']) ? 'danger' : 'secondary') }}">
                                        {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">@lang('quality_control::app.score')</th>
                                <td>
                                    <strong class="{{ $record->overall_score >= 70 ? 'text-success' : 'text-danger' }}">{{ $record->overall_score }}%</strong>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">@lang('quality_control::app.booking')</th>
                                <td>{{ $record->booking_id ?? '—' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">@lang('quality_control::app.cleaner')</th>
                                <td>{{ $record->cleaner?->name ?? ($record->cleaner_id ? '#' . $record->cleaner_id : '—') }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">@lang('quality_control::app.template')</th>
                                <td>{{ $record->template?->name ?? '—' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">@lang('quality_control::app.inspected_by')</th>
                                <td>{{ $record->inspector?->name ?? '—' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">@lang('quality_control::app.inspected_at')</th>
                                <td>{{ $record->inspected_at ? $record->inspected_at->format('d M Y H:i') : '—' }}</td>
                            </tr>
                            @if($record->reclean_triggered)
                                <tr>
                                    <th class="text-muted">@lang('quality_control::app.reclean_triggered')</th>
                                    <td>
                                        <span class="badge badge-warning">{{ $record->reclean_triggered_at?->format('d M Y H:i') ?? __('app.yes') }}</span>
                                    </td>
                                </tr>
                            @endif
                            @if($record->notes)
                                <tr>
                                    <th class="text-muted">@lang('app.notes')</th>
                                    <td>{{ $record->notes }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header py-2"><i class="fa fa-list mr-1"></i>@lang('quality_control::app.checklist_items') ({{ $record->items->count() }})</div>
                    <div class="card-body p-0">
                        @if($record->items->isNotEmpty())
                            <table class="table table-sm mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>@lang('quality_control::app.item_label')</th>
                                        <th>@lang('quality_control::app.score')</th>
                                        <th>@lang('quality_control::app.weight')</th>
                                        <th>@lang('app.notes')</th>
                                        <th>@lang('quality_control::app.photo')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($record->items as $item)
                                        <tr>
                                            <td>{{ $item->item_label }}</td>
                                            <td>
                                                <span class="badge {{ $item->score >= 70 ? 'badge-success' : 'badge-danger' }}">{{ $item->score }}%</span>
                                            </td>
                                            <td>{{ $item->weight > 0 ? $item->weight . '%' : '—' }}</td>
                                            <td>{{ $item->notes ?? '—' }}</td>
                                            <td>
                                                @if($item->photo)
                                                    <a href="{{ asset($item->photo) }}" target="_blank">
                                                        <img src="{{ asset($item->photo) }}" style="max-height:40px;" class="rounded">
                                                    </a>
                                                @else
                                                    —
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center text-muted py-3">@lang('app.noRecordFound')</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
