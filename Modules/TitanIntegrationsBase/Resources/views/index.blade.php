@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <h4 class="page-title">Integrations</h4>
                <div class="page-title-right">
                    <a href="{{ route('titan-integrations.api-tokens.index') }}" class="btn btn-outline-secondary btn-sm mr-2">
                        <i class="fa fa-key"></i> API Tokens
                    </a>
                    <a href="{{ route('titan-integrations.webhooks.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-plug"></i> Webhooks
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @foreach($integrations as $category => $items)
        <div class="row mb-4">
            <div class="col-12">
                <h5 class="text-muted mb-3">{{ $categories[$category] ?? ucfirst($category) }}</h5>
            </div>

            @foreach($items as $item)
            <div class="col-md-4 col-lg-3 mb-3">
                <div class="card h-100 integration-card @if($item['status'] === 'connected') border-success @elseif($item['status'] === 'error') border-danger @endif">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center mb-3">
                            <div class="integration-icon mr-3">
                                <i class="fab fa-{{ $item['icon'] }} fa-2x text-muted"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $item['label'] }}</h6>
                                @if($item['status'] === 'connected')
                                    <span class="badge badge-success badge-sm">Connected</span>
                                    @if($item['is_byo'])
                                        <span class="badge badge-info badge-sm ml-1">Your Key</span>
                                    @endif
                                @elseif($item['status'] === 'error')
                                    <span class="badge badge-danger badge-sm">Error</span>
                                @else
                                    <span class="badge badge-secondary badge-sm">Disconnected</span>
                                @endif
                            </div>
                        </div>

                        @if($item['account_name'])
                            <p class="text-muted small mb-1">
                                <i class="fa fa-user-circle"></i> {{ $item['account_name'] }}
                            </p>
                        @endif
                        @if($item['last_synced'])
                            <p class="text-muted small mb-1">
                                <i class="fa fa-sync"></i> Synced {{ $item['last_synced'] }}
                            </p>
                        @endif
                        @if($item['error'])
                            <p class="text-danger small mb-1">
                                <i class="fa fa-exclamation-circle"></i> {{ Str::limit($item['error'], 60) }}
                            </p>
                        @endif
                        @if($item['note'])
                            <p class="text-muted small mb-2">{{ $item['note'] }}</p>
                        @endif

                        <div class="mt-auto pt-2">
                            @if($item['status'] === 'connected')
                                <form method="POST" action="{{ route('titan-integrations.disconnect', $item['provider']) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Disconnect {{ $item['label'] }}?')">
                                        Disconnect
                                    </button>
                                </form>
                            @elseif($item['auth'] === 'oauth')
                                <a href="{{ route('titan-integrations.oauth.redirect', $item['provider']) }}"
                                   class="btn btn-sm btn-primary">
                                    Connect with {{ $item['label'] }}
                                </a>
                            @elseif(in_array($item['auth'], ['api_key', 'webhook']))
                                <a href="{{ route('titan-integrations.connect.show', $item['provider']) }}"
                                   class="btn btn-sm btn-primary">
                                    Configure
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endforeach
</div>
@endsection
