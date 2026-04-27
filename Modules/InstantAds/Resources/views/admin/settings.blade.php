@extends('layouts.app')

@section('filter-section')
<h4 class="mb-0">{{ __('InstantAds — Admin Settings') }}</h4>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="card-title fw-bold">{{ __('Module Configuration') }}</h6>

                <form id="settingsForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">{{ __('Guest Daily Generation Limit') }}</label>
                        <input type="number" name="guest_daily_limit" class="form-control"
                            value="{{ $config['guest_daily_limit'] ?? 3 }}" min="0" max="100">
                        <div class="form-text">{{ __('0 = disabled for guests') }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Max Batch Size') }}</label>
                        <input type="number" name="max_batch_size" class="form-control"
                            value="{{ $config['max_batch_size'] ?? 4 }}" min="1" max="8">
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('Save Settings') }}</button>
                </form>

                <div id="settingsStatus" class="mt-3 d-none">
                    <div class="alert mb-0" id="settingsMsg"></div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mt-4">
            <div class="card-body">
                <h6 class="card-title fw-bold">{{ __('Quick Links') }}</h6>
                <div class="list-group list-group-flush">
                    <a href="{{ route('instant-ads.admin.community') }}" class="list-group-item list-group-item-action">
                        {{ __('Community Images') }}
                    </a>
                    <a href="{{ route('instant-ads.admin.publish-requests') }}" class="list-group-item list-group-item-action">
                        {{ __('Pending Publish Requests') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('settingsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const statusDiv = document.getElementById('settingsStatus');
    const statusMsg = document.getElementById('settingsMsg');

    fetch('{{ route("instant-ads.admin.settings.update") }}', {
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
        statusMsg.className = data.success ? 'alert alert-success mb-0' : 'alert alert-danger mb-0';
        statusMsg.textContent = data.message || '';
    });
});
</script>
@endpush
