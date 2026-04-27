@extends('layouts.app')
@section('title', 'ProShots — Job Batches')
@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center action-bar mb-3">
        <h4 class="f-21 font-weight-normal mb-0">Job Batches</h4>
        <button class="btn btn-primary btn-small" data-toggle="modal" data-target="#newBatchModal">
            <i class="fa fa-plus mr-1"></i> New Batch
        </button>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th><th>Job Ref</th><th>Title</th><th>Status</th><th>Progress</th><th>Photos</th><th>{{ __('app.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batches as $batch)
                    <tr>
                        <td>{{ $batch->id }}</td>
                        <td><code>{{ $batch->job_ref }}</code></td>
                        <td>{{ $batch->title }}</td>
                        <td>
                            <span class="badge badge-{{ $batch->status === 'completed' ? 'success' : ($batch->status === 'processing' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($batch->status) }}
                            </span>
                        </td>
                        <td style="min-width:120px;">
                            <div class="progress" style="height:8px;">
                                <div class="progress-bar bg-success" style="width:{{ $batch->progress_percent }}%"></div>
                            </div>
                            <small class="text-muted">{{ $batch->completed_photos }}/{{ $batch->total_photos }}</small>
                        </td>
                        <td>{{ $batch->total_photos }}</td>
                        <td>
                            <a href="{{ route('proshots.batches.show', $batch->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fa fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No batches yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="newBatchModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('proshots.batches.store') }}">
                @csrf
                <div class="modal-header"><h5 class="modal-title">New Job Batch</h5></div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Job Reference <span class="text-danger">*</span></label>
                        <input type="text" name="job_ref" class="form-control" required placeholder="e.g. JOB-2026-001">
                    </div>
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" placeholder="Optional title">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Batch</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
