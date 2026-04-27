<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pack->title }} — Proof Pack Review</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body { background: #f5f6fa; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
        .pack-header { background: #fff; border-bottom: 1px solid #e0e0e0; padding: 1.25rem 2rem; }
        .pack-body { max-width: 900px; margin: 2rem auto; padding: 0 1rem; }
        .pack-card { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,.08); padding: 1.5rem; margin-bottom: 1.5rem; }
        .doc-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; }
        .doc-item { border: 1px solid #e0e0e0; border-radius: 6px; padding: 1rem; text-align: center; background: #fafafa; }
        .doc-item i { font-size: 2.5rem; color: #6c757d; margin-bottom: .5rem; }
        .doc-item .doc-title { font-size: .9rem; font-weight: 600; word-break: break-word; }
        .doc-item .doc-type { font-size: .78rem; color: #888; }
        .status-badge { font-size: .85rem; padding: .35em .75em; border-radius: 20px; }
        .btn-approve { background: #28a745; border-color: #28a745; color: #fff; }
        .btn-approve:hover { background: #218838; }
    </style>
</head>
<body>

<div class="pack-header d-flex align-items-center justify-content-between">
    <div>
        <h5 class="mb-0 font-weight-bold">{{ $pack->title }}</h5>
        <small class="text-muted">
            Proof Pack for Job Ref: <strong>{{ $pack->job_ref }}</strong>
            &bull; {{ $pack->documents->count() }} document(s)
        </small>
    </div>
    <span class="status-badge badge badge-{{ ['draft'=>'secondary','sent'=>'info','approved'=>'success','rejected'=>'danger'][$pack->status] ?? 'secondary' }}">
        {{ ucfirst($pack->status) }}
    </span>
</div>

<div class="pack-body">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    {{-- Document grid --}}
    <div class="pack-card">
        <h6 class="font-weight-bold mb-3">
            <i class="fa fa-images mr-1 text-primary"></i> Evidence Documents
        </h6>

        @if($pack->documents->isEmpty())
            <p class="text-muted">No documents are attached to this proof pack.</p>
        @else
            <div class="doc-grid">
                @foreach($pack->documents as $doc)
                <div class="doc-item">
                    @php
                        $isImage = str_starts_with($doc->mime_type ?? '', 'image/');
                    @endphp

                    @if($isImage && $doc->file_path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($doc->file_path) }}"
                             alt="{{ $doc->title }}"
                             class="img-fluid mb-2 rounded"
                             style="max-height: 140px; object-fit: cover;">
                    @else
                        <i class="fa fa-file-alt d-block mb-2"></i>
                    @endif

                    <div class="doc-title">{{ $doc->title }}</div>

                    @if($doc->document_type)
                        <div class="doc-type mt-1">
                            <span class="badge badge-light">{{ ucfirst(str_replace('_', ' ', $doc->document_type)) }}</span>
                        </div>
                    @endif

                    @if($doc->file_path)
                        <a href="{{ \Illuminate\Support\Facades\Storage::url($doc->file_path) }}"
                           target="_blank"
                           class="btn btn-outline-secondary btn-sm mt-2">
                            <i class="fa fa-download mr-1"></i> View
                        </a>
                    @endif
                </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Approval form --}}
    @if($pack->status === 'sent')
    <div class="pack-card">
        <h6 class="font-weight-bold mb-3">
            <i class="fa fa-check-circle mr-1 text-success"></i> Approve This Proof Pack
        </h6>
        <p class="text-muted f-14 mb-3">
            By submitting your approval below you confirm that the documents presented
            accurately represent the work completed for job <strong>{{ $pack->job_ref }}</strong>.
        </p>
        <form method="POST" action="{{ route('titan-vault.client.approve.submit', $token) }}">
            @csrf
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Your Name <span class="text-danger">*</span></label>
                    <input type="text" name="client_name" class="form-control"
                           value="{{ old('client_name', $pack->client_name) }}" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Your Email <span class="text-danger">*</span></label>
                    <input type="email" name="client_email" class="form-control"
                           value="{{ old('client_email', $pack->client_email) }}" required>
                </div>
            </div>
            <button type="submit" class="btn btn-approve px-5">
                <i class="fa fa-check mr-1"></i> Approve Proof Pack
            </button>
        </form>
    </div>
    @elseif($pack->status === 'approved')
    <div class="alert alert-success text-center py-4">
        <i class="fa fa-check-circle fa-2x d-block mb-2"></i>
        <strong>This proof pack has been approved.</strong>
        @if($pack->approved_at)
            <div class="text-muted mt-1 small">Approved on {{ $pack->approved_at->format('d M Y \a\t H:i') }}</div>
        @endif
    </div>
    @elseif($pack->status === 'rejected')
    <div class="alert alert-danger text-center py-4">
        <i class="fa fa-times-circle fa-2x d-block mb-2"></i>
        <strong>This proof pack has been declined.</strong>
    </div>
    @elseif($pack->status === 'draft')
    <div class="alert alert-secondary text-center py-4">
        <i class="fa fa-clock fa-2x d-block mb-2"></i>
        <strong>This proof pack is not yet ready for review.</strong>
    </div>
    @endif

</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
