@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Connect {{ $config['label'] }}</h5>
                </div>
                <div class="card-body">
                    @if($integration?->status === 'connected')
                        <div class="alert alert-success">
                            <i class="fa fa-check-circle"></i>
                            Connected
                            @if($integration->settings['account_name'] ?? null)
                                as <strong>{{ $integration->settings['account_name'] }}</strong>
                            @endif
                        </div>
                    @endif

                    <form id="connect-form">
                        @csrf

                        @if($config['auth'] === 'api_key')
                            <div class="form-group">
                                <label>API Key <span class="text-danger">*</span></label>
                                <input type="password" name="api_key" class="form-control"
                                       placeholder="Enter your {{ $config['label'] }} API key"
                                       autocomplete="off" required>
                                <small class="form-text text-muted">
                                    Your key is encrypted at rest. We never expose it.
                                </small>
                            </div>

                            @if($provider === 'mailchimp')
                                <div class="form-group">
                                    <label>Audience / List ID</label>
                                    <input type="text" name="list_id" class="form-control"
                                           value="{{ $integration?->settings['list_id'] ?? '' }}"
                                           placeholder="e.g. abc123def">
                                    <small class="form-text text-muted">
                                        Leave blank to select after connecting.
                                    </small>
                                </div>
                            @endif

                            @if($provider === 'wordpress')
                                <div class="form-group">
                                    <label>WordPress Site URL <span class="text-danger">*</span></label>
                                    <input type="url" name="site_url" class="form-control"
                                           value="{{ $integration?->settings['site_url'] ?? '' }}"
                                           placeholder="https://yoursite.com">
                                </div>
                                <div class="form-group">
                                    <label>Username <span class="text-danger">*</span></label>
                                    <input type="text" name="username" class="form-control"
                                           value="{{ $integration?->settings['username'] ?? '' }}">
                                </div>
                                <div class="form-group">
                                    <label>Application Password <span class="text-danger">*</span></label>
                                    <input type="password" name="api_key" class="form-control"
                                           placeholder="WordPress Application Password">
                                </div>
                            @endif

                        @elseif($config['auth'] === 'webhook')
                            <div class="form-group">
                                <label>Webhook URL <span class="text-danger">*</span></label>
                                <input type="url" name="webhook_url" class="form-control"
                                       value="{{ $integration?->webhook_url ? '(saved)' : '' }}"
                                       placeholder="https://hooks.slack.com/services/..." required>
                                @if(in_array($provider, ['zapier', 'make']))
                                    <small class="form-text text-muted">
                                        In {{ $config['label'] }}, create a new trigger with "Webhooks" and paste the URL here.
                                    </small>
                                @endif
                            </div>
                        @endif

                        <div class="d-flex justify-content-between mt-3">
                            <a href="{{ route('titan-integrations.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary" id="save-btn">
                                <i class="fa fa-plug"></i> Connect
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('connect-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('save-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Testing connection...';

    const data = Object.fromEntries(new FormData(this));

    fetch('{{ route('titan-integrations.connect', $provider) }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: JSON.stringify(data),
    })
    .then(r => r.json())
    .then(result => {
        if (result.ok) {
            window.location = '{{ route('titan-integrations.index') }}';
        } else {
            alert('Connection failed: ' + (result.error || 'Unknown error'));
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-plug"></i> Connect';
        }
    });
});
</script>
@endsection
