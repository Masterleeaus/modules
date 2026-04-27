<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $document->title }} — Proof Review</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body { background: #f5f6fa; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
        .proof-header { background: #fff; border-bottom: 1px solid #e0e0e0; padding: 1.25rem 2rem; }
        .proof-body { max-width: 860px; margin: 2rem auto; padding: 0 1rem; }
        .proof-card { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,.08); padding: 2rem; margin-bottom: 1.5rem; }
        .status-badge { font-size: .85rem; padding: .35em .75em; border-radius: 20px; }
        .content-box { background: #fafafa; border: 1px solid #e0e0e0; border-radius: 6px; padding: 1.5rem; white-space: pre-wrap; font-size: .95rem; }
        .action-card { border-radius: 8px; overflow: hidden; }
        .btn-approve { background: #28a745; border-color: #28a745; color: #fff; }
        .btn-approve:hover { background: #218838; }
        .btn-revision { background: #fd7e14; border-color: #fd7e14; color: #fff; }
        .btn-revision:hover { background: #e36209; }
    </style>
</head>
<body>

<div class="proof-header d-flex align-items-center justify-content-between">
    <div>
        <h5 class="mb-0 font-weight-bold">{{ $document->title }}</h5>
        <small class="text-muted">Proof Review &bull; Version {{ $document->version }}</small>
    </div>
    <span class="status-badge badge badge-{{ ['draft'=>'secondary','in_review'=>'warning','approved'=>'success','rejected'=>'danger','archived'=>'dark'][$document->status] ?? 'secondary' }}">
        {{ ucfirst(str_replace('_', ' ', $document->status)) }}
    </span>
</div>

<div class="proof-body">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Document content --}}
    <div class="proof-card">
        @if($document->description)
            <p class="text-muted f-14 mb-3">{{ $document->description }}</p>
        @endif

        @if($document->content)
            <div class="content-box">{{ $document->content }}</div>
        @elseif($document->file_path)
            <div class="text-center py-4">
                <i class="fa fa-file fa-3x text-muted mb-3 d-block"></i>
                <a href="{{ Storage::url($document->file_path) }}" target="_blank"
                   class="btn btn-outline-primary">
                    <i class="fa fa-download mr-1"></i> Download Document
                </a>
            </div>
        @else
            <p class="text-muted text-center py-4 mb-0">No content available for preview.</p>
        @endif
    </div>

    @if(in_array($document->status, ['in_review', 'draft']))
        {{-- Approve --}}
        <div class="proof-card action-card">
            <h6 class="font-weight-bold mb-3">Approve this proof</h6>
            <form method="POST" action="{{ route('titan-vault.proof.approve', $token) }}">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Your Name <span class="text-danger">*</span></label>
                        <input type="text" name="approver_name" class="form-control"
                               value="{{ old('approver_name') }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Your Email <span class="text-danger">*</span></label>
                        <input type="email" name="approver_email" class="form-control"
                               value="{{ old('approver_email') }}" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-approve px-4">
                    <i class="fa fa-check mr-1"></i> Approve Proof
                </button>
            </form>
        </div>

        {{-- Request Revision --}}
        <div class="proof-card action-card">
            <h6 class="font-weight-bold mb-3">Request Revision</h6>
            <form method="POST" action="{{ route('titan-vault.proof.revision', $token) }}">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Your Name <span class="text-danger">*</span></label>
                        <input type="text" name="approver_name" class="form-control"
                               value="{{ old('approver_name') }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Your Email <span class="text-danger">*</span></label>
                        <input type="email" name="approver_email" class="form-control"
                               value="{{ old('approver_email') }}" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Revision Notes <span class="text-danger">*</span></label>
                    <textarea name="revision_notes" rows="4" class="form-control"
                              placeholder="Please describe the changes needed…" required>{{ old('revision_notes') }}</textarea>
                </div>
                <button type="submit" class="btn btn-revision px-4">
                    <i class="fa fa-redo mr-1"></i> Request Revision
                </button>
            </form>
        </div>
    @elseif($document->status === 'approved')
        <div class="alert alert-success text-center py-4">
            <i class="fa fa-check-circle fa-2x d-block mb-2"></i>
            <strong>This proof has been approved.</strong>
        </div>
    @elseif($document->status === 'rejected')
        <div class="alert alert-warning text-center py-4">
            <i class="fa fa-exclamation-circle fa-2x d-block mb-2"></i>
            <strong>Revision requested. The team will be in touch soon.</strong>
        </div>
    @endif

</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
