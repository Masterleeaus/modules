@extends('layouts.app')

@section('filter-section')
<div class="d-flex justify-content-between align-items-center">
    <h4 class="mb-0">{{ __('InstantAds — AI Ad Creative Generator') }}</h4>
    <a href="{{ route('instant-ads.realtime') }}" class="btn btn-sm btn-outline-primary">
        {{ __('⚡ Realtime Mode') }}
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Generator Panel -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="card-title fw-bold">{{ __('Generate Ad Creative') }}</h6>
                <form id="generateForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">{{ __('Describe your ad creative') }}</label>
                        <textarea name="prompt" id="prompt" class="form-control" rows="4"
                            placeholder="{{ __('e.g. modern Australian home renovation, bright natural light, trust and quality') }}"
                            required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('AI Model') }}</label>
                        <select name="model" id="modelSelect" class="form-select">
                            @foreach($activeImageModels as $key => $model)
                                <option value="{{ $model['slug'] ?? $key }}">{{ $model['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Variants (Batch)') }}</label>
                        <select name="image_count" class="form-select">
                            <option value="1">1 variant</option>
                            <option value="2">2 variants</option>
                            <option value="3">3 variants</option>
                            <option value="4" selected>4 variants (A/B test)</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" id="generateBtn">
                        <span class="spinner-border spinner-border-sm d-none me-1" id="genSpinner"></span>
                        {{ __('Generate Ad Creative') }}
                    </button>
                </form>
                <div id="generateStatus" class="mt-3 d-none">
                    <div class="alert alert-info mb-0" id="statusMessage"></div>
                </div>
            </div>
        </div>

        <!-- Common Ad Prompts -->
        <div class="card shadow-sm mt-3">
            <div class="card-body">
                <h6 class="card-title">{{ __('Quick Prompt Ideas') }}</h6>
                <div class="d-flex flex-wrap gap-2">
                    @foreach([
                        'Facebook ad background, modern lifestyle, warm tones',
                        'Instagram product shot, clean white background',
                        'YouTube thumbnail base, bold colors, tech theme',
                        'Landing page hero, professional service, trust-building',
                        'Google Display ad background, minimalist design'
                    ] as $quickPrompt)
                    <button class="btn btn-sm btn-outline-secondary quick-prompt"
                        data-prompt="{{ $quickPrompt }}">
                        {{ Str::limit($quickPrompt, 30) }}
                    </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Gallery Panel -->
    <div class="col-lg-8">
        <!-- In-Progress -->
        @if(!empty($imageStats['in_progress_images']))
        <div class="mb-4" id="inProgressSection">
            <h6 class="fw-bold text-warning">{{ __('⏳ Generating...') }}</h6>
            <div class="row g-2" id="inProgressGrid">
                @foreach($imageStats['in_progress_images'] as $img)
                <div class="col-md-3 col-6">
                    <div class="card h-100 border-warning bg-light text-center p-3">
                        <div class="spinner-border text-warning mx-auto" role="status"></div>
                        <small class="text-muted mt-2">{{ Str::limit($img['prompt'] ?? '', 30) }}</small>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Completed Images -->
        <div id="gallerySection">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold">{{ __('Your Ad Creatives') }}</h6>
                <span class="text-muted small">{{ $imageStats['completed_count'] }} {{ __('generated') }}</span>
            </div>
            <div class="row g-2" id="imageGrid">
                @foreach($imageStats['completed_images'] as $record)
                    @if(!empty($record['generated_images']))
                        @foreach($record['generated_images'] as $idx => $imgUrl)
                        <div class="col-md-3 col-6">
                            <div class="card h-100 overflow-hidden image-card position-relative"
                                data-id="{{ $record['id'] }}-{{ $idx }}"
                                data-url="{{ $imgUrl }}">
                                <img src="{{ $imgUrl }}" class="card-img-top"
                                    style="height:160px;object-fit:cover;"
                                    alt="{{ $record['prompt'] ?? 'Ad Creative' }}"
                                    loading="lazy">
                                <div class="card-body p-2">
                                    <p class="card-text small text-muted mb-1">{{ Str::limit($record['prompt'] ?? '', 40) }}</p>
                                    <div class="d-flex gap-1">
                                        <a href="{{ $imgUrl }}" download class="btn btn-xs btn-outline-secondary" title="{{ __('Download') }}">↓</a>
                                        <button class="btn btn-xs btn-outline-success btn-publish"
                                            data-id="{{ $record['id'] }}"
                                            title="{{ __('Publish to Gallery') }}">↑</button>
                                        <button class="btn btn-xs btn-outline-primary btn-share"
                                            data-id="{{ $record['id'] }}"
                                            title="{{ __('Share') }}">⬡</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                @endforeach
            </div>
            @if($imageStats['completed_count'] > 20)
            <div class="text-center mt-3">
                <button class="btn btn-outline-secondary" id="loadMoreBtn">{{ __('Load More') }}</button>
            </div>
            @endif
        </div>

        <!-- Community Gallery -->
        <div class="mt-4">
            <h6 class="fw-bold">{{ __('Community Gallery') }}</h6>
            <div class="row g-2" id="communityGrid">
                <!-- Loaded via AJAX -->
            </div>
            <div class="text-center mt-3 d-none" id="loadMoreCommunity">
                <button class="btn btn-outline-secondary" id="loadMoreCommunityBtn">{{ __('Load More') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Share Modal -->
<div class="modal fade" id="shareModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Share Ad Creative') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="input-group">
                    <input type="text" class="form-control" id="shareUrl" readonly>
                    <button class="btn btn-outline-secondary" onclick="navigator.clipboard.writeText(document.getElementById('shareUrl').value)">
                        {{ __('Copy') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quick prompt buttons
    document.querySelectorAll('.quick-prompt').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('prompt').value = this.dataset.prompt;
        });
    });

    // Generate form submission
    document.getElementById('generateForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn       = document.getElementById('generateBtn');
        const spinner   = document.getElementById('genSpinner');
        const statusDiv = document.getElementById('generateStatus');
        const statusMsg = document.getElementById('statusMessage');

        btn.disabled = true;
        spinner.classList.remove('d-none');

        fetch('{{ route("instant-ads.generate") }}', {
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
            if (data.success) {
                statusMsg.className = 'alert alert-success mb-0';
                statusMsg.textContent = data.message || '{{ __("Generation started!") }}';
                setTimeout(() => location.reload(), 3000);
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

    // Publish button
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-publish');
        if (!btn) return;

        fetch('{{ route("instant-ads.community.images.publish") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ image_id: btn.dataset.id }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                btn.classList.toggle('btn-outline-success', !data.published);
                btn.classList.toggle('btn-success', data.published);
            }
        });
    });

    // Share button
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-share');
        if (!btn) return;

        fetch('{{ route("instant-ads.share.generate") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ image_id: btn.dataset.id }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('shareUrl').value = data.share_url;
                new bootstrap.Modal(document.getElementById('shareModal')).show();
            }
        });
    });

    // Load community gallery
    let communityPage = 1;

    function loadCommunity(page) {
        fetch('{{ route("instant-ads.community.images") }}?page=' + page, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            const grid = document.getElementById('communityGrid');
            (data.images || []).forEach(img => {
                const col = document.createElement('div');
                col.className = 'col-md-3 col-6';
                col.innerHTML = `<div class="card h-100 overflow-hidden">
                    <img src="${img.url}" class="card-img-top" style="height:160px;object-fit:cover;" loading="lazy">
                    <div class="card-body p-2">
                        <p class="small text-muted mb-0">${img.prompt ? img.prompt.substring(0, 40) : ''}</p>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <button class="btn btn-xs btn-outline-danger btn-like" data-id="${img.id}">
                                ♥ <span class="like-count">${img.likes_count || 0}</span>
                            </button>
                        </div>
                    </div>
                </div>`;
                grid.appendChild(col);
            });
            if (data.hasMore) {
                document.getElementById('loadMoreCommunity').classList.remove('d-none');
            }
        });
    }

    loadCommunity(communityPage);

    document.getElementById('loadMoreCommunityBtn')?.addEventListener('click', function() {
        communityPage++;
        loadCommunity(communityPage);
        document.getElementById('loadMoreCommunity').classList.add('d-none');
    });

    // Like buttons in community (delegated)
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-like');
        if (!btn) return;

        fetch('{{ route("instant-ads.community.images.like") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ image_id: btn.dataset.id }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const countEl = btn.querySelector('.like-count');
                if (countEl) countEl.textContent = data.likes_count;
                btn.classList.toggle('btn-danger', data.liked);
                btn.classList.toggle('btn-outline-danger', !data.liked);
            }
        });
    });
});
</script>
@endpush
