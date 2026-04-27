@extends('layouts.app')

@section('filter-section')
<div class="d-flex justify-content-between align-items-center">
    <h4 class="mb-0">{{ __('InstantAds — Realtime Generator') }}</h4>
    <a href="{{ route('instant-ads.index') }}" class="btn btn-sm btn-outline-secondary">
        {{ __('← Back to Generator') }}
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-5 mb-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="card-title fw-bold">{{ __('⚡ Realtime Image Generation') }}</h6>
                <p class="text-muted small">{{ __('Images are generated synchronously in seconds using Flux Schnell.') }}</p>

                <form id="realtimeForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">{{ __('Prompt') }}</label>
                        <textarea name="prompt" id="rtPrompt" class="form-control" rows="3"
                            placeholder="{{ __('Describe the ad image you want...') }}"
                            required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Style (optional)') }}</label>
                        <select name="style" class="form-select">
                            <option value="">{{ __('No style') }}</option>
                            <option value="photorealistic">Photorealistic</option>
                            <option value="illustration">Illustration</option>
                            <option value="minimalist">Minimalist</option>
                            <option value="corporate">Corporate</option>
                            <option value="vibrant">Vibrant</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-warning w-100 fw-bold" id="rtBtn">
                        <span class="spinner-border spinner-border-sm d-none me-1" id="rtSpinner"></span>
                        {{ __('Generate Now') }}
                    </button>
                </form>

                <div id="rtStatus" class="mt-3 d-none">
                    <div class="alert mb-0" id="rtStatusMsg"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <h6 class="fw-bold">{{ __('Generated Images') }}</h6>
        <div id="rtResult" class="mb-4 d-none">
            <div class="row g-2" id="rtImageGrid"></div>
        </div>
        <div id="rtHistory">
            <h6 class="text-muted small">{{ __('Recent Realtime Creatives') }}</h6>
            <div class="row g-2" id="rtHistoryGrid">
                <div class="col-12 text-center text-muted py-4">
                    <small>{{ __('Your recent realtime images will appear here.') }}</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load history
    fetch('{{ route("instant-ads.realtime.images") }}', {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        const grid = document.getElementById('rtHistoryGrid');
        if (data.images && data.images.length > 0) {
            grid.innerHTML = '';
            data.images.forEach(img => {
                (img.generated_images || [img.url]).forEach(url => {
                    if (!url) return;
                    const col = document.createElement('div');
                    col.className = 'col-md-3 col-6';
                    col.innerHTML = `<div class="card overflow-hidden">
                        <img src="${url}" class="card-img-top" style="height:150px;object-fit:cover;" loading="lazy">
                        <div class="card-body p-2">
                            <p class="small text-muted mb-0">${(img.prompt || '').substring(0, 35)}</p>
                            <a href="${url}" download class="btn btn-xs btn-outline-secondary mt-1">↓ Download</a>
                        </div>
                    </div>`;
                    grid.appendChild(col);
                });
            });
        }
    });

    // Realtime generate form
    document.getElementById('realtimeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn       = document.getElementById('rtBtn');
        const spinner   = document.getElementById('rtSpinner');
        const statusDiv = document.getElementById('rtStatus');
        const statusMsg = document.getElementById('rtStatusMsg');
        const resultDiv = document.getElementById('rtResult');
        const imageGrid = document.getElementById('rtImageGrid');

        btn.disabled = true;
        spinner.classList.remove('d-none');
        statusDiv.classList.add('d-none');

        fetch('{{ route("instant-ads.realtime.generate") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: new FormData(this),
        })
        .then(r => r.json())
        .then(data => {
            statusDiv.classList.remove('d-none');
            if (data.success && data.images && data.images.length > 0) {
                statusMsg.className = 'alert alert-success mb-0';
                statusMsg.textContent = '{{ __("Image generated!") }}';
                imageGrid.innerHTML = '';
                data.images.forEach(url => {
                    const col = document.createElement('div');
                    col.className = 'col-md-4 col-6';
                    col.innerHTML = `<div class="card overflow-hidden">
                        <img src="${url}" class="card-img-top" style="height:200px;object-fit:cover;">
                        <div class="card-body p-2">
                            <a href="${url}" download class="btn btn-sm btn-outline-secondary w-100">↓ Download</a>
                        </div>
                    </div>`;
                    imageGrid.appendChild(col);
                });
                resultDiv.classList.remove('d-none');
            } else {
                statusMsg.className = 'alert alert-danger mb-0';
                statusMsg.textContent = data.message || '{{ __("Generation failed.") }}';
            }
        })
        .catch(() => {
            statusDiv.classList.remove('d-none');
            statusMsg.className = 'alert alert-danger mb-0';
            statusMsg.textContent = '{{ __("An error occurred. Please try again.") }}';
        })
        .finally(() => {
            btn.disabled = false;
            spinner.classList.add('d-none');
        });
    });
});
</script>
@endpush
