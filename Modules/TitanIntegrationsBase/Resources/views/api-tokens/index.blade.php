@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <h4 class="page-title">API Tokens</h4>
                <div class="page-title-right">
                    <a href="{{ route('titan-integrations.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fa fa-arrow-left"></i> Back to Integrations
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted">
                        API tokens allow external systems (mobile apps, booking platforms, custom integrations)
                        to access WorkSuite data. Tokens are scoped and rate-limited.
                    </p>
                    <p class="text-muted small">
                        <strong>Authentication:</strong> Include the token as a Bearer header:<br>
                        <code>Authorization: Bearer tk_xxxxxxxxxxxxxx</code>
                    </p>

                    <hr>
                    <h5>Create New Token</h5>
                    <form id="create-token-form" class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Token Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control"
                                           placeholder="e.g. Mobile App, Booking Widget" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Expires in (days)</label>
                                    <input type="number" name="expiry_days" class="form-control"
                                           placeholder="Never" min="1" max="3650">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Scopes</label>
                            <div class="row">
                                @foreach($available_scopes as $scope)
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               name="scopes[]" value="{{ $scope }}"
                                               id="scope_{{ str_replace(':', '_', $scope) }}"
                                               {{ $scope === 'read:bookings' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="scope_{{ str_replace(':', '_', $scope) }}">
                                            <code>{{ $scope }}</code>
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-key"></i> Generate Token
                        </button>
                    </form>

                    <div id="token-reveal" class="alert alert-success d-none">
                        <strong>Copy your token now — it will not be shown again:</strong>
                        <div class="input-group mt-2">
                            <input type="text" id="plain-token" class="form-control font-monospace" readonly>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" onclick="copyToken()">Copy</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Active Tokens</h5></div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Name</th>
                                <th>Scopes</th>
                                <th>Last Used</th>
                                <th>Expires</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tokens as $token)
                            <tr>
                                <td>{{ $token->name }}</td>
                                <td>
                                    @foreach($token->scopes ?? [] as $scope)
                                        <code class="badge badge-light">{{ $scope }}</code>
                                    @endforeach
                                </td>
                                <td>{{ $token->last_used_at?->diffForHumans() ?? 'Never' }}</td>
                                <td>{{ $token->expires_at?->format('d M Y') ?? 'Never' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="revokeToken({{ $token->id }})">Revoke</button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No tokens yet</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h6>API Base URL</h6>
                    <code>{{ url('/api/v1') }}</code>
                    <hr>
                    <h6>Available Endpoints</h6>
                    <ul class="list-unstyled small">
                        <li><code>GET  /api/v1/bookings</code></li>
                        <li><code>POST /api/v1/bookings</code></li>
                        <li><code>GET  /api/v1/bookings/{id}</code></li>
                        <li><code>PUT  /api/v1/bookings/{id}</code></li>
                        <li><code>DEL  /api/v1/bookings/{id}</code></li>
                        <li><code>GET  /api/v1/ping</code></li>
                    </ul>
                    <a href="#" class="btn btn-sm btn-outline-secondary btn-block">View API Docs</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('create-token-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const data = {
        name: this.name.value,
        expiry_days: this.expiry_days.value || null,
        scopes: [...this.querySelectorAll('input[name="scopes[]"]:checked')].map(c => c.value),
    };
    fetch('{{ route('titan-integrations.api-tokens.store') }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: JSON.stringify(data),
    })
    .then(r => r.json())
    .then(result => {
        if (result.ok) {
            document.getElementById('plain-token').value = result.token;
            document.getElementById('token-reveal').classList.remove('d-none');
            location.reload();
        }
    });
});

function copyToken() {
    const el = document.getElementById('plain-token');
    el.select();
    document.execCommand('copy');
}

function revokeToken(id) {
    if (!confirm('Revoke this token? This cannot be undone.')) return;
    fetch(`{{ url('/account/titan-integrations/api-tokens') }}/${id}`, {
        method: 'DELETE',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
    }).then(() => location.reload());
}
</script>
@endsection
