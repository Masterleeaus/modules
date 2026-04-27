@extends('layouts.app')
@section('title', 'Batch: ' . $batch->title)
@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center action-bar mb-3">
        <div>
            <a href="{{ route('proshots.batches.index') }}" class="btn btn-sm btn-outline-secondary mr-2">
                <i class="fa fa-arrow-left"></i>
            </a>
            <span class="f-18 font-weight-normal">{{ $batch->title }}</span>
            <code class="ml-2 f-13">{{ $batch->job_ref }}</code>
        </div>
        <div>
            <span class="badge badge-{{ $batch->status === 'completed' ? 'success' : ($batch->status === 'processing' ? 'warning' : 'secondary') }} mr-2">
                {{ ucfirst($batch->status) }}
            </span>
            @if($batch->isCompleted() && ! $batch->vault_proof_pack_id)
                <form method="POST" action="{{ route('proshots.batches.publish', $batch->id) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-small">
                        <i class="fa fa-cloud-upload mr-1"></i> Publish to Vault
                    </button>
                </form>
            @elseif($batch->vault_proof_pack_id)
                <span class="badge badge-success"><i class="fa fa-check mr-1"></i> Published to Vault</span>
            @endif
        </div>
    </div>

    {{-- Progress --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <div class="d-flex justify-content-between mb-1">
                <small class="text-muted">Processing progress</small>
                <small class="text-muted">{{ $batch->completed_photos }}/{{ $batch->total_photos }} photos ({{ $batch->progress_percent }}%)</small>
            </div>
            <div class="progress" style="height:10px;">
                <div class="progress-bar bg-success" style="width:{{ $batch->progress_percent }}%"></div>
            </div>
        </div>
    </div>

    {{-- Before / After photo grid --}}
    @if($photos->isEmpty())
        <div class="card"><div class="card-body text-center py-5 text-muted">No photos in this batch yet.</div></div>
    @else
        <div class="row">
            @foreach($photos as $photo)
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center py-2">
                        <small class="font-weight-bold">{{ ucfirst($photo->room_type ?? 'Photo') }}</small>
                        @if($photo->photo_stage)
                            <span class="badge badge-{{ $photo->photo_stage === 'after' ? 'success' : 'secondary' }}">
                                {{ ucfirst($photo->photo_stage) }}
                            </span>
                        @endif
                    </div>
                    <div class="card-body p-2 text-center">
                        <img src="{{ $photo->image }}"
                             alt="{{ $photo->room_type ?? 'photo' }}"
                             class="img-fluid rounded"
                             style="max-height:200px; object-fit:cover; width:100%;">
                    </div>
                    <div class="card-footer py-2 d-flex justify-content-between align-items-center">
                        <small class="text-muted">{{ $photo->created_at->format('d M Y') }}</small>
                        <a href="{{ $photo->image }}" download class="btn btn-sm btn-outline-secondary">
                            <i class="fa fa-download"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
