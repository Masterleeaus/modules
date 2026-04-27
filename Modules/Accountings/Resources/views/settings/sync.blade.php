@extends('layouts.app')
@section('content')
@include('accountings::partials.nav')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Accounting Sync</h4>
        <a href="{{ route('accountings.dashboard') }}" class="btn btn-outline-secondary btn-sm">Back to Dashboard</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @php
        $providers = [
            'xero'        => ['label' => 'Xero',        'icon' => '🔵'],
            'myob'        => ['label' => 'MYOB',        'icon' => '🟠'],
            'quickbooks'  => ['label' => 'QuickBooks',  'icon' => '🟢'],
        ];
    @endphp

    <div class="row">
        @foreach($providers as $key => $info)
            @php $s = $status[$key] ?? []; @endphp
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>{{ $info['icon'] }} {{ $info['label'] }}</span>
                        @if($s['connected'] ?? false)
                            <span class="badge bg-success">Connected</span>
                        @else
                            <span class="badge bg-secondary">Not Connected</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <dl class="row small mb-3">
                            <dt class="col-sm-7">Last invoices sync</dt>
                            <dd class="col-sm-5">{{ $s['last_synced_invoices'] ?? '—' }}</dd>
                            <dt class="col-sm-7">Last bills sync</dt>
                            <dd class="col-sm-5">{{ $s['last_synced_bills'] ?? '—' }}</dd>
                            <dt class="col-sm-7">Last full sync</dt>
                            <dd class="col-sm-5">{{ $s['last_synced_all'] ?? '—' }}</dd>
                        </dl>

                        @if($s['connected'] ?? false)
                            <form method="POST" action="{{ route('accountings.settings.sync.run') }}">
                                @csrf
                                <input type="hidden" name="provider" value="{{ $key }}">
                                <button class="btn btn-primary btn-sm w-100"
                                        onclick="return confirm('Trigger manual {{ $info['label'] }} sync?')">
                                    Sync Now
                                </button>
                            </form>
                        @else
                            <a href="#" class="btn btn-outline-secondary btn-sm w-100 disabled">Connect in Integrations</a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="alert alert-info small mt-2">
        <strong>Note:</strong> Nightly sync runs automatically via the queue scheduler.
        Manual sync triggers an immediate sync for invoices and bills.
        MYOB and QuickBooks sync stubs are included; full OAuth flow is managed via the TitanIntegrations module.
    </div>
</div>
@endsection
