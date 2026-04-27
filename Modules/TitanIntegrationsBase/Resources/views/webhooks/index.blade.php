@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <h4 class="page-title">Outbound Webhooks</h4>
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
                        Register a URL to receive real-time events from WorkSuite. Every event is signed
                        with an HMAC-SHA256 signature in the <code>X-TitanIntegrations-Signature</code> header.
                    </p>

                    <hr>
                    <h5>Register Endpoint</h5>
                    <form id="create-webhook-form" class="mb-4">
                        <div class="form-group">
                            <label>Endpoint URL <span class="text-danger">*</span></label>
                            <input type="url" name="url" class="form-control"
                                   placeholder="https://yourapp.com/webhooks/worksuite" required>
                        </div>
                        <div class="form-group">
                            <label>Listen to Events</label>
                            <div class="row">
                                @foreach($available_events as $event)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               name="events[]" value="{{ $event }}"
                                               id="event_{{ str_replace('.', '_', $event) }}">
                                        <label class="form-check-label" for="event_{{ str_replace('.', '_', $event) }}">
                                            <code>{{ $event }}</code>
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-plug"></i> Register Endpoint
                        </button>
                    </form>

                    <div id="secret-reveal" class="alert alert-success d-none">
                        <strong>Save your signing secret — it will not be shown again:</strong>
                        <div class="input-group mt-2">
                            <input type="text" id="endpoint-secret" class="form-control font-monospace" readonly>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" onclick="copySecret()">Copy</button>
                            </div>
                        </div>
                        <p class="mt-2 mb-0 small">
                            Verify signatures: <code>hash_hmac('sha256', $rawBody, $secret)</code>
                            and compare to <code>X-TitanIntegrations-Signature</code> (strip "sha256=" prefix).
                        </p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Registered Endpoints</h5></div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>URL</th>
                                <th>Events</th>
                                <th>Last Triggered</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($endpoints as $endpoint)
                            <tr>
                                <td class="text-truncate" style="max-width:200px" title="{{ $endpoint->url }}">
                                    {{ $endpoint->url }}
                                </td>
                                <td>
                                    @foreach($endpoint->events as $event)
                                        <span class="badge badge-light">{{ $event }}</span>
                                    @endforeach
                                </td>
                                <td>{{ $endpoint->last_triggered_at?->diffForHumans() ?? 'Never' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-info mr-1"
                                        onclick="testEndpoint({{ $endpoint->id }})">Test</button>
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="deleteEndpoint({{ $endpoint->id }})">Delete</button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">No endpoints registered</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h6>Example Payload</h6>
                    <pre class="small bg-light p-2 rounded"><code>{
  "event": "booking.created",
  "timestamp": "2026-04-11T10:00:00Z",
  "data": {
    "id": 123,
    "heading": "House Clean",
    "start_date": "2026-04-15",
    "status": "pending"
  }
}</code></pre>
                    <h6>Retry Policy</h6>
                    <p class="small text-muted mb-0">
                        Failed deliveries retry 3 times with exponential backoff: 5s → 30s → 5min.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('create-webhook-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const data = {
        url: this.url.value,
        events: [...this.querySelectorAll('input[name="events[]"]:checked')].map(c => c.value),
    };
    fetch('{{ route('titan-integrations.webhooks.store') }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: JSON.stringify(data),
    })
    .then(r => r.json())
    .then(result => {
        if (result.ok) {
            document.getElementById('endpoint-secret').value = result.secret;
            document.getElementById('secret-reveal').classList.remove('d-none');
            this.reset();
        }
    });
});

function copySecret() {
    const el = document.getElementById('endpoint-secret');
    el.select(); document.execCommand('copy');
}

function testEndpoint(id) {
    fetch(`{{ url('/account/titan-integrations/webhooks') }}/${id}/test`, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
    })
    .then(r => r.json())
    .then(r => alert(r.message));
}

function deleteEndpoint(id) {
    if (!confirm('Delete this webhook endpoint?')) return;
    fetch(`{{ url('/account/titan-integrations/webhooks') }}/${id}`, {
        method: 'DELETE',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
    }).then(() => location.reload());
}
</script>
@endsection
